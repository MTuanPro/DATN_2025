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
     * Hiển thị danh sách kiến thức chatbot AI với filters và phân trang
     *
     * Function này quản lý toàn bộ kho kiến thức cho AI chatbot, bao gồm
     * các câu hỏi mẫu, câu trả lời, từ khóa, và metadata. Hỗ trợ tìm kiếm,
     * lọc theo nhiều tiêu chí, và sắp xếp linh hoạt.
     *
     * Workflow:
     * 1. Khởi tạo query với eager loading nguoiTao relationship
     * 2. Áp dụng filters từ request:
     *    a. Tìm kiếm (search):
     *       - WHERE cau_hoi_mau LIKE %search%
     *       - OR cau_tra_loi LIKE %search%
     *       - OR tu_khoa LIKE %search%
     *    b. Lọc theo chủ đề (chu_de):
     *       - WHERE chu_de = selected value
     *    c. Lọc theo danh mục (danh_muc):
     *       - WHERE danh_muc = selected category
     *    d. Lọc theo trạng thái (kich_hoat):
     *       - WHERE kich_hoat = true/false
     * 3. Sắp xếp kết quả:
     *    - Default: ORDER BY do_uu_tien DESC
     *    - Custom: sort_by và sort_order từ request
     * 4. Phân trang: 20 items/page với appended query params
     * 5. Lấy danh sách options cho filters:
     *    - chuDeList: Distinct chu_de values
     *    - danhMucList: Distinct danh_muc values (whereNotNull)
     * 6. Return view với data
     *
     * Thông tin hiển thị:
     * - Bảng knowledge base với columns:
     *   + ID, Chủ đề, Danh mục
     *   + Câu hỏi mẫu (truncated)
     *   + Câu trả lời (truncated với tooltip)
     *   + Từ khóa (tags)
     *   + Độ ưu tiên (0-100, progress bar)
     *   + Lượt truy cập (counter)
     *   + Hữu ích (rating)
     *   + Trạng thái (badge active/inactive)
     *   + Người tạo, ngày tạo
     *   + Actions: Edit, Delete, Toggle Active
     * - Filters panel:
     *   + Search box (fulltext)
     *   + Dropdown chủ đề
     *   + Dropdown danh mục
     *   + Radio trạng thái (Tất cả/Kích hoạt/Vô hiệu)
     * - Pagination links
     * - Buttons:
     *   + Thêm kiến thức mới
     *   + Import từ Excel
     *   + Export to Excel
     *   + Xem thống kê
     *
     * Sort options:
     * - do_uu_tien: Độ ưu tiên (default DESC)
     * - luot_truy_cap: Lượt truy cập
     * - huu_ich: Đánh giá hữu ích
     * - created_at: Ngày tạo
     * - updated_at: Ngày cập nhật
     *
     * @param Request $request Chứa filters:
     *   - search: Tìm kiếm fulltext
     *   - chu_de: Lọc theo chủ đề
     *   - danh_muc: Lọc theo danh mục
     *   - kich_hoat: Lọc trạng thái (1/0)
     *   - sort_by: Cột sắp xếp
     *   - sort_order: Thứ tự (asc/desc)
     * @return \Illuminate\View\View
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
     * Lưu kiến thức chatbot AI mới vào database
     *
     * Function này xử lý việc tạo mới một entry trong knowledge base
     * cho AI chatbot, bao gồm validation, auto-fill metadata, và
     * lưu trữ với user tracking.
     *
     * Workflow:
     * 1. Validate input data:
     *    - chu_de: required, string, max 255 chars
     *    - danh_muc: nullable, string, max 255 chars
     *    - cau_hoi_mau: required, string (text)
     *    - cau_tra_loi: required, string (text)
     *    - tu_khoa: nullable, string (comma-separated)
     *    - do_uu_tien: nullable, integer, 0-100
     *    - kich_hoat: nullable, boolean (checkbox)
     * 2. Enrich validated data:
     *    - nguoi_tao_id = Auth::id() (track creator)
     *    - ngay_cap_nhat = now() (timestamp)
     *    - kich_hoat = has('kich_hoat') (convert checkbox)
     * 3. Create new AiChatbotKnowledgeBase record
     * 4. Redirect về index với success message
     *
     * Dữ liệu lưu trữ:
     * - chu_de: Chủ đề chính (VD: "Học phí", "Đăng ký môn học")
     * - danh_muc: Danh mục con (VD: "Thanh toán", "Hướng dẫn")
     * - cau_hoi_mau: Mẫu câu hỏi mà chatbot sẽ match
     * - cau_tra_loi: Câu trả lời chuẩn cho câu hỏi
     * - tu_khoa: Keywords để improve matching (comma-separated)
     * - do_uu_tien: Priority 0-100 (cao hơn = ưu tiên match trước)
     * - kich_hoat: true = enabled, false = disabled
     * - nguoi_tao_id: ID admin tạo entry
     * - ngay_cap_nhat: Timestamp cập nhật cuối
     *
     * AI Matching logic:
     * - Chatbot sẽ dùng data này để match user questions
     * - Sử dụng similarity search trên cau_hoi_mau
     * - Boost bằng tu_khoa
     * - Rank theo do_uu_tien
     * - Chỉ match entries có kich_hoat = true
     *
     * Business rules:
     * - Mỗi entry có thể có nhiều keywords (comma-separated)
     * - Độ ưu tiên cao (90-100): Câu hỏi quan trọng, xuất hiện thường xuyên
     * - Độ ưu tiên trung bình (50-89): Câu hỏi phổ biến
     * - Độ ưu tiên thấp (0-49): Câu hỏi ít gặp, edge cases
     * - Auto set kich_hoat = true nếu checkbox được check
     *
     * @param Request $request Form data từ create view
     * @return \Illuminate\Http\RedirectResponse Redirect về index với success message
     * @throws \Illuminate\Validation\ValidationException Nếu validation fails
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
        ]);
        
        $validated['nguoi_tao_id'] = Auth::id();
        $validated['ngay_cap_nhat'] = now();
        $validated['kich_hoat'] = $request->has('kich_hoat');
        
        AiChatbotKnowledgeBase::create($validated);
        
        return redirect()->route('admin.ai-chatbot.knowledge-base.index')
            ->with('success', 'Thêm kiến thức chatbot thành công!');
    }
    
    /**
     * Hiển thị chi tiết một knowledge base entry với thống kê sử dụng
     *
     * Function này cung cấp view chi tiết của một kiến thức chatbot,
     * bao gồm full content, metadata, và analytics về hiệu quả sử dụng
     * trong các cuộc hội thoại thực tế.
     *
     * Workflow:
     * 1. Lấy knowledge base entry theo route model binding
     * 2. Eager load relationships:
     *    - nguoiTao: Admin đã tạo entry này
     *    - messages: Các AI messages sử dụng knowledge này
     *      + messages.conversation: Cuộc hội thoại chứa message
     *      + messages.conversation.sinhVien: Sinh viên hỏi
     * 3. Tính toán statistics:
     *    a. total_messages: Tổng số lần knowledge được sử dụng
     *    b. total_conversations: Số cuộc hội thoại khác nhau
     *    c. feedback_huu_ich: Số feedback positive
     *       - Join với feedback table
     *       - WHERE danh_gia = 'huu_ich'
     *    d. feedback_khong_huu_ich: Số feedback negative
     *       - WHERE danh_gia = 'khong_huu_ich'
     * 4. Return view với data
     *
     * Thông tin hiển thị:
     * - Thông tin cơ bản:
     *   + Chủ đề, Danh mục
     *   + Câu hỏi mẫu (full text)
     *   + Câu trả lời (full text, formatted)
     *   + Từ khóa (tags)
     *   + Độ ưu tiên (progress bar)
     *   + Trạng thái (badge)
     * - Metadata:
     *   + Người tạo (tên, email)
     *   + Ngày tạo
     *   + Ngày cập nhật cuối
     * - Statistics cards:
     *   + Tổng lượt sử dụng (total_messages)
     *   + Số cuộc hội thoại (total_conversations)
     *   + Feedback hữu ích (green card)
     *   + Feedback không hữu ích (red card)
     *   + Tỷ lệ hữu ích (%) với chart
     * - Usage timeline:
     *   + Line chart sử dụng theo thời gian
     *   + Top conversations sử dụng knowledge này
     * - Actions:
     *   + Chỉnh sửa
     *   + Xoá
     *   + Toggle kích hoạt
     *   + Sao chép để tạo mới
     *
     * Analytics insights:
     * - Nếu total_messages cao nhưng feedback_huu_ich thấp:
     *   => Cần review và cải thiện câu trả lời
     * - Nếu total_messages = 0:
     *   => Knowledge chưa bao giờ được match, cần review cau_hoi_mau
     *
     * @param AiChatbotKnowledgeBase $knowledgeBase Route model binding
     * @return \Illuminate\View\View Detail view với data và stats
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
     * Cập nhật thông tin knowledge base entry hiện có
     *
     * Function này xử lý việc update một knowledge base entry,
     * validate dữ liệu mới, và cập nhật timestamp tự động.
     *
     * Workflow:
     * 1. Validate input data (same rules as store):
     *    - chu_de: required, string, max 255
     *    - danh_muc: nullable, string, max 255
     *    - cau_hoi_mau: required, text
     *    - cau_tra_loi: required, text
     *    - tu_khoa: nullable, string
     *    - do_uu_tien: nullable, integer 0-100
     *    - kich_hoat: nullable, boolean
     * 2. Enrich data:
     *    - ngay_cap_nhat = now() (auto timestamp)
     *    - kich_hoat = has('kich_hoat') (checkbox)
     * 3. Update existing record
     * 4. Redirect về index với success message
     *
     * Side effects khi update:
     * - Nếu thay đổi cau_hoi_mau hoặc tu_khoa:
     *   => AI matching behavior sẽ thay đổi
     * - Nếu thay đổi cau_tra_loi:
     *   => User sẽ nhận câu trả lời mới cho các hội thoại tiếp theo
     * - Nếu toggle kich_hoat = false:
     *   => Knowledge sẽ không còn được sử dụng trong matching
     * - Nếu thay đổi do_uu_tien:
     *   => Thứ tự priority trong search results sẽ thay đổi
     *
     * Best practices:
     * - Nên test lại chatbot sau khi update
     * - Nên giữ log của các thay đổi quan trọng
     * - Nên kiểm tra usage stats trước khi vô hiệu hoá
     * - Update nhiều entries cùng chu_de thì nên có consistency
     *
     * @param Request $request Form data từ edit view
     * @param AiChatbotKnowledgeBase $knowledgeBase Entry cần update
     * @return \Illuminate\Http\RedirectResponse Redirect về index
     * @throws \Illuminate\Validation\ValidationException Nếu validation fails
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
     * Toggle trạng thái kích hoạt/vô hiệu hoá knowledge base entry
     *
     * Function này cho phép bật/tắt nhanh một knowledge base entry
     * thông qua AJAX, không cần reload trang, với JSON response.
     *
     * Workflow:
     * 1. Toggle boolean kich_hoat field:
     *    - Nếu đang true => set false
     *    - Nếu đang false => set true
     * 2. Auto update ngay_cap_nhat = now()
     * 3. Save to database
     * 4. Return JSON response với:
     *    - success: true
     *    - kich_hoat: New state (boolean)
     *    - message: User-friendly message
     *
     * AJAX response structure:
     * ```json
     * {
     *   "success": true,
     *   "kich_hoat": true,
     *   "message": "Đã kích hoạt"
     * }
     * ```
     *
     * Frontend handling:
     * - JavaScript nhận response
     * - Update UI:
     *   + Toggle badge color (green/gray)
     *   + Change button text
     *   + Show toast notification
     * - No page reload needed
     *
     * Use cases:
     * - Tạm thời vô hiệu knowledge không còn chính xác
     * - Test xem chatbot hoạt động ra sao khi thiếu knowledge
     * - Quản lý seasonal content (kích hoạt theo mùa)
     * - A/B testing chatbot responses
     *
     * Impact:
     * - kich_hoat = false:
     *   + Knowledge sẽ SKIP trong AI matching
     *   + Không hiển thị trong active list
     *   + Stats vẫn giữ nguyên
     * - kich_hoat = true:
     *   + Knowledge trở lại active pool
     *   + Chatbot sẽ sử dụng lại
     *
     * @param AiChatbotKnowledgeBase $knowledgeBase Entry cần toggle
     * @return \Illuminate\Http\JsonResponse JSON response cho AJAX
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
     * Hiển thị trang thống kê tổng quan về knowledge base
     *
     * Function này cung cấp analytics dashboard cho knowledge base,
     * giúp admin hiểu performance, coverage, và areas cần cải thiện.
     *
     * Thống kê được tính toán:
     * 1. Tổng quan:
     *    - total_knowledge: Tổng số entries
     *    - active_knowledge: Số entries đang kích hoạt
     *      + Scope: kichHoat() where kich_hoat = true
     * 2. Phân bố theo chủ đề (by_chu_de):
     *    - GROUP BY chu_de
     *    - COUNT(*) as count
     *    - Sắp xếp theo count DESC
     *    - Hiển thị top topics
     * 3. Phân bố theo danh mục (by_danh_muc):
     *    - GROUP BY danh_muc
     *    - WHERE danh_muc IS NOT NULL
     *    - COUNT(*) as count
     *    - Hiển thị categories coverage
     * 4. Top entries được truy cập nhiều nhất (most_accessed):
     *    - ORDER BY luot_truy_cap DESC
     *    - LIMIT 10
     *    - Shows popular topics
     * 5. Top entries hữu ích nhất (most_helpful):
     *    - ORDER BY huu_ich DESC
     *    - LIMIT 10
     *    - Shows best quality content
     *
     * Dashboard hiển thị:
     * - Summary cards:
     *   + Tổng kiến thức (icon book)
     *   + Kiến thức active (icon check, green)
     *   + Coverage ratio (%)
     * - Charts:
     *   + Pie chart: Phân bố theo chủ đề
     *   + Bar chart: Phân bố theo danh mục
     *   + Line chart: Xu hướng tăng trưởng theo thời gian
     * - Top 10 tables:
     *   + Most accessed entries
     *   + Most helpful entries
     *   + Recently updated
     * - Gap analysis:
     *   + Topics với ít coverage
     *   + Low helpfulness entries (cần review)
     *   + Unused entries (luot_truy_cap = 0)
     *
     * Insights cho admin:
     * - Nếu total > active * 2:
     *   => Nhiều entries bị disabled, cần review
     * - Nếu một chu_de chiếm > 50%:
     *   => Imbalance, cần diversify
     * - Nếu most_accessed và most_helpful khác nhau:
     *   => Popular không = Quality
     * - Unused entries:
     *   => Cần improve matching hoặc xóa
     *
     * @return \Illuminate\View\View Statistics dashboard
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
     * Import knowledge base entries từ file CSV/Excel
     *
     * Function này cho phép bulk import knowledge base từ file,
     * hỗ trợ CSV, XLSX, XLS formats, với error handling và reporting.
     *
     * Workflow:
     * 1. Validate uploaded file:
     *    - Required: file field phải tồn tại
     *    - MIME types: csv, xlsx, xls
     *    - Max size: 2048 KB (2MB)
     * 2. Read file bằng PhpSpreadsheet:
     *    - IOFactory::load() auto-detect format
     *    - Get active worksheet
     *    - Convert to array
     * 3. Process data:
     *    - Skip header row (array_shift)
     *    - Loop through data rows
     *    - Với mỗi row:
     *      a. Validate: Skip nếu < 3 columns (empty row)
     *      b. Map columns:
     *         - $row[0] => chu_de
     *         - $row[1] => danh_muc
     *         - $row[2] => cau_hoi_mau
     *         - $row[3] => cau_tra_loi
     *         - $row[4] => tu_khoa
     *         - $row[5] => do_uu_tien
     *      c. Create entry với:
     *         - kich_hoat = true (default)
     *         - nguoi_tao_id = Auth::id()
     *         - ngay_cap_nhat = now()
     *      d. Increment imported counter
     *      e. Nếu error: Catch, log vào errors array
     * 4. Return results:
     *    - Success message với count
     *    - Flash import_errors nếu có
     *    - Redirect về index
     *
     * Excel file format expected:
     * | Chủ đề | Danh mục | Câu hỏi mẫu | Câu trả lời | Từ khóa | Độ ưu tiên |
     * |----------|-----------|---------------|--------------|----------|----------------|
     * | Học phí  | Thanh toán | Cách thanh...  | Bạn có thể... | thanh toán... | 80 |
     *
     * Error handling:
     * - File upload errors: Return back với error message
     * - Row processing errors:
     *   + Catch exception
     *   + Log "Dòng X: error message"
     *   + Continue với rows khác
     *   + Show summary cuối cùng
     * - Invalid data:
     *   + Skip empty rows
     *   + Use null coalescing cho optional fields
     *
     * Success response:
     * - "Import thành công 150 bản ghi."
     * - Nếu có lỗi: "Import thành công 150 bản ghi. Có 5 lỗi."
     * - Flash errors array để hiển thị chi tiết
     *
     * Best practices:
     * - Download template trước khi import
     * - Test với file nhỏ trước
     * - Review imported data sau khi import
     * - Backup trước khi import lớn
     *
     * @param Request $request Chứa uploaded file
     * @return \Illuminate\Http\RedirectResponse Với success/error messages
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
     * Export knowledge base entries ra file Excel
     *
     * Function này cho phép xuất toàn bộ hoặc một phần knowledge base
     * ra file Excel (.xlsx) với format chuẩn, hỗ trợ filters từ index page.
     *
     * Workflow:
     * 1. Khởi tạo query không eager loading (raw data)
     * 2. Áp dụng filters từ request (nếu có):
     *    a. Filter theo chủ đề:
     *       - WHERE chu_de = $request->chu_de
     *    b. Filter theo trạng thái:
     *       - WHERE kich_hoat = $request->kich_hoat
     *    - Note: Có thể mở rộng thêm filters khác
     * 3. Get all filtered data (không paginate)
     * 4. Build export data array:
     *    a. Header row:
     *       - ['Chủ đề', 'Danh mục', 'Câu hỏi mẫu', ...]
     *    b. Data rows:
     *       - Map mỗi entry thành array row
     *       - Convert boolean kich_hoat => 'Có'/'Không'
     *       - Include analytics: luot_truy_cap, huu_ich
     * 5. Tạo Excel file:
     *    - Use SimpleArrayExport exporter
     *    - Filename: knowledge_base_YYYY-MM-DD_HHiiss.xlsx
     * 6. Trigger browser download
     *
     * Exported columns:
     * 1. Chủ đề (chu_de)
     * 2. Danh mục (danh_muc)
     * 3. Câu hỏi mẫu (cau_hoi_mau)
     * 4. Câu trả lời (cau_tra_loi)
     * 5. Từ khóa (tu_khoa)
     * 6. Độ ưu tiên (do_uu_tien)
     * 7. Lượt truy cập (luot_truy_cap)
     * 8. Hữu ích (huu_ich)
     * 9. Kích hoạt (kich_hoat - 'Có'/'Không')
     *
     * Use cases:
     * - Backup knowledge base
     * - Migrate to another system
     * - Offline review/editing
     * - Share với team khác
     * - Archive historical data
     * - Bulk editing (import lại sau khi sửa)
     *
     * File naming:
     * - Pattern: knowledge_base_2025-11-23_143052.xlsx
     * - Includes date và time để không overwrite
     * - Dễ sort và track versions
     *
     * Excel features:
     * - Auto-width columns
     * - Header row bold
     * - UTF-8 encoding (tiếng Việt)
     * - Compatible với Excel 2007+
     *
     * @param Request $request Optional filters: chu_de, kich_hoat
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Excel file download
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
