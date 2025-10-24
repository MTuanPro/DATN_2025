<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaoTao\Nganh;
use App\Models\DaoTao\Khoa;

class NganhController extends Controller
{
    public function index(Request $request)
    {
        $query = Nganh::with('khoa');

        // 🔍 Tìm kiếm theo mã hoặc tên ngành
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('ma_nganh', 'like', "%{$keyword}%")
                    ->orWhere('ten_nganh', 'like', "%{$keyword}%");
            });
        }

        // 🧭 Lọc theo Khoa (nếu chọn)
        if ($request->filled('khoa_id')) {
            $query->where('khoa_id', $request->khoa_id);
        }

        // 🔽 Sắp xếp
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        } else {
            $query->orderBy('id', 'desc');
        }

        // 📄 Phân trang
        $nganhs = $query->paginate(10);
        $khoas = Khoa::orderBy('ten_khoa')->get();

        return view('daotao.nganh.index', compact('nganhs', 'khoas'));
    }

    public function create()
    {
        $khoas = Khoa::all();
        return view('daotao.nganh.create', compact('khoas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_nganh' => 'required|unique:nganh,ma_nganh|max:50',
            'ten_nganh' => 'required|max:255',
            'khoa_id' => 'nullable|exists:khoa,id',
        ]);

        Nganh::create($request->all());
        return redirect()->route('dao-tao.nganh.index')->with('success', 'Thêm ngành thành công!');
    }

    public function edit($id)
    {
        $nganh = Nganh::findOrFail($id);
        $khoas = Khoa::all();
        return view('daotao.nganh.edit', compact('nganh', 'khoas'));
    }

    public function update(Request $request, $id)
    {
        $nganh = Nganh::findOrFail($id);

        $request->validate([
            'ma_nganh' => 'required|max:50|unique:nganh,ma_nganh,' . $nganh->id,
            'ten_nganh' => 'required|max:255',
            'khoa_id' => 'nullable|exists:khoa,id',
        ]);

        $nganh->update($request->all());
        return redirect()->route('dao-tao.nganh.index')->with('success', 'Cập nhật ngành thành công!');
    }

    public function destroy($id)
    {
        $nganh = Nganh::findOrFail($id);
        $nganh->delete();
        return redirect()->route('dao-tao.nganh.index')->with('success', 'Xóa ngành thành công!');
    }
}
