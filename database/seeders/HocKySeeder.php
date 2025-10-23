<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HocKySeeder extends Seeder
{
    public function run(): void
    {
        $hocKys = [
            ['ten_hoc_ky' => 'HK1', 'nam_hoc' => '2024-2025'],
            ['ten_hoc_ky' => 'HK2', 'nam_hoc' => '2024-2025'],
            ['ten_hoc_ky' => 'HK3', 'nam_hoc' => '2024-2025'],
        ];

        foreach ($hocKys as $hk) {
            DB::table('hoc_ky')->insert(array_merge($hk, [
                'ngay_bat_dau' => now()->subMonths(2),
                'ngay_ket_thuc' => now()->addMonths(3),
                'la_hoc_ky_hien_tai' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
