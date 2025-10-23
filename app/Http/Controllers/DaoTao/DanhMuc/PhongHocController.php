<?php

namespace App\Http\Controllers\DaoTao\DanhMuc;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc\PhongHoc;
use Illuminate\Http\Request;

class PhongHocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PhongHoc::query();

        // Tìm kiếm
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('ma_phong', 'like', "%{$keyword}%")
                    ->orWhere('ten_phong', 'like', "%{$keyword}%")
                    ->orWhere('vi_tri', 'like', "%{$keyword}%");
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

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'ma_phong');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $phongHocs = $query->paginate(15);

        return view('daotao.phong-hoc.index', compact('phongHocs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daotao.phong-hoc.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_phong' => 'required|string|max:50|unique:phong_hoc,ma_phong',
            'ten_phong' => 'required|string|max:255',
            'suc_chua' => 'nullable|integer|min:1|max:500',
            'vi_tri' => 'nullable|string|max:255',
            'loai_phong' => 'nullable|string|max:100',
            'trang_thai' => 'nullable|string|max:100',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_phong.required' => 'Mã phòng không được để trống',
            'ma_phong.unique' => 'Mã phòng đã tồn tại',
            'ten_phong.required' => 'Tên phòng không được để trống',
            'suc_chua.integer' => 'Sức chứa phải là số nguyên',
            'suc_chua.min' => 'Sức chứa tối thiểu là 1',
            'suc_chua.max' => 'Sức chứa tối đa là 500',
        ]);

        PhongHoc::create($validated);

        return redirect()->route('dao-tao.phong-hoc.index')
            ->with('success', 'Thêm phòng học thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $phongHoc = PhongHoc::findOrFail($id);
        return view('daotao.phong-hoc.edit', compact('phongHoc'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $phongHoc = PhongHoc::findOrFail($id);

        $validated = $request->validate([
            'ma_phong' => 'required|string|max:50|unique:phong_hoc,ma_phong,' . $id,
            'ten_phong' => 'required|string|max:255',
            'suc_chua' => 'nullable|integer|min:1|max:500',
            'vi_tri' => 'nullable|string|max:255',
            'loai_phong' => 'nullable|string|max:100',
            'trang_thai' => 'nullable|string|max:100',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_phong.required' => 'Mã phòng không được để trống',
            'ma_phong.unique' => 'Mã phòng đã tồn tại',
            'ten_phong.required' => 'Tên phòng không được để trống',
            'suc_chua.integer' => 'Sức chứa phải là số nguyên',
            'suc_chua.min' => 'Sức chứa tối thiểu là 1',
            'suc_chua.max' => 'Sức chứa tối đa là 500',
        ]);

        $phongHoc->update($validated);

        return redirect()->route('dao-tao.phong-hoc.index')
            ->with('success', 'Cập nhật phòng học thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $phongHoc = PhongHoc::findOrFail($id);
        $phongHoc->delete(); // Soft delete

        return redirect()->route('dao-tao.phong-hoc.index')
            ->with('success', 'Xóa phòng học thành công!');
    }
}
