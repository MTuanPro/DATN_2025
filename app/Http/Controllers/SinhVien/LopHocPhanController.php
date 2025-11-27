<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhanSinhVien;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use App\Models\DiemDanh;
use App\Models\LichHocChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LopHocPhanController extends Controller
{
    /**
     * Hiển thị danh sách lớp học phần của sinh viên
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        // Lọc theo học kỳ
        $hocKyId = $request->get('hoc_ky_id');
        $selectedHocKy = null;

        if ($hocKyId) {
            $selectedHocKy = HocKy::find($hocKyId);
        } else {
            // Mặc định lấy học kỳ hiện tại
            $selectedHocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        }

        // Query lớp học phần
        $query = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'lopHocPhan.giangVienChinh.giangVien',
                'lopHocPhan.lichHocCoDinhs.phongHoc',
                'lopHocPhan.lichHocCoDinhs.giangVien',
                'lopHocPhan.lichHocCoDinhs.caHoc',
                'ketQuaHocTap',
            ]);

        // Lọc theo học kỳ
        if ($selectedHocKy) {
            $query->whereHas('lopHocPhan', function ($q) use ($selectedHocKy) {
                $q->where('hoc_ky_id', $selectedHocKy->id);
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        } else {
            // Mặc định chỉ hiển thị các lớp đang học, đã xếp lớp, học lại
            $query->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh', 'hoc_lai']);
        }

        $lopHocPhanSinhViens = $query->orderBy('ngay_dang_ky', 'desc')
            ->paginate(15);

        // Thống kê
        $thongKe = [
            'tong_lop' => LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                ->when($selectedHocKy, function ($q) use ($selectedHocKy) {
                    $q->whereHas('lopHocPhan', function ($q2) use ($selectedHocKy) {
                        $q2->where('hoc_ky_id', $selectedHocKy->id);
                    });
                })
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh', 'hoc_lai'])
                ->count(),
            'dang_hoc' => LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                ->when($selectedHocKy, function ($q) use ($selectedHocKy) {
                    $q->whereHas('lopHocPhan', function ($q2) use ($selectedHocKy) {
                        $q2->where('hoc_ky_id', $selectedHocKy->id);
                    });
                })
                ->whereIn('trang_thai', ['dang_hoc', 'hoc_lai'])
                ->count(),
            'da_hoan_thanh' => LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                ->when($selectedHocKy, function ($q) use ($selectedHocKy) {
                    $q->whereHas('lopHocPhan', function ($q2) use ($selectedHocKy) {
                        $q2->where('hoc_ky_id', $selectedHocKy->id);
                    });
                })
                ->where('trang_thai', 'da_hoan_thanh')
                ->count(),
        ];

        return view('sinhvien.lop-hoc-phan.index', compact(
            'lopHocPhanSinhViens',
            'hocKys',
            'selectedHocKy',
            'thongKe',
            'sinhVien'
        ));
    }

    /**
     * Xem chi tiết lớp học phần
     */
    public function show($id)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy thông tin lớp học phần của sinh viên
        $lopHocPhanSinhVien = LopHocPhanSinhVien::where('id', $id)
            ->where('sinh_vien_id', $sinhVien->id)
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'lopHocPhan.giangVienChinh.giangVien',
                'lopHocPhan.lichHocCoDinhs.phongHoc',
                'lopHocPhan.lichHocCoDinhs.giangVien',
                'lopHocPhan.lichHocCoDinhs.caHoc',
                'ketQuaHocTap',
                'dangKyTam',
            ])
            ->firstOrFail();

        // Lấy lịch học cố định (đã load trong with)
        $lichHocCoDinh = $lopHocPhanSinhVien->lopHocPhan->lichHocCoDinhs;

        // Lấy kết quả học tập
        $ketQuaHocTap = $lopHocPhanSinhVien->ketQuaHocTap;

        return view('sinhvien.lop-hoc-phan.show', compact(
            'lopHocPhanSinhVien',
            'lichHocCoDinh',
            'ketQuaHocTap',
            'sinhVien'
        ));
    }

    /**
     * Xem lịch sử điểm danh theo lớp học phần
     */
    public function lichSuDiemDanh($id)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy thông tin lớp học phần của sinh viên
        $lopHocPhanSinhVien = LopHocPhanSinhVien::where('id', $id)
            ->where('sinh_vien_id', $sinhVien->id)
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'lopHocPhan.giangVienChinh.giangVien',
            ])
            ->firstOrFail();

        // Lấy lịch sử điểm danh
        $diemDanhs = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
            ->with(['lichHocChiTiet.phongHoc', 'lichHocChiTiet.giangVien'])
            ->join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
            ->select('diem_danh.*', 'lich_hoc_chi_tiet.ngay_hoc', 'lich_hoc_chi_tiet.tiet_bat_dau', 'lich_hoc_chi_tiet.tiet_ket_thuc')
            ->orderBy('lich_hoc_chi_tiet.ngay_hoc', 'desc')
            ->orderBy('lich_hoc_chi_tiet.tiet_bat_dau', 'desc')
            ->paginate(20);

        // Thống kê điểm danh
        $thongKe = [
            'tong_buoi' => DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)->count(),
            'co_mat' => DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
                ->where('trang_thai', 'co_mat')
                ->count(),
            'vang' => DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
                ->where('trang_thai', 'vang')
                ->count(),
            'di_tre' => DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
                ->where('trang_thai', 'di_tre')
                ->count(),
            'nghi_phep' => DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
                ->where('trang_thai', 'nghi_phep')
                ->count(),
        ];

        // Tính tỷ lệ chuyên cần
        $tyLeChuyenCan = $thongKe['tong_buoi'] > 0
            ? round(($thongKe['co_mat'] / $thongKe['tong_buoi']) * 100, 2)
            : 0;

        $thongKe['ty_le_chuyen_can'] = $tyLeChuyenCan;

        return view('sinhvien.lop-hoc-phan.lich-su-diem-danh', compact(
            'lopHocPhanSinhVien',
            'diemDanhs',
            'thongKe',
            'sinhVien'
        ));
    }

    /**
     * Xem tổng hợp lịch sử điểm danh tất cả các lớp
     */
    public function tongHopDiemDanh(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        // Lấy học kỳ được chọn (mặc định là học kỳ hiện tại)
        $hocKyId = $request->hoc_ky_id ?? HocKy::where('la_hoc_ky_hien_tai', true)->first()?->id;

        // Lấy tất cả lớp học phần của sinh viên trong học kỳ
        $lopHocPhans = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'lopHocPhan.giangVienChinh.giangVien'
            ])
            ->when($hocKyId, function ($query) use ($hocKyId) {
                $query->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                    $q->where('hoc_ky_id', $hocKyId);
                });
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh', 'hoc_lai'])
            ->get();

        // Lấy thống kê điểm danh cho từng lớp
        $thongKeData = [];
        foreach ($lopHocPhans as $lhp) {
            $tongBuoi = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhp->id)->count();
            $coMat = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhp->id)
                ->where('trang_thai', 'co_mat')->count();
            $vang = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhp->id)
                ->where('trang_thai', 'vang')->count();
            $diTre = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhp->id)
                ->where('trang_thai', 'di_tre')->count();
            $nghiPhep = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhp->id)
                ->where('trang_thai', 'nghi_phep')->count();
            
            $tyLe = $tongBuoi > 0 ? round(($coMat / $tongBuoi) * 100, 2) : 0;

            $thongKeData[$lhp->id] = [
                'tong_buoi' => $tongBuoi,
                'co_mat' => $coMat,
                'vang' => $vang,
                'di_tre' => $diTre,
                'nghi_phep' => $nghiPhep,
                'ty_le' => $tyLe
            ];
        }

        // Lấy danh sách yêu cầu điểm danh bù đã gửi
        $yeuCauDiemDanhBuIds = \App\Models\YeuCauDiemDanhBu::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhans->pluck('id'))
            ->pluck('lich_hoc_chi_tiet_id', 'lop_hoc_phan_sinh_vien_id')
            ->toArray();

        return view('sinhvien.diem-danh.index', compact(
            'lopHocPhans',
            'thongKeData',
            'hocKys',
            'hocKyId',
            'sinhVien',
            'yeuCauDiemDanhBuIds'
        ));
    }
}


