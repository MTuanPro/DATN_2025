<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GiangVienSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy khoa CNTT
        $khoaCNTT = DB::table('khoa')->where('ma_khoa', 'CNTT')->first();
        $khoaKT = DB::table('khoa')->where('ma_khoa', 'KT')->first();
        $khoaNN = DB::table('khoa')->where('ma_khoa', 'NN')->first();

        // Lấy trình độ
        $trinhDos = DB::table('dm_trinh_do')->get()->keyBy('ten_trinh_do');

        $giangViens = [
            [
                'ma_giang_vien' => 'GV001',
                'ho_ten' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@smis.edu.vn',
                'so_dien_thoai' => '0901234567',
                'trinh_do_id' => $trinhDos['Thạc sĩ']->id,
                'chuyen_mon' => 'Lập trình Web, Cơ sở dữ liệu',
                'khoa_id' => $khoaCNTT->id,
                'ngay_vao_truong' => '2015-09-01',
            ],
            [
                'ma_giang_vien' => 'GV002',
                'ho_ten' => 'Trần Thị Bình',
                'email' => 'tranthibinh@smis.edu.vn',
                'so_dien_thoai' => '0902345678',
                'trinh_do_id' => $trinhDos['Tiến sĩ']->id,
                'chuyen_mon' => 'Trí tuệ nhân tạo, Machine Learning',
                'khoa_id' => $khoaCNTT->id,
                'ngay_vao_truong' => '2018-03-15',
            ],
            [
                'ma_giang_vien' => 'GV003',
                'ho_ten' => 'Lê Văn Cường',
                'email' => 'levancuong@smis.edu.vn',
                'so_dien_thoai' => '0903456789',
                'trinh_do_id' => $trinhDos['Thạc sĩ']->id,
                'chuyen_mon' => 'Mạng máy tính, An toàn thông tin',
                'khoa_id' => $khoaCNTT->id,
                'ngay_vao_truong' => '2016-08-20',
            ],
            [
                'ma_giang_vien' => 'GV004',
                'ho_ten' => 'Phạm Thị Dung',
                'email' => 'phamthidung@smis.edu.vn',
                'so_dien_thoai' => '0904567890',
                'trinh_do_id' => $trinhDos['Thạc sĩ']->id,
                'chuyen_mon' => 'Quản trị kinh doanh, Marketing',
                'khoa_id' => $khoaKT->id,
                'ngay_vao_truong' => '2017-02-10',
            ],
            [
                'ma_giang_vien' => 'GV005',
                'ho_ten' => 'Hoàng Văn Em',
                'email' => 'hoangvanem@smis.edu.vn',
                'so_dien_thoai' => '0905678901',
                'trinh_do_id' => $trinhDos['Đại học']->id,
                'chuyen_mon' => 'Tiếng Anh giao tiếp, TOEIC',
                'khoa_id' => $khoaNN->id,
                'ngay_vao_truong' => '2019-09-01',
            ],
            [
                'ma_giang_vien' => 'GV006',
                'ho_ten' => 'Đỗ Thị Phượng',
                'email' => 'dothiphuong@smis.edu.vn',
                'so_dien_thoai' => '0906789012',
                'trinh_do_id' => $trinhDos['Thạc sĩ']->id,
                'chuyen_mon' => 'Phân tích dữ liệu, Business Intelligence',
                'khoa_id' => $khoaKT->id,
                'ngay_vao_truong' => '2020-01-15',
            ],
            // Giảng viên môn Triết học - Tư tưởng
            [
                'ma_giang_vien' => 'GV007',
                'ho_ten' => 'Nguyễn Thị Hằng',
                'email' => 'nguyenthihang@smis.edu.vn',
                'so_dien_thoai' => '0907890123',
                'trinh_do_id' => $trinhDos['Tiến sĩ']->id,
                'chuyen_mon' => 'Triết học Mác - Lênin, Tư tưởng Hồ Chí Minh',
                'khoa_id' => $khoaCNTT->id, // Có thể thuộc bất kỳ khoa nào
                'ngay_vao_truong' => '2014-09-01',
            ],
            // Giảng viên Giáo dục thể chất
            [
                'ma_giang_vien' => 'GV008',
                'ho_ten' => 'Trần Văn Mạnh',
                'email' => 'tranvanmanh@smis.edu.vn',
                'so_dien_thoai' => '0908901234',
                'trinh_do_id' => $trinhDos['Thạc sĩ']->id,
                'chuyen_mon' => 'Giáo dục thể chất, Thể thao',
                'khoa_id' => $khoaCNTT->id,
                'ngay_vao_truong' => '2016-03-01',
            ],
            // Giảng viên Giáo dục quốc phòng
            [
                'ma_giang_vien' => 'GV009',
                'ho_ten' => 'Lê Văn Nam',
                'email' => 'levannam@smis.edu.vn',
                'so_dien_thoai' => '0909012345',
                'trinh_do_id' => $trinhDos['Đại học']->id,
                'chuyen_mon' => 'Giáo dục quốc phòng - An ninh',
                'khoa_id' => $khoaCNTT->id,
                'ngay_vao_truong' => '2015-06-01',
            ],
        ];

        foreach ($giangViens as $gv) {
            // Kiểm tra giảng viên đã tồn tại chưa
            $existingGV = DB::table('giang_vien')->where('ma_giang_vien', $gv['ma_giang_vien'])->first();
            if ($existingGV) {
                echo "⚠️  Giảng viên {$gv['ma_giang_vien']} đã tồn tại, bỏ qua.\n";
                continue;
            }
            
            // Tạo user cho giảng viên
            $userId = DB::table('users')->insertGetId([
                'name' => $gv['ho_ten'],
                'email' => $gv['email'],
                'password' => Hash::make('password'),
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gán vai trò giảng viên
            $vaiTroGV = DB::table('vai_tro')->where('ma_vai_tro', 'giang_vien')->first();
            DB::table('tai_khoan_vai_tro')->insert([
                'tai_khoan_id' => $userId,
                'vai_tro_id' => $vaiTroGV->id,
                'ngay_gan' => now(),
                'nguoi_gan_id' => 1, // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo giảng viên
            DB::table('giang_vien')->insert(array_merge($gv, [
                'user_id' => $userId,
                'anh_dai_dien' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
