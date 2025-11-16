<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatbotFeedback;
use App\Models\AiChatbotMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiChatbotFeedbackController extends Controller
{
    /**
     * Display a listing of feedbacks
     */
    public function index(Request $request)
    {
        $query = AiChatbotFeedback::with([
            'message.conversation.sinhVien.user',
            'message.knowledgeBase',
            'sinhVien.user'
        ]);
        
        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('sinhVien', function($sq) use ($search) {
                    $sq->where('ma_sinh_vien', 'LIKE', "%{$search}%")
                       ->orWhere('ho_ten', 'LIKE', "%{$search}%");
                })
                ->orWhere('ly_do', 'LIKE', "%{$search}%");
            });
        }
        
        // Lọc theo đánh giá
        if ($request->filled('danh_gia')) {
            $query->where('danh_gia', $request->danh_gia);
        }
        
        // Lọc theo knowledge base
        if ($request->filled('knowledge_base_id')) {
            $query->whereHas('message', function($q) use ($request) {
                $q->where('knowledge_base_id', $request->knowledge_base_id);
            });
        }
        
        // Lọc theo khoảng thời gian
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }
        
        // Sắp xếp
        $query->orderBy('created_at', 'desc');
        
        $feedbacks = $query->paginate(20)->appends($request->all());
        
        // Thống kê
        $stats = [
            'total' => AiChatbotFeedback::count(),
            'huu_ich' => AiChatbotFeedback::huuIch()->count(),
            'khong_huu_ich' => AiChatbotFeedback::khongHuuIch()->count(),
            'ty_le_huu_ich' => $this->calculateSatisfactionRate(),
            'top_knowledge_good' => $this->getTopKnowledgeByFeedback('huu_ich', 5),
            'top_knowledge_bad' => $this->getTopKnowledgeByFeedback('khong_huu_ich', 5),
        ];
        
        return view('admin.ai-chatbot.feedback.index', compact('feedbacks', 'stats'));
    }
    
    /**
     * Display the specified feedback
     */
    public function show(AiChatbotFeedback $feedback)
    {
        $feedback->load([
            'message.conversation.messages.knowledgeBase',
            'message.knowledgeBase',
            'sinhVien.user'
        ]);
        
        return view('admin.ai-chatbot.feedback.show', compact('feedback'));
    }
    
    /**
     * Delete feedback
     */
    public function destroy(AiChatbotFeedback $feedback)
    {
        $feedback->delete();
        
        return redirect()->route('admin.ai-chatbot.feedback.index')
            ->with('success', 'Đã xóa đánh giá.');
    }
    
    /**
     * Calculate overall satisfaction rate
     */
    protected function calculateSatisfactionRate(): float
    {
        $total = AiChatbotFeedback::count();
        
        if ($total == 0) {
            return 0;
        }
        
        $positive = AiChatbotFeedback::huuIch()->count();
        
        return round(($positive / $total) * 100, 2);
    }
    
    /**
     * Get top knowledge base by feedback type
     */
    protected function getTopKnowledgeByFeedback(string $type, int $limit = 5): array
    {
        return DB::table('ai_chatbot_feedback')
            ->join('ai_chatbot_message', 'ai_chatbot_feedback.message_id', '=', 'ai_chatbot_message.id')
            ->join('ai_chatbot_knowledge_base', 'ai_chatbot_message.knowledge_base_id', '=', 'ai_chatbot_knowledge_base.id')
            ->where('ai_chatbot_feedback.danh_gia', $type)
            ->select(
                'ai_chatbot_knowledge_base.id',
                'ai_chatbot_knowledge_base.cau_hoi_mau',
                'ai_chatbot_knowledge_base.chu_de',
                DB::raw('COUNT(*) as feedback_count')
            )
            ->groupBy('ai_chatbot_knowledge_base.id', 'ai_chatbot_knowledge_base.cau_hoi_mau', 'ai_chatbot_knowledge_base.chu_de')
            ->orderBy('feedback_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
    
    /**
     * Analytics page
     */
    public function analytics()
    {
        // Thống kê theo thời gian (7 ngày gần nhất)
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyStats[] = [
                'date' => $date->format('d/m'),
                'huu_ich' => AiChatbotFeedback::huuIch()
                    ->whereDate('created_at', $date)
                    ->count(),
                'khong_huu_ich' => AiChatbotFeedback::khongHuuIch()
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        }
        
        // Thống kê theo chủ đề
        $statsByChuDe = DB::table('ai_chatbot_feedback')
            ->join('ai_chatbot_message', 'ai_chatbot_feedback.message_id', '=', 'ai_chatbot_message.id')
            ->join('ai_chatbot_knowledge_base', 'ai_chatbot_message.knowledge_base_id', '=', 'ai_chatbot_knowledge_base.id')
            ->select(
                'ai_chatbot_knowledge_base.chu_de',
                DB::raw('SUM(CASE WHEN ai_chatbot_feedback.danh_gia = "huu_ich" THEN 1 ELSE 0 END) as huu_ich'),
                DB::raw('SUM(CASE WHEN ai_chatbot_feedback.danh_gia = "khong_huu_ich" THEN 1 ELSE 0 END) as khong_huu_ich')
            )
            ->groupBy('ai_chatbot_knowledge_base.chu_de')
            ->get();
        
        return view('admin.ai-chatbot.feedback.analytics', compact('dailyStats', 'statsByChuDe'));
    }
}
