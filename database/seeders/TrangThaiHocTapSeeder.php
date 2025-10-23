<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiHocTapSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_trang_thai' => 'Đang học'],
            ['ten_trang_thai' => 'Bảo lưu'],
            ['ten_trang_thai' => 'Thôi học'],
            ['ten_trang_thai' => 'Tốt nghiệp'],
        ];

        foreach ($data as $item) {
            DB::table('trang_thai_hoc_tap')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
