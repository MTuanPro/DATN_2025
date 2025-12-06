<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA ĐIỂM DANH LỚP 111 ===\n\n";

// Tổng số bản ghi điểm danh
$totalRecords = \App\Models\DiemDanh::whereHas('lopHocPhanSinhVien', function ($q) {
    $q->where('lop_hoc_phan_id', 111);
})->count();
echo "Tổng số bản ghi điểm danh: {$totalRecords}\n";

// Số buổi học unique
$uniqueSessions = \App\Models\DiemDanh::whereHas('lopHocPhanSinhVien', function ($q) {
    $q->where('lop_hoc_phan_id', 111);
})
    ->distinct('lich_hoc_chi_tiet_id')
    ->count('lich_hoc_chi_tiet_id');
echo "Số buổi học unique: {$uniqueSessions}\n\n";

// Chi tiết từng sinh viên
$sinhViens = \App\Models\LopHocPhanSinhVien::where('lop_hoc_phan_id', 111)
    ->with('sinhVien')
    ->get();

echo "=== CHI TIẾT TỪNG SINH VIÊN ===\n\n";
foreach ($sinhViens as $sv) {
    $maSV = $sv->sinhVien->ma_sinh_vien ?? 'N/A';
    $hoTen = $sv->sinhVien->ho_ten ?? 'N/A';

    $tongBuoiDiemDanh = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)->count();
    $buoiCoMat = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)
        ->where('trang_thai', 'co_mat')
        ->count();

    $tyLe = $uniqueSessions > 0 ? round(($buoiCoMat / $uniqueSessions) * 100, 1) : 0;

    echo "- {$maSV} ({$hoTen}):\n";
    echo "  + Tổng buổi điểm danh: {$tongBuoiDiemDanh}\n";
    echo "  + Buổi có mặt: {$buoiCoMat}\n";
    echo "  + Tỷ lệ: {$tyLe}% ({$buoiCoMat}/{$uniqueSessions})\n\n";
}
