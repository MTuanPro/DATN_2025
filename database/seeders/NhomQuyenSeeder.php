<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhomQuyenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'ma_nhom' => 'quan_ly_tai_khoan',
                'ten_nhom' => 'Quản lý tài khoản',
                'mo_ta' => 'Nhóm quyền quản lý users, vai trò, phân quyền'
            ],
            [
                'ma_nhom' => 'quan_ly_sinh_vien',
                'ten_nhom' => 'Quản lý sinh viên',
                'mo_ta' => 'Nhóm quyền quản lý thông tin sinh viên'
            ],
            [
                'ma_nhom' => 'quan_ly_giang_vien',
                'ten_nhom' => 'Quản lý giảng viên',
                'mo_ta' => 'Nhóm quyền quản lý thông tin giảng viên'
            ],
            [
                'ma_nhom' => 'quan_ly_danh_muc',
                'ten_nhom' => 'Quản lý danh mục',
                'mo_ta' => 'Nhóm quyền quản lý khoa, ngành, môn học, phòng học'
            ],
            [
                'ma_nhom' => 'quan_ly_lop_hoc_phan',
                'ten_nhom' => 'Quản lý lớp học phần',
                'mo_ta' => 'Nhóm quyền quản lý lớp HP, phân công GV'
            ],
            [
                'ma_nhom' => 'quan_ly_diem',
                'ten_nhom' => 'Quản lý điểm',
                'mo_ta' => 'Nhóm quyền nhập điểm, xem điểm, khóa điểm'
            ],
            [
                'ma_nhom' => 'quan_ly_hoc_phi',
                'ten_nhom' => 'Quản lý học phí',
                'mo_ta' => 'Nhóm quyền cấu hình và quản lý học phí'
            ],
            [
                'ma_nhom' => 'quan_ly_dang_ky_hoc',
                'ten_nhom' => 'Quản lý đăng ký học',
                'mo_ta' => 'Nhóm quyền đăng ký môn học, xếp lớp'
            ],
        ];

        foreach ($data as $item) {
            DB::table('nhom_quyen')->updateOrInsert(
                ['ma_nhom' => $item['ma_nhom']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
