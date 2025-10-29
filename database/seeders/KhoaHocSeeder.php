<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaHocSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 2020; $i <= 2025; $i++) {
            DB::table('khoa_hoc')->insert([
                'ten_khoa_hoc' => 'K' . ($i - 2000),
                'nam_bat_dau' => $i,
                'nam_ket_thuc' => $i + 4,
                'so_nam_dao_tao' => 4,
                'mo_ta' => 'Khóa học ' . $i . '-' . ($i + 4),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
