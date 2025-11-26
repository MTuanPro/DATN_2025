<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NganhSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID các khoa
        $khoaCNTT = DB::table('khoa')->where('ma_khoa', 'CNTT')->value('id');
        $khoaKT = DB::table('khoa')->where('ma_khoa', 'KT')->value('id');
        $khoaNN = DB::table('khoa')->where('ma_khoa', 'NN')->value('id');

        $data = [
            // Khoa CNTT - 3 ngành
            ['ma_nganh' => '7480201', 'ten_nganh' => 'Công nghệ thông tin', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo kỹ sư phần mềm, lập trình viên chuyên nghiệp'],
            ['ma_nganh' => '7480202', 'ten_nganh' => 'Khoa học máy tính', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo chuyên gia về thuật toán, AI, machine learning'],
            ['ma_nganh' => '7480299', 'ten_nganh' => 'An toàn thông tin', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo chuyên gia bảo mật, an ninh mạng'],

            // Khoa Kinh tế - 3 ngành
            ['ma_nganh' => '7340101', 'ten_nganh' => 'Quản trị kinh doanh', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo quản lý doanh nghiệp, khởi nghiệp'],
            ['ma_nganh' => '7340201', 'ten_nganh' => 'Tài chính - Ngân hàng', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo chuyên viên tài chính, ngân hàng'],
            ['ma_nganh' => '7340301', 'ten_nganh' => 'Kế toán', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo kế toán viên, kiểm toán viên chuyên nghiệp'],

            // Khoa Ngoại ngữ - 3 ngành
            ['ma_nganh' => '7220201', 'ten_nganh' => 'Ngôn ngữ Anh', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo giáo viên, phiên dịch, biên dịch tiếng Anh'],
            ['ma_nganh' => '7220203', 'ten_nganh' => 'Ngôn ngữ Nhật', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo chuyên viên tiếng Nhật, phiên dịch'],
            ['ma_nganh' => '7220204', 'ten_nganh' => 'Ngôn ngữ Trung Quốc', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo chuyên viên tiếng Trung, phiên dịch'],
        ];

        foreach ($data as $item) {
            DB::table('nganh')->updateOrInsert(
                ['ma_nganh' => $item['ma_nganh']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Đã tạo ' . count($data) . ' ngành (3 khoa x 3 ngành)');
    }
}
