<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrinhDoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_trinh_do' => 'Cao đẳng'],
            ['ten_trinh_do' => 'Đại học'],
            ['ten_trinh_do' => 'Thạc sĩ'], // 👈 Thêm dòng này
            ['ten_trinh_do' => 'Tiến sĩ'], // 👈 Và dòng này nếu cần
        ];

        foreach ($data as $item) {
            DB::table('dm_trinh_do')->updateOrInsert(
                ['ten_trinh_do' => $item['ten_trinh_do']],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
