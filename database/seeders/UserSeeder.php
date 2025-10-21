<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo tài khoản Admin
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin System',
            'email' => 'admin@smis.edu.vn',
            'password' => Hash::make('123456'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo tài khoản Trưởng phòng Đào tạo
        $truongPhongId = DB::table('users')->insertGetId([
            'name' => 'Nguyễn Văn Trưởng',
            'email' => 'truongphong@smis.edu.vn',
            'password' => Hash::make('123456'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo tài khoản Nhân viên Đào tạo
        $nhanVienId = DB::table('users')->insertGetId([
            'name' => 'Trần Thị Nhân Viên',
            'email' => 'nhanvien@smis.edu.vn',
            'password' => Hash::make('123456'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo tài khoản Giảng viên
        $giangVienId = DB::table('users')->insertGetId([
            'name' => 'Lê Văn Giảng',
            'email' => 'giangvien@smis.edu.vn',
            'password' => Hash::make('123456'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo tài khoản Sinh viên
        $sinhVienId = DB::table('users')->insertGetId([
            'name' => 'Phạm Thị Sinh Viên',
            'email' => 'sinhvien@smis.edu.vn',
            'password' => Hash::make('123456'),
            'trang_thai' => 'hoat_dong',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Lấy ID của các vai trò
        $vaiTros = DB::table('vai_tro')->get()->keyBy('ma_vai_tro');

        // Gán vai trò cho các tài khoản
        $taiKhoanVaiTros = [
            [
                'tai_khoan_id' => $adminId,
                'vai_tro_id' => $vaiTros['admin']->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tai_khoan_id' => $truongPhongId,
                'vai_tro_id' => $vaiTros['truong_phong_dt']->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tai_khoan_id' => $nhanVienId,
                'vai_tro_id' => $vaiTros['nhan_vien_dt']->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tai_khoan_id' => $giangVienId,
                'vai_tro_id' => $vaiTros['giang_vien']->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tai_khoan_id' => $sinhVienId,
                'vai_tro_id' => $vaiTros['sinh_vien']->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tai_khoan_vai_tro')->insert($taiKhoanVaiTros);

        // Output thông tin đăng nhập
        $this->command->info('========================================');
        $this->command->info('THÔNG TIN TÀI KHOẢN ĐÃ TẠO:');
        $this->command->info('========================================');
        $this->command->info('1. Admin:');
        $this->command->info('   Email: admin@smis.edu.vn');
        $this->command->info('   Password: 123456');
        $this->command->info('');
        $this->command->info('2. Trưởng phòng Đào tạo:');
        $this->command->info('   Email: truongphong@smis.edu.vn');
        $this->command->info('   Password: 123456');
        $this->command->info('');
        $this->command->info('3. Nhân viên Đào tạo:');
        $this->command->info('   Email: nhanvien@smis.edu.vn');
        $this->command->info('   Password: 123456');
        $this->command->info('');
        $this->command->info('4. Giảng viên:');
        $this->command->info('   Email: giangvien@smis.edu.vn');
        $this->command->info('   Password: 123456');
        $this->command->info('');
        $this->command->info('5. Sinh viên:');
        $this->command->info('   Email: sinhvien@smis.edu.vn');
        $this->command->info('   Password: 123456');
        $this->command->info('========================================');
    }
}
