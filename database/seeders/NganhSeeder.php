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
        $khoaCNSH = DB::table('khoa')->where('ma_khoa', 'CNSH')->value('id');
        $khoaKTXD = DB::table('khoa')->where('ma_khoa', 'KT-XD')->value('id');

        $data = [
            // Khoa CNTT
            ['ma_nganh' => '7480201', 'ten_nganh' => 'Công nghệ thông tin', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo kỹ sư CNTT'],
            ['ma_nganh' => '7480103', 'ten_nganh' => 'Khoa học máy tính', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo về khoa học máy tính và AI'],
            ['ma_nganh' => '7480102', 'ten_nganh' => 'Kỹ thuật phần mềm', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo kỹ sư phần mềm chuyên nghiệp'],
            ['ma_nganh' => '7340406', 'ten_nganh' => 'An toàn thông tin', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo chuyên gia an toàn mạng'],

            // Khoa Kinh tế
            ['ma_nganh' => '7340101', 'ten_nganh' => 'Kinh tế', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo cử nhân kinh tế'],
            ['ma_nganh' => '7340401', 'ten_nganh' => 'Quản trị kinh doanh', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo về quản trị và kinh doanh'],
            ['ma_nganh' => '7340301', 'ten_nganh' => 'Kế toán', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo kế toán viên và kiểm toán viên'],
            ['ma_nganh' => '7340201', 'ten_nganh' => 'Tài chính - Ngân hàng', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo về tài chính ngân hàng'],

            // Khoa Ngoại ngữ
            ['ma_nganh' => '7220201', 'ten_nganh' => 'Ngôn ngữ Anh', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo giáo viên và phiên dịch tiếng Anh'],
            ['ma_nganh' => '7220205', 'ten_nganh' => 'Ngôn ngữ Nhật', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo ngôn ngữ và văn hóa Nhật Bản'],
            ['ma_nganh' => '7220206', 'ten_nganh' => 'Ngôn ngữ Trung Quốc', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo tiếng Trung và kinh doanh'],

            // Khoa Công nghệ sinh học
            ['ma_nganh' => '7420201', 'ten_nganh' => 'Công nghệ sinh học', 'khoa_id' => $khoaCNSH, 'mo_ta' => 'Nghiên cứu và ứng dụng CNSH'],
            ['ma_nganh' => '7420101', 'ten_nganh' => 'Sinh học', 'khoa_id' => $khoaCNSH, 'mo_ta' => 'Đào tạo về sinh học và y sinh'],

            // Khoa Kỹ thuật xây dựng
            ['ma_nganh' => '7580201', 'ten_nganh' => 'Kỹ thuật xây dựng', 'khoa_id' => $khoaKTXD, 'mo_ta' => 'Đào tạo kỹ sư xây dựng'],
            ['ma_nganh' => '7580202', 'ten_nganh' => 'Kiến trúc', 'khoa_id' => $khoaKTXD, 'mo_ta' => 'Đào tạo kiến trúc sư'],
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
    }
}
