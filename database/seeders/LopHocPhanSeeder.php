<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DaoTao\MonHoc;
use App\Models\DaoTao\HocKy;
use Carbon\Carbon;

class LopHocPhanSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        echo "🔄 Đang tạo Lớp học phần...\n";

        $hocKys = HocKy::orderBy('ngay_bat_dau', 'desc')->limit(2)->get(); // 2 học kỳ gần nhất
        $monHocs = MonHoc::all(); // Lấy tất cả môn học

        if ($hocKys->isEmpty() || $monHocs->isEmpty()) {
            echo "⚠️  Cần có dữ liệu HocKy và MonHoc trước!\n";
            return;
        }

        $lopHocPhans = [];
        $count = 0;

        foreach ($hocKys as $hocKy) {
            // Chọn ngẫu nhiên 30-40 môn học để mở lớp trong học kỳ này
            $monHocsMoLop = $monHocs->random(min(40, $monHocs->count()));

            foreach ($monHocsMoLop as $monHoc) {
                // Tạo 2-4 lớp cho mỗi môn học (nhiều nhóm hơn)
                $soLop = rand(2, 4);

                for ($nhom = 1; $nhom <= $soLop; $nhom++) {
                    // Mã lớp HP = MaMonHoc.HocKyId.Nhom (VD: CNTT01.1.01)
                    $maLopHp = $monHoc->ma_mon . '.HK' . $hocKy->id . '.N' . str_pad($nhom, 2, '0', STR_PAD_LEFT);

                    // Kiểm tra trung lặp
                    $exists = DB::table('lop_hoc_phan')
                        ->where('ma_lop_hp', $maLopHp)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $sucChua = rand(45, 60);
                    $soLuongDangKy = rand(30, $sucChua - 5);
                    $soLuongToiThieu = 15;

                    // Xác định hình thức dựa vào loại môn
                    if ($monHoc->loai_mon === 'thuc_tap' || $monHoc->loai_mon === 'do_an_tot_nghiep') {
                        $hinhThuc = 'offline';
                    } else if ($monHoc->so_tin_chi_thuc_hanh > 0) {
                        $hinhThuc = ['offline', 'hybrid'][rand(0, 1)];
                    } else {
                        $hinhThuc = ['offline', 'online', 'hybrid'][rand(0, 2)];
                    }

                    $linkOnline = in_array($hinhThuc, ['online', 'hybrid'])
                        ? 'https://meet.google.com/' . substr(md5($maLopHp . time()), 0, 10)
                        : null;

                    // Tạo ngày trong khoảng học kỳ
                    $ngayBatDau = Carbon::parse($hocKy->ngay_bat_dau)->addDays(rand(0, 7));
                    $ngayKetThuc = Carbon::parse($hocKy->ngay_ket_thuc)->subDays(rand(0, 7));

                    // Xác định trạng thái dựa vào học kỳ
                    $now = Carbon::now();
                    if ($ngayBatDau->isFuture()) {
                        $trangThaiLop = 'mo_dang_ky';
                    } elseif ($ngayKetThuc->isPast()) {
                        $trangThaiLop = 'ket_thuc';
                    } else {
                        $trangThaiLop = 'dang_hoc';
                    }

                    $lopHocPhans[] = [
                        'ma_lop_hp' => $maLopHp,
                        'ten_lop_hp' => $monHoc->ten_mon . ' - Nhóm ' . $nhom,
                        'mon_hoc_id' => $monHoc->id,
                        'hoc_ky_id' => $hocKy->id,
                        'nhom_lop' => $nhom,
                        'suc_chua' => $sucChua,
                        'so_luong_dang_ky' => $soLuongDangKy,
                        'so_luong_toi_thieu' => $soLuongToiThieu,
                        'hinh_thuc' => $hinhThuc,
                        'link_online' => $linkOnline,
                        'ngay_bat_dau' => $ngayBatDau,
                        'ngay_ket_thuc' => $ngayKetThuc,
                        'ghi_chu' => 'Lớp học phần ' . $monHoc->ten_mon . ' - Học kỳ ' . $hocKy->ten_hoc_ky,
                        'trang_thai_lop' => $trangThaiLop,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $count++;

                    // Insert theo batch 50 records
                    if (count($lopHocPhans) >= 50) {
                        DB::table('lop_hoc_phan')->insert($lopHocPhans);
                        $lopHocPhans = [];
                    }
                }
            }
        }

        // Insert phần còn lại
        if (!empty($lopHocPhans)) {
            DB::table('lop_hoc_phan')->insert($lopHocPhans);
        }

        echo "✅ Đã tạo {$count} Lớp học phần\n";
    }
}
