<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaoTao\Khoa;

class KhoaController extends Controller
{
    // Danh sách khoa
    public function index(Request $request)
    {
        $query = Khoa::query();

        // Tìm kiếm theo mã hoặc tên khoa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('ma_khoa', 'like', "%{$keyword}%")
                    ->orWhere('ten_khoa', 'like', "%{$keyword}%");
            });
        }

        // Bộ lọc theo trạng thái (nếu có cột status hoặc tương tự)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sắp xếp theo yêu cầu (tuỳ chọn)
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        } else {
            $query->orderBy('id', 'desc');
        }

        $khoas = $query->paginate(10); // phân trang
        return view('daotao.khoa.index', compact('khoas'));
    }
    // Lưu dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'ma_khoa' => 'required|unique:khoa,ma_khoa|max:50',
            'ten_khoa' => 'required|unique:khoa,ten_khoa|max:255',
        ]);

        Khoa::create($request->all());
        return redirect()->route('dao-tao.khoa.index')->with('success', 'Thêm khoa thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $khoa = Khoa::findOrFail($id);
        return view('daotao.khoa.edit', compact('khoa'));
    }
    public function create()
    {
        // Trả về view tạo mới khoa
        return view('daotao.khoa.create');
    }
    // Cập nhật
    public function update(Request $request, $id)
    {
        $khoa = Khoa::findOrFail($id);

        $request->validate([
            'ma_khoa' => 'required|max:50|unique:khoa,ma_khoa,' . $khoa->id,
            'ten_khoa' => 'required|max:255|unique:khoa,ten_khoa,' . $khoa->id,
        ]);

        $khoa->update($request->all());
        return redirect()->route('dao-tao.khoa.index')->with('success', 'Cập nhật khoa thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $khoa = Khoa::findOrFail($id);
        $khoa->delete();
        return redirect()->route('dao-tao.khoa.index')->with('success', 'Xóa khoa thành công!');
    }
}
