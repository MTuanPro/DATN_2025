<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PhongHocSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $loai = ['Lý thuyết', 'Phòng máy', 'Hội trường'];
        $trangThai = ['Hoạt động', 'Bảo trì', 'Không sử dụng'];

        for ($i = 0; $i < 20; $i++) {
            DB::table('phong_hoc')->insert([
                'ma_phong' => strtoupper($faker->unique()->bothify('P###')),
                'ten_phong' => 'Phòng ' . strtoupper($faker->bothify('???')),
                'suc_chua' => rand(30, 100),
                'vi_tri' => 'Tầng ' . rand(1, 5) . ' - Tòa ' . $faker->randomElement(['A', 'B', 'C']),
                'loai_phong' => $faker->randomElement($loai),
                'trang_thai' => $faker->randomElement($trangThai),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

