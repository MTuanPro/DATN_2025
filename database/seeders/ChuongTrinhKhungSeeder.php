<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DaoTao\MonHoc;

class ChuongTrinhKhungSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // LẤY TẤT CẢ CHUYÊN NGÀNH
        // ========================================
        $chuyenNganhs = [
            // CNTT
            'CNPM' => ChuyenNganh::where('ma_chuyen_nganh', 'CNPM')->first(),
            'PTUDDD' => ChuyenNganh::where('ma_chuyen_nganh', 'PTUDDD')->first(),
            'AI-ML' => ChuyenNganh::where('ma_chuyen_nganh', 'AI-ML')->first(),
            'DS' => ChuyenNganh::where('ma_chuyen_nganh', 'DS')->first(),
            'ATTT-BM' => ChuyenNganh::where('ma_chuyen_nganh', 'ATTT-BM')->first(),
            'ATTT-MA' => ChuyenNganh::where('ma_chuyen_nganh', 'ATTT-MA')->first(),
            
            // Kinh tế
            'QTKD-DN' => ChuyenNganh::where('ma_chuyen_nganh', 'QTKD-DN')->first(),
            'QTKD-MKT' => ChuyenNganh::where('ma_chuyen_nganh', 'QTKD-MKT')->first(),
            'TCNH-TC' => ChuyenNganh::where('ma_chuyen_nganh', 'TCNH-TC')->first(),
            'TCNH-NH' => ChuyenNganh::where('ma_chuyen_nganh', 'TCNH-NH')->first(),
            'KT-KTDN' => ChuyenNganh::where('ma_chuyen_nganh', 'KT-KTDN')->first(),
            'KT-KTA' => ChuyenNganh::where('ma_chuyen_nganh', 'KT-KTA')->first(),
            
            // Ngoại ngữ
            'TA-SPNN' => ChuyenNganh::where('ma_chuyen_nganh', 'TA-SPNN')->first(),
            'TA-BDPD' => ChuyenNganh::where('ma_chuyen_nganh', 'TA-BDPD')->first(),
            'TN-BDPD' => ChuyenNganh::where('ma_chuyen_nganh', 'TN-BDPD')->first(),
            'TN-QHQT' => ChuyenNganh::where('ma_chuyen_nganh', 'TN-QHQT')->first(),
            'TT-BDPD' => ChuyenNganh::where('ma_chuyen_nganh', 'TT-BDPD')->first(),
            'TT-TMQT' => ChuyenNganh::where('ma_chuyen_nganh', 'TT-TMQT')->first(),
        ];

        // ========================================
        // KHOA CNTT - Template chung
        // ========================================
        $monChungCNTT = [
            // Đại cương (HK1-2)
            ['ma_mon' => 'DC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC04', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC06', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC07', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'CNTT01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            ['ma_mon' => 'DC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC05', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC08', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'CNTT02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            ['ma_mon' => 'CNTT03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            // Cơ sở ngành (HK3)
            ['ma_mon' => 'DC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'CNTT04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            ['ma_mon' => 'CNTT05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            // Thực tập & Đồ án (HK7-8)
            ['ma_mon' => 'CNTT17', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true],
            ['ma_mon' => 'CNTT18', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true],
        ];

        // Công nghệ phần mềm
        if ($chuyenNganhs['CNPM']) {
            $monChuyenNganh = [
                ['ma_mon' => 'CNTT06', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT07', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['CNPM'], array_merge($monChungCNTT, $monChuyenNganh));
        }

        // Phát triển ứng dụng di động
        if ($chuyenNganhs['PTUDDD']) {
            $monChuyenNganh = [
                ['ma_mon' => 'CNTT06', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT08', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['PTUDDD'], array_merge($monChungCNTT, $monChuyenNganh));
        }

        // Trí tuệ nhân tạo
        if ($chuyenNganhs['AI-ML']) {
            $monChuyenNganh = [
                ['ma_mon' => 'CNTT09', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT10', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['AI-ML'], array_merge($monChungCNTT, $monChuyenNganh));
        }

        // Khoa học dữ liệu
        if ($chuyenNganhs['DS']) {
            $monChuyenNganh = [
                ['ma_mon' => 'CNTT09', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT11', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['DS'], array_merge($monChungCNTT, $monChuyenNganh));
        }

        // Bảo mật hệ thống
        if ($chuyenNganhs['ATTT-BM']) {
            $monChuyenNganh = [
                ['ma_mon' => 'CNTT12', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT13', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'CNTT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['ATTT-BM'], array_merge($monChungCNTT, $monChuyenNganh));
        }

        // Mật mã học
        if ($chuyenNganhs['ATTT-MA']) {
            $monChuyenNganh = [
                ['ma_mon' => 'CNTT12', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT13', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'CNTT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'CNTT15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['ATTT-MA'], array_merge($monChungCNTT, $monChuyenNganh));
        }

        // ========================================
        // KHOA KINH TẾ - Template chung
        // ========================================
        $monChungKT = [
            // Đại cương (HK1-2)
            ['ma_mon' => 'DC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC04', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC06', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC07', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'KT01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            ['ma_mon' => 'DC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC05', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC08', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'KT02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            ['ma_mon' => 'KT03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            // Cơ sở ngành (HK3)
            ['ma_mon' => 'DC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'KT04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            ['ma_mon' => 'KT05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            // Thực tập & Đồ án (HK7-8)
            ['ma_mon' => 'KT17', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true],
            ['ma_mon' => 'KT18', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true],
        ];

        // Quản trị doanh nghiệp
        if ($chuyenNganhs['QTKD-DN']) {
            $monChuyenNganh = [
                ['ma_mon' => 'KT06', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT07', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['QTKD-DN'], array_merge($monChungKT, $monChuyenNganh));
        }

        // Quản trị Marketing
        if ($chuyenNganhs['QTKD-MKT']) {
            $monChuyenNganh = [
                ['ma_mon' => 'KT08', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['QTKD-MKT'], array_merge($monChungKT, $monChuyenNganh));
        }

        // Tài chính doanh nghiệp
        if ($chuyenNganhs['TCNH-TC']) {
            $monChuyenNganh = [
                ['ma_mon' => 'KT10', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT11', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TCNH-TC'], array_merge($monChungKT, $monChuyenNganh));
        }

        // Ngân hàng
        if ($chuyenNganhs['TCNH-NH']) {
            $monChuyenNganh = [
                ['ma_mon' => 'KT10', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT12', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT13', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TCNH-NH'], array_merge($monChungKT, $monChuyenNganh));
        }

        // Kế toán doanh nghiệp
        if ($chuyenNganhs['KT-KTDN']) {
            $monChuyenNganh = [
                ['ma_mon' => 'KT14', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT10', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['KT-KTDN'], array_merge($monChungKT, $monChuyenNganh));
        }

        // Kiểm toán
        if ($chuyenNganhs['KT-KTA']) {
            $monChuyenNganh = [
                ['ma_mon' => 'KT14', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT15', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'KT16', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['KT-KTA'], array_merge($monChungKT, $monChuyenNganh));
        }

        // ========================================
        // KHOA NGOẠI NGỮ - Template chung
        // ========================================
        $monChungNN = [
            // Đại cương (HK1-2)
            ['ma_mon' => 'DC01', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC04', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC07', 'hoc_ky' => 1, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'NN01', 'hoc_ky' => 1, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            ['ma_mon' => 'DC02', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC05', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'DC08', 'hoc_ky' => 2, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'NN02', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            ['ma_mon' => 'NN03', 'hoc_ky' => 2, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            // Cơ sở ngành (HK3)
            ['ma_mon' => 'DC03', 'hoc_ky' => 3, 'loai' => 'dai_cuong', 'bat_buoc' => true],
            ['ma_mon' => 'NN04', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            ['ma_mon' => 'NN05', 'hoc_ky' => 3, 'loai' => 'co_so_nganh', 'bat_buoc' => true],
            
            // Thực tập & Đồ án (HK7-8)
            ['ma_mon' => 'NN16', 'hoc_ky' => 7, 'loai' => 'thuc_tap', 'bat_buoc' => true],
            ['ma_mon' => 'NN17', 'hoc_ky' => 8, 'loai' => 'do_an_tot_nghiep', 'bat_buoc' => true],
        ];

        // Sư phạm tiếng Anh
        if ($chuyenNganhs['TA-SPNN']) {
            $monChuyenNganh = [
                ['ma_mon' => 'NN06', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN07', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'NN15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TA-SPNN'], array_merge($monChungNN, $monChuyenNganh));
        }

        // Biên - Phiên dịch tiếng Anh
        if ($chuyenNganhs['TA-BDPD']) {
            $monChuyenNganh = [
                ['ma_mon' => 'NN08', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'NN15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TA-BDPD'], array_merge($monChungNN, $monChuyenNganh));
        }

        // Biên - Phiên dịch tiếng Nhật
        if ($chuyenNganhs['TN-BDPD']) {
            $monChuyenNganh = [
                ['ma_mon' => 'NN08', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'NN15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TN-BDPD'], array_merge($monChungNN, $monChuyenNganh));
        }

        // Quan hệ quốc tế Nhật Bản
        if ($chuyenNganhs['TN-QHQT']) {
            $monChuyenNganh = [
                ['ma_mon' => 'NN10', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN11', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'NN15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TN-QHQT'], array_merge($monChungNN, $monChuyenNganh));
        }

        // Biên - Phiên dịch tiếng Trung
        if ($chuyenNganhs['TT-BDPD']) {
            $monChuyenNganh = [
                ['ma_mon' => 'NN08', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN09', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'NN15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TT-BDPD'], array_merge($monChungNN, $monChuyenNganh));
        }

        // Thương mại Trung Quốc
        if ($chuyenNganhs['TT-TMQT']) {
            $monChuyenNganh = [
                ['ma_mon' => 'NN12', 'hoc_ky' => 4, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN13', 'hoc_ky' => 5, 'loai' => 'chuyen_nganh_bat_buoc', 'bat_buoc' => true],
                ['ma_mon' => 'NN14', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
                ['ma_mon' => 'NN15', 'hoc_ky' => 6, 'loai' => 'chuyen_nganh_tu_chon', 'bat_buoc' => false],
            ];
            $this->taoChuongTrinhKhung($chuyenNganhs['TT-TMQT'], array_merge($monChungNN, $monChuyenNganh));
        }
    }

    private function taoChuongTrinhKhung($chuyenNganh, $danhSachMon)
    {
        if (!$chuyenNganh) return;

        $thuTu = 1;
        foreach ($danhSachMon as $mon) {
            $monHoc = MonHoc::where('ma_mon', $mon['ma_mon'])->first();
                if ($monHoc) {
                    ChuongTrinhKhung::updateOrInsert(
                        [
                        'chuyen_nganh_id' => $chuyenNganh->id,
                            'mon_hoc_id' => $monHoc->id,
                        ],
                        [
                        'hoc_ky_goi_y' => $mon['hoc_ky'],
                        'loai_mon_hoc' => $mon['loai'],
                        'bat_buoc' => $mon['bat_buoc'],
                        'thu_tu_hoc' => $thuTu++,
                        'so_tin_chi_toi_thieu' => $mon['bat_buoc'] ? null : 3,
                        'ghi_chu' => $mon['bat_buoc'] ? null : 'Sinh viên chọn ít nhất 1 môn trong nhóm',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        $this->command->info('✅ Đã tạo chương trình khung cho: ' . $chuyenNganh->ten_chuyen_nganh);
    }
}
