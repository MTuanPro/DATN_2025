<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ma_khoa' => 'CNTT', 'ten_khoa' => 'Công nghệ thông tin', 'truong_khoa_id' => null, 'mo_ta' => 'Khoa đào tạo về CNTT, phần mềm, mạng máy tính'],
            ['ma_khoa' => 'KT', 'ten_khoa' => 'Kinh tế', 'truong_khoa_id' => null, 'mo_ta' => 'Khoa đào tạo về kinh tế, quản trị kinh doanh'],
            ['ma_khoa' => 'NN', 'ten_khoa' => 'Ngoại ngữ', 'truong_khoa_id' => null, 'mo_ta' => 'Khoa đào tạo ngôn ngữ Anh, Nhật, Trung, Hàn'],
            ['ma_khoa' => 'CNSH', 'ten_khoa' => 'Công nghệ sinh học', 'truong_khoa_id' => null, 'mo_ta' => 'Khoa nghiên cứu và ứng dụng công nghệ sinh học'],
            ['ma_khoa' => 'KT-XD', 'ten_khoa' => 'Kỹ thuật xây dựng', 'truong_khoa_id' => null, 'mo_ta' => 'Khoa đào tạo kỹ sư xây dựng dân dụng và công nghiệp'],
        ];

        foreach ($data as $item) {
            DB::table('khoa')->updateOrInsert(
                ['ma_khoa' => $item['ma_khoa']],
                array_merge($item, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
