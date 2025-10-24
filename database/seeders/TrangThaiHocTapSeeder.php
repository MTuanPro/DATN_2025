<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiHocTapSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'ten_trang_thai' => 'Đang học',
                'mo_ta' => 'Sinh viên đang theo học và có kết quả học tập tốt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Bảo lưu',
                'mo_ta' => 'Sinh viên tạm dừng học tập trong một khoảng thời gian do lý do cá nhân',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Thôi học',
                'mo_ta' => 'Sinh viên đã chấm dứt việc học tại trường',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Tốt nghiệp',
                'mo_ta' => 'Sinh viên đã hoàn thành chương trình đào tạo và được cấp bằng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Đang chờ xét tốt nghiệp',
                'mo_ta' => 'Sinh viên đã hoàn thành khóa học, đang chờ xét duyệt tốt nghiệp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('trang_thai_hoc_tap')->insert($data);
    }
}
