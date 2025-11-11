<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\GiangVien;
use App\Models\LopHocPhan;
use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA PHÂN CÔNG GIẢNG VIÊN THEO CHUYÊN MÔN ===\n\n";

$giangViens = GiangVien::all();

foreach ($giangViens as $gv) {
    echo "----------------------------------------\n";
    echo "{$gv->ma_giang_vien} - {$gv->ho_ten}\n";
    echo "Chuyên môn: {$gv->chuyen_mon}\n";
    echo "Các môn đang dạy (vai trò chính):\n";
    
    $lops = LopHocPhan::select('mon_hoc.ten_mon', DB::raw('COUNT(*) as so_lop'))
        ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
        ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
        ->where('lop_hoc_phan_giang_vien.giang_vien_id', $gv->id)
        ->where('lop_hoc_phan_giang_vien.vai_tro', 'giang_vien_chinh')
        ->groupBy('mon_hoc.id', 'mon_hoc.ten_mon')
        ->orderBy('so_lop', 'desc')
        ->get();
    
    foreach ($lops as $lop) {
        echo "  - {$lop->ten_mon}: {$lop->so_lop} lớp\n";
    }
    
    echo "\n";
}
