<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            VaiTroSeeder::class,
            UserSeeder::class,
            KhoaSeeder::class,
        NganhSeeder::class,
        ChuyenNganhSeeder::class,
        TrinhDoSeeder::class,
        MonHocSeeder::class,
        MonHocTienQuyetSeeder::class,
        ChuongTrinhKhungSeeder::class,
        TrangThaiHocTapSeeder::class,
        PhongHocSeeder::class,
        HocKySeeder::class,
        KhoaHocSeeder::class,

        ]);
    }
}
