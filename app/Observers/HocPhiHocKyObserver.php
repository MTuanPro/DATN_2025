<?php

namespace App\Observers;

use App\Models\HocPhiHocKy;

class HocPhiHocKyObserver
{
    /**
     * Handle the HocPhiHocKy "saving" event.
     * Tự động tính toán các trường trước khi lưu
     */
    public function saving(HocPhiHocKy $hocPhiHocKy): void
    {
        // Tự động tính tổng số tiền = học phí môn học + phí dịch vụ
        $hocPhiHocKy->tong_so_tien =
            $hocPhiHocKy->tong_hoc_phi_mon_hoc + $hocPhiHocKy->phi_dich_vu;

        // Tự động tính số tiền còn lại = tổng số tiền - số tiền đã đóng
        $hocPhiHocKy->so_tien_con_lai =
            $hocPhiHocKy->tong_so_tien - $hocPhiHocKy->so_tien_da_dong;

        // Tự động cập nhật trạng thái
        if ($hocPhiHocKy->so_tien_con_lai <= 0) {
            $hocPhiHocKy->trang_thai = 'da_nop_du';
        } elseif ($hocPhiHocKy->so_tien_da_dong > 0) {
            $hocPhiHocKy->trang_thai = 'da_nop_mot_phan';
        } else {
            $hocPhiHocKy->trang_thai = 'chua_nop';
        }

        // Kiểm tra quá hạn
        if ($hocPhiHocKy->han_dong && now()->gt($hocPhiHocKy->han_dong) && $hocPhiHocKy->so_tien_con_lai > 0) {
            $hocPhiHocKy->trang_thai = 'qua_han';
        }
    }

    /**
     * Handle the HocPhiHocKy "created" event.
     */
    public function created(HocPhiHocKy $hocPhiHocKy): void
    {
        //
    }

    /**
     * Handle the HocPhiHocKy "updated" event.
     */
    public function updated(HocPhiHocKy $hocPhiHocKy): void
    {
        //
    }

    /**
     * Handle the HocPhiHocKy "deleted" event.
     */
    public function deleted(HocPhiHocKy $hocPhiHocKy): void
    {
        //
    }

    /**
     * Handle the HocPhiHocKy "restored" event.
     */
    public function restored(HocPhiHocKy $hocPhiHocKy): void
    {
        //
    }

    /**
     * Handle the HocPhiHocKy "force deleted" event.
     */
    public function forceDeleted(HocPhiHocKy $hocPhiHocKy): void
    {
        //
    }
}
