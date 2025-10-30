<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhongHocSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];

        // Tòa A - Phòng lý thuyết
        for ($tang = 1; $tang <= 5; $tang++) {
            for ($phong = 1; $phong <= 8; $phong++) {
                $maPhong = 'A' . $tang . str_pad($phong, 2, '0', STR_PAD_LEFT);
                $data[] = [
                    'ma_phong' => $maPhong,
                    'ten_phong' => 'Phòng ' . $maPhong,
                    'suc_chua' => rand(40, 60),
                    'vi_tri' => 'Tầng ' . $tang . ' - Tòa A',
                    'loai_phong' => 'Lý thuyết',
                    'trang_thai' => 'Hoạt động',
                ];
            }
        }

        // Tòa B - Phòng máy và Lab
        for ($tang = 1; $tang <= 4; $tang++) {
            for ($phong = 1; $phong <= 6; $phong++) {
                $maPhong = 'B' . $tang . str_pad($phong, 2, '0', STR_PAD_LEFT);
                $data[] = [
                    'ma_phong' => $maPhong,
                    'ten_phong' => 'Phòng máy ' . $maPhong,
                    'suc_chua' => rand(35, 50),
                    'vi_tri' => 'Tầng ' . $tang . ' - Tòa B',
                    'loai_phong' => 'Phòng máy',
                    'trang_thai' => 'Hoạt động',
                ];
            }
        }

        // Tòa C - Phòng thực hành, phòng đa năng
        for ($tang = 1; $tang <= 3; $tang++) {
            for ($phong = 1; $phong <= 5; $phong++) {
                $maPhong = 'C' . $tang . str_pad($phong, 2, '0', STR_PAD_LEFT);
                $loaiPhong = ($phong <= 3) ? 'Lý thuyết' : 'Phòng thực hành';
                $data[] = [
                    'ma_phong' => $maPhong,
                    'ten_phong' => 'Phòng ' . $maPhong,
                    'suc_chua' => rand(30, 45),
                    'vi_tri' => 'Tầng ' . $tang . ' - Tòa C',
                    'loai_phong' => $loaiPhong,
                    'trang_thai' => 'Hoạt động',
                ];
            }
        }

        // Hội trường
        $data[] = [
            'ma_phong' => 'HT01',
            'ten_phong' => 'Hội trường A',
            'suc_chua' => 300,
            'vi_tri' => 'Tầng 1 - Tòa A',
            'loai_phong' => 'Hội trường',
            'trang_thai' => 'Hoạt động',
        ];

        $data[] = [
            'ma_phong' => 'HT02',
            'ten_phong' => 'Hội trường B',
            'suc_chua' => 200,
            'vi_tri' => 'Tầng 1 - Tòa B',
            'loai_phong' => 'Hội trường',
            'trang_thai' => 'Hoạt động',
        ];

        $data[] = [
            'ma_phong' => 'HT03',
            'ten_phong' => 'Hội trường C (Đa năng)',
            'suc_chua' => 150,
            'vi_tri' => 'Tầng 2 - Tòa C',
            'loai_phong' => 'Hội trường',
            'trang_thai' => 'Hoạt động',
        ];

        foreach ($data as $item) {
            DB::table('phong_hoc')->updateOrInsert(
                ['ma_phong' => $item['ma_phong']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
