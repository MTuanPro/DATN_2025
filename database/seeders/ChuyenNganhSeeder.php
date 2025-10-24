<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ChuyenNganhSeeder extends Seeder
{
    public function run(): void
    {
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

