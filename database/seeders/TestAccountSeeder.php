<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo tài khoản test cho email conjvayba@gmail.com
        $testUserId = DB::table('users')->insertGetId([
            'name' => 'Test Account',
            'email' => 'conjvayba@gmail.com',
            'password' => Hash::make('123456'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gán vai trò sinh viên cho tài khoản test
        $sinhVienRole = DB::table('vai_tro')->where('ma_vai_tro', 'sinh_vien')->first();

        DB::table('tai_khoan_vai_tro')->insert([
            'tai_khoan_id' => $testUserId,
            'vai_tro_id' => $sinhVienRole->id,
            'ngay_gan' => now(),
            'nguoi_gan_id' => 1, // Admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('========================================');
        $this->command->info('TÀI KHOẢN TEST ĐÃ TẠO:');
        $this->command->info('========================================');
        $this->command->info('Email: conjvayba@gmail.com');
        $this->command->info('Password: 123456');
        $this->command->info('Vai trò: Sinh viên');
        $this->command->info('========================================');
        $this->command->info('Bạn có thể test chức năng "Quên mật khẩu"');
        $this->command->info('Email reset password sẽ được gửi đến: conjvayba@gmail.com');
        $this->command->info('========================================');
    }
}
