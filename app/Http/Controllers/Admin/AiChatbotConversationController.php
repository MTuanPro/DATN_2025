<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatbotConversation;
use App\Models\AiChatbotFeedback;
use Illuminate\Http\Request;

class AiChatbotConversationController extends Controller
{
    /**
     * Display a listing of conversations
     */
    public function index(Request $request)
    {
        $query = AiChatbotConversation::with('sinhVien.user', 'messages');
        
        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('session_id', 'LIKE', "%{$search}%")
                  ->orWhere('tieu_de_chat', 'LIKE', "%{$search}%")
                  ->orWhereHas('sinhVien', function($sq) use ($search) {
                      $sq->where('ma_sinh_vien', 'LIKE', "%{$search}%")
                         ->orWhere('ho_ten', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        
        // Lọc theo sinh viên
        if ($request->filled('sinh_vien_id')) {
            $query->where('sinh_vien_id', $request->sinh_vien_id);
        }
        
        // Lọc theo khoảng thời gian
        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngay_bat_dau', '>=', $request->tu_ngay);
        }
        
        if ($request->filled('den_ngay')) {
            $query->whereDate('ngay_bat_dau', '<=', $request->den_ngay);
        }
        
        // Sắp xếp
        $query->orderBy('ngay_bat_dau', 'desc');
        
        $conversations = $query->paginate(20)->appends($request->all());
        
        // Thống kê
        $stats = [
            'total' => AiChatbotConversation::count(),
            'dang_mo' => AiChatbotConversation::dangMo()->count(),
            'da_dong' => AiChatbotConversation::daDong()->count(),
            'today' => AiChatbotConversation::whereDate('ngay_bat_dau', today())->count(),
        ];
        
        return view('admin.ai-chatbot.conversation.index', compact('conversations', 'stats'));
    }
    
    /**
     * Display the specified conversation
     */
    public function show(AiChatbotConversation $conversation)
    {
        $conversation->load([
            'sinhVien.user',
            'messages.knowledgeBase',
            'messages.feedback'
        ]);
        
        return view('admin.ai-chatbot.conversation.show', compact('conversation'));
    }
    
    /**
     * Close a conversation
     */
    public function close(AiChatbotConversation $conversation)
    {
        $conversation->dongCuocHoiThoai();
        
        return back()->with('success', 'Đã đóng cuộc hội thoại.');
    }
    
    /**
     * Reopen a conversation
     */
    public function reopen(AiChatbotConversation $conversation)
    {
        $conversation->moLaiCuocHoiThoai();
        
        return back()->with('success', 'Đã mở lại cuộc hội thoại.');
    }
    
    /**
     * Delete a conversation
     */
    public function destroy(AiChatbotConversation $conversation)
    {
        $conversation->delete();
        
        return redirect()->route('admin.ai-chatbot.conversation.index')
            ->with('success', 'Đã xóa cuộc hội thoại.');
    }
}
