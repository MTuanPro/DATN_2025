<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\CauHinhDauDiem;
use App\Models\LopHocPhan;
use App\Models\PhanCongGiangDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CauHinhDiemController extends Controller
{
    /**
     * Danh sách lớp học phần có thể cấu hình điểm
     */
    public function index(Request $request)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách lớp được phân công (chỉ GV chính mới được cấu hình)
        $query = LopHocPhan::with(['monHoc', 'hocKy'])
            ->whereHas('giangViens', function ($q) use ($giangVien) {
                $q->where('giang_vien_id', $giangVien->id)
                  ->where('vai_tro', 'giang_vien_chinh'); // Chỉ GV chính
            });

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop_hp', 'LIKE', "%{$search}%")
                    ->orWhere('ten_lop_hp', 'LIKE', "%{$search}%");
            });
        }

        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Kiểm tra đã cấu hình chưa
        foreach ($lopHocPhans as $lop) {
            $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lop->id)->get();
            $lop->da_cau_hinh = $cauHinhs->isNotEmpty();
            $lop->tong_ty_le = $cauHinhs->sum('ty_le');
            $lop->so_dau_diem = $cauHinhs->count();
        }

        $hocKys = \App\Models\HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        return view('giangvien.cau-hinh-diem.index', compact('lopHocPhans', 'hocKys'));
    }

    /**
     * Xem chi tiết cấu hình điểm của lớp
     */
    public function show($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền cấu hình điểm lớp này');
        }

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($lopHocPhanId);

        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        $tongTyLe = $cauHinhs->sum('ty_le');
        $hoanThien = $tongTyLe == 100;

        return view('giangvien.cau-hinh-diem.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'tongTyLe',
            'hoanThien'
        ));
    }

    /**
     * Form tạo cấu hình mới
     */
    public function create($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền cấu hình điểm lớp này');
        }

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($lopHocPhanId);

        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)->sum('ty_le');
        $tyLeConLai = 100 - $tongTyLe;

        return view('giangvien.cau-hinh-diem.create', compact('lopHocPhan', 'tyLeConLai'));
    }

    /**
     * Lưu cấu hình mới
     */
    public function store(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền cấu hình điểm lớp này');
        }

        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:100',
            'ty_le' => 'required|numeric|min:1|max:100',
            'so_cot' => 'required|integer|min:1|max:10',
        ], [
            'ten_dau_diem.required' => 'Vui lòng nhập tên đầu điểm',
            'ty_le.required' => 'Vui lòng nhập tỷ lệ %',
            'ty_le.min' => 'Tỷ lệ phải từ 1% trở lên',
            'ty_le.max' => 'Tỷ lệ không được vượt quá 100%',
            'so_cot.required' => 'Vui lòng nhập số cột',
            'so_cot.min' => 'Số cột phải từ 1 trở lên',
            'so_cot.max' => 'Số cột không được vượt quá 10',
        ]);

        // Kiểm tra tổng tỷ lệ
        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)->sum('ty_le');
        
        if ($tongTyLe + $validated['ty_le'] > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tổng tỷ lệ không được vượt quá 100%. Tỷ lệ còn lại: ' . (100 - $tongTyLe) . '%');
        }

        try {
            DB::beginTransaction();

            CauHinhDauDiem::create([
                'lop_hoc_phan_id' => $lopHocPhanId,
                'ten_dau_diem' => $validated['ten_dau_diem'],
                'ty_le' => $validated['ty_le'],
                'so_cot' => $validated['so_cot'],
            ]);

            DB::commit();

            return redirect()->route('giangvien.cau-hinh-diem.show', $lopHocPhanId)
                ->with('success', 'Đã thêm cấu hình đầu điểm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Form sửa cấu hình
     */
    public function edit($id)
    {
        $giangVien = Auth::user()->giangVien;

        $cauHinh = CauHinhDauDiem::with(['lopHocPhan.monHoc', 'nhapDiems'])->findOrFail($id);
        $lopHocPhan = $cauHinh->lopHocPhan;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa');
        }

        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('id', '!=', $id)
            ->sum('ty_le');
        
        $tyLeConLai = 100 - $tongTyLe;

        return view('giangvien.cau-hinh-diem.edit', compact('cauHinh', 'lopHocPhan', 'tyLeConLai'));
    }

    /**
     * Cập nhật cấu hình
     */
    public function update(Request $request, $id)
    {
        $giangVien = Auth::user()->giangVien;

        $cauHinh = CauHinhDauDiem::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa');
        }

        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:100',
            'ty_le' => 'required|numeric|min:1|max:100',
            'so_cot' => 'required|integer|min:1|max:10',
        ]);

        // Kiểm tra tổng tỷ lệ
        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('id', '!=', $id)
            ->sum('ty_le');
        
        if ($tongTyLe + $validated['ty_le'] > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tổng tỷ lệ không được vượt quá 100%');
        }

        try {
            DB::beginTransaction();

            $cauHinh->update($validated);

            DB::commit();

            return redirect()->route('giangvien.cau-hinh-diem.show', $cauHinh->lop_hoc_phan_id)
                ->with('success', 'Đã cập nhật cấu hình thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa cấu hình
     */
    public function destroy($id)
    {
        $giangVien = Auth::user()->giangVien;

        $cauHinh = CauHinhDauDiem::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền xóa');
        }

        try {
            DB::beginTransaction();

            $lopHocPhanId = $cauHinh->lop_hoc_phan_id;
            $cauHinh->delete();

            DB::commit();

            return redirect()->route('giangvien.cau-hinh-diem.show', $lopHocPhanId)
                ->with('success', 'Đã xóa cấu hình thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
