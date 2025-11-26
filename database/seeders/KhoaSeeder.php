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
                'mo_ta' => 'Khoa Công nghệ thông tin đào tạo các ngành về phần mềm, mạng máy tính, an toàn thông tin',
            ],
            [
                'ma_khoa' => 'KT',
                'ten_khoa' => 'Kinh tế',
                'mo_ta' => 'Khoa Kinh tế đào tạo các ngành về quản trị kinh doanh, tài chính ngân hàng, kế toán',
            ],
            [
                'ma_khoa' => 'NN',
                'ten_khoa' => 'Ngoại ngữ',
                'mo_ta' => 'Khoa Ngoại ngữ đào tạo tiếng Anh, tiếng Nhật, tiếng Trung Quốc',
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

        $this->command->info('✅ Đã tạo ' . count($data) . ' khoa');
    }
}
