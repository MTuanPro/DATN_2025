<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonHocTienQuyetSeeder extends Seeder
{
    public function run(): void
    {
        $monHocIds = DB::table('mon_hoc')->pluck('id');

        foreach ($monHocIds as $id) {
            if (rand(0, 1)) {
                $tienQuyetId = $monHocIds->random();
                if ($tienQuyetId != $id) {
                    DB::table('mon_hoc_tien_quyet')->insert([
                        'mon_hoc_id' => $id,
                        'mon_tien_quyet_id' => $tienQuyetId,
                        'loai_tien_quyet' => 'bat_buoc',
                        'dieu_kien_qua_mon' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
