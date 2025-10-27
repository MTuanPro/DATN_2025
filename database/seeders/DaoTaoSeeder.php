<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DaoTaoSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID vai trò
        $truongPhongRole = DB::table('vai_tro')->where('ma_vai_tro', 'truong_phong_dt')->first();
        $nhanVienRole = DB::table('vai_tro')->where('ma_vai_tro', 'nhan_vien_dt')->first();
        $adminUser = DB::table('users')->where('email', 'admin@smis.edu.vn')->first();

        // ========================================
        // 1️⃣ Tạo tài khoản Trưởng phòng Đào tạo
        // ========================================
        $existingTruongPhong = DB::table('users')->where('email', 'truongphong@smis.edu.vn')->first();

        if ($existingTruongPhong) {
            $this->command->warn('Tài khoản truongphong@smis.edu.vn đã tồn tại!');
            DB::table('users')->where('id', $existingTruongPhong->id)->update([
                'password' => Hash::make('password'),
                'updated_at' => now(),
            ]);
            $truongPhongId = $existingTruongPhong->id;
        } else {
            $truongPhongId = DB::table('users')->insertGetId([
                'name' => 'Nguyễn Văn Trưởng',
                'email' => 'truongphong@smis.edu.vn',
                'password' => Hash::make('password'),
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Gán vai trò Trưởng phòng
        $hasTruongPhongRole = DB::table('tai_khoan_vai_tro')
            ->where('tai_khoan_id', $truongPhongId)
            ->where('vai_tro_id', $truongPhongRole->id)
            ->exists();

        if (!$hasTruongPhongRole) {
            DB::table('tai_khoan_vai_tro')->insert([
                'tai_khoan_id' => $truongPhongId,
                'vai_tro_id' => $truongPhongRole->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => $adminUser->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Record Trưởng phòng Đào tạo
        $existingTruongPhongDaoTao = DB::table('dao_tao')->where('user_id', $truongPhongId)->first();

        if (!$existingTruongPhongDaoTao) {
            $nextCode = 'DT' . str_pad(DB::table('dao_tao')->count() + 1, 3, '0', STR_PAD_LEFT);

            DB::table('dao_tao')->insert([
                'ma_dao_tao' => $nextCode,
                'user_id' => $truongPhongId,
                'ho_ten' => 'Nguyễn Văn Trưởng',
                'email' => 'truongphong@smis.edu.vn',
                'so_dien_thoai' => '0987654321',
                'anh_dai_dien' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ========================================
        // 2️⃣ Tạo tài khoản Nhân viên Đào tạo
        // ========================================
        $existingNhanVien = DB::table('users')->where('email', 'nhanvien@smis.edu.vn')->first();

        if ($existingNhanVien) {
            $this->command->warn('Tài khoản nhanvien@smis.edu.vn đã tồn tại!');
            DB::table('users')->where('id', $existingNhanVien->id)->update([
                'password' => Hash::make('password'),
                'updated_at' => now(),
            ]);
            $nhanVienId = $existingNhanVien->id;
        } else {
            $nhanVienId = DB::table('users')->insertGetId([
                'name' => 'Trần Thị Nhân Viên',
                'email' => 'nhanvien@smis.edu.vn',
                'password' => Hash::make('password'),
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Gán vai trò Nhân viên
        $hasNhanVienRole = DB::table('tai_khoan_vai_tro')
            ->where('tai_khoan_id', $nhanVienId)
            ->where('vai_tro_id', $nhanVienRole->id)
            ->exists();

        if (!$hasNhanVienRole) {
            DB::table('tai_khoan_vai_tro')->insert([
                'tai_khoan_id' => $nhanVienId,
                'vai_tro_id' => $nhanVienRole->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => $adminUser->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Record Nhân viên Đào tạo
        $existingNhanVienDaoTao = DB::table('dao_tao')->where('user_id', $nhanVienId)->first();

        if (!$existingNhanVienDaoTao) {
            $nextCode = 'DT' . str_pad(DB::table('dao_tao')->count() + 1, 3, '0', STR_PAD_LEFT);

            DB::table('dao_tao')->insert([
                'ma_dao_tao' => $nextCode,
                'user_id' => $nhanVienId,
                'ho_ten' => 'Trần Thị Nhân Viên',
                'email' => 'nhanvien@smis.edu.vn',
                'so_dien_thoai' => '0912345678',
                'anh_dai_dien' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('========================================');
        $this->command->info('TÀI KHOẢN ĐÀO TẠO ĐÃ TẠO/CẬP NHẬT:');
        $this->command->info('========================================');
        $this->command->info('1. Trưởng phòng Đào tạo: truongphong@smis.edu.vn / password');
        $this->command->info('2. Nhân viên Đào tạo: nhanvien@smis.edu.vn / password');
        $this->command->info('========================================');
    }
}
