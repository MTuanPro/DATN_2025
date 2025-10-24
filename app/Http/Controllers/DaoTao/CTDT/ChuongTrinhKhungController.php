<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\ChuongTrinhKhung;
use App\Models\Daotao\ChuyenNganh;
use App\Models\Daotao\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChuongTrinhKhungController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chuyenNganhs = ChuyenNganh::with('nganh.khoa')->orderBy('ten_chuyen_nganh')->get();

        // Lấy chuyên ngành được chọn
        $selectedChuyenNganhId = $request->get('chuyen_nganh_id');

        $chuongTrinhKhung = null;
        $thongKe = null;

        if ($selectedChuyenNganhId) {
            // Lấy CTĐT của chuyên ngành
            $chuongTrinhKhung = ChuongTrinhKhung::with(['monHoc.khoa', 'chuyenNganh'])
                ->where('chuyen_nganh_id', $selectedChuyenNganhId)
                ->orderBy('hoc_ky_goi_y')
                ->orderBy('thu_tu_hoc')
                ->get()
                ->groupBy('hoc_ky_goi_y'); // Group theo học kỳ

            // Thống kê
            $allMonHoc = ChuongTrinhKhung::with('monHoc')
                ->where('chuyen_nganh_id', $selectedChuyenNganhId)
                ->get();

            $thongKe = [
                'tong_mon_hoc' => $allMonHoc->count(),
                'tong_tin_chi' => $allMonHoc->sum(function ($item) {
                    return $item->monHoc->so_tin_chi ?? 0;
                }),
                'tin_chi_bat_buoc' => $allMonHoc->where('bat_buoc', true)->sum(function ($item) {
                    return $item->monHoc->so_tin_chi ?? 0;
                }),
                'tin_chi_tu_chon' => $allMonHoc->where('bat_buoc', false)->sum(function ($item) {
                    return $item->monHoc->so_tin_chi ?? 0;
                }),
                'mon_bat_buoc' => $allMonHoc->where('bat_buoc', true)->count(),
                'mon_tu_chon' => $allMonHoc->where('bat_buoc', false)->count(),
            ];
        }

        return view('daotao.chuong-trinh-khung.index', compact(
            'chuyenNganhs',
            'selectedChuyenNganhId',
            'chuongTrinhKhung',
            'thongKe'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $chuyenNganhId = $request->get('chuyen_nganh_id');

        if (!$chuyenNganhId) {
            return redirect()->route('dao-tao.chuong-trinh-khung.index')
                ->with('error', 'Vui lòng chọn chuyên ngành trước');
        }

        $chuyenNganh = ChuyenNganh::with('nganh.khoa')->findOrFail($chuyenNganhId);

        // Lấy danh sách môn học chưa có trong CTĐT
        $monHocDaCo = ChuongTrinhKhung::where('chuyen_nganh_id', $chuyenNganhId)
            ->pluck('mon_hoc_id')
            ->toArray();

        $monHocs = MonHoc::with('khoa')
            ->whereNotIn('id', $monHocDaCo)
            ->orderBy('ma_mon')
            ->get();

        return view('daotao.chuong-trinh-khung.create', compact('chuyenNganh', 'monHocs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chuyen_nganh_id' => 'required|exists:chuyen_nganh,id',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_goi_y' => 'required|integer|min:1|max:8',
            'loai_mon_hoc' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
            'bat_buoc' => 'required|boolean',
            'so_tin_chi_toi_thieu' => 'nullable|integer|min:1',
            'thu_tu_hoc' => 'nullable|integer|min:1',
            'ghi_chu' => 'nullable|string',
        ], [
            'chuyen_nganh_id.required' => 'Chuyên ngành không được để trống',
            'mon_hoc_id.required' => 'Môn học không được để trống',
            'mon_hoc_id.exists' => 'Môn học không tồn tại',
            'hoc_ky_goi_y.required' => 'Học kỳ gợi ý không được để trống',
            'hoc_ky_goi_y.min' => 'Học kỳ phải từ 1-8',
            'hoc_ky_goi_y.max' => 'Học kỳ phải từ 1-8',
            'loai_mon_hoc.required' => 'Loại môn học không được để trống',
            'bat_buoc.required' => 'Vui lòng chọn bắt buộc hoặc tự chọn',
        ]);

        // Kiểm tra môn học đã tồn tại trong CTĐT chưa
        $exists = ChuongTrinhKhung::where('chuyen_nganh_id', $validated['chuyen_nganh_id'])
            ->where('mon_hoc_id', $validated['mon_hoc_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'mon_hoc_id' => 'Môn học này đã có trong Chương trình khung'
            ])->withInput();
        }

        ChuongTrinhKhung::create($validated);

        return redirect()->route('dao-tao.chuong-trinh-khung.index', ['chuyen_nganh_id' => $validated['chuyen_nganh_id']])
            ->with('success', 'Thêm môn học vào CTĐT thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $chuongTrinhKhung = ChuongTrinhKhung::with(['chuyenNganh.nganh.khoa', 'monHoc'])->findOrFail($id);

        return view('daotao.chuong-trinh-khung.edit', compact('chuongTrinhKhung'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ctdt = ChuongTrinhKhung::findOrFail($id);

        $validated = $request->validate([
            'hoc_ky_goi_y' => 'required|integer|min:1|max:8',
            'loai_mon_hoc' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
            'bat_buoc' => 'required|boolean',
            'so_tin_chi_toi_thieu' => 'nullable|integer|min:1',
            'thu_tu_hoc' => 'nullable|integer|min:1',
            'ghi_chu' => 'nullable|string',
        ], [
            'hoc_ky_goi_y.required' => 'Học kỳ gợi ý không được để trống',
            'hoc_ky_goi_y.min' => 'Học kỳ phải từ 1-8',
            'hoc_ky_goi_y.max' => 'Học kỳ phải từ 1-8',
            'loai_mon_hoc.required' => 'Loại môn học không được để trống',
            'bat_buoc.required' => 'Vui lòng chọn bắt buộc hoặc tự chọn',
        ]);

        $ctdt->update($validated);

        return redirect()->route('dao-tao.chuong-trinh-khung.index', ['chuyen_nganh_id' => $ctdt->chuyen_nganh_id])
            ->with('success', 'Cập nhật CTĐT thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ctdt = ChuongTrinhKhung::findOrFail($id);
        $chuyenNganhId = $ctdt->chuyen_nganh_id;
        $ctdt->delete();

        return redirect()->route('dao-tao.chuong-trinh-khung.index', ['chuyen_nganh_id' => $chuyenNganhId])
            ->with('success', 'Xóa môn học khỏi CTĐT thành công!');
    }
}
