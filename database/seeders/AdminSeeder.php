<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra tài khoản admin đã tồn tại chưa
        $existingAdmin = DB::table('users')->where('email', 'admin@smis.edu.vn')->first();

        if ($existingAdmin) {
            $this->command->warn('Tài khoản admin@smis.edu.vn đã tồn tại!');

            // Cập nhật password nếu cần
            DB::table('users')->where('id', $existingAdmin->id)->update([
                'password' => Hash::make('password'),
                'updated_at' => now(),
            ]);

            $adminId = $existingAdmin->id;
        } else {
            // Tạo tài khoản Admin mới
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Admin System',
                'email' => 'admin@smis.edu.vn',
                'password' => Hash::make('password'),
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Lấy ID của vai trò admin
        $adminRole = DB::table('vai_tro')->where('ma_vai_tro', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Vai trò admin không tồn tại! Vui lòng chạy VaiTroSeeder trước.');
            return;
        }

        // Kiểm tra đã gán vai trò chưa
        $hasRole = DB::table('tai_khoan_vai_tro')
            ->where('tai_khoan_id', $adminId)
            ->where('vai_tro_id', $adminRole->id)
            ->exists();

        if (!$hasRole) {
            // Gán vai trò admin
            DB::table('tai_khoan_vai_tro')->insert([
                'tai_khoan_id' => $adminId,
                'vai_tro_id' => $adminRole->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Kiểm tra record trong bảng admin
        $existingAdminRecord = DB::table('admin')->where('user_id', $adminId)->first();

        if (!$existingAdminRecord) {
            // Tự động sinh mã admin dạng AD001, AD002, ...
            $nextCode = 'AD' . str_pad(DB::table('admin')->count() + 1, 3, '0', STR_PAD_LEFT);

            // Tạo record trong bảng admin
            DB::table('admin')->insert([
                'user_id' => $adminId,
                'ma_admin' => $nextCode,
                'ho_ten' => 'Admin System',
                'email' => 'admin@smis.edu.vn',
                'so_dien_thoai' => '0123456789',
                'anh_dai_dien' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('========================================');
        $this->command->info('TÀI KHOẢN ADMIN ĐÃ TẠO/CẬP NHẬT:');
        $this->command->info('========================================');
        $this->command->info('Email: admin@smis.edu.vn');
        $this->command->info('Password: password');
        $this->command->info('========================================');
    }
}
