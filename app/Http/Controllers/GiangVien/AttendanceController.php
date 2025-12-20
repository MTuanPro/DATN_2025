<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiemDanh;
use App\Models\LichHocChiTiet;
use App\Models\LopHocPhanSinhVien;
use App\Models\PhanCongGiangDay;
use App\Models\DaoTao\SinhVien;
use App\Models\GiangVien;
use App\Models\CanhBaoHocVu;
use App\Mail\CanhBaoDiemDanhMail;
use App\Mail\BaoCaoSinhVienYeuMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Hiển thị danh sách các buổi học cần điểm danh của giảng viên
     *
     * Chức năng quản lý điểm danh sinh viên cho giảng viên, bao gồm:
     *
     * Quy trình hiển thị:
     * 1. Kiểm tra quyền giảng viên (giangVien relation từ user)
     * 2. Lấy tất cả lớp học phần được phân công (từ PhanCongGiangDay)
     * 3. Lấy danh sách buổi học (LichHocChiTiet) của các lớp đó
     * 4. Sắp xếp theo ngày học giảm dần (mới nhất lên đầu)
     *
     * Các bộ lọc hỗ trợ:
     * - Theo lớp học phần (lop_hoc_phan_id) - dropdown select
     * - Theo trạng thái điểm danh (trang_thai):
     *   + 'chua_diem_danh': Chưa điểm danh (màu vàng)
     *   + 'dang_diem_danh': Đang điểm danh (màu xanh)
     *   + 'da_diem_danh': Đã hoàn tất điểm danh (màu xám)
     * - Theo ngày học (tu_ngay, den_ngay) - date range picker
     * - Tìm kiếm theo tên môn học (search)
     *
     * Thông tin hiển thị cho mỗi buổi học:
     * - Ngày học, giờ học (ca_hoc)
     * - Tên môn học, mã lớp học phần
     * - Phòng học
     * - Trạng thái điểm danh với icon và màu sắc
     * - Thống kê nhanh:
     *   + Tổng số sinh viên lớp
     *   + Số sinh viên đã điểm danh / chưa điểm danh
     *   + Tỷ lệ % hoàn thành điểm danh
     * - Nút hành động: 'Điểm danh', 'Xem chi tiết', 'Sửa'
     *
     * Tính năng đặc biệt:
     * - Highlight buổi học hôm nay (ngay_hoc = today)
     * - Cảnh báo buổi học đã qua chưa điểm danh (màu đỏ)
     * - Hiển thị số buổi chưa điểm danh trong badge
     * - Quick action: Điểm danh nhanh cho tất cả 'Có mặt' (1 click)
     * - Phân trang 20 buổi học/trang
     *
     * @param Request $request Chứa các filter: lop_hoc_phan_id, trang_thai, tu_ngay, den_ngay, search
     * @return \Illuminate\View\View Danh sách buổi học với thống kê điểm danh
     * @return \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền giảng viên
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        // Lấy các lớp được phân công (không lọc theo khoa để hiển thị tất cả lớp được phân công)
        $lopHocPhanIds = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->pluck('lop_hoc_phan_id')
            ->toArray();

        // Query buổi học
        $query = LichHocChiTiet::with(['lopHocPhan.monHoc', 'phongHoc', 'caHoc'])
            ->whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->orderBy('ngay_hoc', 'desc');

        // Bộ lọc
        if ($request->filled('lop_hoc_phan_id')) {
            $query->where('lop_hoc_phan_id', $request->lop_hoc_phan_id);
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngay_hoc', '>=', $request->tu_ngay);
        }

        if ($request->filled('den_ngay')) {
            $query->whereDate('ngay_hoc', '<=', $request->den_ngay);
        }

        $buoiHocList = $query->paginate(20);

        // Lấy danh sách lớp để filter
        $danhSachLopHocPhan = \App\Models\LopHocPhan::with('monHoc')
            ->whereIn('id', $lopHocPhanIds)
            ->get();

        // Thống kê điểm danh cho mỗi buổi và tự động cập nhật trạng thái
        foreach ($buoiHocList as $buoiHoc) {
            $diemDanhStats = DiemDanh::where('lich_hoc_chi_tiet_id', $buoiHoc->id)
                ->selectRaw('
                    COUNT(*) as tong,
                    SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                    SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                    SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                ')
                ->first();

            // Tự động cập nhật trạng thái: Nếu đã có điểm danh nhưng trạng thái vẫn là "Chưa dạy" hoặc "Đang dạy"
            if ($diemDanhStats && $diemDanhStats->tong > 0) {
                if ($buoiHoc->trang_thai == 'chua_day' || $buoiHoc->trang_thai == 'dang_day') {
                    // Cập nhật trạng thái trước khi gán thuộc tính động
                    LichHocChiTiet::where('id', $buoiHoc->id)->update(['trang_thai' => 'da_day']);
                    // Refresh để hiển thị đúng trạng thái mới
                    $buoiHoc->refresh();
                }
            }
            
            // Gán thuộc tính động sau khi đã cập nhật (nếu có)
            $buoiHoc->diem_danh_stats = $diemDanhStats;
        }

        return view('giangvien.diem-danh.index', compact('buoiHocList', 'danhSachLopHocPhan'));
    }

    /**
     * Hiển thị giao diện điểm danh chi tiết cho một buổi học cụ thể
     *
     * Function này hiển thị danh sách tất cả sinh viên trong lớp học phần
     * với trạng thái điểm danh (đã điểm danh hoặc chưa), cho phép giảng viên
     * đánh dấu có mặt/vắng/đi trễ/nghỉ phép cho từng sinh viên.
     *
     * Workflow:
     * 1. Xác thực giảng viên đang đăng nhập qua Auth
     * 2. Lấy thông tin buổi học (LichHocChiTiet) theo ID:
     *    - Eager load lopHocPhan.monHoc, phongHoc
     * 3. Kiểm tra quyền giảng dạy:
     *    - Query PhanCongGiangDay với lop_hoc_phan_id và giang_vien_id
     *    - Abort 403 nếu không có quyền
     * 4. Lấy danh sách sinh viên trong lớp:
     *    - Query LopHocPhanSinhVien với eager load sinhVien.nganh
     *    - Where lop_hoc_phan_id = buổi học's class
     *    - WhereIn trang_thai: ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']
     *    - OrderBy ID để danh sách ổn định
     * 5. Lấy dữ liệu điểm danh đã có (nếu buổi đã điểm danh):
     *    - Query DiemDanh where lich_hoc_chi_tiet_id = current buổi
     *    - Pluck trang_thai indexed by lop_hoc_phan_sinh_vien_id
     *    - Pluck ghi_chu indexed by lop_hoc_phan_sinh_vien_id
     * 6. Kiểm tra xem có thể sửa điểm danh không:
     *    - Parse ngay_hoc sang Carbon (timezone Asia/Ho_Chi_Minh)
     *    - So sánh với ngày hiện tại
     *    - Có thể sửa nếu: ngày học = hôm nay hoặc tương lai
     *    - Không sửa được nếu buổi học đã qua (business rule)
     * 7. Return view với full data
     *
     * Thông tin hiển thị:
     * - Thông tin buổi học: Ngày, ca học, phòng, môn học
     * - Bảng danh sách sinh viên với columns:
     *   + STT
     *   + Mã sinh viên
     *   + Họ và tên
     *   + Lớp hành chính
     *   + Trạng thái điểm danh (radio buttons hoặc select):
     *     - co_mat: Có mặt
     *     - vang: Vắng
     *     - di_tre: Đi trễ
     *     - nghi_phep: Nghỉ phép
     *   + Ghi chú (textarea, optional)
     * - Nút hành động:
     *   + Lưu điểm danh (submit form)
     *   + Đánh dấu tất cả "Có mặt" (quick action)
     *   + Huỷ (quay lại danh sách)
     *
     * Tính năng đặc biệt:
     * - Hiển thị trạng thái đã điểm danh (nếu có)
     * - Disable form nếu buổi học đã qua (coTheSua = false)
     * - Quick actions: Bulk select "Có mặt" cho tất cả
     * - Auto-save draft mỗi 30 giây (LocalStorage)
     * - Validation client-side trước khi submit
     *
     * Business rules:
     * - Chỉ cho phép điểm danh trong ngày hoặc trước ngày học
     * - Không cho sửa điểm danh của các buổi đã qua
     * - Sinh viên tạm dừng/đã rút không hiển thị trong danh sách
     *
     * @param Request $request HTTP request object
     * @param int $id ID của buổi học (lich_hoc_chi_tiet_id)
     * @return \Illuminate\View\View View điểm danh với data:
     *   - buoiHoc: LichHocChiTiet instance
     *   - sinhViens: Collection các LopHocPhanSinhVien
     *   - diemDanhData: Array trạng thái điểm danh indexed by student ID
     *   - diemDanhGhiChu: Array ghi chú indexed by student ID
     *   - coTheSua: Boolean - có thể sửa điểm danh hay không
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy buổi học
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        // Lấy buổi học
        $buoiHoc = LichHocChiTiet::with(['lopHocPhan.monHoc', 'phongHoc'])
            ->findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền điểm danh buổi học này.');
        }

        // Lấy danh sách sinh viên (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $sinhViens = LopHocPhanSinhVien::with(['sinhVien', 'lopHocPhan'])
            ->where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->orderBy('id')
            ->get();

        // Lấy dữ liệu điểm danh (nếu đã có)
        $diemDanhData = DiemDanh::where('lich_hoc_chi_tiet_id', $id)
            ->pluck('trang_thai', 'lop_hoc_phan_sinh_vien_id')
            ->toArray();

        $diemDanhGhiChu = DiemDanh::where('lich_hoc_chi_tiet_id', $id)
            ->pluck('ghi_chu', 'lop_hoc_phan_sinh_vien_id')
            ->toArray();

        // Kiểm tra xem có thể sửa điểm danh không (chỉ trong ngày, không cho sửa nếu đã qua ngày)
        $ngayHoc = Carbon::parse($buoiHoc->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->startOfDay();
        $ngayHienTai = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
        $coTheSua = $ngayHoc->isSameDay($ngayHienTai) || $ngayHoc->isFuture();

        return view('giangvien.diem-danh.show', compact(
            'buoiHoc',
            'sinhViens',
            'diemDanhData',
            'diemDanhGhiChu',
            'coTheSua'
        ));
    }

    /**
     * Lưu dữ liệu điểm danh cho một buổi học vào database
     *
     * Function này xử lý việc lưu trữ trạng thái điểm danh của tất cả sinh viên
     * trong một buổi học. Sử dụng updateOrCreate để cập nhật nếu đã tồn tại,
     * hoặc tạo mới nếu chưa có dữ liệu.
     *
     * Workflow chi tiết:
     * 1. Xác thực giảng viên đang đăng nhập
     *    - Lấy user qua $request->user()
     *    - Lấy giảng viên relationship
     *    - Abort 403 nếu không tìm thấy
     * 2. Lấy thông tin buổi học theo ID (findOrFail)
     * 3. Kiểm tra quyền giảng dạy:
     *    - Query PhanCongGiangDay
     *    - Where lop_hoc_phan_id và giang_vien_id
     *    - Abort 403 nếu không match
     * 4. Validate thời gian sửa điểm danh:
     *    - Parse ngay_hoc về Carbon (Asia/Ho_Chi_Minh timezone)
     *    - So sánh với ngày hiện tại
     *    - Chỉ cho sửa nếu: ngày học = hôm nay hoặc tương lai
     *    - Nếu buổi đã qua và đã có điểm danh: redirect back với error
     * 5. Validate request data:
     *    - diem_danh: required, array
     *    - diem_danh.*: required, in:co_mat,vang,di_tre,nghi_phep
     *    - ghi_chu: nullable, array
     *    - ghi_chu.*: nullable, string, max:500 characters
     * 6. Lấy thời gian điểm danh = Carbon::now('Asia/Ho_Chi_Minh')
     * 7. Lặp qua từng sinh viên trong request:
     *    - Lấy trạng thái từ $request->diem_danh array
     *    - Lấy ghi chú (nếu có) từ $request->ghi_chu array
     *    - Gọi DiemDanh::updateOrCreate() với:
     *      + Where conditions: lop_hoc_phan_sinh_vien_id, lich_hoc_chi_tiet_id
     *      + Update/Create values: trang_thai, thoi_gian_diem_danh, ghi_chu
     * 8. Redirect về trang show buổi học với success message
     *
     * Dữ liệu được lưu:
     * - lop_hoc_phan_sinh_vien_id: ID sinh viên trong lớp
     * - lich_hoc_chi_tiet_id: ID buổi học
     * - trang_thai: Trạng thái điểm danh (co_mat/vang/di_tre/nghi_phep)
     * - thoi_gian_diem_danh: Timestamp khi điểm danh
     * - ghi_chu: Ghi chú cho sinh viên (optional, max 500 chars)
     *
     * Trạng thái điểm danh hợp lệ:
     * - 'co_mat': Sinh viên có mặt
     * - 'vang': Sinh viên vắng (không phép)
     * - 'di_tre': Sinh viên đến trễ
     * - 'nghi_phep': Sinh viên nghỉ có phép (có giấy phép)
     *
     * Business rules áp dụng:
     * - Chỉ cho phép điểm danh/sửa trong ngày hoặc trước ngày học
     * - Không cho sửa điểm danh của các buổi đã qua
     * - Mỗi sinh viên chỉ có 1 trạng thái điểm danh cho mỗi buổi
     * - Thới gian điểm danh sử dụng server timezone (Asia/Ho_Chi_Minh)
     * - UpdateOrCreate để tránh duplicate records
     *
     * Side effects:
     * - Tạo hoặc cập nhật records trong bảng diem_danh
     * - Có thể trigger events/observers (nếu có setup)
     * - Có thể gửi email cảnh báo nếu sinh viên vắng nhiều
     *
     * Error handling:
     * - 403: Nếu không có quyền giảng viên hoặc không phụ trách lớp
     * - 404: Nếu không tìm thấy buổi học
     * - Validation errors: Nếu dữ liệu không hợp lệ
     * - Redirect back với error nếu buổi học đã qua
     *
     * @param Request $request HTTP request chứa:
     *   - diem_danh: Array [lop_hoc_phan_sinh_vien_id => trang_thai]
     *   - ghi_chu: Array [lop_hoc_phan_sinh_vien_id => ghi_chu_text] (optional)
     * @param int $id ID buổi học (lich_hoc_chi_tiet_id)
     * @return \Illuminate\Http\RedirectResponse Redirect về show page với success/error message
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy buổi học
     * @throws \Illuminate\Validation\ValidationException Nếu dữ liệu không hợp lệ
     */
    public function store(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        $buoiHoc = LichHocChiTiet::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền điểm danh buổi học này.');
        }

        // Kiểm tra thời gian sửa (chỉ trong ngày, không cho sửa nếu đã qua ngày)
        $ngayHoc = Carbon::parse($buoiHoc->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->startOfDay();
        $ngayHienTai = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
        $coTheSua = $ngayHoc->isSameDay($ngayHienTai) || $ngayHoc->isFuture();

        if (!$coTheSua && DiemDanh::where('lich_hoc_chi_tiet_id', $id)->exists()) {
            return redirect()->back()->with('error', 'Đã quá ngày học. Không thể sửa điểm danh cho các ngày đã qua.');
        }

        $validated = $request->validate([
            'diem_danh' => 'required|array',
            'diem_danh.*' => 'required|in:co_mat,vang,di_tre,nghi_phep',
            'ghi_chu' => 'nullable|array',
            'ghi_chu.*' => 'nullable|string|max:500',
        ]);

        $thoiGianDiemDanh = Carbon::now('Asia/Ho_Chi_Minh');

        foreach ($request->diem_danh as $lopHocPhanSinhVienId => $trangThai) {
            $ghiChu = $request->ghi_chu[$lopHocPhanSinhVienId] ?? null;

            DiemDanh::updateOrCreate(
                [
                    'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSinhVienId,
                    'lich_hoc_chi_tiet_id' => $id,
                ],
                [
                    'trang_thai' => $trangThai,
                    'thoi_gian_diem_danh' => $thoiGianDiemDanh,
                    'ghi_chu' => $ghiChu,
                ]
            );
        }

         // Cập nhật trạng thái buổi học từ "Chưa dạy" sang "Đã dạy" sau khi đã điểm danh
        if ($buoiHoc->trang_thai == 'chua_day' || $buoiHoc->trang_thai == 'dang_day') {
            $buoiHoc->update(['trang_thai' => 'da_day']);
        }

        return redirect()->route('giangvien.diem-danh.show', $id)
            ->with('success', 'Đã lưu điểm danh thành công!');
    }

    /**
     * Hiển thị báo cáo tổng hợp về tình hình điểm danh của từng sinh viên trong lớp
     *
     * Function này cung cấp báo cáo chi tiết về chuyên cần của từng sinh viên,
     * bao gồm tổng số buổi học, số buổi có mặt/vắng/đi trễ/nghỉ phép,
     * và tỷ lệ có mặt (%) để đánh giá chuyên cần.
     *
     * Workflow:
     * 1. Xác thực giảng viên đang đăng nhập
     *    - Abort 403 nếu không tìm thấy giảng viên profile
     * 2. Lấy danh sách lớp học phần được phân công:
     *    - Query PhanCongGiangDay where giang_vien_id
     *    - Pluck lop_hoc_phan_id vào array
     *    - Lấy full LopHocPhan objects với eager load monHoc
     * 3. Nhận filter lop_hoc_phan_id từ request (optional)
     * 4. Nếu lọc theo lớp cụ thể:
     *    a. Lấy danh sách sinh viên trong lớp:
     *       - Query LopHocPhanSinhVien
     *       - WhereIn trang_thai: ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']
     *       - Eager load sinhVien.nganh
     *    b. Tính tổng buổi học đã diễn ra:
     *       - Count LichHocChiTiet where ngay_hoc <= now()
     *    c. Với mỗi sinh viên, tính thống kê điểm danh:
     *       - Query DiemDanh with selectRaw
     *       - Count tổng buổi đã điểm danh
     *       - Sum SUM(CASE...) cho từng trạng thái:
     *         + co_mat, vang, di_tre, nghi_phep
     *       - Tính tỷ lệ có mặt = (có mặt / tổng buổi) * 100%
     *       - Làm tròn 1 số thập phân
     *    d. Tạo array báo cáo cho mỗi sinh viên
     * 5. Return view với danh sách lớp và báo cáo (nếu có)
     *
     * Thông tin hiển thị:
     * - Filter dropdown: Chọn lớp học phần
     * - Bảng báo cáo chi tiết (nếu đã chọn lớp):
     *   + STT
     *   + Mã sinh viên
     *   + Họ và tên
     *   + Lớp hành chính
     *   + Tổng buổi học
     *   + Số buổi có mặt
     *   + Số buổi vắng
     *   + Số buổi đi trễ
     *   + Số buổi nghỉ phép
     *   + Tỷ lệ có mặt (%) với color coding:
     *     - Xanh: >= 90%
     *     - Vàng: 80-89%
     *     - Đỏ: < 80%
     * - Nút hành động:
     *   + Xuất Excel
     *   + Xuất PDF
     *   + Gửi cảnh báo (cho sinh viên vắng nhiều)
     *
     * Tính năng đặc biệt:
     * - Highlight sinh viên có tỷ lệ có mặt < 80% (đỏ)
     * - Sort table theo tỷ lệ có mặt (thấp đến cao)
     * - Filter quick: "Chỉ hiển thị sinh viên vắng nhiều"
     * - Thống kê tổng hợp: TB tỷ lệ có mặt của lớp
     *
     * Business rules:
     * - Chỉ tính các buổi học đã diễn ra (ngay_hoc <= now)
     * - Sinh viên có tỷ lệ < 80% có thể bị cảnh báo học vụ
     * - Sinh viên có tỷ lệ < 70% không được dự thi
     *
     * @param Request $request Chứa filter:
     *   - lop_hoc_phan_id (optional): ID lớp cần xem báo cáo
     * @return \Illuminate\View\View View báo cáo với data:
     *   - danhSachLopHocPhan: Collection các lớp được phân công
     *   - lopHocPhanId: Current selected lớp ID
     *   - baoCao: Array thống kê từng sinh viên (null nếu chưa chọn lớp)
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền
     */
    public function report(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        // Lấy các lớp được phân công (không lọc theo khoa để hiển thị tất cả lớp được phân công)
        $lopHocPhanIds = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->pluck('lop_hoc_phan_id')
            ->toArray();

        $danhSachLopHocPhan = \App\Models\LopHocPhan::with('monHoc')
            ->whereIn('id', $lopHocPhanIds)
            ->get();

        $lopHocPhanId = $request->lop_hoc_phan_id;
        $baoCao = null;

        if ($lopHocPhanId) {
            // Lấy tất cả sinh viên trong lớp (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
            $sinhViens = LopHocPhanSinhVien::with(['sinhVien'])
                ->where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->get();

            // Lấy tất cả buổi học của lớp
            $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
                ->where('ngay_hoc', '<=', Carbon::now('Asia/Ho_Chi_Minh'))
                ->count();

            $baoCao = [];
            foreach ($sinhViens as $sv) {
                $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)
                    ->selectRaw('
                        COUNT(*) as tong_buoi_diem_danh,
                        SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                        SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                        SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                        SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                    ')
                    ->first();

                $coMat = $diemDanhStats ? ($diemDanhStats->co_mat ?? 0) : 0;
                $tyLeCoMat = $tongBuoiHoc > 0 
                    ? round(($coMat / $tongBuoiHoc) * 100, 1) 
                    : 0;

                $baoCao[] = [
                    'sinh_vien' => $sv->sinhVien,
                    'stats' => $diemDanhStats,
                    'tong_buoi_hoc' => $tongBuoiHoc,
                    'ty_le_co_mat' => $tyLeCoMat,
                ];
            }
        }

        return view('giangvien.diem-danh.report', compact(
            'danhSachLopHocPhan',
            'lopHocPhanId',
            'baoCao'
        ));
    }

    /**
     * Xuất báo cáo điểm danh ra file CSV/Excel với đầy đủ thống kê
     *
     * Function này tạo và stream file CSV chứa báo cáo điểm danh chi tiết
     * cho từng sinh viên trong lớp. File được format đẹp, hỗ trợ UTF-8,
     * và có thể mở bằng Excel mà không bị lỗi font.
     *
     * Workflow:
     * 1. Xác thực giảng viên - abort 403 nếu không hợp lệ
     * 2. Validate request:
     *    - Require lop_hoc_phan_id
     *    - Redirect back với error nếu thiếu
     * 3. Kiểm tra quyền xuất báo cáo:
     *    - Query PhanCongGiangDay
     *    - Abort 403 nếu không phân công cho giảng viên này
     * 4. Lấy thông tin lớp học phần:
     *    - FindOrFail LopHocPhan
     *    - Eager load monHoc
     * 5. Lấy danh sách sinh viên:
     *    - Query LopHocPhanSinhVien
     *    - WhereIn trang_thai: ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']
     *    - Eager load sinhVien.nganh
     * 6. Tính tổng buổi học đã diễn ra:
     *    - Count LichHocChiTiet where ngay_hoc <= now()
     * 7. Với mỗi sinh viên, tính thống kê điểm danh:
     *    - Query DiemDanh with aggregations
     *    - Tính tỷ lệ có mặt (%)
     *    - Xác định đánh giá (Xuất sắc/Tốt/Khá/Trung bình/Yếu)
     * 8. Tạo CSV content với callback function:
     *    a. Open php://output stream
     *    b. Write UTF-8 BOM (chr(0xEF).chr(0xBB).chr(0xBF))
     *       - Để Excel nhận diện đúng UTF-8
     *    c. Write header section:
     *       - Tiêu đề báo cáo
     *       - Thông tin lớp: Mã lớp, tên môn
     *       - Tổng buổi học
     *       - Ngày xuất (dd/mm/YYYY HH:ii)
     *       - Empty row separator
     *    d. Write table header:
     *       - STT, Mã SV, Họ và tên, Lớp HC
     *       - Tổng buổi, Có mặt, Vắng, Đi trễ, Nghỉ phép
     *       - Tỷ lệ (%), Đánh giá
     *    e. Write data rows:
     *       - Loop through baoCao array
     *       - Write CSV row cho mỗi sinh viên
     *    f. Close file stream
     * 9. Return StreamedResponse với headers:
     *    - Content-Type: text/csv; charset=UTF-8
     *    - Content-Disposition: attachment
     *    - Filename: bao-cao-diem-danh-{ma_lop}-{date}.csv
     *
     * CSV structure:
     * - Header section (5 rows): Title, Class info, Total sessions, Export date, Empty
     * - Table header (1 row): 11 columns
     * - Data rows: 1 row per student
     *
     * Đánh giá chuyên cần:
     * - >= 90%: Xuất sắc
     * - 80-89%: Tốt
     * - 70-79%: Khá
     * - 60-69%: Trung bình
     * - < 60%: Yếu (cần cảnh báo)
     *
     * Tính năng đặc biệt:
     * - UTF-8 BOM để Excel nhận diện tiếng Việt
     * - Streaming response để không load hết vào memory
     * - Filename có mã lớp và date để dễ quản lý
     * - Compatible với Excel, LibreOffice, Google Sheets
     *
     * Use cases:
     * - Giảng viên xuất báo cáo để lưu trữ
     * - Nộp báo cáo cho bộ môn
     * - Phân tích số liệu trong Excel
     * - Chia sẻ với GVCN về tình hình vắng học
     *
     * @param Request $request Chứa:
     *   - lop_hoc_phan_id (required): ID lớp cần xuất báo cáo
     * @return \Symfony\Component\HttpFoundation\StreamedResponse CSV file stream
     * @return \Illuminate\Http\RedirectResponse Nếu thiếu lop_hoc_phan_id
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy lớp
     */
    public function exportExcel(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        $lopHocPhanId = $request->lop_hoc_phan_id;

        if (!$lopHocPhanId) {
            return redirect()->back()->with('error', 'Vui lòng chọn lớp học phần.');
        }

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền xuất báo cáo lớp này.');
        }

        // Lấy thông tin lớp
        $lopHocPhan = \App\Models\LopHocPhan::with('monHoc')->findOrFail($lopHocPhanId);

        // Lấy dữ liệu báo cáo (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $sinhViens = LopHocPhanSinhVien::with(['sinhVien'])
            ->where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->get();

        $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('ngay_hoc', '<=', Carbon::now())
            ->count();

        $baoCao = [];
        foreach ($sinhViens as $sv) {
            $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)
                ->selectRaw('
                    COUNT(*) as tong_buoi_diem_danh,
                    SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                    SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                    SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                ')
                ->first();

            $tyLeCoMat = $tongBuoiHoc > 0 
                ? round(($diemDanhStats->co_mat / $tongBuoiHoc) * 100, 1) 
                : 0;

            $baoCao[] = [
                'sinh_vien' => $sv->sinhVien,
                'stats' => $diemDanhStats,
                'tong_buoi_hoc' => $tongBuoiHoc,
                'ty_le_co_mat' => $tyLeCoMat,
            ];
        }

        // Tạo CSV content
        $filename = 'bao-cao-diem-danh-' . $lopHocPhan->ma_lop_hp . '-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($baoCao, $lopHocPhan, $tongBuoiHoc) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['BÁO CÁO ĐIỂM DANH']);
            fputcsv($file, ['Lớp học phần: ' . $lopHocPhan->ma_lop_hp]);
            fputcsv($file, ['Môn học: ' . $lopHocPhan->monHoc->ten_mon]);
            fputcsv($file, ['Tổng số buổi học: ' . $tongBuoiHoc]);
            fputcsv($file, ['Ngày xuất: ' . date('d/m/Y H:i')]);
            fputcsv($file, []); // Empty row
            
            // Table header
            fputcsv($file, [
                'STT',
                'Mã SV',
                'Họ và tên',
                'Lớp HC',
                'Tổng buổi',
                'Có mặt',
                'Vắng',
                'Đi trễ',
                'Nghỉ phép',
                'Tỷ lệ (%)',
                'Đánh giá'
            ]);
            
            // Data rows
            foreach ($baoCao as $index => $item) {
                $tyLe = $item['ty_le_co_mat'];
                if ($tyLe >= 90) {
                    $danhGia = 'Xuất sắc';
                } elseif ($tyLe >= 80) {
                    $danhGia = 'Tốt';
                } elseif ($tyLe >= 70) {
                    $danhGia = 'Khá';
                } elseif ($tyLe >= 60) {
                    $danhGia = 'Trung bình';
                } else {
                    $danhGia = 'Yếu';
                }
                
                fputcsv($file, [
                    $index + 1,
                    $item['sinh_vien']->ma_sinh_vien,
                    $item['sinh_vien']->ho_ten,
                    'N/A', // Lớp hành chính đã được xóa
                    $item['tong_buoi_hoc'],
                    $item['stats']->co_mat,
                    $item['stats']->vang,
                    $item['stats']->di_tre,
                    $item['stats']->nghi_phep,
                    $tyLe,
                    $danhGia
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xuất báo cáo điểm danh ra file PDF với format chuyên nghiệp
     *
     * Function này render báo cáo điểm danh thành PDF view sử dụng Blade template.
     * PDF có thể hiển thị trực tiếp trong browser hoặc download, phù hợp
     * cho việc in ấn và nộp báo cáo chính thức.
     *
     * Workflow:
     * 1. Xác thực giảng viên đang đăng nhập
     *    - Abort 403 nếu không tìm thấy giảng viên profile
     * 2. Validate request:
     *    - Require lop_hoc_phan_id
     *    - Redirect back với error message nếu thiếu
     * 3. Kiểm tra quyền xuất báo cáo:
     *    - Query PhanCongGiangDay
     *    - Where lop_hoc_phan_id và giang_vien_id
     *    - Abort 403 nếu không có quyền
     * 4. Lấy thông tin lớp học phần:
     *    - FindOrFail LopHocPhan by ID
     *    - Eager load monHoc relationship
     * 5. Lấy danh sách sinh viên trong lớp:
     *    - Query LopHocPhanSinhVien
     *    - WhereIn trang_thai: ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']
     *    - Eager load sinhVien.nganh
     * 6. Tính tổng buổi học đã diễn ra:
     *    - Count LichHocChiTiet
     *    - Where ngay_hoc <= Carbon::now()
     * 7. Với mỗi sinh viên, tính thống kê điểm danh:
     *    - Query DiemDanh with selectRaw aggregations
     *    - Sum by trang_thai (co_mat, vang, di_tre, nghi_phep)
     *    - Tính tỷ lệ có mặt = (có mặt / tổng buổi) * 100%
     *    - Làm tròn 1 decimal
     *    - Build baoCao array
     * 8. Return Blade view 'giangvien.diem-danh.export-pdf':
     *    - Pass lopHocPhan object
     *    - Pass baoCao array
     *    - Pass tongBuoiHoc
     *
     * PDF view (Blade template) hiển thị:
     * - Header chính thức:
     *   + Logo trường (nếu có)
     *   + Tên trường, khoa
     *   + Tiêu đề: "BÁO CÁO ĐIỂM DANH"
     * - Thông tin lớp:
     *   + Mã lớp học phần
     *   + Tên môn học
     *   + Tổng số buổi học
     *   + Ngày xuất báo cáo
     * - Bảng thống kê chi tiết:
     *   + STT, Mã SV, Họ tên, Lớp HC
     *   + Tổng buổi, Có mặt, Vắng, Đi trễ, Nghỉ phép
     *   + Tỷ lệ % với color coding
     * - Footer:
     *   + Ngày ký
     *   + Chữ ký giảng viên
     *   + Page numbers (nếu nhiều trang)
     *
     * Tính năng render:
     * - Responsive cho các kích thước giấy (A4)
     * - Color-coded tỷ lệ có mặt (xanh/vàng/đỏ)
     * - Professional formatting với borders và spacing
     * - Support Vietnamese UTF-8 characters
     *
     * Use cases:
     * - In báo cáo giấy để nộp cho bộ môn
     * - Lưu PDF để gửi email
     * - Hiển thị trực tiếp trong browser
     * - Archive báo cáo theo học kỳ
     *
     * @param Request $request Chứa:
     *   - lop_hoc_phan_id (required): ID lớp cần xuất báo cáo
     * @return \Illuminate\View\View PDF view để render với data:
     *   - lopHocPhan: LopHocPhan instance
     *   - baoCao: Array thống kê từng sinh viên
     *   - tongBuoiHoc: Tổng số buổi học đã diễn ra
     * @return \Illuminate\Http\RedirectResponse Nếu thiếu lop_hoc_phan_id
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy lớp
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        $lopHocPhanId = $request->lop_hoc_phan_id;

        if (!$lopHocPhanId) {
            return redirect()->back()->with('error', 'Vui lòng chọn lớp học phần.');
        }

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền xuất báo cáo lớp này.');
        }

        // Lấy thông tin lớp
        $lopHocPhan = \App\Models\LopHocPhan::with('monHoc')->findOrFail($lopHocPhanId);

        // Lấy dữ liệu báo cáo (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $sinhViens = LopHocPhanSinhVien::with(['sinhVien'])
            ->where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->get();

        $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('ngay_hoc', '<=', Carbon::now())
            ->count();

        $baoCao = [];
        foreach ($sinhViens as $sv) {
            $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)
                ->selectRaw('
                    COUNT(*) as tong_buoi_diem_danh,
                    SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                    SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                    SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                ')
                ->first();

            $tyLeCoMat = $tongBuoiHoc > 0 
                ? round(($diemDanhStats->co_mat / $tongBuoiHoc) * 100, 1) 
                : 0;

            $baoCao[] = [
                'sinh_vien' => $sv->sinhVien,
                'stats' => $diemDanhStats,
                'tong_buoi_hoc' => $tongBuoiHoc,
                'ty_le_co_mat' => $tyLeCoMat,
            ];
        }

        return view('giangvien.diem-danh.export-pdf', compact('lopHocPhan', 'baoCao', 'tongBuoiHoc'));
    }

    /**
     * Kiểm tra chuyên cần và gửi email cảnh báo cho sinh viên vắng học nhiều
     *
     * Function này tự động kiểm tra tỷ lệ chuyên cần của từng sinh viên trong lớp,
     * tạo cảnh báo học vụ trong database, và gửi email cảnh báo cho những sinh viên
     * có tỷ lệ có mặt < 80% (vắng > 20%). Đây là tính năng quan trọng để quản lý
     * chuyên cần và cảnh báo kịp thời cho sinh viên.
     *
     * Workflow chi tiết:
     * 1. Xác thực giảng viên đang đăng nhập
     *    - Abort 403 nếu không tìm thấy giảng viên profile
     * 2. Validate request:
     *    - Require lop_hoc_phan_id
     *    - Redirect back với error nếu thiếu
     * 3. Kiểm tra quyền gửi cảnh báo:
     *    - Query PhanCongGiangDay
     *    - Abort 403 nếu giảng viên không phụ trách lớp này
     * 4. Lấy thông tin lớp học phần:
     *    - FindOrFail LopHocPhan
     *    - Eager load monHoc
     * 5. Lấy danh sách sinh viên trong lớp:
     *    - Query LopHocPhanSinhVien
     *    - WhereIn trang_thai: ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']
     *    - Eager load sinhVien
     * 6. Tính tổng buổi học đã diễn ra:
     *    - Count LichHocChiTiet where ngay_hoc <= now()
     *    - Return với warning nếu tongBuoiHoc = 0
     * 7. Khởi tạo counters:
     *    - danhSachCanhBao: Array sinh viên cần cảnh báo
     *    - soLuongDaGui: Số email đã gửi thành công
     *    - soLuongLoi: Số email gửi thất bại
     *    - danhSachLoi: Array lỗi messages
     * 8. Với mỗi sinh viên, kiểm tra chuyên cần:
     *    a. Query thống kê điểm danh:
     *       - Count tổng buổi đã điểm danh
     *       - Sum theo từng trạng thái (co_mat, vang, di_tre, nghi_phep)
     *    b. Tính tỷ lệ có mặt = (có mặt / tổng buổi học) * 100%
     *    c. Nếu tỷ lệ < 80% (cần cảnh báo):
     *       - Build thongKe array với full stats
     *       - Add vào danhSachCanhBao
     *       - Gọi taoCanhBaoHocVu() để lưu vào database
     *       - Try gửi email cảnh báo:
     *         + Validate sinh viên exists
     *         + Validate email exists
     *         + Mail::send CanhBaoDiemDanhMail
     *         + Increment soLuongDaGui nếu thành công
     *         + Log error và add vào danhSachLoi nếu thất bại
     * 9. Return redirect với kết quả:
     *    - Success message với số lượng đã gửi
     *    - Warning nếu có lỗi, kèm danh sách lỗi
     *
     * Quy tắc cảnh báo:
     * - Tỷ lệ có mặt < 80%: Cảnh báo level 1
     * - Tỷ lệ có mặt < 70%: Cảnh báo level 2, có thể bị cấm thi
     * - Chỉ tính các buổi học đã diễn ra (ngay_hoc <= now)
     * - Nghỉ phép vẫn được tính trong tổng vắng
     *
     * Dữ liệu cảnh báo học vụ tạo ra:
     * - sinh_vien_id: ID sinh viên
     * - lop_hoc_phan_id: ID lớp học phần
     * - loai_canh_bao: 'diem_danh' / 'hoc_tap'
     * - noi_dung: Mô tả chi tiết tình trạng vắng
     * - ty_le_vang: Tỷ lệ % (tính từ 100 - ty_le_co_mat)
     * - trang_thai: 'chua_xu_ly'
     * - nguoi_tao_id: Giảng viên tạo cảnh báo
     * - ngay_tao: Timestamp hiện tại
     *
     * Email cảnh báo gửi cho sinh viên bao gồm:
     * - Thông tin sinh viên (mã SV, họ tên)
     * - Thông tin lớp học phần (mã lớp, môn học)
     * - Thống kê chuyên cần chi tiết:
     *   + Tổng buổi học
     *   + Số buổi có mặt, vắng, đi trễ, nghỉ phép
     *   + Tỷ lệ có mặt (%)
     * - Cảnh báo hậu quả nếu tiếp tục vắng
     * - Hướng dẫn liên hệ với giảng viên/GVCN
     *
     * Logging:
     * - Log info khi gửi email thành công (sinh_vien_id, email, ty_le)
     * - Log error khi tạo cảnh báo học vụ thất bại
     * - Track danh sách lỗi để báo cáo cho giảng viên
     *
     * Error handling:
     * - Sinh viên không tồn tại: Skip, add vào danhSachLoi
     * - Sinh viên chưa có email: Skip, add vào danhSachLoi
     * - Mail sending failure: Catch exception, log, add vào danhSachLoi
     * - Database error khi tạo cảnh báo: Log error
     *
     * Side effects:
     * - Tạo records mới trong bảng canh_bao_hoc_vu
     * - Gửi emails đến sinh viên (có thể nhiều emails)
     * - Log entries trong Laravel log files
     * - Có thể trigger notifications cho GVCN (nếu setup)
     *
     * Use cases:
     * - Giảng viên chủ động gửi cảnh báo giữa kỳ
     * - Tự động cảnh báo sau khi điểm danh
     * - Báo cáo định kỳ về tình hình chuyên cần
     * - Phối hợp với GVCN quản lý sinh viên vắng nhiều
     *
     * @param Request $request Chứa:
     *   - lop_hoc_phan_id (required): ID lớp cần kiểm tra và gửi cảnh báo
     * @return \Illuminate\Http\RedirectResponse Redirect back với messages:
     *   - Success: "Đã gửi {count} email cảnh báo thành công"
     *   - Warning: "Đã gửi {count} emails, {error_count} lỗi" (kèm danh sách lỗi)
     *   - Warning: "Lớp chưa có buổi học nào" (nếu tongBuoiHoc = 0)
     *   - Error: "Vui lòng chọn lớp học phần" (nếu thiếu lop_hoc_phan_id)
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy lớp
     */
    public function checkAndSendWarnings(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        $lopHocPhanId = $request->lop_hoc_phan_id;

        if (!$lopHocPhanId) {
            return redirect()->back()->with('error', 'Vui lòng chọn lớp học phần.');
        }

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền gửi cảnh báo cho lớp này.');
        }

        // Lấy thông tin lớp
        $lopHocPhan = \App\Models\LopHocPhan::with('monHoc')->findOrFail($lopHocPhanId);

        // Lấy danh sách sinh viên (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $sinhViens = LopHocPhanSinhVien::with('sinhVien')
            ->where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->get();

        // Tổng số buổi học đã diễn ra
        $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('ngay_hoc', '<=', Carbon::now())
            ->count();

        if ($tongBuoiHoc == 0) {
            return redirect()->back()->with('warning', 'Lớp chưa có buổi học nào.');
        }

        $danhSachCanhBao = [];
        $soLuongDaGui = 0;
        $soLuongLoi = 0;
        $danhSachLoi = [];

        foreach ($sinhViens as $sv) {
            // Thống kê điểm danh
            $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)
                ->selectRaw('
                    COUNT(*) as tong_buoi_diem_danh,
                    SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                    SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                    SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                ')
                ->first();

            // Nếu chưa có điểm danh nào, set giá trị mặc định
            $coMat = $diemDanhStats ? ($diemDanhStats->co_mat ?? 0) : 0;
            $vang = $diemDanhStats ? ($diemDanhStats->vang ?? 0) : 0;
            $diTre = $diemDanhStats ? ($diemDanhStats->di_tre ?? 0) : 0;
            $nghiPhep = $diemDanhStats ? ($diemDanhStats->nghi_phep ?? 0) : 0;

            // Tính tỷ lệ chuyên cần
            $tyLeCoMat = $tongBuoiHoc > 0 
                ? round(($coMat / $tongBuoiHoc) * 100, 1) 
                : 0;

            // Nếu tỷ lệ < 80% (vắng > 20%)
            if ($tyLeCoMat < 80) {
                $thongKe = [
                    'tong_buoi' => $tongBuoiHoc,
                    'co_mat' => $coMat,
                    'vang' => $vang,
                    'di_tre' => $diTre,
                    'nghi_phep' => $nghiPhep,
                    'ty_le' => $tyLeCoMat,
                ];

                $danhSachCanhBao[] = [
                    'sinh_vien' => $sv->sinhVien,
                    'lop_hoc_phan' => $lopHocPhan,
                    'thong_ke' => $thongKe,
                ];

                // Tạo cảnh báo học vụ trong database
                try {
                    $this->taoCanhBaoHocVu($sv->sinhVien, $lopHocPhan, $thongKe, $tyLeCoMat, $giangVien);
                } catch (\Exception $e) {
                    Log::error('Lỗi tạo cảnh báo học vụ', [
                        'sinh_vien_id' => $sv->sinhVien->id ?? null,
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Gửi email cho sinh viên
                try {
                    if (!$sv->sinhVien) {
                        $soLuongLoi++;
                        $danhSachLoi[] = "Sinh viên ID {$sv->sinh_vien_id}: Không tìm thấy thông tin sinh viên";
                        continue;
                    }

                    if (!$sv->sinhVien->email) {
                        $soLuongLoi++;
                        $danhSachLoi[] = "Sinh viên {$sv->sinhVien->ma_sinh_vien}: Chưa có email";
                        continue;
                    }

                    Mail::to($sv->sinhVien->email)->send(
                        new CanhBaoDiemDanhMail($sv->sinhVien, $lopHocPhan, $thongKe)
                    );
                    $soLuongDaGui++;
                    Log::info('Đã gửi cảnh báo chuyên cần', [
                        'sinh_vien_id' => $sv->sinhVien->id,
                        'email' => $sv->sinhVien->email,
                        'ty_le' => $tyLeCoMat
                    ]);
                } catch (\Exception $e) {
                    $soLuongLoi++;
                    $danhSachLoi[] = "Sinh viên {$sv->sinhVien->ma_sinh_vien}: " . $e->getMessage();
                    Log::error('Lỗi gửi email cảnh báo', [
                        'sinh_vien_id' => $sv->sinhVien->id ?? null,
                        'email' => $sv->sinhVien->email ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        // Gửi báo cáo cho giảng viên chủ nhiệm (nếu có sinh viên cần cảnh báo)
        if (count($danhSachCanhBao) > 0) {
            $this->sendReportToHomeRoomTeachers($danhSachCanhBao);
        }

        // Tạo thông báo kết quả
        $message = '';
        if ($soLuongDaGui > 0) {
            $message = "Đã gửi cảnh báo đến {$soLuongDaGui} sinh viên có tỷ lệ chuyên cần < 80%.";
        }
        
        if ($soLuongLoi > 0) {
            $message .= ($message ? ' ' : '') . "Có {$soLuongLoi} sinh viên không thể gửi cảnh báo.";
            if (count($danhSachLoi) > 0) {
                Log::warning('Danh sách lỗi gửi cảnh báo', ['loi' => $danhSachLoi]);
            }
        }
        
        if (count($danhSachCanhBao) == 0) {
            return redirect()->back()->with('info', 
                'Không có sinh viên nào cần cảnh báo (tất cả đều đạt tỷ lệ chuyên cần >= 80%).');
        }
        
        if ($soLuongDaGui > 0) {
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 
                'Không thể gửi cảnh báo cho bất kỳ sinh viên nào. Vui lòng kiểm tra cấu hình email hoặc log để biết chi tiết.');
        }
    }

    /**
     * Tạo cảnh báo học vụ cho sinh viên
     */
    private function taoCanhBaoHocVu($sinhVien, $lopHocPhan, $thongKe, $tyLeCoMat, $giangVien)
    {
        // Lấy học kỳ từ lớp học phần
        $hocKyId = $lopHocPhan->hoc_ky_id;
        
        // Kiểm tra xem đã có cảnh báo vắng nhiều cho sinh viên này trong học kỳ này chưa
        $canhBaoTonTai = CanhBaoHocVu::where('sinh_vien_id', $sinhVien->id)
            ->where('hoc_ky_id', $hocKyId)
            ->where('loai_canh_bao', 'vang_nhieu')
            ->where('trang_thai', 'chua_xu_ly')
            ->first();
        
        // Nếu đã có cảnh báo chưa xử lý, cập nhật lại thay vì tạo mới
        if ($canhBaoTonTai) {
            // Cập nhật lý do và ngày cảnh báo
            $canhBaoTonTai->update([
                'ly_do' => $this->taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat),
                'ngay_canh_bao' => Carbon::now('Asia/Ho_Chi_Minh'),
                'muc_do' => $this->xacDinhMucDo($tyLeCoMat),
            ]);
            Log::info('Đã cập nhật cảnh báo học vụ', [
                'canh_bao_id' => $canhBaoTonTai->id,
                'sinh_vien_id' => $sinhVien->id,
                'ty_le' => $tyLeCoMat
            ]);
            return $canhBaoTonTai;
        }
        
        // Xác định mức độ cảnh báo dựa trên tỷ lệ chuyên cần
        $mucDo = $this->xacDinhMucDo($tyLeCoMat);
        
        // Tạo cảnh báo mới
        $canhBao = CanhBaoHocVu::create([
            'sinh_vien_id' => $sinhVien->id,
            'hoc_ky_id' => $hocKyId,
            'loai_canh_bao' => 'vang_nhieu',
            'muc_do' => $mucDo,
            'ly_do' => $this->taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat),
            'ngay_canh_bao' => Carbon::now(),
            'nguoi_tao_id' => $giangVien->user_id ?? null,
            'trang_thai' => 'chua_xu_ly',
            'ghi_chu' => "Tự động tạo từ hệ thống điểm danh. Lớp: {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->ten_lop_hp}",
        ]);
        
        Log::info('Đã tạo cảnh báo học vụ', [
            'canh_bao_id' => $canhBao->id,
            'sinh_vien_id' => $sinhVien->id,
            'lop_hoc_phan_id' => $lopHocPhan->id,
            'ty_le' => $tyLeCoMat,
            'muc_do' => $mucDo
        ]);
        
        return $canhBao;
    }
    
    /**
     * Xác định mức độ cảnh báo dựa trên tỷ lệ chuyên cần
     */
    private function xacDinhMucDo($tyLeCoMat)
    {
        if ($tyLeCoMat < 50) {
            return 'dinh_chi'; // Đình chỉ nếu vắng > 50%
        } elseif ($tyLeCoMat < 70) {
            return 'canh_cao'; // Cảnh cáo nếu vắng 30-50%
        } else {
            return 'canh_cao'; // Cảnh cáo nếu vắng 20-30%
        }
    }
    
    /**
     * Tạo lý do cảnh báo
     */
    private function taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat)
    {
        $lyDo = "Tỷ lệ chuyên cần thấp ({$tyLeCoMat}%) trong môn học {$lopHocPhan->monHoc->ten_mon} ";
        $lyDo .= "(Lớp: {$lopHocPhan->ma_lop_hp}). ";
        $lyDo .= "Tổng số buổi học: {$thongKe['tong_buoi']}, ";
        $lyDo .= "Có mặt: {$thongKe['co_mat']}, ";
        $lyDo .= "Vắng: {$thongKe['vang']}, ";
        
        if ($thongKe['di_tre'] > 0) {
            $lyDo .= "Đi trễ: {$thongKe['di_tre']}, ";
        }
        
        if ($thongKe['nghi_phep'] > 0) {
            $lyDo .= "Nghỉ phép: {$thongKe['nghi_phep']}, ";
        }
        
        $lyDo .= "Tỷ lệ vắng: " . (100 - $tyLeCoMat) . "%.";
        
        return $lyDo;
    }

    /**
     * Gửi báo cáo cho giảng viên chủ nhiệm
     * Lưu ý: Chức năng này đã bị vô hiệu hóa do lớp hành chính đã được xóa khỏi hệ thống
     */
    private function sendReportToHomeRoomTeachers($danhSachCanhBao)
    {
        // Lớp hành chính đã được xóa khỏi hệ thống
        // Không thể nhóm sinh viên theo giảng viên chủ nhiệm nữa
        // Function này được giữ lại để tránh lỗi nếu có code khác gọi đến
        return;
    }
}
