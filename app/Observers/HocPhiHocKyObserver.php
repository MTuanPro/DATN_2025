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
        // ✅ CHỈ TÍNH PHÍ DỊCH VỤ KHI CÓ ÍT NHẤT 1 MÔN HỌC (tong_tin_chi_dang_ky > 0)
        // Nếu không có môn nào hoặc tất cả môn đã bị hủy, thì không tính phí dịch vụ
        $phiDichVu = ($hocPhiHocKy->tong_tin_chi_dang_ky > 0) ? $hocPhiHocKy->phi_dich_vu : 0;
        
        // Tự động tính tổng số tiền = học phí môn học + phí dịch vụ (nếu có môn học)
        $hocPhiHocKy->tong_so_tien =
            $hocPhiHocKy->tong_hoc_phi_mon_hoc + $phiDichVu;

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
