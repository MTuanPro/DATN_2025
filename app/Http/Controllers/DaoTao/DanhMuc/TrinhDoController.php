<?php

namespace App\Http\Controllers\DaoTao\DanhMuc;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc\TrinhDo;
use Illuminate\Http\Request;

class TrinhDoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TrinhDo::query();

        // Tìm kiếm
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('ten_trinh_do', 'like', "%{$keyword}%");
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $trinhDos = $query->paginate(10);

        return view('daotao.trinh-do.index', compact('trinhDos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daotao.trinh-do.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_trinh_do' => 'required|string|max:255|unique:dm_trinh_do,ten_trinh_do',
        ], [
            'ten_trinh_do.required' => 'Tên trình độ không được để trống',
            'ten_trinh_do.unique' => 'Tên trình độ đã tồn tại',
            'ten_trinh_do.max' => 'Tên trình độ không được vượt quá 255 ký tự',
        ]);

        TrinhDo::create($validated);

        return redirect()->route('dao-tao.trinh-do.index')
            ->with('success', 'Thêm trình độ thành công!');
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
        $trinhDo = TrinhDo::findOrFail($id);
        return view('daotao.trinh-do.edit', compact('trinhDo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $trinhDo = TrinhDo::findOrFail($id);

        $validated = $request->validate([
            'ten_trinh_do' => 'required|string|max:255|unique:dm_trinh_do,ten_trinh_do,' . $id,
        ], [
            'ten_trinh_do.required' => 'Tên trình độ không được để trống',
            'ten_trinh_do.unique' => 'Tên trình độ đã tồn tại',
            'ten_trinh_do.max' => 'Tên trình độ không được vượt quá 255 ký tự',
        ]);

        $trinhDo->update($validated);

        return redirect()->route('dao-tao.trinh-do.index')
            ->with('success', 'Cập nhật trình độ thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $trinhDo = TrinhDo::findOrFail($id);
        $trinhDo->delete(); // Soft delete

        return redirect()->route('dao-tao.trinh-do.index')
            ->with('success', 'Xóa trình độ thành công!');
    }
}
