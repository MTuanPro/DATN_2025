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
        // Sử dụng QuanLySinhVienSeeder để seed toàn bộ dữ liệu
        // Seeder này sẽ tự động gọi tất cả các seeder cần thiết theo đúng thứ tự
        $this->call(QuanLySinhVienSeeder::class);
    }
}
