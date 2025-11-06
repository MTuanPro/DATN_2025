<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HocKySeeder extends Seeder
{
    public function run(): void
    {
        $hocKys = [];

        // Chỉ tạo học kỳ cho năm học hiện tại: 2025-2026
        $namHoc = '2025-2026';

        // Học kỳ 1 (Tháng 9/2025 - Tháng 1/2026) - ĐANG MỞ ĐĂNG KÝ
        $hocKys[] = [
            'ten_hoc_ky' => 'Học kỳ 1',
            'nam_hoc' => $namHoc,
            'ngay_bat_dau' => Carbon::create(2025, 9, 1),
            'ngay_ket_thuc' => Carbon::create(2026, 1, 15),
            'ngay_bat_dau_dang_ky' => Carbon::create(2025, 8, 15), // Đã qua
            'ngay_ket_thuc_dang_ky' => Carbon::create(2025, 11, 30), // Còn thời gian
            'la_hoc_ky_hien_tai' => true,
            'mo_ta' => 'Học kỳ 1 năm học 2025-2026',
        ];

        // Học kỳ 2 (Tháng 2/2026 - Tháng 6/2026) - CHƯA MỞ
        $hocKys[] = [
            'ten_hoc_ky' => 'Học kỳ 2',
            'nam_hoc' => $namHoc,
            'ngay_bat_dau' => Carbon::create(2026, 2, 1),
            'ngay_ket_thuc' => Carbon::create(2026, 6, 15),
            'ngay_bat_dau_dang_ky' => Carbon::create(2026, 1, 20),
            'ngay_ket_thuc_dang_ky' => Carbon::create(2026, 2, 10),
            'la_hoc_ky_hien_tai' => false,
            'mo_ta' => 'Học kỳ 2 năm học 2025-2026',
        ];

        // Insert dữ liệu
        foreach ($hocKys as $hk) {
            DB::table('hoc_ky')->updateOrInsert(
                [
                    'ten_hoc_ky' => $hk['ten_hoc_ky'],
                    'nam_hoc' => $hk['nam_hoc'],
                ],
                array_merge($hk, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        echo "✅ Đã tạo " . count($hocKys) . " học kỳ\n";
        echo "   📌 Học kỳ 1 năm 2025-2026 đang mở đăng ký\n";
    }
}
