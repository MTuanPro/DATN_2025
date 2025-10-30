<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LopHocPhanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LopHocPhan::with(['monHoc', 'hocKy', 'giangVienChinh.giangVien']);

        // Lọc theo học kỳ
        if ($request->has('hoc_ky_id') && $request->hoc_ky_id != '') {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo môn học
        if ($request->has('mon_hoc_id') && $request->mon_hoc_id != '') {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai != '') {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop_hp', 'like', "%{$search}%")
                    ->orWhere('ten_lop_hp', 'like', "%{$search}%");
            });
        }

        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate(15);
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
        $monHocs = MonHoc::orderBy('ten_mon')->get();

        return view('daotao.lop-hoc-phan.index', compact('lopHocPhans', 'hocKys', 'monHocs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $monHocs = MonHoc::orderBy('ten_mon')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.lop-hoc-phan.create', compact('monHocs', 'hocKys'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_lop_hoc_phan' => 'required|string|max:20|unique:lop_hoc_phan,ma_lop_hoc_phan',
            'ten_lop_hoc_phan' => 'required|string|max:255',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'so_luong_toi_da' => 'required|integer|min:1|max:200',
            'so_luong_toi_thieu' => 'required|integer|min:1|lte:so_luong_toi_da',
            'loai_lop' => 'required|in:ly_thuyet,thuc_hanh,ly_thuyet_thuc_hanh',
            'hinh_thuc_hoc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'trang_thai' => 'required|in:cho_mo,dang_mo,dang_hoc,ket_thuc,huy',
            'ghi_chu' => 'nullable|string',
        ], [
            'ma_lop_hoc_phan.required' => 'Mã lớp học phần là bắt buộc',
            'ma_lop_hoc_phan.unique' => 'Mã lớp học phần đã tồn tại',
            'ten_lop_hoc_phan.required' => 'Tên lớp học phần là bắt buộc',
            'mon_hoc_id.required' => 'Môn học là bắt buộc',
            'mon_hoc_id.exists' => 'Môn học không tồn tại',
            'hoc_ky_id.required' => 'Học kỳ là bắt buộc',
            'hoc_ky_id.exists' => 'Học kỳ không tồn tại',
            'so_luong_toi_da.required' => 'Số lượng tối đa là bắt buộc',
            'so_luong_toi_da.min' => 'Số lượng tối đa phải lớn hơn 0',
            'so_luong_toi_da.max' => 'Số lượng tối đa không được vượt quá 200',
            'so_luong_toi_thieu.required' => 'Số lượng tối thiểu là bắt buộc',
            'so_luong_toi_thieu.lte' => 'Số lượng tối thiểu phải nhỏ hơn hoặc bằng số lượng tối đa',
            'loai_lop.required' => 'Loại lớp là bắt buộc',
            'hinh_thuc_hoc.required' => 'Hình thức học là bắt buộc',
            'link_online.url' => 'Link online phải là URL hợp lệ',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
        ]);

        LopHocPhan::create($validated);

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Tạo lớp học phần thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(LopHocPhan $lopHocPhan)
    {
        $lopHocPhan->load(['monHoc', 'hocKy', 'lopHocPhanGiangVien.giangVien', 'cauHinhDauDiem']);

        return view('daotao.lop-hoc-phan.show', compact('lopHocPhan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LopHocPhan $lopHocPhan)
    {
        $monHocs = MonHoc::orderBy('ten_mon')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.lop-hoc-phan.edit', compact('lopHocPhan', 'monHocs', 'hocKys'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LopHocPhan $lopHocPhan)
    {
        $validated = $request->validate([
            'ma_lop_hoc_phan' => 'required|string|max:20|unique:lop_hoc_phan,ma_lop_hoc_phan,' . $lopHocPhan->id,
            'ten_lop_hoc_phan' => 'required|string|max:255',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'so_luong_toi_da' => 'required|integer|min:1|max:200',
            'so_luong_toi_thieu' => 'required|integer|min:1|lte:so_luong_toi_da',
            'loai_lop' => 'required|in:ly_thuyet,thuc_hanh,ly_thuyet_thuc_hanh',
            'hinh_thuc_hoc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'trang_thai' => 'required|in:cho_mo,dang_mo,dang_hoc,ket_thuc,huy',
            'ghi_chu' => 'nullable|string',
        ], [
            'ma_lop_hoc_phan.required' => 'Mã lớp học phần là bắt buộc',
            'ma_lop_hoc_phan.unique' => 'Mã lớp học phần đã tồn tại',
            'ten_lop_hoc_phan.required' => 'Tên lớp học phần là bắt buộc',
            'mon_hoc_id.required' => 'Môn học là bắt buộc',
            'hoc_ky_id.required' => 'Học kỳ là bắt buộc',
            'so_luong_toi_da.required' => 'Số lượng tối đa là bắt buộc',
            'so_luong_toi_thieu.required' => 'Số lượng tối thiểu là bắt buộc',
            'so_luong_toi_thieu.lte' => 'Số lượng tối thiểu phải nhỏ hơn hoặc bằng số lượng tối đa',
            'loai_lop.required' => 'Loại lớp là bắt buộc',
            'hinh_thuc_hoc.required' => 'Hình thức học là bắt buộc',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
        ]);

        $lopHocPhan->update($validated);

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Cập nhật lớp học phần thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LopHocPhan $lopHocPhan)
    {
        // Kiểm tra xem lớp đã có sinh viên đăng ký chưa
        if ($lopHocPhan->so_luong_hien_tai > 0) {
            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('error', 'Không thể xóa lớp học phần đã có sinh viên đăng ký!');
        }

        $lopHocPhan->delete();

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Xóa lớp học phần thành công!');
    }
}
