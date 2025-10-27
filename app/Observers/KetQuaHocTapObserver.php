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
            // Tự động convert điểm hệ 4
            $ketQua->diem_he_4 = $this->convertTo4Scale($ketQua->diem_he_10);

            // Tự động convert điểm chữ
            $ketQua->diem_chu = $this->convertToLetter($ketQua->diem_he_10);

            // Tự động check qua môn
            $ketQua->qua_mon = $ketQua->diem_he_10 >= 4.0;
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
        //
    }

    /**
     * Handle the KetQuaHocTap "updated" event.
     */
    public function updated(KetQuaHocTap $ketQua): void
    {
        //
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
