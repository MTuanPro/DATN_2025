<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\MonHoc;
use App\Models\Daotao\Khoa;
use Illuminate\Http\Request;

class MonHocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MonHoc::with('khoa');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_mon', 'like', "%$search%")
                    ->orWhere('ten_mon', 'like', "%$search%");
            });
        }

        // Lọc theo khoa
        if ($request->filled('khoa_id')) {
            $query->where('khoa_id', $request->khoa_id);
        }

        // Lọc theo loại môn
        if ($request->filled('loai_mon')) {
            $query->where('loai_mon', $request->loai_mon);
        }

        // Lọc theo hình thức dạy
        if ($request->filled('hinh_thuc_day')) {
            $query->where('hinh_thuc_day', $request->hinh_thuc_day);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'ma_mon');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $monHocs = $query->paginate(15);
        $khoas = Khoa::orderBy('ten_khoa')->get();

        return view('daotao.mon-hoc.index', compact('monHocs', 'khoas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $khoas = Khoa::orderBy('ten_khoa')->get();
        return view('daotao.mon-hoc.create', compact('khoas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_mon' => 'required|string|max:20|unique:mon_hoc,ma_mon',
            'ten_mon' => 'required|string|max:255',
            'so_tin_chi' => 'required|integer|min:1|max:5',
            'so_tin_chi_ly_thuyet' => 'required|integer|min:0|max:5',
            'so_tin_chi_thuc_hanh' => 'required|integer|min:0|max:5',
            'loai_mon' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
            'khoa_id' => 'required|exists:khoa,id',
            'hinh_thuc_day' => 'required|in:offline,online,hybrid',
            'thoi_luong_hoc' => 'nullable|integer|min:1',
            'so_buoi_hoc' => 'nullable|integer|min:1',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_mon.required' => 'Mã môn học không được để trống',
            'ma_mon.unique' => 'Mã môn học đã tồn tại',
            'ten_mon.required' => 'Tên môn học không được để trống',
            'so_tin_chi.required' => 'Số tín chỉ không được để trống',
            'so_tin_chi.min' => 'Số tín chỉ phải từ 1-5',
            'so_tin_chi.max' => 'Số tín chỉ phải từ 1-5',
            'loai_mon.required' => 'Loại môn học không được để trống',
            'khoa_id.required' => 'Khoa quản lý không được để trống',
            'khoa_id.exists' => 'Khoa không tồn tại',
            'hinh_thuc_day.required' => 'Hình thức dạy không được để trống',
        ]);

        // Validate: Tổng tín chỉ = lý thuyết + thực hành
        if ($validated['so_tin_chi'] != ($validated['so_tin_chi_ly_thuyet'] + $validated['so_tin_chi_thuc_hanh'])) {
            return redirect()->back()->withErrors([
                'so_tin_chi' => 'Tổng số tín chỉ phải bằng tổng tín chỉ lý thuyết + thực hành'
            ])->withInput();
        }

        // Validate: Thời lượng học tối thiểu = so_tin_chi * 15
        if ($request->filled('thoi_luong_hoc')) {
            $minThoiLuongHoc = $validated['so_tin_chi'] * 15;
            if ($validated['thoi_luong_hoc'] < $minThoiLuongHoc) {
                return redirect()->back()->withErrors([
                    'thoi_luong_hoc' => "Thời lượng học tối thiểu là {$minThoiLuongHoc} giờ (số tín chỉ × 15)"
                ])->withInput();
            }
        }

        MonHoc::create($validated);

        return redirect()->route('dao-tao.mon-hoc.index')->with('success', 'Thêm môn học thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $monHoc = MonHoc::findOrFail($id);
        $khoas = Khoa::orderBy('ten_khoa')->get();
        return view('daotao.mon-hoc.edit', compact('monHoc', 'khoas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $monHoc = MonHoc::findOrFail($id);

        $validated = $request->validate([
            'ma_mon' => "required|string|max:20|unique:mon_hoc,ma_mon,{$id}",
            'ten_mon' => 'required|string|max:255',
            'so_tin_chi' => 'required|integer|min:1|max:5',
            'so_tin_chi_ly_thuyet' => 'required|integer|min:0|max:5',
            'so_tin_chi_thuc_hanh' => 'required|integer|min:0|max:5',
            'loai_mon' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
            'khoa_id' => 'required|exists:khoa,id',
            'hinh_thuc_day' => 'required|in:offline,online,hybrid',
            'thoi_luong_hoc' => 'nullable|integer|min:1',
            'so_buoi_hoc' => 'nullable|integer|min:1',
            'mo_ta' => 'nullable|string',
        ], [
            'ma_mon.required' => 'Mã môn học không được để trống',
            'ma_mon.unique' => 'Mã môn học đã tồn tại',
            'ten_mon.required' => 'Tên môn học không được để trống',
            'so_tin_chi.required' => 'Số tín chỉ không được để trống',
            'loai_mon.required' => 'Loại môn học không được để trống',
            'khoa_id.required' => 'Khoa quản lý không được để trống',
            'hinh_thuc_day.required' => 'Hình thức dạy không được để trống',
        ]);

        // Validate tổng tín chỉ
        if ($validated['so_tin_chi'] != ($validated['so_tin_chi_ly_thuyet'] + $validated['so_tin_chi_thuc_hanh'])) {
            return redirect()->back()->withErrors([
                'so_tin_chi' => 'Tổng số tín chỉ phải bằng tổng tín chỉ lý thuyết + thực hành'
            ])->withInput();
        }

        $monHoc->update($validated);

        return redirect()->route('dao-tao.mon-hoc.index')->with('success', 'Cập nhật môn học thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $monHoc = MonHoc::findOrFail($id);
        $monHoc->delete();

        return redirect()->route('dao-tao.mon-hoc.index')->with('success', 'Xóa môn học thành công!');
    }

    /**
     * Hiển thị trang quản lý môn tiên quyết
     */
    public function tienQuyet($id)
    {
        $monHoc = MonHoc::with(['monTienQuyet', 'monCanMonNay'])->findOrFail($id);
        $danhSachMonHoc = MonHoc::where('id', '!=', $id)->orderBy('ma_mon')->get();

        return view('daotao.mon-hoc.tien-quyet', compact('monHoc', 'danhSachMonHoc'));
    }

    /**
     * Thêm môn tiên quyết
     */
    public function storeTienQuyet(Request $request, $id)
    {
        $monHoc = MonHoc::findOrFail($id);

        $validated = $request->validate([
            'mon_tien_quyet_id' => 'required|exists:mon_hoc,id',
            'loai_tien_quyet' => 'required|in:bat_buoc,khuyen_nghi',
            'dieu_kien_qua_mon' => 'required|boolean',
            'ghi_chu' => 'nullable|string',
        ], [
            'mon_tien_quyet_id.required' => 'Vui lòng chọn môn tiên quyết',
            'mon_tien_quyet_id.exists' => 'Môn học không tồn tại',
        ]);

        // Kiểm tra không tự tham chiếu
        if ($validated['mon_tien_quyet_id'] == $id) {
            return redirect()->back()->withErrors([
                'mon_tien_quyet_id' => 'Môn học không thể là tiên quyết của chính nó'
            ]);
        }

        // Kiểm tra vòng lặp (circular dependency)
        if ($this->hasCircularDependency($id, $validated['mon_tien_quyet_id'])) {
            return redirect()->back()->withErrors([
                'mon_tien_quyet_id' => 'Tạo vòng lặp tiên quyết. Vui lòng kiểm tra lại!'
            ]);
        }

        // Kiểm tra đã tồn tại chưa
        if ($monHoc->monTienQuyet()->where('mon_tien_quyet_id', $validated['mon_tien_quyet_id'])->exists()) {
            return redirect()->back()->withErrors([
                'mon_tien_quyet_id' => 'Môn tiên quyết này đã được thêm'
            ]);
        }

        $monHoc->monTienQuyet()->attach($validated['mon_tien_quyet_id'], [
            'loai_tien_quyet' => $validated['loai_tien_quyet'],
            'dieu_kien_qua_mon' => $validated['dieu_kien_qua_mon'],
            'ghi_chu' => $validated['ghi_chu'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Thêm môn tiên quyết thành công!');
    }

    /**
     * Xóa môn tiên quyết
     */
    public function destroyTienQuyet($id, $tienQuyetId)
    {
        $monHoc = MonHoc::findOrFail($id);
        $monHoc->monTienQuyet()->detach($tienQuyetId);

        return redirect()->back()->with('success', 'Xóa môn tiên quyết thành công!');
    }

    /**
     * Kiểm tra vòng lặp circular dependency
     * Ví dụ: A → B → C → A (không được phép)
     */
    private function hasCircularDependency($monHocId, $monTienQuyetId, $visited = [])
    {
        // Nếu môn tiên quyết hiện tại đã được visited, có vòng lặp
        if (in_array($monTienQuyetId, $visited)) {
            return true;
        }

        // Thêm môn tiên quyết hiện tại vào danh sách visited
        $visited[] = $monTienQuyetId;

        // Lấy danh sách môn tiên quyết của môn tiên quyết hiện tại
        $monTienQuyet = MonHoc::find($monTienQuyetId);
        if (!$monTienQuyet) {
            return false;
        }

        $cacMonTienQuyetCuaMonNay = $monTienQuyet->monTienQuyet()->pluck('mon_tien_quyet_id')->toArray();

        // Nếu trong danh sách môn tiên quyết có môn gốc, có vòng lặp
        if (in_array($monHocId, $cacMonTienQuyetCuaMonNay)) {
            return true;
        }

        // Kiểm tra đệ quy cho từng môn tiên quyết
        foreach ($cacMonTienQuyetCuaMonNay as $nextMonTienQuyetId) {
            if ($this->hasCircularDependency($monHocId, $nextMonTienQuyetId, $visited)) {
                return true;
            }
        }

        return false;
    }
}
