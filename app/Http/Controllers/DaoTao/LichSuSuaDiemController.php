<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LichSuSuaDiem;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use App\Models\CauHinhDauDiem;
use Illuminate\Http\Request;

class LichSuSuaDiemController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử sửa điểm
     */
    public function index(Request $request)
    {
        $query = LichSuSuaDiem::with([
            'lopHocPhanSinhVien.sinhVien',
            'lopHocPhanSinhVien.lopHocPhan.monHoc',
            'lopHocPhanSinhVien.lopHocPhan.hocKy',
            'cauHinh',
            'nguoiSua.giangVien'
        ]);

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->whereHas('lopHocPhanSinhVien.lopHocPhan', function ($q) use ($request) {
                $q->where('hoc_ky_id', $request->hoc_ky_id);
            });
        }

        // Lọc theo lớp học phần
        if ($request->filled('lop_hoc_phan_id')) {
            $query->whereHas('lopHocPhanSinhVien', function ($q) use ($request) {
                $q->where('lop_hoc_phan_id', $request->lop_hoc_phan_id);
            });
        }

        // Lọc theo giảng viên
        if ($request->filled('giang_vien_id')) {
            $query->where('nguoi_sua_id', $request->giang_vien_id);
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

        // Chỉ lấy những bản ghi có điểm thay đổi (điểm cũ khác điểm mới)
        $query->whereRaw('diem_cu != diem_moi');

        $lichSu = $query->orderBy('created_at', 'desc')->paginate(50);

        // Lấy danh sách học kỳ để filter
        $hocKys = HocKy::orderBy('ngay_bat_dau', 'desc')->get();

        // Lấy danh sách lớp học phần (nếu đã chọn học kỳ)
        $lopHocPhans = [];
        if ($request->filled('hoc_ky_id')) {
            $lopHocPhans = LopHocPhan::where('hoc_ky_id', $request->hoc_ky_id)
                ->with('monHoc')
                ->get();
        }

        return view('daotao.lich-su-sua-diem.index', compact('lichSu', 'hocKys', 'lopHocPhans'));
    }

    /**
     * Hiển thị chi tiết lịch sử sửa điểm của một lớp học phần
     */
    public function show($lopHocPhanId, Request $request)
    {
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'giangVien'])
            ->findOrFail($lopHocPhanId);

        $query = LichSuSuaDiem::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
                $q->where('lop_hoc_phan_id', $lopHocPhanId);
            })
            ->with([
                'lopHocPhanSinhVien.sinhVien',
                'cauHinh',
                'nguoiSua.giangVien'
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

        // Chỉ lấy những bản ghi có điểm thay đổi (điểm cũ khác điểm mới)
        $query->whereRaw('diem_cu != diem_moi');

        $lichSu = $query->orderBy('created_at', 'desc')->paginate(30);

        return view('daotao.lich-su-sua-diem.show', compact('lopHocPhan', 'lichSu'));
    }
}
