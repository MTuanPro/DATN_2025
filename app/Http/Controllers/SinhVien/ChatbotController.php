<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\AiChatbotConversation;
use App\Models\AiChatbotMessage;
use App\Models\AiChatbotFeedback;
use App\Services\ChatbotMatchingService;
use App\Services\AdvancedChatbotMatchingService;
use App\Services\ChatbotContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected $matchingService;
    protected $advancedMatchingService;
    protected $contextService;

    public function __construct(
        ChatbotMatchingService $matchingService,
        AdvancedChatbotMatchingService $advancedMatchingService,
        ChatbotContextService $contextService
    ) {
        $this->matchingService = $matchingService;
        $this->advancedMatchingService = $advancedMatchingService;
        $this->contextService = $contextService;
    }

    /**
     * Display chatbot interface
     */
    public function index()
    {
        $sinhVien = Auth::user()->sinhVien;

        // Lấy cuộc hội thoại đang mở (nếu có)
        $activeConversation = AiChatbotConversation::where('sinh_vien_id', $sinhVien->id)
            ->dangMo()
            ->latest('ngay_bat_dau')
            ->first();

        // FIX: Eager load relationships để tránh N+1 query
        $conversations = AiChatbotConversation::where('sinh_vien_id', $sinhVien->id)
            ->with([
                'messages' => function ($query) {
                    $query->latest('thoi_gian_gui')->take(5);
                },
                'messages.knowledgeBase:id,chu_de,danh_muc',
                'messages.feedback:id,message_id,danh_gia'
            ])
            ->orderBy('ngay_bat_dau', 'desc')
            ->take(10)
            ->get();

        return view('sinh-vien.chatbot.index', compact('activeConversation', 'conversations'));
    }

    /**
     * Create new conversation
     * FIX: Sử dụng transaction để đảm bảo data integrity
     */
    public function createConversation(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        try {
            $conversation = DB::transaction(function () use ($sinhVien) {
                // Đóng tất cả cuộc hội thoại đang mở (bulk update - atomic)
                AiChatbotConversation::where('sinh_vien_id', $sinhVien->id)
                    ->where('trang_thai', 'dang_mo')
                    ->update([
                        'trang_thai' => 'da_dong',
                        'ngay_ket_thuc' => now(),
                    ]);

                // Tạo cuộc hội thoại mới
                return AiChatbotConversation::create([
                    'sinh_vien_id' => $sinhVien->id,
                    'session_id' => Str::uuid(),
                    'ngay_bat_dau' => now(),
                    'trang_thai' => 'dang_mo',
                ]);
            });

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'session_id' => $conversation->session_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create conversation', [
                'sinh_vien_id' => $sinhVien->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Không thể tạo cuộc hội thoại. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Send message and get bot response
     * FIX: Sanitize input để tránh XSS attack
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:ai_chatbot_conversation,id',
            'message' => 'required|string|max:1000',
            'chu_de' => 'nullable|string',
        ]);

        $sinhVien = Auth::user()->sinhVien;
        $conversation = AiChatbotConversation::findOrFail($request->conversation_id);

        // Kiểm tra quyền
        if ($conversation->sinh_vien_id != $sinhVien->id) {
            return response()->json(['error' => 'Không có quyền truy cập'], 403);
        }

        // FIX: Sanitize user input
        $cleanMessage = strip_tags($request->message);
        $cleanMessage = htmlspecialchars($cleanMessage, ENT_QUOTES, 'UTF-8');
        $cleanMessage = Str::limit($cleanMessage, 1000); // Hard limit

        // Lưu tin nhắn của user
        $userMessage = AiChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'nguoi_gui' => 'user',
            'noi_dung' => $cleanMessage,
            'thoi_gian_gui' => now(),
        ]);

        // UPGRADE: Sử dụng Advanced Matching với Context
        // Lấy context từ conversation trước
        $context = $this->contextService->getContext($conversation->id);

        // Tìm câu trả lời phù hợp với advanced matching
        $matchResult = $this->advancedMatchingService->findBestMatch(
            $cleanMessage,
            $context
        );

        $knowledge = $matchResult['knowledge'];
        $similarity = $matchResult['similarity'];
        $intent = $matchResult['intent'] ?? 'unknown';
        $entities = $matchResult['entities'] ?? [];

        // Tạo câu trả lời
        if ($knowledge) {
            // Có câu trả lời từ knowledge base
            $botResponse = $knowledge->cau_tra_loi;
            $knowledgeId = $knowledge->id;

            // Tăng lượt truy cập
            $knowledge->tangLuotTruyCap();
        } else {
            // FIX: Sử dụng config thay vì hard-coded
            // Không tìm thấy câu trả lời phù hợp
            $botResponse = config(
                'chatbot.default_response',
                'Xin lỗi, tôi chưa có thông tin về câu hỏi này. Bạn có thể liên hệ phòng Đào tạo để được hỗ trợ chi tiết hơn.'
            );
            $botResponse .= "\n\n📞 Hotline: " . config('chatbot.hotline', '024.xxxx.xxxx');
            $botResponse .= "\n📧 Email: " . config('chatbot.email', 'daotao@smis.edu.vn');
            $knowledgeId = null;

            // FIX: Log câu hỏi không match được
            Log::info('Chatbot no match', [
                'question' => $cleanMessage,
                'chu_de' => $request->chu_de,
                'sinh_vien_id' => $sinhVien->id,
                'best_similarity' => $similarity,
            ]);
        }

        // Lưu tin nhắn của bot
        $botMessage = AiChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'nguoi_gui' => 'bot',
            'noi_dung' => $botResponse,
            'knowledge_base_id' => $knowledgeId,
            'do_tuong_dong' => $similarity,
            'thoi_gian_gui' => now(),
        ]);

        // UPGRADE: Update context sau khi trả lời
        $this->contextService->updateContext($conversation->id, [
            'last_question' => $cleanMessage,
            'last_response' => $botResponse,
            'last_similarity' => $similarity,
            'previous_topic' => $intent,
            'entities' => $entities,
        ]);

        // Add topic to history
        if ($intent != 'unknown') {
            $this->contextService->addTopic($conversation->id, $intent);
        }

        // Add entities to context for follow-up questions
        if (!empty($entities)) {
            $this->contextService->addEntities($conversation->id, $entities);
        }

        // Tự động tạo tiêu đề cho cuộc hội thoại
        if (!$conversation->tieu_de_chat) {
            $conversation->taoTieuDeTuDong();
        }

        return response()->json([
            'success' => true,
            'user_message' => [
                'id' => $userMessage->id,
                'noi_dung' => $userMessage->noi_dung,
                'thoi_gian_gui' => $userMessage->thoi_gian_gui->format('H:i'),
            ],
            'bot_message' => [
                'id' => $botMessage->id,
                'noi_dung' => $botMessage->noi_dung,
                'thoi_gian_gui' => $botMessage->thoi_gian_gui->format('H:i'),
                'knowledge_id' => $knowledgeId,
                'similarity' => $similarity ? round($similarity * 100) : null,
                'intent' => $intent, // Debug info
                'entities' => config('app.debug') ? $entities : null, // Debug info
            ],
        ]);
    }

    /**
     * Get conversation messages
     */
    public function getMessages($conversationId)
    {
        $sinhVien = Auth::user()->sinhVien;
        $conversation = AiChatbotConversation::findOrFail($conversationId);

        // Kiểm tra quyền
        if ($conversation->sinh_vien_id != $sinhVien->id) {
            return response()->json(['error' => 'Không có quyền truy cập'], 403);
        }

        $messages = $conversation->messages()
            ->with('knowledgeBase')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'nguoi_gui' => $msg->nguoi_gui,
                    'noi_dung' => $msg->noi_dung,
                    'thoi_gian_gui' => $msg->thoi_gian_gui->format('H:i d/m/Y'),
                    'knowledge_id' => $msg->knowledge_base_id,
                    'similarity' => $msg->doTuongDongPhanTram(),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Load conversation (for history)
     */
    public function loadConversation($conversationId)
    {
        $sinhVien = Auth::user()->sinhVien;

        // Sử dụng find() thay vì findOrFail() để xử lý trường hợp không tìm thấy
        $conversation = AiChatbotConversation::with('messages.knowledgeBase')
            ->find($conversationId);

        // Nếu không tìm thấy conversation, redirect về history với thông báo
        if (!$conversation) {
            return redirect()->route('sinh-vien.chatbot.history')
                ->with('error', 'Cuộc hội thoại không tồn tại hoặc đã bị xóa');
        }

        // Kiểm tra quyền
        if ($conversation->sinh_vien_id != $sinhVien->id) {
            return redirect()->route('sinh-vien.chatbot.history')
                ->with('error', 'Bạn không có quyền xem cuộc hội thoại này');
        }

        return view('sinh-vien.chatbot.conversation', compact('conversation'));
    }

    /**
     * Submit feedback for a message
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:ai_chatbot_message,id',
            'danh_gia' => 'required|in:huu_ich,khong_huu_ich',
            'ly_do' => 'nullable|string|max:500',
        ]);

        $sinhVien = Auth::user()->sinhVien;
        $message = AiChatbotMessage::with('conversation')->findOrFail($request->message_id);

        // Kiểm tra quyền
        if ($message->conversation->sinh_vien_id != $sinhVien->id) {
            return response()->json(['error' => 'Không có quyền truy cập'], 403);
        }

        // Kiểm tra đã feedback chưa
        if ($message->daCoFeedback()) {
            // Cập nhật feedback
            $feedback = $message->feedback;
            $oldRating = $feedback->danh_gia;

            $feedback->update([
                'danh_gia' => $request->danh_gia,
                'ly_do' => $request->ly_do,
            ]);

            // Cập nhật thống kê knowledge base
            if ($message->knowledgeBase) {
                if ($oldRating == 'huu_ich' && $request->danh_gia == 'khong_huu_ich') {
                    $message->knowledgeBase->giamHuuIch();
                } elseif ($oldRating == 'khong_huu_ich' && $request->danh_gia == 'huu_ich') {
                    $message->knowledgeBase->tangHuuIch();
                }
            }
        } else {
            // Tạo feedback mới
            AiChatbotFeedback::create([
                'message_id' => $message->id,
                'sinh_vien_id' => $sinhVien->id,
                'danh_gia' => $request->danh_gia,
                'ly_do' => $request->ly_do,
            ]);

            // Cập nhật thống kê knowledge base
            if ($message->knowledgeBase && $request->danh_gia == 'huu_ich') {
                $message->knowledgeBase->tangHuuIch();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã đánh giá!',
        ]);
    }

    /**
     * Get conversation history
     */
    public function history()
    {
        $sinhVien = Auth::user()->sinhVien;

        $conversations = AiChatbotConversation::where('sinh_vien_id', $sinhVien->id)
            ->withCount('messages')
            ->orderBy('ngay_bat_dau', 'desc')
            ->paginate(15);

        return view('sinh-vien.chatbot.history', compact('conversations'));
    }

    /**
     * Delete conversation
     */
    public function deleteConversation($conversationId)
    {
        try {
            $sinhVien = Auth::user()->sinhVien;

            // Sử dụng find() và kiểm tra null thay vì findOrFail()
            $conversation = AiChatbotConversation::find($conversationId);

            // Kiểm tra conversation có tồn tại không
            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cuộc hội thoại không tồn tại hoặc đã bị xóa'
                ], 404);
            }

            // Kiểm tra quyền
            if ($conversation->sinh_vien_id != $sinhVien->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có quyền truy cập'
                ], 403);
            }

            // UPGRADE: Clear context khi xóa conversation
            $this->contextService->clearContext($conversationId);

            $conversation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa cuộc hội thoại',
            ]);
        } catch (\Exception $e) {
            \Log::error('Lỗi xóa conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi xóa cuộc hội thoại'
            ], 500);
        }
    }

    /**
     * Get suggested questions by topic
     */
    public function getSuggestedQuestions(Request $request)
    {
        $chuDe = $request->get('chu_de');

        $query = \App\Models\AiChatbotKnowledgeBase::kichHoat()
            ->select('id', 'cau_hoi_mau', 'chu_de')
            ->orderBy('do_uu_tien', 'desc')
            ->limit(5);

        if ($chuDe) {
            $query->where('chu_de', $chuDe);
        }

        $questions = $query->get();

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }
}
