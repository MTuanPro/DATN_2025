<?php

namespace App\Http\Controllers\DaoTao\CTDT;

use App\Http\Controllers\Controller;
use App\Models\Daotao\MonHoc;
use App\Models\Daotao\MonHocTienQuyet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonHocTienQuyetController extends Controller
{
    /**
     * Hiển thị danh sách môn tiên quyết của một môn học
     */
    public function index($monHocId)
    {
        $monHoc = MonHoc::with([
            'khoa',
            'monTienQuyet' => function ($query) {
                $query->with('khoa')->withPivot('id', 'loai_tien_quyet', 'dieu_kien_qua_mon', 'ghi_chu');
            },
            'monCanMonNay' => function ($query) {
                $query->with('khoa')->withPivot('id', 'loai_tien_quyet', 'dieu_kien_qua_mon', 'ghi_chu');
            }
        ])->findOrFail($monHocId);

        // Lấy danh sách môn học có thể thêm làm tiên quyết
        $daTienQuyetIds = $monHoc->monTienQuyet->pluck('id')->toArray();
        $daTienQuyetIds[] = $monHocId; // Thêm chính nó

        $danhSachMonHoc = MonHoc::with('khoa')
            ->whereNotIn('id', $daTienQuyetIds)
            ->orderBy('ma_mon')
            ->get();

        return view('daotao.mon-hoc.tien-quyet', compact('monHoc', 'danhSachMonHoc'));
    }
    /**
     * Thêm môn tiên quyết
     */
    public function store(Request $request, $monHocId)
    {
        $validated = $request->validate([
            'mon_tien_quyet_id' => 'required|exists:mon_hoc,id',
            'loai_tien_quyet' => 'required|in:bat_buoc,khuyen_nghi',
            'dieu_kien_qua_mon' => 'required|boolean',
            'ghi_chu' => 'nullable|string|max:500',
        ], [
            'mon_tien_quyet_id.required' => 'Vui lòng chọn môn tiên quyết',
            'mon_tien_quyet_id.exists' => 'Môn học không tồn tại',
            'loai_tien_quyet.required' => 'Vui lòng chọn loại tiên quyết',
            'loai_tien_quyet.in' => 'Loại tiên quyết không hợp lệ',
            'dieu_kien_qua_mon.required' => 'Vui lòng chọn điều kiện qua môn',
            'ghi_chu.max' => 'Ghi chú không được quá 500 ký tự',
        ]);

        // Kiểm tra không thể thêm chính nó làm tiên quyết
        if ($monHocId == $validated['mon_tien_quyet_id']) {
            return redirect()->back()
                ->with('error', 'Không thể thêm chính môn học này làm môn tiên quyết!')
                ->withInput();
        }

        // Kiểm tra đã tồn tại chưa
        $exists = MonHocTienQuyet::where('mon_hoc_id', $monHocId)
            ->where('mon_tien_quyet_id', $validated['mon_tien_quyet_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Môn tiên quyết này đã tồn tại!')
                ->withInput();
        }

        // Kiểm tra vòng lặp tiên quyết (circular dependency)
        if ($this->detectCircularDependency($monHocId, $validated['mon_tien_quyet_id'])) {
            $monHoc = MonHoc::find($monHocId);
            $monTienQuyet = MonHoc::find($validated['mon_tien_quyet_id']);

            return redirect()->back()
                ->with('error', "Phát hiện vòng lặp tiên quyết! Môn '{$monTienQuyet->ten_mon}' đã có '{$monHoc->ten_mon}' là môn tiên quyết (trực tiếp hoặc gián tiếp).")
                ->withInput();
        }

        DB::beginTransaction();
        try {
            MonHocTienQuyet::create([
                'mon_hoc_id' => $monHocId,
                'mon_tien_quyet_id' => $validated['mon_tien_quyet_id'],
                'loai_tien_quyet' => $validated['loai_tien_quyet'],
                'dieu_kien_qua_mon' => $validated['dieu_kien_qua_mon'],
                'ghi_chu' => $validated['ghi_chu'],
            ]);

            DB::commit();
            return redirect()->route('dao-tao.mon-hoc.tien-quyet', $monHocId)
                ->with('success', 'Thêm môn tiên quyết thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cập nhật môn tiên quyết
     */
    public function update(Request $request, $monHocId, $tienQuyetId)
    {
        $validated = $request->validate([
            'loai_tien_quyet' => 'required|in:bat_buoc,khuyen_nghi',
            'dieu_kien_qua_mon' => 'required|boolean',
            'ghi_chu' => 'nullable|string|max:500',
        ], [
            'loai_tien_quyet.required' => 'Vui lòng chọn loại tiên quyết',
            'loai_tien_quyet.in' => 'Loại tiên quyết không hợp lệ',
            'dieu_kien_qua_mon.required' => 'Vui lòng chọn điều kiện qua môn',
            'ghi_chu.max' => 'Ghi chú không được quá 500 ký tự',
        ]);

        $monTienQuyet = MonHocTienQuyet::where('mon_hoc_id', $monHocId)
            ->where('id', $tienQuyetId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $monTienQuyet->update($validated);

            DB::commit();
            return redirect()->route('dao-tao.mon-hoc.tien-quyet', $monHocId)
                ->with('success', 'Cập nhật môn tiên quyết thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa môn tiên quyết
     */
    public function destroy($monHocId, $tienQuyetId)
    {
        $monTienQuyet = MonHocTienQuyet::where('mon_hoc_id', $monHocId)
            ->where('id', $tienQuyetId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $monTienQuyet->delete();

            DB::commit();
            return redirect()->route('dao-tao.mon-hoc.tien-quyet', $monHocId)
                ->with('success', 'Xóa môn tiên quyết thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra vòng lặp tiên quyết (circular dependency)
     * Trả về true nếu phát hiện vòng lặp
     * 
     * Logic: Nếu môn A muốn thêm môn B làm tiên quyết,
     * cần kiểm tra xem môn B có phụ thuộc vào môn A không (trực tiếp hoặc gián tiếp)
     * 
     * Ví dụ vòng lặp:
     * - Trực tiếp: A → B và B → A
     * - Gián tiếp: A → B → C → A
     */
    private function detectCircularDependency($monHocId, $monTienQuyetId, $visited = [])
    {
        // Nếu đã thăm môn này rồi, có vòng lặp
        if (in_array($monTienQuyetId, $visited)) {
            return true;
        }

        // Thêm môn hiện tại vào danh sách đã thăm
        $visited[] = $monTienQuyetId;

        // Lấy tất cả môn tiên quyết của môn đang xét
        $cacMonTienQuyet = MonHocTienQuyet::where('mon_hoc_id', $monTienQuyetId)
            ->pluck('mon_tien_quyet_id')
            ->toArray();

        // Nếu trong danh sách môn tiên quyết có môn gốc, có vòng lặp
        if (in_array($monHocId, $cacMonTienQuyet)) {
            return true;
        }

        // Đệ quy kiểm tra các môn tiên quyết của các môn tiên quyết
        foreach ($cacMonTienQuyet as $monId) {
            if ($this->detectCircularDependency($monHocId, $monId, $    }
}
