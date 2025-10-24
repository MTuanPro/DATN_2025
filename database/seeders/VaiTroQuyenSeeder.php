<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaiTroQuyenSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy vai trò và quyền
        $vaiTros = DB::table('vai_tro')->get()->keyBy('ma_vai_tro');
        $quyens = DB::table('quyen')->get()->keyBy('ma_quyen');

        // Gán quyền cho Admin (tất cả quyền)
        $adminQuyens = [];
        foreach ($quyens as $quyen) {
            $adminQuyens[] = [
                'vai_tro_id' => $vaiTros['admin']->id,
                'quyen_id' => $quyen->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Gán quyền cho Trưởng phòng Đào tạo (hầu hết quyền)
        $truongPhongQuyens = [];
        $truongPhongMaQuyens = [
            'sinh_vien.xem',
            'sinh_vien.them',
            'sinh_vien.sua',
            'giang_vien.xem',
            'giang_vien.them',
            'giang_vien.sua',
            'khoa.xem',
            'khoa.them',
            'khoa.sua',
            'nganh.xem',
            'nganh.them',
            'mon_hoc.xem',
            'mon_hoc.them',
            'diem.xem',
            'diem.khoa',
            'hoc_phi.xem',
            'hoc_phi.cau_hinh',
        ];

        foreach ($truongPhongMaQuyens as $maQuyen) {
            if (isset($quyens[$maQuyen])) {
                $truongPhongQuyens[] = [
                    'vai_tro_id' => $vaiTros['truong_phong_dt']->id,
                    'quyen_id' => $quyens[$maQuyen]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Gán quyền cho Nhân viên Đào tạo
        $nhanVienQuyens = [];
        $nhanVienMaQuyens = [
            'sinh_vien.xem',
            'sinh_vien.them',
            'sinh_vien.sua',
            'giang_vien.xem',
            'khoa.xem',
            'nganh.xem',
            'mon_hoc.xem',
            'diem.xem',
            'hoc_phi.xem',
        ];

        foreach ($nhanVienMaQuyens as $maQuyen) {
            if (isset($quyens[$maQuyen])) {
                $nhanVienQuyens[] = [
                    'vai_tro_id' => $vaiTros['nhan_vien_dt']->id,
                    'quyen_id' => $quyens[$maQuyen]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Gán quyền cho Giảng viên
        $giangVienQuyens = [];
        $giangVienMaQuyens = [
            'sinh_vien.xem',
            'diem.nhap',
            'diem.xem',
            'diem.sua',
        ];

        foreach ($giangVienMaQuyens as $maQuyen) {
            if (isset($quyens[$maQuyen])) {
                $giangVienQuyens[] = [
                    'vai_tro_id' => $vaiTros['giang_vien']->id,
                    'quyen_id' => $quyens[$maQuyen]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Sinh viên chỉ có quyền xem thông tin của mình
        $sinhVienQuyens = [
            [
                'vai_tro_id' => $vaiTros['sinh_vien']->id,
                'quyen_id' => $quyens['diem.xem']->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vai_tro_id' => $vaiTros['sinh_vien']->id,
                'quyen_id' => $quyens['hoc_phi.xem']->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert tất cả
        DB::table('vai_tro_quyen')->insert(array_merge(
            $adminQuyens,
            $truongPhongQuyens,
            $nhanVienQuyens,
            $giangVienQuyens,
            $sinhVienQuyens
        ));
    }
}
