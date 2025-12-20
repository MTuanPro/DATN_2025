<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder gán actors cho các quyền
 * Mỗi quyền được gán cho các actor (nhóm người dùng) phù hợp
 * 
 * Ví dụ:
 * - Quyền quản lý user, vai trò: chỉ dành cho Admin
 * - Quyền quản lý khoa, ngành, môn học: dành cho Phòng đào tạo
 * - Quyền nhập điểm: dành cho Giảng viên và Phòng đào tạo
 * - Quyền xem thông tin: có thể cho nhiều actor
 */
class QuyenActorSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả quyền
        $quyens = DB::table('quyen')->get()->keyBy('ma_quyen');

        // Định nghĩa actor cho từng quyền
        // Cấu trúc: 'ma_quyen' => ['actor1', 'actor2', ...]
        $quyenActors = [
            // ===== Quản lý tài khoản - Chỉ Admin =====
            'user.xem' => ['admin'],
            'user.them' => ['admin'],
            'user.sua' => ['admin'],
            'user.xoa' => ['admin'],
            'vai_tro.xem' => ['admin'],
            'vai_tro.them' => ['admin'],
            'vai_tro.sua' => ['admin'],
            'vai_tro.xoa' => ['admin'],

            // ===== Quản lý sinh viên - Phòng đào tạo =====
            'sinh_vien.xem' => ['dao_tao', 'giang_vien'], // GV cần xem danh sách SV trong lớp
            'sinh_vien.them' => ['dao_tao'],
            'sinh_vien.sua' => ['dao_tao'],
            'sinh_vien.xoa' => ['dao_tao'],

            // ===== Quản lý giảng viên - Phòng đào tạo =====
            'giang_vien.xem' => ['dao_tao'],
            'giang_vien.them' => ['dao_tao'],
            'giang_vien.sua' => ['dao_tao'],
            'giang_vien.xoa' => ['dao_tao'],

            // ===== Quản lý danh mục - Phòng đào tạo =====
            'khoa.xem' => ['dao_tao'],
            'khoa.them' => ['dao_tao'],
            'khoa.sua' => ['dao_tao'],
            'khoa.xoa' => ['dao_tao'],
            'nganh.xem' => ['dao_tao'],
            'nganh.them' => ['dao_tao'],
            'nganh.sua' => ['dao_tao'],
            'nganh.xoa' => ['dao_tao'],
            'mon_hoc.xem' => ['dao_tao', 'giang_vien'], // GV cần xem môn học để giảng dạy
            'mon_hoc.them' => ['dao_tao'],
            'mon_hoc.sua' => ['dao_tao'],
            'mon_hoc.xoa' => ['dao_tao'],

            // ===== Quản lý điểm - Giảng viên và Phòng đào tạo =====
            'diem.nhap' => ['giang_vien'], // Chỉ GV mới nhập điểm
            'diem.xem' => ['dao_tao', 'giang_vien', 'sinh_vien'], // SV xem điểm của mình
            'diem.sua' => ['dao_tao', 'giang_vien'], // GV sửa điểm trước khi khóa
            'diem.khoa' => ['dao_tao'], // Chỉ đào tạo mới khóa điểm

            // ===== Quản lý học phí - Phòng đào tạo và Admin =====
            'hoc_phi.xem' => ['dao_tao', 'sinh_vien'], // SV xem học phí của mình
            'hoc_phi.cau_hinh' => ['dao_tao'],
            'hoc_phi.thu' => ['dao_tao'],
        ];

        // Xóa dữ liệu cũ
        DB::table('quyen_actor')->truncate();

        // Insert dữ liệu mới
        $data = [];
        foreach ($quyenActors as $maQuyen => $actors) {
            if (!isset($quyens[$maQuyen])) {
                continue;
            }

            $quyenId = $quyens[$maQuyen]->id;

            foreach ($actors as $actor) {
                $data[] = [
                    'quyen_id' => $quyenId,
                    'actor' => $actor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($data)) {
            DB::table('quyen_actor')->insert($data);
        }

        $this->command->info('Đã gán actors cho ' . count($quyenActors) . ' quyền.');
    }
}
