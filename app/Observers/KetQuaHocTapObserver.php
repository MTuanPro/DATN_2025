<?php

namespace App\Observers;

use App\Models\KetQuaHocTap;

class KetQuaHocTapObserver
{
    /**
     * Handle the KetQuaHocTap "saving" event.
     * Tự động convert điểm và check qua môn
     */
    public function saving(KetQuaHocTap $ketQua): void
    {
        if ($ketQua->diem_he_10 !== null) {
            // Tự động convert điểm hệ 4 (chỉ nếu chưa được set)
            if ($ketQua->diem_he_4 === null) {
                $ketQua->diem_he_4 = $this->convertTo4Scale($ketQua->diem_he_10);
            }

            // Tự động convert điểm chữ (chỉ nếu chưa được set)
            if ($ketQua->diem_chu === null) {
                $ketQua->diem_chu = $this->convertToLetter($ketQua->diem_he_10);
            }

            // Tự động check qua môn (chỉ nếu chưa được set từ DiemService)
            // DiemService sẽ tính toán qua_mon dựa trên điểm F và tỷ lệ vắng
            if ($ketQua->qua_mon === null) {
                // Kiểm tra điểm chữ = F → trượt
                $diemChu = $ketQua->diem_chu ?? $this->convertToLetter($ketQua->diem_he_10);
                if ($diemChu === 'F') {
                    $ketQua->qua_mon = false;
                } else {
                    $ketQua->qua_mon = $ketQua->diem_he_10 >= 4.0;
                }
            }
        }
    }

    /**
     * Convert điểm hệ 10 sang hệ 4
     * 
     * @param float $diem10
     * @return float
     */
    private function convertTo4Scale($diem10): float
    {
        if ($diem10 >= 9.0) return 4.0;
        if ($diem10 >= 8.5) return 3.7;
        if ($diem10 >= 8.0) return 3.5;
        if ($diem10 >= 7.0) return 3.0;
        if ($diem10 >= 6.5) return 2.5;
        if ($diem10 >= 5.5) return 2.0;
        if ($diem10 >= 5.0) return 1.5;
        if ($diem10 >= 4.0) return 1.0;
        return 0.0;
    }

    /**
     * Convert điểm hệ 10 sang điểm chữ
     * 
     * @param float $diem10
     * @return string
     */
    private function convertToLetter($diem10): string
    {
        if ($diem10 >= 9.0) return 'A';
        if ($diem10 >= 8.5) return 'B+';
        if ($diem10 >= 8.0) return 'B';
        if ($diem10 >= 7.0) return 'C+';
        if ($diem10 >= 6.5) return 'C';
        if ($diem10 >= 5.5) return 'D+';
        if ($diem10 >= 5.0) return 'D';
        if ($diem10 >= 4.0) return 'D';
        return 'F';
    }

    /**
     * Handle the KetQuaHocTap "created" event.
     */
    public function created(KetQuaHocTap $ketQua): void
    {
        // Gửi thông báo khi điểm mới được tạo
        $this->sendGradeNotification($ketQua);
    }

    /**
     * Handle the KetQuaHocTap "updated" event.
     */
    public function updated(KetQuaHocTap $ketQua): void
    {
        // Gửi thông báo khi điểm được cập nhật
        if ($ketQua->wasChanged('diem_he_10') && $ketQua->diem_he_10 !== null) {
            $this->sendGradeNotification($ketQua);
        }
    }

    /**
     * Gửi thông báo điểm cho sinh viên
     */
    private function sendGradeNotification(KetQuaHocTap $ketQua): void
    {
        try {
            if ($ketQua->diem_he_10 !== null && $ketQua->lopHocPhanSinhVien && $ketQua->lopHocPhanSinhVien->sinhVien) {
                $notificationService = app(\App\Services\NotificationService::class);
                $sinhVien = $ketQua->lopHocPhanSinhVien->sinhVien;
                $monHoc = $ketQua->lopHocPhanSinhVien->lopHocPhan->monHoc ?? null;

                if ($monHoc) {
                    $notificationService->sendGradeNotification(
                        $sinhVien->id,
                        $monHoc->ten_mon,
                        $ketQua->diem_he_10
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo điểm: ' . $e->getMessage());
        }
    }

    /**
     * Handle the KetQuaHocTap "deleted" event.
     */
    public function deleted(KetQuaHocTap $ketQua): void
    {
        //
    }

    /**
     * Handle the KetQuaHocTap "restored" event.
     */
    public function restored(KetQuaHocTap $ketQua): void
    {
        //
    }

    /**
     * Handle the KetQuaHocTap "force deleted" event.
     */
    public function forceDeleted(KetQuaHocTap $ketQua): void
    {
        //
    }
}
