<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaHocSeeder extends Seeder
{
    public function run(): void
    {
        $khoaHocs = [];

        // Tạo khóa học từ 2019 đến 2025
        for ($i = 2019; $i <= 2025; $i++) {
            $khoaHocs[] = [
                'ten_khoa_hoc' => 'K' . ($i - 2000),
                'nam_bat_dau' => $i,
                'nam_ket_thuc' => $i + 4,
                'so_nam_dao_tao' => 4,
                'mo_ta' => 'Khóa học ' . $i . ' - ' . ($i + 4) . ' (Sinh viên nhập học năm ' . $i . ')',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($khoaHocs as $khoaHoc) {
            DB::table('khoa_hoc')->updateOrInsert(
                ['ten_khoa_hoc' => $khoaHoc['ten_khoa_hoc']],
                $khoaHoc
            );
        }

        echo "✅ Đã tạo " . count($khoaHocs) . " khóa học\n";
    }
}
