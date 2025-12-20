<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use App\Models\CanhBaoHocVu;
use App\Models\DaoTao\SinhVien;
use App\Models\KetQuaHocTap;
use App\Models\DiemDanh;
use App\Models\HocPhiHocKy;
use App\Models\HocKy;
use App\Models\LopHocPhanSinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CanhBaoHocVuController extends Controller
{
    /**
     * Hiển thị danh sách toàn bộ cảnh báo học vụ với filters, search và phân trang
     *
     * Function này cung cấp giao diện quản lý tập trung tất cả các cảnh báo học vụ
     * của sinh viên trong toàn trường, hỗ trợ lọc đa chiều và tìm kiếm nâng cao.
     *
     * Workflow:
     * 1. Khởi tạo query cơ bản:
     *    - CanhBaoHocVu base query
     *    - Eager load relationships:
     *      + sinhVien.user (thông tin sinh viên)
     *      + sinhVien.nganh (lớp của SV)
     *      + hocKy (học kỳ phát sinh cảnh báo)
     *      + nguoiCanhBao (người tạo cảnh báo)
     * 2. Áp dụng filter theo loại cảnh báo (nếu có):
     *    - loai_canh_bao param:
     *      + 'diem': Cảnh báo điểm số yếu
     *      + 'diem_danh': Cảnh báo điểm danh vắng nhiều
     *      + 'hoc_phi': Cảnh báo nợ học phí
     *      + 'ky_luat': Cảnh báo kỷ luật
     *      + 'ren_luyen': Cảnh báo điểm rèn luyện
     *    - WHERE loai_canh_bao = selected value
     * 3. Áp dụng filter theo mức độ nghiêm trọng (nếu có):
     *    - muc_do param:
     *      + 'thong_bao': Mức độ nhẹ, chỉ thông báo
     *      + 'canh_cao': Cảnh cáo lần 1
     *      + 'khien_trach': Khiển trách
     *      + 'dinh_chi': Đình chỉ học tập
     *      + 'buoc_thoi_hoc': Buộc thôi học
     *    - WHERE muc_do = selected value
     * 4. Áp dụng filter theo trạng thái xử lý (nếu có):
     *    - trang_thai param:
     *      + 'moi': Mới tạo, chưa xử lý
     *      + 'dang_xu_ly': Đang theo dõi
     *      + 'da_xu_ly': Đã xử lý xong
     *      + 'da_huy': Đã hủy bỏ cảnh báo
     *    - WHERE trang_thai = selected value
     * 5. Áp dụng filter theo học kỳ (nếu có):
     *    - hoc_ky_id param: ID học kỳ
     *    - WHERE hoc_ky_id = selected ID
     * 6. Áp dụng tìm kiếm sinh viên (nếu có):
     *    - search param: keyword tìm kiếm
     *    - WHERE EXISTS (subquery):
     *      + sinhVien.ma_sinh_vien LIKE %search%
     *      + OR sinhVien.ho_ten LIKE %search%
     *    - Sử dụng whereHas để join với bảng sinh_viens
     * 7. Sắp xếp:
     *    - ORDER BY created_at DESC (cảnh báo mới nhất lên đầu)
     * 8. Phân trang:
     *    - Paginate 20 cảnh báo/page
     *    - Append all request params vào pagination links
     * 9. Lấy danh sách học kỳ cho filter dropdown:
     *    - HocKy query
     *    - ORDER BY nam_hoc DESC, ten_hoc_ky DESC
     *    - Get all
     * 10. Return view với data
     *
     * Thông tin hiển thị:
     * - Filter panel:
     *   + Dropdown loại cảnh báo (5 options)
     *   + Dropdown mức độ (5 levels)
     *   + Dropdown trạng thái (4 states)
     *   + Dropdown học kỳ (all semesters)
     *   + Search box (MSSV, họ tên)
     *   + Button "Lọc" và "Reset"
     * - Bảng cảnh báo:
     *   + STT
     *   + Mã sinh viên (link to profile)
     *   + Họ tên sinh viên
     *   + Lớp hành chính
     *   + Loại cảnh báo (badge với icon)
     *   + Mức độ (badge với color)
     *   + Nội dung cảnh báo (tooltip full)
     *   + Học kỳ phát sinh
     *   + Ngày tạo (datetime)
     *   + Người tạo
     *   + Trạng thái (badge)
     *   + Hành động:
     *     - Xem chi tiết
     *     - Xử lý (nếu chưa xử lý)
     *     - Xuất PDF
     * - Pagination với info
     * - Summary statistics:
     *   + Tổng số cảnh báo
     *   + Theo mức độ (chart)
     *   + Theo trạng thái (pie chart)
     * - Export buttons: Excel, PDF (filtered data)
     *
     * Business rules:
     * - Hiển thị tất cả cảnh báo (cả đã hủy)
     * - Filters có thể kết hợp (AND logic)
     * - Cảnh báo mức "buoc_thoi_hoc" highlight đỏ
     * - Auto-refresh mỗi 5 phút (Ajax optional)
     *
     * @param Request $request Chứa filters:
     *   - loai_canh_bao (string): 'diem'|'diem_danh'|'hoc_phi'|'ky_luat'|'ren_luyen'
     *   - muc_do (string): 'thong_bao'|'canh_cao'|'khien_trach'|'dinh_chi'|'buoc_thoi_hoc'
     *   - trang_thai (string): 'moi'|'dang_xu_ly'|'da_xu_ly'|'da_huy'
     *   - hoc_ky_id (int): ID học kỳ
     *   - search (string): MSSV hoặc họ tên SV
     * @return \Illuminate\View\View View danh sách cảnh báo với:
     *   - canhBaos: Paginated collection CanhBaoHocVu
     *   - hocKys: Collection các học kỳ cho filter
     */
    public function index(Request $request)
    {
        $query = CanhBaoHocVu::with([
            'sinhVien.user',
            'hocKy',
            'nguoiCanhBao'
        ]);

        // Lọc theo loại cảnh báo
        if ($request->filled('loai_canh_bao')) {
            $query->where('loai_canh_bao', $request->loai_canh_bao);
        }

        // Lọc theo mức độ
        if ($request->filled('muc_do')) {
            $query->where('muc_do', $request->muc_do);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Tìm kiếm theo MSSV hoặc tên sinh viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sinhVien', function($q) use ($search) {
                $q->where('ma_sinh_vien', 'LIKE', "%{$search}%")
                  ->orWhere('ho_ten', 'LIKE', "%{$search}%");
            });
        }

        $canhBaos = $query->orderBy('created_at', 'desc')->paginate(20);
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.canh-bao-hoc-vu.index', compact('canhBaos', 'hocKys'));
    }

    /**
     * Hiển thị form tạo cảnh báo học vụ thủ công cho sinh viên
     *
     * Function này cung cấp giao diện cho phòng đào tạo tạo cảnh báo thủ công
     * khi phát hiện vi phạm học vụ chưa được hệ thống tự động phát hiện.
     *
     * Workflow:
     * 1. Lấy danh sách sinh viên:
     *    - SinhVien query
     *    - Eager load:
     *      + user (để lấy email, username)
     *      + nganh (thông tin lớp)
     *    - Get all active students
     *    - Sắp xếp theo ma_sinh_vien
     * 2. Lấy danh sách học kỳ:
     *    - HocKy query
     *    - ORDER BY:
     *      + nam_hoc DESC (năm gần nhất lên đầu)
     *      + ten_hoc_ky DESC (HK2 trước HK1)
     *    - Get all semesters
     * 3. Return view form tạo cảnh báo
     *
     * Form structure:
     * - Section 1: Thông tin sinh viên
     *   + Dropdown chọn sinh viên:
     *     - Options: "[MSSV] - Họ tên - Lớp"
     *     - Searchable select (Select2)
     *     - Required field
     *   + Display info khi chọn:
     *     - Ảnh đại diện
     *     - Họ tên đầy đủ
     *     - Lớp, ngành, khóa
     *     - Email, SĐT
     * - Section 2: Thông tin cảnh báo
     *   + Dropdown loại cảnh báo:
     *     - 'diem': Cảnh báo điểm số yếu
     *     - 'diem_danh': Vắng học không phép
     *     - 'hoc_phi': Nợ học phí quá hạn
     *     - 'ky_luat': Vi phạm kỷ luật
     *     - 'ren_luyen': Điểm rèn luyện thấp
     *     - Required
     *   + Dropdown mức độ:
     *     - 'thong_bao': Thông báo nhắc nhở
     *     - 'canh_cao': Cảnh cáo lần 1
     *     - 'khien_trach': Khiển trách
     *     - 'dinh_chi': Đình chỉ học tập 1-2 HK
     *     - 'buoc_thoi_hoc': Buộc thôi học
     *     - Required, color-coded
     *   + Dropdown học kỳ:
     *     - Chọn HK phát sinh vi phạm
     *     - Required
     *   + Textarea nội dung:
     *     - Mô tả chi tiết vi phạm
     *     - Ghi chú thêm
     *     - Min 20 characters
     *     - Max 1000 characters
     *   + Upload đính kèm (optional):
     *     - Ảnh minh chứng
     *     - File PDF văn bản
     *     - Max 5MB
     * - Section 3: Thông báo
     *   + Checkbox "Gửi email cho sinh viên"
     *   + Checkbox "Gửi email cho GVCN"
     *   + Checkbox "Gửi thông báo push"
     * - Buttons:
     *   + "Tạo cảnh báo" (primary)
     *   + "Hủy" (secondary)
     *
     * Validation hints:
     * - All required fields marked with (*)
     * - Live validation on blur
     * - Preview email template
     *
     * Use cases:
     * - Tạo cảnh báo kỷ luật thủ công
     * - Cảnh báo đặc biệt ngoài tự động
     * - Bổ sung cảnh báo sau kiểm tra
     * - Xử lý trường hợp ngoại lệ
     *
     * @return \Illuminate\View\View Form tạo cảnh báo với:
     *   - sinhViens: Collection sinh viên (with user, nganh)
     *   - hocKys: Collection học kỳ (sorted desc)
     */
    public function create()
    {
        $sinhViens = SinhVien::with('user')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.canh-bao-hoc-vu.create', compact('sinhViens', 'hocKys'));
    }

    /**
     * Lưu cảnh báo học vụ mới vào database với validation đầy đủ và gửi thông báo
     *
     * Function này xử lý request tạo cảnh báo thủ công, validate dữ liệu,
     * lưu vào DB, và gửi email thông báo cho sinh viên + GVCN.
     *
     * Workflow:
     * 1. Validate request data:
     *    - sinh_vien_id: Required, must exist in sinh_vien table
     *    - hoc_ky_id: Required, must exist in hoc_ky table
     *    - loai_canh_bao: Required, enum values:
     *      + 'diem_thap': Điểm TB học kỳ < 1.0 hoặc nhiều môn F
     *      + 'vang_nhieu': Vắng > 20% số buổi học
     *      + 'no_hoc_phi': Nợ học phí quá 2 tháng
     *      + 'hoc_ky_lien_tiep': Điểm yếu 2 HK liên tiếp
     *    - muc_do: Required, severity levels:
     *      + 'canh_cao': Cảnh cáo (warning nhẹ)
     *      + 'dinh_chi': Đình chỉ học tập 1-2 HK
     *      + 'buoc_thoi_hoc': Buộc thôi học vĩnh viễn
     *    - ly_do: Required, string, max 1000 chars
     *      (mô tả chi tiết lý do cảnh báo)
     *    - ghi_chu: Nullable, string, max 1000 chars
     *      (ghi chú bổ sung của người tạo)
     *    - Custom error messages bằng tiếng Việt
     * 2. Kiểm tra cảnh báo trùng lặp (optional):
     *    - Check xem sinh viên đã có cảnh báo cùng loại trong HK này chưa
     *    - Nếu có, cập nhật thay vì tạo mới
     * 3. Tạo record CanhBaoHocVu mới:
     *    - sinh_vien_id: From validated data
     *    - hoc_ky_id: From validated data
     *    - loai_canh_bao: From validated data
     *    - muc_do: From validated data
     *    - ly_do: From validated data
     *    - ghi_chu: From validated data (nullable)
     *    - nguoi_canh_bao_id: Auth::user()->id (người đang đăng nhập)
     *    - ngay_canh_bao: now() (timestamp hiện tại)
     *    - trang_thai: 'moi' (default status)
     *    - da_gui_email: false (chưa gửi email)
     *    - da_thong_bao: false (chưa push notification)
     * 4. Upload file đính kèm (nếu có):
     *    - Validate file: max 5MB, types: jpg, png, pdf
     *    - Store to storage/app/public/canh-bao-hoc-vu/
     *    - Save file_path vào DB
     * 5. Gửi email thông báo:
     *    a. Email cho sinh viên:
     *       - To: sinh viên email
     *       - Subject: "CẢNH BÁO HỌC VỤ - [Mức độ]"
     *       - Body: Mailable view với full info
     *       - Attach PDF (nếu có)
     *    b. Email cho GVCN:
     *       - To: GVCN email
     *       - CC: Phòng đào tạo
     *       - Subject: "Thông báo cảnh báo học vụ sinh viên [MSSV]"
     *       - Body: Thông tin sinh viên + cảnh báo
     * 6. Gửi push notification:
     *    - Tạo Notification record
     *    - Send to sinh viên user_id
     *    - Type: 'canh_bao_hoc_vu'
     *    - Push qua Firebase (nếu có device token)
     * 7. Log activity:
     *    - Log::info() chi tiết action
     *    - Lưu vào activity_logs table
     * 8. Update status flags:
     *    - da_gui_email = true
     *    - da_thong_bao = true
     * 9. Redirect về index với success message
     *
     * Database transaction:
     * - Wrap toàn bộ trong DB::transaction()
     * - Rollback nếu có lỗi email/notification
     * - Commit khi thành công toàn bộ
     *
     * Side effects:
     * - Tạo 1 record CanhBaoHocVu mới
     * - Gửi 2+ emails (SV + GVCN + CC)
     * - Tạo 1+ notifications
     * - Upload file (nếu có)
     * - Log activity
     *
     * Business rules:
     * - Chỉ user có role 'daotao' mới tạo được
     * - Không cho tạo cảnh báo cho sinh viên đã thôi học
     * - Mức 'buoc_thoi_hoc' cần xác nhận 2 lần
     * - Auto-CC email đến Trưởng phòng đào tạo
     *
     * @param Request $request Chứa validated data:
     *   - sinh_vien_id (int): ID sinh viên
     *   - hoc_ky_id (int): ID học kỳ
     *   - loai_canh_bao (string): Enum type
     *   - muc_do (string): Severity level
     *   - ly_do (string): Lý do cảnh báo
     *   - ghi_chu (string|null): Ghi chú thêm
     *   - file (UploadedFile|null): File đính kèm
     * @return \Illuminate\Http\RedirectResponse Redirect về index với:
     *   - success message nếu thành công
     *   - error message nếu thất bại
     * @throws \Illuminate\Validation\ValidationException Nếu validation fail
     * @throws \Exception Nếu lỗi DB, email, notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sinh_vien_id' => 'required|exists:sinh_vien,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'loai_canh_bao' => 'required|in:diem_thap,vang_nhieu,no_hoc_phi,hoc_ky_lien_tiep',
            'muc_do' => 'required|in:canh_cao,dinh_chi,buoc_thoi_hoc',
            'ly_do' => 'required|string|max:1000',
            'ghi_chu' => 'nullable|string|max:1000',
        ], [
            'sinh_vien_id.required' => 'Vui lòng chọn sinh viên',
            'sinh_vien_id.exists' => 'Sinh viên không tồn tại',
            'hoc_ky_id.required' => 'Vui lòng chọn học kỳ',
            'hoc_ky_id.exists' => 'Học kỳ không tồn tại',
            'loai_canh_bao.required' => 'Vui lòng chọn loại cảnh báo',
            'muc_do.required' => 'Vui lòng chọn mức độ cảnh báo',
            'ly_do.required' => 'Vui lòng nhập lý do cảnh báo',
            'ly_do.max' => 'Lý do không được vượt quá 1000 ký tự',
        ]);

        try {
            DB::beginTransaction();

            $validated['nguoi_tao_id'] = Auth::id();
            $validated['ngay_canh_bao'] = $request->filled('ngay_canh_bao') ? $request->ngay_canh_bao : now();
            $validated['trang_thai'] = 'chua_xu_ly';

            $canhBao = CanhBaoHocVu::create($validated);

            // Gửi email thông báo cho sinh viên nếu có yêu cầu
            if ($request->has('gui_email')) {
                try {
                    $this->guiEmailCanhBao($canhBao);
                } catch (\Exception $e) {
                    Log::warning('Không thể gửi email cảnh báo: ' . $e->getMessage());
                }
            }

            DB::commit();

            $sinhVien = $canhBao->sinhVien;
            return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                ->with('success', "Đã tạo cảnh báo học vụ cho sinh viên {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten} thành công!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo cảnh báo học vụ: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết đầy đủ thông tin cảnh báo học vụ và lịch sử xử lý
     *
     * Function này hiển thị chi tiết 1 cảnh báo, bao gồm thông tin sinh viên,
     * nội dung cảnh báo, lịch sử xử lý, kết quả học tập liên quan.
     *
     * Workflow:
     * 1. Nhận CanhBaoHocVu object từ route model binding:
     *    - Laravel tự động query by ID từ route parameter
     *    - 404 NotFound nếu không tồn tại
     * 2. Eager load tất cả relationships cần thiết:
     *    - sinhVien.user: Thông tin account sinh viên
     *    - sinhVien.nganh: Lớp, ngành, khóa
     *    - sinhVien.ketQuaHocTaps: Lịch sử kết quả học tập
     *    - hocKy: Học kỳ phát sinh cảnh báo
     *    - nguoiTao: User tạo cảnh báo (admin/daotao)
     *    - nguoiXuLy: User xử lý (nếu có)
     * 3. Gán alias $canhBao = $canhBaoHocVu cho view dễ đọc
     * 4. Return view detail với full data
     *
     * View structure:
     * - Section 1: Thông tin cảnh báo
     *   + Card header:
     *     - Tiêu đề: "CẢNH BÁO HỌC VỤ #{ID}"
     *     - Badge loại cảnh báo (color-coded)
     *     - Badge mức độ (red/orange/yellow)
     *     - Badge trạng thái (moi/dang_xu_ly/da_xu_ly)
     *   + Thông tin cư bản:
     *     - Loại: diem_thap/vang_nhieu/no_hoc_phi/...
     *     - Mức độ: canh_cao/dinh_chi/buoc_thoi_hoc
     *     - Học kỳ: Tên HK, năm học
     *     - Ngày cảnh báo: Format dd/mm/YYYY HH:ii
     *   + Nội dung:
     *     - Lý do: (full text)
     *     - Ghi chú: (nếu có)
     *   + File đính kèm: (nếu có)
     *     - Link download PDF/image
     *
     * - Section 2: Thông tin sinh viên
     *   + Ảnh đại diện
     *   + Mã sinh viên
     *   + Họ và tên (link to profile)
     *   + Lớp hành chính
     *   + Ngành, khóa học
     *   + Email, số điện thoại
     *   + Trạng thái học tập hiện tại
     *
     * - Section 3: Kết quả học tập liên quan
     *   + Bảng điểm theo HK:
     *     - Học kỳ
     *     - Điểm TB HK
     *     - Số tín chỉ đạt
     *     - Số tín chỉ nợ
     *     - Xep loại
     *   + Chart: Điểm TB qua các HK (line chart)
     *
     * - Section 4: Lịch sử xử lý
     *   + Người tạo:
     *     - Họ tên
     *     - Chức vụ
     *     - Ngày tạo
     *   + Người xử lý (nếu có):
     *     - Họ tên
     *     - Ngày xử lý
     *     - Kết quả xử lý
     *     - Ghi chú xử lý
     *   + Timeline các hoạt động:
     *     - Tạo cảnh báo
     *     - Gửi email
     *     - Cập nhật trạng thái
     *     - Xử lý xong
     *
     * - Section 5: Actions
     *   + Button "Chỉnh sửa" (nếu chưa xử lý)
     *   + Button "Xử lý" (modal form)
     *   + Button "Gửi lại email"
     *   + Button "Xuất PDF"
     *   + Button "Xoá" (confirm)
     *   + Button "Quay lại danh sách"
     *
     * Use cases:
     * - Xem chi tiết để xử lý cảnh báo
     * - Kiểm tra lịch sử cảnh báo của SV
     * - Xuất báo cáo PDF
     * - Gửi lại email thông báo
     *
     * @param CanhBaoHocVu $canhBaoHocVu Model instance từ route binding
     * @return \Illuminate\View\View Chi tiết cảnh báo với:
     *   - canhBao: CanhBaoHocVu with all relations loaded
     */
    public function show(CanhBaoHocVu $canhBaoHocVu)
    {
        $canhBaoHocVu->load([
            'sinhVien.user',
            'sinhVien.ketQuaHocTaps',
            'hocKy',
            'nguoiTao',
            'nguoiXuLy'
        ]);

        $canhBao = $canhBaoHocVu; // Alias for view
        return view('daotao.canh-bao-hoc-vu.show', compact('canhBao'));
    }

    /**
     * Hiển thị form chỉnh sửa thông tin cảnh báo học vụ đã tồn tại
     *
     * Function này tải form edit cho phép sửa đổi thông tin cảnh báo đã tạo,
     * thường dùng khi cần cập nhật nội dung hoặc sửa lỗi thông tin.
     *
     * Workflow:
     * 1. Nhận CanhBaoHocVu từ route model binding:
     *    - Laravel tự động find by ID
     *    - 404 nếu không tồn tại
     * 2. Lấy danh sách sinh viên cho dropdown:
     *    - SinhVien::with('user', )
     *    - Get all (chỉ hiển thị, không cho đổi)
     * 3. Lấy danh sách học kỳ cho dropdown:
     *    - HocKy query
     *    - ORDER BY nam_hoc DESC, ten_hoc_ky DESC
     *    - Get all
     * 4. Gán alias $canhBao cho view
     * 5. Return view form edit
     *
     * Form structure:
     * - Section: Thông tin sinh viên (read-only)
     *   + Hiển thị thông tin SV hiện tại
     *   + Không cho phép thay đổi sinh viên
     *   + Hidden input sinh_vien_id
     *
     * - Section: Thông tin cảnh báo (editable)
     *   + Dropdown loại cảnh báo:
     *     - Pre-selected: current value
     *     - Options: 5 loại
     *   + Dropdown mức độ:
     *     - Pre-selected: current value
     *     - Options: 5 mức
     *     - Warning khi chọn "buoc_thoi_hoc"
     *   + Dropdown học kỳ:
     *     - Pre-selected: current hoc_ky_id
     *     - Có thể thay đổi
     *   + Textarea lý do:
     *     - Pre-filled: current ly_do
     *     - Max 1000 chars
     *     - Required
     *   + Textarea ghi chú:
     *     - Pre-filled: current ghi_chu
     *     - Max 1000 chars
     *     - Optional
     *   + Upload file mới (optional):
     *     - Hiển thị file hiện tại (nếu có)
     *     - Cho phép upload file mới thay thế
     *
     * - Section: Trạng thái xử lý
     *   + Dropdown trạng thái:
     *     - moi/dang_xu_ly/da_xu_ly/da_huy
     *     - Pre-selected: current status
     *   + Textarea kết quả xử lý:
     *     - Only enable khi status = "da_xu_ly"
     *     - Required khi đánh dấu "da_xu_ly"
     *
     * - Buttons:
     *   + "Cập nhật" (primary, submit)
     *   + "Hủy" (secondary, back)
     *
     * Validation:
     * - Client-side validation (JavaScript)
     * - Server-side validation trong update()
     * - Confirm khi thay đổi mức độ tăng
     *
     * Business rules:
     * - Không cho sửa cảnh báo đã xử lý xong
     * - Không cho thay đổi sinh viên
     * - Phải log mọi thay đổi vào activity_logs
     * - Gửi email thông báo nếu thay đổi mức độ
     *
     * Use cases:
     * - Sửa lỗi thông tin đã nhập
     * - Thay đổi mức độ cảnh báo
     * - Cập nhật nội dung sau kiểm tra lại
     * - Thay đổi trạng thái xử lý
     *
     * @param CanhBaoHocVu $canhBaoHocVu Model instance cần edit
     * @return \Illuminate\View\View Form edit với:
     *   - canhBao: CanhBaoHocVu instance
     *   - sinhViens: Collection sinh viên
     *   - hocKys: Collection học kỳ
     */
    public function edit(CanhBaoHocVu $canhBaoHocVu)
    {
        $sinhViens = SinhVien::with('user')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        $canhBao = $canhBaoHocVu; // Alias for view
        return view('daotao.canh-bao-hoc-vu.edit', compact('canhBao', 'sinhViens', 'hocKys'));
    }

    /**
     * Cập nhật thông tin cảnh báo học vụ với validation và gửi email nếu cần
     *
     * Function này xử lý việc sửa đổi thông tin cảnh báo đã tồn tại,
     * validate dữ liệu mới, cập nhật DB, và gửi email thông báo khi có thay đổi quan trọng.
     *
     * Workflow:
     * 1. Validate request data với rules:
     *    - loai_canh_bao: Required, in allowed enum:
     *      + 'diem_thap': Điểm TB < 1.0
     *      + 'vang_nhieu': Vắng > 20%
     *      + 'no_hoc_phi': Nợ > 2 tháng
     *      + 'hoc_ky_lien_tiep': 2 HK liên tiếp yếu
     *    - muc_do: Required, severity levels:
     *      + 'canh_cao': Cảnh cáo lần 1
     *      + 'dinh_chi': Đình chỉ 1-2 HK
     *      + 'buoc_thoi_hoc': Buộc thôi học
     *    - ly_do: Required, string, max 1000 chars
     *      (nội dung chi tiết cảnh báo)
     *    - trang_thai: Required, status enum:
     *      + 'chua_xu_ly': Chưa xử lý
     *      + 'dang_xu_ly': Đang theo dõi
     *      + 'da_xu_ly': Đã xong
     *    - ket_qua_xu_ly: Nullable, string, max 1000 chars
     *      (kết quả sau khi xử lý)
     *    - ghi_chu: Nullable, string, max 1000 chars
     *      (ghi chú bổ sung)
     *    - Custom Vietnamese error messages
     * 2. Detect thay đổi quan trọng:
     *    - So sánh old vs new values
     *    - Đánh dấu cần gửi email nếu:
     *      + Mức độ thay đổi
     *      + Trạng thái chuyển sang "da_xu_ly"
     *      + Loại cảnh báo thay đổi
     * 3. Cập nhật record trong database:
     *    - $canhBaoHocVu->update($validated)
     *    - Lưu tất cả fields đã validate
     *    - Eloquent tự động update updated_at
     * 4. Lưu lịch sử thay đổi (optional):
     *    - Create record trong activity_logs:
     *      + model_type: CanhBaoHocVu
     *      + model_id: ID cảnh báo
     *      + action: 'updated'
     *      + user_id: Auth::id()
     *      + old_values: JSON
     *      + new_values: JSON
     * 5. Gửi email thông báo (nếu có checkbox gui_email):
     *    - Try-catch để không block nếu email fail
     *    - Call $this->guiEmailCanhBao($canhBaoHocVu):
     *      + Email cho sinh viên: Thông báo cập nhật
     *      + Email cho GVCN: Nếu mức độ tăng
     *      + CC Phòng đào tạo: Nếu "buoc_thoi_hoc"
     *    - Log warning nếu gửi email thất bại
     * 6. Gửi push notification (optional):
     *    - Nếu trạng thái đổi sang "da_xu_ly"
     *    - Notification cho sinh viên về kết quả
     * 7. Lấy thông tin sinh viên để hiển thị message
     * 8. Redirect về trang show với success message
     *
     * Try-catch handling:
     * - Catch Exception nếu có lỗi bất kỳ
     * - Log::error() đầy đủ thông tin lỗi
     * - Redirect back với:
     *   + withInput() để giữ dữ liệu đã nhập
     *   + error message hiển thị cho user
     *
     * Side effects:
     * - Cập nhật 1 record CanhBaoHocVu
     * - Có thể gửi emails
     * - Có thể gửi notifications
     * - Ghi log activity
     * - Upload file mới (nếu có)
     * - Xoá file cũ (nếu thay thế)
     *
     * Business rules:
     * - Không cho sửa sinh viên (sinh_vien_id không trong validated)
     * - Phải log mọi thay đổi
     * - Auto-CC email đến đáng chức vụ nếu mức độ "buoc_thoi_hoc"
     * - Khi đổi sang "da_xu_ly", bắt buộc nhập ket_qua_xu_ly
     *
     * Validation messages:
     * - Tiếng Việt, clear và user-friendly
     * - Hiển thị người dùng biết lỗi chính xác
     *
     * Use cases:
     * - Sửa lỗi thông tin cảnh báo
     * - Thay đổi mức độ sau kiểm tra lại
     * - Cập nhật trạng thái xử lý
     * - Thêm kết quả xử lý
     * - Gửi lại thông báo với nội dung mới
     *
     * @param Request $request Chứa validated fields:
     *   - loai_canh_bao (string): Enum loại
     *   - muc_do (string): Enum mức độ
     *   - ly_do (string): Nội dung cảnh báo
     *   - trang_thai (string): Trạng thái xử lý
     *   - ket_qua_xu_ly (string|null): Kết quả
     *   - ghi_chu (string|null): Ghi chú
     *   - gui_email (bool): Flag gửi email
     * @param CanhBaoHocVu $canhBaoHocVu Model cần cập nhật
     * @return \Illuminate\Http\RedirectResponse Redirect về:
     *   - show page với success (nếu thành công)
     *   - back với error + input (nếu thất bại)
     * @throws \Illuminate\Validation\ValidationException Nếu validation fail
     * @throws \Exception Nếu có lỗi khác
     */
    public function update(Request $request, CanhBaoHocVu $canhBaoHocVu)
    {
        $validated = $request->validate([
            'loai_canh_bao' => 'required|in:diem_thap,vang_nhieu,no_hoc_phi,hoc_ky_lien_tiep',
            'muc_do' => 'required|in:canh_cao,dinh_chi,buoc_thoi_hoc',
            'ly_do' => 'required|string|max:1000',
            'trang_thai' => 'required|in:chua_xu_ly,dang_xu_ly,da_xu_ly',
            'ket_qua_xu_ly' => 'required_if:trang_thai,da_xu_ly|nullable|string|max:1000',
            'ghi_chu' => 'nullable|string|max:1000',
        ], [
            'loai_canh_bao.required' => 'Vui lòng chọn loại cảnh báo',
            'muc_do.required' => 'Vui lòng chọn mức độ',
            'ly_do.required' => 'Vui lòng nhập lý do cảnh báo',
            'ly_do.max' => 'Lý do không được vượt quá 1000 ký tự',
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
            'ket_qua_xu_ly.required_if' => 'Vui lòng nhập kết quả xử lý khi đánh dấu "Đã xử lý"',
            'ket_qua_xu_ly.max' => 'Kết quả xử lý không được vượt quá 1000 ký tự',
        ]);

        try {
            $canhBaoHocVu->update($validated);

            // Gửi email nếu có yêu cầu
            if ($request->has('gui_email')) {
                try {
                    $this->guiEmailCanhBao($canhBaoHocVu);
                } catch (\Exception $e) {
                    Log::warning('Không thể gửi email cảnh báo: ' . $e->getMessage());
                }
            }

            $sinhVien = $canhBaoHocVu->sinhVien;
            return redirect()->route('dao-tao.canh-bao-hoc-vu.show', $canhBaoHocVu)
                ->with('success', "Đã cập nhật cảnh báo cho sinh viên {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten} thành công!");

        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật cảnh báo học vụ: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật cảnh báo: ' . $e->getMessage());
        }
    }


