<?php

namespace App\Observers;

use App\Models\LopHocPhanSinhVien;
use App\Models\LopHocPhan;
use App\Services\HocPhiService;
use Illuminate\Support\Facades\Log;

class LopHocPhanSinhVienObserver
{
    protected $hocPhiService;

    public function __construct(HocPhiService $hocPhiService)
    {
        $this->hocPhiService = $hocPhiService;
    }

    /**
     * Handle the LopHocPhanSinhVien "created" event.
     * Tự động tăng số lượng đăng ký khi thêm sinh viên
     * Tự động tính học phí khi sinh viên được xếp lớp
     */
    public function created(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            // 1. Tăng số lượng đăng ký
            $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
            if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                $lopHocPhan->increment('so_luong_dang_ky');
            }

            // 2. Tự động tính học phí cho sinh viên
            $this->tinhHocPhiTuDong($lhpsv);
        }
    }

    /**
     * Tính học phí tự động cho sinh viên khi được xếp lớp
     */
    protected function tinhHocPhiTuDong(LopHocPhanSinhVien $lhpsv): void
    {
        try {
            $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
            
            if (!$lopHocPhan) {
                Log::warning("⚠️ Không tìm thấy lớp học phần ID: {$lhpsv->lop_hoc_phan_id}");
                return;
            }

            $result = $this->hocPhiService->tinhHocPhiKhiDangKy(
                $lhpsv->sinh_vien_id,
                $lopHocPhan->hoc_ky_id,
                [$lhpsv->id]
            );

            if ($result) {
                Log::info("✅ [AUTO] Đã tính học phí cho sinh viên ID: {$lhpsv->sinh_vien_id} - Lớp: {$lopHocPhan->ma_lop_hp} - Tổng: " . number_format($result->tong_so_tien, 0, ',', '.') . " VND");
            } else {
                Log::warning("⚠️ [AUTO] Không thể tính học phí cho sinh viên ID: {$lhpsv->sinh_vien_id}");
            }
        } catch (\Exception $e) {
            Log::error("❌ [AUTO] Lỗi tính học phí tự động cho sinh viên ID: {$lhpsv->sinh_vien_id} - Lỗi: {$e->getMessage()}");
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "updated" event.
     * Cập nhật số lượng khi thay đổi trạng thái
     * Tự động tính học phí khi trạng thái chuyển sang "đã xếp lớp"
     */
    public function updated(LopHocPhanSinhVien $lhpsv): void
    {
        if ($lhpsv->isDirty('trang_thai')) {
            $oldStatus = $lhpsv->getOriginal('trang_thai');
            $newStatus = $lhpsv->trang_thai;

            // Trạng thái cũ không tính, trạng thái mới có tính -> tăng + tính học phí
            if (
                !in_array($oldStatus, ['da_xep_lop', 'dang_hoc']) &&
                in_array($newStatus, ['da_xep_lop', 'dang_hoc'])
            ) {
                $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
                if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                    $lopHocPhan->increment('so_luong_dang_ky');
                }

                // Tính học phí tự động khi chuyển trạng thái sang "đã xếp lớp"
                $this->tinhHocPhiTuDong($lhpsv);
            }

            // Trạng thái cũ có tính, trạng thái mới không tính -> giảm + hủy học phí
            if (
                in_array($oldStatus, ['da_xep_lop', 'dang_hoc']) &&
                !in_array($newStatus, ['da_xep_lop', 'dang_hoc'])
            ) {
                LopHocPhan::where('id', $lhpsv->lop_hoc_phan_id)
                    ->decrement('so_luong_dang_ky');

                // Hủy học phí khi hủy lớp
                $this->huyHocPhiTuDong($lhpsv);
            }
        }
    }

    /**
     * Hủy học phí tự động khi sinh viên rút khỏi lớp
     */
    protected function huyHocPhiTuDong(LopHocPhanSinhVien $lhpsv): void
    {
        try {
            $this->hocPhiService->huyHocPhiMonHoc($lhpsv->id);
            Log::info("✅ [AUTO] Đã hủy học phí cho sinh viên ID: {$lhpsv->sinh_vien_id}");
        } catch (\Exception $e) {
            Log::error("❌ [AUTO] Lỗi hủy học phí tự động: {$e->getMessage()}");
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "deleted" event.
     * Tự động giảm số lượng đăng ký khi xóa sinh viên
     */
    public function deleted(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            LopHocPhan::where('id', $lhpsv->lop_hoc_phan_id)
                ->decrement('so_luong_dang_ky');
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "restored" event.
     */
    public function restored(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
            if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                $lopHocPhan->increment('so_luong_dang_ky');
            }
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "force deleted" event.
     */
    public function forceDeleted(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            LopHocPhan::where('id', $lhpsv->lop_hoc_phan_id)
                ->decrement('so_luong_dang_ky');
        }
    }
}
