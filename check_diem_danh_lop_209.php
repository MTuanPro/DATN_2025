<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\LopHocPhanSinhVien;
use App\Models\DiemDanh;
use App\Models\LichHocChiTiet;
use App\Models\SinhVien;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA DỮ LIỆU ĐIỂM DANH LỚP 209 ===\n\n";

// Lấy sinh viên 25010012
$sinhVien = SinhVien::where('ma_sinh_vien', '25010012')->first();

if (!$sinhVien) {
    echo "Không tìm thấy sinh viên 25010012\n";
    exit;
}

echo "Sinh viên: {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten}\n\n";

// Lấy lop_hoc_phan_sinh_vien
$lhpsv = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
    ->where('lop_hoc_phan_id', 209)
    ->first();

if (!$lhpsv) {
    echo "Sinh viên không có trong lớp 209\n";
    exit;
}

echo "Lop_hoc_phan_sinh_vien ID: {$lhpsv->id}\n\n";

// Đếm buổi học
$tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', 209)
    ->where('ngay_hoc', '<=', now())
    ->count();

echo "Tổng buổi học (đã diễn ra): $tongBuoiHoc\n\n";

// Đếm điểm danh
$diemDanhRecords = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)->get();

echo "Tổng records điểm danh: {$diemDanhRecords->count()}\n";
echo "Có mặt: " . $diemDanhRecords->where('trang_thai', 'co_mat')->count() . "\n";
echo "Vắng: " . $diemDanhRecords->where('trang_thai', 'vang')->count() . "\n";
echo "Đi trễ: " . $diemDanhRecords->where('trang_thai', 'di_tre')->count() . "\n";
echo "Nghỉ phép: " . $diemDanhRecords->where('trang_thai', 'nghi_phep')->count() . "\n\n";

// Kiểm tra duplicate
echo "Chi tiết điểm danh:\n";
foreach ($diemDanhRecords as $dd) {
    $lichHoc = LichHocChiTiet::find($dd->lich_hoc_chi_tiet_id);
    echo "  - DiemDanh ID={$dd->id}, LichHoc ID={$dd->lich_hoc_chi_tiet_id}";
    echo ", Lop HP ID=" . ($lichHoc ? $lichHoc->lop_hoc_phan_id : 'N/A');
    echo ", Ngày=" . ($lichHoc ? $lichHoc->ngay_hoc : 'N/A');
    echo ", Status={$dd->trang_thai}\n";
}

$grouped = $diemDanhRecords->groupBy('lich_hoc_chi_tiet_id');
echo "\nNhóm theo buổi học:\n";
foreach ($grouped as $lichHocId => $items) {
    echo "  Lich_hoc_chi_tiet_id=$lichHocId: {$items->count()} record(s)";
    if ($items->count() > 1) {
        echo " ⚠️ DUPLICATE!";
    }
    echo "\n";
}

// Kiểm tra toàn bộ lớp
echo "\n=== KIỂM TRA TOÀN BỘ LỚP 209 ===\n\n";

$allStudents = LopHocPhanSinhVien::where('lop_hoc_phan_id', 209)
    ->where('trang_thai', 'dang_hoc')
    ->with('sinhVien')
    ->get();

echo "Tổng sinh viên: {$allStudents->count()}\n\n";

foreach ($allStudents as $lhpsv) {
    $ddCount = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)->count();
    $coMat = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
        ->where('trang_thai', 'co_mat')
        ->count();
    
    if ($ddCount > $tongBuoiHoc || $coMat > $tongBuoiHoc) {
        echo "⚠️ {$lhpsv->sinhVien->ma_sinh_vien}: Tổng DD=$ddCount, Có mặt=$coMat (Buổi học=$tongBuoiHoc)\n";
    }
}
