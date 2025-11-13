<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\AiChatbotConversation;
use App\Models\AiChatbotMessage;
use App\Models\AiChatbotFeedback;
use App\Services\ChatbotMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected $matchingService;
    
    public function __construct(ChatbotMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
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
        
        // Lấy lịch sử hội thoại
        $conversations = AiChatbotConversation::where('sinh_vien_id', $sinhVien->id)
            ->with('messages')
            ->orderBy('ngay_bat_dau', 'desc')
            ->take(10)
            ->get();
        
        return view('sinh-vien.chatbot.index', compact('activeConversation', 'conversations'));
    }
    
    /**
     * Create new conversation
     */
    public function createConversation(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;
        
        // Đóng cuộc hội thoại cũ (nếu có)
        AiChatbotConversation::where('sinh_vien_id', $sinhVien->id)
            ->dangMo()
            ->each(function($conv) {
                $conv->dongCuocHoiThoai();
            });
        
        // Tạo cuộc hội thoại mới
        $conversation = AiChatbotConversation::create([
            'sinh_vien_id' => $sinhVien->id,
            'session_id' => Str::uuid(),
            'ngay_bat_dau' => now(),
            'trang_thai' => 'dang_mo',
        ]);
        
        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'session_id' => $conversation->session_id,
        ]);
    }
    
    /**
     * Send message and get bot response
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
        
        // Lưu tin nhắn của user
        $userMessage = AiChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'nguoi_gui' => 'user',
            'noi_dung' => $request->message,
            'thoi_gian_gui' => now(),
        ]);
        
        // Tìm câu trả lời phù hợp
        $matchResult = $this->matchingService->findBestMatch(
            $request->message,
            $request->chu_de
        );
        
        $knowledge = $matchResult['knowledge'];
        $similarity = $matchResult['similarity'];
        
        // Tạo câu trả lời
        if ($knowledge) {
            // Có câu trả lời từ knowledge base
            $botResponse = $knowledge->cau_tra_loi;
            $knowledgeId = $knowledge->id;
            
            // Tăng lượt truy cập
            $knowledge->tangLuotTruyCap();
        } else {
            // Không tìm thấy câu trả lời phù hợp
            $botResponse = "Xin lỗi, tôi chưa có thông tin về câu hỏi này. Bạn có thể liên hệ phòng Đào tạo để được hỗ trợ chi tiết hơn.\n\n";
            $botResponse .= "📞 Hotline: 024.xxxx.xxxx\n";
            $botResponse .= "📧 Email: daotao@smis.edu.vn";
            $knowledgeId = null;
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
            ->map(function($msg) {
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
        $conversation = AiChatbotConversation::with('messages.knowledgeBase')
            ->findOrFail($conversationId);
        
        // Kiểm tra quyền
        if ($conversation->sinh_vien_id != $sinhVien->id) {
            abort(403);
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
        $sinhVien = Auth::user()->sinhVien;
        $conversation = AiChatbotConversation::findOrFail($conversationId);
        
        // Kiểm tra quyền
        if ($conversation->sinh_vien_id != $sinhVien->id) {
            return response()->json(['error' => 'Không có quyền truy cập'], 403);
        }
        
        $conversation->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa cuộc hội thoại',
        ]);
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
