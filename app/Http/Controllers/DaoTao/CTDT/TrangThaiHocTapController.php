<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\TrangThaiHocTap;
use Illuminate\Http\Request;

class TrangThaiHocTapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TrangThaiHocTap::query();

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ten_trang_thai', 'LIKE', "%{$search}%");
        }

        $trangThais = $query->latest()->paginate(15);

        return view('daotao.trang-thai-hoc-tap.index', compact('trangThais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daotao.trang-thai-hoc-tap.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_trang_thai' => 'required|string|max:255|unique:trang_thai_hoc_tap,ten_trang_thai',
            'mo_ta' => 'nullable|string',
        ], [
            'ten_trang_thai.required' => 'Tên trạng thái là bắt buộc',
            'ten_trang_thai.unique' => 'Tên trạng thái đã tồn tại',
        ]);

        TrangThaiHocTap::create($validated);

        return redirect()->route('dao-tao.trang-thai-hoc-tap.index')
            ->with('success', 'Thêm trạng thái học tập thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trangThai = TrangThaiHocTap::findOrFail($id);
        return view('daotao.trang-thai-hoc-tap.show', compact('trangThai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $trangThai = TrangThaiHocTap::findOrFail($id);
        return view('daotao.trang-thai-hoc-tap.edit', compact('trangThai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $trangThai = TrangThaiHocTap::findOrFail($id);

        $validated = $request->validate([
            'ten_trang_thai' => 'required|string|max:255|unique:trang_thai_hoc_tap,ten_trang_thai,' . $id,
            'mo_ta' => 'nullable|string',
        ], [
            'ten_trang_thai.required' => 'Tên trạng thái là bắt buộc',
            'ten_trang_thai.unique' => 'Tên trạng thái đã tồn tại',
        ]);

        $trangThai->update($validated);

        return redirect()->route('dao-tao.trang-thai-hoc-tap.index')
            ->with('success', 'Cập nhật trạng thái học tập thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $trangThai = TrangThaiHocTap::findOrFail($id);
            $trangThai->delete();

            return redirect()->route('dao-tao.trang-thai-hoc-tap.index')
                ->with('success', 'Xóa trạng thái học tập thành công!');
        } catch (\Exception $e) {
            return redirect()->route('dao-tao.trang-thai-hoc-tap.index')
                ->with('error', 'Không thể xóa trạng thái. Trạng thái đang được sử dụng.');
        }
    }
}
