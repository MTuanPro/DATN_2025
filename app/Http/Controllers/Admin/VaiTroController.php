<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VaiTro;
use Illuminate\Http\Request;

class VaiTroController extends Controller
{
    /**
     * Hiển thị danh sách vai trò
     */
    public function index(Request $request)
    {
        $query = VaiTro::query()->withCount('users');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_vai_tro', 'like', "%{$search}%")
                    ->orWhere('ma_vai_tro', 'like', "%{$search}%")
                    ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        // Sắp xếp theo mức độ ưu tiên
        $vaiTros = $query->orderBy('muc_do_uu_tien', 'asc')
            ->orderBy('ten_vai_tro', 'asc')
            ->paginate(15);

        return view('admin.vai-tro.index', compact('vaiTros'));
    }

    /**
     * Hiển thị form tạo vai trò mới
     */
    public function create()
    {
        return view('admin.vai-tro.create');
    }

    /**
     * Lưu vai trò mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_vai_tro' => 'required|string|max:50|unique:vai_tro,ma_vai_tro|alpha_dash',
            'ten_vai_tro' => 'required|string|max:100',
            'mo_ta' => 'nullable|string|max:500',
            'muc_do_uu_tien' => 'required|integer|min:1|max:100',
        ], [
            'ma_vai_tro.required' => 'Mã vai trò không được để trống',
            'ma_vai_tro.unique' => 'Mã vai trò đã tồn tại',
            'ma_vai_tro.alpha_dash' => 'Mã vai trò chỉ chứa chữ cái, số, gạch ngang và gạch dưới',
            'ten_vai_tro.required' => 'Tên vai trò không được để trống',
            'muc_do_uu_tien.required' => 'Mức độ ưu tiên không được để trống',
            'muc_do_uu_tien.min' => 'Mức độ ưu tiên tối thiểu là 1',
            'muc_do_uu_tien.max' => 'Mức độ ưu tiên tối đa là 100',
        ]);

        VaiTro::create($validated);

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Thêm vai trò mới thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa vai trò
     */
    public function edit(VaiTro $vaiTro)
    {
        $vaiTro->loadCount('users');
        return view('admin.vai-tro.edit', compact('vaiTro'));
    }

    /**
     * Cập nhật vai trò
     */
    public function update(Request $request, VaiTro $vaiTro)
    {
        $validated = $request->validate([
            'ma_vai_tro' => 'required|string|max:50|alpha_dash|unique:vai_tro,ma_vai_tro,' . $vaiTro->id,
            'ten_vai_tro' => 'required|string|max:100',
            'mo_ta' => 'nullable|string|max:500',
            'muc_do_uu_tien' => 'required|integer|min:1|max:100',
        ], [
            'ma_vai_tro.required' => 'Mã vai trò không được để trống',
            'ma_vai_tro.unique' => 'Mã vai trò đã tồn tại',
            'ma_vai_tro.alpha_dash' => 'Mã vai trò chỉ chứa chữ cái, số, gạch ngang và gạch dưới',
            'ten_vai_tro.required' => 'Tên vai trò không được để trống',
            'muc_do_uu_tien.required' => 'Mức độ ưu tiên không được để trống',
            'muc_do_uu_tien.min' => 'Mức độ ưu tiên tối thiểu là 1',
            'muc_do_uu_tien.max' => 'Mức độ ưu tiên tối đa là 100',
        ]);

        $vaiTro->update($validated);

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Cập nhật vai trò thành công!');
    }

    /**
     * Xóa vai trò
     */
    public function destroy(VaiTro $vaiTro)
    {
        // Kiểm tra xem vai trò có user nào đang sử dụng không
        if ($vaiTro->users()->count() > 0) {
            return back()->with('error', 'Không thể xóa vai trò này vì đang có ' . $vaiTro->users()->count() . ' người dùng!');
        }

        $vaiTro->delete();

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Xóa vai trò thành công!');
    }
}
