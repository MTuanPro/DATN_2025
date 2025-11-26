<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonHocSeeder extends Seeder
{
    public function run(): void
    {
        $khoaCNTT = DB::table('khoa')->where('ma_khoa', 'CNTT')->value('id');
        $khoaKT = DB::table('khoa')->where('ma_khoa', 'KT')->value('id');
        $khoaNN = DB::table('khoa')->where('ma_khoa', 'NN')->value('id');

        $data = [
            // ========== MÔN ĐẠI CƯƠNG (10 môn) ==========
            ['ma_mon' => 'DC01', 'ten_mon' => 'Triết học Mác - Lênin', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'DC02', 'ten_mon' => 'Kinh tế chính trị', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'DC03', 'ten_mon' => 'Tư tưởng Hồ Chí Minh', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'DC04', 'ten_mon' => 'Tiếng Anh 1', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'DC05', 'ten_mon' => 'Tiếng Anh 2', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'DC06', 'ten_mon' => 'Toán cao cấp', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'DC07', 'ten_mon' => 'Giáo dục thể chất', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 30],
            ['ma_mon' => 'DC08', 'ten_mon' => 'Giáo dục quốc phòng', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 20],

            // ========== KHOA CNTT - MÔN CƠ SỞ NGÀNH (5 môn) ==========
            ['ma_mon' => 'CNTT01', 'ten_mon' => 'Nhập môn lập trình', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT02', 'ten_mon' => 'Cấu trúc dữ liệu và giải thuật', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT03', 'ten_mon' => 'Lập trình hướng đối tượng', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT04', 'ten_mon' => 'Cơ sở dữ liệu', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT05', 'ten_mon' => 'Mạng máy tính', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // ========== CNTT - CHUYÊN NGÀNH (12 môn) ==========
            ['ma_mon' => 'CNTT06', 'ten_mon' => 'Công nghệ phần mềm', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT07', 'ten_mon' => 'Phát triển ứng dụng Web', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT08', 'ten_mon' => 'Lập trình di động (Mobile)', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT09', 'ten_mon' => 'Trí tuệ nhân tạo (AI)', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT10', 'ten_mon' => 'Machine Learning', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT11', 'ten_mon' => 'Khoa học dữ liệu (Data Science)', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT12', 'ten_mon' => 'An toàn bảo mật hệ thống', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT13', 'ten_mon' => 'Mật mã học', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT14', 'ten_mon' => 'IoT và ứng dụng', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT15', 'ten_mon' => 'Cloud Computing', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'online', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT16', 'ten_mon' => 'Blockchain', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT17', 'ten_mon' => 'Thực tập chuyên ngành CNTT', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 4, 'loai_mon' => 'thuc_tap', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 20],
            ['ma_mon' => 'CNTT18', 'ten_mon' => 'Đồ án tốt nghiệp CNTT', 'so_tin_chi' => 10, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 10, 'loai_mon' => 'do_an_tot_nghiep', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 150, 'so_buoi_hoc' => 50],

            // ========== KHOA KINH TẾ - MÔN CƠ SỞ NGÀNH (5 môn) ==========
            ['ma_mon' => 'KT01', 'ten_mon' => 'Kinh tế vi mô', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT02', 'ten_mon' => 'Kinh tế vĩ mô', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT03', 'ten_mon' => 'Quản trị học', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT04', 'ten_mon' => 'Kế toán căn bản', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT05', 'ten_mon' => 'Marketing căn bản', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // ========== KINH TẾ - CHUYÊN NGÀNH (12 môn) ==========
            ['ma_mon' => 'KT06', 'ten_mon' => 'Quản trị nguồn nhân lực', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT07', 'ten_mon' => 'Quản trị chiến lược', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT08', 'ten_mon' => 'Marketing số (Digital Marketing)', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'hybrid', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT09', 'ten_mon' => 'Quản lý thương hiệu (Branding)', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT10', 'ten_mon' => 'Tài chính doanh nghiệp', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT11', 'ten_mon' => 'Phân tích đầu tư', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT12', 'ten_mon' => 'Ngân hàng thương mại', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT13', 'ten_mon' => 'Thanh toán quốc tế', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT14', 'ten_mon' => 'Kế toán tài chính', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT15', 'ten_mon' => 'Kiểm toán', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT16', 'ten_mon' => 'Thương mại điện tử', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'hybrid', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT17', 'ten_mon' => 'Thực tập chuyên ngành KT', 'so_tin_chi' => 6, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 6, 'loai_mon' => 'thuc_tap', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 90, 'so_buoi_hoc' => 30],
            ['ma_mon' => 'KT18', 'ten_mon' => 'Khóa luận tốt nghiệp KT', 'so_tin_chi' => 10, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 10, 'loai_mon' => 'do_an_tot_nghiep', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 150, 'so_buoi_hoc' => 50],

            // ========== KHOA NGOẠI NGỮ - MÔN CƠ SỞ NGÀNH (5 môn) ==========
            ['ma_mon' => 'NN01', 'ten_mon' => 'Ngữ âm - Âm vị học', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN02', 'ten_mon' => 'Ngữ pháp', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN03', 'ten_mon' => 'Kỹ năng nghe - Nói', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 1, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN04', 'ten_mon' => 'Kỹ năng đọc - Viết', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 1, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN05', 'ten_mon' => 'Văn hóa và Giao tiếp', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // ========== NGOẠI NGỮ - CHUYÊN NGÀNH (12 môn) ==========
            ['ma_mon' => 'NN06', 'ten_mon' => 'Phương pháp giảng dạy ngoại ngữ', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN07', 'ten_mon' => 'Tâm lý học giáo dục', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'NN08', 'ten_mon' => 'Kỹ thuật biên dịch', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN09', 'ten_mon' => 'Kỹ thuật phiên dịch', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 1, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN10', 'ten_mon' => 'Văn hóa và Lịch sử Nhật Bản', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN11', 'ten_mon' => 'Kinh tế Nhật Bản', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN12', 'ten_mon' => 'Thương mại Trung Quốc', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN13', 'ten_mon' => 'Văn hóa Trung Quốc', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN14', 'ten_mon' => 'Tiếng Anh chuyên ngành', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN15', 'ten_mon' => 'Văn học nước ngoài', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'NN16', 'ten_mon' => 'Thực tập sư phạm/Phiên dịch', 'so_tin_chi' => 6, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 6, 'loai_mon' => 'thuc_tap', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 90, 'so_buoi_hoc' => 30],
            ['ma_mon' => 'NN17', 'ten_mon' => 'Khóa luận tốt nghiệp NN', 'so_tin_chi' => 10, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 10, 'loai_mon' => 'do_an_tot_nghiep', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 150, 'so_buoi_hoc' => 50],
        ];

        foreach ($data as $item) {
            DB::table('mon_hoc')->updateOrInsert(
                ['ma_mon' => $item['ma_mon']],
                array_merge($item, [
                    'mo_ta' => $item['ten_mon'] . ' - Môn học thuộc khối ' . $item['loai_mon'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Đã tạo ' . count($data) . ' môn học');

        // ========================================
        // TẠO MÔN TIÊN QUYẾT
        // ========================================
        $this->taoMonTienQuyet();
    }

    private function taoMonTienQuyet()
    {
        // Lấy ID các môn học
        $getMonId = function($maMon) {
            return DB::table('mon_hoc')->where('ma_mon', $maMon)->value('id');
        };

        $monTienQuyet = [
            // ========== KHOA CNTT ==========
            // Môn cơ sở ngành
            ['mon_hoc' => 'CNTT02', 'tien_quyet' => 'CNTT01', 'loai' => 'bat_buoc'], // Cấu trúc DL cần Nhập môn lập trình
            ['mon_hoc' => 'CNTT03', 'tien_quyet' => 'CNTT01', 'loai' => 'bat_buoc'], // OOP cần Nhập môn lập trình
            ['mon_hoc' => 'CNTT04', 'tien_quyet' => 'CNTT02', 'loai' => 'bat_buoc'], // CSDL cần Cấu trúc DL
            ['mon_hoc' => 'CNTT05', 'tien_quyet' => 'CNTT01', 'loai' => 'bat_buoc'], // Mạng cần Nhập môn

            // Môn chuyên ngành CNTT
            ['mon_hoc' => 'CNTT06', 'tien_quyet' => 'CNTT03', 'loai' => 'bat_buoc'], // Công nghệ PM cần OOP
            ['mon_hoc' => 'CNTT07', 'tien_quyet' => 'CNTT04', 'loai' => 'bat_buoc'], // Web cần CSDL
            ['mon_hoc' => 'CNTT07', 'tien_quyet' => 'CNTT03', 'loai' => 'bat_buoc'], // Web cần OOP
            ['mon_hoc' => 'CNTT08', 'tien_quyet' => 'CNTT03', 'loai' => 'bat_buoc'], // Mobile cần OOP
            ['mon_hoc' => 'CNTT09', 'tien_quyet' => 'CNTT02', 'loai' => 'bat_buoc'], // AI cần Cấu trúc DL
            ['mon_hoc' => 'CNTT10', 'tien_quyet' => 'CNTT09', 'loai' => 'bat_buoc'], // ML cần AI
            ['mon_hoc' => 'CNTT11', 'tien_quyet' => 'CNTT02', 'loai' => 'bat_buoc'], // Data Science cần Cấu trúc DL
            ['mon_hoc' => 'CNTT12', 'tien_quyet' => 'CNTT05', 'loai' => 'bat_buoc'], // An toàn cần Mạng
            ['mon_hoc' => 'CNTT13', 'tien_quyet' => 'CNTT12', 'loai' => 'bat_buoc'], // Mật mã cần An toàn
            ['mon_hoc' => 'CNTT14', 'tien_quyet' => 'CNTT05', 'loai' => 'khuyen_khich'], // IoT cần Mạng
            ['mon_hoc' => 'CNTT15', 'tien_quyet' => 'CNTT05', 'loai' => 'khuyen_khich'], // Cloud cần Mạng
            ['mon_hoc' => 'CNTT16', 'tien_quyet' => 'CNTT13', 'loai' => 'khuyen_khich'], // Blockchain cần Mật mã

            // Thực tập và Đồ án CNTT - Chỉ yêu cầu môn cơ bản
            ['mon_hoc' => 'CNTT17', 'tien_quyet' => 'CNTT03', 'loai' => 'bat_buoc'], // Thực tập cần OOP
            ['mon_hoc' => 'CNTT17', 'tien_quyet' => 'CNTT04', 'loai' => 'bat_buoc'], // Thực tập cần CSDL
            ['mon_hoc' => 'CNTT18', 'tien_quyet' => 'CNTT03', 'loai' => 'bat_buoc'], // Đồ án cần OOP
            ['mon_hoc' => 'CNTT18', 'tien_quyet' => 'CNTT04', 'loai' => 'bat_buoc'], // Đồ án cần CSDL
            ['mon_hoc' => 'CNTT18', 'tien_quyet' => 'CNTT17', 'loai' => 'bat_buoc'], // Đồ án cần Thực tập

            // ========== KHOA KINH TẾ ==========
            // Môn cơ sở ngành
            ['mon_hoc' => 'KT02', 'tien_quyet' => 'KT01', 'loai' => 'bat_buoc'], // Vĩ mô cần Vi mô
            ['mon_hoc' => 'KT03', 'tien_quyet' => 'KT01', 'loai' => 'bat_buoc'], // Quản trị cần Vi mô
            ['mon_hoc' => 'KT04', 'tien_quyet' => 'KT01', 'loai' => 'khuyen_khich'], // Kế toán cần Vi mô
            ['mon_hoc' => 'KT05', 'tien_quyet' => 'KT03', 'loai' => 'bat_buoc'], // Marketing cần Quản trị

            // Môn chuyên ngành Kinh tế
            ['mon_hoc' => 'KT06', 'tien_quyet' => 'KT03', 'loai' => 'bat_buoc'], // Quản trị nhân lực cần Quản trị
            ['mon_hoc' => 'KT07', 'tien_quyet' => 'KT03', 'loai' => 'bat_buoc'], // Chiến lược cần Quản trị
            ['mon_hoc' => 'KT08', 'tien_quyet' => 'KT05', 'loai' => 'bat_buoc'], // Marketing số cần Marketing CB
            ['mon_hoc' => 'KT09', 'tien_quyet' => 'KT05', 'loai' => 'bat_buoc'], // Branding cần Marketing CB
            ['mon_hoc' => 'KT10', 'tien_quyet' => 'KT04', 'loai' => 'bat_buoc'], // Tài chính DN cần Kế toán
            ['mon_hoc' => 'KT11', 'tien_quyet' => 'KT10', 'loai' => 'bat_buoc'], // Phân tích ĐT cần Tài chính
            ['mon_hoc' => 'KT12', 'tien_quyet' => 'KT10', 'loai' => 'bat_buoc'], // Ngân hàng cần Tài chính
            ['mon_hoc' => 'KT13', 'tien_quyet' => 'KT12', 'loai' => 'bat_buoc'], // Thanh toán QT cần Ngân hàng (HK6)
            ['mon_hoc' => 'KT14', 'tien_quyet' => 'KT04', 'loai' => 'bat_buoc'], // KT tài chính cần KT căn bản
            ['mon_hoc' => 'KT15', 'tien_quyet' => 'KT14', 'loai' => 'bat_buoc'], // Kiểm toán cần KT tài chính
            ['mon_hoc' => 'KT16', 'tien_quyet' => 'KT05', 'loai' => 'khuyen_khich'], // TMĐT cần Marketing

            // Thực tập và Đồ án KT - Chỉ yêu cầu môn cơ bản
            ['mon_hoc' => 'KT17', 'tien_quyet' => 'KT03', 'loai' => 'bat_buoc'], // Thực tập cần Quản trị
            ['mon_hoc' => 'KT17', 'tien_quyet' => 'KT04', 'loai' => 'bat_buoc'], // Thực tập cần Kế toán
            ['mon_hoc' => 'KT18', 'tien_quyet' => 'KT17', 'loai' => 'bat_buoc'], // Khóa luận cần Thực tập
            ['mon_hoc' => 'KT18', 'tien_quyet' => 'KT03', 'loai' => 'bat_buoc'], // Khóa luận cần Quản trị
            ['mon_hoc' => 'KT18', 'tien_quyet' => 'KT04', 'loai' => 'bat_buoc'], // Khóa luận cần Kế toán

            // ========== KHOA NGOẠI NGỮ ==========
            // Môn cơ sở ngành
            ['mon_hoc' => 'NN02', 'tien_quyet' => 'NN01', 'loai' => 'bat_buoc'], // Ngữ pháp cần Ngữ âm
            ['mon_hoc' => 'NN03', 'tien_quyet' => 'NN01', 'loai' => 'bat_buoc'], // Nghe-Nói cần Ngữ âm
            ['mon_hoc' => 'NN04', 'tien_quyet' => 'NN02', 'loai' => 'bat_buoc'], // Đọc-Viết cần Ngữ pháp
            ['mon_hoc' => 'NN05', 'tien_quyet' => 'NN03', 'loai' => 'bat_buoc'], // Văn hóa GT cần Nghe-Nói

            // Môn chuyên ngành Ngoại ngữ
            ['mon_hoc' => 'NN06', 'tien_quyet' => 'NN04', 'loai' => 'bat_buoc'], // PP giảng dạy cần Đọc-Viết
            ['mon_hoc' => 'NN07', 'tien_quyet' => 'NN05', 'loai' => 'khuyen_khich'], // Tâm lý GD cần Văn hóa
            ['mon_hoc' => 'NN08', 'tien_quyet' => 'NN04', 'loai' => 'bat_buoc'], // Biên dịch cần Đọc-Viết
            ['mon_hoc' => 'NN09', 'tien_quyet' => 'NN03', 'loai' => 'bat_buoc'], // Phiên dịch cần Nghe-Nói
            ['mon_hoc' => 'NN09', 'tien_quyet' => 'NN08', 'loai' => 'bat_buoc'], // Phiên dịch cần Biên dịch
            ['mon_hoc' => 'NN10', 'tien_quyet' => 'NN05', 'loai' => 'bat_buoc'], // Văn hóa Nhật cần Văn hóa GT
            ['mon_hoc' => 'NN11', 'tien_quyet' => 'NN10', 'loai' => 'bat_buoc'], // Kinh tế Nhật cần Văn hóa Nhật
            ['mon_hoc' => 'NN12', 'tien_quyet' => 'NN05', 'loai' => 'bat_buoc'], // TM Trung Quốc cần Văn hóa
            ['mon_hoc' => 'NN13', 'tien_quyet' => 'NN05', 'loai' => 'bat_buoc'], // Văn hóa TQ cần Văn hóa GT
            ['mon_hoc' => 'NN14', 'tien_quyet' => 'NN04', 'loai' => 'khuyen_khich'], // CN cần Đọc-Viết
            ['mon_hoc' => 'NN15', 'tien_quyet' => 'NN04', 'loai' => 'khuyen_khich'], // Văn học cần Đọc-Viết

            // Thực tập và Đồ án NN
            ['mon_hoc' => 'NN16', 'tien_quyet' => 'NN08', 'loai' => 'bat_buoc'], // Thực tập cần Biên dịch
            ['mon_hoc' => 'NN16', 'tien_quyet' => 'NN09', 'loai' => 'bat_buoc'], // Thực tập cần Phiên dịch
            ['mon_hoc' => 'NN17', 'tien_quyet' => 'NN16', 'loai' => 'bat_buoc'], // Khóa luận cần Thực tập
            ['mon_hoc' => 'NN17', 'tien_quyet' => 'NN08', 'loai' => 'bat_buoc'], // Khóa luận cần Biên dịch
            ['mon_hoc' => 'NN17', 'tien_quyet' => 'NN09', 'loai' => 'bat_buoc'], // Khóa luận cần Phiên dịch
        ];

        $count = 0;
        foreach ($monTienQuyet as $item) {
            $monHocId = $getMonId($item['mon_hoc']);
            $monTienQuyetId = $getMonId($item['tien_quyet']);

            if ($monHocId && $monTienQuyetId) {
                DB::table('mon_hoc_tien_quyet')->updateOrInsert(
                    [
                        'mon_hoc_id' => $monHocId,
                        'mon_tien_quyet_id' => $monTienQuyetId,
                    ],
                    [
                        'loai_tien_quyet' => $item['loai'],
                        'dieu_kien_qua_mon' => $item['loai'] === 'bat_buoc' ? true : false,
                        'ghi_chu' => $item['loai'] === 'bat_buoc' ? 'Phải đạt môn tiên quyết mới được đăng ký' : 'Khuyến khích học môn tiên quyết trước',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $count++;
            }
        }

        $this->command->info('✅ Đã tạo ' . $count . ' mối quan hệ môn tiên quyết');
    }
}
