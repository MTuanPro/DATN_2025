<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\MonHoc;
use App\Models\Daotao\Khoa;
use App\Models\Daotao\MonHocTienQuyet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                $q->where('ma_mon', 'LIKE', "%{$search}%")
                    ->orWhere('ten_mon', 'LIKE', "%{$search}%");
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

        // Lọc theo số tín chỉ
        if ($request->filled('so_tin_chi')) {
            $query->where('so_tin_chi', $request->so_tin_chi);
        }

        $monHocs = $query->latest()->paginate(15);
        $khoas = Khoa::all();

        return view('daotao.mon-hoc.index', compact('monHocs', 'khoas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $khoas = Khoa::all();
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
            'mo_ta' => 'nullable|string',
            'loai_mon' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
            'khoa_id' => 'required|exists:khoa,id',
            'hinh_thuc_day' => 'required|in:offline,online,hybrid',
            'thoi_luong_hoc' => 'nullable|integer|min:1',
            'so_buoi_hoc' => 'nullable|integer|min:10',
        ], [
            'ma_mon.required' => 'Mã môn học là bắt buộc',
            'ma_mon.unique' => 'Mã môn học đã tồn tại',
            'ten_mon.required' => 'Tên môn học là bắt buộc',
            'so_tin_chi.required' => 'Số tín chỉ là bắt buộc',
            'so_tin_chi.min' => 'Số tín chỉ phải từ 1-5',
            'so_tin_chi.max' => 'Số tín chỉ phải từ 1-5',
            'khoa_id.required' => 'Khoa quản lý là bắt buộc',
            'loai_mon.required' => 'Loại môn học là bắt buộc',
            'hinh_thuc_day.required' => 'Hình thức dạy là bắt buộc',
        ]);

        // Kiểm tra tổng tín chỉ
        if ($validated['so_tin_chi'] != ($validated['so_tin_chi_ly_thuyet'] + $validated['so_tin_chi_thuc_hanh'])) {
            return back()->withErrors(['so_tin_chi' => 'Tổng tín chỉ phải bằng tổng tín chỉ lý thuyết và thực hành'])->withInput();
        }

        MonHoc::create($validated);

        return redirect()->route('dao-tao.mon-hoc.index')
            ->with('success', 'Thêm môn học thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $monHoc = MonHoc::with(['khoa', 'monTienQuyet', 'monCanMonNay'])->findOrFail($id);
        return view('daotao.mon-hoc.show', compact('monHoc'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $monHoc = MonHoc::findOrFail($id);
        $khoas = Khoa::all();
        return view('daotao.mon-hoc.edit', compact('monHoc', 'khoas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $monHoc = MonHoc::findOrFail($id);

        $validated = $request->validate([
            'ma_mon' => 'required|string|max:20|unique:mon_hoc,ma_mon,' . $id,
            'ten_mon' => 'required|string|max:255',
            'so_tin_chi' => 'required|integer|min:1|max:5',
            'so_tin_chi_ly_thuyet' => 'required|integer|min:0|max:5',
            'so_tin_chi_thuc_hanh' => 'required|integer|min:0|max:5',
            'mo_ta' => 'nullable|string',
            'loai_mon' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
            'khoa_id' => 'required|exists:khoa,id',
            'hinh_thuc_day' => 'required|in:offline,online,hybrid',
            'thoi_luong_hoc' => 'nullable|integer|min:1',
            'so_buoi_hoc' => 'nullable|integer|min:10',
        ], [
            'ma_mon.required' => 'Mã môn học là bắt buộc',
            'ma_mon.unique' => 'Mã môn học đã tồn tại',
            'ten_mon.required' => 'Tên môn học là bắt buộc',
            'so_tin_chi.required' => 'Số tín chỉ là bắt buộc',
            'khoa_id.required' => 'Khoa quản lý là bắt buộc',
            'loai_mon.required' => 'Loại môn học là bắt buộc',
            'hinh_thuc_day.required' => 'Hình thức dạy là bắt buộc',
        ]);

        // Kiểm tra tổng tín chỉ
        if ($validated['so_tin_chi'] != ($validated['so_tin_chi_ly_thuyet'] + $validated['so_tin_chi_thuc_hanh'])) {
            return back()->withErrors(['so_tin_chi' => 'Tổng tín chỉ phải bằng tổng tín chỉ lý thuyết và thực hành'])->withInput();
        }

        $monHoc->update($validated);

        return redirect()->route('dao-tao.mon-hoc.index')
            ->with('success', 'Cập nhật môn học thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $monHoc = MonHoc::findOrFail($id);
            $monHoc->delete();

            return redirect()->route('dao-tao.mon-hoc.index')
                ->with('success', 'Xóa môn học thành công!');
        } catch (\Exception $e) {
            return redirect()->route('dao-tao.mon-hoc.index')
                ->with('error', 'Không thể xóa môn học. Môn học đang được sử dụng.');
        }
    }

    /**
     * Hiển thị trang quản lý môn tiên quyết
     */
    public function tienQuyet(string $id)
    {
        $monHoc = MonHoc::with([
            'khoa',
            'monTienQuyet' => function ($query) {
                $query->withPivot('id', 'loai_tien_quyet', 'dieu_kien_qua_mon', 'ghi_chu');
            },
            'monTienQuyet.khoa'
        ])->findOrFail($id);

        // Lấy danh sách môn học có thể thêm làm tiên quyết (loại bỏ các môn đã là tiên quyết và chính nó)
        $daTienQuyetIds = $monHoc->monTienQuyet->pluck('id')->toArray();
        $daTienQuyetIds[] = $id; // Thêm chính nó

        $danhSachMonHoc = MonHoc::with('khoa')
            ->whereNotIn('id', $daTienQuyetIds)
            ->orderBy('ma_mon')
            ->get();

        return view('daotao.mon-hoc.tien-quyet', compact('monHoc', 'danhSachMonHoc'));
    }

    /**
     * Thêm môn tiên quyết
     */
    public function storeTienQuyet(Request $request, string $id)
    {
        $validated = $request->validate([
            'mon_tien_quyet_id' => 'required|exists:mon_hoc,id',
            'loai_tien_quyet' => 'required|in:bat_buoc,khuyen_nghi',
            'dieu_kien_qua_mon' => 'required|boolean',
            'ghi_chu' => 'nullable|string',
        ]);

        // Kiểm tra không thêm chính nó
        if ($id == $validated['mon_tien_quyet_id']) {
            return back()->with('error', 'Không thể thêm môn học làm tiên quyết cho chính nó!');
        }

        // Kiểm tra đã tồn tại chưa
        $exists = MonHocTienQuyet::where('mon_hoc_id', $id)
            ->where('mon_tien_quyet_id', $validated['mon_tien_quyet_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Môn tiên quyết này đã được thêm trước đó!');
        }

        // Kiểm tra vòng lặp phụ thuộc
        if ($this->detectCircularDependency($id, $validated['mon_tien_quyet_id'])) {
            return back()->with('error', 'Không thể thêm môn tiên quyết vì sẽ tạo vòng lặp phụ thuộc!');
        }

        MonHocTienQuyet::create([
            'mon_hoc_id' => $id,
            'mon_tien_quyet_id' => $validated['mon_tien_quyet_id'],
            'loai_tien_quyet' => $validated['loai_tien_quyet'],
            'dieu_kien_qua_mon' => $validated['dieu_kien_qua_mon'],
            'ghi_chu' => $validated['ghi_chu'],
        ]);

        return back()->with('success', 'Thêm môn tiên quyết thành công!');
    }

    /**
     * Xóa môn tiên quyết
     */
    public function destroyTienQuyet(string $monHocId, string $tienQuyetId)
    {
        DB::beginTransaction();
        try {
            Log::info('Bắt đầu xóa môn tiên quyết', [
                'mon_hoc_id' => $monHocId,
                'tien_quyet_id' => $tienQuyetId
            ]);

            // Tìm record trong bảng mon_hoc_tien_quyet (không quan tâm soft delete)
            $monTienQuyet = MonHocTienQuyet::withTrashed()
                ->where('id', $tienQuyetId)
                ->where('mon_hoc_id', $monHocId)
                ->first();

            if (!$monTienQuyet) {
                // Thử tìm không có withTrashed
                $monTienQuyet = MonHocTienQuyet::where('id', $tienQuyetId)
                    ->where('mon_hoc_id', $monHocId)
                    ->first();
            }

            if (!$monTienQuyet) {
                Log::warning('Không tìm thấy môn tiên quyết', [
                    'mon_hoc_id' => $monHocId,
                    'tien_quyet_id' => $tienQuyetId
                ]);
                DB::rollBack();
                return back()->with('error', 'Không tìm thấy môn tiên quyết cần xóa!');
            }

            // Xóa vĩnh viễn (force delete) để đảm bảo xóa hoàn toàn
            $monTienQuyet->forceDelete();

            DB::commit();
            Log::info('Xóa môn tiên quyết thành công', [
                'mon_hoc_id' => $monHocId,
                'tien_quyet_id' => $tienQuyetId
            ]);
            return back()->with('success', 'Xóa môn tiên quyết thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa môn tiên quyết: ' . $e->getMessage(), [
                'mon_hoc_id' => $monHocId,
                'tien_quyet_id' => $tienQuyetId,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Không thể xóa môn tiên quyết: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra vòng lặp phụ thuộc
     */
    private function detectCircularDependency($monHocId, $monTienQuyetId, $visited = [])
    {
        // Nếu đã thăm môn này rồi, có vòng lặp
        if (in_array($monTienQuyetId, $visited)) {
            return true;
        }

        $visited[] = $monTienQuyetId;

        // Lấy các môn tiên quyết của môn tiên quyết
        $tienQuyets = MonHocTienQuyet::where('mon_hoc_id', $monTienQuyetId)
            ->pluck('mon_tien_quyet_id')
            ->toArray();

        // Nếu trong các môn tiên quyết có môn gốc, có vòng lặp
        if (in_array($monHocId, $tienQuyets)) {
            return true;
        }

        // Kiểm tra đệ quy
        foreach ($tienQuyets as $tq) {
            if ($this->detectCircularDependency($monHocId, $tq, $visited)) {
                return true;
            }
        }

        return false;
    }
}
