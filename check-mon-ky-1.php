<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA MÔN HỌC KỲ 1 ===\n\n";

// 1. Kiểm tra môn kỳ 1
$monKy1 = DB::table('chuong_trinh_khung')
    ->join('mon_hoc', 'chuong_trinh_khung.mon_hoc_id', '=', 'mon_hoc.id')
    ->where('hoc_ky_goi_y', 1)
    ->where('bat_buoc', true)
    ->select('mon_hoc.id', 'mon_hoc.ma_mon', 'mon_hoc.ten_mon', 'chuong_trinh_khung.hoc_ky_goi_y')
    ->first();

echo "📚 Môn học kỳ 1 đầu tiên: " . ($monKy1 ? $monKy1->ma_mon . ' - ' . $monKy1->ten_mon : 'không có') . "\n\n";

// 2. Kiểm tra sinh viên kỳ 2 và kết quả học tập kỳ 1
$svKy2 = DB::table('sinh_vien')->where('ky_hien_tai', 2)->first();

if ($svKy2) {
    echo "👨‍🎓 Sinh viên kỳ 2: {$svKy2->ma_sinh_vien} - {$svKy2->ho_ten}\n";
    echo "📧 Chuyên ngành ID: {$svKy2->chuyen_nganh_id}\n\n";

    // Kiểm tra kết quả học tập
    $ketQua = DB::table('ket_qua_hoc_tap as kq')
        ->join('lop_hoc_phan_sinh_vien as lhpsv', 'kq.lop_hoc_phan_sinh_vien_id', '=', 'lhpsv.id')
        ->join('lop_hoc_phan as lhp', 'lhpsv.lop_hoc_phan_id', '=', 'lhp.id')
        ->join('mon_hoc as mh', 'lhp.mon_hoc_id', '=', 'mh.id')
        ->join('hoc_ky as hk', 'lhp.hoc_ky_id', '=', 'hk.id')
        ->join('chuong_trinh_khung as ctk', function ($join) use ($svKy2) {
            $join->on('ctk.mon_hoc_id', '=', 'mh.id')
                ->where('ctk.chuyen_nganh_id', $svKy2->chuyen_nganh_id);
        })
        ->where('lhpsv.sinh_vien_id', $svKy2->id)
        ->select(
            'mh.ma_mon',
            'mh.ten_mon',
            'kq.diem_he_10',
            'kq.qua_mon',
            'hk.ten_hoc_ky',
            'hk.nam_hoc',
            'hk.la_hoc_ky_hien_tai',
            'lhpsv.trang_thai',
            'ctk.hoc_ky_goi_y'
        )
        ->orderBy('ctk.hoc_ky_goi_y')
        ->get();

    echo "📝 Số môn đã học: " . $ketQua->count() . "\n";

    if ($ketQua->count() > 0) {
        echo "\n📊 Danh sách môn đã học:\n";
        foreach ($ketQua as $kq) {
            $status = $kq->qua_mon ? '✅ Qua' : '❌ Trượt';
            $trangThai = $kq->trang_thai;
            $hocKy = $kq->la_hoc_ky_hien_tai ? '(HK hiện tại)' : '';
            echo "  - [{$kq->ma_mon}] {$kq->ten_mon}\n";
            echo "    Kỳ gợi ý: {$kq->hoc_ky_goi_y} | Điểm: {$kq->diem_he_10} | {$status} | Trạng thái: {$trangThai}\n";
            echo "    Học kỳ: {$kq->ten_hoc_ky} {$kq->nam_hoc} {$hocKy}\n\n";
        }
    } else {
        echo "⚠️  Sinh viên chưa có kết quả học tập nào!\n";
    }

    // 3. Kiểm tra các môn kỳ 1 mà sinh viên chưa học
    echo "\n📚 Kiểm tra môn kỳ 1 trong chương trình khung:\n";
    $monKy1CuaSV = DB::table('chuong_trinh_khung as ctk')
        ->join('mon_hoc as mh', 'ctk.mon_hoc_id', '=', 'mh.id')
        ->where('ctk.chuyen_nganh_id', $svKy2->chuyen_nganh_id)
        ->where('ctk.hoc_ky_goi_y', 1)
        ->where('ctk.bat_buoc', true)
        ->select('mh.id', 'mh.ma_mon', 'mh.ten_mon', 'ctk.hoc_ky_goi_y')
        ->get();

    echo "Tổng số môn kỳ 1 bắt buộc: " . $monKy1CuaSV->count() . "\n\n";

    foreach ($monKy1CuaSV as $mon) {
        // Kiểm tra xem sinh viên đã có kết quả cho môn này chưa
        $daHoc = $ketQua->where('ma_mon', $mon->ma_mon)->first();

        if ($daHoc) {
            $status = $daHoc->qua_mon ? '✅ Đã qua' : '❌ Trượt (cần học lại)';
            echo "  [{$mon->ma_mon}] {$mon->ten_mon}: {$status}\n";
        } else {
            echo "  [{$mon->ma_mon}] {$mon->ten_mon}: ⚠️ CHƯA HỌC (cần đăng ký)\n";
        }
    }

    // 4. Kiểm tra đăng ký hiện tại trong học kỳ hiện tại
    echo "\n\n📝 Kiểm tra đăng ký môn học trong học kỳ hiện tại:\n";
    $hocKyHienTai = DB::table('hoc_ky')->where('la_hoc_ky_hien_tai', true)->first();

    if ($hocKyHienTai) {
        echo "Học kỳ hiện tại: {$hocKyHienTai->ten_hoc_ky} {$hocKyHienTai->nam_hoc}\n";
        echo "Đang mở đăng ký: " . ($hocKyHienTai->dang_mo_dang_ky ? 'Có' : 'Không') . "\n\n";

        $dangKyHienTai = DB::table('dang_ky_mon_hoc_tam')
            ->join('mon_hoc', 'dang_ky_mon_hoc_tam.mon_hoc_id', '=', 'mon_hoc.id')
            ->where('dang_ky_mon_hoc_tam.sinh_vien_id', $svKy2->id)
            ->where('dang_ky_mon_hoc_tam.hoc_ky_id', $hocKyHienTai->id)
            ->select('mon_hoc.ma_mon', 'mon_hoc.ten_mon', 'dang_ky_mon_hoc_tam.trang_thai')
            ->get();

        echo "Số môn đã đăng ký: " . $dangKyHienTai->count() . "\n";

        if ($dangKyHienTai->count() > 0) {
            foreach ($dangKyHienTai as $dk) {
                echo "  - [{$dk->ma_mon}] {$dk->ten_mon} - Trạng thái: {$dk->trang_thai}\n";
            }
        }
    }
}

