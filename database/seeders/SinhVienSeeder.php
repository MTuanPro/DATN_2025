<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SinhVienSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID các khoa
        $khoaCNTT = DB::table('khoa')->where('ten_khoa', 'LIKE', '%Công nghệ thông tin%')->value('id');
        $khoaKT = DB::table('khoa')->where('ten_khoa', 'LIKE', '%Kinh tế%')->value('id');
        $khoaNN = DB::table('khoa')->where('ten_khoa', 'LIKE', '%Ngoại ngữ%')->value('id');

        if (!$khoaCNTT || !$khoaKT || !$khoaNN) {
            echo "❌ Không tìm thấy đủ các khoa\n";
            return;
        }

        // Lấy ID khóa học K25
        $khoaHocK25 = DB::table('khoa_hoc')->where('ten_khoa_hoc', 'K25')->value('id');
        if (!$khoaHocK25) {
            echo "❌ Không tìm thấy khóa học K25\n";
            return;
        }

        // Lấy ID các ngành
        $nganhCNTT = DB::table('nganh')->where('ma_nganh', '7480201')->value('id'); // Công nghệ thông tin
        $nganhKHMT = DB::table('nganh')->where('ma_nganh', '7480202')->value('id'); // Khoa học máy tính
        $nganhATTT = DB::table('nganh')->where('ma_nganh', '7480299')->value('id'); // An toàn thông tin
        $nganhQTKD = DB::table('nganh')->where('ma_nganh', '7340101')->value('id'); // Quản trị kinh doanh
        $nganhTCNH = DB::table('nganh')->where('ma_nganh', '7340201')->value('id'); // Tài chính - Ngân hàng
        $nganhKT = DB::table('nganh')->where('ma_nganh', '7340301')->value('id'); // Kế toán
        $nganhNNA = DB::table('nganh')->where('ma_nganh', '7220201')->value('id'); // Ngôn ngữ Anh
        $nganhNNJ = DB::table('nganh')->where('ma_nganh', '7220203')->value('id'); // Ngôn ngữ Nhật
        $nganhNNC = DB::table('nganh')->where('ma_nganh', '7220204')->value('id'); // Ngôn ngữ Trung Quốc

        // Lấy ID các lớp hành chính K25
        $lopCNTT25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'CNTT25A')->value('id');
        $lopCNTT25B = DB::table('lop_hanh_chinh')->where('ma_lop', 'CNTT25B')->value('id');
        $lopKHMT25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'KHMT25A')->value('id');
        $lopATTT25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'ATTT25A')->value('id');
        $lopQTKD25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'QTKD25A')->value('id');
        $lopQTKD25B = DB::table('lop_hanh_chinh')->where('ma_lop', 'QTKD25B')->value('id');
        $lopTCNH25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'TCNH25A')->value('id');
        $lopKT25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'KT25A')->value('id');
        $lopNNA25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'NNA25A')->value('id');
        $lopNNJ25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'NNJ25A')->value('id');
        $lopNNC25A = DB::table('lop_hanh_chinh')->where('ma_lop', 'NNC25A')->value('id');

        // Lấy ID trạng thái học tập "Đang học"
        $trangThaiDangHoc = DB::table('trang_thai_hoc_tap')->where('ten_trang_thai', 'Đang học')->value('id');
        if (!$trangThaiDangHoc) {
            echo "❌ Không tìm thấy trạng thái học tập 'Đang học'\n";
            return;
        }

        // Lấy ID vai trò sinh viên
        $vaiTroSinhVien = DB::table('vai_tro')->where('ma_vai_tro', 'sinh_vien')->value('id');
        if (!$vaiTroSinhVien) {
            echo "❌ Không tìm thấy vai trò Sinh viên\n";
            return;
        }

        // Danh sách sinh viên (20 người, chia đều 3 khoa)
        $sinhViens = [
            // Khoa CNTT - 7 sinh viên
            [
                'ma_sinh_vien' => 'SV25001',
                'ho_ten' => 'Nguyễn Văn An',
                'email' => 'sv25001@sis.edu.vn',
                'ngay_sinh' => '2005-03-15',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234001',
                'can_cuoc_cong_dan' => '001205030001',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopCNTT25A,
                'nganh_id' => $nganhCNTT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25002',
                'ho_ten' => 'Trần Thị Bích',
                'email' => 'sv25002@sis.edu.vn',
                'ngay_sinh' => '2005-07-22',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234002',
                'can_cuoc_cong_dan' => '001205070002',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopCNTT25A,
                'nganh_id' => $nganhCNTT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25003',
                'ho_ten' => 'Lê Minh Cường',
                'email' => 'sv25003@sis.edu.vn',
                'ngay_sinh' => '2005-11-10',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234003',
                'can_cuoc_cong_dan' => '001205110003',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopCNTT25B,
                'nganh_id' => $nganhCNTT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25004',
                'ho_ten' => 'Phạm Văn Dũng',
                'email' => 'sv25004@sis.edu.vn',
                'ngay_sinh' => '2005-05-18',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234004',
                'can_cuoc_cong_dan' => '001205050004',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopKHMT25A,
                'nganh_id' => $nganhKHMT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25005',
                'ho_ten' => 'Hoàng Thị Em',
                'email' => 'sv25005@sis.edu.vn',
                'ngay_sinh' => '2005-09-25',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234005',
                'can_cuoc_cong_dan' => '001205090005',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopKHMT25A,
                'nganh_id' => $nganhKHMT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25006',
                'ho_ten' => 'Đỗ Văn Phong',
                'email' => 'sv25006@sis.edu.vn',
                'ngay_sinh' => '2005-04-12',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234006',
                'can_cuoc_cong_dan' => '001205040006',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopATTT25A,
                'nganh_id' => $nganhATTT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25007',
                'ho_ten' => 'Vũ Thị Giang',
                'email' => 'sv25007@sis.edu.vn',
                'ngay_sinh' => '2005-08-30',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234007',
                'can_cuoc_cong_dan' => '001205080007',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopATTT25A,
                'nganh_id' => $nganhATTT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],

            // Khoa Kinh tế - 7 sinh viên
            [
                'ma_sinh_vien' => 'SV25008',
                'ho_ten' => 'Lý Văn Long',
                'email' => 'sv25008@sis.edu.vn',
                'ngay_sinh' => '2005-03-20',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234008',
                'can_cuoc_cong_dan' => '001205030008',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopQTKD25A,
                'nganh_id' => $nganhQTKD,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25009',
                'ho_ten' => 'Phan Thị Mai',
                'email' => 'sv25009@sis.edu.vn',
                'ngay_sinh' => '2005-11-14',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234009',
                'can_cuoc_cong_dan' => '001205110009',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopQTKD25A,
                'nganh_id' => $nganhQTKD,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25010',
                'ho_ten' => 'Võ Văn Nam',
                'email' => 'sv25010@sis.edu.vn',
                'ngay_sinh' => '2005-05-08',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234010',
                'can_cuoc_cong_dan' => '001205050010',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopQTKD25B,
                'nganh_id' => $nganhQTKD,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25011',
                'ho_ten' => 'Đặng Thị Oanh',
                'email' => 'sv25011@sis.edu.vn',
                'ngay_sinh' => '2005-09-12',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234011',
                'can_cuoc_cong_dan' => '001205090011',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopTCNH25A,
                'nganh_id' => $nganhTCNH,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25012',
                'ho_ten' => 'Trịnh Văn Phúc',
                'email' => 'sv25012@sis.edu.vn',
                'ngay_sinh' => '2005-07-25',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234012',
                'can_cuoc_cong_dan' => '001205070012',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopTCNH25A,
                'nganh_id' => $nganhTCNH,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25013',
                'ho_ten' => 'Cao Thị Quỳnh',
                'email' => 'sv25013@sis.edu.vn',
                'ngay_sinh' => '2005-12-03',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234013',
                'can_cuoc_cong_dan' => '001205120013',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopKT25A,
                'nganh_id' => $nganhKT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25014',
                'ho_ten' => 'Hồ Văn Rộng',
                'email' => 'sv25014@sis.edu.vn',
                'ngay_sinh' => '2005-04-16',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234014',
                'can_cuoc_cong_dan' => '001205040014',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopKT25A,
                'nganh_id' => $nganhKT,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],

            // Khoa Ngoại ngữ - 6 sinh viên
            [
                'ma_sinh_vien' => 'SV25015',
                'ho_ten' => 'Nguyễn Thị Xuân',
                'email' => 'sv25015@sis.edu.vn',
                'ngay_sinh' => '2005-12-10',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234015',
                'can_cuoc_cong_dan' => '001205120015',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopNNA25A,
                'nganh_id' => $nganhNNA,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25016',
                'ho_ten' => 'Trần Văn Yên',
                'email' => 'sv25016@sis.edu.vn',
                'ngay_sinh' => '2005-03-28',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234016',
                'can_cuoc_cong_dan' => '001205030016',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopNNA25A,
                'nganh_id' => $nganhNNA,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25017',
                'ho_ten' => 'Lê Thị Ánh',
                'email' => 'sv25017@sis.edu.vn',
                'ngay_sinh' => '2005-07-19',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234017',
                'can_cuoc_cong_dan' => '001205070017',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopNNJ25A,
                'nganh_id' => $nganhNNJ,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25018',
                'ho_ten' => 'Phạm Văn Bình',
                'email' => 'sv25018@sis.edu.vn',
                'ngay_sinh' => '2005-01-15',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234018',
                'can_cuoc_cong_dan' => '001205010018',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopNNJ25A,
                'nganh_id' => $nganhNNJ,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25019',
                'ho_ten' => 'Hoàng Thị Cẩm',
                'email' => 'sv25019@sis.edu.vn',
                'ngay_sinh' => '2005-11-24',
                'gioi_tinh' => 'nu',
                'so_dien_thoai' => '0901234019',
                'can_cuoc_cong_dan' => '001205110019',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopNNC25A,
                'nganh_id' => $nganhNNC,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
            [
                'ma_sinh_vien' => 'SV25020',
                'ho_ten' => 'Đỗ Văn Đạt',
                'email' => 'sv25020@sis.edu.vn',
                'ngay_sinh' => '2005-06-09',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234020',
                'can_cuoc_cong_dan' => '001205060020',
                'khoa_hoc_id' => $khoaHocK25,
                'lop_hanh_chinh_id' => $lopNNC25A,
                'nganh_id' => $nganhNNC,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
        ];

        $count = 0;
        $countCNTT = 0;
        $countKT = 0;
        $countNN = 0;

        foreach ($sinhViens as $svData) {
            // Tạo user trước
            $userId = DB::table('users')->insertGetId([
                'name' => $svData['ho_ten'],
                'email' => $svData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gán vai trò sinh viên cho user
            DB::table('tai_khoan_vai_tro')->insert([
                'tai_khoan_id' => $userId,
                'vai_tro_id' => $vaiTroSinhVien,
                'ngay_gan' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo sinh viên
            DB::table('sinh_vien')->insert([
                'user_id' => $userId,
                'ma_sinh_vien' => $svData['ma_sinh_vien'],
                'ho_ten' => $svData['ho_ten'],
                'email' => $svData['email'],
                'ngay_sinh' => $svData['ngay_sinh'],
                'gioi_tinh' => $svData['gioi_tinh'],
                'so_dien_thoai' => $svData['so_dien_thoai'],
                'can_cuoc_cong_dan' => $svData['can_cuoc_cong_dan'],
                'khoa_hoc_id' => $svData['khoa_hoc_id'],
                'lop_hanh_chinh_id' => $svData['lop_hanh_chinh_id'],
                'nganh_id' => $svData['nganh_id'],
                'chuyen_nganh_id' => null,
                'ky_hien_tai' => $svData['ky_hien_tai'],
                'trang_thai_hoc_tap_id' => $svData['trang_thai_hoc_tap_id'],
                'giang_vien_chu_nhiem_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Đếm theo khoa
            $nganh = DB::table('nganh')->where('id', $svData['nganh_id'])->first();
            if ($nganh) {
                if ($nganh->khoa_id == $khoaCNTT) {
                    $countCNTT++;
                } elseif ($nganh->khoa_id == $khoaKT) {
                    $countKT++;
                } elseif ($nganh->khoa_id == $khoaNN) {
                    $countNN++;
                }
            }

            $count++;
        }

        echo "✅ Đã tạo {$count} sinh viên\n";
        echo "   👨‍🎓 Khoa CNTT: {$countCNTT} sinh viên\n";
        echo "   👨‍🎓 Khoa Kinh tế: {$countKT} sinh viên\n";
        echo "   👨‍🎓 Khoa Ngoại ngữ: {$countNN} sinh viên\n";
        echo "   🔑 Mật khẩu mặc định: password\n";
    }
}

