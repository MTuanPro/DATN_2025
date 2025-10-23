<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChuongTrinhKhungSeeder extends Seeder
{
    public function run(): void
    {
        $monHocs = DB::table('mon_hoc')->pluck('id');
        $chuyenNganhs = DB::table('chuyen_nganh')->pluck('id');

        foreach ($chuyenNganhs as $cn_id) {
            foreach ($monHocs->random(10) as $mon_id) {
                DB::table('chuong_trinh_khung')->insert([
                    'chuyen_nganh_id' => $cn_id,
                    'mon_hoc_id' => $mon_id,
                    'hoc_ky_goi_y' => rand(1, 8),
                    'thu_tu_hoc' => rand(1, 100),
                    'bat_buoc' => rand(0, 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
