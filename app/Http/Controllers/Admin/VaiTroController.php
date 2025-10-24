<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VaiTro;
use Illuminate\Http\Request;

class VaiTroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VaiTro::withCount('users');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_vai_tro', 'like', "%{$search}%")
                    ->orWhere('ten_vai_tro', 'like', "%{$search}%")
                    ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        $vaiTros = $query->orderBy('muc_do_uu_tien')->paginate(10);

        return view('admin.vai-tro.index', compact('vaiTros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vai-tro.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_vai_tro' => 'required|string|max:50|unique:vai_tro,ma_vai_tro|alpha_dash',
            'ten_vai_tro' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'muc_do_uu_tien' => 'required|integer|min:1|max:100',
        ], [
            'ma_vai_tro.required' => 'Mã vai trò không được để trống',
            'ma_vai_tro.unique' => 'Mã vai trò đã tồn tại',
            'ma_vai_tro.alpha_dash' => 'Mã vai trò chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới',
            'ma_vai_tro.max' => 'Mã vai trò không được quá 50 ký tự',
            'ten_vai_tro.required' => 'Tên vai trò không được để trống',
            'ten_vai_tro.max' => 'Tên vai trò không được quá 255 ký tự',
            'muc_do_uu_tien.required' => 'Mức độ ưu tiên không được để trống',
            'muc_do_uu_tien.integer' => 'Mức độ ưu tiên phải là số nguyên',
            'muc_do_uu_tien.min' => 'Mức độ ưu tiên tối thiểu là 1',
            'muc_do_uu_tien.max' => 'Mức độ ưu tiên tối đa là 100',
        ]);

        VaiTro::create($validated);

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Thêm vai trò thành công!');
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
    public function edit(VaiTro $vaiTro)
    {
        $vaiTro->loadCount('users');
        return view('admin.vai-tro.edit', compact('vaiTro'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VaiTro $vaiTro)
    {
        $validated = $request->validate([
            'ma_vai_tro' => 'required|string|max:50|alpha_dash|unique:vai_tro,ma_vai_tro,' . $vaiTro->id,
            'ten_vai_tro' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'muc_do_uu_tien' => 'required|integer|min:1|max:100',
        ], [
            'ma_vai_tro.required' => 'Mã vai trò không được để trống',
            'ma_vai_tro.unique' => 'Mã vai trò đã tồn tại',
            'ma_vai_tro.alpha_dash' => 'Mã vai trò chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới',
            'ma_vai_tro.max' => 'Mã vai trò không được quá 50 ký tự',
            'ten_vai_tro.required' => 'Tên vai trò không được để trống',
            'ten_vai_tro.max' => 'Tên vai trò không được quá 255 ký tự',
            'muc_do_uu_tien.required' => 'Mức độ ưu tiên không được để trống',
            'muc_do_uu_tien.integer' => 'Mức độ ưu tiên phải là số nguyên',
            'muc_do_uu_tien.min' => 'Mức độ ưu tiên tối thiểu là 1',
            'muc_do_uu_tien.max' => 'Mức độ ưu tiên tối đa là 100',
        ]);

        $vaiTro->update($validated);

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Cập nhật vai trò thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VaiTro $vaiTro)
    {
        // Kiểm tra xem vai trò có đang được sử dụng không
        if ($vaiTro->users()->count() > 0) {
            return redirect()->route('admin.vai-tro.index')
                ->with('error', 'Không thể xóa vai trò đang có người dùng!');
        }

        $vaiTro->delete();

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Xóa vai trò thành công!');
    }
}
