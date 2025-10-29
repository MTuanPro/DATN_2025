<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\KhoaHoc;
use Illuminate\Http\Request;

class KhoaHocController extends Controller
{
    // Xem danh sách
    public function index(Request $request)
    {
        $query = KhoaHoc::query();

        // Tìm kiếm
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where('ten_khoa_hoc', 'like', "%{$keyword}%");
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai != '') {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Sắp xếp
        $sortField = $request->get('sort', 'nam_bat_dau');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $khoaHocs = $query->paginate(10);

        return view('daotao.khoa-hoc.index', compact('khoaHocs'));
    }

    // Form tạo mới
    public function create()
    {
        return view('daotao.khoa-hoc.create');
    }

    // Lưu mới
    public function store(Request $request)
    {
        $request->validate([
            'ten_khoa_hoc' => 'required|max:50|unique:khoa_hoc,ten_khoa_hoc',
            'nam_bat_dau' => 'required|integer|min:2000|max:2100',
            'nam_ket_thuc' => 'required|integer|min:2000|max:2100|gt:nam_bat_dau',
            'so_nam_dao_tao' => 'required|integer|min:1|max:10',
            'trang_thai' => 'required|in:dang_hoc,da_tot_nghiep',
        ], [
            'ten_khoa_hoc.required' => 'Tên khóa học là bắt buộc',
            'ten_khoa_hoc.unique' => 'Tên khóa học đã tồn tại',
            'nam_bat_dau.required' => 'Năm bắt đầu là bắt buộc',
            'nam_bat_dau.integer' => 'Năm bắt đầu phải là số nguyên',
            'nam_bat_dau.min' => 'Năm bắt đầu không hợp lệ',
            'nam_ket_thuc.required' => 'Năm kết thúc là bắt buộc',
            'nam_ket_thuc.gt' => 'Năm kết thúc phải lớn hơn năm bắt đầu',
            'so_nam_dao_tao.required' => 'Số năm đào tạo là bắt buộc',
            'so_nam_dao_tao.min' => 'Số năm đào tạo phải từ 1 trở lên',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
        ]);

        KhoaHoc::create($request->all());
        return redirect()->route('dao-tao.khoa-hoc.index')->with('success', 'Thêm khóa học thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $khoaHoc = KhoaHoc::findOrFail($id);
        return view('daotao.khoa-hoc.edit', compact('khoaHoc'));
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $khoaHoc = KhoaHoc::findOrFail($id);

        $request->validate([
            'ten_khoa_hoc' => 'required|max:50|unique:khoa_hoc,ten_khoa_hoc,' . $khoaHoc->id,
            'nam_bat_dau' => 'required|integer|min:2000|max:2100',
            'nam_ket_thuc' => 'required|integer|min:2000|max:2100|gt:nam_bat_dau',
            'so_nam_dao_tao' => 'required|integer|min:1|max:10',
            'trang_thai' => 'required|in:dang_hoc,da_tot_nghiep',
        ], [
            'ten_khoa_hoc.required' => 'Tên khóa học là bắt buộc',
            'ten_khoa_hoc.unique' => 'Tên khóa học đã tồn tại',
            'nam_bat_dau.required' => 'Năm bắt đầu là bắt buộc',
            'nam_bat_dau.integer' => 'Năm bắt đầu phải là số nguyên',
            'nam_ket_thuc.required' => 'Năm kết thúc là bắt buộc',
            'nam_ket_thuc.gt' => 'Năm kết thúc phải lớn hơn năm bắt đầu',
            'so_nam_dao_tao.required' => 'Số năm đào tạo là bắt buộc',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
        ]);

        $khoaHoc->update($request->all());
        return redirect()->route('dao-tao.khoa-hoc.index')->with('success', 'Cập nhật khóa học thành công!');
    }

    // Xóa (soft delete)
    public function destroy($id)
    {
        $khoaHoc = KhoaHoc::findOrFail($id);
        $khoaHoc->delete();
        return redirect()->route('dao-tao.khoa-hoc.index')->with('success', 'Xóa khóa học thành công!');
    }
}
