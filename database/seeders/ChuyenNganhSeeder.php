<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChuyenNganhSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID các ngành
        $nganhCNTT = DB::table('nganh')->where('ma_nganh', '7480201')->value('id');
        $nganhKHMT = DB::table('nganh')->where('ma_nganh', '7480103')->value('id');
        $nganhKTPM = DB::table('nganh')->where('ma_nganh', '7480102')->value('id');
        $nganhATTT = DB::table('nganh')->where('ma_nganh', '7340406')->value('id');

        $nganhQTKD = DB::table('nganh')->where('ma_nganh', '7340401')->value('id');
        $nganhKeToan = DB::table('nganh')->where('ma_nganh', '7340301')->value('id');

        $nganhNgonNguAnh = DB::table('nganh')->where('ma_nganh', '7220201')->value('id');

        $data = [
            // Chuyên ngành Công nghệ thông tin
            ['ma_chuyen_nganh' => 'CNPM', 'ten_chuyen_nganh' => 'Công nghệ phần mềm', 'nganh_id' => $nganhCNTT, 'tong_tin_chi_toi_thieu' => 130, 'mo_ta' => 'Phát triển phần mềm ứng dụng và hệ thống'],
            ['ma_chuyen_nganh' => 'HTTT', 'ten_chuyen_nganh' => 'Hệ thống thông tin', 'nganh_id' => $nganhCNTT, 'tong_tin_chi_toi_thieu' => 130, 'mo_ta' => 'Quản trị và phát triển hệ thống thông tin'],
            ['ma_chuyen_nganh' => 'MMT', 'ten_chuyen_nganh' => 'Mạng máy tính', 'nganh_id' => $nganhCNTT, 'tong_tin_chi_toi_thieu' => 130, 'mo_ta' => 'Thiết kế và quản trị mạng'],

            // Chuyên ngành Khoa học máy tính
            ['ma_chuyen_nganh' => 'AI-ML', 'ten_chuyen_nganh' => 'Trí tuệ nhân tạo và Học máy', 'nganh_id' => $nganhKHMT, 'tong_tin_chi_toi_thieu' => 135, 'mo_ta' => 'AI, Machine Learning, Deep Learning'],
            ['ma_chuyen_nganh' => 'DS', 'ten_chuyen_nganh' => 'Khoa học dữ liệu', 'nganh_id' => $nganhKHMT, 'tong_tin_chi_toi_thieu' => 135, 'mo_ta' => 'Phân tích và xử lý dữ liệu lớn'],

            // Chuyên ngành Kỹ thuật phần mềm
            ['ma_chuyen_nganh' => 'WEB', 'ten_chuyen_nganh' => 'Phát triển Web', 'nganh_id' => $nganhKTPM, 'tong_tin_chi_toi_thieu' => 128, 'mo_ta' => 'Full-stack web development'],
            ['ma_chuyen_nganh' => 'MOBILE', 'ten_chuyen_nganh' => 'Phát triển ứng dụng di động', 'nganh_id' => $nganhKTPM, 'tong_tin_chi_toi_thieu' => 128, 'mo_ta' => 'Android, iOS development'],
            ['ma_chuyen_nganh' => 'GAME', 'ten_chuyen_nganh' => 'Phát triển Game', 'nganh_id' => $nganhKTPM, 'tong_tin_chi_toi_thieu' => 128, 'mo_ta' => 'Game design và development'],

            // Chuyên ngành An toàn thông tin
            ['ma_chuyen_nganh' => 'CYBER', 'ten_chuyen_nganh' => 'An ninh mạng', 'nganh_id' => $nganhATTT, 'tong_tin_chi_toi_thieu' => 132, 'mo_ta' => 'Bảo mật hệ thống và mạng'],
            ['ma_chuyen_nganh' => 'PENTEST', 'ten_chuyen_nganh' => 'Kiểm thử xâm nhập', 'nganh_id' => $nganhATTT, 'tong_tin_chi_toi_thieu' => 132, 'mo_ta' => 'Ethical hacking và pentesting'],

            // Chuyên ngành Quản trị kinh doanh
            ['ma_chuyen_nganh' => 'MARKETING', 'ten_chuyen_nganh' => 'Marketing', 'nganh_id' => $nganhQTKD, 'tong_tin_chi_toi_thieu' => 120, 'mo_ta' => 'Quản trị marketing và thương hiệu'],
            ['ma_chuyen_nganh' => 'HR', 'ten_chuyen_nganh' => 'Quản trị nhân sự', 'nganh_id' => $nganhQTKD, 'tong_tin_chi_toi_thieu' => 120, 'mo_ta' => 'Quản lý và phát triển nguồn nhân lực'],
            ['ma_chuyen_nganh' => 'LOGISTICS', 'ten_chuyen_nganh' => 'Logistics và Chuỗi cung ứng', 'nganh_id' => $nganhQTKD, 'tong_tin_chi_toi_thieu' => 120, 'mo_ta' => 'Quản lý chuỗi cung ứng'],

            // Chuyên ngành Kế toán
            ['ma_chuyen_nganh' => 'AUDIT', 'ten_chuyen_nganh' => 'Kiểm toán', 'nganh_id' => $nganhKeToan, 'tong_tin_chi_toi_thieu' => 125, 'mo_ta' => 'Kiểm toán độc lập và nội bộ'],
            ['ma_chuyen_nganh' => 'TAX', 'ten_chuyen_nganh' => 'Kế toán thuế', 'nganh_id' => $nganhKeToan, 'tong_tin_chi_toi_thieu' => 125, 'mo_ta' => 'Kế toán và tư vấn thuế'],

            // Chuyên ngành Ngôn ngữ Anh
            ['ma_chuyen_nganh' => 'TRANSLATION', 'ten_chuyen_nganh' => 'Biên - Phiên dịch', 'nganh_id' => $nganhNgonNguAnh, 'tong_tin_chi_toi_thieu' => 120, 'mo_ta' => 'Đào tạo phiên dịch viên chuyên nghiệp'],
            ['ma_chuyen_nganh' => 'TESOL', 'ten_chuyen_nganh' => 'Sư phạm tiếng Anh', 'nganh_id' => $nganhNgonNguAnh, 'tong_tin_chi_toi_thieu' => 120, 'mo_ta' => 'Đào tạo giáo viên tiếng Anh'],
        ];

        foreach ($data as $item) {
            DB::table('chuyen_nganh')->updateOrInsert(
                ['ma_chuyen_nganh' => $item['ma_chuyen_nganh']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
