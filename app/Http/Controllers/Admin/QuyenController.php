<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quyen;
use App\Models\NhomQuyen;
use Illuminate\Http\Request;

class QuyenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Quyen::with('nhomQuyen');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_quyen', 'like', "%{$search}%")
                    ->orWhere('ten_quyen', 'like', "%{$search}%")
                    ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        // Lọc theo nhóm quyền
        if ($request->filled('nhom_quyen_id')) {
            $query->where('nhom_quyen_id', $request->nhom_quyen_id);
        }

        $quyens = $query->orderBy('ma_quyen')->paginate(10);
        $nhomQuyens = NhomQuyen::orderBy('ten_nhom')->get();

        return view('admin.quyen.index', compact('quyens', 'nhomQuyens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nhomQuyens = NhomQuyen::orderBy('ten_nhom')->get();
        return view('admin.quyen.create', compact('nhomQuyens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_quyen' => 'required|string|max:100|unique:quyen,ma_quyen|alpha_dash',
            'ten_quyen' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'nhom_quyen_id' => 'required|exists:nhom_quyen,id',
        ], [
            'ma_quyen.required' => 'Mã quyền không được để trống',
            'ma_quyen.unique' => 'Mã quyền đã tồn tại',
            'ma_quyen.alpha_dash' => 'Mã quyền chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới',
            'ma_quyen.max' => 'Mã quyền không được quá 100 ký tự',
            'ten_quyen.required' => 'Tên quyền không được để trống',
            'ten_quyen.max' => 'Tên quyền không được quá 255 ký tự',
            'nhom_quyen_id.required' => 'Vui lòng chọn nhóm quyền',
            'nhom_quyen_id.exists' => 'Nhóm quyền không tồn tại',
        ]);

        Quyen::create($validated);

        return redirect()->route('quyen.index')
            ->with('success', 'Thêm quyền thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quyen $quyen)
    {
        $nhomQuyens = NhomQuyen::orderBy('ten_nhom')->get();
        return view('admin.quyen.edit', compact('quyen', 'nhomQuyens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quyen $quyen)
    {
        $validated = $request->validate([
            'ma_quyen' => 'required|string|max:100|alpha_dash|unique:quyen,ma_quyen,' . $quyen->id,
            'ten_quyen' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'nhom_quyen_id' => 'required|exists:nhom_quyen,id',
        ], [
            'ma_quyen.required' => 'Mã quyền không được để trống',
            'ma_quyen.unique' => 'Mã quyền đã tồn tại',
            'ma_quyen.alpha_dash' => 'Mã quyền chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới',
            'ma_quyen.max' => 'Mã quyền không được quá 100 ký tự',
            'ten_quyen.required' => 'Tên quyền không được để trống',
            'ten_quyen.max' => 'Tên quyền không được quá 255 ký tự',
            'nhom_quyen_id.required' => 'Vui lòng chọn nhóm quyền',
            'nhom_quyen_id.exists' => 'Nhóm quyền không tồn tại',
        ]);

        $quyen->update($validated);

        return redirect()->route('quyen.index')
            ->with('success', 'Cập nhật quyền thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quyen $quyen)
    {
        // Kiểm tra xem quyền có đang được gán cho vai trò nào không
        if ($quyen->vaiTros()->count() > 0) {
            return redirect()->route('quyen.index')
                ->with('error', 'Không thể xóa quyền đang được gán cho vai trò!');
        }

        $quyen->delete();

        return redirect()->route('quyen.index')
            ->with('success', 'Xóa quyền thành công!');
    }
}

