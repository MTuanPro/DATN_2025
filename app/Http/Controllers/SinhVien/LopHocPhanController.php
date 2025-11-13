<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhanSinhVien;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            // Mặc định chỉ hiển thị các lớp đang học hoặc đã xếp lớp
            $query->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);
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
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->count(),
            'dang_hoc' => LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                ->when($selectedHocKy, function ($q) use ($selectedHocKy) {
                    $q->whereHas('lopHocPhan', function ($q2) use ($selectedHocKy) {
                        $q2->where('hoc_ky_id', $selectedHocKy->id);
                    });
                })
                ->where('trang_thai', 'dang_hoc')
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
}

