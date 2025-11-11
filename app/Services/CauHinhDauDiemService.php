<?php

namespace App\Services;

use App\Models\CauHinhDauDiem;

class CauHinhDauDiemService
{
    /**
     * Validate tổng tỷ lệ = 100%
     * Vì mỗi đầu điểm có so_cot = 1, chỉ cần sum(ty_le)
     * 
     * @param int $lopHocPhanId
     * @param float $tyLeNew Tỷ lệ % cho mỗi cột
     * @param int $soCotNew Số cột điểm (số đầu điểm sẽ tạo)
     * @param int|null $excludeId ID cấu hình đang sửa (để loại trừ khỏi tổng)
     * @return array
     */
    public function validateTotalPercentage($lopHocPhanId, $tyLeNew, $soCotNew = 1, $excludeId = null): array
    {
        $query = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Tính tổng tỷ lệ hiện tại (chỉ sum ty_le vì mỗi đầu điểm có 1 cột)
        $tongTyLe = $query->sum('ty_le');
        
        // Tổng tỷ lệ mới sẽ thêm = tyLeNew × soCotNew
        $newItemTotal = $tyLeNew * $soCotNew;
        $newTotal = $tongTyLe + $newItemTotal;

        return [
            'passed' => $newTotal <= 100,
            'current_total' => $tongTyLe,
            'new_total' => $newTotal,
            'new_item_total' => $newItemTotal,
            'remaining' => 100 - $tongTyLe,
            'max' => 100
        ];
    }

    /**
     * Kiểm tra tất cả điều kiện khi thêm/sửa cấu hình đầu điểm
     * 
     * @param int $lopHocPhanId
     * @param string $tenDauDiem
     * @param float $tyLe Tỷ lệ % cho mỗi cột
     * @param int $soCot Số cột điểm
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

        // 3. Kiểm tra tổng tỷ lệ (ty_le × so_cot)
        if (empty($errors)) {
            $percentCheck = $this->validateTotalPercentage($lopHocPhanId, $tyLe, $soCot, $excludeId);
            if (!$percentCheck['passed']) {
                $errors[] = "Tổng tỷ lệ vượt quá 100% (Hiện tại: {$percentCheck['current_total']}%, Thêm: {$tyLe}% × {$soCot} cột = {$percentCheck['new_item_total']}%, Tổng: {$percentCheck['new_total']}%)";
            }
        }

        // 4. Kiểm tra trùng tên đầu điểm
        // Nếu số cột > 1, kiểm tra cả tên có số đếm (VD: quiz 1, quiz 2, ...)
        $existingNames = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->when($excludeId, function($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->pluck('ten_dau_diem')
            ->toArray();

        if ($soCot == 1) {
            // Kiểm tra tên đơn
            if (in_array($tenDauDiem, $existingNames)) {
                $errors[] = 'Tên đầu điểm đã tồn tại trong lớp học phần này';
            }
        } else {
            // Kiểm tra tên có số đếm (quiz 1, quiz 2, ...)
            for ($i = 1; $i <= $soCot; $i++) {
                $tenCoSoDem = $tenDauDiem . ' ' . $i;
                if (in_array($tenCoSoDem, $existingNames)) {
                    $errors[] = "Tên đầu điểm '{$tenCoSoDem}' đã tồn tại";
                    break; // Chỉ báo lỗi 1 lần
                }
            }
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

        // Tính tổng tỷ lệ: Mỗi đầu điểm có so_cot = 1, nên chỉ cần sum(ty_le)
        $tongTyLe = $cauHinhs->sum('ty_le');
        $tongCot = $cauHinhs->count(); // Số lượng đầu điểm = số cột

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
