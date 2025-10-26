<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;
use Carbon\Carbon;

class LopHocPhanSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        echo "🔄 Đang tạo Lớp học phần...\n";

        $hocKys = HocKy::limit(3)->get();
        $monHocs = MonHoc::limit(10)->get();

        if ($hocKys->isEmpty() || $monHocs->isEmpty()) {
            echo "⚠️  Cần có dữ liệu HocKy và MonHoc trước!\n";
            return;
        }

        $lopHocPhans = [];

        foreach ($hocKys as $hocKy) {
            foreach ($monHocs as $monHoc) {
                // Tạo 1-2 lớp cho mỗi môn học
                $soLop = rand(1, 2);

                for ($nhom = 1; $nhom <= $soLop; $nhom++) {
                    // Mã lớp HP = MaMonHoc.HocKyId.Nhom (VD: DC001.1.01)
                    $maLopHp = $monHoc->ma_mon . '.' . $hocKy->id . '.' . str_pad($nhom, 2, '0', STR_PAD_LEFT);

                    // Kiểm tra trung lặp
                    $exists = DB::table('lop_hoc_phan')
                        ->where('ma_lop_hp', $maLopHp)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $sucChua = rand(40, 60);
                    $soLuongDangKy = rand(0, $sucChua);
                    $soLuongToiThieu = rand(10, 20);

                    $hinhThuc = ['offline', 'online', 'hybrid'][rand(0, 2)];
                    $linkOnline = in_array($hinhThuc, ['online', 'hybrid'])
                        ? 'https://meet.google.com/' . substr(md5($maLopHp), 0, 10)
                        : null;

                    // Tạo ngày trong khoảng học kỳ
                    $ngayBatDau = Carbon::parse($hocKy->ngay_bat_dau)->addDays(rand(0, 7));
                    $ngayKetThuc = Carbon::parse($hocKy->ngay_ket_thuc)->subDays(rand(0, 7));

                    $trangThaiLop = ['mo_dang_ky', 'dang_hoc', 'ket_thuc'][rand(0, 2)];

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
                        'ghi_chu' => 'Lớp ' . $maLopHp,
                        'trang_thai_lop' => $trangThaiLop,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($lopHocPhans)) {
            DB::table('lop_hoc_phan')->insert($lopHocPhans);
            echo "✅ Đã tạo " . count($lopHocPhans) . " Lớp học phần\n";
        } else {
            echo "ℹ️  Không có lớp học phần mới để tạo\n";
        }
    }
}
