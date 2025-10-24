<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DaoTao\MonHoc;

class ChuongTrinhKhungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy chuyên ngành đầu tiên (giả sử đã có data)
        $chuyenNganh = ChuyenNganh::first();

        if (!$chuyenNganh) {
            $this->command->info('Không có chuyên ngành nào. Vui lòng chạy ChuyenNganhSeeder trước.');
            return;
        }

        // Lấy tất cả môn học
        $monHocs = MonHoc::all();

        if ($monHocs->isEmpty()) {
            $this->command->info('Không có môn học nào. Vui lòng chạy MonHocSeeder trước.');
            return;
        }

        // Phân bổ môn học vào các học kỳ
        $chuongTrinh = [
            // HỌC KỲ 1 - Các môn đại cương
            [
                'ma_mon' => 'IT101',
                'hoc_ky' => 1,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 1
            ],
            [
                'ma_mon' => 'IT102',
                'hoc_ky' => 1,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 2
            ],
            [
                'ma_mon' => 'IT103',
                'hoc_ky' => 1,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 3
            ],
            [
                'ma_mon' => 'IT107',
                'hoc_ky' => 1,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 4
            ],
            [
                'ma_mon' => 'IT109',
                'hoc_ky' => 1,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 5
            ],

            // HỌC KỲ 2
            [
                'ma_mon' => 'IT104',
                'hoc_ky' => 2,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 1
            ],
            [
                'ma_mon' => 'IT108',
                'hoc_ky' => 2,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 2
            ],
            [
                'ma_mon' => 'IT110',
                'hoc_ky' => 2,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 3
            ],
            [
                'ma_mon' => 'IT201',
                'hoc_ky' => 2,
                'loai' => 'co_so_nganh',
                'bat_buoc' => true,
                'thu_tu' => 4
            ],

            // HỌC KỲ 3
            [
                'ma_mon' => 'IT105',
                'hoc_ky' => 3,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 1
            ],
            [
                'ma_mon' => 'IT106',
                'hoc_ky' => 3,
                'loai' => 'dai_cuong',
                'bat_buoc' => true,
                'thu_tu' => 2
            ],
            [
                'ma_mon' => 'IT202',
                'hoc_ky' => 3,
                'loai' => 'co_so_nganh',
                'bat_buoc' => true,
                'thu_tu' => 3
            ],
            [
                'ma_mon' => 'IT203',
                'hoc_ky' => 3,
                'loai' => 'co_so_nganh',
                'bat_buoc' => true,
                'thu_tu' => 4
            ],

            // HỌC KỲ 4
            [
                'ma_mon' => 'IT204',
                'hoc_ky' => 4,
                'loai' => 'co_so_nganh',
                'bat_buoc' => true,
                'thu_tu' => 1
            ],
            [
                'ma_mon' => 'IT205',
                'hoc_ky' => 4,
                'loai' => 'co_so_nganh',
                'bat_buoc' => true,
                'thu_tu' => 2
            ],
            [
                'ma_mon' => 'IT206',
                'hoc_ky' => 4,
                'loai' => 'co_so_nganh',
                'bat_buoc' => true,
                'thu_tu' => 3
            ],

            // HỌC KỲ 5 - Chuyên ngành bắt buộc
            [
                'ma_mon' => 'IT301',
                'hoc_ky' => 5,
                'loai' => 'chuyen_nganh_bat_buoc',
                'bat_buoc' => true,
                'thu_tu' => 1
            ],
            [
                'ma_mon' => 'IT302',
                'hoc_ky' => 5,
                'loai' => 'chuyen_nganh_bat_buoc',
                'bat_buoc' => true,
                'thu_tu' => 2
            ],
            [
                'ma_mon' => 'IT305',
                'hoc_ky' => 5,
                'loai' => 'chuyen_nganh_bat_buoc',
                'bat_buoc' => true,
                'thu_tu' => 3
            ],

            // HỌC KỲ 6
            [
                'ma_mon' => 'IT303',
                'hoc_ky' => 6,
                'loai' => 'chuyen_nganh_bat_buoc',
                'bat_buoc' => true,
                'thu_tu' => 1
            ],
            [
                'ma_mon' => 'IT304',
                'hoc_ky' => 6,
                'loai' => 'chuyen_nganh_bat_buoc',
                'bat_buoc' => true,
                'thu_tu' => 2
            ],
            [
                'ma_mon' => 'IT401',
                'hoc_ky' => 6,
                'loai' => 'chuyen_nganh_tu_chon',
                'bat_buoc' => false,
                'thu_tu' => 3
            ],
            [
                'ma_mon' => 'IT402',
                'hoc_ky' => 6,
                'loai' => 'chuyen_nganh_tu_chon',
                'bat_buoc' => false,
                'thu_tu' => 4
            ],

            // HỌC KỲ 7
            [
                'ma_mon' => 'IT403',
                'hoc_ky' => 7,
                'loai' => 'chuyen_nganh_tu_chon',
                'bat_buoc' => false,
                'thu_tu' => 1
            ],
            [
                'ma_mon' => 'IT404',
                'hoc_ky' => 7,
                'loai' => 'chuyen_nganh_tu_chon',
                'bat_buoc' => false,
                'thu_tu' => 2
            ],
            [
                'ma_mon' => 'IT405',
                'hoc_ky' => 7,
                'loai' => 'chuyen_nganh_tu_chon',
                'bat_buoc' => false,
                'thu_tu' => 3
            ],
            [
                'ma_mon' => 'IT501',
                'hoc_ky' => 7,
                'loai' => 'thuc_tap',
                'bat_buoc' => true,
                'thu_tu' => 4
            ],

            // HỌC KỲ 8 - Đồ án tốt nghiệp
            [
                'ma_mon' => 'IT502',
                'hoc_ky' => 8,
                'loai' => 'do_an_tot_nghiep',
                'bat_buoc' => true,
                'thu_tu' => 1
            ],
        ];

        foreach ($chuongTrinh as $ct) {
            $monHoc = MonHoc::where('ma_mon', $ct['ma_mon'])->first();

            if ($monHoc) {
                ChuongTrinhKhung::create([
                    'chuyen_nganh_id' => $chuyenNganh->id,
                    'mon_hoc_id' => $monHoc->id,
                    'hoc_ky_goi_y' => $ct['hoc_ky'],
                    'loai_mon_hoc' => $ct['loai'],
                    'bat_buoc' => $ct['bat_buoc'],
                    'thu_tu_hoc' => $ct['thu_tu'],
                    'so_tin_chi_toi_thieu' => $ct['bat_buoc'] ? null : 3,
                    'ghi_chu' => $ct['bat_buoc'] ? null : 'Sinh viên chọn ít nhất 1 môn',
                ]);
            }
        }

        $this->command->info('✅ Đã tạo Chương trình khung cho chuyên ngành: ' . $chuyenNganh->ten_chuyen_nganh);
    }
}