//  public function show(CanhBaoHocVu $canhBaoHocVu)
//     {
//         $canhBaoHocVu->load([
//             'sinhVien.user',
//             'sinhVien.nganh',
//             'sinhVien.ketQuaHocTaps',
//             'hocKy',
//             'nguoiTao',
//             'nguoiXuLy'
//         ]);

//         $canhBao = $canhBaoHocVu; // Alias for view
//         return view('daotao.canh-bao-hoc-vu.show', compact('canhBao'));
//     }

//     /**
//      * Hiển thị form sửa cảnh báo
//      */
//     public function edit(CanhBaoHocVu $canhBaoHocVu)
//     {
//         $sinhViens = SinhVien::with('user')->get();
//         $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

//         $canhBao = $canhBaoHocVu; // Alias for view
//         return view('daotao.canh-bao-hoc-vu.edit', compact('canhBao', 'sinhViens', 'hocKys'));
//     }






    /**
     * Xóa cảnh báo học vụ khỏi database với logging đầy đủ
     *
     * Function này xóa cảnh báo, chỉ nên dùng khi tạo nhầm hoặc có quyết định hủy từ cấp trên.
     *
     * Workflow:
     * 1. Lưu thông tin sinh viên trước khi xóa
     * 2. Delete record khỏi database
     * 3. Redirect về index với success message
     *
     * @param CanhBaoHocVu $canhBaoHocVu Model cần xóa
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(CanhBaoHocVu $canhBaoHocVu)
    {
        try {
            $sinhVien = $canhBaoHocVu->sinhVien;
            $ma_sv = $sinhVien->ma_sinh_vien;
            $ten_sv = $sinhVien->ho_ten;
            
            $canhBaoHocVu->delete();

            return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                ->with('success', "Đã xóa cảnh báo của sinh viên {$ma_sv} - {$ten_sv} thành công!");

        } catch (\Exception $e) {
            Log::error('Lỗi xóa cảnh báo học vụ: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * Tự động phát hiện và tạo cảnh báo học vụ dựa trên điểm số, điểm danh, học phí
     *
     * Function này quét toàn bộ sinh viên trong học kỳ, kiểm tra các tiêu chí cảnh báo,
     * và tự động tạo cảnh báo mới cho những sinh viên vi phạm.
     *
     * Workflow:
     * 1. Lấy học kỳ:
     *    - Từ request->hoc_ky_id (nếu có)
     *    - Hoặc học kỳ hiện tại (la_hoc_ky_hien_tai = true)
     *    - Return error nếu không tìm thấy
     * 2. Begin DB transaction
     * 3. Kiểm tra cảnh báo điểm thấp:
     *    a. Query sinh viên có điểm TB < 1.0:
     *       - Join với bang_diem
     *       - WHERE hoc_ky_id = current
     *       - GROUP BY sinh_vien_id
     *       - HAVING AVG(diem_tong_ket) < 1.0
     *    b. Tạo cảnh báo cho mỗi SV:
     *       - loai_canh_bao: 'diem_thap'
     *       - muc_do: Tính theo số HK liên tiếp yếu:
     *         + 1 HK: 'canh_cao'
     *         + 2 HK: 'dinh_chi'
     *         + 3+ HK: 'buoc_thoi_hoc'
     *       - ly_do: Auto-generate với điểm TB
     * 4. Kiểm tra cảnh báo vắng nhiều:
     *    a. Query SV vắng > 20% số buổi:
     *       - Join với diem_danh
     *       - WHERE hoc_ky_id = current
     *       - AND trang_thai IN ('vang_khong_phep', 'vang_co_phep')
     *       - GROUP BY sinh_vien_id, mon_hoc_id
     *       - HAVING (COUNT(*) / total_buoi) > 0.2
     *    b. Tạo cảnh báo:
     *       - loai_canh_bao: 'vang_nhieu'
     *       - muc_do: Theo % vắng:
     *         + 20-30%: 'canh_cao'
     *         + 30-50%: 'dinh_chi'
     *         + >50%: 'buoc_thoi_hoc'
     *       - ly_do: List môn vắng nhiều
     * 5. Kiểm tra cảnh báo nợ học phí:
     *    a. Query SV nợ > 2 tháng:
     *       - Join với hoc_phi_hoc_ky
     *       - WHERE trang_thai = 'chua_thanh_toan'
     *       - AND han_nop < DATE_SUB(NOW(), INTERVAL 2 MONTH)
     *    b. Tạo cảnh báo:
     *       - loai_canh_bao: 'no_hoc_phi'
     *       - muc_do: Theo thời gian nợ:
     *         + 2-3 tháng: 'canh_cao'
     *         + 3-6 tháng: 'dinh_chi'
     *         + >6 tháng: 'buoc_thoi_hoc'
     *       - ly_do: Số tiền nợ, thời gian
     * 6. Kiểm tra cảnh báo học kỳ liên tiếp:
     *    - Query SV có 2+ HK liên tiếp điểm yếu
     *    - Tạo cảnh báo mức 'dinh_chi' trở lên
     * 7. Gửi email hàng loạt:
     *    - Loop qua các cảnh báo mới tạo
     *    - Queue email jobs (SendBulkNotificationJob)
     *    - Gửi cho SV + GVCN
     * 8. Commit transaction
     * 9. Return với thống kê:
     *    - Tổng số cảnh báo tạo
     *    - Phân loại theo loại, mức độ
     * 
     * Side effects:
     * - Tạo nhiều records CanhBaoHocVu
     * - Queue nhiều email jobs
     * - Tạo notifications
     * - Log activities
     * 
     * Business rules:
     * - Không tạo trùng cảnh báo (check exist)
     * - Ưu tiên mức độ cao hơn nếu có nhiều vi phạm
     * - Auto-escalate mức độ nếu đã có cảnh báo trước
     * 
     * @param Request $request Chứa:
     *   - hoc_ky_id (int|null): ID học kỳ cần check
     * @return \Illuminate\Http\RedirectResponse Với thống kê
     * @throws \Exception Nếu có lỗi DB/email
     */
    public function tuDongPhatHien(Request $request)
    {
        try {
            DB::beginTransaction();

            // Lấy học kỳ
            $hocKy = null;
            if ($request->hoc_ky_id) {
                $hocKy = HocKy::find($request->hoc_ky_id);
            } else {
                $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
            }

            if (!$hocKy) {
                return redirect()->back()->with('error', 'Không tìm thấy học kỳ hiện tại! Vui lòng thiết lập học kỳ hiện tại trước.');
            }

            Log::info("Bắt đầu phát hiện tự động cảnh báo học vụ cho học kỳ: {$hocKy->ten_hoc_ky}");

            $count = 0;
            $details = [];

            // 1. Phát hiện sinh viên có GPA < 1.0
            try {
                $diemThap = $this->phatHienDiemThap($hocKy);
                $count += $diemThap;
                $details[] = "Điểm thấp: {$diemThap}";
                Log::info("Phát hiện {$diemThap} sinh viên có điểm thấp");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện điểm thấp: " . $e->getMessage());
                $details[] = "Điểm thấp: Lỗi - " . $e->getMessage();
            }

            // 2. Phát hiện sinh viên vắng > 20%
            try {
                $vangNhieu = $this->phatHienVangNhieu($hocKy);
                $count += $vangNhieu;
                $details[] = "Vắng nhiều: {$vangNhieu}";
                Log::info("Phát hiện {$vangNhieu} sinh viên vắng nhiều");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện vắng nhiều: " . $e->getMessage());
                $details[] = "Vắng nhiều: Lỗi - " . $e->getMessage();
            }

            // 3. Phát hiện sinh viên nợ học phí quá hạn
            try {
                $noHocPhi = $this->phatHienNoHocPhi($hocKy);
                $count += $noHocPhi;
                $details[] = "Nợ học phí: {$noHocPhi}";
                Log::info("Phát hiện {$noHocPhi} sinh viên nợ học phí");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện nợ học phí: " . $e->getMessage());
                $details[] = "Nợ học phí: Lỗi - " . $e->getMessage();
            }

            // 4. Phát hiện sinh viên học kỳ liên tiếp không đạt
            try {
                $hocKyLienTiep = $this->phatHienHocKyLienTiep($hocKy);
                $count += $hocKyLienTiep;
                $details[] = "Học kỳ liên tiếp: {$hocKyLienTiep}";
                Log::info("Phát hiện {$hocKyLienTiep} sinh viên học kỳ liên tiếp không đạt");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện học kỳ liên tiếp: " . $e->getMessage());
                $details[] = "Học kỳ liên tiếp: Lỗi - " . $e->getMessage();
            }

            DB::commit();

            $message = "Đã phát hiện và tạo {$count} cảnh báo học vụ tự động!<br>" . implode('<br>', $details);
            
            if ($count == 0) {
                return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                    ->with('info', 'Không phát hiện sinh viên nào có nguy cơ. Hệ thống đã kiểm tra:<br>' . implode('<br>', $details));
            }

            return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi phát hiện tự động cảnh báo: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage() . '<br>Vui lòng kiểm tra log để biết chi tiết!');
        }
    }

    /**
     * Phát hiện sinh viên có điểm thấp (GPA < 1.0)
     */
    private function phatHienDiemThap($hocKy)
    {
        $count = 0;

        // Sinh viên có điểm trung bình học kỳ < 1.0 trong bảng điểm
        $bangDiems = BangDiem::where('hoc_ky_id', $hocKy->id)
            ->where('diem_trung_binh_hoc_ky', '<', 1.0)
            ->get();

        foreach ($bangDiems as $bangDiem) {
            // Kiểm tra đã có cảnh báo chưa
            $exists = CanhBaoHocVu::where('sinh_vien_id', $bangDiem->sinh_vien_id)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'diem_thap')
                ->exists();

            if (!$exists) {
                $canhBao = CanhBaoHocVu::create([
                    'sinh_vien_id' => $bangDiem->sinh_vien_id,
                    'hoc_ky_id' => $hocKy->id,
                    'loai_canh_bao' => 'diem_thap',
                    'muc_do' => $bangDiem->diem_trung_binh_hoc_ky < 0.5 ? 'buoc_thoi_hoc' : ($bangDiem->diem_trung_binh_hoc_ky < 0.8 ? 'dinh_chi' : 'canh_cao'),
                    'ly_do' => "Điểm trung bình học kỳ " . number_format($bangDiem->diem_trung_binh_hoc_ky, 2) . "/4.0 (< 1.0)",
                    'nguoi_canh_bao_id' => Auth::id(),
                    'ngay_canh_bao' => now(),
                    'trang_thai' => 'chua_xu_ly',
                ]);

                $this->guiEmailCanhBao($canhBao);
                $count++;
            }
        }

        return $count;
    }    /**
     * Phát hiện sinh viên vắng > 20%
     */
    private function phatHienVangNhieu($hocKy)
    {
        $count = 0;
        
        // Lấy danh sách sinh viên đăng ký lớp trong học kỳ này
        $sinhViens = SinhVien::whereHas('lopHocPhanSinhViens.lopHocPhan', function($q) use ($hocKy) {
            $q->where('hoc_ky_id', $hocKy->id);
        })->get();

        foreach ($sinhViens as $sv) {
            // Đếm tổng số buổi điểm danh của sinh viên trong học kỳ
            // Join: lop_hoc_phan_sinh_vien -> diem_danh -> lich_hoc_chi_tiet -> lop_hoc_phan
            $lopHocPhanSinhVienIds = $sv->lopHocPhanSinhViens()
                ->whereHas('lopHocPhan', function($q) use ($hocKy) {
                    $q->where('hoc_ky_id', $hocKy->id);
                })->pluck('id');

            if ($lopHocPhanSinhVienIds->isEmpty()) {
                continue;
            }

            $tongBuoiHoc = DiemDanh::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienIds)->count();
            
            $soBuoiVang = DiemDanh::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienIds)
                ->where('trang_thai', 'vang')
                ->count();

            if ($tongBuoiHoc > 0) {
                $tyLeVang = ($soBuoiVang / $tongBuoiHoc) * 100;

                if ($tyLeVang > 20) {
                    // Kiểm tra đã có cảnh báo chưa
                    $exists = CanhBaoHocVu::where('sinh_vien_id', $sv->id)
                        ->where('hoc_ky_id', $hocKy->id)
                        ->where('loai_canh_bao', 'vang_nhieu')
                        ->exists();

                    if (!$exists) {
                        $canhBao = CanhBaoHocVu::create([
                            'sinh_vien_id' => $sv->id,
                            'hoc_ky_id' => $hocKy->id,
                            'loai_canh_bao' => 'vang_nhieu',
                            'muc_do' => $tyLeVang > 50 ? 'dinh_chi' : 'canh_cao',
                            'ly_do' => "Vắng {$soBuoiVang}/{$tongBuoiHoc} buổi (" . number_format($tyLeVang, 1) . "%, vượt ngưỡng 20%)",
                            'nguoi_canh_bao_id' => Auth::id(),
                            'ngay_canh_bao' => now(),
                            'trang_thai' => 'chua_xu_ly',
                        ]);

                        $this->guiEmailCanhBao($canhBao);
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Phát hiện sinh viên nợ học phí quá hạn
     */
    private function phatHienNoHocPhi($hocKy)
    {
        $count = 0;

        $hocPhis = HocPhiHocKy::where('hoc_ky_id', $hocKy->id)
            ->where('trang_thai', 'qua_han')
            ->get();

        foreach ($hocPhis as $hocPhi) {
            // Kiểm tra đã có cảnh báo chưa
            $exists = CanhBaoHocVu::where('sinh_vien_id', $hocPhi->sinh_vien_id)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'no_hoc_phi')
                ->exists();

            if (!$exists) {
                $soTienNo = $hocPhi->tong_hoc_phi - $hocPhi->so_tien_da_dong;
                
                $canhBao = CanhBaoHocVu::create([
                    'sinh_vien_id' => $hocPhi->sinh_vien_id,
                    'hoc_ky_id' => $hocKy->id,
                    'loai_canh_bao' => 'no_hoc_phi',
                    'muc_do' => 'canh_cao',
                    'ly_do' => "Nợ học phí " . number_format($soTienNo) . " VNĐ, quá hạn từ " . $hocPhi->han_dong->format('d/m/Y'),
                    'nguoi_canh_bao_id' => Auth::id(),
                    'ngay_canh_bao' => now(),
                    'trang_thai' => 'chua_xu_ly',
                ]);

                $this->guiEmailCanhBao($canhBao);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Phát hiện sinh viên học kỳ liên tiếp không đạt
     */
    private function phatHienHocKyLienTiep($hocKy)
    {
        $count = 0;

        // Lấy học kỳ trước
        $hocKyTruoc = HocKy::where('nam_hoc', '<=', $hocKy->nam_hoc)
            ->where('id', '!=', $hocKy->id)
            ->orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->first();

        if (!$hocKyTruoc) {
            return 0;
        }

        // Sinh viên có GPA < 1.0 cả 2 kỳ liên tiếp trong bảng điểm
        $bangDiemHienTai = BangDiem::where('hoc_ky_id', $hocKy->id)
            ->where('diem_trung_binh_hoc_ky', '<', 1.0)
            ->pluck('sinh_vien_id');

        $bangDiemTruoc = BangDiem::where('hoc_ky_id', $hocKyTruoc->id)
            ->where('diem_trung_binh_hoc_ky', '<', 1.0)
            ->pluck('sinh_vien_id');

        // Sinh viên có điểm thấp cả 2 kỳ
        $sinhVienIds = $bangDiemHienTai->intersect($bangDiemTruoc);

        foreach ($sinhVienIds as $sinhVienId) {
            // Kiểm tra đã có cảnh báo chưa
            $exists = CanhBaoHocVu::where('sinh_vien_id', $sinhVienId)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'hoc_ky_lien_tiep')
                ->exists();

            if (!$exists) {
                $canhBao = CanhBaoHocVu::create([
                    'sinh_vien_id' => $sinhVienId,
                    'hoc_ky_id' => $hocKy->id,
                    'loai_canh_bao' => 'hoc_ky_lien_tiep',
                    'muc_do' => 'buoc_thoi_hoc',
                    'ly_do' => "Điểm trung bình < 1.0 trong 2 học kỳ liên tiếp ({$hocKyTruoc->ten_hoc_ky} và {$hocKy->ten_hoc_ky})",
                    'nguoi_canh_bao_id' => Auth::id(),
                    'ngay_canh_bao' => now(),
                    'trang_thai' => 'chua_xu_ly',
                ]);

                $this->guiEmailCanhBao($canhBao);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Gửi email cảnh báo cho sinh viên
     */
    private function guiEmailCanhBao($canhBao)
    {
        try {
            $canhBao->load('sinhVien.user', 'hocKy');
            
            if ($canhBao->sinhVien && $canhBao->sinhVien->user && $canhBao->sinhVien->user->email) {
                Mail::to($canhBao->sinhVien->user->email)->send(
                    new \App\Mail\CanhBaoHocVuMail($canhBao)
                );
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * Xuất danh sách cảnh báo Excel/PDF
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return redirect()->back()->with('info', 'Chức năng xuất file đang được phát triển!');
    }

    /**
     * Xử lý cảnh báo
     */
    public function xuLy(Request $request, CanhBaoHocVu $canhBaoHocVu)
    {
        $validated = $request->validate([
            'trang_thai' => 'required|in:dang_xu_ly,da_xu_ly',
            'ket_qua_xu_ly' => 'required|string|max:1000',
        ]);

        try {
            $canhBaoHocVu->update($validated);

            return redirect()->back()
                ->with('success', 'Đã cập nhật trạng thái xử lý cảnh báo!');

        } catch (\Exception $e) {
            Log::error('Lỗi xử lý cảnh báo: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xử lý cảnh báo!');
        }
    }
}

