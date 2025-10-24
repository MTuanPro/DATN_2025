<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminDaoTaoSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy user admin
        $adminUser = DB::table('users')->where('email', 'admin@smis.edu.vn')->first();
        $truongPhongUser = DB::table('users')->where('email', 'truongphong@smis.edu.vn')->first();
        $nhanVienUser = DB::table('users')->where('email', 'nhanvien@smis.edu.vn')->first();

        // Tạo Admin
        if ($adminUser) {
            DB::table('admin')->updateOrInsert(
                ['email' => 'admin@smis.edu.vn'],
                [
                    'ho_ten' => 'Nguyễn Văn Admin',
                    'email' => 'admin@smis.edu.vn',
                    'so_dien_thoai' => '0123456789',
                    'anh_dai_dien' => null,
                    'user_id' => $adminUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Tạo Đào tạo (Trưởng phòng)
        if ($truongPhongUser) {
            DB::table('dao_tao')->updateOrInsert(
                ['email' => 'truongphong@smis.edu.vn'],
                [
                    'ho_ten' => 'Trần Thị Hương',
                    'email' => 'truongphong@smis.edu.vn',
                    'so_dien_thoai' => '0987654321',
                    'anh_dai_dien' => null,
                    'user_id' => $truongPhongUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Tạo Đào tạo (Nhân viên)
        if ($nhanVienUser) {
            DB::table('dao_tao')->updateOrInsert(
                ['email' => 'nhanvien@smis.edu.vn'],
                [
                    'ho_ten' => 'Lê Văn Minh',
                    'email' => 'nhanvien@smis.edu.vn',
                    'so_dien_thoai' => '0912345678',
                    'anh_dai_dien' => null,
                    'user_id' => $nhanVienUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
