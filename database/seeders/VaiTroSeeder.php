<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaiTros = [
            [
                'ma_vai_tro' => 'admin',
                'ten_vai_tro' => 'Quản trị viên',
                'mo_ta' => 'Quản trị hệ thống, quản lý tài khoản, phân quyền',
                'muc_do_uu_tien' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_vai_tro' => 'truong_phong_dt',
                'ten_vai_tro' => 'Trưởng phòng Đào tạo',
                'mo_ta' => 'Quản lý toàn bộ hoạt động đào tạo, phê duyệt các quyết định quan trọng',
                'muc_do_uu_tien' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_vai_tro' => 'nhan_vien_dt',
                'ten_vai_tro' => 'Nhân viên Đào tạo',
                'mo_ta' => 'Thực hiện các nghiệp vụ đào tạo hàng ngày',
                'muc_do_uu_tien' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_vai_tro' => 'giang_vien',
                'ten_vai_tro' => 'Giảng viên',
                'mo_ta' => 'Giảng dạy, chấm điểm, quản lý lớp học',
                'muc_do_uu_tien' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_vai_tro' => 'sinh_vien',
                'ten_vai_tro' => 'Sinh viên',
                'mo_ta' => 'Học tập, đăng ký học phần, xem kết quả học tập',
                'muc_do_uu_tien' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('vai_tro')->insert($vaiTros);
    }
}
