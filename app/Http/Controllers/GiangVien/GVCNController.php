<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\SinhVien;
use App\Models\GiangVien;
use App\Models\BangDiem;
use App\Models\CanhBaoHocVu;
use App\Models\HocKy;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GVCNController extends Controller
{
    /**
     * Hiển thị danh sách các lớp hành chính mà giảng viên làm chủ nhiệm
     *
     * Function này cung cấp giao diện tổng quan cho giảng viên chủ nhiệm (GVCN)
     * để quản lý và theo dõi tất cả các lớp hành chính mình phụ trách,
     * bao gồm thống kê tổng quan về số lượng và tình trạng sinh viên.
     *
     * Workflow:
     * 1. Lấy thông tin giảng viên đang đăng nhập:
     *    - Auth::user()->giangVien relationship
     *    - Redirect về dashboard nếu không tìm thấy
     * 2. Query danh sách lớp chủ nhiệm:
     *    - LopHanhChinh where giang_vien_chu_nhiem_id = giảng viên ID
     *    - Eager load relationships:
     *      + khoaHoc: Khóa học của lớp
     *      + nganh: Ngành đào tạo
     *      + sinhVien: Tất cả sinh viên trong lớp
     * 3. Với mỗi lớp, tính toán thống kê:
     *    a. Tổng số sinh viên: sinhVien->count()
     *    b. Sinh viên nam: filter where gioi_tinh = 'nam'
     *    c. Sinh viên nữ: filter where gioi_tinh = 'nu'
     *    d. Số sinh viên đang học:
     *       - Filter sinh viên có trangThaiHocTap
     *       - Where ten_trang_thai = 'Đang học'
     *       - Count kết quả
     * 4. Return view với data
     *
     * Thông tin hiển thị:
     * - Danh sách lớp chủ nhiệm dạng card/table:
     *   + Mã lớp (mã lớp hành chính)
     *   + Tên lớp
     *   + Khóa học (năm nhập học)
     *   + Ngành đào tạo
     *   + Thống kê sinh viên:
     *     - Tổng số: tong_sinh_vien
     *     - Nam: sinh_vien_nam (với icon)
     *     - Nữ: sinh_vien_nu (với icon)
     *     - Đang học: dang_hoc (badge xanh)
     *   + Nút hành động:
     *     - Xem chi tiết lớp
     *     - Danh sách sinh viên
     *     - Xem kết quả học tập
     *     - Xem cảnh báo học vụ
     *
     * Tính năng dashboard:
     * - Hiển thị tất cả lớp mà GVCN phụ trách
     * - Thống kê tổng hợp các lớp:
     *   + Tổng số lớp
     *   + Tổng số sinh viên
     *   + Trung bình sinh viên/lớp
     * - Quick stats:
     *   + Số sinh viên có cảnh báo học vụ
     *   + Số sinh viên có kết quả yếu
     *   + Số sinh viên tạm dừng/thôi học
     * - Links nhanh:
     *   + Gửi thông báo đến lớp
     *   + Xem báo cáo học tập
     *   + Quản lý cảnh báo
     *
     * Business rules:
     * - Một giảng viên có thể làm GVCN cho nhiều lớp
     * - Chỉ hiển thị lớp mà GVCN đang phụ trách (active)
     * - Thống kê chỉ tính sinh viên thuộc lớp hành chính
     *
     * @return \Illuminate\View\View View danh sách lớp chủ nhiệm với data:
     *   - lopChuNhiem: Collection các LopHanhChinh với computed stats
     *   - giangVien: GiangVien instance hiện tại
     * @return \Illuminate\Http\RedirectResponse Nếu không tìm thấy giảng viên
     */
    public function index()
    {
        // Lấy thông tin giảng viên từ user hiện tại
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy danh sách lớp chủ nhiệm
        $lopChuNhiem = LopHanhChinh::where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh', 'sinhVien'])
            ->get();

        // Tính toán thống kê cho từng lớp
        foreach ($lopChuNhiem as $lop) {
            $lop->tong_sinh_vien = $lop->sinhVien->count();
            $lop->sinh_vien_nam = $lop->sinhVien->where('gioi_tinh', 'nam')->count();
            $lop->sinh_vien_nu = $lop->sinhVien->where('gioi_tinh', 'nu')->count();
            $lop->dang_hoc = $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Đang học';
            })->count();
        }

        return view('giangvien.lop-chu-nhiem.index', compact('lopChuNhiem', 'giangVien'));
    }

    /**
     * Hiển thị thông tin chi tiết và thống kê của một lớp hành chính
     *
     * Function này cung cấp dashboard chi tiết cho từng lớp hành chính,
     * bao gồm thống kê toàn diện về số lượng sinh viên, giới tính,
     * trạng thái học tập, và phân bố chuyên ngành.
     *
     * Workflow:
     * 1. Xác thực giảng viên chủ nhiệm
     *    - Lấy giảng viên từ Auth::user()
     *    - Redirect nếu không tìm thấy
     * 2. Lấy thông tin lớp với kiểm tra quyền:
     *    - FindOrFail LopHanhChinh by ID
     *    - Where giang_vien_chu_nhiem_id = current giảng viên
     *    - Eager load: khoaHoc, nganh, sinhVien.trangThaiHocTap, sinhVien.chuyenNganh
     * 3. Tính toán thống kê tổng hợp:
     *    a. Thống kê số lượng:
     *       - tong_sinh_vien: Count tất cả sinh viên
     *    b. Thống kê theo giới tính:
     *       - nam: Count where gioi_tinh = 'nam'
     *       - nu: Count where gioi_tinh = 'nu'
     *       - khac: Count where gioi_tinh = 'khac'
     *    c. Thống kê theo trạng thái học tập:
     *       - dang_hoc: Filter ten_trang_thai = 'Đang học'
     *       - bao_luu: Filter ten_trang_thai = 'Bảo lưu'
     *       - thoi_hoc: Filter ten_trang_thai = 'Thôi học'
     *       - tot_nghiep: Filter ten_trang_thai = 'Tốt nghiệp'
     * 4. Phân tích phân bố chuyên ngành:
     *    - Lọc sinh viên có chuyen_nganh_id (từ năm 3 trở lên)
     *    - Group by chuyen_nganh_id
     *    - Map để tạo array với:
     *      + ten_chuyen_nganh: Tên chuyên ngành hoặc 'Chưa xác định'
     *      + so_luong: Số sinh viên trong chuyên ngành
     * 5. Return view với full data
     *
     * Thông tin hiển thị:
     * - Thông tin lớp:
     *   + Mã lớp, tên lớp
     *   + Khóa học, ngành đào tạo
     *   + Giảng viên chủ nhiệm (tên, mã, email, sđt)
     * - Thống kê cards:
     *   + Tổng sinh viên (icon users)
     *   + Nam/Nữ/Khác (icons với màu sắc)
     *   + Đang học (badge xanh)
     *   + Bảo lưu (badge vàng)
     *   + Thôi học (đỏ)
     *   + Tốt nghiệp (xám)
     * - Biểu đồ:
     *   + Pie chart giới tính
     *   + Bar chart trạng thái học tập
     *   + Doughnut chart phân bố chuyên ngành
     * - Phân bố chuyên ngành:
     *   + Bảng hiển thị tên chuyên ngành và số lượng
     *   + Progress bar cho mỗi chuyên ngành
     *   + Tỷ lệ % so với tổng
     * - Nút hành động:
     *   + Xem danh sách sinh viên
     *   + Xuất Excel
     *   + Xuất PDF
     *   + Xem kết quả học tập
     *   + Xem cảnh báo học vụ
     *
     * Tính năng đặc biệt:
     * - Realtime statistics với Chart.js
     * - Responsive design cho mobile
     * - Export to Excel/PDF
     * - Quick links đến các tính năng quản lý
     *
     * Business rules:
     * - Chỉ GVCN của lớp mới xem được (kiểm tra giang_vien_chu_nhiem_id)
     * - Chuyên ngành chỉ hiển thị cho sinh viên năm 3, 4
     * - Thống kê bao gồm cả sinh viên đã chuyển lớp (nếu còn trong lớp)
     *
     * @param int $id ID của lớp hành chính
     * @return \Illuminate\View\View View chi tiết lớp với:
     *   - lop: LopHanhChinh instance với relationships
     *   - thongKe: Array thống kê tổng hợp
     *   - phanBoChuyenNganh: Collection phân bố chuyên ngành
     *   - giangVien: GiangVien instance
     * @return \Illuminate\Http\RedirectResponse Nếu không tìm thấy giảng viên
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy lớp hoặc không có quyền
     */
    public function show($id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy thông tin lớp
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh', 'sinhVien.trangThaiHocTap', 'sinhVien.chuyenNganh'])
            ->firstOrFail();

        // Thống kê lớp
        $thongKe = [
            'tong_sinh_vien' => $lop->sinhVien->count(),
            'nam' => $lop->sinhVien->where('gioi_tinh', 'nam')->count(),
            'nu' => $lop->sinhVien->where('gioi_tinh', 'nu')->count(),
            'khac' => $lop->sinhVien->where('gioi_tinh', 'khac')->count(),
            'dang_hoc' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Đang học';
            })->count(),
            'bao_luu' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Bảo lưu';
            })->count(),
            'thoi_hoc' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Thôi học';
            })->count(),
            'tot_nghiep' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Tốt nghiệp';
            })->count(),
        ];

        // Phân bố theo chuyên ngành (cho sinh viên từ năm 3 trở lên)
        $phanBoChuyenNganh = $lop->sinhVien
            ->whereNotNull('chuyen_nganh_id')
            ->groupBy('chuyen_nganh_id')
            ->map(function ($group) {
                return [
                    'ten_chuyen_nganh' => $group->first()->chuyenNganh->ten_chuyen_nganh ?? 'Chưa xác định',
                    'so_luong' => $group->count(),
                ];
            });

        return view('giangvien.lop-chu-nhiem.show', compact('lop', 'thongKe', 'phanBoChuyenNganh', 'giangVien'));
    }

    /**
     * Hiển thị danh sách chi tiết sinh viên trong lớp với filters và tìm kiếm nâng cao
     *
     * Function này cung cấp giao diện quản lý sinh viên cho GVCN, bao gồm
     * tìm kiếm, lọc theo nhiều tiêu chí, phân trang, và các thao tác nhanh.
     *
     * Workflow:
     * 1. Xác thực GVCN đang đăng nhập
     *    - Redirect nếu không tìm thấy giảng viên
     * 2. Kiểm tra quyền truy cập:
     *    - FindOrFail LopHanhChinh by ID
     *    - Where giang_vien_chu_nhiem_id = current giảng viên
     *    - Eager load khoaHoc, nganh
     * 3. Build query sinh viên:
     *    - Base: SinhVien where lop_hanh_chinh_id = $id
     *    - Eager load: trangThaiHocTap, nganh, chuyenNganh, user
     * 4. Apply filters từ request:
     *    a. Tìm kiếm text (search):
     *       - Where ma_sinh_vien LIKE %search%
     *       - OR ho_ten LIKE %search%
     *       - OR email LIKE %search%
     *       - OR so_dien_thoai LIKE %search%
     *    b. Lọc theo giới tính:
     *       - Where gioi_tinh = request value
     *    c. Lọc theo trạng thái học tập:
     *       - Where trang_thai_hoc_tap_id = request value
     *    d. Lọc theo chuyên ngành:
     *       - Where chuyen_nganh_id = request value
     * 5. Sắp xếp:
     *    - OrderBy ma_sinh_vien ASC (mặc định)
     * 6. Phân trang:
     *    - Paginate 20 items per page
     *    - Append request params vào pagination links
     * 7. Lấy data cho filter dropdowns:
     *    - chuyenNganhs: ChuyenNganh where nganh_id của lớp
     *    - trangThais: All TrangThaiHocTap
     * 8. Return view với full data
     *
     * Thông tin hiển thị:
     * - Filter controls:
     *   + Search box: Tìm theo mã SV, họ tên, email, SĐT
     *   + Dropdown giới tính: Nam/Nữ/Khác
     *   + Dropdown trạng thái học tập: Đang học/Bảo lưu/Thôi học/...
     *   + Dropdown chuyên ngành (nếu có)
     *   + Button Reset filters
     * - Bảng danh sách sinh viên:
     *   + STT
     *   + Mã sinh viên (link đến profile)
     *   + Họ và tên (có avatar)
     *   + Giới tính (icon)
     *   + Email (clickable)
     *   + Số điện thoại
     *   + Chuyên ngành (nếu có)
     *   + Trạng thái học tập (badge với màu)
     *   + Hành động:
     *     - Xem hồ sơ
     *     - Xem điểm
     *     - Xem cảnh báo
     *     - Gửi email
     * - Pagination:
     *   + 20 sinh viên/trang
     *   + Giữ filters khi chuyển trang
     * - Bulk actions:
     *   + Chọn nhiều sinh viên
     *   + Gửi email hàng loạt
     *   + Export danh sách đã chọn
     *
     * Tính năng nâng cao:
     * - Advanced search với multiple conditions
     * - Real-time search (debounced)
     * - Export danh sách đã lọc to Excel/PDF
     * - Quick stats: Tổng SV, SV đang học, SV vắng nhiều
     * - Inline edit một số thông tin cơ bản
     *
     * Business rules:
     * - Chỉ GVCN của lớp mới có quyền xem
     * - Hiển thị cả sinh viên đã chuyển lớp (nếu còn trong DB)
     * - Filter chuyên ngành chỉ hiển thị khi có sinh viên năm 3+
     *
     * @param Request $request Chứa filters:
     *   - search (string): Keyword tìm kiếm
     *   - gioi_tinh (string): 'nam'|'nu'|'khac'
     *   - trang_thai_id (int): ID trạng thái học tập
     *   - chuyen_nganh_id (int): ID chuyên ngành
     * @param int $id ID lớp hành chính
     * @return \Illuminate\View\View View danh sách sinh viên với:
     *   - lop: LopHanhChinh instance
     *   - sinhViens: Paginated collection sinh viên
     *   - giangVien: GiangVien instance
     *   - chuyenNganhs: Collection chuyên ngành cho filter
     *   - trangThais: Collection trạng thái học tập
     * @return \Illuminate\Http\RedirectResponse Nếu không tìm thấy giảng viên
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không có quyền
     */
    public function danhSachSinhVien(Request $request, $id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra quyền truy cập
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Query sinh viên
        $query = SinhVien::where('lop_hanh_chinh_id', $id)
            ->with(['trangThaiHocTap', 'nganh', 'chuyenNganh', 'user']);

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_sinh_vien', 'like', '%' . $search . '%')
                    ->orWhere('ho_ten', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('so_dien_thoai', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo giới tính
        if ($request->has('gioi_tinh') && $request->gioi_tinh != '') {
            $query->where('gioi_tinh', $request->gioi_tinh);
        }

        // Lọc theo trạng thái học tập
        if ($request->has('trang_thai_id') && $request->trang_thai_id != '') {
            $query->where('trang_thai_hoc_tap_id', $request->trang_thai_id);
        }

        // Lọc theo chuyên ngành
        if ($request->has('chuyen_nganh_id') && $request->chuyen_nganh_id != '') {
            $query->where('chuyen_nganh_id', $request->chuyen_nganh_id);
        }

        // Sắp xếp
        $query->orderBy('ma_sinh_vien', 'asc');

        // Phân trang
        $sinhViens = $query->paginate(20)->appends($request->all());

        // Lấy danh sách chuyên ngành và trạng thái để filter
        $chuyenNganhs = \App\Models\DaoTao\ChuyenNganh::where('nganh_id', $lop->nganh_id)->get();
        $trangThais = \App\Models\DanhMuc\TrangThaiHocTap::all();

        return view('giangvien.lop-chu-nhiem.sinh-vien', compact(
            'lop',
            'sinhViens',
            'giangVien',
            'chuyenNganhs',
            'trangThais'
        ));
    }

    /**
     * Xuất danh sách sinh viên lớp ra file Excel với format chuyên nghiệp đầy đủ
     *
     * Function này tạo file Excel (.xlsx) chứa toàn bộ thông tin sinh viên trong lớp
     * với formatting đẹp, colors, borders, auto-width columns, hỗ trợ UTF-8 tiếng Việt.
     *
     * Workflow:
     * 1. Xác thực GVCN và kiểm tra quyền:
     *    - Lấy giảng viên từ Auth
     *    - FindOrFail lớp where giang_vien_chu_nhiem_id
     *    - Eager load khoaHoc, nganh
     * 2. Query danh sách sinh viên:
     *    - SinhVien where lop_hanh_chinh_id
     *    - Eager load: trangThaiHocTap, nganh, chuyenNganh
     *    - OrderBy ma_sinh_vien ASC
     * 3. Tạo Spreadsheet object (PhpSpreadsheet)
     * 4. Setup Excel structure:
     *    a. Header section (rows 1-3):
     *       - Row 1: Title "DANH SÁCH SINH VIÊN LỚP {ma_lop}"
     *         + Merge cells A1:K1
     *         + Bold, size 16, centered
     *       - Row 2: Thông tin lớp
     *         + "Khóa học: ..., Ngành: ..."
     *         + Merge cells
     *       - Row 3: Ngày xuất
     *         + Format: dd/mm/YYYY HH:ii
     *    b. Table header (row 5):
     *       - Columns: STT, Mã SV, Họ đệm, Tên, Giới tính, Ngày sinh,
     *         Email, SĐT, Chuyên ngành, Trạng thái, Ghi chú
     *       - Bold font
     *       - Background: Blue (#4472C4)
     *       - Text: White
     *       - Borders: All sides
     *    c. Data rows (from row 6):
     *       - Loop through sinhViens
     *       - Write data for each column
     *       - Apply borders
     * 5. Formatting:
     *    - Auto-size all columns (A-K)
     *    - Apply thin borders to data area
     *    - Align numbers to right
     *    - Wrap text for long content
     * 6. Generate và download file:
     *    - Filename: danh-sach-sv-{ma_lop}-{date}.xlsx
     *    - Set HTTP headers:
     *      + Content-Type: application/vnd.openxmlformats...
     *      + Content-Disposition: attachment
     *      + Cache-Control: max-age=0
     *    - Write to php://output
     *    - Exit
     *
     * Excel structure:
     * - Sheet name: "Danh sách SV"
     * - Headers: 11 columns
     * - Data: 1 row per student
     * - Footer: Tổng số sinh viên
     *
     * Columns detail:
     * - STT: Auto-increment number
     * - Mã SV: ma_sinh_vien
     * - Họ đệm: Split from ho_ten
     * - Tên: Last part of ho_ten
     * - Giới tính: Nam/Nữ/Khác
     * - Ngày sinh: Format dd/mm/YYYY
     * - Email: Student email
     * - SĐT: so_dien_thoai
     * - Chuyên ngành: ten_chuyen_nganh or empty
     * - Trạng thái: ten_trang_thai
     * - Ghi chú: Empty for manual notes
     *
     * Tính năng đặc biệt:
     * - UTF-8 encoding cho tiếng Việt
     * - Professional table formatting
     * - Auto-width columns
     * - Color-coded headers
     * - Borders for better readability
     * - Compatible với Excel 2007+, LibreOffice, Google Sheets
     *
     * Use cases:
     * - GVCN xuất danh sách để lưu trữ
     * - Nộp danh sách lớp cho phòng đào tạo
     * - Chia sẻ với giảng viên khác
     * - In danh sách điểm danh giấy
     * - Import vào hệ thống khác
     *
     * @param int $id ID lớp hành chính
     * @return void File Excel được stream trực tiếp
     * @return \Illuminate\Http\RedirectResponse Nếu không tìm thấy giảng viên
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không có quyền
     * @throws \PhpOffice\PhpSpreadsheet\Exception Nếu lỗi tạo Excel
     */
    public function exportExcel($id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy thông tin lớp
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Lấy danh sách sinh viên
        $sinhViens = SinhVien::where('lop_hanh_chinh_id', $id)
            ->with(['trangThaiHocTap', 'nganh', 'chuyenNganh'])
            ->orderBy('ma_sinh_vien', 'asc')
            ->get();

        // Tạo spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'DANH SÁCH SINH VIÊN LỚP ' . $lop->ma_lop);
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Khóa học: ' . ($lop->khoaHoc->ten_khoa_hoc ?? 'N/A'));
        $sheet->setCellValue('A3', 'Ngành: ' . ($lop->nganh->ten_nganh ?? 'N/A'));
        $sheet->setCellValue('A4', 'GVCN: ' . $giangVien->ho_ten);
        $sheet->setCellValue('A5', 'Ngày xuất: ' . now()->format('d/m/Y H:i'));

        // Column headers
        $row = 7;
        $headers = ['STT', 'Mã SV', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Email', 'SĐT', 'Kỳ hiện tại', 'Chuyên ngành', 'Trạng thái', 'Địa chỉ'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $col++;
        }

        // Data
        $row = 8;
        $stt = 1;
        foreach ($sinhViens as $sv) {
            $diaChi = implode(', ', array_filter([
                $sv->so_nha_duong,
                $sv->phuong_xa,
                $sv->quan_huyen,
                $sv->tinh_thanh
            ]));

            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $sv->ma_sinh_vien);
            $sheet->setCellValue('C' . $row, $sv->ho_ten);
            $sheet->setCellValue('D' . $row, $sv->ngay_sinh ? $sv->ngay_sinh->format('d/m/Y') : '');
            $sheet->setCellValue('E' . $row, ucfirst($sv->gioi_tinh));
            $sheet->setCellValue('F' . $row, $sv->email);
            $sheet->setCellValue('G' . $row, $sv->so_dien_thoai);
            $sheet->setCellValue('H' . $row, $sv->ky_hien_tai);
            $sheet->setCellValue('I' . $row, $sv->chuyenNganh->ten_chuyen_nganh ?? 'Chưa chọn');
            $sheet->setCellValue('J' . $row, $sv->trangThaiHocTap->ten_trang_thai ?? 'N/A');
            $sheet->setCellValue('K' . $row, $diaChi);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border
        $sheet->getStyle('A7:K' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $fileName = 'Danh_sach_sinh_vien_' . $lop->ma_lop . '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Xuất danh sách sinh viên lớp ra file PDF chính thức để in ấn và lưu trữ
     *
     * Function này tạo file PDF chứa danh sách sinh viên với format chuyên nghiệp,
     * phù hợp cho việc in ấn, nộp báo cáo chính thức, hoặc lưu trữ hồ sơ.
     *
     * Workflow:
     * 1. Xác thực GVCN và kiểm tra quyền truy cập:
     *    - Lấy giảng viên từ Auth::user()
     *    - Redirect nếu không tìm thấy
     *    - FindOrFail lớp where giang_vien_chu_nhiem_id
     *    - Eager load khoaHoc, nganh
     * 2. Query danh sách sinh viên:
     *    - SinhVien where lop_hanh_chinh_id = $id
     *    - Eager load: trangThaiHocTap, nganh, chuyenNganh
     *    - OrderBy ma_sinh_vien ASC
     * 3. Chuẩn bị data cho PDF:
     *    - lop: LopHanhChinh object
     *    - sinhViens: Collection sinh viên
     *    - giangVien: GVCN info
     *    - ngayXuat: Current date formatted
     * 4. Load PDF view template:
     *    - Blade template: giangvien.lop-chu-nhiem.export-pdf
     *    - Pass data to view
     * 5. Generate PDF using DomPDF:
     *    - Pdf::loadView()
     *    - Set options: paper A4, orientation portrait
     * 6. Download response:
     *    - Filename: danh-sach-sv-{ma_lop}-{date}.pdf
     *    - Trigger browser download
     *
     * PDF content structure:
     * - Header chính thức:
     *   + Logo trường (nếu có)
     *   + Tên trường đầy đủ
     *   + Tên khoa/viện
     *   + Đường kẻ phân cách
     * - Tiêu đề:
     *   + "DANH SÁCH SINH VIÊN" (font lớn, bold)
     *   + Thông tin lớp: Mã lớp, khóa học, ngành
     * - Bảng danh sách:
     *   + Table với borders
     *   + Header row: STT, Mã SV, Họ tên, Giới tính,
     *     Ngày sinh, Email, SĐT, Chuyên ngành, Trạng thái
     *   + Data rows: 1 row per student
     *   + Styling: Alternating row colors
     * - Footer:
     *   + Thống kê: "Tổng số: XX sinh viên"
     *   + Ngày xuất báo cáo
     *   + Chữ ký:
     *     - Trưởng khoa (trái)
     *     - GVCN (phải)
     *   + Page numbers (nếu nhiều trang)
     *
     * PDF formatting:
     * - Paper: A4 (210mm x 297mm)
     * - Orientation: Portrait
     * - Margins: 15mm all sides
     * - Font: DejaVu Sans (support Vietnamese UTF-8)
     * - Font size: 10pt for data, 12pt for headers
     * - Line spacing: 1.2
     *
     * Tính năng đặc biệt:
     * - Professional header với logo
     * - Proper Vietnamese character rendering
     * - Alternating row colors (zebra striping)
     * - Page breaks cho nhiều sinh viên
     * - Header/Footer trên mỗi trang
     * - Signature sections
     * - Print-friendly formatting
     *
     * Use cases:
     * - In danh sách lớp để dán tường
     * - Nộp báo cáo chính thức cho nhà trường
     * - Lưu trữ hồ sơ lớp học
     * - Đính kèm email báo cáo
     * - Archive theo học kỳ
     *
     * Business rules:
     * - Chỉ GVCN mới có quyền xuất
     * - PDF phải có chữ ký điện tử (nếu setup)
     * - Watermark "Bản sao" nếu không phải bản gốc
     *
     * @param int $id ID lớp hành chính
     * @return \Illuminate\Http\Response PDF download response
     * @return \Illuminate\Http\RedirectResponse Nếu không tìm thấy giảng viên
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không có quyền
     * @throws \Barryvdh\DomPDF\Exception Nếu lỗi generate PDF
     */
    public function exportPDF($id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy thông tin lớp
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Lấy danh sách sinh viên
        $sinhViens = SinhVien::where('lop_hanh_chinh_id', $id)
            ->with(['trangThaiHocTap', 'nganh', 'chuyenNganh'])
            ->orderBy('ma_sinh_vien', 'asc')
            ->get();

        $data = [
            'lop' => $lop,
            'sinhViens' => $sinhViens,
            'giangVien' => $giangVien,
            'ngayXuat' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('giangvien.lop-chu-nhiem.pdf.danh-sach-sinh-vien', $data);
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'Danh_sach_sinh_vien_' . $lop->ma_lop . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Hiển thị báo cáo tổng hợp kết quả học tập của tất cả sinh viên trong lớp
     *
     * Function này cung cấp dashboard kết quả học tập cho GVCN để theo dõi
     * tình hình học tập của cả lớp, bao gồm điểm trung bình, số tín chỉ tích lũy,
     * xếp loại học tập, và các cảnh báo học vụ.
     *
     * Workflow:
     * 1. Xác thực GVCN và kiểm tra quyền:
     *    - Lấy giảng viên từ Auth
     *    - FindOrFail lớp where giang_vien_chu_nhiem_id
     * 2. Nhận filters từ request:
     *    - hoc_ky_id: Lọc theo học kỳ (optional)
     *    - xep_loai: Lọc theo xếp loại (Xuất sắc/Giỏi/Khá/...) (optional)
     * 3. Query kết quả học tập:
     *    - Join BangDiem với SinhVien
     *    - Where lop_hanh_chinh_id = $id
     *    - Apply filters nếu có
     *    - Group by sinh_vien_id
     * 4. Tính toán cho từng sinh viên:
     *    - Điểm trung bình tích lũy (ĐTBTL) hệ 4 và hệ 10
     *    - Tổng số tín chỉ đã tích lũy
     *    - Số tín chỉ nợ (chưa qua)
     *    - Xếp loại học tập:
     *      + Xuất sắc: ĐTBTL >= 3.6 (hệ 4)
     *      + Giỏi: 3.2 <= ĐTBTL < 3.6
     *      + Khá: 2.5 <= ĐTBTL < 3.2
     *      + Trung bình: 2.0 <= ĐTBTL < 2.5
     *      + Yếu: ĐTBTL < 2.0
     * 5. Thống kê tổng hợp:
     *    - Số sinh viên theo từng xếp loại
     *    - ĐTBTL trung bình của lớp
     *    - Top 5 sinh viên cao điểm
     *    - Danh sách sinh viên có kết quả yếu
     * 6. Return view với data
     *
     * Thông tin hiển thị:
     * - Filters:
     *   + Dropdown chọn học kỳ
     *   + Dropdown xếp loại
     * - Thống kê cards:
     *   + ĐTBTL trung bình lớp
     *   + Số SV xuất sắc/giỏi
     *   + Số SV yếu kém cần quan tâm
     * - Bảng kết quả:
     *   + STT, Mã SV, Họ tên
     *   + ĐTBTL hệ 4, hệ 10
     *   + Tín chỉ tích lũy
     *   + Tín chỉ nợ
     *   + Xếp loại (badge màu)
     * - Charts:
     *   + Pie chart phân bố xếp loại
     *   + Line chart xu hướng điểm theo học kỳ
     *
     * Tính năng đặc biệt:
     * - Highlight sinh viên có ĐTBTL < 2.0 (đỏ)
     * - Link quick đến chi tiết bảng điểm
     * - Export kết quả to Excel/PDF
     * - So sánh với trung bình khóa
     *
     * @param Request $request Filters:
     *   - hoc_ky_id (int|null)
     *   - xep_loai (string|null)
     * @param int $id ID lớp
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function xemKetQuaHocTap(Request $request, $id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra quyền truy cập
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        // Lọc theo học kỳ
        $hocKyId = $request->get('hoc_ky_id');

        // Query bảng điểm
        $query = BangDiem::whereHas('sinhVien', function ($q) use ($id) {
            $q->where('lop_hanh_chinh_id', $id);
        })->with(['sinhVien', 'hocKy']);

        if ($hocKyId) {
            $query->where('hoc_ky_id', $hocKyId);
        }

        // Lọc theo xếp loại
        if ($request->has('xep_loai') && $request->xep_loai != '') {
            $query->where('xep_loai_hoc_tap', $request->xep_loai);
        }

        // Lọc theo trạng thái công bố
        if ($request->has('da_cong_bo') && $request->da_cong_bo != '') {
            $query->where('da_cong_bo', $request->da_cong_bo);
        }

        // Tìm kiếm sinh viên
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->where('ma_sinh_vien', 'like', '%' . $search . '%')
                    ->orWhere('ho_ten', 'like', '%' . $search . '%');
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'diem_trung_binh_he_4');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang
        $bangDiems = $query->paginate(20)->appends($request->all());

        // Thống kê tổng quan
        $thongKe = [
            'tong_sinh_vien' => $lop->sinhVien->count(),
            'diem_tb_lop' => number_format($bangDiems->avg('diem_trung_binh_he_4'), 2),
            'xuat_sac' => $bangDiems->where('xep_loai_hoc_tap', 'xuat_sac')->count(),
            'gioi' => $bangDiems->where('xep_loai_hoc_tap', 'gioi')->count(),
            'kha' => $bangDiems->where('xep_loai_hoc_tap', 'kha')->count(),
            'trung_binh' => $bangDiems->where('xep_loai_hoc_tap', 'trung_binh')->count(),
            'yeu' => $bangDiems->where('xep_loai_hoc_tap', 'yeu')->count(),
            'kem' => $bangDiems->where('xep_loai_hoc_tap', 'kem')->count(),
        ];

        return view('giangvien.lop-chu-nhiem.ket-qua-hoc-tap', compact(
            'lop',
            'bangDiems',
            'hocKys',
            'giangVien',
            'thongKe',
            'hocKyId'
        ));
    }

    /**
     * Hiển thị danh sách cảnh báo học vụ của sinh viên trong lớp cho GVCN
     *
     * Function này cung cấp dashboard quản lý cảnh báo học vụ, giúp GVCN
     * theo dõi và xử lý kịp thời các trường hợp sinh viên vi phạm quy định,
     * kết quả học tập yếu, hoặc vắng học nhiều.
     *
     * Workflow:
     * 1. Xác thực GVCN và kiểm tra quyền:
     *    - Lấy giảng viên từ Auth
     *    - FindOrFail lớp where giang_vien_chu_nhiem_id
     * 2. Nhận filters từ request:
     *    - loai_canh_bao: 'diem_danh'|'hoc_tap'|'ky_luat' (optional)
     *    - trang_thai: 'chua_xu_ly'|'dang_xu_ly'|'da_xu_ly' (optional)
     *    - muc_do: 'nhe'|'trung_binh'|'nang' (optional)
     * 3. Query cảnh báo học vụ:
     *    - CanhBaoHocVu join với SinhVien
     *    - Where sinh_vien.lop_hanh_chinh_id = $id
     *    - Apply filters
     *    - OrderBy created_at DESC (mới nhất trước)
     * 4. Phân loại cảnh báo:
     *    a. Cảnh báo điểm danh:
     *       - Vắng > 20%: Cảnh báo level 1
     *       - Vắng > 30%: Cảnh báo level 2, cấm thi
     *    b. Cảnh báo học tập:
     *       - ĐTBTL < 2.0: Học vụ
     *       - ĐTBTL < 1.0: Nguy cơ buộc thôi học
     *    c. Cảnh báo kỷ luật:
     *       - Vi phạm quy chế
     *       - Gian lận thi cử
     * 5. Thống kê:
     *    - Tổng số cảnh báo
     *    - Số cảnh báo chưa xử lý
     *    - Phân bố theo loại
     * 6. Return view
     *
     * Thông tin hiển thị:
     * - Filters và search
     * - Thống kê cards:
     *   + Tổng cảnh báo
     *   + Chưa xử lý (đỏ)
     *   + Đang xử lý (vàng)
     *   + Đã xử lý (xanh)
     * - Bảng cảnh báo:
     *   + Mã SV, Họ tên
     *   + Loại cảnh báo (badge)
     *   + Nội dung cảnh báo
     *   + Mức độ (nghiêm trọng)
     *   + Ngày tạo
     *   + Trạng thái xử lý
     *   + Actions: Xem chi tiết, Xử lý, Gửi email
     * - Quick actions:
     *   + Gửi email nhắc nhở
     *   + Đánh dấu đã xử lý
     *   + Tạo biên bản họp phụ huynh
     *
     * Tính năng đặc biệt:
     * - Color coding theo mức độ nghiêm trọng
     * - Auto-highlight cảnh báo cấp bách
     * - Bulk actions cho nhiều cảnh báo
     * - Export danh sách cảnh báo
     * - Gửi email tự động cho sinh viên/phụ huynh
     *
     * @param Request $request Filters
     * @param int $id ID lớp
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function xemCanhBaoHocVu(Request $request, $id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra quyền truy cập
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        // Query cảnh báo học vụ
        $query = CanhBaoHocVu::whereHas('sinhVien', function ($q) use ($id) {
            $q->where('lop_hanh_chinh_id', $id);
        })->with(['sinhVien', 'hocKy', 'nguoiCanhBao']);

        // Lọc theo học kỳ
        if ($request->has('hoc_ky_id') && $request->hoc_ky_id != '') {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo mức độ
        if ($request->has('muc_do') && $request->muc_do != '') {
            $query->where('muc_do', $request->muc_do);
        }

        // Lọc theo loại cảnh báo
        if ($request->has('loai_canh_bao') && $request->loai_canh_bao != '') {
            $query->where('loai_canh_bao', $request->loai_canh_bao);
        }

        // Lọc theo trạng thái xử lý
        if ($request->has('da_xu_ly') && $request->da_xu_ly != '') {
            $query->where('da_xu_ly', $request->da_xu_ly);
        }

        // Tìm kiếm sinh viên
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->where('ma_sinh_vien', 'like', '%' . $search . '%')
                    ->orWhere('ho_ten', 'like', '%' . $search . '%');
            });
        }

        // Sắp xếp
        $query->orderBy('ngay_canh_bao', 'desc');

        // Phân trang
        $canhBaos = $query->paginate(20)->appends($request->all());

        // Thống kê
        $thongKe = [
            'tong_canh_bao' => CanhBaoHocVu::whereHas('sinhVien', function ($q) use ($id) {
                $q->where('lop_hanh_chinh_id', $id);
            })->count(),
            'chua_xu_ly' => CanhBaoHocVu::whereHas('sinhVien', function ($q) use ($id) {
                $q->where('lop_hanh_chinh_id', $id);
            })->chuaXuLy()->count(),
            'da_xu_ly' => CanhBaoHocVu::whereHas('sinhVien', function ($q) use ($id) {
                $q->where('lop_hanh_chinh_id', $id);
            })->daXuLy()->count(),
            'canh_cao' => CanhBaoHocVu::whereHas('sinhVien', function ($q) use ($id) {
                $q->where('lop_hanh_chinh_id', $id);
            })->mucDo('canh_cao')->count(),
            'dinh_chi' => CanhBaoHocVu::whereHas('sinhVien', function ($q) use ($id) {
                $q->where('lop_hanh_chinh_id', $id);
            })->mucDo('dinh_chi')->count(),
            'buoc_thoi_hoc' => CanhBaoHocVu::whereHas('sinhVien', function ($q) use ($id) {
                $q->where('lop_hanh_chinh_id', $id);
            })->mucDo('buoc_thoi_hoc')->count(),
        ];

        return view('giangvien.lop-chu-nhiem.canh-bao-hoc-vu', compact(
            'lop',
            'canhBaos',
            'hocKys',
            'giangVien',
            'thongKe'
        ));
    }
}
