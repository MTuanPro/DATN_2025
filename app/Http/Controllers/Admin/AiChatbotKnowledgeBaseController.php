<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatbotKnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\SimpleArrayExport;

class AiChatbotKnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the knowledge base
     */
    public function index(Request $request)
    {
        $query = AiChatbotKnowledgeBase::with('nguoiTao');
        
        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cau_hoi_mau', 'LIKE', "%{$search}%")
                  ->orWhere('cau_tra_loi', 'LIKE', "%{$search}%")
                  ->orWhere('tu_khoa', 'LIKE', "%{$search}%");
            });
        }
        
        // Lọc theo chủ đề
        if ($request->filled('chu_de')) {
            $query->where('chu_de', $request->chu_de);
        }
        
        // Lọc theo danh mục
        if ($request->filled('danh_muc')) {
            $query->where('danh_muc', $request->danh_muc);
        }
        
        // Lọc theo trạng thái
        if ($request->filled('kich_hoat')) {
            $query->where('kich_hoat', $request->kich_hoat);
        }
        
        // Sắp xếp
        $sortBy = $request->get('sort_by', 'do_uu_tien');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $knowledgeBase = $query->paginate(20)->appends($request->all());
        
        // Lấy danh sách chủ đề và danh mục
        $chuDeList = AiChatbotKnowledgeBase::select('chu_de')->distinct()->pluck('chu_de');
        $danhMucList = AiChatbotKnowledgeBase::select('danh_muc')->distinct()->whereNotNull('danh_muc')->pluck('danh_muc');
        
        return view('admin.ai-chatbot.knowledge-base.index', compact('knowledgeBase', 'chuDeList', 'danhMucList'));
    }
    
    /**
     * Show the form for creating a new knowledge
     */
    public function create()
    {
        return view('admin.ai-chatbot.knowledge-base.create');
    }
    
    /**
     * Store a newly created knowledge
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chu_de' => 'required|string|max:255',
            'danh_muc' => 'nullable|string|max:255',
            'cau_hoi_mau' => 'required|string',
            'cau_tra_loi' => 'required|string',
            'tu_khoa' => 'nullable|string',
            'do_uu_tien' => 'nullable|integer|min:0|max:100',
            'kich_hoat' => 'nullable|boolean',
        ]);
        
        $validated['nguoi_tao_id'] = Auth::id();
        $validated['ngay_cap_nhat'] = now();
        $validated['kich_hoat'] = $request->has('kich_hoat');
        
        AiChatbotKnowledgeBase::create($validated);
        
        return redirect()->route('admin.ai-chatbot.knowledge-base.index')
            ->with('success', 'Thêm kiến thức chatbot thành công!');
    }
    
    /**
     * Display the specified knowledge
     */
    public function show(AiChatbotKnowledgeBase $knowledgeBase)
    {
        $knowledgeBase->load('nguoiTao', 'messages.conversation.sinhVien');
        
        // Lấy thống kê sử dụng
        $stats = [
            'total_messages' => $knowledgeBase->messages()->count(),
            'total_conversations' => $knowledgeBase->messages()->distinct('conversation_id')->count(),
            'feedback_huu_ich' => $knowledgeBase->messages()
                ->whereHas('feedback', function($q) {
                    $q->where('danh_gia', 'huu_ich');
                })->count(),
            'feedback_khong_huu_ich' => $knowledgeBase->messages()
                ->whereHas('feedback', function($q) {
                    $q->where('danh_gia', 'khong_huu_ich');
                })->count(),
        ];
        
        return view('admin.ai-chatbot.knowledge-base.show', compact('knowledgeBase', 'stats'));
    }
    
    /**
     * Show the form for editing the specified knowledge
     */
    public function edit(AiChatbotKnowledgeBase $knowledgeBase)
    {
        return view('admin.ai-chatbot.knowledge-base.edit', compact('knowledgeBase'));
    }
    
    /**
     * Update the specified knowledge
     */
    public function update(Request $request, AiChatbotKnowledgeBase $knowledgeBase)
    {
        $validated = $request->validate([
            'chu_de' => 'required|string|max:255',
            'danh_muc' => 'nullable|string|max:255',
            'cau_hoi_mau' => 'required|string',
            'cau_tra_loi' => 'required|string',
            'tu_khoa' => 'nullable|string',
            'do_uu_tien' => 'nullable|integer|min:0|max:100',
            'kich_hoat' => 'nullable|boolean',
        ]);
        
        $validated['ngay_cap_nhat'] = now();
        $validated['kich_hoat'] = $request->has('kich_hoat');
        
        $knowledgeBase->update($validated);
        
        return redirect()->route('admin.ai-chatbot.knowledge-base.index')
            ->with('success', 'Cập nhật kiến thức chatbot thành công!');
    }
    
    /**
     * Remove the specified knowledge
     */
    public function destroy(AiChatbotKnowledgeBase $knowledgeBase)
    {
        $knowledgeBase->delete();
        
        return redirect()->route('admin.ai-chatbot.knowledge-base.index')
            ->with('success', 'Xóa kiến thức chatbot thành công!');
    }
    
    /**
     * Toggle activate status
     */
    public function toggleActivate(AiChatbotKnowledgeBase $knowledgeBase)
    {
        $knowledgeBase->update([
            'kich_hoat' => !$knowledgeBase->kich_hoat,
            'ngay_cap_nhat' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'kich_hoat' => $knowledgeBase->kich_hoat,
            'message' => $knowledgeBase->kich_hoat ? 'Đã kích hoạt' : 'Đã vô hiệu hóa',
        ]);
    }
    
    /**
     * Show statistics page
     */
    public function statistics()
    {
        $stats = [
            'total_knowledge' => AiChatbotKnowledgeBase::count(),
            'active_knowledge' => AiChatbotKnowledgeBase::kichHoat()->count(),
            'by_chu_de' => AiChatbotKnowledgeBase::selectRaw('chu_de, COUNT(*) as count')
                ->groupBy('chu_de')
                ->get(),
            'by_danh_muc' => AiChatbotKnowledgeBase::selectRaw('danh_muc, COUNT(*) as count')
                ->whereNotNull('danh_muc')
                ->groupBy('danh_muc')
                ->get(),
            'most_accessed' => AiChatbotKnowledgeBase::orderBy('luot_truy_cap', 'desc')
                ->take(10)
                ->get(),
            'most_helpful' => AiChatbotKnowledgeBase::orderBy('huu_ich', 'desc')
                ->take(10)
                ->get(),
        ];
        
        return view('admin.ai-chatbot.knowledge-base.statistics', compact('stats'));
    }
    
    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.ai-chatbot.knowledge-base.import');
    }
    
    /**
     * Import knowledge from CSV/Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);
        
        try {
            $file = $request->file('file');
            
            // Read Excel file using PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            // Skip header row
            array_shift($data);
            
            $imported = 0;
            $errors = [];
            
            foreach ($data as $index => $row) {
                try {
                    if (count($row) < 3) {
                        continue; // Skip empty rows
                    }
                    
                    AiChatbotKnowledgeBase::create([
                        'chu_de' => $row[0] ?? '',
                        'danh_muc' => $row[1] ?? null,
                        'cau_hoi_mau' => $row[2] ?? '',
                        'cau_tra_loi' => $row[3] ?? '',
                        'tu_khoa' => $row[4] ?? null,
                        'do_uu_tien' => $row[5] ?? 0,
                        'kich_hoat' => true,
                        'nguoi_tao_id' => Auth::id(),
                        'ngay_cap_nhat' => now(),
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            $message = "Import thành công {$imported} bản ghi.";
            if (!empty($errors)) {
                $message .= " Có " . count($errors) . " lỗi.";
            }
            
            return redirect()->route('admin.ai-chatbot.knowledge-base.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi import: ' . $e->getMessage());
        }
    }
    
    /**
     * Export knowledge to Excel
     */
    public function export(Request $request)
    {
        $query = AiChatbotKnowledgeBase::query();
        
        // Apply filters from index page
        if ($request->filled('chu_de')) {
            $query->where('chu_de', $request->chu_de);
        }
        
        if ($request->filled('kich_hoat')) {
            $query->where('kich_hoat', $request->kich_hoat);
        }
        
        $data = $query->get();
        
        $exportData = [
            ['Chủ đề', 'Danh mục', 'Câu hỏi mẫu', 'Câu trả lời', 'Từ khóa', 'Độ ưu tiên', 'Lượt truy cập', 'Hữu ích', 'Kích hoạt']
        ];
        
        foreach ($data as $item) {
            $exportData[] = [
                $item->chu_de,
                $item->danh_muc,
                $item->cau_hoi_mau,
                $item->cau_tra_loi,
                $item->tu_khoa,
                $item->do_uu_tien,
                $item->luot_truy_cap,
                $item->huu_ich,
                $item->kich_hoat ? 'Có' : 'Không',
            ];
        }
        
        $exporter = new SimpleArrayExport($exportData);
        return $exporter->download('knowledge_base_' . date('Y-m-d_His') . '.xlsx');
    }
}
