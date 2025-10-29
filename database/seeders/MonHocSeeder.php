<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MonHocSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $khoaIds = DB::table('khoa')->pluck('id');
        $loaiMon = ['dai_cuong', 'co_so_nganh', 'chuyen_nganh_bat_buoc', 'chuyen_nganh_tu_chon', 'thuc_tap', 'do_an_tot_nghiep'];
        $hinhThuc = ['offline', 'online', 'hybrid'];

        for ($i = 0; $i < 50; $i++) {
            $lt = rand(1, 3);
            $th = rand(0, 2);
            DB::table('mon_hoc')->insert([
                'ma_mon' => strtoupper($faker->unique()->bothify('MH###')),
                'ten_mon' => ucfirst($faker->words(2, true)),
                'so_tin_chi' => $lt + $th,
                'so_tin_chi_ly_thuyet' => $lt,
                'so_tin_chi_thuc_hanh' => $th,
                'mo_ta' => $faker->sentence(5),
                'loai_mon' => $faker->randomElement($loaiMon),
                'khoa_id' => $khoaIds->random(),
                'hinh_thuc_day' => $faker->randomElement($hinhThuc),
                'thoi_luong_hoc' => ($lt + $th) * 15,
                'so_buoi_hoc' => rand(10, 18),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

