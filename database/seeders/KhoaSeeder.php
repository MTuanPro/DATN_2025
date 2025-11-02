<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'ma_khoa' => 'CNTT',
                'ten_khoa' => 'Công nghệ thông tin',
                'mo_ta' => 'Khoa Công nghệ thông tin đào tạo các ngành về phần mềm, mạng máy tính, an toàn thông tin, khoa học dữ liệu',
            ],
            [
                'ma_khoa' => 'KT',
                'ten_khoa' => 'Kinh tế',
                'mo_ta' => 'Khoa Kinh tế đào tạo các ngành về quản trị kinh doanh, tài chính ngân hàng, kế toán, marketing',
            ],
            [
                'ma_khoa' => 'NN',
                'ten_khoa' => 'Ngoại ngữ',
                'mo_ta' => 'Khoa Ngoại ngữ đào tạo tiếng Anh, tiếng Nhật, tiếng Trung, tiếng Hàn chuyên nghiệp',
            ],
            [
                'ma_khoa' => 'DL',
                'ten_khoa' => 'Du lịch',
                'mo_ta' => 'Khoa Du lịch đào tạo quản trị khách sạn, điều hành tour, quản trị dịch vụ du lịch và lữ hành',
            ],
            [
                'ma_khoa' => 'XH',
                'ten_khoa' => 'Khoa học xã hội',
                'mo_ta' => 'Khoa Khoa học xã hội đào tạo xã hội học, tâm lý học, công tác xã hội',
            ],
            [
                'ma_khoa' => 'SP',
                'ten_khoa' => 'Sư phạm',
                'mo_ta' => 'Khoa Sư phạm đào tạo giáo viên mầm non, tiểu học, trung học cơ sở',
            ],
            [
                'ma_khoa' => 'KT-XD',
                'ten_khoa' => 'Kỹ thuật xây dựng',
                'mo_ta' => 'Khoa Kỹ thuật xây dựng đào tạo kỹ sư xây dựng dân dụng, công nghiệp, giao thông',
            ],
            [
                'ma_khoa' => 'MT',
                'ten_khoa' => 'Môi trường',
                'mo_ta' => 'Khoa Môi trường đào tạo quản lý môi trường, công nghệ môi trường, khoa học môi trường',
            ],
        ];

        foreach ($data as $item) {
            DB::table('khoa')->updateOrInsert(
                ['ma_khoa' => $item['ma_khoa']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
