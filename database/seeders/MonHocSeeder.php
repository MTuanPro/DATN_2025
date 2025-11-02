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
        $khoaSP = DB::table('khoa')->where('ma_khoa', 'SP')->value('id'); // Môn đại cương quản lý bởi khoa Sư phạm

        $data = [
            // Môn đại cương (chung cho tất cả ngành) - Giao cho Khoa Sư phạm quản lý
            ['ma_mon' => 'MLDC01', 'ten_mon' => 'Triết học Mác - Lênin', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'MLDC02', 'ten_mon' => 'Kinh tế chính trị Mác - Lênin', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'MLDC03', 'ten_mon' => 'Chủ nghĩa xã hội khoa học', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'MLDC04', 'ten_mon' => 'Lịch sử Đảng Cộng sản Việt Nam', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'MLDC05', 'ten_mon' => 'Tư tưởng Hồ Chí Minh', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'TA01', 'ten_mon' => 'Tiếng Anh 1', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'TA02', 'ten_mon' => 'Tiếng Anh 2', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'TA03', 'ten_mon' => 'Tiếng Anh 3', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaNN, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'GDTC01', 'ten_mon' => 'Giáo dục thể chất 1', 'so_tin_chi' => 1, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 15, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'GDTC02', 'ten_mon' => 'Giáo dục thể chất 2', 'so_tin_chi' => 1, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 15, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'GDQP01', 'ten_mon' => 'Giáo dục quốc phòng - An ninh', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'dai_cuong', 'khoa_id' => $khoaSP, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 20],

            // Môn cơ sở ngành CNTT
            ['ma_mon' => 'CNTT01', 'ten_mon' => 'Nhập môn lập trình', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT02', 'ten_mon' => 'Cấu trúc dữ liệu và giải thuật', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT03', 'ten_mon' => 'Lập trình hướng đối tượng', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT04', 'ten_mon' => 'Cơ sở dữ liệu', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT05', 'ten_mon' => 'Mạng máy tính', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT06', 'ten_mon' => 'Hệ điều hành', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // Môn chuyên ngành bắt buộc CNTT
            ['ma_mon' => 'CNTT07', 'ten_mon' => 'Công nghệ phần mềm', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT08', 'ten_mon' => 'Phát triển ứng dụng Web', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'hybrid', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT09', 'ten_mon' => 'Lập trình di động', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'hybrid', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT10', 'ten_mon' => 'Trí tuệ nhân tạo', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT11', 'ten_mon' => 'An toàn và bảo mật thông tin', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // Môn chuyên ngành tự chọn CNTT
            ['ma_mon' => 'CNTT12', 'ten_mon' => 'Học máy (Machine Learning)', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'hybrid', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT13', 'ten_mon' => 'Xử lý ảnh số', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT14', 'ten_mon' => 'Lập trình Game', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT15', 'ten_mon' => 'Blockchain và ứng dụng', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'CNTT16', 'ten_mon' => 'Cloud Computing', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'online', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // Thực tập và đồ án CNTT
            ['ma_mon' => 'CNTT17', 'ten_mon' => 'Thực tập cơ sở', 'so_tin_chi' => 2, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 2, 'loai_mon' => 'thuc_tap', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 30, 'so_buoi_hoc' => 10],
            ['ma_mon' => 'CNTT18', 'ten_mon' => 'Thực tập chuyên ngành', 'so_tin_chi' => 4, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 4, 'loai_mon' => 'thuc_tap', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 60, 'so_buoi_hoc' => 20],
            ['ma_mon' => 'CNTT19', 'ten_mon' => 'Đồ án tốt nghiệp', 'so_tin_chi' => 10, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 10, 'loai_mon' => 'do_an_tot_nghiep', 'khoa_id' => $khoaCNTT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 150, 'so_buoi_hoc' => 50],

            // Môn cơ sở ngành Kinh tế
            ['ma_mon' => 'KT01', 'ten_mon' => 'Kinh tế vi mô', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT02', 'ten_mon' => 'Kinh tế vĩ mô', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT03', 'ten_mon' => 'Quản trị học', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT04', 'ten_mon' => 'Kế toán tài chính', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT05', 'ten_mon' => 'Thống kê kinh doanh', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT06', 'ten_mon' => 'Marketing căn bản', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'co_so_nganh', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // Môn chuyên ngành Kinh tế
            ['ma_mon' => 'KT07', 'ten_mon' => 'Quản trị nguồn nhân lực', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT08', 'ten_mon' => 'Quản trị tài chính doanh nghiệp', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 3, 'so_tin_chi_thuc_hanh' => 0, 'loai_mon' => 'chuyen_nganh_bat_buoc', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT09', 'ten_mon' => 'Marketing số', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'hybrid', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],
            ['ma_mon' => 'KT10', 'ten_mon' => 'Thương mại điện tử', 'so_tin_chi' => 3, 'so_tin_chi_ly_thuyet' => 2, 'so_tin_chi_thuc_hanh' => 1, 'loai_mon' => 'chuyen_nganh_tu_chon', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'hybrid', 'thoi_luong_hoc' => 45, 'so_buoi_hoc' => 15],

            // Thực tập Kinh tế
            ['ma_mon' => 'KT11', 'ten_mon' => 'Thực tập tốt nghiệp', 'so_tin_chi' => 6, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 6, 'loai_mon' => 'thuc_tap', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 90, 'so_buoi_hoc' => 30],
            ['ma_mon' => 'KT12', 'ten_mon' => 'Khóa luận tốt nghiệp', 'so_tin_chi' => 10, 'so_tin_chi_ly_thuyet' => 0, 'so_tin_chi_thuc_hanh' => 10, 'loai_mon' => 'do_an_tot_nghiep', 'khoa_id' => $khoaKT, 'hinh_thuc_day' => 'offline', 'thoi_luong_hoc' => 150, 'so_buoi_hoc' => 50],
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
    }
}
