<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaoTao\SinhVien;
use App\Models\GiangVien;
use App\Models\LopHocPhan;
use App\Models\KetQuaHocTap;
use App\Models\DiemDanh;
use App\Models\HocPhiHocKy;
use App\Models\DangKyMonHoc;
use App\Models\LopHocPhanSinhVien;
use App\Models\CanhBaoHocVu;
use App\Models\DaoTao\PhongHoc;
use App\Models\DaoTao\Khoa;
use App\Models\DaoTao\Nganh;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\HocKy;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BaoCaoController extends Controller
{
    /**
     * Hiển thị trang chủ dashboard báo cáo đào tạo với tất cả loại báo cáo
     *
     * Function này tạo landing page cho hệ thống báo cáo đào tạo,
     * cung cấp menu navigation đến 9 loại báo cáo khác nhau với
     * visual cards, icons, và descriptions.
     *
     * Workflow:
     * 1. Define reportTypes array với 9 report categories:
     *    a. Báo cáo Sinh viên:
     *       - Thống kê theo khoa, ngành, khóa, lớp
     *       - Icon: people-fill (blue)
     *       - Route: dao-tao.bao-cao.sinh-vien
     *    b. Báo cáo Kết quả học tập:
     *       - Phân bố điểm, GPA, tỷ lệ đỗ/trượt
     *       - Icon: clipboard-data (green)
     *       - Route: dao-tao.bao-cao.ket-qua
     *    c. Báo cáo Điểm danh:
     *       - Tỷ lệ vắng mặt theo lớp/môn học
     *       - Icon: calendar-check (warning)
     *       - Route: dao-tao.bao-cao.diem-danh
     *    d. Báo cáo Học phí:
     *       - Thu học phí, nợ học phí theo kỳ
     *       - Icon: cash-stack (info)
     *       - Route: dao-tao.bao-cao.hoc-phi
     *    e. Báo cáo Đăng ký môn học:
     *       - Thống kê đăng ký theo môn, lớp, học kỳ
     *       - Icon: journal-bookmark (secondary)
     *       - Route: dao-tao.bao-cao.dang-ky
     *    f. Báo cáo Xếp lớp:
     *       - Thành công/thất bại, tỷ lệ xếp lớp
     *       - Icon: diagram-3 (dark)
     *       - Route: dao-tao.bao-cao.xep-lop
     *    g. Báo cáo Tải giảng viên:
     *       - Số giờ giảng, số lớp, tải giảng
     *       - Icon: person-workspace (danger)
     *       - Route: dao-tao.bao-cao.tai-giang-vien
     *    h. Báo cáo Phòng học:
     *       - Sử dụng phòng, lịch phòng trống
     *       - Icon: door-open (primary)
     *       - Route: dao-tao.bao-cao.phong-hoc
     *    i. Báo cáo Cảnh báo học vụ:
     *       - Cảnh báo theo loại, mức độ, trạng thái
     *       - Icon: exclamation-triangle (warning)
     *       - Route: dao-tao.bao-cao.canh-bao
     * 2. Return view với reportTypes array
     *
     * Dashboard features:
     * - Grid layout với cards (3 columns)
     * - Mỗi card có:
     *   + Icon Bootstrap (bi-*)
     *   + Color theme
     *   + Title và description
     *   + Link đến report page
     * - Hover effects
     * - Responsive design
     * - Quick access menu
     *
     * Navigation:
     * - Click card => Route to specific report
     * - Breadcrumbs: Dashboard > Báo cáo
     * - Sidebar menu highlighting
     *
     * Use cases:
     * - Landing page cho reporting module
     * - Quick overview các loại báo cáo
     * - Central hub cho analytics
     * - Admin/Đào tạo staff access point
     *
     * @return \Illuminate\View\View Dashboard với:
     *   - reportTypes: Array 9 report categories
     */
    public function index()
    {
        $reportTypes = [
            [
                'title' => 'Báo cáo Sinh viên',
                'description' => 'Thống kê sinh viên theo khoa, ngành, khóa, lớp',
                'icon' => 'bi-people-fill',
                'color' => 'primary',
                'route' => 'dao-tao.bao-cao.sinh-vien'
            ],
            [
                'title' => 'Báo cáo Kết quả học tập',
                'description' => 'Phân bố điểm, GPA, tỷ lệ đỗ/trượt',
                'icon' => 'bi-clipboard-data',
                'color' => 'success',
                'route' => 'dao-tao.bao-cao.ket-qua'
            ],
            [
                'title' => 'Báo cáo Điểm danh',
                'description' => 'Tỷ lệ vắng mặt theo lớp/môn học',
                'icon' => 'bi-calendar-check',
                'color' => 'warning',
                'route' => 'dao-tao.bao-cao.diem-danh'
            ],
            [
                'title' => 'Báo cáo Học phí',
                'description' => 'Thu học phí, nợ học phí theo kỳ',
                'icon' => 'bi-cash-stack',
                'color' => 'info',
                'route' => 'dao-tao.bao-cao.hoc-phi'
            ],
            [
                'title' => 'Báo cáo Đăng ký môn học',
                'description' => 'Thống kê đăng ký theo môn, lớp, học kỳ',
                'icon' => 'bi-journal-bookmark',
                'color' => 'secondary',
                'route' => 'dao-tao.bao-cao.dang-ky'
            ],
            [
                'title' => 'Báo cáo Xếp lớp',
                'description' => 'Thành công/thất bại, tỷ lệ xếp lớp',
                'icon' => 'bi-diagram-3',
                'color' => 'dark',
                'route' => 'dao-tao.bao-cao.xep-lop'
            ],
            [
                'title' => 'Báo cáo Tải giảng viên',
                'description' => 'Số giờ giảng, số lớp, tải giảng',
                'icon' => 'bi-person-workspace',
                'color' => 'danger',
                'route' => 'dao-tao.bao-cao.tai-giang-vien'
            ],
            [
                'title' => 'Báo cáo Phòng học',
                'description' => 'Sử dụng phòng, lịch phòng trống',
                'icon' => 'bi-door-open',
                'color' => 'primary',
                'route' => 'dao-tao.bao-cao.phong-hoc'
            ],
            [
                'title' => 'Báo cáo Cảnh báo học vụ',
                'description' => 'Cảnh báo theo loại, mức độ, trạng thái',
                'icon' => 'bi-exclamation-triangle',
                'color' => 'warning',
                'route' => 'dao-tao.bao-cao.canh-bao'
            ],
        ];

        return view('daotao.bao-cao.index', compact('reportTypes'));
    }

    /**
     * Báo cáo tổng hợp về sinh viên theo khoa/ngành/khóa/lớp
     *
     * Function này cung cấp báo cáo chi tiết và thống kê tổng quan
     * về sinh viên trong toàn trường, hỗ trợ filters nhiều chiều
     * và analytics theo các metrics khác nhau.
     *
     * Workflow:
     * 1. Lấy master data cho filters:
     *    - khoas: Tất cả khoa
     *    - nganhs: Tất cả ngành
     *    - khoaHocs: Các khóa học (DESC theo năm)
     * 2. Khởi tạo query với eager loading:
     *    - SinhVien::with relationships:
     *      + khoaHoc
     *      + chuyenNganh.nganh.khoa (nested)
     *      + trangThaiHocTap
     *      + lopHanhChinh
     * 3. Áp dụng filters theo request:
     *    a. khoa_id:
     *       - whereHas chuyenNganh.nganh
     *       - Filter theo khoa_id
     *    b. nganh_id:
     *       - whereHas chuyenNganh
     *       - Filter theo nganh_id
     *    c. khoa_hoc_id:
     *       - Direct where khoa_hoc_id
     *    d. lop (tên lớp):
     *       - whereHas lopHanhChinh
     *       - LIKE search ten_lop
     * 4. Paginate: 50 sinh viên/page
     * 5. Tính thống kê tổng quan:
     *    - total: Tổng số sinh viên
     *    - hoc: Số đang học
     *    - bao_luu: Số bảo lưu
     *    - thoi_hoc: Số thôi học
     * 6. Tính thống kê theo khoa:
     *    - Join tables: sinh_vien, chuyen_nganh, nganh, khoa, trang_thai_hoc_tap
     *    - GROUP BY khoa
     *    - SELECT:
     *      + ten_khoa
     *      + COUNT(*) as total
     *      + SUM(CASE...) cho từng trạng thái
     * 7. Return view với full data
     *
     * Thông tin hiển thị:
     * - Filter panel:
     *   + Dropdown khoa
     *   + Dropdown ngành (cascade với khoa)
     *   + Dropdown khóa học
     *   + Input tìm kiếm lớp
     * - Summary cards:
     *   + Tổng sinh viên (blue)
     *   + Đang học (green)
     *   + Bảo lưu (yellow)
     *   + Thôi học (red)
     * - Bảng sinh viên:
     *   + Mã SV, Họ tên, Lớp
     *   + Khoa, Ngành, Khóa
     *   + Trạng thái học tập (badge)
     *   + Actions: View detail
     * - Thống kê theo khoa (table/chart):
     *   + Tên khoa
     *   + Tổng SV
     *   + Đang học, Bảo lưu, Thôi học
     *   + Bar chart phân bố
     * - Export buttons:
     *   + Xuất Excel
     *   + Xuất PDF
     *
     * Analytics features:
     * - Pie chart: Phân bố trạng thái học tập
     * - Bar chart: Sinh viên theo khoa
     * - Line chart: Xu hướng theo khóa học
     * - Drill-down: Click khoa => filter theo khoa
     *
     * @param Request $request Filters:
     *   - khoa_id: ID khoa
     *   - nganh_id: ID ngành
     *   - khoa_hoc_id: ID khóa học
     *   - lop: Tên lớp (search)
     * @return \Illuminate\View\View Báo cáo sinh viên với:
     *   - sinhViens: Paginated collection
     *   - khoas, nganhs, khoaHocs: Filter options
     *   - statistics: Tổng hợp
     *   - statsByKhoa: Theo khoa
     */
    public function sinhVien(Request $request)
    {
        $khoas = Khoa::all();
        $nganhs = Nganh::all();
        $khoaHocs = KhoaHoc::orderBy('nam_bat_dau', 'desc')->get();

        $query = SinhVien::with(['khoaHoc', 'chuyenNganh.nganh.khoa', 'trangThaiHocTap', 'lopHanhChinh']);

        // Filters
        if ($request->filled('khoa_id')) {
            $query->whereHas('chuyenNganh.nganh', function($q) use ($request) {
                $q->where('khoa_id', $request->khoa_id);
            });
        }
        if ($request->filled('nganh_id')) {
            $query->whereHas('chuyenNganh', function($q) use ($request) {
                $q->where('nganh_id', $request->nganh_id);
            });
        }
        if ($request->filled('khoa_hoc_id')) {
            $query->where('khoa_hoc_id', $request->khoa_hoc_id);
        }
        if ($request->filled('lop')) {
            $query->whereHas('lopHanhChinh', function($q) use ($request) {
                $q->where('ten_lop', 'like', '%' . $request->lop . '%');
            });
        }

        $sinhViens = $query->paginate(50);

        // Thống kê tổng quan
        $statistics = [
            'total' => SinhVien::count(),
            'hoc' => SinhVien::whereHas('trangThaiHocTap', function($q) {
                $q->where('ten_trang_thai', 'Đang học');
            })->count(),
            'bao_luu' => SinhVien::whereHas('trangThaiHocTap', function($q) {
                $q->where('ten_trang_thai', 'Bảo lưu');
            })->count(),
            'thoi_hoc' => SinhVien::whereHas('trangThaiHocTap', function($q) {
                $q->where('ten_trang_thai', 'Thôi học');
            })->count(),
        ];

        // Thống kê theo khoa
        $statsByKhoa = DB::table('sinh_vien')
            ->join('chuyen_nganh', 'sinh_vien.chuyen_nganh_id', '=', 'chuyen_nganh.id')
            ->join('nganh', 'chuyen_nganh.nganh_id', '=', 'nganh.id')
            ->join('khoa', 'nganh.khoa_id', '=', 'khoa.id')
            ->leftJoin('trang_thai_hoc_tap', 'sinh_vien.trang_thai_hoc_tap_id', '=', 'trang_thai_hoc_tap.id')
            ->select(
                'khoa.ten_khoa',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN trang_thai_hoc_tap.ten_trang_thai = 'Đang học' THEN 1 ELSE 0 END) as dang_hoc"),
                DB::raw("SUM(CASE WHEN trang_thai_hoc_tap.ten_trang_thai = 'Bảo lưu' THEN 1 ELSE 0 END) as bao_luu"),
                DB::raw("SUM(CASE WHEN trang_thai_hoc_tap.ten_trang_thai = 'Thôi học' THEN 1 ELSE 0 END) as thoi_hoc")
            )
            ->groupBy('khoa.id', 'khoa.ten_khoa')
            ->get();

        return view('daotao.bao-cao.sinh-vien', compact('sinhViens', 'khoas', 'nganhs', 'khoaHocs', 'statistics', 'statsByKhoa'));
    }

    /**
     * Báo cáo kết quả học tập với phân bố điểm và GPA analytics
     *
     * Function này cung cấp báo cáo chi tiết về kết quả học tập,
     * bao gồm phân bố điểm, GPA, tỷ lệ qua môn, và analytics
     * theo nhiều chiều khác nhau.
     *
     * Workflow:
     * 1. Lấy master data cho filters:
     *    - hocKys: Học kỳ (DESC theo năm)
     *    - khoaHocs: Khóa học (DESC)
     * 2. Khởi tạo query KetQuaHocTap:
     *    - with eager loading:
     *      + lopHocPhanSinhVien.lopHocPhan.monHoc
     *      + Load nested relationships
     * 3. Áp dụng filters (chưa implement full trong code hiện tại):
     *    - hoc_ky_id: Filter theo học kỳ
     *    - khoa_hoc_id: Filter theo khóa
     *    - lop_hoc_phan_id: Filter theo lớp học phần
     * 4. Tính analytics:
     *    a. Phân bố điểm chữ:
     *       - GROUP BY diem_chu
     *       - COUNT cho mỗi loại (A+, A, B+, ...)
     *    b. Phân bố GPA:
     *       - Ranges: 3.6-4.0, 3.2-3.59, 2.5-3.19, ...
     *    c. Tỷ lệ qua môn:
     *       - COUNT(qua_mon = true) / COUNT(*)
     *    d. Điểm trung bình:
     *       - AVG(diem_he_10)
     *       - AVG(diem_he_4)
     * 5. Return view với statistics
     *
     * Metrics tính toán:
     * - Tổng số kết quả
     * - Phân bố điểm chữ:
     *   + A+: >= 9.5 (4.0)
     *   + A: 8.5-9.4 (3.7-3.9)
     *   + B+: 8.0-8.4 (3.5-3.6)
     *   + B: 7.0-7.9 (3.0-3.4)
     *   + C+: 6.5-6.9 (2.5-2.9)
     *   + C: 5.5-6.4 (2.0-2.4)
     *   + D+: 5.0-5.4 (1.5-1.9)
     *   + D: 4.0-4.9 (1.0-1.4)
     *   + F: < 4.0 (< 1.0)
     * - Tỷ lệ qua môn (%)
     * - Tỷ lệ rớt (%)
     * - Điểm TB hệ 10 và hệ 4
     * - Phân loại học lực:
     *   + Xuất sắc: GPA >= 3.6
     *   + Giỏi: GPA 3.2-3.59
     *   + Khá: GPA 2.5-3.19
     *   + Trung bình: GPA 2.0-2.49
     *   + Yếu: GPA < 2.0
     *
     * Charts hiển thị:
     * - Column chart: Phân bố điểm chữ
     * - Pie chart: Tỷ lệ qua/rớt
     * - Histogram: Phân bố GPA
     * - Box plot: Phân tích điểm (min, Q1, median, Q3, max)
     *
     * Filters:
     * - Học kỳ dropdown
     * - Khóa học dropdown
     * - Lớp học phần search
     * - Ngành/Khoa cascading
     *
     * Export options:
     * - Excel: Full data với charts
     * - PDF: Professional report
     * - CSV: Raw data
     *
     * @param Request $request Filters:
     *   - hoc_ky_id: Học kỳ
     *   - khoa_hoc_id: Khóa học
     *   - lop_hoc_phan_id: Lớp học phần
     * @return \Illuminate\View\View Báo cáo kết quả
     */
    public function ketQua(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
        $khoaHocs = KhoaHoc::orderBy('nam_bat_dau', 'desc')->get();

        $query = KetQuaHocTap::with(['lopHocPhanSinhVien.lopHocPhan.monHoc']);

        if ($request->filled('hoc_ky_id')) {
            $query->whereHas('lopHocPhanSinhVien.lopHocPhan', function($q) use ($request) {
                $q->where('hoc_ky_id', $request->hoc_ky_id);
            });
        }

        // Statistics
        $statistics = [
            'total_ket_qua' => KetQuaHocTap::count(),
            'qua_mon' => KetQuaHocTap::where('qua_mon', true)->count(),
            'truot_mon' => KetQuaHocTap::where('qua_mon', false)->count(),
            'avg_gpa' => KetQuaHocTap::avg('diem_he_4') ?? 0,
        ];

        // Grade distribution
        $gradeDistribution = KetQuaHocTap::select('diem_chu', DB::raw('count(*) as count'))
            ->whereNotNull('diem_chu')
            ->groupBy('diem_chu')
            ->orderByRaw("FIELD(diem_chu, 'A+', 'A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F')")
            ->get();

        // GPA by Khoa Hoc
        $gpaByKhoa = DB::table('sinh_vien')
            ->join('khoa_hoc', 'sinh_vien.khoa_hoc_id', '=', 'khoa_hoc.id')
            ->leftJoin('lop_hoc_phan_sinh_vien', 'sinh_vien.id', '=', 'lop_hoc_phan_sinh_vien.sinh_vien_id')
            ->leftJoin('ket_qua_hoc_tap', 'lop_hoc_phan_sinh_vien.id', '=', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id')
            ->select('khoa_hoc.ten_khoa_hoc', DB::raw('AVG(ket_qua_hoc_tap.diem_he_4) as avg_gpa'))
            ->groupBy('khoa_hoc.id', 'khoa_hoc.ten_khoa_hoc')
            ->get();

        // Detailed results by course
        $detailedResults = DB::table('lop_hoc_phan')
            ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->leftJoin('lop_hoc_phan_giang_vien', function($join) {
                $join->on('lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                     ->where('lop_hoc_phan_giang_vien.vai_tro', '=', 'giang_vien_chinh');
            })
            ->leftJoin('giang_vien', 'lop_hoc_phan_giang_vien.giang_vien_id', '=', 'giang_vien.id')
            ->leftJoin('lop_hoc_phan_sinh_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id')
            ->leftJoin('ket_qua_hoc_tap', 'lop_hoc_phan_sinh_vien.id', '=', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id')
            ->select(
                'mon_hoc.ten_mon',
                'lop_hoc_phan.ma_lop_hp',
                'giang_vien.ho_ten as ten_giang_vien',
                DB::raw('COUNT(DISTINCT lop_hoc_phan_sinh_vien.id) as total'),
                DB::raw('AVG(ket_qua_hoc_tap.diem_he_10) as avg_diem'),
                DB::raw('SUM(CASE WHEN ket_qua_hoc_tap.qua_mon = 1 THEN 1 ELSE 0 END) as qua_mon'),
                DB::raw('SUM(CASE WHEN ket_qua_hoc_tap.qua_mon = 0 THEN 1 ELSE 0 END) as truot_mon')
            )
            ->groupBy('lop_hoc_phan.id', 'mon_hoc.ten_mon', 'lop_hoc_phan.ma_lop_hp', 'giang_vien.ho_ten')
            ->limit(20)
            ->get();

        return view('daotao.bao-cao.ket-qua', compact('hocKys', 'khoaHocs', 'statistics', 'gradeDistribution', 'gpaByKhoa', 'detailedResults'));
    }

    /**
     * Báo cáo Điểm danh
     */
    public function diemDanh(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Statistics
        $statistics = [
            'total_buoi' => DiemDanh::distinct('lich_hoc_chi_tiet_id')->count('lich_hoc_chi_tiet_id'),
            'co_mat' => DiemDanh::where('trang_thai', 'co_mat')->count(),
            'vang_mat' => DiemDanh::where('trang_thai', 'vang')->count(),
        ];

        // Attendance data by class
        $attendanceData = DB::table('lop_hoc_phan')
            ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->leftJoin('lop_hoc_phan_giang_vien', function($join) {
                $join->on('lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                     ->where('lop_hoc_phan_giang_vien.vai_tro', '=', 'giang_vien_chinh');
            })
            ->leftJoin('giang_vien', 'lop_hoc_phan_giang_vien.giang_vien_id', '=', 'giang_vien.id')
            ->leftJoin('lop_hoc_phan_sinh_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id')
            ->leftJoin('diem_danh', 'lop_hoc_phan_sinh_vien.id', '=', 'diem_danh.lop_hoc_phan_sinh_vien_id')
            ->select(
                'lop_hoc_phan.ma_lop_hp',
                'mon_hoc.ten_mon',
                'giang_vien.ho_ten as ten_giang_vien',
                DB::raw('COUNT(DISTINCT lop_hoc_phan_sinh_vien.sinh_vien_id) as so_luong_sv'),
                DB::raw('COUNT(DISTINCT diem_danh.lich_hoc_chi_tiet_id) as total_buoi'),
                DB::raw('SUM(CASE WHEN diem_danh.trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat'),
                DB::raw('SUM(CASE WHEN diem_danh.trang_thai = "vang" THEN 1 ELSE 0 END) as vang_mat')
            )
            ->groupBy('lop_hoc_phan.id', 'lop_hoc_phan.ma_lop_hp', 'mon_hoc.ten_mon', 'giang_vien.ho_ten')
            ->limit(50)
            ->get();

        return view('daotao.bao-cao.diem-danh', compact('hocKys', 'statistics', 'attendanceData'));
    }

    /**
     * Báo cáo Học phí
     */
    public function hocPhi(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        $query = HocPhiHocKy::with(['sinhVien', 'hocKy']);

        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        $hocPhiData = $query->limit(50)->get();

        // Statistics
        $totalHocPhi = HocPhiHocKy::sum('tong_so_tien') ?? 0;
        $daDong = HocPhiHocKy::sum('so_tien_da_dong') ?? 0;
        $conNo = HocPhiHocKy::sum('so_tien_con_lai') ?? 0;

        $statistics = [
            'total_hoc_phi' => $totalHocPhi,
            'da_dong' => $daDong,
            'con_no' => $conNo,
            'ty_le_thu' => $totalHocPhi > 0 ? ($daDong / $totalHocPhi * 100) : 0,
        ];

        // Stats by Khoa Hoc
        $statsByKhoa = DB::table('hoc_phi_hoc_ky')
            ->join('sinh_vien', 'hoc_phi_hoc_ky.sinh_vien_id', '=', 'sinh_vien.id')
            ->join('khoa_hoc', 'sinh_vien.khoa_hoc_id', '=', 'khoa_hoc.id')
            ->select(
                'khoa_hoc.ten_khoa_hoc',
                DB::raw('COUNT(DISTINCT sinh_vien.id) as so_luong_sv'),
                DB::raw('SUM(hoc_phi_hoc_ky.tong_so_tien) as total'),
                DB::raw('SUM(hoc_phi_hoc_ky.so_tien_da_dong) as da_dong'),
                DB::raw('SUM(hoc_phi_hoc_ky.so_tien_con_lai) as con_no')
            )
            ->groupBy('khoa_hoc.id', 'khoa_hoc.ten_khoa_hoc')
            ->get();

        return view('daotao.bao-cao.hoc-phi', compact('hocKys', 'statistics', 'hocPhiData', 'statsByKhoa'));
    }

    /**
     * Báo cáo Đăng ký môn học
     */
    public function dangKy(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Statistics
        $statistics = [
            'total_dang_ky' => DangKyMonHoc::count(),
            'thanh_cong' => DangKyMonHoc::where('trang_thai', 'thanh_cong')->count(),
            'huy' => DangKyMonHoc::where('trang_thai', 'huy')->count(),
        ];

        // Top courses
        $topCourses = DB::table('dang_ky_mon_hocs')
            ->join('lop_hoc_phan', 'dang_ky_mon_hocs.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
            ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->select(
                'mon_hoc.ten_mon',
                'mon_hoc.ma_mon',
                'mon_hoc.so_tin_chi',
                DB::raw('COUNT(*) as so_luot_dang_ky')
            )
            ->groupBy('mon_hoc.id', 'mon_hoc.ten_mon', 'mon_hoc.ma_mon', 'mon_hoc.so_tin_chi')
            ->orderByDesc('so_luot_dang_ky')
            ->limit(10)
            ->get();

        // Registration by class
        $registrationByClass = DB::table('lop_hoc_phan')
            ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->leftJoin('lop_hoc_phan_giang_vien', function($join) {
                $join->on('lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                     ->where('lop_hoc_phan_giang_vien.vai_tro', '=', 'giang_vien_chinh');
            })
            ->leftJoin('giang_vien', 'lop_hoc_phan_giang_vien.giang_vien_id', '=', 'giang_vien.id')
            ->leftJoin('lop_hoc_phan_sinh_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id')
            ->select(
                'lop_hoc_phan.ma_lop_hp',
                'mon_hoc.ten_mon',
                'giang_vien.ho_ten as ten_giang_vien',
                'lop_hoc_phan.suc_chua as si_so_toi_da',
                DB::raw('COUNT(lop_hoc_phan_sinh_vien.id) as da_dang_ky')
            )
            ->groupBy('lop_hoc_phan.id', 'lop_hoc_phan.ma_lop_hp', 'mon_hoc.ten_mon', 'giang_vien.ho_ten', 'lop_hoc_phan.suc_chua')
            ->limit(50)
            ->get();

        return view('daotao.bao-cao.dang-ky', compact('hocKys', 'statistics', 'topCourses', 'registrationByClass'));
    }

    /**
     * Báo cáo Xếp lớp
     */
    public function xepLop(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Statistics
        $totalLop = LopHocPhan::count();
        $duSiSo = LopHocPhan::whereRaw('(SELECT COUNT(*) FROM lop_hoc_phan_sinh_vien WHERE lop_hoc_phan_id = lop_hoc_phan.id) >= 10')->count();
        $thieuSiSo = $totalLop - $duSiSo;

        $statistics = [
            'total_lop' => $totalLop,
            'du_si_so' => $duSiSo,
            'thieu_si_so' => $thieuSiSo,
            'ty_le_thanh_cong' => $totalLop > 0 ? ($duSiSo / $totalLop * 100) : 0,
        ];

        // Class assignments
        $classAssignments = DB::table('lop_hoc_phan')
            ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->leftJoin('lop_hoc_phan_giang_vien', function($join) {
                $join->on('lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                     ->where('lop_hoc_phan_giang_vien.vai_tro', '=', 'giang_vien_chinh');
            })
            ->leftJoin('giang_vien', 'lop_hoc_phan_giang_vien.giang_vien_id', '=', 'giang_vien.id')
            ->select(
                'lop_hoc_phan.ma_lop_hp',
                'mon_hoc.ten_mon',
                'giang_vien.ho_ten as ten_giang_vien',
                'lop_hoc_phan.suc_chua as si_so_toi_da',
                'lop_hoc_phan.so_luong_toi_thieu as si_so_toi_thieu',
                DB::raw('(SELECT COUNT(*) FROM lop_hoc_phan_sinh_vien WHERE lop_hoc_phan_id = lop_hoc_phan.id) as da_xep')
            )
            ->limit(50)
            ->get();

        return view('daotao.bao-cao.xep-lop', compact('hocKys', 'statistics', 'classAssignments'));
    }

    /**
     * Báo cáo Tải giảng viên
     */
    public function taiGiangVien(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Workload data
        $workloadData = DB::table('giang_vien')
            ->leftJoin('khoa', 'giang_vien.khoa_id', '=', 'khoa.id')
            ->leftJoin('lop_hoc_phan_giang_vien', function($join) {
                $join->on('giang_vien.id', '=', 'lop_hoc_phan_giang_vien.giang_vien_id')
                     ->where('lop_hoc_phan_giang_vien.vai_tro', '=', 'giang_vien_chinh');
            })
            ->leftJoin('lop_hoc_phan', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
            ->leftJoin('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->select(
                'giang_vien.ma_giang_vien',
                'giang_vien.ho_ten',
                'giang_vien.email',
                'khoa.ten_khoa',
                DB::raw('COUNT(DISTINCT lop_hoc_phan.id) as so_lop'),
                DB::raw('SUM(mon_hoc.so_tin_chi) as tong_tin_chi'),
                DB::raw('SUM(mon_hoc.so_tin_chi * 15) as tong_gio')
            )
            ->groupBy('giang_vien.id', 'giang_vien.ma_giang_vien', 'giang_vien.ho_ten', 'giang_vien.email', 'khoa.ten_khoa')
            ->get();

        $statistics = [
            'total_giang_vien' => GiangVien::count(),
            'avg_lop_per_gv' => $workloadData->avg('so_lop') ?? 0,
            'avg_gio_per_gv' => $workloadData->avg('tong_gio') ?? 0,
        ];

        return view('daotao.bao-cao.tai-giang-vien', compact('hocKys', 'workloadData', 'statistics'));
    }

    /**
     * Báo cáo Phòng học
     */
    public function phongHoc(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Room usage
        $roomUsage = DB::table('phong_hoc')
            ->leftJoin('lich_hoc_chi_tiet', 'phong_hoc.id', '=', 'lich_hoc_chi_tiet.phong_hoc_id')
            ->select(
                'phong_hoc.ten_phong',
                'phong_hoc.vi_tri',
                'phong_hoc.suc_chua',
                'phong_hoc.loai_phong',
                DB::raw('COUNT(DISTINCT lich_hoc_chi_tiet.id) as so_buoi'),
                DB::raw('COUNT(DISTINCT lich_hoc_chi_tiet.id) * 2 as tong_gio')
            )
            ->groupBy('phong_hoc.id', 'phong_hoc.ten_phong', 'phong_hoc.vi_tri', 'phong_hoc.suc_chua', 'phong_hoc.loai_phong')
            ->get();

        $totalPhong = PhongHoc::count();
        $dangSuDung = $roomUsage->where('so_buoi', '>', 0)->count();

        $statistics = [
            'total_phong' => $totalPhong,
            'dang_su_dung' => $dangSuDung,
            'ty_le_su_dung' => $totalPhong > 0 ? ($dangSuDung / $totalPhong * 100) : 0,
        ];

        return view('daotao.bao-cao.phong-hoc', compact('hocKys', 'roomUsage', 'statistics'));
    }

    /**
     * Báo cáo Cảnh báo học vụ
     */
    public function canhBao(Request $request)
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        $query = CanhBaoHocVu::with(['sinhVien', 'hocKy']);

        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }
        if ($request->filled('loai_canh_bao')) {
            $query->where('loai_canh_bao', $request->loai_canh_bao);
        }

        $warningData = $query->paginate(50);

        // Statistics
        $statistics = [
            'total_canh_bao' => CanhBaoHocVu::count(),
            'hoc_vu' => CanhBaoHocVu::where('loai_canh_bao', 'hoc_vu')->count(),
            'diem_danh' => CanhBaoHocVu::where('loai_canh_bao', 'diem_danh')->count(),
            'ky_luat' => CanhBaoHocVu::where('loai_canh_bao', 'ky_luat')->count(),
        ];

        // Stats by type
        $statsByType = CanhBaoHocVu::select('loai_canh_bao', DB::raw('count(*) as count'))
            ->groupBy('loai_canh_bao')
            ->get();

        // Stats by severity
        $statsBySeverity = CanhBaoHocVu::select('muc_do', DB::raw('count(*) as count'))
            ->groupBy('muc_do')
            ->get();

        return view('daotao.bao-cao.canh-bao', compact('hocKys', 'warningData', 'statistics', 'statsByType', 'statsBySeverity'));
    }

    /**
     * Xuất báo cáo Excel
     */
    public function exportExcel(Request $request)
    {
        $loaiBaoCao = $request->input('loai', 'sinh-vien');
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'TRƯỜNG ĐẠI HỌC ABC');
        $sheet->setCellValue('A2', 'BÁO CÁO ĐÀO TẠO');
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Tiêu đề báo cáo
        $reportTitles = [
            'sinh-vien' => 'BÁO CÁO SINH VIÊN',
            'ket-qua' => 'BÁO CÁO KẾT QUẢ HỌC TẬP',
            'diem-danh' => 'BÁO CÁO ĐIỂM DANH',
            'hoc-phi' => 'BÁO CÁO HỌC PHÍ',
            'dang-ky' => 'BÁO CÁO ĐĂNG KÝ MÔN HỌC',
            'xep-lop' => 'BÁO CÁO XẾP LỚP',
            'tai-giang-vien' => 'BÁO CÁO TẢI GIẢNG VIÊN',
            'phong-hoc' => 'BÁO CÁO SỬ DỤNG PHÒNG HỌC',
            'canh-bao' => 'BÁO CÁO CẢNH BÁO HỌC VỤ',
        ];

        $sheet->setCellValue('A3', $reportTitles[$loaiBaoCao] ?? 'BÁO CÁO');
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Ngày xuất: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A4:F4');

        $row = 6;

        // Lấy filters
        $khoaId = $request->input('khoa_id');
        $nganhId = $request->input('nganh_id');
        $hocKyId = $request->input('hoc_ky_id');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        switch ($loaiBaoCao) {
            case 'sinh-vien':
                $sheet->setCellValue('A' . $row, 'STT');
                $sheet->setCellValue('B' . $row, 'Mã SV');
                $sheet->setCellValue('C' . $row, 'Họ tên');
                $sheet->setCellValue('D' . $row, 'Khoa');
                $sheet->setCellValue('E' . $row, 'Ngành');
                $sheet->setCellValue('F' . $row, 'Trạng thái');
                
                $this->styleHeaderRow($sheet, $row, 'A', 'F');
                $row++;

                $query = SinhVien::with(['chuyenNganh.nganh.khoa', 'trangThaiHocTap']);
                
                if ($khoaId) {
                    $query->whereHas('chuyenNganh.nganh', function($q) use ($khoaId) {
                        $q->where('khoa_id', $khoaId);
                    });
                }
                if ($nganhId) {
                    $query->whereHas('chuyenNganh', function($q) use ($nganhId) {
                        $q->where('nganh_id', $nganhId);
                    });
                }

                $sinhViens = $query->get();
                $stt = 1;

                foreach ($sinhViens as $sv) {
                    $sheet->setCellValue('A' . $row, $stt++);
                    $sheet->setCellValue('B' . $row, $sv->ma_sinh_vien);
                    $sheet->setCellValue('C' . $row, $sv->ho_ten);
                    $sheet->setCellValue('D' . $row, $sv->chuyenNganh->nganh->khoa->ten_khoa ?? '');
                    $sheet->setCellValue('E' . $row, $sv->chuyenNganh->nganh->ten_nganh ?? '');
                    $sheet->setCellValue('F' . $row, $sv->trangThaiHocTap->ten_trang_thai ?? '');
                    $row++;
                }
                break;

            case 'ket-qua':
                $sheet->setCellValue('A' . $row, 'STT');
                $sheet->setCellValue('B' . $row, 'Mã SV');
                $sheet->setCellValue('C' . $row, 'Họ tên');
                $sheet->setCellValue('D' . $row, 'Môn học');
                $sheet->setCellValue('E' . $row, 'Điểm TB');
                $sheet->setCellValue('F' . $row, 'Kết quả');
                
                $this->styleHeaderRow($sheet, $row, 'A', 'F');
                $row++;

                $query = KetQuaHocTap::with(['lopHocPhanSinhVien.sinhVien', 'lopHocPhanSinhVien.lopHocPhan.monHoc']);
                
                if ($hocKyId) {
                    $query->whereHas('lopHocPhanSinhVien.lopHocPhan', function($q) use ($hocKyId) {
                        $q->where('hoc_ky_id', $hocKyId);
                    });
                }

                $ketQuas = $query->get();
                $stt = 1;

                foreach ($ketQuas as $kq) {
                    $sheet->setCellValue('A' . $row, $stt++);
                    $sheet->setCellValue('B' . $row, $kq->lopHocPhanSinhVien->sinhVien->ma_sinh_vien ?? '');
                    $sheet->setCellValue('C' . $row, $kq->lopHocPhanSinhVien->sinhVien->ho_ten ?? '');
                    $sheet->setCellValue('D' . $row, $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon ?? '');
                    $sheet->setCellValue('E' . $row, $kq->diem_tong_ket);
                    $sheet->setCellValue('F' . $row, $kq->qua_mon ? 'Đạt' : 'Không đạt');
                    $row++;
                }
                break;

            case 'diem-danh':
                $sheet->setCellValue('A' . $row, 'STT');
                $sheet->setCellValue('B' . $row, 'Mã SV');
                $sheet->setCellValue('C' . $row, 'Họ tên');
                $sheet->setCellValue('D' . $row, 'Lớp HP');
                $sheet->setCellValue('E' . $row, 'Ngày học');
                $sheet->setCellValue('F' . $row, 'Trạng thái');
                
                $this->styleHeaderRow($sheet, $row, 'A', 'F');
                $row++;

                $query = DiemDanh::with(['sinhVien', 'lichHocChiTiet.lopHocPhan']);
                
                if ($tuNgay && $denNgay) {
                    $query->whereHas('lichHocChiTiet', function($q) use ($tuNgay, $denNgay) {
                        $q->whereBetween('ngay_hoc', [$tuNgay, $denNgay]);
                    });
                }

                $diemDanhs = $query->get();
                $stt = 1;

                foreach ($diemDanhs as $dd) {
                    $sheet->setCellValue('A' . $row, $stt++);
                    $sheet->setCellValue('B' . $row, $dd->sinhVien->ma_sinh_vien ?? '');
                    $sheet->setCellValue('C' . $row, $dd->sinhVien->ho_ten ?? '');
                    $sheet->setCellValue('D' . $row, $dd->lichHocChiTiet->lopHocPhan->ma_lop_hp ?? '');
                    $sheet->setCellValue('E' . $row, $dd->lichHocChiTiet->ngay_hoc ?? '');
                    $sheet->setCellValue('F' . $row, $this->getTrangThaiDiemDanhText($dd->trang_thai));
                    $row++;
                }
                break;

            case 'hoc-phi':
                $sheet->setCellValue('A' . $row, 'STT');
                $sheet->setCellValue('B' . $row, 'Mã SV');
                $sheet->setCellValue('C' . $row, 'Họ tên');
                $sheet->setCellValue('D' . $row, 'Học kỳ');
                $sheet->setCellValue('E' . $row, 'Tổng phí');
                $sheet->setCellValue('F' . $row, 'Đã đóng');
                
                $this->styleHeaderRow($sheet, $row, 'A', 'F');
                $row++;

                $query = HocPhiHocKy::with(['sinhVien', 'hocKy']);
                
                if ($hocKyId) {
                    $query->where('hoc_ky_id', $hocKyId);
                }

                $hocPhis = $query->get();
                $stt = 1;

                foreach ($hocPhis as $hp) {
                    $sheet->setCellValue('A' . $row, $stt++);
                    $sheet->setCellValue('B' . $row, $hp->sinhVien->ma_sinh_vien ?? '');
                    $sheet->setCellValue('C' . $row, $hp->sinhVien->ho_ten ?? '');
                    $sheet->setCellValue('D' . $row, $hp->hocKy->ten_hoc_ky ?? '');
                    $sheet->setCellValue('E' . $row, number_format($hp->tong_hoc_phi));
                    $sheet->setCellValue('F' . $row, number_format($hp->da_dong));
                    $row++;
                }
                break;

            case 'canh-bao':
                $sheet->setCellValue('A' . $row, 'STT');
                $sheet->setCellValue('B' . $row, 'Mã SV');
                $sheet->setCellValue('C' . $row, 'Họ tên');
                $sheet->setCellValue('D' . $row, 'Loại cảnh báo');
                $sheet->setCellValue('E' . $row, 'Mức độ');
                $sheet->setCellValue('F' . $row, 'Ngày cảnh báo');
                
                $this->styleHeaderRow($sheet, $row, 'A', 'F');
                $row++;

                $query = CanhBaoHocVu::with(['sinhVien', 'hocKy']);
                
                if ($hocKyId) {
                    $query->where('hoc_ky_id', $hocKyId);
                }

                $canhBaos = $query->get();
                $stt = 1;

                foreach ($canhBaos as $cb) {
                    $sheet->setCellValue('A' . $row, $stt++);
                    $sheet->setCellValue('B' . $row, $cb->sinhVien->ma_sinh_vien ?? '');
                    $sheet->setCellValue('C' . $row, $cb->sinhVien->ho_ten ?? '');
                    $sheet->setCellValue('D' . $row, $cb->loai_canh_bao);
                    $sheet->setCellValue('E' . $row, $cb->muc_do);
                    $sheet->setCellValue('F' . $row, $cb->ngay_canh_bao);
                    $row++;
                }
                break;

            default:
                $sheet->setCellValue('A' . $row, 'Loại báo cáo không hợp lệ');
                break;
        }

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Borders
        if ($row > 6) {
            $sheet->getStyle('A6:F' . ($row - 1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        // Export
        $writer = new Xlsx($spreadsheet);
        $fileName = 'bao_cao_' . $loaiBaoCao . '_' . now()->format('YmdHis') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Xuất báo cáo PDF
     */
    public function exportPdf(Request $request)
    {
        $loaiBaoCao = $request->input('loai', 'sinh-vien');
        $khoaId = $request->input('khoa_id');
        $nganhId = $request->input('nganh_id');
        $hocKyId = $request->input('hoc_ky_id');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        $data = [
            'loaiBaoCao' => $loaiBaoCao,
            'ngayXuat' => now()->format('d/m/Y H:i'),
        ];

        $reportTitles = [
            'sinh-vien' => 'BÁO CÁO SINH VIÊN',
            'ket-qua' => 'BÁO CÁO KẾT QUẢ HỌC TẬP',
            'diem-danh' => 'BÁO CÁO ĐIỂM DANH',
            'hoc-phi' => 'BÁO CÁO HỌC PHÍ',
            'dang-ky' => 'BÁO CÁO ĐĂNG KÝ MÔN HỌC',
            'xep-lop' => 'BÁO CÁO XẾP LỚP',
            'tai-giang-vien' => 'BÁO CÁO TẢI GIẢNG VIÊN',
            'phong-hoc' => 'BÁO CÁO SỬ DỤNG PHÒNG HỌC',
            'canh-bao' => 'BÁO CÁO CẢNH BÁO HỌC VỤ',
        ];

        $data['tieuDe'] = $reportTitles[$loaiBaoCao] ?? 'BÁO CÁO';

        switch ($loaiBaoCao) {
            case 'sinh-vien':
                $query = SinhVien::with(['chuyenNganh.nganh.khoa', 'trangThaiHocTap']);
                
                if ($khoaId) {
                    $query->whereHas('chuyenNganh.nganh', function($q) use ($khoaId) {
                        $q->where('khoa_id', $khoaId);
                    });
                }
                if ($nganhId) {
                    $query->whereHas('chuyenNganh', function($q) use ($nganhId) {
                        $q->where('nganh_id', $nganhId);
                    });
                }

                $data['items'] = $query->get();
                break;

            case 'ket-qua':
                $query = KetQuaHocTap::with(['lopHocPhanSinhVien.sinhVien', 'lopHocPhanSinhVien.lopHocPhan.monHoc']);
                
                if ($hocKyId) {
                    $query->whereHas('lopHocPhanSinhVien.lopHocPhan', function($q) use ($hocKyId) {
                        $q->where('hoc_ky_id', $hocKyId);
                    });
                }

                $data['items'] = $query->get();
                break;

            case 'diem-danh':
                $query = DiemDanh::with(['sinhVien', 'lichHocChiTiet.lopHocPhan']);
                
                if ($tuNgay && $denNgay) {
                    $query->whereHas('lichHocChiTiet', function($q) use ($tuNgay, $denNgay) {
                        $q->whereBetween('ngay_hoc', [$tuNgay, $denNgay]);
                    });
                }

                $data['items'] = $query->get();
                break;

            case 'hoc-phi':
                $query = HocPhiHocKy::with(['sinhVien', 'hocKy']);
                
                if ($hocKyId) {
                    $query->where('hoc_ky_id', $hocKyId);
                }

                $data['items'] = $query->get();
                break;

            case 'canh-bao':
                $query = CanhBaoHocVu::with(['sinhVien', 'hocKy']);
                
                if ($hocKyId) {
                    $query->where('hoc_ky_id', $hocKyId);
                }

                $data['items'] = $query->get();
                break;

            default:
                $data['items'] = [];
                break;
        }

        $pdf = Pdf::loadView('daotao.bao-cao.pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $fileName = 'bao_cao_' . $loaiBaoCao . '_' . now()->format('YmdHis') . '.pdf';
        
        return $pdf->download($fileName);
    }

    /**
     * Helper: Style header row for Excel
     */
    private function styleHeaderRow($sheet, $row, $startCol, $endCol)
    {
        $range = $startCol . $row . ':' . $endCol . $row;
        
        $sheet->getStyle($range)->getFont()->setBold(true);
        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($range)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($range)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    /**
     * Helper: Get text for điểm danh status
     */
    private function getTrangThaiDiemDanhText($trangThai)
    {
        $statusMap = [
            'co_mat' => 'Có mặt',
            'vang' => 'Vắng',
            'di_tre' => 'Đi trễ',
            'nghi_phep' => 'Nghỉ phép',
        ];

        return $statusMap[$trangThai] ?? $trangThai;
    }
}
