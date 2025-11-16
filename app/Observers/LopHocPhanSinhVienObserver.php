<?php

namespace App\Observers;

use App\Models\LopHocPhanSinhVien;
use App\Models\LopHocPhan;

class LopHocPhanSinhVienObserver
{
    /**
     * Handle the LopHocPhanSinhVien "created" event.
     * Tự động tăng số lượng đăng ký khi thêm sinh viên
     */
    public function created(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
            if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                $lopHocPhan->increment('so_luong_dang_ky');
            }
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "updated" event.
     * Cập nhật số lượng khi thay đổi trạng thái
     */
    public function updated(LopHocPhanSinhVien $lhpsv): void
    {
        if ($lhpsv->isDirty('trang_thai')) {
            $oldStatus = $lhpsv->getOriginal('trang_thai');
            $newStatus = $lhpsv->trang_thai;

            // Trạng thái cũ không tính, trạng thái mới có tính -> tăng
            if (
                !in_array($oldStatus, ['da_xep_lop', 'dang_hoc']) &&
                in_array($newStatus, ['da_xep_lop', 'dang_hoc'])
            ) {
                $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
                if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                    $lopHocPhan->increment('so_luong_dang_ky');
                }
            }

            // Trạng thái cũ có tính, trạng thái mới không tính -> giảm
            if (
                in_array($oldStatus, ['da_xep_lop', 'dang_hoc']) &&
                !in_array($newStatus, ['da_xep_lop', 'dang_hoc'])
            ) {
                LopHocPhan::where('id', $lhpsv->lop_hoc_phan_id)
                    ->decrement('so_luong_dang_ky');
            }
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
