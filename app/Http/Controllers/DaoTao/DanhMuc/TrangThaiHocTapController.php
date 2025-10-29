<?php

namespace App\Http\Controllers\DaoTao\DanhMuc;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc\TrangThaiHocTap;
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
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_trang_thai', 'like', "%{$keyword}%")
                    ->orWhere('mo_ta', 'like', "%{$keyword}%");
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $trangThais = $query->paginate(10);

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
            'ten_trang_thai.required' => 'Tên trạng thái không được để trống',
            'ten_trang_thai.unique' => 'Tên trạng thái đã tồn tại',
            'ten_trang_thai.max' => 'Tên trạng thái không được vượt quá 255 ký tự',
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
        //
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
            'ten_trang_thai.required' => 'Tên trạng thái không được để trống',
            'ten_trang_thai.unique' => 'Tên trạng thái đã tồn tại',
            'ten_trang_thai.max' => 'Tên trạng thái không được vượt quá 255 ký tự',
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
        $trangThai = TrangThaiHocTap::findOrFail($id);
        $trangThai->delete(); // Soft delete

        return redirect()->route('dao-tao.trang-thai-hoc-tap.index')
            ->with('success', 'Xóa trạng thái học tập thành công!');
    }
}
