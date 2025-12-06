<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\DangKyMonHoc;
use App\Models\BangDiem;
use App\Models\NhapDiem;
use App\Models\CauHinhDauDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class KetQuaHocTapController extends Controller
{
    /**
     * Hiển thị danh sách lớp học phần để xem kết quả
     */
    public function index(Request $request)
    {
        $giangVien = auth()->user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách lớp học phần của giảng viên
        $query = LopHocPhan::with(['monHoc', 'hocKy', 'giangViens'])
            ->whereHas('giangViens', function ($q) use ($giangVien) {
                $q->where('giang_vien_id', $giangVien->id);
            });

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai_lop', $request->trang_thai);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop_hp', 'LIKE', "%{$search}%")
                    ->orWhere('ten_lop_hp', 'LIKE', "%{$search}%")
                    ->orWhereHas('monHoc', function ($mh) use ($search) {
                        $mh->where('ten_mon', 'LIKE', "%{$search}%")
                            ->orWhere('ma_mon', 'LIKE', "%{$search}%");
                    });
            });
        }

        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Thống kê cho mỗi lớp
        foreach ($lopHocPhans as $lop) {
            // Tổng sinh viên (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
            $tongSV = \App\Models\LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->count();

            // Sinh viên đã có điểm (có ít nhất 1 điểm đã nhập)
            $svCoDiem = \App\Models\LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->whereHas('nhapDiems')
                ->count();

            $lop->so_sinh_vien = $tongSV;
            $lop->sv_da_nhap = $svCoDiem;
            $lop->ty_le_nhap = $tongSV > 0 ? round($svCoDiem / $tongSV * 100, 1) : 0;
            $lop->da_nhap_diem = $svCoDiem > 0;
        }

        $hocKys = \App\Models\HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        return view('giangvien.ket-qua-hoc-tap.index', compact('lopHocPhans', 'hocKys'));
    }

    /**
     * Xem bảng điểm tổng kết lớp học phần
     */
    public function show($id)
    {
        $giangVien = auth()->user()->giangVien;

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'giangViens'])
            ->whereHas('giangViens', function ($q) use ($giangVien) {
                $q->where('giang_vien_id', $giangVien->id);
            })
            ->findOrFail($id);

        // Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $id)->get();

        if ($cauHinhs->isEmpty()) {
            return redirect()->route('giangvien.ket-qua-hoc-tap.index')
                ->with('error', 'Lớp học phần chưa có cấu hình đầu điểm');
        }

        // Lấy danh sách sinh viên từ lop_hoc_phan_sinh_vien (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $danhSachSinhVien = \App\Models\LopHocPhanSinhVien::where('lop_hoc_phan_id', $id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien', 'ketQuaHocTap'])
            ->get()
            ->map(function ($lhpsv) use ($cauHinhs) {
                // Lấy tất cả điểm đã nhập
                $danhSachDiem = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                    ->with('cauHinh')
                    ->get();

                $lhpsv->danh_sach_diem = $danhSachDiem;

                // Lấy điểm tổng kết từ ket_qua_hoc_tap
                $ketQua = $lhpsv->ketQuaHocTap;
                $lhpsv->diem_tong_ket = $ketQua ? $ketQua->diem_he_10 : null;
                $lhpsv->diem_chu = $ketQua ? $ketQua->diem_chu : null;
                $lhpsv->xep_loai = $ketQua ? $ketQua->xep_loai : null;

                return $lhpsv;
            })
            ->sortBy('sinhVien.ma_sinh_vien');

        // Thống kê phân bố điểm
        $thongKe = [
            'tong_sv' => $danhSachSinhVien->count(),
            'sv_co_diem' => $danhSachSinhVien->filter(fn($sv) => $sv->diem_tong_ket !== null)->count(),
            'sv_qua_mon' => $danhSachSinhVien->filter(fn($sv) => $sv->diem_tong_ket >= 4)->count(),
            'sv_truot' => $danhSachSinhVien->filter(fn($sv) => $sv->diem_tong_ket !== null && $sv->diem_tong_ket < 4)->count(),
            'diem_cao_nhat' => $danhSachSinhVien->max('diem_tong_ket') ?? 0,
            'diem_thap_nhat' => $danhSachSinhVien->where('diem_tong_ket', '!=', null)->min('diem_tong_ket') ?? 0,
            'diem_trung_binh' => $danhSachSinhVien->where('diem_tong_ket', '!=', null)->avg('diem_tong_ket') ?? 0,
        ];

        return view('giangvien.ket-qua-hoc-tap.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'danhSachSinhVien',
            'thongKe'
        ));
    }

    /**
     * Phân tích điểm chi tiết
     */
    public function phanTich($id)
    {
        $giangVien = auth()->user()->giangVien;

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])
            ->whereHas('giangViens', function ($q) use ($giangVien) {
                $q->where('giang_vien_id', $giangVien->id);
            })
            ->findOrFail($id);

        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $id)->get();

        if ($cauHinhs->isEmpty()) {
            return redirect()->route('giangvien.ket-qua-hoc-tap.index')
                ->with('error', 'Lớp học phần chưa có cấu hình đầu điểm');
        }

        // Lấy danh sách sinh viên có điểm (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $danhSachSinhVien = \App\Models\LopHocPhanSinhVien::where('lop_hoc_phan_id', $id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien', 'ketQuaHocTap'])
            ->get()
            ->filter(function ($lhpsv) {
                return $lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_he_10 !== null;
            })
            ->map(function ($lhpsv) {
                $ketQua = $lhpsv->ketQuaHocTap;
                $lhpsv->diem_tong_ket = $ketQua->diem_he_10;
                $lhpsv->diem_chu = $ketQua->diem_chu;
                $lhpsv->qua_mon = $ketQua->diem_he_10 >= 4;
                return $lhpsv;
            });

        // Thống kê chi tiết
        $thongKe = [
            'tong_sv' => $danhSachSinhVien->count(),
            'sv_qua_mon' => $danhSachSinhVien->filter(fn($sv) => $sv->qua_mon)->count(),
            'sv_truot' => $danhSachSinhVien->filter(fn($sv) => !$sv->qua_mon)->count(),
            'diem_cao_nhat' => $danhSachSinhVien->max('diem_tong_ket') ?? 0,
            'diem_thap_nhat' => $danhSachSinhVien->min('diem_tong_ket') ?? 0,
            'diem_trung_binh' => $danhSachSinhVien->avg('diem_tong_ket') ?? 0,
        ];

        // Phân bố theo loại điểm chữ
        $phanBoDiemChu = $danhSachSinhVien->groupBy('diem_chu')
            ->map(fn($group) => $group->count())
            ->toArray();

        // Biểu đồ phân bố điểm (theo khoảng)
        $phanBoKhoang = [
            '9.0-10' => 0,
            '8.0-8.9' => 0,
            '7.0-7.9' => 0,
            '6.0-6.9' => 0,
            '5.0-5.9' => 0,
            '4.0-4.9' => 0,
            '0-3.9' => 0,
        ];

        foreach ($danhSachSinhVien as $sv) {
            $diem = $sv->diem_tong_ket;
            if ($diem >= 9.0) $phanBoKhoang['9.0-10']++;
            elseif ($diem >= 8.0) $phanBoKhoang['8.0-8.9']++;
            elseif ($diem >= 7.0) $phanBoKhoang['7.0-7.9']++;
            elseif ($diem >= 6.0) $phanBoKhoang['6.0-6.9']++;
            elseif ($diem >= 5.0) $phanBoKhoang['5.0-5.9']++;
            elseif ($diem >= 4.0) $phanBoKhoang['4.0-4.9']++;
            else $phanBoKhoang['0-3.9']++;
        }

        return view('giangvien.ket-qua-hoc-tap.phan-tich', compact(
            'lopHocPhan',
            'thongKe',
            'phanBoDiemChu',
            'phanBoKhoang'
        ));
    }

    /**
     * Xuất bảng điểm Excel
     */
    public function exportExcel($id)
    {
        // TODO: Cài đặt package maatwebsite/excel
        // composer require maatwebsite/excel

        return redirect()->back()->with('error', 'Chức năng xuất Excel đang được phát triển');

        /*
        $giangVien = auth()->user()->giangVien;

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])
            ->whereHas('giangViens', function ($q) use ($giangVien) {
                $q->where('giang_vien_id', $giangVien->id);
            })
            ->findOrFail($id);

        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $id)->get();

        $danhSachSinhVien = \App\Models\LopHocPhanSinhVien::where('lop_hoc_phan_id', $id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien', 'ketQuaHocTap'])
            ->get();

        $fileName = 'bang_diem_' . $lopHocPhan->ma_lop_hp . '_' . date('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BangDiemExport($danhSachSinhVien, $lopHocPhan, $cauHinhs),
            $fileName
        );
        */
    }

    /**
     * Xuất bảng điểm PDF
     */
    public function exportPdf($id)
    {
        $giangVien = auth()->user()->giangVien;

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])
            ->whereHas('giangViens', function ($q) use ($giangVien) {
                $q->where('giang_vien_id', $giangVien->id);
            })
            ->findOrFail($id);

        $cauHinh = CauHinhDauDiem::where('lop_hoc_phan_id', $id)->first();

        $danhSachDiem = DangKyMonHoc::where('lop_hoc_phan_id', $id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien'])
            ->get()
            ->map(function ($dk) use ($cauHinh) {
                $nhapDiem = NhapDiem::where('dang_ky_mon_hoc_id', $dk->id)->first();
                $dk->diem = $nhapDiem;
                $dk->diem_tong_ket = $nhapDiem ? $this->tinhDiemTongKet($nhapDiem, $cauHinh) : null;
                return $dk;
            })
            ->sortBy('sinhVien.ho_ten');

        $pdf = PDF::loadView('giangvien.ket-qua-hoc-tap.pdf', compact(
            'lopHocPhan',
            'cauHinh',
            'danhSachDiem'
        ));

        $fileName = 'bang_diem_' . $lopHocPhan->ma_lop_hp . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Tính điểm tổng kết
     */
    private function tinhDiemTongKet($nhapDiem, $cauHinh)
    {
        $tongDiem = 0;
        $tongTyLe = 0;

        // Điểm chuyên cần
        if ($cauHinh->chuyen_can_ty_le > 0 && $nhapDiem->diem_chuyen_can !== null) {
            $tongDiem += $nhapDiem->diem_chuyen_can * $cauHinh->chuyen_can_ty_le / 100;
            $tongTyLe += $cauHinh->chuyen_can_ty_le;
        }

        // Điểm giữa kỳ
        if ($cauHinh->giua_ky_ty_le > 0 && $nhapDiem->diem_giua_ky !== null) {
            $tongDiem += $nhapDiem->diem_giua_ky * $cauHinh->giua_ky_ty_le / 100;
            $tongTyLe += $cauHinh->giua_ky_ty_le;
        }

        // Điểm cuối kỳ
        if ($cauHinh->cuoi_ky_ty_le > 0 && $nhapDiem->diem_cuoi_ky !== null) {
            $tongDiem += $nhapDiem->diem_cuoi_ky * $cauHinh->cuoi_ky_ty_le / 100;
            $tongTyLe += $cauHinh->cuoi_ky_ty_le;
        }

        // Điểm thực hành
        if ($cauHinh->thuc_hanh_ty_le > 0 && $nhapDiem->diem_thuc_hanh !== null) {
            $tongDiem += $nhapDiem->diem_thuc_hanh * $cauHinh->thuc_hanh_ty_le / 100;
            $tongTyLe += $cauHinh->thuc_hanh_ty_le;
        }

        // Điểm tiểu luận
        if ($cauHinh->tieu_luan_ty_le > 0 && $nhapDiem->diem_tieu_luan !== null) {
            $tongDiem += $nhapDiem->diem_tieu_luan * $cauHinh->tieu_luan_ty_le / 100;
            $tongTyLe += $cauHinh->tieu_luan_ty_le;
        }

        $diemHe10 = round($tongDiem, 2);
        $diemHe4 = $this->chuyenDoiDiemHe4($diemHe10);
        $diemChu = $this->chuyenDoiDiemChu($diemHe10);
        $quaMon = $diemHe10 >= 4.0;

        return [
            'diem_he_10' => $diemHe10,
            'diem_he_4' => $diemHe4,
            'diem_chu' => $diemChu,
            'qua_mon' => $quaMon,
            'ty_le_da_nhap' => $tongTyLe,
        ];
    }

    /**
     * Chuyển đổi điểm hệ 10 sang hệ 4
     */
    private function chuyenDoiDiemHe4($diemHe10)
    {
        if ($diemHe10 >= 8.5) return 4.0;
        if ($diemHe10 >= 8.0) return 3.5;
        if ($diemHe10 >= 7.0) return 3.0;
        if ($diemHe10 >= 6.5) return 2.5;
        if ($diemHe10 >= 5.5) return 2.0;
        if ($diemHe10 >= 5.0) return 1.5;
        if ($diemHe10 >= 4.0) return 1.0;
        return 0.0;
    }

    /**
     * Chuyển đổi điểm hệ 10 sang điểm chữ
     */
    private function chuyenDoiDiemChu($diemHe10)
    {
        if ($diemHe10 >= 8.5) return 'A';
        if ($diemHe10 >= 8.0) return 'B+';
        if ($diemHe10 >= 7.0) return 'B';
        if ($diemHe10 >= 6.5) return 'C+';
        if ($diemHe10 >= 5.5) return 'C';
        if ($diemHe10 >= 5.0) return 'D+';
        if ($diemHe10 >= 4.0) return 'D';
        return 'F';
    }

    /**
     * Thống kê phân bố điểm
     */
    private function thongKePhanBoDiem($danhSachDiem)
    {
        $phanBo = [
            'A' => 0,
            'B+' => 0,
            'B' => 0,
            'C+' => 0,
            'C' => 0,
            'D+' => 0,
            'D' => 0,
            'F' => 0,
        ];

        $tongSV = 0;
        $svQuaMon = 0;
        $tongDiem = 0;
        $diemCaoNhat = 0;
        $diemThapNhat = 10;

        foreach ($danhSachDiem as $dk) {
            if ($dk->diem_tong_ket) {
                $tongSV++;
                $diem = $dk->diem_tong_ket['diem_he_10'];
                $diemChu = $dk->diem_tong_ket['diem_chu'];

                $phanBo[$diemChu]++;
                $tongDiem += $diem;

                if ($dk->diem_tong_ket['qua_mon']) {
                    $svQuaMon++;
                }

                $diemCaoNhat = max($diemCaoNhat, $diem);
                $diemThapNhat = min($diemThapNhat, $diem);
            }
        }

        return [
            'phan_bo_diem_chu' => $phanBo,
            'tong_sinh_vien' => $tongSV,
            'sinh_vien_qua_mon' => $svQuaMon,
            'sinh_vien_truot' => $tongSV - $svQuaMon,
            'ty_le_qua_mon' => $tongSV > 0 ? round(($svQuaMon / $tongSV) * 100, 2) : 0,
            'diem_trung_binh' => $tongSV > 0 ? round($tongDiem / $tongSV, 2) : 0,
            'diem_cao_nhat' => $tongSV > 0 ? $diemCaoNhat : 0,
            'diem_thap_nhat' => $tongSV > 0 ? $diemThapNhat : 0,
        ];
    }

    /**
     * Kiểm tra đã nhập điểm chưa
     */
    private function checkDaNhapDiem($lopHocPhanId)
    {
        $tongSV = DangKyMonHoc::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->count();

        if ($tongSV == 0) return false;

        $daNhap = DangKyMonHoc::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->whereHas('nhapDiem')
            ->count();

        return $daNhap > 0;
    }

    /**
     * Xuất danh sách thi - Tự động thêm sinh viên đủ điều kiện vào lịch thi
     * Điều kiện: Điểm danh >= 80% VÀ Điểm CC >= 2/4
     */
    public function xuatDanhSachThi($lopHocPhanId)
    {
        try {
            \Log::info('Bắt đầu xuất danh sách thi', ['lop_hoc_phan_id' => $lopHocPhanId]);

            $giangVien = auth()->user()->giangVien;

            if (!$giangVien) {
                \Log::error('Không tìm thấy giảng viên');
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin giảng viên'
                ], 403);
            }

            // Lấy thông tin lớp học phần
            $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

            if (!$lopHocPhan) {
                \Log::error('Không tìm thấy lớp học phần', ['id' => $lopHocPhanId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy lớp học phần'
                ], 404);
            }

            \Log::info('Đang xử lý lớp học phần', [
                'ma_lop' => $lopHocPhan->ma_lop_hp,
                'mon_hoc' => $lopHocPhan->monHoc->ten_mon
            ]);

            // Lấy danh sách sinh viên
            $sinhViens = \App\Models\LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->with(['sinhVien', 'ketQuaHocTap'])
                ->get();

            $cauHinhs = \App\Models\CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
                ->orderBy('id')
                ->get();

            // Tìm cấu hình Chuyên cần
            $cauHinhCC = $cauHinhs->firstWhere('ten_dau_diem', 'Chuyên cần');

            $sinhVienDuDieuKien = [];

            foreach ($sinhViens as $lhpsv) {
                // 1. Kiểm tra tỷ lệ điểm danh >= 80%
                // Đếm tổng số buổi đã có điểm danh (unique theo lich_hoc_chi_tiet_id) của TẤT CẢ sinh viên trong lớp
                $tongBuoi = \App\Models\DiemDanh::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
                    $q->where('lop_hoc_phan_id', $lopHocPhanId);
                })
                    ->distinct('lich_hoc_chi_tiet_id')
                    ->count('lich_hoc_chi_tiet_id');

                // Đếm số buổi có mặt của sinh viên cụ thể
                $buoiCoMat = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                    ->where('trang_thai', 'co_mat')
                    ->count();

                // Tính tỷ lệ điểm danh
                $tyLeDiemDanh = $tongBuoi > 0 ? round(($buoiCoMat / $tongBuoi) * 100, 1) : 0;
                $duDieuKienDiemDanh = $tyLeDiemDanh >= 80;

                \Log::info('Debug điểm danh sinh viên', [
                    'sinh_vien_id' => $lhpsv->sinh_vien_id,
                    'ma_sv' => $lhpsv->sinhVien->ma_sinh_vien ?? 'N/A',
                    'tong_buoi' => $tongBuoi,
                    'buoi_co_mat' => $buoiCoMat,
                    'ty_le_diem_danh' => $tyLeDiemDanh,
                    'du_dieu_kien_diem_danh' => $duDieuKienDiemDanh
                ]);

                // 2. Kiểm tra điểm CC >= 2/4
                $diemCC = null;
                $duDieuKienDiemCC = false;

                if ($cauHinhCC) {
                    $diemCCRecord = \App\Models\NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                        ->where('cau_hinh_id', $cauHinhCC->id)
                        ->first();

                    if ($diemCCRecord) {
                        // Chuyển điểm hệ 10 sang hệ 4
                        $diemCCHe10 = $diemCCRecord->diem_so;
                        if ($diemCCHe10 >= 9.0) {
                            $diemCC = 4.0;
                        } elseif ($diemCCHe10 >= 8.5) {
                            $diemCC = 3.5;
                        } elseif ($diemCCHe10 >= 8.0) {
                            $diemCC = 3.0;
                        } elseif ($diemCCHe10 >= 7.0) {
                            $diemCC = 2.5;
                        } elseif ($diemCCHe10 >= 6.5) {
                            $diemCC = 2.0;
                        } elseif ($diemCCHe10 >= 5.5) {
                            $diemCC = 1.5;
                        } elseif ($diemCCHe10 >= 5.0) {
                            $diemCC = 1.0;
                        } else {
                            $diemCC = 0;
                        }

                        $duDieuKienDiemCC = $diemCC >= 2.0;
                    }
                }

                \Log::info('Debug điểm Chuyên cần', [
                    'sinh_vien_id' => $lhpsv->sinh_vien_id,
                    'ma_sv' => $lhpsv->sinhVien->ma_sinh_vien ?? 'N/A',
                    'co_cau_hinh_cc' => $cauHinhCC ? 'Có' : 'Không',
                    'diem_cc_he_4' => $diemCC,
                    'du_dieu_kien_diem_cc' => $duDieuKienDiemCC
                ]);

                // 3. Kiểm tra đủ điều kiện: Điểm danh >= 80% VÀ Điểm CC >= 2/4
                if ($duDieuKienDiemDanh && $duDieuKienDiemCC) {
                    $sinhVienDuDieuKien[] = $lhpsv->sinh_vien_id;
                    \Log::info('✅ Sinh viên ĐỦ điều kiện thi', [
                        'sinh_vien_id' => $lhpsv->sinh_vien_id,
                        'ma_sv' => $lhpsv->sinhVien->ma_sinh_vien ?? 'N/A'
                    ]);
                } else {
                    \Log::warning('❌ Sinh viên KHÔNG đủ điều kiện thi', [
                        'sinh_vien_id' => $lhpsv->sinh_vien_id,
                        'ma_sv' => $lhpsv->sinhVien->ma_sinh_vien ?? 'N/A',
                        'ly_do' => (!$duDieuKienDiemDanh ? 'Điểm danh < 80%' : '') .
                            (!$duDieuKienDiemCC ? ($duDieuKienDiemDanh ? '' : ' và ') . 'Điểm CC < 2/4' : '')
                    ]);
                }
            }

            // Tìm lịch thi của lớp học phần
            $lichThi = \App\Models\LichThi::where('lop_hoc_phan_id', $lopHocPhanId)
                ->orderBy('ngay_thi')
                ->first();

            if (!$lichThi) {
                \Log::warning('Chưa có lịch thi', ['lop_hoc_phan_id' => $lopHocPhanId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa có lịch thi cho lớp học phần này. Vui lòng liên hệ phòng đào tạo để tạo lịch thi.'
                ], 404);
            }

            \Log::info('Tìm thấy lịch thi', [
                'lich_thi_id' => $lichThi->id,
                'so_sinh_vien_du_dieu_kien' => count($sinhVienDuDieuKien)
            ]);

            // Thêm sinh viên đủ điều kiện vào danh sách thi
            DB::beginTransaction();

            $soLuongThem = 0;
            $soLuongDaTonTai = 0;

            foreach ($sinhVienDuDieuKien as $sinhVienId) {
                $daTonTai = \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThi->id)
                    ->where('sinh_vien_id', $sinhVienId)
                    ->exists();

                if ($daTonTai) {
                    $soLuongDaTonTai++;
                    continue;
                }

                \App\Models\LichThiSinhVien::create([
                    'lich_thi_id' => $lichThi->id,
                    'sinh_vien_id' => $sinhVienId,
                    'phong_thi_id' => $lichThi->phong_thi_id,
                    'trang_thai' => 'du_thi'
                ]);

                $soLuongThem++;
            }

            // Cập nhật số sinh viên dự thi
            $tongSinhVien = \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThi->id)->count();
            $lichThi->update(['so_sinh_vien_du_thi' => $tongSinhVien]);

            DB::commit();

            \Log::info('Xuất danh sách thi thành công', [
                'so_luong_them' => $soLuongThem,
                'so_luong_da_ton_tai' => $soLuongDaTonTai,
                'tong_sinh_vien' => $tongSinhVien
            ]);

            $message = "✓ Đã thêm <strong>{$soLuongThem} sinh viên</strong> đủ điều kiện vào danh sách thi.";
            if ($soLuongDaTonTai > 0) {
                $message .= "<br>→ {$soLuongDaTonTai} sinh viên đã có trong danh sách (giữ nguyên).";
            }
            $message .= "<br>→ Tổng cộng: <strong>{$tongSinhVien} sinh viên</strong> dự thi.";

            // Trả về JSON cho AJAX
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('giangvien.lich-thi.show', $lichThi->id),
                'data' => [
                    'so_luong_them' => $soLuongThem,
                    'so_luong_da_ton_tai' => $soLuongDaTonTai,
                    'tong_sinh_vien' => $tongSinhVien
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi xuất danh sách thi', [
                'lop_hoc_phan_id' => $lopHocPhanId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
