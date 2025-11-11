<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\SinhVien;
use App\Models\LopHocPhanSinhVien;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA ĐĂNG KÝ LỚP CỦA SINH VIÊN 25010012 ===\n\n";

$sinhVien = SinhVien::where('ma_sinh_vien', '25010012')->first();

if (!$sinhVien) {
    echo "Không tìm thấy sinh viên\n";
    exit;
}

$lops = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
    ->with('lopHocPhan')
    ->get();

echo "Sinh viên: {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten}\n";
echo "Tổng số lớp đăng ký: {$lops->count()}\n\n";

foreach ($lops as $lop) {
    echo "LHPSV ID={$lop->id}, Lop HP ID={$lop->lop_hoc_phan_id}, Mã lớp={$lop->lopHocPhan->ma_lop_hp}\n";
}

echo "\n=== VẤN ĐỀ ===\n";
echo "Seeder BaoCaoDataSeeder đã tạo sai dữ liệu điểm danh!\n";
echo "Điểm danh phải liên kết với đúng lich_hoc_chi_tiet của lop_hoc_phan tương ứng.\n";
echo "Nhưng hiện tại có điểm danh của sinh viên từ nhiều lớp khác nhau được gán cùng một lop_hoc_phan_sinh_vien_id.\n";
