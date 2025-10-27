<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuyenSeeder extends Seeder
{
    public function run(): void
    {
        $nhomQuyens = DB::table('nhom_quyen')->get()->keyBy('ma_nhom');

        $data = [
            // Quản lý tài khoản
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'user.xem', 'ten_quyen' => 'Xem danh sách user', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'user.them', 'ten_quyen' => 'Thêm user', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'user.sua', 'ten_quyen' => 'Sửa user', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'user.xoa', 'ten_quyen' => 'Xóa user', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'vai_tro.xem', 'ten_quyen' => 'Xem vai trò', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'vai_tro.them', 'ten_quyen' => 'Thêm vai trò', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'vai_tro.sua', 'ten_quyen' => 'Sửa vai trò', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_tai_khoan']->id, 'ma_quyen' => 'vai_tro.xoa', 'ten_quyen' => 'Xóa vai trò', 'mo_ta' => null],

            // Quản lý sinh viên
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_sinh_vien']->id, 'ma_quyen' => 'sinh_vien.xem', 'ten_quyen' => 'Xem sinh viên', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_sinh_vien']->id, 'ma_quyen' => 'sinh_vien.them', 'ten_quyen' => 'Thêm sinh viên', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_sinh_vien']->id, 'ma_quyen' => 'sinh_vien.sua', 'ten_quyen' => 'Sửa sinh viên', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_sinh_vien']->id, 'ma_quyen' => 'sinh_vien.xoa', 'ten_quyen' => 'Xóa sinh viên', 'mo_ta' => null],

            // Quản lý giảng viên
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_giang_vien']->id, 'ma_quyen' => 'giang_vien.xem', 'ten_quyen' => 'Xem giảng viên', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_giang_vien']->id, 'ma_quyen' => 'giang_vien.them', 'ten_quyen' => 'Thêm giảng viên', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_giang_vien']->id, 'ma_quyen' => 'giang_vien.sua', 'ten_quyen' => 'Sửa giảng viên', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_giang_vien']->id, 'ma_quyen' => 'giang_vien.xoa', 'ten_quyen' => 'Xóa giảng viên', 'mo_ta' => null],

            // Quản lý danh mục
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'khoa.xem', 'ten_quyen' => 'Xem khoa', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'khoa.them', 'ten_quyen' => 'Thêm khoa', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'khoa.sua', 'ten_quyen' => 'Sửa khoa', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'khoa.xoa', 'ten_quyen' => 'Xóa khoa', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'nganh.xem', 'ten_quyen' => 'Xem ngành', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'nganh.them', 'ten_quyen' => 'Thêm ngành', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'mon_hoc.xem', 'ten_quyen' => 'Xem môn học', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_danh_muc']->id, 'ma_quyen' => 'mon_hoc.them', 'ten_quyen' => 'Thêm môn học', 'mo_ta' => null],

            // Quản lý điểm
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_diem']->id, 'ma_quyen' => 'diem.nhap', 'ten_quyen' => 'Nhập điểm', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_diem']->id, 'ma_quyen' => 'diem.xem', 'ten_quyen' => 'Xem điểm', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_diem']->id, 'ma_quyen' => 'diem.sua', 'ten_quyen' => 'Sửa điểm', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_diem']->id, 'ma_quyen' => 'diem.khoa', 'ten_quyen' => 'Khóa điểm', 'mo_ta' => null],

            // Quản lý học phí
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_hoc_phi']->id, 'ma_quyen' => 'hoc_phi.xem', 'ten_quyen' => 'Xem học phí', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_hoc_phi']->id, 'ma_quyen' => 'hoc_phi.cau_hinh', 'ten_quyen' => 'Cấu hình học phí', 'mo_ta' => null],
            ['nhom_quyen_id' => $nhomQuyens['quan_ly_hoc_phi']->id, 'ma_quyen' => 'hoc_phi.thu', 'ten_quyen' => 'Thu học phí', 'mo_ta' => null],
        ];

        foreach ($data as $item) {
            DB::table('quyen')->updateOrInsert(
                ['ma_quyen' => $item['ma_quyen']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
