<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\CanhBaoHocVu;
use App\Models\GiangVien;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\SinhVien;
use App\Models\LopHocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CanhBaoHocVuController extends Controller
{
    /**
     * Hiển thị danh sách cảnh báo học vụ
     * của sinh viên trong lớp giảng dạy + lớp chủ nhiệm
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $giangVien = $user->giangVien ?? GiangVien::where('user_id', $user->id)->first();

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách sinh viên liên quan đến giảng viên
        $sinhVienIds = $this->getSinhVienRelatedToGiangVien($giangVien);

        // Query cảnh báo
        $query = CanhBaoHocVu::with(['sinhVien.user', 'nguoiTao', 'nguoiXuLy'])
            ->whereIn('sinh_vien_id', $sinhVienIds)
            ->orderBy('created_at', 'desc');

        // Filter theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Filter theo mức độ
        if ($request->filled('muc_do')) {
            $query->where('muc_do', $request->muc_do);
        }

        // Filter theo loại
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }

        // Search theo tên/mã sinh viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->where('ma_sinh_vien', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $canhBaoList = $query->paginate(15);

        // Thống kê
        $tongCanhBao = CanhBaoHocVu::whereIn('sinh_vien_id', $sinhVienIds)->count();
        $chuaXuLy = CanhBaoHocVu::whereIn('sinh_vien_id', $sinhVienIds)
            ->where('trang_thai', 'chua_xu_ly')->count();
        $dangXuLy = CanhBaoHocVu::whereIn('sinh_vien_id', $sinhVienIds)
            ->where('trang_thai', 'dang_xu_ly')->count();
        $daXuLy = CanhBaoHocVu::whereIn('sinh_vien_id', $sinhVienIds)
            ->where('trang_thai', 'da_xu_ly')->count();

        return view('giangvien.canh-bao-hoc-vu.index', compact(
            'canhBaoList',
            'tongCanhBao',
            'chuaXuLy',
            'dangXuLy',
            'daXuLy',
            'giangVien'
        ));
    }

    /**
     * Hiển thị chi tiết cảnh báo
     */
    public function show($id)
    {
        $user = auth()->user();
        $giangVien = $user->giangVien ?? GiangVien::where('user_id', $user->id)->first();

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách sinh viên liên quan
        $sinhVienIds = $this->getSinhVienRelatedToGiangVien($giangVien);

        // Lấy cảnh báo
        $canhBao = CanhBaoHocVu::with([
            'sinhVien.user',
            'sinhVien.lopHanhChinh',
            'sinhVien.ketQuaHocTaps',
            'sinhVien.diemDanh',
            'nguoiTao',
            'nguoiXuLy'
        ])->whereIn('sinh_vien_id', $sinhVienIds)
            ->findOrFail($id);

        // Lấy lịch sử cảnh báo của sinh viên này
        $lichSuCanhBao = CanhBaoHocVu::where('sinh_vien_id', $canhBao->sinh_vien_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('giangvien.canh-bao-hoc-vu.show', compact(
            'canhBao',
            'lichSuCanhBao',
            'giangVien'
        ));
    }

    /**
     * Lấy danh sách ID sinh viên liên quan đến giảng viên
     * (trong lớp giảng dạy + lớp chủ nhiệm)
     */
    private function getSinhVienRelatedToGiangVien($giangVien)
    {
        $sinhVienIds = [];

        // 1. Sinh viên trong các lớp học phần giảng viên dạy
        // Lấy lớp học phần từ bảng trung gian lop_hoc_phan_giang_vien
        $lopHocPhanIds = DB::table('lop_hoc_phan_giang_vien')
            ->where('giang_vien_id', $giangVien->id)
            ->pluck('lop_hoc_phan_id');

        if ($lopHocPhanIds->isNotEmpty()) {
            $svTrongLopHocPhan = DB::table('lop_hoc_phan_sinh_vien')
                ->whereIn('lop_hoc_phan_id', $lopHocPhanIds)
                ->pluck('sinh_vien_id')
                ->toArray();
            $sinhVienIds = array_merge($sinhVienIds, $svTrongLopHocPhan);
        }

        // 2. Sinh viên trong lớp hành chính giảng viên làm chủ nhiệm
        $lopChuNhiem = LopHanhChinh::where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->first();

        if ($lopChuNhiem) {
            $svTrongLopChuNhiem = SinhVien::where('lop_hanh_chinh_id', $lopChuNhiem->id)
                ->pluck('id')
                ->toArray();
            $sinhVienIds = array_merge($sinhVienIds, $svTrongLopChuNhiem);
        }

        // Loại bỏ trùng lặp
        return array_unique($sinhVienIds);
    }
}
