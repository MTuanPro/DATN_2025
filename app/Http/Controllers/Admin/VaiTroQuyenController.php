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
     * Chỉ hiển thị các quyền phù hợp với actor của vai trò
     */
    public function index()
    {
        $vaiTros = VaiTro::with('quyens')->orderBy('muc_do_uu_tien')->get();

        // Lấy nhóm quyền với các quyền được filter theo actor của từng vai trò
        $nhomQuyens = NhomQuyen::with(['quyens' => function ($query) {
            $query->with('actors');
        }])->orderBy('ma_nhom')->get();

        // Tạo ma trận quyền theo vai trò
        $matrix = [];
        foreach ($vaiTros as $vaiTro) {
            $matrix[$vaiTro->id] = $vaiTro->quyens->pluck('id')->toArray();
        }

        // Actors để hiển thị thông tin
        $actors = Quyen::ACTORS;

        return view('admin.vai-tro-quyen.index', compact('vaiTros', 'nhomQuyens', 'matrix', 'actors'));
    }

    /**
     * Cập nhật quyền cho vai trò
     * Kiểm tra quyền có phù hợp với actor của vai trò không
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

        // Lọc chỉ những quyền phù hợp với actor của vai trò
        $validQuyenIds = [];
        if (isset($validated['quyens']) && $vaiTro->actor) {
            $validQuyenIds = Quyen::whereIn('id', $validated['quyens'])
                ->forActor($vaiTro->actor)
                ->pluck('id')
                ->toArray();
        } elseif (isset($validated['quyens'])) {
            // Nếu vai trò chưa có actor, cho phép tất cả quyền (để tương thích ngược)
            $validQuyenIds = $validated['quyens'];
        }

        $vaiTro->quyens()->sync($validQuyenIds);

        return redirect()->route('admin.vai-tro-quyen.index')
            ->with('success', "Cập nhật quyền cho vai trò \"{$vaiTro->ten_vai_tro}\" thành công!");
    }

    /**
     * Cập nhật toàn bộ ma trận quyền
     * Kiểm tra từng quyền có phù hợp với actor của vai trò tương ứng không
     */
    public function updateMatrix(Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:vai_tro,id',
        ]);

        // Lấy danh sách role_ids từ form (những vai trò được hiển thị)
        $roleIds = $validated['role_ids'] ?? [];
        
        // Nếu không có role_ids, lấy tất cả vai trò
        $vaiTros = !empty($roleIds) 
            ? VaiTro::whereIn('id', $roleIds)->get()
            : VaiTro::all();

        foreach ($vaiTros as $vaiTro) {
            // Lấy quyền từ request, nếu không có thì là array rỗng
            $quyenIds = $validated['permissions'][$vaiTro->id] ?? [];
            
            // Lọc chỉ những quyền phù hợp với actor của vai trò
            if ($vaiTro->actor && !empty($quyenIds)) {
                // Lấy tất cả quyền được chọn
                $allSelectedQuyens = Quyen::whereIn('id', $quyenIds)->with('actors')->get();
                
                // Lọc: CHỈ giữ lại quyền có actor khớp với vai trò (strict)
                $validQuyenIds = $allSelectedQuyens->filter(function ($quyen) use ($vaiTro) {
                    $quyenActors = $quyen->actors->pluck('actor')->toArray();
                    // Chỉ cho phép quyền có actor khớp với vai trò
                    return !empty($quyenActors) && in_array($vaiTro->actor, $quyenActors);
                })->pluck('id')->toArray();
                
                $vaiTro->quyens()->sync($validQuyenIds);
            } else {
                // Sync với array rỗng nếu vai trò không có actor hoặc không có quyền nào được chọn
                $vaiTro->quyens()->sync([]);
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
     * Kiểm tra quyền có phù hợp với actor của vai trò không
     */
    public function attachPermission(VaiTro $vaiTro, Quyen $quyen)
    {
        // Kiểm tra quyền có phù hợp với actor của vai trò không
        if ($vaiTro->actor && !$quyen->belongsToActor($vaiTro->actor)) {
            return response()->json([
                'success' => false,
                'message' => 'Quyền này không áp dụng cho nhóm người dùng của vai trò này',
            ], 400);
        }

        if (!$vaiTro->quyens()->where('quyen.id', $quyen->id)->exists()) {
            $vaiTro->quyens()->attach($quyen->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gán quyền cho vai trò',
        ]);
    }
}
