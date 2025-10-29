<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DaoTaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy ID vai trò
        $truongPhongRole = DB::table('vai_tro')->where('ma_vai_tro', 'truong_phong_dt')->first();
        $nhanVienRole = DB::table('vai_tro')->where('ma_vai_tro', 'nhan_vien_dt')->first();
        $adminUser = DB::table('users')->where('email', 'admin@smis.edu.vn')->first();

        // Tạo tài khoản Trưởng phòng Đào tạo
        $truongPhongId = DB::table('users')->insertGetId([
            'name' => 'Nguyễn Văn Trưởng',
            'email' => 'truongphong@smis.edu.vn',
            'password' => Hash::make('password'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gán vai trò Trưởng phòng
        DB::table('tai_khoan_vai_tro')->insert([
            'tai_khoan_id' => $truongPhongId,
            'vai_tro_id' => $truongPhongRole->id,
            'ngay_gan' => now(),
            'nguoi_gan_id' => $adminUser->id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo record trong bảng dao_tao
        DB::table('dao_tao')->insert([
            'user_id' => $truongPhongId,
            'ho_ten' => 'Nguyễn Văn Trưởng',
            'email' => 'truongphong@smis.edu.vn',
            'so_dien_thoai' => '0987654321',
            'anh_dai_dien' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo tài khoản Nhân viên Đào tạo
        $nhanVienId = DB::table('users')->insertGetId([
            'name' => 'Trần Thị Nhân Viên',
            'email' => 'nhanvien@smis.edu.vn',
            'password' => Hash::make('password'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gán vai trò Nhân viên
        DB::table('tai_khoan_vai_tro')->insert([
            'tai_khoan_id' => $nhanVienId,
            'vai_tro_id' => $nhanVienRole->id,
            'ngay_gan' => now(),
            'nguoi_gan_id' => $adminUser->id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo record trong bảng dao_tao
        DB::table('dao_tao')->insert([
            'user_id' => $nhanVienId,
            'ho_ten' => 'Trần Thị Nhân Viên',
            'email' => 'nhanvien@smis.edu.vn',
            'so_dien_thoai' => '0912345678',
            'anh_dai_dien' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('========================================');
        $this->command->info('TÀI KHOẢN ĐÀO TẠO ĐÃ TẠO:');
        $this->command->info('========================================');
        $this->command->info('1. Trưởng phòng Đào tạo:');
        $this->command->info('   Email: truongphong@smis.edu.vn');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('2. Nhân viên Đào tạo:');
        $this->command->info('   Email: nhanvien@smis.edu.vn');
        $this->command->info('   Password: password');
        $this->command->info('========================================');
    }
}
