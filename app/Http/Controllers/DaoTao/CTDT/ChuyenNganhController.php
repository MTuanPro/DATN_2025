<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\ChuyenNganh;
use App\Models\Daotao\Nganh;
use Illuminate\Http\Request;

class ChuyenNganhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ChuyenNganh::with('nganh');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_chuyen_nganh', 'LIKE', "%{$search}%")
                    ->orWhere('ten_chuyen_nganh', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo ngành
        if ($request->filled('nganh_id')) {
            $query->where('nganh_id', $request->nganh_id);
        }

        $chuyenNganhs = $query->latest()->paginate(15);
        $nganhs = Nganh::all();

        return view('daotao.chuyen-nganh.index', compact('chuyenNganhs', 'nganhs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nganhs = Nganh::all();
        return view('daotao.chuyen-nganh.create', compact('nganhs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_chuyen_nganh' => 'required|string|max:20|unique:chuyen_nganh,ma_chuyen_nganh',
            'ten_chuyen_nganh' => 'required|string|max:255',
            'nganh_id' => 'nullable|exists:nganh,id',
            'tong_tin_chi_toi_thieu' => 'nullable|integer|min:120|max:200',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_chuyen_nganh.required' => 'Mã chuyên ngành là bắt buộc',
            'ma_chuyen_nganh.unique' => 'Mã chuyên ngành đã tồn tại',
            'ten_chuyen_nganh.required' => 'Tên chuyên ngành là bắt buộc',
            'tong_tin_chi_toi_thieu.min' => 'Tổng tín chỉ tối thiểu phải từ 120-200',
            'tong_tin_chi_toi_thieu.max' => 'Tổng tín chỉ tối thiểu phải từ 120-200',
        ]);

        ChuyenNganh::create($validated);

        return redirect()->route('dao-tao.chuyen-nganh.index')
            ->with('success', 'Thêm chuyên ngành thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $chuyenNganh = ChuyenNganh::with(['nganh', 'chuongTrinhKhung'])->findOrFail($id);
        return view('daotao.chuyen-nganh.show', compact('chuyenNganh'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $chuyenNganh = ChuyenNganh::findOrFail($id);
        $nganhs = Nganh::all();
        return view('daotao.chuyen-nganh.edit', compact('chuyenNganh', 'nganhs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $chuyenNganh = ChuyenNganh::findOrFail($id);

        $validated = $request->validate([
            'ma_chuyen_nganh' => 'required|string|max:20|unique:chuyen_nganh,ma_chuyen_nganh,' . $id,
            'ten_chuyen_nganh' => 'required|string|max:255',
            'nganh_id' => 'nullable|exists:nganh,id',
            'tong_tin_chi_toi_thieu' => 'nullable|integer|min:120|max:200',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_chuyen_nganh.required' => 'Mã chuyên ngành là bắt buộc',
            'ma_chuyen_nganh.unique' => 'Mã chuyên ngành đã tồn tại',
            'ten_chuyen_nganh.required' => 'Tên chuyên ngành là bắt buộc',
            'tong_tin_chi_toi_thieu.min' => 'Tổng tín chỉ tối thiểu phải từ 120-200',
            'tong_tin_chi_toi_thieu.max' => 'Tổng tín chỉ tối thiểu phải từ 120-200',
        ]);

        $chuyenNganh->update($validated);

        return redirect()->route('dao-tao.chuyen-nganh.index')
            ->with('success', 'Cập nhật chuyên ngành thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $chuyenNganh = ChuyenNganh::findOrFail($id);
            $chuyenNganh->delete();

            return redirect()->route('dao-tao.chuyen-nganh.index')
                ->with('success', 'Xóa chuyên ngành thành công!');
        } catch (\Exception $e) {
            return redirect()->route('dao-tao.chuyen-nganh.index')
                ->with('error', 'Không thể xóa chuyên ngành. Chuyên ngành đang được sử dụng.');
        }
    }
}
