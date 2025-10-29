<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


use Faker\Factory as Faker;

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

        $faker = Faker::create('vi_VN');
        $nganhIds = DB::table('nganh')->pluck('id');

        foreach ($nganhIds as $nganh_id) {
            for ($i = 0; $i < 2; $i++) {
                DB::table('chuyen_nganh')->insert([
                    'ma_chuyen_nganh' => strtoupper($faker->lexify('CN??')),
                    'ten_chuyen_nganh' => ucfirst($faker->words(3, true)),
                    'nganh_id' => $nganh_id,
                    'tong_tin_chi_toi_thieu' => rand(120, 150),
                    'mo_ta' => $faker->sentence(6),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            }
        }
    }
}
<<<<<<< HEAD
=======

>>>>>>> 3ce5bf463aba81437bc908d45799f550b6b5f94d
