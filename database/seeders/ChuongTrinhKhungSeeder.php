<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DaoTao\MonHoc;
use Illuminate\Support\Facades\DB;

class ChuongTrinhKhungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các chuyên ngành CNTT
        $chuyenNganhCNPM = ChuyenNganh::where('ma_chuyen_nganh', 'CNPM')->first();
        $chuyenNganhPTUDDD = ChuyenNganh::where('ma_chuyen_nganh', 'PTUDDD')->first();
        $chuyenNganhHTTT = ChuyenNganh::where('ma_chuyen_nganh', 'HTTT')->first();
        $chuyenNganhATTT = ChuyenNganh::where('ma_chuyen_nganh', 'ATTT')->first();

        // Chuyên ngành khác
        $chuyenNganhQLKD = ChuyenNganh::where('ma_chuyen_nganh', 'QTKD-DN')->first();

        if (!$chuyenNganhCNPM && !$chuyenNganhQLKD) {
            $this->command->info('Không có chuyên ngành nào. Vui lòng chạy ChuyenNganhSeeder trước.');
            return;
        }

        // ========================================
        // CHƯƠNG TRÌNH KHUNG: CNPM - Công nghệ phần mềm
        // ========================================
        if ($chuyenNganhCNPM) {
            $chuongTrinhCNPM = [
                // HỌC KỲ 1 - Môn đại cương
                ['ma_mon' => 'MLDC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 2
                ['ma_mon' => 'MLDC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],
                ['ma_mon' => 'CNTT03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 5],

                // HỌC KỲ 3
                ['ma_mon' => 'MLDC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 4
                ['ma_mon' => 'MLDC04', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'GDQP01', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT06', 'hoc_ky' => 4, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT07', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 5 - Chuyên ngành bắt buộc
                ['ma_mon' => 'MLDC05', 'hoc_ky' => 5, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT08', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT10', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 6
                ['ma_mon' => 'CNTT11', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT12', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT13', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 4],

                // HỌC KỲ 7
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 7, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT16', 'hoc_ky' => 7, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT17', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT18', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 8 - Đồ án tốt nghiệp
                ['ma_mon' => 'CNTT19', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true, 'thu_tu' => 1],
            ];

            foreach ($chuongTrinhCNPM as $ct) {
                $monHoc = MonHoc::where('ma_mon', $ct['ma_mon'])->first();
                if ($monHoc) {
                    ChuongTrinhKhung::updateOrInsert(
                        [
                            'chuyen_nganh_id' => $chuyenNganhCNPM->id,
                            'mon_hoc_id' => $monHoc->id,
                        ],
                        [
                            'hoc_ky_goi_y' => $ct['hoc_ky'],
                            'loai_mon_hoc' => $ct['loai'],
                            'bat_buoc' => $ct['bat_buoc'],
                            'thu_tu_hoc' => $ct['thu_tu'],
                            'so_tin_chi_toi_thieu' => $ct['bat_buoc'] ? null : 3,
                            'ghi_chu' => $ct['bat_buoc'] ? null : 'Sinh viên chọn ít nhất 1 môn trong nhóm',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
            $this->command->info('✅ Đã tạo chương trình khung cho: ' . $chuyenNganhCNPM->ten_chuyen_nganh);
        }

        // ========================================
        // CHƯƠNG TRÌNH KHUNG: PTUDDD - Phát triển ứng dụng di động
        // ========================================
        if ($chuyenNganhPTUDDD) {
            $chuongTrinhPTUDDD = [
                // HỌC KỲ 1-3: Giống CNPM (môn đại cương và cơ sở ngành)
                ['ma_mon' => 'MLDC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'MLDC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],
                ['ma_mon' => 'CNTT03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 5],

                ['ma_mon' => 'MLDC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 4-8: Môn chuyên ngành di động
                ['ma_mon' => 'MLDC04', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'GDQP01', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT06', 'hoc_ky' => 4, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT07', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'MLDC05', 'hoc_ky' => 5, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT08', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT10', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'CNTT11', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT12', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT13', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 3],

                ['ma_mon' => 'CNTT17', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT18', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 2],

                ['ma_mon' => 'CNTT19', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true, 'thu_tu' => 1],
            ];

            foreach ($chuongTrinhPTUDDD as $ct) {
                $monHoc = MonHoc::where('ma_mon', $ct['ma_mon'])->first();
                if ($monHoc) {
                    ChuongTrinhKhung::updateOrInsert(
                        [
                            'chuyen_nganh_id' => $chuyenNganhPTUDDD->id,
                            'mon_hoc_id' => $monHoc->id,
                        ],
                        [
                            'hoc_ky_goi_y' => $ct['hoc_ky'],
                            'loai_mon_hoc' => $ct['loai'],
                            'bat_buoc' => $ct['bat_buoc'],
                            'thu_tu_hoc' => $ct['thu_tu'],
                            'so_tin_chi_toi_thieu' => $ct['bat_buoc'] ? null : 3,
                            'ghi_chu' => $ct['bat_buoc'] ? null : 'Sinh viên chọn ít nhất 1 môn trong nhóm',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
            $this->command->info('✅ Đã tạo chương trình khung cho: ' . $chuyenNganhPTUDDD->ten_chuyen_nganh);
        }

        // ========================================
        // CHƯƠNG TRÌNH KHUNG: HTTT - Hệ thống thông tin
        // ========================================
        if ($chuyenNganhHTTT) {
            $chuongTrinhHTTT = [
                // HỌC KỲ 1-3: Giống CNPM
                ['ma_mon' => 'MLDC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'MLDC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],
                ['ma_mon' => 'CNTT03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 5],

                ['ma_mon' => 'MLDC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 4-8: Môn chuyên ngành hệ thống thông tin
                ['ma_mon' => 'MLDC04', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'GDQP01', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT06', 'hoc_ky' => 4, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT07', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'MLDC05', 'hoc_ky' => 5, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT08', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT10', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'CNTT11', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 3],

                ['ma_mon' => 'CNTT17', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT18', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 2],

                ['ma_mon' => 'CNTT19', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true, 'thu_tu' => 1],
            ];

            foreach ($chuongTrinhHTTT as $ct) {
                $monHoc = MonHoc::where('ma_mon', $ct['ma_mon'])->first();
                if ($monHoc) {
                    ChuongTrinhKhung::updateOrInsert(
                        [
                            'chuyen_nganh_id' => $chuyenNganhHTTT->id,
                            'mon_hoc_id' => $monHoc->id,
                        ],
                        [
                            'hoc_ky_goi_y' => $ct['hoc_ky'],
                            'loai_mon_hoc' => $ct['loai'],
                            'bat_buoc' => $ct['bat_buoc'],
                            'thu_tu_hoc' => $ct['thu_tu'],
                            'so_tin_chi_toi_thieu' => $ct['bat_buoc'] ? null : 3,
                            'ghi_chu' => $ct['bat_buoc'] ? null : 'Sinh viên chọn ít nhất 1 môn trong nhóm',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
            $this->command->info('✅ Đã tạo chương trình khung cho: ' . $chuyenNganhHTTT->ten_chuyen_nganh);
        }

        // ========================================
        // CHƯƠNG TRÌNH KHUNG: ATTT - An toàn thông tin
        // ========================================
        if ($chuyenNganhATTT) {
            $chuongTrinhATTT = [
                // HỌC KỲ 1-3: Giống CNPM
                ['ma_mon' => 'MLDC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'MLDC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],
                ['ma_mon' => 'CNTT03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 5],

                ['ma_mon' => 'MLDC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 4-8: Môn chuyên ngành an toàn thông tin
                ['ma_mon' => 'MLDC04', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'GDQP01', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT06', 'hoc_ky' => 4, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT07', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'MLDC05', 'hoc_ky' => 5, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT08', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'CNTT10', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                ['ma_mon' => 'CNTT11', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 2],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 3],

                ['ma_mon' => 'CNTT17', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'CNTT18', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 2],

                ['ma_mon' => 'CNTT19', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true, 'thu_tu' => 1],
            ];

            foreach ($chuongTrinhATTT as $ct) {
                $monHoc = MonHoc::where('ma_mon', $ct['ma_mon'])->first();
                if ($monHoc) {
                    ChuongTrinhKhung::updateOrInsert(
                        [
                            'chuyen_nganh_id' => $chuyenNganhATTT->id,
                            'mon_hoc_id' => $monHoc->id,
                        ],
                        [
                            'hoc_ky_goi_y' => $ct['hoc_ky'],
                            'loai_mon_hoc' => $ct['loai'],
                            'bat_buoc' => $ct['bat_buoc'],
                            'thu_tu_hoc' => $ct['thu_tu'],
                            'so_tin_chi_toi_thieu' => $ct['bat_buoc'] ? null : 3,
                            'ghi_chu' => $ct['bat_buoc'] ? null : 'Sinh viên chọn ít nhất 1 môn trong nhóm',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
            $this->command->info('✅ Đã tạo chương trình khung cho: ' . $chuyenNganhATTT->ten_chuyen_nganh);
        }

        // ========================================
        // CHƯƠNG TRÌNH KHUNG: QTKD-DN - Quản trị doanh nghiệp
        // ========================================
        if ($chuyenNganhQLKD) {
            $chuongTrinhQLKD = [
                // HỌC KỲ 1
                ['ma_mon' => 'MLDC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'KT01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 2
                ['ma_mon' => 'MLDC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'GDTC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'KT02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],
                ['ma_mon' => 'KT03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 5],

                // HỌC KỲ 3
                ['ma_mon' => 'MLDC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'TA03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'KT04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'KT05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 4
                ['ma_mon' => 'MLDC04', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'GDQP01', 'hoc_ky' => 4, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'KT06', 'hoc_ky' => 4, 'loai' => 'co_so_nganh', 'bat_buoc' => true, 'thu_tu' => 3],
                ['ma_mon' => 'KT07', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 4],

                // HỌC KỲ 5
                ['ma_mon' => 'MLDC05', 'hoc_ky' => 5, 'loai' => 'dai_cuong', 'bat_buoc' => true, 'thu_tu' => 1],
                ['ma_mon' => 'KT08', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true, 'thu_tu' => 2],
                ['ma_mon' => 'KT09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 3],
                ['ma_mon' => 'KT10', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false, 'thu_tu' => 4],

                // HỌC KỲ 6-7
                ['ma_mon' => 'KT11', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true, 'thu_tu' => 1],

                // HỌC KỲ 8
                ['ma_mon' => 'KT12', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true, 'thu_tu' => 1],
            ];

            foreach ($chuongTrinhQLKD as $ct) {
                $monHoc = MonHoc::where('ma_mon', $ct['ma_mon'])->first();
                if ($monHoc) {
                    ChuongTrinhKhung::updateOrInsert(
                        [
                            'chuyen_nganh_id' => $chuyenNganhQLKD->id,
                            'mon_hoc_id' => $monHoc->id,
                        ],
                        [
                            'hoc_ky_goi_y' => $ct['hoc_ky'],
                            'loai_mon_hoc' => $ct['loai'],
                            'bat_buoc' => $ct['bat_buoc'],
                            'thu_tu_hoc' => $ct['thu_tu'],
                            'so_tin_chi_toi_thieu' => $ct['bat_buoc'] ? null : 3,
                            'ghi_chu' => $ct['bat_buoc'] ? null : 'Sinh viên chọn ít nhất 1 môn trong nhóm',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
            $this->command->info('✅ Đã tạo chương trình khung cho: ' . $chuyenNganhQLKD->ten_chuyen_nganh);
        }
    }
}
