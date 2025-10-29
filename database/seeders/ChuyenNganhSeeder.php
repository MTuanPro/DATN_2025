<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChuyenNganhSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'ma_chuyen_nganh' => 'CNOS',
                'ten_chuyen_nganh' => 'Công nghệ phần mềm',
                'nganh_id' => 4,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Ngành về phát triển phần mềm.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ... các chuyên ngành khác
        ];

        foreach ($data as $item) {
            $exists = DB::table('chuyen_nganh')
                ->where('ma_chuyen_nganh', $item['ma_chuyen_nganh'])
                ->exists();

            if (!$exists) {
                DB::table('chuyen_nganh')->insert($item);
            } else {
                $this->command->warn("⚠️  Mã chuyên ngành {$item['ma_chuyen_nganh']} đã tồn tại, bỏ qua!");
            }
        }
    }
}
