<?php

namespace App\Services;

use App\Models\CauHinhDauDiem;

class CauHinhDauDiemService
{
    /**
     * Validate tổng tỷ lệ = 100%
     * 
     * @param int $lopHocPhanId
     * @param float $tyLeNew
     * @param int|null $excludeId ID cấu hình đang sửa (để loại trừ khỏi tổng)
     * @return array
     */
    public function validateTotalPercentage($lopHocPhanId, $tyLeNew, $excludeId = null): array
    {
        $query = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $tongTyLe = $query->sum('ty_le');
        $newTotal = $tongTyLe + $tyLeNew;

        return [
            'passed' => $newTotal <= 100,
            'current_total' => $tongTyLe,
            'new_total' => $newTotal,
            'remaining' => 100 - $tongTyLe,
            'max' => 100
        ];
    }

    /**
     * Kiểm tra tất cả điều kiện khi thêm/sửa cấu hình đầu điểm
     * 
     * @param int $lopHocPhanId
     * @param string $tenDauDiem
     * @param float $tyLe
     * @param int $soCot
     * @param int|null $excludeId
     * @return array
     */
    public function validateCauHinh($lopHocPhanId, $tenDauDiem, $tyLe, $soCot, $excludeId = null): array
    {
        $errors = [];

        // 1. Kiểm tra tỷ lệ > 0 và <= 100
        if ($tyLe <= 0 || $tyLe > 100) {
            $errors[] = 'Tỷ lệ phải lớn hơn 0 và nhỏ hơn hoặc bằng 100%';
        }

        // 2. Kiểm tra số cột >= 1
        if ($soCot < 1) {
            $errors[] = 'Số cột điểm phải >= 1';
        }

        // 3. Kiểm tra tổng tỷ lệ
        if (empty($errors)) {
            $percentCheck = $this->validateTotalPercentage($lopHocPhanId, $tyLe, $excludeId);
            if (!$percentCheck['passed']) {
                $errors[] = "Tổng tỷ lệ vượt quá 100% (Hiện tại: {$percentCheck['current_total']}%, Thêm: {$tyLe}% = {$percentCheck['new_total']}%)";
            }
        }

        // 4. Kiểm tra trùng tên đầu điểm
        $query = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('ten_dau_diem', $tenDauDiem);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            $errors[] = 'Tên đầu điểm đã tồn tại trong lớp học phần này';
        }

        return [
            'passed' => empty($errors),
            'errors' => $errors,
            'details' => [
                'percentage' => $percentCheck ?? null
            ]
        ];
    }

    /**
     * Lấy thông tin tổng quan cấu hình đầu điểm của lớp
     * 
     * @param int $lopHocPhanId
     * @return array
     */
    public function getSummary($lopHocPhanId): array
    {
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)->get();

        $tongTyLe = $cauHinhs->sum('ty_le');
        $tongCot = $cauHinhs->sum('so_cot');

        return [
            'total_percentage' => $tongTyLe,
            'remaining_percentage' => 100 - $tongTyLe,
            'total_columns' => $tongCot,
            'count' => $cauHinhs->count(),
            'is_complete' => $tongTyLe == 100,
            'configs' => $cauHinhs->toArray()
        ];
    }

    /**
     * Kiểm tra xem cấu hình đã đủ 100% chưa
     * 
     * @param int $lopHocPhanId
     * @return bool
     */
    public function isComplete($lopHocPhanId): bool
    {
        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)->sum('ty_le');
        return $tongTyLe == 100;
    }
}
