<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DaoTao\Nganh;
use Illuminate\Http\Request;

class ChuyenNganhController extends Controller
{
    // Xem danh sách
    public function index(Request $request)
    {
        $query = ChuyenNganh::with('nganh.khoa');

        // Tìm kiếm
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('ma_chuyen_nganh', 'like', "%{$keyword}%")
                    ->orWhere('ten_chuyen_nganh', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo ngành
        if ($request->has('nganh_id') && $request->nganh_id != '') {
            $query->where('nganh_id', $request->nganh_id);
        }

        // Sắp xếp
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $chuyenNganhs = $query->paginate(10);
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();

        return view('daotao.chuyen-nganh.index', compact('chuyenNganhs', 'nganhs'));
    }

    // Form tạo mới
    public function create()
    {
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        return view('daotao.chuyen-nganh.create', compact('nganhs'));
    }

    // Lưu mới
    public function store(Request $request)
    {
        $request->validate([
            'ma_chuyen_nganh' => 'required|max:50|unique:chuyen_nganh,ma_chuyen_nganh',
            'ten_chuyen_nganh' => 'required|max:255',
            'nganh_id' => 'required|exists:nganh,id',
            'tong_tin_chi_toi_thieu' => 'nullable|integer|min:0|max:200',
        ], [
            'ma_chuyen_nganh.required' => 'Mã chuyên ngành là bắt buộc',
            'ma_chuyen_nganh.unique' => 'Mã chuyên ngành đã tồn tại',
            'ten_chuyen_nganh.required' => 'Tên chuyên ngành là bắt buộc',
            'nganh_id.required' => 'Ngành là bắt buộc',
            'nganh_id.exists' => 'Ngành không tồn tại',
            'tong_tin_chi_toi_thieu.integer' => 'Tổng tín chỉ phải là số nguyên',
            'tong_tin_chi_toi_thieu.min' => 'Tổng tín chỉ phải lớn hơn 0',
            'tong_tin_chi_toi_thieu.max' => 'Tổng tín chỉ không được vượt quá 200',
        ]);

        ChuyenNganh::create($request->all());
        return redirect()->route('dao-tao.chuyen-nganh.index')->with('success', 'Thêm chuyên ngành thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $chuyenNganh = ChuyenNganh::with('nganh')->findOrFail($id);
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        return view('daotao.chuyen-nganh.edit', compact('chuyenNganh', 'nganhs'));
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $chuyenNganh = ChuyenNganh::findOrFail($id);

        $request->validate([
            'ma_chuyen_nganh' => 'required|max:50|unique:chuyen_nganh,ma_chuyen_nganh,' . $chuyenNganh->id,
            'ten_chuyen_nganh' => 'required|max:255',
            'nganh_id' => 'required|exists:nganh,id',
            'tong_tin_chi_toi_thieu' => 'nullable|integer|min:0|max:200',
        ], [
            'ma_chuyen_nganh.required' => 'Mã chuyên ngành là bắt buộc',
            'ma_chuyen_nganh.unique' => 'Mã chuyên ngành đã tồn tại',
            'ten_chuyen_nganh.required' => 'Tên chuyên ngành là bắt buộc',
            'nganh_id.required' => 'Ngành là bắt buộc',
            'nganh_id.exists' => 'Ngành không tồn tại',
            'tong_tin_chi_toi_thieu.integer' => 'Tổng tín chỉ phải là số nguyên',
            'tong_tin_chi_toi_thieu.min' => 'Tổng tín chỉ phải lớn hơn 0',
            'tong_tin_chi_toi_thieu.max' => 'Tổng tín chỉ không được vượt quá 200',
        ]);

        $chuyenNganh->update($request->all());
        return redirect()->route('dao-tao.chuyen-nganh.index')->with('success', 'Cập nhật chuyên ngành thành công!');
    }

    // Xóa (soft delete)
    public function destroy($id)
    {
        $chuyenNganh = ChuyenNganh::findOrFail($id);
        $chuyenNganh->delete();
        return redirect()->route('dao-tao.chuyen-nganh.index')->with('success', 'Xóa chuyên ngành thành công!');
    }
}
