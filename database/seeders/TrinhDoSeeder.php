<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrinhDoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_trinh_do' => 'Cao đẳng', 'created_at' => now(), 'updated_at' => now()],
            ['ten_trinh_do' => 'Đại học', 'created_at' => now(), 'updated_at' => now()],
            ['ten_trinh_do' => 'Thạc sĩ', 'created_at' => now(), 'updated_at' => now()],
            ['ten_trinh_do' => 'Tiến sĩ', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('dm_trinh_do')->insert($data);
    }
}
