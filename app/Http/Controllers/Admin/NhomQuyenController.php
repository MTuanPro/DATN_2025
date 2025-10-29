<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhomQuyen;
use Illuminate\Http\Request;

class NhomQuyenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NhomQuyen::withCount('quyens');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_nhom', 'like', "%{$search}%")
                    ->orWhere('ten_nhom', 'like', "%{$search}%")
                    ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        $nhomQuyens = $query->orderBy('ma_nhom')->paginate(10);

        return view('admin.nhom-quyen.index', compact('nhomQuyens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.nhom-quyen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_nhom' => 'required|string|max:50|unique:nhom_quyen,ma_nhom|alpha_dash',
            'ten_nhom' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_nhom.required' => 'Mã nhóm quyền không được để trống',
            'ma_nhom.unique' => 'Mã nhóm quyền đã tồn tại',
            'ma_nhom.alpha_dash' => 'Mã nhóm quyền chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới',
            'ma_nhom.max' => 'Mã nhóm quyền không được quá 50 ký tự',
            'ten_nhom.required' => 'Tên nhóm quyền không được để trống',
            'ten_nhom.max' => 'Tên nhóm quyền không được quá 255 ký tự',
        ]);

        NhomQuyen::create($validated);

        return redirect()->route('admin.nhom-quyen.index')
            ->with('success', 'Thêm nhóm quyền thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NhomQuyen $nhomQuyen)
    {
        $nhomQuyen->loadCount('quyens');
        return view('admin.nhom-quyen.edit', compact('nhomQuyen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NhomQuyen $nhomQuyen)
    {
        $validated = $request->validate([
            'ma_nhom' => 'required|string|max:50|alpha_dash|unique:nhom_quyen,ma_nhom,' . $nhomQuyen->id,
            'ten_nhom' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_nhom.required' => 'Mã nhóm quyền không được để trống',
            'ma_nhom.unique' => 'Mã nhóm quyền đã tồn tại',
            'ma_nhom.alpha_dash' => 'Mã nhóm quyền chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới',
            'ma_nhom.max' => 'Mã nhóm quyền không được quá 50 ký tự',
            'ten_nhom.required' => 'Tên nhóm quyền không được để trống',
            'ten_nhom.max' => 'Tên nhóm quyền không được quá 255 ký tự',
        ]);

        $nhomQuyen->update($validated);

        return redirect()->route('admin.nhom-quyen.index')
            ->with('success', 'Cập nhật nhóm quyền thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NhomQuyen $nhomQuyen)
    {
        // Kiểm tra xem nhóm quyền có đang được sử dụng không
        if ($nhomQuyen->quyens()->count() > 0) {
            return redirect()->route('admin.nhom-quyen.index')
                ->with('error', 'Không thể xóa nhóm quyền đang có quyền!');
        }

        $nhomQuyen->delete();

        return redirect()->route('admin.nhom-quyen.index')
            ->with('success', 'Xóa nhóm quyền thành công!');
    }
}
