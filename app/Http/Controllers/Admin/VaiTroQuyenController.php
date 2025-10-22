<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VaiTro;
use App\Models\NhomQuyen;
use App\Models\Quyen;
use Illuminate\Http\Request;

class VaiTroQuyenController extends Controller
{
    /**
     * Hiển thị ma trận vai trò - quyền
     */
    public function index()
    {
        $vaiTros = VaiTro::with('quyens')->orderBy('muc_do_uu_tien')->get();
        $nhomQuyens = NhomQuyen::with('quyens')->orderBy('ma_nhom')->get();

        // Tạo ma trận quyền theo vai trò
        $matrix = [];
        foreach ($vaiTros as $vaiTro) {
            $matrix[$vaiTro->id] = $vaiTro->quyens->pluck('id')->toArray();
        }

        return view('admin.vai-tro-quyen.index', compact('vaiTros', 'nhomQuyens', 'matrix'));
    }

    /**
     * Cập nhật quyền cho vai trò
     */
    public function update(Request $request, VaiTro $vaiTro)
    {
        $validated = $request->validate([
            'quyens' => 'nullable|array',
            'quyens.*' => 'exists:quyen,id',
        ], [
            'quyens.array' => 'Dữ liệu quyền không hợp lệ',
            'quyens.*.exists' => 'Quyền không tồn tại',
        ]);

        // Sync quyền cho vai trò
        if (isset($validated['quyens'])) {
            $vaiTro->quyens()->sync($validated['quyens']);
        } else {
            $vaiTro->quyens()->detach();
        }

        return redirect()->route('admin.vai-tro-quyen.index')
            ->with('success', "Cập nhật quyền cho vai trò \"{$vaiTro->ten_vai_tro}\" thành công!");
    }

    /**
     * Cập nhật toàn bộ ma trận quyền
     */
    public function updateMatrix(Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
        ]);

        foreach ($validated['permissions'] as $vaiTroId => $quyenIds) {
            $vaiTro = VaiTro::find($vaiTroId);
            if ($vaiTro) {
                $vaiTro->quyens()->sync($quyenIds ?? []);
            }
        }

        return redirect()->route('admin.vai-tro-quyen.index')
            ->with('success', 'Cập nhật ma trận quyền thành công!');
    }

    /**
     * Xóa quyền khỏi vai trò
     */
    public function detachPermission(VaiTro $vaiTro, Quyen $quyen)
    {
        $vaiTro->quyens()->detach($quyen->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa quyền khỏi vai trò',
        ]);
    }

    /**
     * Gán quyền cho vai trò
     */
    public function attachPermission(VaiTro $vaiTro, Quyen $quyen)
    {
        if (!$vaiTro->quyens()->where('quyen.id', $quyen->id)->exists()) {
            $vaiTro->quyens()->attach($quyen->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gán quyền cho vai trò',
        ]);
    }
}
