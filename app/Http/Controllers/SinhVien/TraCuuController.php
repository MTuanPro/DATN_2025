<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\GiangVien;
use App\Models\Daotao\PhongHoc;
use App\Models\HocKy;
use App\Models\Daotao\MonHoc;
use App\Models\Daotao\Khoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraCuuController extends Controller
{
    /**
     * Tra cứu học phần
     */
    public function traHocPhan(Request $request)
    {
        $query = LopHocPhan::with([
            'monHoc',
            'hocKy',
            'giangVienChinh.giangVien',
        ]);

        // Tìm kiếm theo mã lớp HP, tên lớp HP, mã môn, tên môn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop_hp', 'like', "%{$search}%")
                    ->orWhere('ten_lop_hp', 'like', "%{$search}%")
                    ->orWhereHas('monHoc', function ($q2) use ($search) {
                        $q2->where('ma_mon', 'like', "%{$search}%")
                            ->orWhere('ten_mon', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo môn học
        if ($request->filled('mon_hoc_id')) {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        // Lọc theo trạng thái lớp
        if ($request->filled('trang_thai_lop')) {
            $query->where('trang_thai_lop', $request->trang_thai_lop);
        }

        // Lọc theo hình thức (trực tiếp/online)
        if ($request->filled('hinh_thuc')) {
            $query->where('hinh_thuc', $request->hinh_thuc);
        }

        // Chỉ hiển thị các lớp đang mở đăng ký hoặc đang học
        if (!$request->filled('trang_thai_lop')) {
            $query->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc']);
        }

        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Lấy danh sách học kỳ và môn học cho filter
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        $monHocs = MonHoc::orderBy('ten_mon')->get();

        return view('sinhvien.tra-cuu.hoc-phan', compact('lopHocPhans', 'hocKys', 'monHocs'));
    }

    /**
     * Tra cứu giảng viên
     */
    public function traGiangVien(Request $request)
    {
        $query = GiangVien::with([
            'khoa',
            'trinhDo',
        ]);

        // Tìm kiếm theo mã giảng viên, tên, email, số điện thoại
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_giang_vien', 'like', "%{$search}%")
                    ->orWhere('ho_ten', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$search}%");
            });
        }

        // Lọc theo khoa
        if ($request->filled('khoa_id')) {
            $query->where('khoa_id', $request->khoa_id);
        }

        // Lọc theo trình độ
        if ($request->filled('trinh_do_id')) {
            $query->where('trinh_do_id', $request->trinh_do_id);
        }

        // Lọc theo giới tính
        if ($request->filled('gioi_tinh')) {
            $query->where('gioi_tinh', $request->gioi_tinh);
        }

        $giangViens = $query->orderBy('ho_ten')->paginate(15);

        // Lấy danh sách khoa và trình độ cho filter
        $khoas = Khoa::orderBy('ten_khoa')->get();
        $trinhDos = \App\Models\DanhMuc\TrinhDo::orderBy('ten_trinh_do')->get();

        return view('sinhvien.tra-cuu.giang-vien', compact('giangViens', 'khoas', 'trinhDos'));
    }

    /**
     * Tra cứu phòng học
     */
    public function traPhongHoc(Request $request)
    {
        $query = PhongHoc::query();

        // Tìm kiếm theo mã phòng, tên phòng, vị trí
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_phong', 'like', "%{$search}%")
                    ->orWhere('ten_phong', 'like', "%{$search}%")
                    ->orWhere('vi_tri', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại phòng
        if ($request->filled('loai_phong')) {
            $query->where('loai_phong', $request->loai_phong);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo sức chứa (tối thiểu)
        if ($request->filled('suc_chua_min')) {
            $query->where('suc_chua', '>=', $request->suc_chua_min);
        }

        $phongHocs = $query->orderBy('ten_phong')->paginate(15);

        return view('sinhvien.tra-cuu.phong-hoc', compact('phongHocs'));
    }
}

