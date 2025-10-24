<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class NganhSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $khoaIds = DB::table('khoa')->pluck('id');

        foreach ($khoaIds as $khoa_id) {
            for ($i = 0; $i < 3; $i++) {
                DB::table('nganh')->insert([
                    'ma_nganh' => $faker->unique()->numerify('74####'),
                    'ten_nganh' => ucfirst($faker->words(3, true)),
                    'khoa_id' => $khoa_id,
                    'mo_ta' => $faker->sentence(8),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
