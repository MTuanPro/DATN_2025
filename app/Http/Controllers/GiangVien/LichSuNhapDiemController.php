<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LichSuSuaDiem;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use App\Models\PhanCongGiangDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LichSuNhapDiemController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử nhập điểm của giảng viên hiện tại
     */
    public function index(Request $request)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Chỉ lấy lịch sử của giảng viên hiện tại
        $query = LichSuSuaDiem::where('nguoi_sua_id', Auth::id())
            ->with([
                'lopHocPhanSinhVien.sinhVien',
                'lopHocPhanSinhVien.lopHocPhan.monHoc',
                'lopHocPhanSinhVien.lopHocPhan.hocKy',
                'cauHinh',
            ]);

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->whereHas('lopHocPhanSinhVien.lopHocPhan', function ($q) use ($request) {
                $q->where('hoc_ky_id', $request->hoc_ky_id);
            });
        }

        // Lọc theo lớp học phần (chỉ các lớp giảng viên được phân công)
        if ($request->filled('lop_hoc_phan_id')) {
            $lopHocPhanId = $request->lop_hoc_phan_id;
            // Kiểm tra giảng viên có được phân công lớp này không
            $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
                ->where('giang_vien_id', $giangVien->id)
                ->exists();
            
            if ($duocPhanCong) {
                $query->whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
                    $q->where('lop_hoc_phan_id', $lopHocPhanId);
                });
            }
        }

        // Lọc theo loại thao tác
        if ($request->filled('loai_thao_tac')) {
            $query->where('loai_thao_tac', $request->loai_thao_tac);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }

        // Tìm kiếm theo mã sinh viên hoặc tên
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('lopHocPhanSinhVien.sinhVien', function ($q) use ($keyword) {
                $q->where('ma_sinh_vien', 'like', "%{$keyword}%")
                  ->orWhere('ho_ten', 'like', "%{$keyword}%");
            });
        }

        $lichSu = $query->orderBy('created_at', 'desc')->paginate(30);

        // Lấy danh sách học kỳ để filter
        $hocKys = HocKy::orderBy('ngay_bat_dau', 'desc')->get();

        // Lấy danh sách lớp học phần mà giảng viên được phân công (nếu đã chọn học kỳ)
        $lopHocPhans = [];
        if ($request->filled('hoc_ky_id')) {
            $lopHocPhanIds = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
                ->pluck('lop_hoc_phan_id');
            
            $lopHocPhans = LopHocPhan::whereIn('id', $lopHocPhanIds)
                ->where('hoc_ky_id', $request->hoc_ky_id)
                ->with('monHoc')
                ->get();
        }

        return view('giangvien.lich-su-nhap-diem.index', compact('lichSu', 'hocKys', 'lopHocPhans'));
    }

    /**
     * Hiển thị chi tiết lịch sử nhập điểm của một lớp học phần
     */
    public function show($lopHocPhanId, Request $request)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Kiểm tra giảng viên có được phân công lớp này không
        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->exists();

        if (!$duocPhanCong) {
            return redirect()->route('giangvien.lich-su-nhap-diem.index')
                ->with('error', 'Bạn không có quyền xem lịch sử nhập điểm của lớp học phần này');
        }

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])
            ->findOrFail($lopHocPhanId);

        $query = LichSuSuaDiem::where('nguoi_sua_id', Auth::id())
            ->whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
                $q->where('lop_hoc_phan_id', $lopHocPhanId);
            })
            ->with([
                'lopHocPhanSinhVien.sinhVien',
                'cauHinh',
            ]);

        // Lọc theo sinh viên
        if ($request->filled('sinh_vien_id')) {
            $query->whereHas('lopHocPhanSinhVien', function ($q) use ($request) {
                $q->where('sinh_vien_id', $request->sinh_vien_id);
            });
        }

        // Lọc theo loại thao tác
        if ($request->filled('loai_thao_tac')) {
            $query->where('loai_thao_tac', $request->loai_thao_tac);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }

        $lichSu = $query->orderBy('created_at', 'desc')->paginate(30);

        return view('giangvien.lich-su-nhap-diem.show', compact('lopHocPhan', 'lichSu'));
    }
}

