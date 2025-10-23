<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhongHocSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Phòng học lý thuyết - Nhà A
            ['ma_phong' => 'A101', 'ten_phong' => 'Phòng học A101', 'suc_chua' => 60, 'vi_tri' => 'Tầng 1 - Nhà A', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học lý thuyết tiêu chuẩn, có máy chiếu và hệ thống âm thanh'],
            ['ma_phong' => 'A102', 'ten_phong' => 'Phòng học A102', 'suc_chua' => 60, 'vi_tri' => 'Tầng 1 - Nhà A', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học lý thuyết tiêu chuẩn'],
            ['ma_phong' => 'A201', 'ten_phong' => 'Phòng học A201', 'suc_chua' => 80, 'vi_tri' => 'Tầng 2 - Nhà A', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học lớn, phù hợp cho lớp đông học viên'],
            ['ma_phong' => 'A202', 'ten_phong' => 'Phòng học A202', 'suc_chua' => 80, 'vi_tri' => 'Tầng 2 - Nhà A', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học lớn'],
            ['ma_phong' => 'A301', 'ten_phong' => 'Phòng học A301', 'suc_chua' => 50, 'vi_tri' => 'Tầng 3 - Nhà A', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học nhỏ gọn'],

            // Phòng học lý thuyết - Nhà B
            ['ma_phong' => 'B101', 'ten_phong' => 'Phòng học B101', 'suc_chua' => 70, 'vi_tri' => 'Tầng 1 - Nhà B', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học lý thuyết'],
            ['ma_phong' => 'B102', 'ten_phong' => 'Phòng học B102', 'suc_chua' => 70, 'vi_tri' => 'Tầng 1 - Nhà B', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học lý thuyết'],
            ['ma_phong' => 'B201', 'ten_phong' => 'Phòng học B201', 'suc_chua' => 90, 'vi_tri' => 'Tầng 2 - Nhà B', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học lớn nhất'],

            // Phòng máy tính
            ['ma_phong' => 'LAB01', 'ten_phong' => 'Phòng máy 01', 'suc_chua' => 40, 'vi_tri' => 'Tầng 1 - Nhà C', 'loai_phong' => 'Phòng máy', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng máy 40 máy, cấu hình i5-Gen12, 16GB RAM, phù hợp lập trình'],
            ['ma_phong' => 'LAB02', 'ten_phong' => 'Phòng máy 02', 'suc_chua' => 40, 'vi_tri' => 'Tầng 1 - Nhà C', 'loai_phong' => 'Phòng máy', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng máy 40 máy, cấu hình i5-Gen12, 16GB RAM'],
            ['ma_phong' => 'LAB03', 'ten_phong' => 'Phòng máy 03', 'suc_chua' => 50, 'vi_tri' => 'Tầng 2 - Nhà C', 'loai_phong' => 'Phòng máy', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng máy 50 máy, cấu hình i7-Gen13, 32GB RAM, phục vụ AI/ML'],
            ['ma_phong' => 'LAB04', 'ten_phong' => 'Phòng máy 04', 'suc_chua' => 45, 'vi_tri' => 'Tầng 2 - Nhà C', 'loai_phong' => 'Phòng máy', 'trang_thai' => 'Bảo trì', 'mo_ta' => 'Phòng máy đang nâng cấp hệ thống'],

            // Phòng thực hành
            ['ma_phong' => 'TH01', 'ten_phong' => 'Phòng thực hành 01', 'suc_chua' => 30, 'vi_tri' => 'Tầng 1 - Nhà D', 'loai_phong' => 'Thực hành', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng thực hành mạng máy tính, có Router, Switch, thiết bị mạng'],
            ['ma_phong' => 'TH02', 'ten_phong' => 'Phòng thực hành 02', 'suc_chua' => 30, 'vi_tri' => 'Tầng 1 - Nhà D', 'loai_phong' => 'Thực hành', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng thực hành IoT, Arduino, Raspberry Pi'],
            ['ma_phong' => 'TH03', 'ten_phong' => 'Phòng thực hành 03', 'suc_chua' => 25, 'vi_tri' => 'Tầng 2 - Nhà D', 'loai_phong' => 'Thực hành', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng thực hành An toàn thông tin, có hệ thống Penetration Testing'],

            // Hội trường
            ['ma_phong' => 'HT01', 'ten_phong' => 'Hội trường lớn', 'suc_chua' => 300, 'vi_tri' => 'Tầng 1 - Nhà E', 'loai_phong' => 'Hội trường', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Hội trường lớn, phục vụ sự kiện, lễ khai giảng, tốt nghiệp'],
            ['ma_phong' => 'HT02', 'ten_phong' => 'Hội trường nhỏ', 'suc_chua' => 150, 'vi_tri' => 'Tầng 2 - Nhà E', 'loai_phong' => 'Hội trường', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Hội trường nhỏ, phục vụ seminar, workshop'],
            ['ma_phong' => 'HT03', 'ten_phong' => 'Phòng họp', 'suc_chua' => 50, 'vi_tri' => 'Tầng 3 - Nhà E', 'loai_phong' => 'Hội trường', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng họp, phục vụ cuộc họp, báo cáo tiểu luận'],

            // Phòng đặc biệt
            ['ma_phong' => 'C301', 'ten_phong' => 'Phòng học thông minh', 'suc_chua' => 60, 'vi_tri' => 'Tầng 3 - Nhà C', 'loai_phong' => 'Lý thuyết', 'trang_thai' => 'Hoạt động', 'mo_ta' => 'Phòng học thông minh với bảng tương tác, camera livestream, phục vụ học online'],
        ];

        foreach ($data as $item) {
            DB::table('phong_hoc')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
