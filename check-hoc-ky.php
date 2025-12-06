<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DANH SÁCH HỌC KỲ ===\n\n";

$hocKys = DB::table('hoc_ky')
    ->orderBy('ngay_bat_dau')
    ->get(['id', 'ten_hoc_ky', 'nam_hoc', 'la_hoc_ky_hien_tai', 'ngay_bat_dau']);

echo "Tổng số học kỳ: " . $hocKys->count() . "\n\n";

foreach ($hocKys as $hk) {
    $status = $hk->la_hoc_ky_hien_tai ? '✅ Hiện tại' : '📅 Quá khứ';
    echo "  [{$hk->id}] {$hk->ten_hoc_ky} {$hk->nam_hoc} - {$status}\n";
    echo "      Ngày bắt đầu: {$hk->ngay_bat_dau}\n";
}

echo "\n=== PHÂN TÍCH ===\n";
$hocKyHienTai = DB::table('hoc_ky')->where('la_hoc_ky_hien_tai', true)->first();
echo "Học kỳ hiện tại: {$hocKyHienTai->ten_hoc_ky} {$hocKyHienTai->nam_hoc}\n";

$hocKyQuaKhu = DB::table('hoc_ky')->where('la_hoc_ky_hien_tai', false)->count();
echo "Số học kỳ quá khứ: {$hocKyQuaKhu}\n";
