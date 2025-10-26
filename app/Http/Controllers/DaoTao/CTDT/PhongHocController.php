<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\PhongHoc;
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
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_phong', 'LIKE', "%{$search}%")
                    ->orWhere('ten_phong', 'LIKE', "%{$search}%")
                    ->orWhere('vi_tri', 'LIKE', "%{$search}%");
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

        $phongHocs = $query->latest()->paginate(15);

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
            'suc_chua' => 'nullable|integer|min:1',
            'vi_tri' => 'nullable|string|max:255',
            'loai_phong' => 'nullable|string|max:100',
            'trang_thai' => 'required|in:Hoạt động,Bảo trì,Không sử dụng',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_phong.required' => 'Mã phòng là bắt buộc',
            'ma_phong.unique' => 'Mã phòng đã tồn tại',
            'ten_phong.required' => 'Tên phòng là bắt buộc',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'suc_chua.min' => 'Sức chứa phải lớn hơn 0',
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
        $phongHoc = PhongHoc::findOrFail($id);
        return view('daotao.phong-hoc.show', compact('phongHoc'));
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
            'suc_chua' => 'nullable|integer|min:1',
            'vi_tri' => 'nullable|string|max:255',
            'loai_phong' => 'nullable|string|max:100',
            'trang_thai' => 'required|in:Hoạt động,Bảo trì,Không sử dụng',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_phong.required' => 'Mã phòng là bắt buộc',
            'ma_phong.unique' => 'Mã phòng đã tồn tại',
            'ten_phong.required' => 'Tên phòng là bắt buộc',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'suc_chua.min' => 'Sức chứa phải lớn hơn 0',
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
        try {
            $phongHoc = PhongHoc::findOrFail($id);
            $phongHoc->delete();

            return redirect()->route('dao-tao.phong-hoc.index')
                ->with('success', 'Xóa phòng học thành công!');
        } catch (\Exception $e) {
            return redirect()->route('dao-tao.phong-hoc.index')
                ->with('error', 'Không thể xóa phòng học. Phòng học đang được sử dụng.');
        }
    }
}
