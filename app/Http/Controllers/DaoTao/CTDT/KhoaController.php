<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaoTao\Khoa;
use Illuminate\Support\Facades\Auth;

class KhoaController extends Controller
{
    // Danh sách khoa
   public function index(Request $request)
{
    // Kiểm tra quyền xem khoa
    if (!Auth::user()->hasPermission('khoa.xem')) {
        abort(403, 'Bạn không có quyền xem danh sách khoa');
    }

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
        // Kiểm tra quyền thêm khoa
        if (!Auth::user()->hasPermission('khoa.them')) {
            abort(403, 'Bạn không có quyền thêm khoa');
        }

        $validated = $request->validate([
            'ma_khoa' => 'required|unique:khoa,ma_khoa|max:50',
            'ten_khoa' => 'required|unique:khoa,ten_khoa|max:255',
            'truong_khoa_id' => 'nullable|integer|exists:giang_vien,id',
            'mo_ta' => 'nullable|string|max:1000',
        ], [
            'ma_khoa.required' => 'Mã khoa không được để trống',
            'ma_khoa.unique' => 'Mã khoa đã tồn tại',
            'ma_khoa.max' => 'Mã khoa không được quá 50 ký tự',
            'ten_khoa.required' => 'Tên khoa không được để trống',
            'ten_khoa.unique' => 'Tên khoa đã tồn tại',
            'ten_khoa.max' => 'Tên khoa không được quá 255 ký tự',
            'truong_khoa_id.integer' => 'Trưởng khoa phải là số nguyên hợp lệ',
            'truong_khoa_id.exists' => 'Giảng viên được chọn không tồn tại',
            'mo_ta.max' => 'Mô tả không được quá 1000 ký tự',
        ]);

        // Chuyển chuỗi rỗng thành null cho truong_khoa_id
        if (isset($validated['truong_khoa_id']) && $validated['truong_khoa_id'] === '') {
            $validated['truong_khoa_id'] = null;
        }

        // Chỉ lấy các trường đã validate
        Khoa::create($validated);
        return redirect()->route('dao-tao.khoa.index')->with('success', 'Thêm khoa thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        // Kiểm tra quyền sửa khoa
        if (!Auth::user()->hasPermission('khoa.sua')) {
            abort(403, 'Bạn không có quyền sửa khoa');
        }

        $khoa = Khoa::findOrFail($id);
        return view('daotao.khoa.edit', compact('khoa'));
    }
public function create()
{
    // Kiểm tra quyền thêm khoa
    if (!Auth::user()->hasPermission('khoa.them')) {
        abort(403, 'Bạn không có quyền thêm khoa');
    }

    // Trả về view tạo mới khoa
    return view('daotao.khoa.create');
}
    // Cập nhật
    public function update(Request $request, $id)
    {
        // Kiểm tra quyền sửa khoa
        if (!Auth::user()->hasPermission('khoa.sua')) {
            abort(403, 'Bạn không có quyền sửa khoa');
        }

        $khoa = Khoa::findOrFail($id);

        $validated = $request->validate([
            'ma_khoa' => 'required|max:50|unique:khoa,ma_khoa,' . $khoa->id,
            'ten_khoa' => 'required|max:255|unique:khoa,ten_khoa,' . $khoa->id,
            'truong_khoa_id' => 'nullable|integer|exists:giang_vien,id',
            'mo_ta' => 'nullable|string|max:1000',
        ], [
            'ma_khoa.required' => 'Mã khoa không được để trống',
            'ma_khoa.unique' => 'Mã khoa đã tồn tại',
            'ma_khoa.max' => 'Mã khoa không được quá 50 ký tự',
            'ten_khoa.required' => 'Tên khoa không được để trống',
            'ten_khoa.unique' => 'Tên khoa đã tồn tại',
            'ten_khoa.max' => 'Tên khoa không được quá 255 ký tự',
            'truong_khoa_id.integer' => 'Trưởng khoa phải là số nguyên hợp lệ',
            'truong_khoa_id.exists' => 'Giảng viên được chọn không tồn tại',
            'mo_ta.max' => 'Mô tả không được quá 1000 ký tự',
        ]);

        // Chuyển chuỗi rỗng thành null cho truong_khoa_id
        if (isset($validated['truong_khoa_id']) && $validated['truong_khoa_id'] === '') {
            $validated['truong_khoa_id'] = null;
        }

        // Chỉ cập nhật các trường đã validate
        $khoa->update($validated);
        return redirect()->route('dao-tao.khoa.index')->with('success', 'Cập nhật khoa thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        // Kiểm tra quyền xóa khoa
        if (!Auth::user()->hasPermission('khoa.xoa')) {
            abort(403, 'Bạn không có quyền xóa khoa');
        }

        Khoa::destroy($id);
        return redirect()->route('dao-tao.khoa.index')->with('success', 'Xóa khoa thành công!');
    }
}
