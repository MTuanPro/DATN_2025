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
        $khoaDL = DB::table('khoa')->where('ma_khoa', 'DL')->value('id');
        $khoaXH = DB::table('khoa')->where('ma_khoa', 'XH')->value('id');
        $khoaSP = DB::table('khoa')->where('ma_khoa', 'SP')->value('id');
        $khoaXD = DB::table('khoa')->where('ma_khoa', 'KT-XD')->value('id');
        $khoaMT = DB::table('khoa')->where('ma_khoa', 'MT')->value('id');

        $data = [
            // Khoa CNTT
            ['ma_nganh' => '7480201', 'ten_nganh' => 'Công nghệ thông tin', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo kỹ sư phần mềm, lập trình viên chuyên nghiệp'],
            ['ma_nganh' => '7480202', 'ten_nganh' => 'Khoa học máy tính', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo chuyên gia về thuật toán, AI, machine learning'],
            ['ma_nganh' => '7480299', 'ten_nganh' => 'An toàn thông tin', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo chuyên gia bảo mật, an ninh mạng'],
            ['ma_nganh' => '7480203', 'ten_nganh' => 'Kỹ thuật phần mềm', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo kỹ sư phát triển phần mềm quy mô lớn'],
            ['ma_nganh' => '7340405', 'ten_nganh' => 'Khoa học dữ liệu', 'khoa_id' => $khoaCNTT, 'mo_ta' => 'Đào tạo chuyên gia phân tích dữ liệu, data scientist'],

            // Khoa Kinh tế
            ['ma_nganh' => '7340101', 'ten_nganh' => 'Quản trị kinh doanh', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo quản lý doanh nghiệp, khởi nghiệp'],
            ['ma_nganh' => '7340201', 'ten_nganh' => 'Tài chính - Ngân hàng', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo chuyên viên tài chính, ngân hàng'],
            ['ma_nganh' => '7340301', 'ten_nganh' => 'Kế toán', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo kế toán viên, kiểm toán viên chuyên nghiệp'],
            ['ma_nganh' => '7340115', 'ten_nganh' => 'Marketing', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo chuyên viên marketing, quản lý thương hiệu'],
            ['ma_nganh' => '7340401', 'ten_nganh' => 'Kinh tế', 'khoa_id' => $khoaKT, 'mo_ta' => 'Đào tạo chuyên gia kinh tế, nghiên cứu thị trường'],

            // Khoa Ngoại ngữ
            ['ma_nganh' => '7220201', 'ten_nganh' => 'Ngôn ngữ Anh', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo giáo viên, phiên dịch, biên dịch tiếng Anh'],
            ['ma_nganh' => '7220203', 'ten_nganh' => 'Ngôn ngữ Nhật', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo chuyên viên tiếng Nhật, phiên dịch'],
            ['ma_nganh' => '7220204', 'ten_nganh' => 'Ngôn ngữ Trung Quốc', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo chuyên viên tiếng Trung, phiên dịch'],
            ['ma_nganh' => '7220209', 'ten_nganh' => 'Ngôn ngữ Hàn Quốc', 'khoa_id' => $khoaNN, 'mo_ta' => 'Đào tạo chuyên viên tiếng Hàn, phiên dịch'],

            // Khoa Du lịch
            ['ma_nganh' => '7810101', 'ten_nganh' => 'Quản trị khách sạn', 'khoa_id' => $khoaDL, 'mo_ta' => 'Đào tạo quản lý khách sạn, resort cao cấp'],
            ['ma_nganh' => '7810103', 'ten_nganh' => 'Quản trị dịch vụ du lịch và lữ hành', 'khoa_id' => $khoaDL, 'mo_ta' => 'Đào tạo điều hành tour, hướng dẫn viên'],

            // Khoa Khoa học xã hội
            ['ma_nganh' => '7310301', 'ten_nganh' => 'Xã hội học', 'khoa_id' => $khoaXH, 'mo_ta' => 'Đào tạo nghiên cứu viên xã hội học'],
            ['ma_nganh' => '7310401', 'ten_nganh' => 'Tâm lý học', 'khoa_id' => $khoaXH, 'mo_ta' => 'Đào tạo tâm lý trị liệu, tư vấn tâm lý'],
            ['ma_nganh' => '7760101', 'ten_nganh' => 'Công tác xã hội', 'khoa_id' => $khoaXH, 'mo_ta' => 'Đào tạo nhân viên công tác xã hội'],

            // Khoa Sư phạm
            ['ma_nganh' => '7140201', 'ten_nganh' => 'Sư phạm mầm non', 'khoa_id' => $khoaSP, 'mo_ta' => 'Đào tạo giáo viên mầm non'],
            ['ma_nganh' => '7140202', 'ten_nganh' => 'Sư phạm tiểu học', 'khoa_id' => $khoaSP, 'mo_ta' => 'Đào tạo giáo viên tiểu học'],
            ['ma_nganh' => '7140111', 'ten_nganh' => 'Sư phạm toán học', 'khoa_id' => $khoaSP, 'mo_ta' => 'Đào tạo giáo viên toán THCS, THPT'],

            // Khoa Kỹ thuật xây dựng
            ['ma_nganh' => '7580201', 'ten_nganh' => 'Kỹ thuật xây dựng', 'khoa_id' => $khoaXD, 'mo_ta' => 'Đào tạo kỹ sư xây dựng công trình dân dụng, công nghiệp'],
            ['ma_nganh' => '7580203', 'ten_nganh' => 'Kỹ thuật xây dựng công trình giao thông', 'khoa_id' => $khoaXD, 'mo_ta' => 'Đào tạo kỹ sư xây dựng đường, cầu'],

            // Khoa Môi trường
            ['ma_nganh' => '7440301', 'ten_nganh' => 'Quản lý tài nguyên và môi trường', 'khoa_id' => $khoaMT, 'mo_ta' => 'Đào tạo chuyên viên quản lý môi trường'],
            ['ma_nganh' => '7510406', 'ten_nganh' => 'Công nghệ kỹ thuật môi trường', 'khoa_id' => $khoaMT, 'mo_ta' => 'Đào tạo kỹ sư xử lý môi trường'],
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