echo "\n\n=== KIỂM TRA LỚP HỌC PHẦN KỲ 1 ===\n";
$lopKy1 = DB::table('lop_hoc_phan as lhp')
    ->join('mon_hoc as mh', 'lhp.mon_hoc_id', '=', 'mh.id')
    ->join('hoc_ky as hk', 'lhp.hoc_ky_id', '=', 'hk.id')
    ->join('chuong_trinh_khung as ctk', 'ctk.mon_hoc_id', '=', 'mh.id')
    ->where('hk.la_hoc_ky_hien_tai', true)
    ->where('ctk.hoc_ky_goi_y', 1)
    ->where('ctk.bat_buoc', true)
    ->select('lhp.id', 'lhp.ma_lop_hp', 'mh.ma_mon', 'mh.ten_mon', 'lhp.trang_thai_lop', 'lhp.suc_chua', 'lhp.so_luong_dang_ky')
    ->distinct()
    ->get();

echo "Số lớp học phần môn kỳ 1 trong HK hiện tại: " . $lopKy1->count() . "\n\n";

if ($lopKy1->count() > 0) {
    foreach ($lopKy1 as $lop) {
        echo "  [{$lop->ma_mon}] {$lop->ten_mon}\n";
        echo "    Mã lớp: {$lop->ma_lop_hp}\n";
        echo "    Trạng thái: {$lop->trang_thai_lop}\n";
        echo "    Sĩ số: {$lop->so_luong_dang_ky}/{$lop->suc_chua}\n\n";
    }
} else {
    echo "⚠️ Không có lớp học phần nào cho môn kỳ 1 trong học kỳ hiện tại!\n";
    echo "   Sinh viên không thể đăng ký học lại vì không có lớp!\n";
}
