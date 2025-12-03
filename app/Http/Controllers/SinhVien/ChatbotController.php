<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\AiChatbotConversation;
use App\Models\AiChatbotMessage;
use App\Models\AiChatbotFeedback;
use App\Services\ChatbotMatchingService;
use App\Services\AdvancedChatbotMatchingService;
use App\Services\ChatbotContextService;
use App\Services\ChatbotGPTService;
use App\Services\ChatbotGeminiService;
use App\Services\ChatbotDatabaseService;
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
    protected $gptService;
    protected $geminiService;
    protected $databaseService;

    /**
     * Khởi tạo ChatbotController với 5 service dependencies cho AI chatbot
     *
     * Các service được inject:
     * - ChatbotMatchingService: Tìm kiếm câu trả lời từ knowledge base bằng tính tương đồng cơ bản
     * - AdvancedChatbotMatchingService: Tính tương đồng nâng cao (intent, entities, context)
     * - ChatbotContextService: Quản lý context của cuộc hội thoại (lịch sử, chủ đề, entities)
     * - ChatbotGPTService: Tích hợp ChatGPT API (OpenAI) cho câu hỏi không match
     * - ChatbotGeminiService: Tích hợp Gemini API (Google) cho câu hỏi không match
     *
     * @param ChatbotMatchingService $matchingService Service tìm kiếm cư bản
     * @param AdvancedChatbotMatchingService $advancedMatchingService Service tìm kiếm nâng cao
     * @param ChatbotContextService $contextService Service quản lý context
     * @param ChatbotGPTService $gptService Service ChatGPT (fallback AI)
     * @param ChatbotGeminiService $geminiService Service Gemini (primary AI)
     * @return void
     */
    public function __construct(
        ChatbotMatchingService $matchingService,
        AdvancedChatbotMatchingService $advancedMatchingService,
        ChatbotContextService $contextService,
        ChatbotGPTService $gptService,
        ChatbotGeminiService $geminiService,
        ChatbotDatabaseService $databaseService
    ) {
        $this->matchingService = $matchingService;
        $this->advancedMatchingService = $advancedMatchingService;
        $this->contextService = $contextService;
        $this->gptService = $gptService;
        $this->geminiService = $geminiService;
        $this->databaseService = $databaseService;
    }

    /**
     * Hiển thị giao diện chatbot chính của sinh viên
     *
     * Load:
     * - Cuộc hội thoại đang mở hiện tại (nếu có)
     * - 10 cuộc hội thoại gần đây với 5 tin nhắn mới nhất mỗi cuộc
     * - Eager load relationships để tránh N+1 query
     * - Thông tin knowledge base và feedback liên quan
     *
     * @return \Illuminate\View\View Giao diện chatbot với active conversation và lịch sử
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
     * Tạo cuộc hội thoại mới cho sinh viên qua AJAX
     *
     * Quy trình:
     * 1. Sử dụng database transaction để đảm bảo data integrity
     * 2. Đóng tất cả cuộc hội thoại đang mở của sinh viên (bulk update - atomic)
     * 3. Tạo cuộc hội thoại mới với session_id duy nhất (UUID)
     * 4. Trạng thái ban đầu: 'dang_mo'
     * 5. Log lỗi nếu có vấn đề trong quá trình tạo
     *
     * @param Request $request Không cần tham số
     * @return \Illuminate\Http\JsonResponse JSON {success, conversation_id, session_id}
     * @throws \Exception Khi có lỗi database
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
     * Gửi tin nhắn và nhận phản hồi từ chatbot AI qua AJAX
     *
     * Quy trình xử lý tin nhắn (multi-tier AI architecture):
     * 1. Validate và sanitize input để tránh XSS attack (strip_tags, htmlspecialchars, limit 1000 chars)
     * 2. Kiểm tra quyền truy cập cuộc hội thoại
     * 3. Lưu tin nhắn của user vào database
     * 4. Lấy context từ conversation (lịch sử, chủ đề, entities)
     * 5. Sử dụng AdvancedChatbotMatchingService để tìm kiếm trong knowledge base:
     *    - Phân tích intent (intent recognition)
     *    - Nhận diện entities (entity extraction)
     *    - Tính tương đồng với context-aware algorithm
     * 6. Nếu match (similarity > threshold):
     *    - Trả về câu trả lời từ knowledge base
     *    - Tăng lượt truy cập cho knowledge
     * 7. Nếu không match (similarity < threshold):
     *    - Thử Gemini AI (priority 1, nếu enabled và similarity < min_similarity)
     *    - Fallback ChatGPT (priority 2, nếu Gemini fail)
     *    - Fallback default response (priority 3, nếu cả 2 AI fail)
     * 8. Lưu tin nhắn bot với metadata (knowledge_id, similarity, AI provider, tokens)
     * 9. Cập nhật context sau khi trả lời (last_question, last_response, intent, entities)
     * 10. Tự động tạo tiêu đề cho conversation nếu chưa có
     * 11. Trả về JSON với thông tin chi tiết (bao gồm debug info nếu app.debug = true)
     *
     * @param Request $request Chứa conversation_id, message (max 1000 chars), chu_de (optional)
     * @return \Illuminate\Http\JsonResponse JSON {success, user_message, bot_message, similarity, intent, entities, used_ai, ai_provider, ai_tokens}
     * @throws \Illuminate\Validation\ValidationException Khi dữ liệu không hợp lệ
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
        $usedAI = false;
        $aiProvider = null;
        $aiTokensUsed = 0;
        $usedDatabase = false;
        
        // BƯỚC 1: Thử query database trước (ưu tiên cao nhất cho câu hỏi cụ thể)
        $dbIntent = $this->databaseService->detectIntent($cleanMessage, $sinhVien->id);
        
        if ($dbIntent['intent'] && $dbIntent['confidence'] > 0.7) {
            $dbResponse = $this->databaseService->queryDatabase(
                $dbIntent['intent'],
                $sinhVien->id,
                $dbIntent['entities']
            );
            
            if ($dbResponse) {
                $botResponse = $dbResponse;
                $usedDatabase = true;
                $knowledgeId = null;
                
                Log::info('Chatbot used Database Query', [
                    'intent' => $dbIntent['intent'],
                    'entities' => $dbIntent['entities'],
                    'question' => $cleanMessage,
                ]);
            }
        }
        
        // BƯỚC 2: Nếu không query được database, thử knowledge base
        if (!$usedDatabase && $knowledge) {
            // Có câu trả lời từ knowledge base
            $botResponse = $knowledge->cau_tra_loi;
            $knowledgeId = $knowledge->id;

            // Tăng lượt truy cập
            $knowledge->tangLuotTruyCap();
        } elseif (!$usedDatabase && !$knowledge) {
            // Không tìm thấy trong knowledge base
            $knowledgeId = null;
            
            // Lấy context từ conversation
            $conversationContext = $this->contextService->getContext($conversation->id);
            $recentMessages = $conversation->messages()
                ->orderBy('thoi_gian_gui', 'desc')
                ->take(10)
                ->get()
                ->reverse()
                ->map(function($msg) {
                    return [
                        'nguoi_gui' => $msg->nguoi_gui,
                        'noi_dung' => $msg->noi_dung,
                    ];
                })
                ->toArray();
            
            $contextForAI = [
                'messages' => $recentMessages,
                'intent' => $intent,
                'entities' => $entities,
            ];
            
            // Lấy một số knowledge base liên quan để làm context
            $relatedKnowledge = \App\Models\AiChatbotKnowledgeBase::kichHoat()
                ->where('chu_de', $intent)
                ->limit(3)
                ->get()
                ->map(function($kb) {
                    return [
                        'cau_hoi' => $kb->cau_hoi_mau,
                        'cau_tra_loi' => $kb->cau_tra_loi,
                    ];
                })
                ->toArray();
            
            // Ưu tiên: Gemini > GPT > Default
            $minSimilarity = config('chatbot.gemini.min_similarity_for_gemini', 0.3);
            if (config('chatbot.gemini.enabled', false) && 
                config('chatbot.gemini.use_when_no_match', true) && 
                $similarity < $minSimilarity) {
                
                // Thử dùng Gemini
                $geminiResult = $this->geminiService->getResponse($cleanMessage, $contextForAI, $relatedKnowledge);
                
                if (!empty($geminiResult['response'])) {
                    $botResponse = $geminiResult['response'];
                    $usedAI = true;
                    $aiProvider = 'gemini';
                    $aiTokensUsed = $geminiResult['tokens_used'] ?? 0;
                    
                    Log::info('Chatbot used Gemini', [
                        'question' => $cleanMessage,
                        'tokens_used' => $aiTokensUsed,
                        'similarity' => $similarity,
                    ]);
                }
            }
            
            // Nếu Gemini không hoạt động, thử GPT
            if (!$usedAI) {
                $gptEnabled = config('chatbot.gpt.enabled', false);
                $useGPTWhenNoMatch = config('chatbot.gpt.use_when_no_match', true);
                $minSimilarityForGPT = config('chatbot.gpt.min_similarity_for_gpt', 0.3);
                
                if ($gptEnabled && $useGPTWhenNoMatch && $similarity < $minSimilarityForGPT) {
                    $gptResult = $this->gptService->getResponse($cleanMessage, $contextForAI, $relatedKnowledge);
                    
                    if (!empty($gptResult['response'])) {
                        $botResponse = $gptResult['response'];
                        $usedAI = true;
                        $aiProvider = 'gpt';
                        $aiTokensUsed = $gptResult['tokens_used'] ?? 0;
                        
                        Log::info('Chatbot used GPT', [
                            'question' => $cleanMessage,
                            'tokens_used' => $aiTokensUsed,
                            'similarity' => $similarity,
                        ]);
                    }
                }
            }
            
            // Nếu cả Gemini và GPT đều không hoạt động, dùng default response
            if (!$usedAI) {
                $botResponse = config(
                    'chatbot.default_response',
                    'Xin lỗi, tôi chưa có thông tin về câu hỏi này. Bạn có thể liên hệ phòng Đào tạo để được hỗ trợ chi tiết hơn.'
                );
                $botResponse .= "\n\n📞 Hotline: " . config('chatbot.hotline', '024.xxxx.xxxx');
                $botResponse .= "\n📧 Email: " . config('chatbot.email', 'daotao@smis.edu.vn');
                
                Log::warning('Chatbot AI failed, using default', [
                    'question' => $cleanMessage,
                ]);
            }

            // Log câu hỏi không match được
            Log::info('Chatbot no match', [
                'question' => $cleanMessage,
                'chu_de' => $request->chu_de,
                'sinh_vien_id' => $sinhVien->id,
                'best_similarity' => $similarity,
                'used_ai' => $usedAI,
                'ai_provider' => $aiProvider,
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
                'used_ai' => $usedAI, // Thông tin về việc sử dụng AI
                'ai_provider' => $aiProvider, // 'gemini' hoặc 'gpt'
                'ai_tokens' => $usedAI ? $aiTokensUsed : null, // Số tokens đã dùng
            ],
        ]);
    }

    /**
     * Lấy tất cả tin nhắn của một cuộc hội thoại qua AJAX
     *
     * Load eager relationships (knowledgeBase) để tránh N+1 query.
     * Kiểm tra quyền sinh viên trước khi trả về.
     * Format thời gian theo 'H:i d/m/Y'.
     * Tính % tương đồng cho mỗi message.
     *
     * @param int $conversationId ID của cuộc hội thoại cần lấy tin nhắn
     * @return \Illuminate\Http\JsonResponse JSON {success, messages[]}
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu conversation không tồn tại
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
     * Tải một cuộc hội thoại cũ từ lịch sử để xem lại
     *
     * Kiểm tra:
     * - Cuộc hội thoại có tồn tại không (dùng find() thay vì findOrFail())
     * - Quyền truy cập của sinh viên
     * - Redirect về history với thông báo lỗi nếu không hợp lệ
     *
     * Eager load messages và knowledgeBase để tránh N+1 query.
     *
     * @param int $conversationId ID của cuộc hội thoại cần xem
     * @return \Illuminate\View\View Trang xem chi tiết conversation
     * @return \Illuminate\Http\RedirectResponse Redirect về history nếu không hợp lệ
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
     * Gửi đánh giá (feedback) cho một tin nhắn bot qua AJAX
     *
     * Quy trình:
     * 1. Validate dữ liệu (message_id, danh_gia: huu_ich/khong_huu_ich, ly_do)
     * 2. Kiểm tra quyền sinh viên qua conversation
     * 3. Nếu đã có feedback trước đó:
     *    - Cập nhật feedback (update)
     *    - Cập nhật thống kê knowledge base (tăng/giảm đếm hữu ích)
     * 4. Nếu chưa có feedback:
     *    - Tạo feedback mới (create)
     *    - Tăng đếm hữu ích cho knowledge base (nếu danh_gia = huu_ich)
     *
     * @param Request $request Chứa message_id, danh_gia (huu_ich|khong_huu_ich), ly_do (optional, max 500)
     * @return \Illuminate\Http\JsonResponse JSON {success, message}
     * @throws \Illuminate\Validation\ValidationException Khi dữ liệu không hợp lệ
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
     * Hiển thị lịch sử tất cả các cuộc hội thoại của sinh viên có phân trang
     *
     * Load conversations kèm số lượng tin nhắn (withCount).
     * Sắp xếp theo ngay_bat_dau giảm dần (mới nhất lên đầu).
     * Phân trang 15 conversations/page.
     *
     * @return \Illuminate\View\View Trang lịch sử với danh sách conversations
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
     * Xóa một cuộc hội thoại và cả context liên quan qua AJAX
     *
     * Quy trình:
     * 1. Kiểm tra conversation có tồn tại không (dùng find())
     * 2. Kiểm tra quyền sinh viên
     * 3. Xóa context từ ChatbotContextService (cache/Redis)
     * 4. Xóa conversation (soft delete, messages sẽ tự xóa nếu có onDelete cascade)
     * 5. Log lỗi nếu có vấn đề
     *
     * @param int $conversationId ID của cuộc hội thoại cần xóa
     * @return \Illuminate\Http\JsonResponse JSON {success, message/error}
     * @throws \Exception Khi có lỗi trong quá trình xóa
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
            Log::error('Lỗi xóa conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi xóa cuộc hội thoại'
            ], 500);
        }
    }

    /**
     * Lấy danh sách câu hỏi gợi ý từ knowledge base qua AJAX
     *
     * Lọc:
     * - Chỉ lấy knowledge base đang kích hoạt (scope kichHoat)
     * - Lọc theo chủ đề (chu_de) nếu có tham số
     * - Sắp xếp theo độ ưu tiên giảm dần
     * - Giới hạn 5 câu hỏi
     *
     * @param Request $request Có thể chứa chu_de để lọc theo chủ đề
     * @return \Illuminate\Http\JsonResponse JSON {success, questions[]}
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
