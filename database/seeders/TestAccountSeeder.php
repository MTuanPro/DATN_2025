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
        // Kiểm tra xem tài khoản đã tồn tại chưa
        $existingUser = DB::table('users')->where('email', 'conjvayba@gmail.com')->first();

        if ($existingUser) {
            $this->command->warn('Tài khoản conjvayba@gmail.com đã tồn tại!');
            $testUserId = $existingUser->id;

            // Update password
            DB::table('users')->where('id', $testUserId)->update([
                'password' => Hash::make('password'),
                'updated_at' => now(),
            ]);
        } else {
            // Tạo tài khoản test mới
            $testUserId = DB::table('users')->insertGetId([
                'name' => 'Test Account',
                'email' => 'conjvayba@gmail.com',
                'password' => Hash::make('password'),
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Gán vai trò sinh viên cho tài khoản test (nếu chưa có)
        $sinhVienRole = DB::table('vai_tro')->where('ma_vai_tro', 'sinh_vien')->first();

        $hasRole = DB::table('tai_khoan_vai_tro')
            ->where('tai_khoan_id', $testUserId)
            ->where('vai_tro_id', $sinhVienRole->id)
            ->exists();

        if (!$hasRole) {
            DB::table('tai_khoan_vai_tro')->insert([
                'tai_khoan_id' => $testUserId,
                'vai_tro_id' => $sinhVienRole->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => 1, // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Kiểm tra xem đã có record sinh_vien chưa
        $existingSinhVien = DB::table('sinh_vien')->where('user_id', $testUserId)->first();

        if (!$existingSinhVien) {
            // Tạo record sinh_vien cho tài khoản test
            $lopHanhChinh = DB::table('lop_hanh_chinh')->first();
            $khoaHoc = DB::table('khoa_hoc')->first();
            $nganh = DB::table('nganh')->first();
            $trangThaiHocTap = DB::table('trang_thai_hoc_tap')->where('ten_trang_thai', 'Đang học')->first();

            if ($lopHanhChinh && $khoaHoc && $nganh && $trangThaiHocTap) {
                DB::table('sinh_vien')->insert([
                    'user_id' => $testUserId,
                    'ma_sinh_vien' => 'TEST001',
                    'ho_ten' => 'Nguyễn Test Account',
                    'email' => 'conjvayba@gmail.com',
                    'ngay_sinh' => '2000-01-01',
                    'gioi_tinh' => 'nam',
                    'so_dien_thoai' => '0123456789',
                    'so_nha_duong' => '123 Test Street',
                    'phuong_xa' => 'Phường Test',
                    'quan_huyen' => 'Quận Test',
                    'tinh_thanh' => 'TP. Hồ Chí Minh',
                    'can_cuoc_cong_dan' => '001200000001',
                    'ngay_cap_cccd' => now()->subYears(2),
                    'noi_cap_cccd' => 'Công an TP. Hồ Chí Minh',
                    'khoa_hoc_id' => $khoaHoc->id,
                    'lop_hanh_chinh_id' => $lopHanhChinh->id,
                    'nganh_id' => $nganh->id,
                    'ky_hien_tai' => 1,
                    'trang_thai_hoc_tap_id' => $trangThaiHocTap->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('========================================');
        $this->command->info('TÀI KHOẢN TEST ĐÃ TẠO/CẬP NHẬT:');
        $this->command->info('========================================');
        $this->command->info('Email: conjvayba@gmail.com');
        $this->command->info('Password: password');
        $this->command->info('Vai trò: Sinh viên');
        $this->command->info('Mã SV: TEST001');
        $this->command->info('========================================');
        $this->command->info('Bạn có thể test chức năng "Quên mật khẩu"');
        $this->command->info('Email reset password sẽ được gửi đến: conjvayba@gmail.com');
        $this->command->info('========================================');
    }
}
