<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD


use Faker\Factory as Faker;

=======
>>>>>>> origin/main
use Illuminate\Support\Facades\DB;

class ChuyenNganhSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD

        $data = [
            [
                'ma_chuyen_nganh' => 'CNOS',
                'ten_chuyen_nganh' => 'Công nghệ phần mềm',
                'nganh_id' => 4,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Ngành về phát triển phần mềm.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ... các chuyên ngành khác
        ];

        foreach ($data as $item) {
            $exists = DB::table('chuyen_nganh')
                ->where('ma_chuyen_nganh', $item['ma_chuyen_nganh'])
                ->exists();

            if (!$exists) {
                DB::table('chuyen_nganh')->insert($item);
            } else {
                $this->command->warn("⚠️  Mã chuyên ngành {$item['ma_chuyen_nganh']} đã tồn tại, bỏ qua!");

        $faker = Faker::create('vi_VN');
        $nganhIds = DB::table('nganh')->pluck('id');

        foreach ($nganhIds as $nganh_id) {
            for ($i = 0; $i < 2; $i++) {
                DB::table('chuyen_nganh')->insert([
                    'ma_chuyen_nganh' => strtoupper($faker->lexify('CN??')),
                    'ten_chuyen_nganh' => ucfirst($faker->words(3, true)),
                    'nganh_id' => $nganh_id,
                    'tong_tin_chi_toi_thieu' => rand(120, 150),
                    'mo_ta' => $faker->sentence(6),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

=======
        // Lấy ID các ngành
        $nganhCNTT = DB::table('nganh')->where('ma_nganh', '7480201')->value('id');
        $nganhKHMT = DB::table('nganh')->where('ma_nganh', '7480202')->value('id');
        $nganhQLKD = DB::table('nganh')->where('ma_nganh', '7340101')->value('id');
        $nganhTCNH = DB::table('nganh')->where('ma_nganh', '7340201')->value('id');

        $data = [
            // Chuyên ngành CNTT
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
            [
                'ma_chuyen_nganh' => 'HTTT',
                'ten_chuyen_nganh' => 'Hệ thống thông tin',
                'nganh_id' => $nganhCNTT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành quản trị và phát triển hệ thống thông tin doanh nghiệp',
            ],
            [
                'ma_chuyen_nganh' => 'ATTT',
                'ten_chuyen_nganh' => 'An toàn thông tin',
                'nganh_id' => $nganhCNTT,
                'tong_tin_chi_toi_thieu' => 140,
                'mo_ta' => 'Chuyên ngành bảo mật mạng, an ninh mạng, phòng chống tấn công',
            ],

            // Chuyên ngành Khoa học máy tính
            [
                'ma_chuyen_nganh' => 'AI-ML',
                'ten_chuyen_nganh' => 'Trí tuệ nhân tạo và Học máy',
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

            // Chuyên ngành Quản trị kinh doanh
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
            [
                'ma_chuyen_nganh' => 'QTKD-KN',
                'ten_chuyen_nganh' => 'Quản trị khởi nghiệp',
                'nganh_id' => $nganhQLKD,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành khởi nghiệp, quản lý startup, đổi mới sáng tạo',
            ],

            // Chuyên ngành Tài chính - Ngân hàng
            [
                'ma_chuyen_nganh' => 'TC',
                'ten_chuyen_nganh' => 'Tài chính doanh nghiệp',
                'nganh_id' => $nganhTCNH,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành quản lý tài chính, đầu tư, phân tích tài chính',
            ],
            [
                'ma_chuyen_nganh' => 'NH',
                'ten_chuyen_nganh' => 'Ngân hàng',
                'nganh_id' => $nganhTCNH,
                'tong_tin_chi_toi_thieu' => 130,
                'mo_ta' => 'Chuyên ngành nghiệp vụ ngân hàng, tín dụng, thanh toán quốc tế',
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
>>>>>>> origin/main
            }
        }
    }
}
<<<<<<< HEAD
<<<<<<< HEAD
=======

>>>>>>> 3ce5bf463aba81437bc908d45799f550b6b5f94d
=======
>>>>>>> origin/main
