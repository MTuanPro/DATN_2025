<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Lấy sinh viên test
$sinhVien = DB::table('sinh_vien')->where('ma_sinh_vien', 'SV25068')->first();

if (!$sinhVien) {
    echo "❌ Không tìm thấy sinh viên SV25068\n";
    exit;
}

echo "📊 Kiểm tra dữ liệu học tập của sinh viên: {$sinhVien->ho_ten}\n";
echo "   Kỳ hiện tại: {$sinhVien->ky_hien_tai}\n\n";

// Đếm tổng số môn đã học
$tongMon = DB::table('ket_qua_hoc_tap')
    ->join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
    ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
    ->count();

echo "📚 Tổng số môn đã học: {$tongMon}\n";

// Đếm số điểm đã nhập
$tongDiem = DB::table('nhap_diem')
    ->join('lop_hoc_phan_sinh_vien', 'nhap_diem.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
    ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
    ->count();

echo "📝 Tổng số điểm đã nhập: {$tongDiem}\n";

// Đếm số buổi điểm danh
$tongDiemDanh = DB::table('diem_danh')
    ->join('lop_hoc_phan_sinh_vien', 'diem_danh.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
    ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
    ->count();

echo "✅ Tổng số buổi điểm danh: {$tongDiemDanh}\n";

// Đếm số lịch học
$tongLichHoc = DB::table('lich_hoc_chi_tiet')
    ->join('lop_hoc_phan', 'lich_hoc_chi_tiet.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
    ->join('lop_hoc_phan_sinh_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id')
    ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
    ->distinct('lich_hoc_chi_tiet.id')
    ->count('lich_hoc_chi_tiet.id');

echo "📅 Tổng số buổi học: {$tongLichHoc}\n\n";

// Kiểm tra từng môn
$ketQua = DB::table('ket_qua_hoc_tap')
    ->join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
    ->join('lop_hoc_phan', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
    ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
    ->join('hoc_ky', 'lop_hoc_phan.hoc_ky_id', '=', 'hoc_ky.id')
    ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
    ->select(
        'mon_hoc.ma_mon',
        'mon_hoc.ten_mon',
        'hoc_ky.ten_hoc_ky',
        'hoc_ky.nam_hoc',
        'ket_qua_hoc_tap.diem_he_10',
        'ket_qua_hoc_tap.qua_mon',
        'lop_hoc_phan_sinh_vien.id as lhp_sv_id'
    )
    ->orderBy('hoc_ky.ngay_bat_dau')
    ->get();

echo "📋 Chi tiết từng môn:\n";
echo str_repeat("=", 120) . "\n";
printf(
    "%-10s | %-35s | %-20s | %-8s | %-8s | %-10s | %-10s\n",
    "Mã môn",
    "Tên môn",
    "Học kỳ",
    "Điểm",
    "Qua môn",
    "Số điểm",
    "Điểm danh"
);
echo str_repeat("=", 120) . "\n";

foreach ($ketQua as $kq) {
    // Đếm số điểm đã nhập
    $soDiem = DB::table('nhap_diem')
        ->where('lop_hoc_phan_sinh_vien_id', $kq->lhp_sv_id)
        ->count();

    // Đếm số buổi điểm danh
    $soDiemDanh = DB::table('diem_danh')
        ->where('lop_hoc_phan_sinh_vien_id', $kq->lhp_sv_id)
        ->count();

    printf(
        "%-10s | %-35s | %-20s | %-8s | %-8s | %-10s | %-10s\n",
        $kq->ma_mon,
        substr($kq->ten_mon, 0, 35),
        $kq->ten_hoc_ky . ' ' . $kq->nam_hoc,
        $kq->diem_he_10,
        $kq->qua_mon ? 'Có' : 'Không',
        $soDiem,
        $soDiemDanh
    );
}

echo str_repeat("=", 120) . "\n";
