<?php

namespace App\Services;

use App\Models\MonHocTienQuyet;
use App\Models\MonHoc;

class MonHocTienQuyetService
{
    /**
     * Kiểm tra vòng lặp môn tiên quyết (circular dependency)
     * 
     * @param int $monHocId
     * @param int $monTienQuyetId
     * @return bool true nếu có vòng lặp, false nếu không
     */
    public function checkCircularDependency($monHocId, $monTienQuyetId): bool
    {
        $visited = [$monHocId];
        $queue = [$monTienQuyetId];

        while (!empty($queue)) {
            $current = array_shift($queue);

            // Nếu gặp lại môn đã visit -> có vòng lặp
            if (in_array($current, $visited)) {
                return true;
            }

            $visited[] = $current;

            // Lấy các môn tiên quyết của môn hiện tại
            $children = MonHocTienQuyet::where('mon_hoc_id', $current)
                ->pluck('mon_tien_quyet_id')
                ->toArray();

            $queue = array_merge($queue, $children);
        }

        return false;
    }

    /**
     * Lấy toàn bộ cây môn tiên quyết
     * 
     * @param int $monHocId
     * @return array
     */
    public function getPrerequisiteTree($monHocId): array
    {
        $tree = [];
        $this->buildTree($monHocId, $tree, 0);
        return $tree;
    }

    /**
     * Build cây môn tiên quyết đệ quy
     * 
     * @param int $monHocId
     * @param array &$tree
     * @param int $level
     */
    private function buildTree($monHocId, &$tree, $level = 0)
    {
        $prerequisites = MonHocTienQuyet::where('mon_hoc_id', $monHocId)
            ->with('monTienQuyet')
            ->get();

        foreach ($prerequisites as $prereq) {
            $tree[] = [
                'level' => $level,
                'mon_hoc' => $prereq->monTienQuyet,
                'loai' => $prereq->loai_tien_quyet,
                'dieu_kien_qua_mon' => $prereq->dieu_kien_qua_mon
            ];

            // Đệ quy lấy môn tiên quyết của môn tiên quyết
            $this->buildTree($prereq->mon_tien_quyet_id, $tree, $level + 1);
        }
    }

    /**
     * Validate trước khi thêm môn tiên quyết
     * 
     * @param int $monHocId
     * @param int $monTienQuyetId
     * @return array
     */
    public function validateAddPrerequisite($monHocId, $monTienQuyetId): array
    {
        $errors = [];

        // 1. Kiểm tra môn học có tồn tại
        if (!MonHoc::find($monHocId)) {
            $errors[] = 'Môn học không tồn tại';
        }

        if (!MonHoc::find($monTienQuyetId)) {
            $errors[] = 'Môn tiên quyết không tồn tại';
        }

        // 2. Kiểm tra không trùng
        if ($monHocId === $monTienQuyetId) {
            $errors[] = 'Môn học không thể là môn tiên quyết của chính nó';
        }

        // 3. Kiểm tra đã tồn tại chưa
        $exists = MonHocTienQuyet::where('mon_hoc_id', $monHocId)
            ->where('mon_tien_quyet_id', $monTienQuyetId)
            ->exists();

        if ($exists) {
            $errors[] = 'Môn tiên quyết này đã được thêm';
        }

        // 4. Kiểm tra vòng lặp
        if (empty($errors) && $this->checkCircularDependency($monHocId, $monTienQuyetId)) {
            $errors[] = 'Không thể thêm môn tiên quyết này vì tạo vòng lặp phụ thuộc';
        }

        return [
            'passed' => empty($errors),
            'errors' => $errors
        ];
    }
}
