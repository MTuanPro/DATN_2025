<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\MonHoc;
use App\Models\Daotao\Khoa;
use App\Models\Daotao\MonHocTienQuyet;
use App\Models\CauHinhDauDiemMacDinh;
use App\Models\CauHinhDauDiem;
use App\Models\LopHocPhan;
use App\Models\NhapDiem;
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

        // Lọc theo hình thức dạy
        if ($request->filled('hinh_thuc_day')) {
            $query->where('hinh_thuc_day', $request->hinh_thuc_day);
        }

        $monHocs = $query->latest()->paginate(15)->appends($request->all());
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

        DB::beginTransaction();
        try {
            $monHoc = MonHoc::create($validated);

            // Tự động tạo cấu hình đầu điểm mặc định cho môn học
            $this->taoCauHinhDauDiemMacDinh($monHoc->id);

            DB::commit();

            return redirect()->route('dao-tao.mon-hoc.index', request()->query())
                ->with('success', 'Thêm môn học thành công! Đã tự động tạo cấu hình đầu điểm mặc định.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo môn học: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
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

        return redirect()->route('dao-tao.mon-hoc.index', request()->query())
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

            return redirect()->route('dao-tao.mon-hoc.index', request()->query())
                ->with('success', 'Xóa môn học thành công!');
        } catch (\Exception $e) {
            return redirect()->route('dao-tao.mon-hoc.index', request()->query())
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





//     /**
//      * Display the specified resource.
//      */
//     public function show(string $id)
//     {
//         $monHoc = MonHoc::with(['khoa', 'monTienQuyet', 'monCanMonNay'])->findOrFail($id);
//         return view('daotao.mon-hoc.show', compact('monHoc'));
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(string $id)
//     {
//         $monHoc = MonHoc::findOrFail($id);
//         $khoas = Khoa::all();
//         return view('daotao.mon-hoc.edit', compact('monHoc', 'khoas'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         $monHoc = MonHoc::findOrFail($id);

//         $validated = $request->validate([
//             'ma_mon' => 'required|string|max:20|unique:mon_hoc,ma_mon,' . $id,
//             'ten_mon' => 'required|string|max:255',
//             'so_tin_chi' => 'required|integer|min:1|max:5',
//             'so_tin_chi_ly_thuyet' => 'required|integer|min:0|max:5',
//             'so_tin_chi_thuc_hanh' => 'required|integer|min:0|max:5',
//             'mo_ta' => 'nullable|string',
//             'loai_mon' => 'required|in:dai_cuong,co_so_nganh,chuyen_nganh_bat_buoc,chuyen_nganh_tu_chon,thuc_tap,do_an_tot_nghiep',
//             'khoa_id' => 'required|exists:khoa,id',
//             'hinh_thuc_day' => 'required|in:offline,online,hybrid',
//             'thoi_luong_hoc' => 'nullable|integer|min:1',
//             'so_buoi_hoc' => 'nullable|integer|min:10',
//         ], [
//             'ma_mon.required' => 'Mã môn học là bắt buộc',
//             'ma_mon.unique' => 'Mã môn học đã tồn tại',
//             'ten_mon.required' => 'Tên môn học là bắt buộc',
//             'so_tin_chi.required' => 'Số tín chỉ là bắt buộc',
//             'khoa_id.required' => 'Khoa quản lý là bắt buộc',
//             'loai_mon.required' => 'Loại môn học là bắt buộc',
//             'hinh_thuc_day.required' => 'Hình thức dạy là bắt buộc',
//         ]);

//         // Kiểm tra tổng tín chỉ
//         if ($validated['so_tin_chi'] != ($validated['so_tin_chi_ly_thuyet'] + $validated['so_tin_chi_thuc_hanh'])) {
//             return back()->withErrors(['so_tin_chi' => 'Tổng tín chỉ phải bằng tổng tín chỉ lý thuyết và thực hành'])->withInput();
//         }

//         $monHoc->update($validated);

//         return redirect()->route('dao-tao.mon-hoc.index')
//             ->with('success', 'Cập nhật môn học thành công!');
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(string $id)
//     {
//         try {
//             $monHoc = MonHoc::findOrFail($id);
//             $monHoc->delete();

//             return redirect()->route('dao-tao.mon-hoc.index')
//                 ->with('success', 'Xóa môn học thành công!');
//         } catch (\Exception $e) {
//             return redirect()->route('dao-tao.mon-hoc.index')
//                 ->with('error', 'Không thể xóa môn học. Môn học đang được sử dụng.');
//         }
//     }

//     /**
//      * Hiển thị trang quản lý môn tiên quyết
//      */
//     public function tienQuyet(string $id)
//     {
//         $monHoc = MonHoc::with([
//             'khoa',
//             'monTienQuyet' => function ($query) {
//                 $query->withPivot('id', 'loai_tien_quyet', 'dieu_kien_qua_mon', 'ghi_chu');
//             },
//             'monTienQuyet.khoa'
//         ])->findOrFail($id);

//         // Lấy danh sách môn học có thể thêm làm tiên quyết (loại bỏ các môn đã là tiên quyết và chính nó)
//         $daTienQuyetIds = $monHoc->monTienQuyet->pluck('id')->toArray();
//         $daTienQuyetIds[] = $id; // Thêm chính nó

//         $danhSachMonHoc = MonHoc::with('khoa')
//             ->whereNotIn('id', $daTienQuyetIds)
//             ->orderBy('ma_mon')
//             ->get();

//         return view('daotao.mon-hoc.tien-quyet', compact('monHoc', 'danhSachMonHoc'));
//     }

//     /**
//      * Thêm môn tiên quyết
//      */
//     public function storeTienQuyet(Request $request, string $id)
//     {
//         $validated = $request->validate([
//             'mon_tien_quyet_id' => 'required|exists:mon_hoc,id',
//             'loai_tien_quyet' => 'required|in:bat_buoc,khuyen_nghi',
//             'dieu_kien_qua_mon' => 'required|boolean',
//             'ghi_chu' => 'nullable|string',
//         ]);

//         // Kiểm tra không thêm chính nó
//         if ($id == $validated['mon_tien_quyet_id']) {
//             return back()->with('error', 'Không thể thêm môn học làm tiên quyết cho chính nó!');
//         }

//         // Kiểm tra đã tồn tại chưa
//         $exists = MonHocTienQuyet::where('mon_hoc_id', $id)
//             ->where('mon_tien_quyet_id', $validated['mon_tien_quyet_id'])
//             ->exists();

//         if ($exists) {
//             return back()->with('error', 'Môn tiên quyết này đã được thêm trước đó!');
//         }

//         // Kiểm tra vòng lặp phụ thuộc
//         if ($this->detectCircularDependency($id, $validated['mon_tien_quyet_id'])) {
//             return back()->with('error', 'Không thể thêm môn tiên quyết vì sẽ tạo vòng lặp phụ thuộc!');
//         }

//         MonHocTienQuyet::create([
//             'mon_hoc_id' => $id,
//             'mon_tien_quyet_id' => $validated['mon_tien_quyet_id'],
//             'loai_tien_quyet' => $validated['loai_tien_quyet'],
//             'dieu_kien_qua_mon' => $validated['dieu_kien_qua_mon'],
//             'ghi_chu' => $validated['ghi_chu'],
//         ]);

//         return back()->with('success', 'Thêm môn tiên quyết thành công!');
//     }

//     /**
//      * Xóa môn tiên quyết
//      */
//     public function destroyTienQuyet(string $monHocId, string $tienQuyetId)
//     {
//         DB::beginTransaction();
//         try {
//             Log::info('Bắt đầu xóa môn tiên quyết', [
//                 'mon_hoc_id' => $monHocId,
//                 'tien_quyet_id' => $tienQuyetId
//             ]);

//             // Tìm record trong bảng mon_hoc_tien_quyet (không quan tâm soft delete)
//             $monTienQuyet = MonHocTienQuyet::withTrashed()
//                 ->where('id', $tienQuyetId)
//                 ->where('mon_hoc_id', $monHocId)
//                 ->first();

//             if (!$monTienQuyet) {
//                 // Thử tìm không có withTrashed
//                 $monTienQuyet = MonHocTienQuyet::where('id', $tienQuyetId)
//                     ->where('mon_hoc_id', $monHocId)
//                     ->first();
//             }

//             if (!$monTienQuyet) {
//                 Log::warning('Không tìm thấy môn tiên quyết', [
//                     'mon_hoc_id' => $monHocId,
//                     'tien_quyet_id' => $tienQuyetId
//                 ]);
//                 DB::rollBack();
//                 return back()->with('error', 'Không tìm thấy môn tiên quyết cần xóa!');
//             }

//             // Xóa vĩnh viễn (force delete) để đảm bảo xóa hoàn toàn
//             $monTienQuyet->forceDelete();

//             DB::commit();
//             Log::info('Xóa môn tiên quyết thành công', [
//                 'mon_hoc_id' => $monHocId,
//                 'tien_quyet_id' => $tienQuyetId
//             ]);
//             return back()->with('success', 'Xóa môn tiên quyết thành công!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Lỗi khi xóa môn tiên quyết: ' . $e->getMessage(), [
//                 'mon_hoc_id' => $monHocId,
//                 'tien_quyet_id' => $tienQuyetId,
//                 'trace' => $e->getTraceAsString()
//             ]);
//             return back()->with('error', 'Không thể xóa môn tiên quyết: ' . $e->getMessage());
//         }
//     }

    /**
     * Tạo cấu hình đầu điểm mặc định cho môn học
     */
    private function taoCauHinhDauDiemMacDinh($monHocId)
    {
        // Cấu hình mặc định: Chuyên cần 10%, Giữa kỳ 30%, Cuối kỳ 60%
        $cauHinhMacDinh = [
            ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
            ['ten_dau_diem' => 'Giữa kỳ', 'ty_le' => 30, 'so_cot' => 1],
            ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 60, 'so_cot' => 1],
        ];

        foreach ($cauHinhMacDinh as $cauHinh) {
            CauHinhDauDiemMacDinh::create([
                'mon_hoc_id' => $monHocId,
                'ten_dau_diem' => $cauHinh['ten_dau_diem'],
                'ty_le' => $cauHinh['ty_le'],
                'so_cot' => $cauHinh['so_cot'],
            ]);
        }
    }

//     /**
//      * Kiểm tra vòng lặp phụ thuộc
//      */
//     private function detectCircularDependency($monHocId, $monTienQuyetId, $visited = [])
//     {
//         // Nếu đã thăm môn này rồi, có vòng lặp
//         if (in_array($monTienQuyetId, $visited)) {
//             return true;
//         }

//         $visited[] = $monTienQuyetId;

//         // Lấy các môn tiên quyết của môn tiên quyết
//         $tienQuyets = MonHocTienQuyet::where('mon_hoc_id', $monTienQuyetId)
//             ->pluck('mon_tien_quyet_id')
//             ->toArray();

//         // Nếu trong các môn tiên quyết có môn gốc, có vòng lặp
//         if (in_array($monHocId, $tienQuyets)) {
//             return true;
//         }

//         // Kiểm tra đệ quy
//         foreach ($tienQuyets as $tq) {
//             if ($this->detectCircularDependency($monHocId, $tq, $visited)) {
//                 return true;
//             }
//         }

//         return false;
//     }

    /**
     * Hiển thị cấu hình đầu điểm mặc định của môn học
     */
    public function cauHinhDiem(string $id)
    {
        $monHoc = MonHoc::with(['cauHinhDauDiemMacDinh'])->findOrFail($id);
        $cauHinhs = $monHoc->cauHinhDauDiemMacDinh;
        $tongTyLe = $cauHinhs->sum('ty_le');
        $tyLeConLai = 100 - $tongTyLe;

        return view('daotao.mon-hoc.cau-hinh-diem', compact('monHoc', 'cauHinhs', 'tongTyLe', 'tyLeConLai'));
    }

    /**
     * Thêm cấu hình đầu điểm mặc định cho môn học
     */
    public function storeCauHinhDiem(Request $request, string $id)
    {
        $monHoc = MonHoc::findOrFail($id);

        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:50|unique:cau_hinh_dau_diem_mac_dinh,ten_dau_diem,NULL,id,mon_hoc_id,' . $id,
            'ty_le' => 'required|numeric|min:0.01|max:100',
            'so_cot' => 'required|integer|min:1|max:20',
        ], [
            'ten_dau_diem.required' => 'Tên đầu điểm là bắt buộc',
            'ten_dau_diem.unique' => 'Tên đầu điểm này đã tồn tại',
            'ty_le.required' => 'Tỷ lệ % là bắt buộc',
            'ty_le.min' => 'Tỷ lệ % phải lớn hơn 0',
            'ty_le.max' => 'Tỷ lệ % không được vượt quá 100',
            'so_cot.required' => 'Số cột điểm là bắt buộc',
            'so_cot.min' => 'Số cột điểm phải lớn hơn 0',
            'so_cot.max' => 'Số cột điểm không được vượt quá 20',
        ]);

        // Kiểm tra tổng tỷ lệ
        $tongTyLe = CauHinhDauDiemMacDinh::where('mon_hoc_id', $id)->sum('ty_le');
        if (($tongTyLe + $validated['ty_le']) > 100) {
            return back()->withErrors(['ty_le' => 'Tổng tỷ lệ không được vượt quá 100%. Tỷ lệ còn lại: ' . (100 - $tongTyLe) . '%'])->withInput();
        }

        DB::beginTransaction();
        try {
            $cauHinh = CauHinhDauDiemMacDinh::create([
                'mon_hoc_id' => $id,
                'ten_dau_diem' => $validated['ten_dau_diem'],
                'ty_le' => $validated['ty_le'],
                'so_cot' => $validated['so_cot'],
            ]);

            // Đồng bộ cấu hình cho các lớp học phần (chỉ lớp chưa có điểm nhập)
            $this->dongBoCauHinhToLopHocPhan($id, $cauHinh->ten_dau_diem, $cauHinh->id);

            DB::commit();

            return redirect()->route('dao-tao.mon-hoc.cau-hinh-diem', $id)
                ->with('success', 'Đã thêm cấu hình đầu điểm thành công. Đã đồng bộ cho các lớp học phần chưa có điểm.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi thêm cấu hình đầu điểm: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật cấu hình đầu điểm mặc định
     */
    public function updateCauHinhDiem(Request $request, string $monHocId, string $cauHinhMacDinhId)
    {
        $monHoc = MonHoc::findOrFail($monHocId);
        $cauHinh = CauHinhDauDiemMacDinh::where('id', $cauHinhMacDinhId)
            ->where('mon_hoc_id', $monHocId)
            ->firstOrFail();

        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:50|unique:cau_hinh_dau_diem_mac_dinh,ten_dau_diem,' . $cauHinhMacDinhId . ',id,mon_hoc_id,' . $monHocId,
            'ty_le' => 'required|numeric|min:0.01|max:100',
            'so_cot' => 'required|integer|min:1|max:20',
        ], [
            'ten_dau_diem.required' => 'Tên đầu điểm là bắt buộc',
            'ten_dau_diem.unique' => 'Tên đầu điểm này đã tồn tại',
            'ty_le.required' => 'Tỷ lệ % là bắt buộc',
            'ty_le.min' => 'Tỷ lệ % phải lớn hơn 0',
            'ty_le.max' => 'Tỷ lệ % không được vượt quá 100',
            'so_cot.required' => 'Số cột điểm là bắt buộc',
            'so_cot.min' => 'Số cột điểm phải lớn hơn 0',
            'so_cot.max' => 'Số cột điểm không được vượt quá 20',
        ]);

        // Kiểm tra tổng tỷ lệ
        $tongTyLe = CauHinhDauDiemMacDinh::where('mon_hoc_id', $monHocId)
            ->where('id', '!=', $cauHinhMacDinhId)
            ->sum('ty_le');
        if (($tongTyLe + $validated['ty_le']) > 100) {
            return back()->withErrors(['ty_le' => 'Tổng tỷ lệ không được vượt quá 100%. Tỷ lệ còn lại: ' . (100 - $tongTyLe) . '%'])->withInput();
        }

        DB::beginTransaction();
        try {
            $tenDauDiemCu = $cauHinh->ten_dau_diem;
            $cauHinh->update($validated);

            // Đồng bộ cấu hình cho các lớp học phần (chỉ lớp chưa có điểm nhập)
            // Nếu tên đầu điểm thay đổi, cần cập nhật cả tên ở lớp học phần
            if ($tenDauDiemCu != $validated['ten_dau_diem']) {
                $this->capNhatTenDauDiemLopHocPhan($monHocId, $tenDauDiemCu, $validated['ten_dau_diem']);
            }
            $this->dongBoCauHinhToLopHocPhan($monHocId, $validated['ten_dau_diem'], $cauHinh->id);

            DB::commit();

            return redirect()->route('dao-tao.mon-hoc.cau-hinh-diem', $monHocId)
                ->with('success', 'Đã cập nhật cấu hình đầu điểm thành công. Đã đồng bộ cho các lớp học phần chưa có điểm.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật cấu hình đầu điểm: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa cấu hình đầu điểm mặc định
     */
    public function destroyCauHinhDiem(string $monHocId, string $cauHinhMacDinhId)
    {
        $cauHinh = CauHinhDauDiemMacDinh::where('id', $cauHinhMacDinhId)
            ->where('mon_hoc_id', $monHocId)
            ->firstOrFail();

        $tenDauDiem = $cauHinh->ten_dau_diem;
        
        DB::beginTransaction();
        try {
            $cauHinh->delete();

            // Xóa cấu hình tương ứng ở các lớp học phần (chỉ lớp chưa có điểm nhập)
            $this->xoaCauHinhTuLopHocPhan($monHocId, $tenDauDiem);

            DB::commit();

            return redirect()->route('dao-tao.mon-hoc.cau-hinh-diem', $monHocId)
                ->with('success', 'Đã xóa cấu hình đầu điểm thành công. Đã xóa ở các lớp học phần chưa có điểm.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xóa cấu hình đầu điểm: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Đồng bộ cấu hình đầu điểm từ môn học sang các lớp học phần (chỉ lớp chưa có điểm nhập)
     */
    private function dongBoCauHinhToLopHocPhan($monHocId, $tenDauDiem, $cauHinhMacDinhId)
    {
        // Lấy cấu hình mặc định
        $cauHinhMacDinh = CauHinhDauDiemMacDinh::find($cauHinhMacDinhId);
        if (!$cauHinhMacDinh) {
            return;
        }

        // Lấy tất cả lớp học phần của môn học này
        $lopHocPhans = LopHocPhan::where('mon_hoc_id', $monHocId)->get();

        foreach ($lopHocPhans as $lopHocPhan) {
            // Kiểm tra lớp học phần đã có điểm nhập chưa
            $daCoDiem = NhapDiem::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhan) {
                $q->where('lop_hoc_phan_id', $lopHocPhan->id);
            })->exists();

            // Chỉ đồng bộ nếu lớp chưa có điểm nhập
            if (!$daCoDiem) {
                // Kiểm tra xem lớp đã có cấu hình với tên này chưa
                $existingCauHinh = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('ten_dau_diem', $tenDauDiem)
                    ->first();

                if ($existingCauHinh) {
                    // Cập nhật cấu hình hiện có
                    $existingCauHinh->update([
                        'ty_le' => $cauHinhMacDinh->ty_le,
                        'so_cot' => $cauHinhMacDinh->so_cot,
                    ]);
                } else {
                    // Tạo mới cấu hình
                    CauHinhDauDiem::create([
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'ten_dau_diem' => $cauHinhMacDinh->ten_dau_diem,
                        'ty_le' => $cauHinhMacDinh->ty_le,
                        'so_cot' => $cauHinhMacDinh->so_cot,
                    ]);
                }
            }
        }
    }

    /**
     * Cập nhật tên đầu điểm ở các lớp học phần khi tên đầu điểm mặc định thay đổi
     */
    private function capNhatTenDauDiemLopHocPhan($monHocId, $tenDauDiemCu, $tenDauDiemMoi)
    {
        // Lấy tất cả lớp học phần của môn học này
        $lopHocPhans = LopHocPhan::where('mon_hoc_id', $monHocId)->get();

        foreach ($lopHocPhans as $lopHocPhan) {
            // Kiểm tra lớp học phần đã có điểm nhập chưa
            $daCoDiem = NhapDiem::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhan) {
                $q->where('lop_hoc_phan_id', $lopHocPhan->id);
            })->exists();

            // Chỉ cập nhật nếu lớp chưa có điểm nhập
            if (!$daCoDiem) {
                CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('ten_dau_diem', $tenDauDiemCu)
                    ->update(['ten_dau_diem' => $tenDauDiemMoi]);
            }
        }
    }

    /**
     * Xóa cấu hình đầu điểm ở các lớp học phần (chỉ lớp chưa có điểm nhập)
     */
    private function xoaCauHinhTuLopHocPhan($monHocId, $tenDauDiem)
    {
        // Lấy tất cả lớp học phần của môn học này
        $lopHocPhans = LopHocPhan::where('mon_hoc_id', $monHocId)->get();

        foreach ($lopHocPhans as $lopHocPhan) {
            // Kiểm tra lớp học phần đã có điểm nhập chưa
            $daCoDiem = NhapDiem::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhan) {
                $q->where('lop_hoc_phan_id', $lopHocPhan->id);
            })->exists();

            // Chỉ xóa nếu lớp chưa có điểm nhập
            if (!$daCoDiem) {
                CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('ten_dau_diem', $tenDauDiem)
                    ->delete();
            }
        }
    }
}
