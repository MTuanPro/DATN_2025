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
        $nganhKHMT = DB::table('nganh')->where('ma_nganh', '7480202')->value('id');
        $nganhATTT = DB::table('nganh')->where('ma_nganh', '7480299')->value('id');
        $nganhQLKD = DB::table('nganh')->where('ma_nganh', '7340101')->value('id');
        $nganhTCNH = DB::table('nganh')->where('ma_nganh', '7340201')->value('id');
        $nganhKeToan = DB::table('nganh')->where('ma_nganh', '7340301')->value('id');
        $nganhTiengAnh = DB::table('nganh')->where('ma_nganh', '7220201')->value('id');
        $nganhTiengNhat = DB::table('nganh')->where('ma_nganh', '7220203')->value('id');
        $nganhTiengTrung = DB::table('nganh')->where('ma_nganh', '7220204')->value('id');

        $data = [
            // KHOA CNTT - Ngành 1: Công nghệ thông tin (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'CNPM',
                'ten_chuyen_nganh' => 'Công nghệ phần mềm',
                'nganh_id' => $nganhCNTT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành đào tạo kỹ sư phát triển phần mềm chuyên nghiệp',
            ],
            [
                'ma_chuyen_nganh' => 'PTUDDD',
                'ten_chuyen_nganh' => 'Phát triển ứng dụng di động',
                'nganh_id' => $nganhCNTT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành lập trình ứng dụng trên iOS, Android, Cross-platform',
            ],

            // KHOA CNTT - Ngành 2: Khoa học máy tính (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'AI-ML',
                'ten_chuyen_nganh' => 'Trí tuệ nhân tạo',
                'nganh_id' => $nganhKHMT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành AI, Machine Learning, Deep Learning, Computer Vision',
            ],
            [
                'ma_chuyen_nganh' => 'DS',
                'ten_chuyen_nganh' => 'Khoa học dữ liệu',
                'nganh_id' => $nganhKHMT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành phân tích dữ liệu, Big Data, Data Mining',
            ],

            // KHOA CNTT - Ngành 3: An toàn thông tin (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'ATTT-BM',
                'ten_chuyen_nganh' => 'Bảo mật hệ thống',
                'nganh_id' => $nganhATTT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành bảo mật mạng, an ninh mạng, phòng chống tấn công',
            ],
            [
                'ma_chuyen_nganh' => 'ATTT-MA',
                'ten_chuyen_nganh' => 'Mật mã học',
                'nganh_id' => $nganhATTT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành nghiên cứu và ứng dụng mật mã, blockchain',
            ],

            // KHOA KINH TẾ - Ngành 1: Quản trị kinh doanh (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'QTKD-DN',
                'ten_chuyen_nganh' => 'Quản trị doanh nghiệp',
                'nganh_id' => $nganhQLKD,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành quản lý vận hành, chiến lược kinh doanh doanh nghiệp',
            ],
            [
                'ma_chuyen_nganh' => 'QTKD-MKT',
                'ten_chuyen_nganh' => 'Quản trị Marketing',
                'nganh_id' => $nganhQLKD,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành marketing, quảng cáo, nghiên cứu thị trường',
            ],

            // KHOA KINH TẾ - Ngành 2: Tài chính - Ngân hàng (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'TCNH-TC',
                'ten_chuyen_nganh' => 'Tài chính doanh nghiệp',
                'nganh_id' => $nganhTCNH,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành quản lý tài chính, đầu tư, phân tích tài chính',
            ],
            [
                'ma_chuyen_nganh' => 'TCNH-NH',
                'ten_chuyen_nganh' => 'Ngân hàng',
                'nganh_id' => $nganhTCNH,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành nghiệp vụ ngân hàng, tín dụng, thanh toán quốc tế',
            ],

            // KHOA KINH TẾ - Ngành 3: Kế toán (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'KT-KTDN',
                'ten_chuyen_nganh' => 'Kế toán doanh nghiệp',
                'nganh_id' => $nganhKeToan,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành kế toán tài chính, kế toán quản trị doanh nghiệp',
            ],
            [
                'ma_chuyen_nganh' => 'KT-KTA',
                'ten_chuyen_nganh' => 'Kiểm toán',
                'nganh_id' => $nganhKeToan,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành kiểm toán độc lập, kiểm toán nội bộ',
            ],

            // KHOA NGOẠI NGỮ - Ngành 1: Ngôn ngữ Anh (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'TA-SPNN',
                'ten_chuyen_nganh' => 'Sư phạm tiếng Anh',
                'nganh_id' => $nganhTiengAnh,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành đào tạo giáo viên tiếng Anh',
            ],
            [
                'ma_chuyen_nganh' => 'TA-BDPD',
                'ten_chuyen_nganh' => 'Biên - Phiên dịch tiếng Anh',
                'nganh_id' => $nganhTiengAnh,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành biên dịch, phiên dịch tiếng Anh chuyên nghiệp',
            ],

            // KHOA NGOẠI NGỮ - Ngành 2: Ngôn ngữ Nhật (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'TN-BDPD',
                'ten_chuyen_nganh' => 'Biên - Phiên dịch tiếng Nhật',
                'nganh_id' => $nganhTiengNhat,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành biên dịch, phiên dịch tiếng Nhật',
            ],
            [
                'ma_chuyen_nganh' => 'TN-QHQT',
                'ten_chuyen_nganh' => 'Quan hệ quốc tế Nhật Bản',
                'nganh_id' => $nganhTiengNhat,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành về văn hóa, kinh tế, quan hệ Việt - Nhật',
            ],

            // KHOA NGOẠI NGỮ - Ngành 3: Ngôn ngữ Trung Quốc (2 chuyên ngành)
            [
                'ma_chuyen_nganh' => 'TT-BDPD',
                'ten_chuyen_nganh' => 'Biên - Phiên dịch tiếng Trung',
                'nganh_id' => $nganhTiengTrung,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành biên dịch, phiên dịch tiếng Trung',
            ],
            [
                'ma_chuyen_nganh' => 'TT-TMQT',
                'ten_chuyen_nganh' => 'Thương mại Trung Quốc',
                'nganh_id' => $nganhTiengTrung,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành thương mại, kinh doanh với Trung Quốc',
            ],
        ];

        foreach ($data as $item) {
            if ($item['nganh_id']) {
                DB::table('chuyen_nganh')->updateOrInsert(
                    ['ma_chuyen_nganh' => $item['ma_chuyen_nganh']],
                    array_merge($item, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        }

        $this->command->info('✅ Đã tạo ' . count($data) . ' chuyên ngành (9 ngành x 2 chuyên ngành)');
    }
}
