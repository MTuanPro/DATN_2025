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
use App\Mail\CanhBaoDiemDanhMail;
use App\Mail\BaoCaoSinhVienYeuMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Danh sách buổi học cần điểm danh
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
        $query = LichHocChiTiet::with(['lopHocPhan.monHoc', 'phongHoc'])
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

        // Thống kê điểm danh cho mỗi buổi
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

            $buoiHoc->diem_danh_stats = $diemDanhStats;
        }

        return view('giangvien.diem-danh.index', compact('buoiHocList', 'danhSachLopHocPhan'));
    }

    /**
     * Hiển thị danh sách sinh viên cần điểm danh
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

        // Lấy danh sách sinh viên
        $sinhViens = LopHocPhanSinhVien::with(['sinhVien.lopHanhChinh'])
            ->where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->where('trang_thai', 'dang_hoc')
            ->orderBy('id')
            ->get();

        // Lấy dữ liệu điểm danh (nếu đã có)
        $diemDanhData = DiemDanh::where('lich_hoc_chi_tiet_id', $id)
            ->pluck('trang_thai', 'lop_hoc_phan_sinh_vien_id')
            ->toArray();

        $diemDanhGhiChu = DiemDanh::where('lich_hoc_chi_tiet_id', $id)
            ->pluck('ghi_chu', 'lop_hoc_phan_sinh_vien_id')
            ->toArray();

        // Kiểm tra xem có thể sửa điểm danh không (trong vòng 24h sau buổi học)
        $coTheSua = Carbon::parse($buoiHoc->ngay_hoc)->addHours(24)->isFuture();

        return view('giangvien.diem-danh.show', compact(
            'buoiHoc',
            'sinhViens',
            'diemDanhData',
            'diemDanhGhiChu',
            'coTheSua'
        ));
    }

    /**
     * Lưu điểm danh
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

        // Kiểm tra thời gian sửa (trong vòng 24h)
        $coTheSua = Carbon::parse($buoiHoc->ngay_hoc)->addHours(24)->isFuture();

        if (!$coTheSua && DiemDanh::where('lich_hoc_chi_tiet_id', $id)->exists()) {
            return redirect()->back()->with('error', 'Đã quá thời gian cho phép sửa điểm danh (24h sau buổi học).');
        }

        $validated = $request->validate([
            'diem_danh' => 'required|array',
            'diem_danh.*' => 'required|in:co_mat,vang,di_tre,nghi_phep',
            'ghi_chu' => 'nullable|array',
            'ghi_chu.*' => 'nullable|string|max:500',
        ]);

        $thoiGianDiemDanh = Carbon::now();

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

        return redirect()->route('giangvien.diem-danh.show', $id)
            ->with('success', 'Đã lưu điểm danh thành công!');
    }

    /**
     * Báo cáo điểm danh theo lớp
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
            // Lấy tất cả sinh viên trong lớp
            $sinhViens = LopHocPhanSinhVien::with(['sinhVien.lopHanhChinh'])
                ->where('lop_hoc_phan_id', $lopHocPhanId)
                ->where('trang_thai', 'dang_hoc')
                ->get();

            // Lấy tất cả buổi học của lớp
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
     * Export báo cáo điểm danh ra Excel
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

        // Lấy dữ liệu báo cáo
        $sinhViens = LopHocPhanSinhVien::with(['sinhVien.lopHanhChinh'])
            ->where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('trang_thai', 'dang_hoc')
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
                    $item['sinh_vien']->lopHanhChinh->ten_lop ?? 'N/A',
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
     * Export báo cáo điểm danh ra PDF
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

        // Lấy dữ liệu báo cáo
        $sinhViens = LopHocPhanSinhVien::with(['sinhVien.lopHanhChinh'])
            ->where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('trang_thai', 'dang_hoc')
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
     * Kiểm tra và gửi cảnh báo chuyên cần
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

        // Lấy danh sách sinh viên
        $sinhViens = LopHocPhanSinhVien::with('sinhVien')
            ->where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('trang_thai', 'dang_hoc')
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

            // Tính tỷ lệ chuyên cần
            $tyLeCoMat = $tongBuoiHoc > 0 
                ? round(($diemDanhStats->co_mat / $tongBuoiHoc) * 100, 1) 
                : 0;

            // Nếu tỷ lệ < 80% (vắng > 20%)
            if ($tyLeCoMat < 80) {
                $thongKe = [
                    'tong_buoi' => $tongBuoiHoc,
                    'co_mat' => $diemDanhStats->co_mat,
                    'vang' => $diemDanhStats->vang,
                    'di_tre' => $diemDanhStats->di_tre,
                    'nghi_phep' => $diemDanhStats->nghi_phep,
                    'ty_le' => $tyLeCoMat,
                ];

                $danhSachCanhBao[] = [
                    'sinh_vien' => $sv->sinhVien,
                    'lop_hoc_phan' => $lopHocPhan,
                    'thong_ke' => $thongKe,
                ];

                // Gửi email cho sinh viên
                try {
                    if ($sv->sinhVien && $sv->sinhVien->email) {
                        Mail::to($sv->sinhVien->email)->send(
                            new CanhBaoDiemDanhMail($sv->sinhVien, $lopHocPhan, $thongKe)
                        );
                        $soLuongDaGui++;
                    }
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email cảnh báo: ' . $e->getMessage());
                }
            }
        }

        // Gửi báo cáo cho giảng viên chủ nhiệm (nếu có sinh viên cần cảnh báo)
        if (count($danhSachCanhBao) > 0) {
            $this->sendReportToHomeRoomTeachers($danhSachCanhBao);
        }

        if ($soLuongDaGui > 0) {
            return redirect()->back()->with('success', 
                "Đã gửi cảnh báo đến {$soLuongDaGui} sinh viên có tỷ lệ chuyên cần < 80%.");
        } else {
            return redirect()->back()->with('info', 
                'Không có sinh viên nào cần cảnh báo (tất cả đều đạt tỷ lệ chuyên cần >= 80%).');
        }
    }

    /**
     * Gửi báo cáo cho giảng viên chủ nhiệm
     */
    private function sendReportToHomeRoomTeachers($danhSachCanhBao)
    {
        // Nhóm sinh viên theo giảng viên chủ nhiệm
        $nhomTheoGVCN = [];

        foreach ($danhSachCanhBao as $item) {
            $sinhVien = $item['sinh_vien'];
            $lopHanhChinh = $sinhVien->lopHanhChinh;

            if ($lopHanhChinh && $lopHanhChinh->giang_vien_chu_nhiem_id) {
                $gvcnId = $lopHanhChinh->giang_vien_chu_nhiem_id;

                if (!isset($nhomTheoGVCN[$gvcnId])) {
                    $nhomTheoGVCN[$gvcnId] = [];
                }

                $nhomTheoGVCN[$gvcnId][] = $item;
            }
        }

        // Gửi email cho từng giảng viên chủ nhiệm
        foreach ($nhomTheoGVCN as $gvcnId => $danhSach) {
            $giangVienChuNhiem = GiangVien::find($gvcnId);

            if ($giangVienChuNhiem && $giangVienChuNhiem->email) {
                try {
                    Mail::to($giangVienChuNhiem->email)->send(
                        new BaoCaoSinhVienYeuMail($giangVienChuNhiem, $danhSach)
                    );
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email báo cáo GVCN: ' . $e->getMessage());
                }
            }
        }
    }
}
