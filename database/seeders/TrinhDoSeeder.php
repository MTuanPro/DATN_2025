<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            ['ten_trinh_do' => 'Sau đại học'],
        ];

        foreach ($data as $item) {
            DB::table('dm_trinh_do')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

