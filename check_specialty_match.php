<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PHÂN TÍCH TỶ LỆ PHÂN CÔNG ĐÚNG CHUYÊN MÔN ===\n\n";

// Định nghĩa chuyên môn
$chuyenMon = [
    'GV001' => ['keywords' => ['lập trình', 'web', 'database', 'cơ sở dữ liệu', 'phần mềm', 'software', 'oop', 'hướng đối tượng', 'game', 'di động'], 'name' => 'Lập trình Web, CSDL'],
    'GV002' => ['keywords' => ['trí tuệ', 'ai', 'machine learning', 'học máy', 'deep learning', 'xử lý ảnh', 'image', 'thị giác', 'blockchain'], 'name' => 'AI, Machine Learning'],
    'GV003' => ['keywords' => ['mạng', 'network', 'an toàn', 'bảo mật', 'security', 'hệ thống', 'hệ điều hành', 'cloud'], 'name' => 'Mạng, An toàn TT'],
    'GV004' => ['keywords' => ['quản trị', 'kinh doanh', 'marketing', 'quản lý', 'business', 'chiến lược', 'kinh tế', 'thương mại', 'commerce', 'kế toán', 'tài chính'], 'name' => 'Quản trị KD, Marketing'],
    'GV005' => ['keywords' => ['tiếng anh', 'english'], 'name' => 'Tiếng Anh'],
    'GV006' => ['keywords' => ['phân tích', 'thống kê', 'data', 'business intelligence', 'cấu trúc dữ liệu', 'giải thuật'], 'name' => 'Phân tích dữ liệu, BI'],
    'GV007' => ['keywords' => ['triết học', 'tư tưởng', 'đường lối', 'chủ nghĩa', 'lịch sử đảng', 'mác', 'lênin', 'hồ chí minh', 'chính trị'], 'name' => 'Triết học, Tư tưởng'],
    'GV008' => ['keywords' => ['giáo dục thể chất', 'thể dục', 'thể thao', 'gdtc'], 'name' => 'Giáo dục thể chất'],
    'GV009' => ['keywords' => ['giáo dục quốc phòng', 'quốc phòng', 'an ninh', 'gdqp'], 'name' => 'Giáo dục quốc phòng'],
];

$giangViens = DB::table('giang_vien')->get()->keyBy('ma_giang_vien');

foreach ($chuyenMon as $maGV => $config) {
    $gv = $giangViens->get($maGV);
    if (!$gv) continue;
    
    // Lấy danh sách lớp và môn học
    $lopHocPhans = DB::table('lop_hoc_phan_giang_vien as lhpgv')
        ->join('lop_hoc_phan as lhp', 'lhpgv.lop_hoc_phan_id', '=', 'lhp.id')
        ->join('mon_hoc as mh', 'lhp.mon_hoc_id', '=', 'mh.id')
        ->where('lhpgv.giang_vien_id', $gv->id)
        ->where('lhpgv.vai_tro', 'giang_vien_chinh')
        ->select('mh.ten_mon', 'mh.ma_mon', 'lhp.ma_lop_hp')
        ->get();
    
    $tongLop = $lopHocPhans->count();
    $lopDungChuyenMon = 0;
    $lopKhongDungChuyenMon = [];
    
    foreach ($lopHocPhans as $lop) {
        $tenMon = strtolower($lop->ten_mon);
        $isDungChuyenMon = false;
        
        foreach ($config['keywords'] as $keyword) {
            if (str_contains($tenMon, $keyword)) {
                $isDungChuyenMon = true;
                break;
            }
        }
        
        if ($isDungChuyenMon) {
            $lopDungChuyenMon++;
        } else {
            $lopKhongDungChuyenMon[] = $lop->ten_mon;
        }
    }
    
    $tyLe = $tongLop > 0 ? round(($lopDungChuyenMon / $tongLop) * 100, 1) : 0;
    
    echo "----------------------------------------\n";
    echo "$maGV - {$gv->ho_ten}\n";
    echo "Chuyên môn: {$config['name']}\n";
    echo "Tổng lớp: $tongLop\n";
    echo "Lớp đúng chuyên môn: $lopDungChuyenMon ({$tyLe}%)\n";
    
    if (!empty($lopKhongDungChuyenMon)) {
        echo "Lớp KHÔNG đúng chuyên môn (" . count($lopKhongDungChuyenMon) . "):\n";
        $counted = array_count_values($lopKhongDungChuyenMon);
        foreach ($counted as $mon => $soLuong) {
            echo "  - $mon: $soLuong lớp\n";
        }
    } else {
        echo "✅ TẤT CẢ LỚP ĐÚNG CHUYÊN MÔN (100%)\n";
    }
    echo "\n";
}

// Tổng kết
echo "\n=== TỔNG KẾT ===\n";
$tongTatCa = DB::table('lop_hoc_phan_giang_vien')
    ->where('vai_tro', 'giang_vien_chinh')
    ->count();
echo "Tổng số phân công: $tongTatCa\n";
