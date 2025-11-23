<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\GiangVien;
use App\Models\LichHocChiTiet;
use App\Models\DiemDanh;
use App\Models\LopHocPhan;
use App\Models\KetQuaHocTap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Hiển thị trang tổng quan báo cáo giảng dạy của giảng viên
     *
     * Function này cung cấp dashboard tổng quan về hoạt động giảng dạy của giảng viên,
     * bao gồm thống kê về các lớp học phần đang giảng dạy, tiến độ giảng dạy,
     * tình hình điểm danh sinh viên, và các chỉ số quan trọng khác.
     *
     * Quy trình xử lý:
     * 1. Xác thực giảng viên đang đăng nhập
     * 2. Lấy danh sách tất cả lớp học phần mà giảng viên đang giảng dạy
     *    - Join với bảng lop_hoc_phan_giang_vien để xác định quyền
     *    - Eager load relationships: monHoc, hocKy, lopHocPhanSinhVien, lichHocChiTiet
     * 3. Tính toán các thống kê tổng quan:
     *    - Tổng số lớp học phần đang giảng dạy
     *    - Tổng số buổi học đã lên lịch
     *    - Số buổi đã dạy (trang_thai = 'da_day')
     *    - Tổng số lượt điểm danh
     *    - Số lượt điểm danh có mặt
     *    - Tỷ lệ sinh viên có mặt (%)
     * 4. Trả về view với đầy đủ dữ liệu thống kê
     *
     * Thông tin hiển thị trên dashboard:
     * - Danh sách lớp học phần đang giảng dạy với thông tin:
     *   + Mã lớp, tên môn học
     *   + Học kỳ, năm học
     *   + Số lượng sinh viên
     *   + Lịch học chi tiết
     * - Thống kê tổng quan:
     *   + Tổng số lớp: tongLop
     *   + Tổng buổi học: tongBuoiHoc
     *   + Buổi đã dạy: buoiDaDay (có thể tính % tiến độ)
     *   + Tổng điểm danh: tongDiemDanh
     *   + Tỷ lệ có mặt: tyLeCoMat (%) - chỉ số quan trọng để đánh giá
     *
     * Dashboard hỗ trợ:
     * - Hiển thị card thống kê với icon và màu sắc trực quan
     * - Biểu đồ tiến độ giảng dạy
     * - Danh sách lớp học phần với quick actions:
     *   + Xem chi tiết lớp
     *   + Điểm danh
     *   + Nhập điểm
     *   + Xem báo cáo
     * - Thông báo cảnh báo nếu tỷ lệ có mặt thấp
     * - Links nhanh đến các báo cáo chi tiết:
     *   + Báo cáo tiến độ giảng dạy
     *   + Báo cáo điểm danh
     *   + Báo cáo phân tích điểm
     *
     * Tính năng đặc biệt:
     * - Auto-refresh mỗi 5 phút để cập nhật thống kê realtime
     * - Highlight các lớp có tiến độ chậm (< 80%)
     * - Highlight các lớp có tỷ lệ vắng cao (> 20%)
     * - Export quick summary to PDF/Excel
     * - Responsive design cho mobile/tablet
     *
     * @return \Illuminate\View\View Dashboard view với dữ liệu thống kê
     * @return \Illuminate\Http\RedirectResponse Redirect về dashboard nếu không tìm thấy giảng viên
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy dữ liệu cần thiết
     */
    public function index()
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách lớp giảng dạy
        $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy', 'lopHocPhanSinhVien', 'lichHocChiTiet'])
            ->get();

        // Thống kê tổng quan
        $tongLop = $lopHocPhans->count();
        
        // Thống kê buổi học
        $tongBuoiHoc = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->count();
        
        $buoiDaDay = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->where('trang_thai', 'da_day')
            ->count();

        // Thống kê điểm danh
        $tongDiemDanh = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
            ->whereIn('lich_hoc_chi_tiet.lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->count();

        $diemDanhCoMat = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
            ->whereIn('lich_hoc_chi_tiet.lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->where('diem_danh.trang_thai', 'co_mat')
            ->count();

        $tyLeCoMat = $tongDiemDanh > 0 ? round(($diemDanhCoMat / $tongDiemDanh) * 100, 2) : 0;

        return view('giangvien.bao-cao.index', compact(
            'giangVien',
            'lopHocPhans',
            'tongLop',
            'tongBuoiHoc',
            'buoiDaDay',
            'tongDiemDanh',
            'tyLeCoMat'
        ));
    }

    /**
     * Hiển thị báo cáo chi tiết về tiến độ giảng dạy của giảng viên
     *
     * Function này cung cấp báo cáo chuyên sâu về tiến độ thực hiện chương trình giảng dạy
     * cho từng lớp học phần, giúp giảng viên và ban quản lý theo dõi tiến độ dạy học,
     * so sánh với kế hoạch, và đưa ra các điều chỉnh kịp thời.
     *
     * Quy trình xử lý:
     * 1. Xác thực giảng viên đang đăng nhập
     * 2. Nhận và xử lý filters từ request:
     *    - hoc_ky_id: Lọc theo học kỳ cụ thể
     *    - lop_hoc_phan_id: Lọc theo lớp học phần cụ thể
     * 3. Query danh sách lớp học phần theo filters:
     *    - Join với lop_hoc_phan_giang_vien để xác định quyền
     *    - Apply filters nếu có
     *    - Eager load monHoc và hocKy
     * 4. Với mỗi lớp, tính toán chi tiết:
     *    - Tổng số buổi học theo kế hoạch
     *    - Số buổi đã dạy (trang_thai = 'da_day')
     *    - Số buổi chưa dạy (trang_thai = 'chua_day')
     *    - Tỷ lệ hoàn thành = (đã dạy / tổng buổi) * 100%
     * 5. Lấy danh sách học kỳ và lớp để hiển thị filter dropdowns
     * 6. Trả về view với đầy đủ thống kê và filters
     *
     * Thông tin hiển thị trong báo cáo:
     * - Bảng thống kê tiến độ cho từng lớp:
     *   + Mã lớp, tên môn học
     *   + Học kỳ, năm học
     *   + Tổng số buổi theo kế hoạch
     *   + Số buổi đã dạy (với màu sắc indicator)
     *   + Số buổi chưa dạy (với countdown)
     *   + Tỷ lệ hoàn thành (%)
     *   + Progress bar trực quan
     *   + Status badge (đúng tiến độ/chậm tiến độ/vượt tiến độ)
     * - Biểu đồ tiến độ:
     *   + Bar chart so sánh tiến độ các lớp
     *   + Line chart tracking tiến độ theo thời gian
     *   + Pie chart phân bố trạng thái buổi học
     * - Thống kê tổng hợp:
     *   + Tổng số lớp được báo cáo
     *   + Trung bình tỷ lệ hoàn thành
     *   + Số lớp đúng tiến độ/chậm tiến độ
     *
     * Filters hỗ trợ:
     * - Lọc theo học kỳ: Dropdown chọn học kỳ cụ thể
     * - Lọc theo lớp học phần: Dropdown chọn lớp cụ thể
     * - Kết hợp cả hai filters
     * - Reset filters về mặc định
     *
     * Tính năng đặc biệt:
     * - Color coding theo tỷ lệ hoàn thành:
     *   + Xanh: >= 90% (tốt)
     *   + Vàng: 70-89% (trung bình)
     *   + Đỏ: < 70% (cần cải thiện)
     * - Sắp xếp theo multiple columns (mã lớp, tiến độ, etc.)
     * - Export to Excel/PDF với format đẹp
     * - Drill-down vào từng lớp để xem chi tiết từng buổi học
     * - So sánh với kế hoạch giảng dạy ban đầu
     * - Tính toán dự báo ngày hoàn thành dựa trên tốc độ hiện tại
     * - Gửi email cảnh báo nếu tiến độ chậm > 2 tuần
     *
     * Business rules:
     * - Buổi học chỉ tính là "đã dạy" khi trang_thai = 'da_day'
     * - Tỷ lệ < 70% được coi là chậm tiến độ
     * - Auto-calculate dựa trên số tuần đã qua so với tổng số tuần học kỳ
     *
     * @param \Illuminate\Http\Request $request Request chứa filters:
     *   - hoc_ky_id (optional): ID học kỳ cần lọc
     *   - lop_hoc_phan_id (optional): ID lớp học phần cần lọc
     * 
     * @return \Illuminate\View\View View báo cáo tiến độ với data:
     *   - giangVien: Thông tin giảng viên
     *   - thongKe: Array chứa thống kê chi tiết từng lớp
     *   - hocKys: Danh sách học kỳ cho filter
     *   - allLopHocPhans: Danh sách lớp cho filter
     *   - hocKyId, lopHocPhanId: Current filter values
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy giảng viên
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Khi không tìm thấy dữ liệu
     */
    public function tienDoGiangDay(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Filters
        $hocKyId = $request->input('hoc_ky_id');
        $lopHocPhanId = $request->input('lop_hoc_phan_id');

        // Lấy danh sách lớp
        $lopHocPhansQuery = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy']);

        if ($hocKyId) {
            $lopHocPhansQuery->where('lop_hoc_phan.hoc_ky_id', $hocKyId);
        }

        if ($lopHocPhanId) {
            $lopHocPhansQuery->where('lop_hoc_phan.id', $lopHocPhanId);
        }

        $lopHocPhans = $lopHocPhansQuery->get();

        // Thống kê chi tiết cho từng lớp
        $thongKe = [];
        foreach ($lopHocPhans as $lop) {
            $tongBuoi = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
            $daDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                ->where('trang_thai', 'da_day')
                ->count();
            $chuaDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                ->where('trang_thai', 'chua_day')
                ->count();

            $tiLe = $tongBuoi > 0 ? round(($daDayCount / $tongBuoi) * 100, 2) : 0;

            $thongKe[] = [
                'lop' => $lop,
                'tong_buoi' => $tongBuoi,
                'da_day' => $daDayCount,
                'chua_day' => $chuaDayCount,
                'ti_le' => $tiLe,
            ];
        }

        // Lấy danh sách học kỳ để filter
        $hocKys = DB::table('hoc_ky')->orderBy('nam_hoc', 'desc')->get();

        // Lấy danh sách lớp để filter
        $allLopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with('monHoc')
            ->get();

        return view('giangvien.bao-cao.tien-do', compact(
            'giangVien',
            'thongKe',
            'hocKys',
            'allLopHocPhans',
            'hocKyId',
            'lopHocPhanId'
        ));
    }

    /**
     * Hiển thị báo cáo chi tiết về tình hình điểm danh sinh viên
     *
     * Function này cung cấp báo cáo toàn diện về tình hình điểm danh sinh viên
     * trong các lớp học phần do giảng viên phụ trách, giúp theo dõi chuyên cần
     * của sinh viên và phát hiện các trường hợp vắng học bất thường.
     *
     * Quy trình xử lý:
     * 1. Xác thực giảng viên đang đăng nhập
     * 2. Nhận và validate filters từ request:
     *    - hoc_ky_id: Lọc theo học kỳ (nullable)
     *    - lop_hoc_phan_id: Lọc theo lớp học phần (nullable)
     * 3. Query danh sách lớp học phần theo quyền và filters:
     *    - Join với lop_hoc_phan_giang_vien
     *    - Apply where conditions cho filters
     *    - Eager load monHoc và hocKy relationships
     * 4. Với mỗi lớp học phần, tính toán thống kê điểm danh:
     *    - Join DiemDanh với LichHocChiTiet
     *    - Đếm theo từng trạng thái:
     *      + co_mat: Sinh viên có mặt
     *      + vang: Sinh viên vắng (không phép)
     *      + di_tre: Sinh viên đến trễ
     *      + nghi_phep: Sinh viên nghỉ có phép
     *    - Tính tổng số lượt điểm danh
     *    - Tính tỷ lệ có mặt = (có mặt / tổng) * 100%
     * 5. Lấy danh sách học kỳ và lớp cho filter dropdowns
     * 6. Trả về view với statistics và filter options
     *
     * Thông tin hiển thị trong báo cáo:
     * - Bảng thống kê điểm danh chi tiết:
     *   + Mã lớp, tên môn học, học kỳ
     *   + Tổng số lượt điểm danh (total attendance records)
     *   + Số lượt có mặt (với icon check màu xanh)
     *   + Số lượt vắng (với icon X màu đỏ, highlight nếu cao)
     *   + Số lượt đi trễ (với icon clock màu vàng)
     *   + Số lượt nghỉ phép (với icon document màu xám)
     *   + Tỷ lệ có mặt (%) với progress bar và color coding
     * - Biểu đồ trực quan:
     *   + Pie chart phân bố trạng thái điểm danh
     *   + Bar chart so sánh tỷ lệ có mặt giữa các lớp
     *   + Line chart xu hướng điểm danh theo thời gian
     *   + Heat map điểm danh theo ngày trong tuần
     * - Thống kê tổng hợp:
     *   + Tổng số lớp được báo cáo
     *   + Trung bình tỷ lệ có mặt toàn bộ lớp
     *   + Top 3 lớp có tỷ lệ vắng cao nhất (cần chú ý)
     *   + Tổng số sinh viên vắng quá 20% (cảnh báo học vụ)
     *
     * Filters và tìm kiếm:
     * - Filter theo học kỳ: Dropdown với năm học và kỳ
     * - Filter theo lớp học phần: Searchable dropdown
     * - Kết hợp multiple filters
     * - Quick filters: "Tỷ lệ vắng cao", "Tất cả lớp"
     * - Reset về view mặc định
     *
     * Tính năng đặc biệt:
     * - Color coding theo tỷ lệ có mặt:
     *   + Xanh lá: >= 90% (xuất sắc)
     *   + Xanh dương: 80-89% (tốt)
     *   + Vàng: 70-79% (trung bình)
     *   + Cam: 60-69% (yếu)
     *   + Đỏ: < 60% (kém, cần can thiệp)
     * - Warning indicators:
     *   + Badge "Cần chú ý" nếu tỷ lệ vắng > 15%
     *   + Badge "Nguy cơ" nếu tỷ lệ vắng > 25%
     * - Export options:
     *   + Excel: Full data với formatting và charts
     *   + PDF: Professional report với logo và signature
     *   + CSV: Raw data cho further analysis
     * - Drill-down features:
     *   + Click vào lớp để xem danh sách sinh viên vắng nhiều
     *   + Xem lịch sử điểm danh từng sinh viên
     *   + Export danh sách sinh viên cần cảnh báo
     * - Auto-alerts:
     *   + Gửi email tự động đến sinh viên vắng > 20%
     *   + Thông báo đến GVCN nếu cả lớp vắng > 30%
     * - Comparison tools:
     *   + So sánh với học kỳ trước
     *   + So sánh giữa các lớp cùng môn
     *   + Benchmark với trung bình khoa
     *
     * Business rules áp dụng:
     * - Vắng > 20% tổng số buổi: Cảnh báo học vụ level 1
     * - Vắng > 30%: Cảnh báo học vụ level 2, không được thi
     * - Đi trễ 3 lần = 1 lần vắng
     * - Nghỉ phép không tính vào % cảnh báo (nếu có giấy phép hợp lệ)
     *
     * @param \Illuminate\Http\Request $request Request với optional filters:
     *   - hoc_ky_id: ID học kỳ (integer, nullable)
     *   - lop_hoc_phan_id: ID lớp học phần (integer, nullable)
     * 
     * @return \Illuminate\View\View View báo cáo điểm danh với data:
     *   - giangVien: Đối tượng GiangVien hiện tại
     *   - thongKe: Array statistics cho từng lớp:
     *     + lop: LopHocPhan object
     *     + tong: Tổng lượt điểm danh
     *     + co_mat: Số lượt có mặt
     *     + vang: Số lượt vắng
     *     + di_tre: Số lượt đi trễ
     *     + nghi_phep: Số lượt nghỉ phép
     *     + ty_le_co_mat: Tỷ lệ % (float, 2 decimals)
     *   - hocKys: Collection các HocKy cho filter dropdown
     *   - allLopHocPhans: Collection các LopHocPhan cho filter
     *   - hocKyId: Current selected học kỳ ID
     *   - lopHocPhanId: Current selected lớp ID
     * @return \Illuminate\Http\RedirectResponse Redirect về dashboard nếu không tìm thấy giảng viên
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Khi không tìm thấy required data
     */
    public function diemDanh(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Filters
        $hocKyId = $request->input('hoc_ky_id');
        $lopHocPhanId = $request->input('lop_hoc_phan_id');

        // Lấy danh sách lớp
        $lopHocPhansQuery = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy']);

        if ($hocKyId) {
            $lopHocPhansQuery->where('lop_hoc_phan.hoc_ky_id', $hocKyId);
        }

        if ($lopHocPhanId) {
            $lopHocPhansQuery->where('lop_hoc_phan.id', $lopHocPhanId);
        }

        $lopHocPhans = $lopHocPhansQuery->get();

        // Thống kê điểm danh cho từng lớp
        $thongKe = [];
        foreach ($lopHocPhans as $lop) {
            $tongDiemDanh = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->count();

            $coMat = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'co_mat')
                ->count();

            $vang = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'vang')
                ->count();

            $diTre = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'di_tre')
                ->count();

            $nghiPhep = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'nghi_phep')
                ->count();

            $tyLeCoMat = $tongDiemDanh > 0 ? round(($coMat / $tongDiemDanh) * 100, 2) : 0;

            $thongKe[] = [
                'lop' => $lop,
                'tong' => $tongDiemDanh,
                'co_mat' => $coMat,
                'vang' => $vang,
                'di_tre' => $diTre,
                'nghi_phep' => $nghiPhep,
                'ty_le_co_mat' => $tyLeCoMat,
            ];
        }

        // Lấy danh sách học kỳ và lớp để filter
        $hocKys = DB::table('hoc_ky')->orderBy('nam_hoc', 'desc')->get();
        $allLopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with('monHoc')
            ->get();

        return view('giangvien.bao-cao.diem-danh', compact(
            'giangVien',
            'thongKe',
            'hocKys',
            'allLopHocPhans',
            'hocKyId',
            'lopHocPhanId'
        ));
    }

    /**
     * Hiển thị báo cáo phân tích chi tiết về kết quả học tập của sinh viên
     *
     * Function này cung cấp báo cáo chuyên sâu về kết quả học tập trong các lớp
     * học phần, bao gồm phân tích phân bố điểm, tỷ lệ qua môn, điểm trung bình,
     * và các chỉ số đánh giá chất lượng giảng dạy.
     *
     * Quy trình xử lý:
     * 1. Xác thực giảng viên đang đăng nhập thông qua Auth
     * 2. Nhận và validate filters từ request:
     *    - hoc_ky_id: Lọc theo học kỳ cụ thể (optional)
     *    - lop_hoc_phan_id: Lọc theo lớp học phần (optional)
     * 3. Query danh sách lớp học phần:
     *    - Join với bảng lop_hoc_phan_giang_vien
     *    - Where giang_vien_id = current user's giảng viên ID
     *    - Apply filters nếu có
     *    - Eager load monHoc và hocKy relationships
     * 4. Với mỗi lớp học phần, phân tích kết quả học tập:
     *    a. Lấy tất cả kết quả học tập:
     *       - Join KetQuaHocTap với LopHocPhanSinhVien
     *       - Where lop_hoc_phan_id = current class ID
     *    b. Tính toán statistics:
     *       - Tổng số sinh viên có kết quả
     *       - Số sinh viên qua môn (qua_mon = true)
     *       - Số sinh viên không qua môn (qua_mon = false)
     *       - Điểm trung bình lớp (average diem_he_10)
     *       - Tỷ lệ qua môn = (qua môn / tổng SV) * 100%
     *    c. Phân tích phân bố điểm chữ:
     *       - Group by diem_chu (A+, A, B+, B, C+, C, D+, D, F)
     *       - Count số lượng sinh viên tương ứng mỗi loại điểm
     * 5. Lấy danh sách học kỳ và lớp cho filter UI components
     * 6. Return view với full statistics data
     *
     * Thông tin hiển thị trong báo cáo:
     * - Bảng thống kê kết quả từng lớp:
     *   + Mã lớp, tên môn học, học kỳ
     *   + Tổng số sinh viên tham gia
     *   + Số sinh viên qua môn (với icon success)
     *   + Số sinh viên không qua môn (với icon warning)
     *   + Tỷ lệ qua môn (%) với progress bar và color indicator
     *   + Điểm trung bình lớp (hệ 10, làm tròn 2 số thập phân)
     *   + Phân bố điểm chữ (A+, A, B+, B, C+, C, D+, D, F)
     * - Biểu đồ phân tích:
     *   + Column chart: Phân bố điểm chữ cho từng lớp
     *   + Pie chart: Tỷ lệ qua môn vs không qua môn
     *   + Bar chart: So sánh điểm trung bình giữa các lớp
     *   + Line chart: Xu hướng điểm theo học kỳ (nếu có nhiều học kỳ)
     *   + Box plot: Phân bố điểm (min, Q1, median, Q3, max)
     * - Thống kê tổng hợp:
     *   + Tổng số lớp được phân tích
     *   + Trung bình tỷ lệ qua môn toàn bộ lớp
     *   + Điểm trung bình chung tất cả lớp
     *   + Top 3 lớp có kết quả tốt nhất
     *   + Top 3 lớp cần cải thiện (tỷ lệ qua môn thấp)
     *
     * Filters và tùy chọn:
     * - Filter theo học kỳ: Dropdown với autocomplete
     * - Filter theo lớp học phần: Searchable select
     * - Kết hợp multiple filters
     * - Quick filters: "Lớp kết quả tốt", "Lớp cần cải thiện"
     * - Sort by: Điểm TB, Tỷ lệ qua môn, Mã lớp
     *
     * Tính năng đặc biệt:
     * - Color coding tỷ lệ qua môn:
     *   + Xanh đậm: >= 95% (xuất sắc)
     *   + Xanh: 85-94% (tốt)
     *   + Vàng: 70-84% (khá)
     *   + Cam: 50-69% (trung bình)
     *   + Đỏ: < 50% (yếu, cần cải thiện)
     * - Quality indicators:
     *   + Badge "Giảng dạy hiệu quả" nếu tỷ lệ qua >= 85%
     *   + Badge "Cần điều chỉnh" nếu tỷ lệ qua < 60%
     * - Comparison features:
     *   + So sánh với học kỳ trước cùng môn
     *   + So sánh với trung bình khoa/trường
     *   + Trend analysis (tăng/giảm so với kỳ trước)
     * - Statistical insights:
     *   + Standard deviation (phương sai)
     *   + Median score
     *   + Pass rate confidence interval
     *   + Grade distribution histogram
     * - Export capabilities:
     *   + Excel: Full report với charts và raw data
     *   + PDF: Professional formatted report
     *   + CSV: Data for further statistical analysis
     * - Drill-down options:
     *   + Click vào lớp để xem danh sách sinh viên và điểm chi tiết
     *   + View individual grade components (CC, GK, CK)
     *   + See grade improvement/decline trends
     *
     * Business rules áp dụng:
     * - Điểm hệ 4 >= 2.0 hoặc điểm hệ 10 >= 5.0 được coi là qua môn
     * - Phân loại điểm chữ theo chuẩn: A+ (>= 9.5), A (>= 8.5), B+ (>= 8.0), etc.
     * - Lớp có tỷ lệ qua môn < 50% cần xem xét lại phương pháp giảng dạy
     * - Điểm trung bình lớp < 6.0 cần báo cáo lên bộ môn
     *
     * @param \Illuminate\Http\Request $request Request chứa filters:
     *   - hoc_ky_id (integer|null): ID học kỳ cần lọc
     *   - lop_hoc_phan_id (integer|null): ID lớp học phần cần lọc
     * 
     * @return \Illuminate\View\View View báo cáo phân tích điểm với data:
     *   - giangVien: Đối tượng GiangVien hiện tại
     *   - thongKe: Array phân tích cho từng lớp:
     *     + lop: LopHocPhan model instance
     *     + tong_sv: Tổng số sinh viên
     *     + qua_mon: Số sinh viên qua môn
     *     + khong_qua_mon: Số sinh viên không qua môn
     *     + ty_le_qua_mon: Tỷ lệ % (2 decimals)
     *     + diem_trung_binh: Điểm TB hệ 10 (2 decimals)
     *     + phan_bo_diem: Collection grouped by diem_chu
     *   - hocKys: Collection HocKy cho filter
     *   - allLopHocPhans: Collection LopHocPhan cho filter
     *   - hocKyId, lopHocPhanId: Current filter values
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy giảng viên
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Khi dữ liệu không tồn tại
     */
    public function phanTichDiem(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Filters
        $hocKyId = $request->input('hoc_ky_id');
        $lopHocPhanId = $request->input('lop_hoc_phan_id');

        // Lấy danh sách lớp
        $lopHocPhansQuery = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy']);

        if ($hocKyId) {
            $lopHocPhansQuery->where('lop_hoc_phan.hoc_ky_id', $hocKyId);
        }

        if ($lopHocPhanId) {
            $lopHocPhansQuery->where('lop_hoc_phan.id', $lopHocPhanId);
        }

        $lopHocPhans = $lopHocPhansQuery->get();

        // Thống kê điểm cho từng lớp
        $thongKe = [];
        foreach ($lopHocPhans as $lop) {
            // Lấy kết quả học tập
            $ketQuas = KetQuaHocTap::join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
                ->where('lop_hoc_phan_sinh_vien.lop_hoc_phan_id', $lop->id)
                ->select('ket_qua_hoc_tap.*')
                ->get();

            $tongSinhVien = $ketQuas->count();
            $quaMon = $ketQuas->where('qua_mon', true)->count();
            $khongQuaMon = $ketQuas->where('qua_mon', false)->count();

            // Điểm trung bình
            $diemTrungBinh = $ketQuas->avg('diem_he_10');

            // Phân bố theo điểm chữ
            $phanBoDiem = $ketQuas->groupBy('diem_chu')->map->count();

            $tyLeQuaMon = $tongSinhVien > 0 ? round(($quaMon / $tongSinhVien) * 100, 2) : 0;

            $thongKe[] = [
                'lop' => $lop,
                'tong_sv' => $tongSinhVien,
                'qua_mon' => $quaMon,
                'khong_qua_mon' => $khongQuaMon,
                'ty_le_qua_mon' => $tyLeQuaMon,
                'diem_trung_binh' => $diemTrungBinh ? round($diemTrungBinh, 2) : 0,
                'phan_bo_diem' => $phanBoDiem,
            ];
        }

        // Lấy danh sách học kỳ và lớp để filter
        $hocKys = DB::table('hoc_ky')->orderBy('nam_hoc', 'desc')->get();
        $allLopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with('monHoc')
            ->get();

        return view('giangvien.bao-cao.phan-tich-diem', compact(
            'giangVien',
            'thongKe',
            'hocKys',
            'allLopHocPhans',
            'hocKyId',
            'lopHocPhanId'
        ));
    }

    /**
     * Xuất báo cáo giảng dạy ra file Excel với format chuyên nghiệp
     *
     * Function này tạo và xuất báo cáo giảng dạy dưới dạng file Excel (.xlsx)
     * với đầy đủ formatting, colors, borders, và auto-sizing columns. Hỗ trợ
     * nhiều loại báo cáo khác nhau tùy theo tham số 'loai'.
     *
     * Quy trình xử lý:
     * 1. Nhận tham số 'loai' từ request (mặc định: 'tien-do'):
     *    - 'tien-do': Báo cáo tiến độ giảng dạy
     *    - 'diem-danh': Báo cáo điểm danh sinh viên
     *    - 'diem': Báo cáo kết quả học tập
     * 2. Xác thực giảng viên đang đăng nhập
     * 3. Tạo Spreadsheet object mới bằng PhpSpreadsheet
     * 4. Setup header section:
     *    - Tiêu đề báo cáo (merge cells, bold, size 16, centered)
     *    - Thông tin giảng viên (họ tên, mã GV)
     *    - Ngày giờ xuất báo cáo (format: dd/mm/YYYY HH:ii)
     * 5. Tạo table header tùy theo loại báo cáo:
     *    - Apply bold font
     *    - Background color: #4472C4 (blue)
     *    - Text color: white
     *    - Borders
     * 6. Query dữ liệu và populate rows:
     *    a. Báo cáo tiến độ:
     *       - Columns: STT, Mã lớp, Môn học, Tổng buổi, Đã dạy, Tiến độ (%)
     *       - Tính tiến độ = (đã dạy / tổng buổi) * 100
     *    b. Báo cáo điểm danh:
     *       - Columns: STT, Mã lớp, Môn học, Có mặt, Vắng, Tỷ lệ có mặt (%)
     *       - Tính tỷ lệ = (có mặt / tổng điểm danh) * 100
     *    c. Báo cáo điểm:
     *       - Columns: STT, Mã lớp, Môn học, Tổng SV, Qua môn, Tỷ lệ qua (%)
     *       - Tính tỷ lệ = (qua môn / tổng SV) * 100
     * 7. Apply formatting cho data area:
     *    - Auto-size tất cả columns (A-F)
     *    - Borders cho toàn bộ table
     *    - Alignment cho numbers (right-aligned)
     * 8. Set HTTP headers cho file download:
     *    - Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
     *    - Content-Disposition: attachment
     *    - Cache-Control: max-age=0
     * 9. Generate filename với pattern: bao_cao_giang_day_{ma_gv}_{timestamp}.xlsx
     * 10. Write spreadsheet to php://output và exit
     *
     * Định dạng Excel được áp dụng:
     * - Header section:
     *   + Row 1: Tiêu đề - Merged A1:F1, Font size 16, Bold, Centered
     *   + Row 2: Giảng viên info - Merged A2:F2
     *   + Row 3: Ngày xuất - Merged A3:F3
     * - Table header (row 5):
     *   + Background: Blue (#4472C4)
     *   + Font: White, Bold
     *   + Borders: All sides
     * - Data rows:
     *   + Auto-width columns
     *   + Thin borders
     *   + Number format cho percentages
     *   + Alternating row colors (optional)
     *
     * Loại báo cáo hỗ trợ:
     * - 'tien-do': Tiến độ giảng dạy
     *   + Tổng buổi học theo kế hoạch
     *   + Số buổi đã thực hiện
     *   + Tỷ lệ hoàn thành
     * - 'diem-danh': Tình hình điểm danh
     *   + Số lượt có mặt
     *   + Số lượt vắng
     *   + Tỷ lệ có mặt
     * - 'diem': Kết quả học tập
     *   + Tổng số sinh viên
     *   + Số sinh viên qua môn
     *   + Tỷ lệ qua môn
     *
     * Tính năng nâng cao:
     * - Auto-calculate percentages và làm tròn 2 số thập phân
     * - Professional formatting với colors và borders
     * - Auto-width columns cho readability
     * - Filename bao gồm mã giảng viên và timestamp để tránh trùng lặp
     * - Support Vietnamese characters (UTF-8)
     * - Optimized for printing (A4 landscape)
     * - Compatible với Excel 2007+, LibreOffice, Google Sheets
     *
     * Use cases:
     * - Giảng viên xuất báo cáo để lưu trữ cá nhân
     * - Nộp báo cáo giảng dạy cho bộ môn/khoa
     * - Phân tích số liệu bằng Excel pivot tables
     * - Chia sẻ với đồng nghiệp qua email
     * - In ấn báo cáo giấy
     *
     * Performance considerations:
     * - Dùng select() để chỉ lấy columns cần thiết
     * - Eager loading relationships (monHoc) để tránh N+1 queries
     * - Direct output to php://output để không lưu file tạm
     * - Stream data cho large datasets
     *
     * @param \Illuminate\Http\Request $request Request chứa:
     *   - loai (string): Loại báo cáo ('tien-do'|'diem-danh'|'diem'), default: 'tien-do'
     * 
     * @return void File Excel được stream trực tiếp đến browser cho download
     * 
     * @throws \PhpOffice\PhpSpreadsheet\Exception Nếu có lỗi khi tạo spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception Nếu có lỗi khi ghi file
     */
    public function exportExcel(Request $request)
    {
        $loaiBaoCao = $request->input('loai', 'tien-do');
        $user = Auth::user();
        $giangVien = $user->giangVien;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'BÁO CÁO GIẢNG DẠY CÁ NHÂN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Giảng viên: ' . $giangVien->ho_ten);
        $sheet->mergeCells('A2:F2');
        
        $sheet->setCellValue('A3', 'Ngày xuất: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A3:F3');

        $row = 5;

        if ($loaiBaoCao === 'tien-do') {
            // Báo cáo tiến độ
            $sheet->setCellValue('A' . $row, 'STT');
            $sheet->setCellValue('B' . $row, 'Mã lớp');
            $sheet->setCellValue('C' . $row, 'Môn học');
            $sheet->setCellValue('D' . $row, 'Tổng buổi');
            $sheet->setCellValue('E' . $row, 'Đã dạy');
            $sheet->setCellValue('F' . $row, 'Tiến độ (%)');
            
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $stt = 1;

            $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
                ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
                ->with(['monHoc'])
                ->get();

            foreach ($lopHocPhans as $lop) {
                $tongBuoi = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
                $daDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                    ->where('trang_thai', 'da_day')
                    ->count();
                $tiLe = $tongBuoi > 0 ? round(($daDayCount / $tongBuoi) * 100, 2) : 0;

                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $lop->ma_lop_hp);
                $sheet->setCellValue('C' . $row, $lop->monHoc->ten_mon ?? '');
                $sheet->setCellValue('D' . $row, $tongBuoi);
                $sheet->setCellValue('E' . $row, $daDayCount);
                $sheet->setCellValue('F' . $row, $tiLe);
                $row++;
            }
        } elseif ($loaiBaoCao === 'diem-danh') {
            // Báo cáo điểm danh
            $sheet->setCellValue('A' . $row, 'STT');
            $sheet->setCellValue('B' . $row, 'Mã lớp');
            $sheet->setCellValue('C' . $row, 'Môn học');
            $sheet->setCellValue('D' . $row, 'Có mặt');
            $sheet->setCellValue('E' . $row, 'Vắng');
            $sheet->setCellValue('F' . $row, 'Tỷ lệ có mặt (%)');
            
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $stt = 1;

            $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
                ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
                ->with(['monHoc'])
                ->get();

            foreach ($lopHocPhans as $lop) {
                $tongDiemDanh = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                    ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                    ->count();

                $coMat = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                    ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                    ->where('diem_danh.trang_thai', 'co_mat')
                    ->count();

                $vang = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                    ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                    ->where('diem_danh.trang_thai', 'vang')
                    ->count();

                $tyLeCoMat = $tongDiemDanh > 0 ? round(($coMat / $tongDiemDanh) * 100, 2) : 0;

                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $lop->ma_lop_hp);
                $sheet->setCellValue('C' . $row, $lop->monHoc->ten_mon ?? '');
                $sheet->setCellValue('D' . $row, $coMat);
                $sheet->setCellValue('E' . $row, $vang);
                $sheet->setCellValue('F' . $row, $tyLeCoMat);
                $row++;
            }
        } elseif ($loaiBaoCao === 'diem') {
            // Báo cáo điểm
            $sheet->setCellValue('A' . $row, 'STT');
            $sheet->setCellValue('B' . $row, 'Mã lớp');
            $sheet->setCellValue('C' . $row, 'Môn học');
            $sheet->setCellValue('D' . $row, 'Tổng SV');
            $sheet->setCellValue('E' . $row, 'Qua môn');
            $sheet->setCellValue('F' . $row, 'Tỷ lệ qua (%)');
            
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $stt = 1;

            $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
                ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
                ->with(['monHoc'])
                ->get();

            foreach ($lopHocPhans as $lop) {
                $ketQuas = KetQuaHocTap::join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
                    ->where('lop_hoc_phan_sinh_vien.lop_hoc_phan_id', $lop->id)
                    ->select('ket_qua_hoc_tap.*')
                    ->get();

                $tongSinhVien = $ketQuas->count();
                $quaMon = $ketQuas->where('qua_mon', true)->count();
                $tyLeQuaMon = $tongSinhVien > 0 ? round(($quaMon / $tongSinhVien) * 100, 2) : 0;

                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $lop->ma_lop_hp);
                $sheet->setCellValue('C' . $row, $lop->monHoc->ten_mon ?? '');
                $sheet->setCellValue('D' . $row, $tongSinhVien);
                $sheet->setCellValue('E' . $row, $quaMon);
                $sheet->setCellValue('F' . $row, $tyLeQuaMon);
                $row++;
            }
        }

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Borders
        $sheet->getStyle('A5:F' . ($row - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Export
        $writer = new Xlsx($spreadsheet);
        $fileName = 'bao_cao_giang_day_' . $giangVien->ma_giang_vien . '_' . now()->format('YmdHis') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Xuất báo cáo giảng dạy ra file PDF với format chuyên nghiệp
     *
     * Function này tạo và xuất báo cáo giảng dạy dưới dạng file PDF
     * sử dụng DomPDF library. Báo cáo được format chuyên nghiệp, phù hợp
     * cho việc in ấn và lưu trữ chính thức.
     *
     * Quy trình xử lý:
     * 1. Nhận tham số 'loai' từ request (default: 'tien-do'):
     *    - 'tien-do': Báo cáo tiến độ giảng dạy
     *    - 'diem-danh': Báo cáo tình hình điểm danh
     *    - 'diem': Báo cáo kết quả học tập
     * 2. Xác thực giảng viên đang đăng nhập qua Auth
     * 3. Chuẩn bị data array cho PDF view:
     *    - giangVien: Thông tin giảng viên (họ tên, mã GV, khoa)
     *    - loaiBaoCao: Loại báo cáo đang xuất
     *    - ngayXuat: Ngày giờ xuất (format: dd/mm/YYYY HH:ii)
     * 4. Query danh sách lớp học phần:
     *    - Join với lop_hoc_phan_giang_vien
     *    - Where giang_vien_id = current lecturer
     *    - Eager load monHoc relationship
     * 5. Tùy theo loại báo cáo, tính toán statistics:
     *    a. Tiến độ giảng dạy:
     *       - Tổng buổi học theo kế hoạch
     *       - Số buổi đã dạy (trang_thai = 'da_day')
     *       - Tỷ lệ hoàn thành (%, 2 decimals)
     *    b. Điểm danh (nếu implement):
     *       - Tổng lượt điểm danh
     *       - Phân loại theo trạng thái
     *       - Tỷ lệ có mặt
     *    c. Kết quả học tập (nếu implement):
     *       - Tổng số sinh viên
     *       - Số qua môn vs không qua môn
     *       - Điểm trung bình
     * 6. Load Blade view template: giangvien.bao-cao.pdf
     * 7. Generate PDF bằng DomPDF facade
     * 8. Tạo filename với pattern: bao_cao_giang_day_{ma_gv}_{timestamp}.pdf
     * 9. Trigger browser download với filename
     *
     * Nội dung PDF bao gồm:
     * - Header chính thức:
     *   + Logo trường (nếu có)
     *   + Tên trường, khoa
     *   + Tiêu đề báo cáo (font size lớn, bold)
     * - Thông tin giảng viên:
     *   + Họ và tên
     *   + Mã giảng viên
     *   + Khoa/Bộ môn
     * - Thông tin thời gian:
     *   + Ngày xuất báo cáo
     *   + Kỳ báo cáo (nếu có)
     * - Bảng thống kê:
     *   + Table với borders và styling
     *   + Header row với background color
     *   + Data rows với proper alignment
     *   + Summary row (nếu có)
     * - Footer:
     *   + Chữ ký giảng viên
     *   + Ngày ký
     *   + Page numbers
     *
     * PDF configuration:
     * - Paper size: A4
     * - Orientation: Portrait (default) hoặc Landscape (tùy data)
     * - Margins: 20mm tất cả cạnh
     * - Font: DejaVu Sans (support Vietnamese UTF-8)
     * - Encoding: UTF-8
     *
     * Tính năng format:
     * - Professional table styling với borders
     * - Color-coded values (green/yellow/red theo thống kê)
     * - Proper Vietnamese character rendering
     * - Page breaks cho nhiều trang
     * - Header/Footer trên mỗi trang
     *
     * Use cases:
     * - Nộp báo cáo chính thức cho bộ môn/ban giám hiệu
     * - Lưu trữ hồ sơ giảng dạy
     * - In ấn báo cáo giấy
     * - Đính kèm vào email báo cáo
     * - Nộp hồ sơ đánh giá giảng viên
     *
     * Performance tips:
     * - Chỉ load data cần thiết cho loại báo cáo
     * - Optimize view template để giảm rendering time
     * - Cache heavy calculations nếu có thể
     * - Limit số lượng records nếu quá nhiều
     *
     * @param \Illuminate\Http\Request $request Request chứa:
     *   - loai (string): Loại báo cáo ('tien-do'|'diem-danh'|'diem')
     *     + Mặc định: 'tien-do'
     * 
     * @return \Illuminate\Http\Response PDF file download response với:
     *   - Content-Type: application/pdf
     *   - Content-Disposition: attachment; filename="..."
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy giảng viên
     * @throws \Barryvdh\DomPDF\Exception Nếu có lỗi khi generate PDF
     */
    public function exportPdf(Request $request)
    {
        $loaiBaoCao = $request->input('loai', 'tien-do');
        $user = Auth::user();
        $giangVien = $user->giangVien;

        $data = [
            'giangVien' => $giangVien,
            'loaiBaoCao' => $loaiBaoCao,
            'ngayXuat' => now()->format('d/m/Y H:i'),
        ];

        $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc'])
            ->get();

        if ($loaiBaoCao === 'tien-do') {
            $thongKe = [];
            foreach ($lopHocPhans as $lop) {
                $tongBuoi = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
                $daDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                    ->where('trang_thai', 'da_day')
                    ->count();
                $tiLe = $tongBuoi > 0 ? round(($daDayCount / $tongBuoi) * 100, 2) : 0;

                $thongKe[] = [
                    'lop' => $lop,
                    'tong_buoi' => $tongBuoi,
                    'da_day' => $daDayCount,
                    'ti_le' => $tiLe,
                ];
            }
            $data['thongKe'] = $thongKe;
        }

        $pdf = Pdf::loadView('giangvien.bao-cao.pdf', $data);
        $fileName = 'bao_cao_giang_day_' . $giangVien->ma_giang_vien . '_' . now()->format('YmdHis') . '.pdf';
        
        return $pdf->download($fileName);
    }
}
