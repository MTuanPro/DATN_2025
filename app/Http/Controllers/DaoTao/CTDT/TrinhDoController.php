<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\TrinhDo;
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
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ten_trinh_do', 'LIKE', "%{$search}%");
        }

        $trinhDos = $query->latest()->paginate(15);

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
            'ten_trinh_do.required' => 'Tên trình độ là bắt buộc',
            'ten_trinh_do.unique' => 'Tên trình độ đã tồn tại',
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
        $trinhDo = TrinhDo::findOrFail($id);
        return view('daotao.trinh-do.show', compact('trinhDo'));
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
            'ten_trinh_do.required' => 'Tên trình độ là bắt buộc',
            'ten_trinh_do.unique' => 'Tên trình độ đã tồn tại',
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
        try {
            $trinhDo = TrinhDo::findOrFail($id);
            $trinhDo->delete();

            return redirect()->route('dao-tao.trinh-do.index')
                ->with('success', 'Xóa trình độ thành công!');
        } catch (\Exception $e) {
            return redirect()->route('dao-tao.trinh-do.index')
                ->with('error', 'Không thể xóa trình độ. Trình độ đang được sử dụng.');
        }
    }
}
