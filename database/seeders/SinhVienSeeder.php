<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\KetQuaHocTap;
use App\Models\HocKy;
use App\Models\PhanCongGiangDay;
use App\Models\BangDiem;
use App\Models\GiangVien;
use App\Models\CauHinhDauDiem;
use App\Models\NhapDiem;
use App\Models\DangKyMonHocTam;
use Carbon\Carbon;

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

        // Lấy ID các chuyên ngành (mỗi ngành có 2 chuyên ngành)
        $chuyenNganhCNTT = DB::table('chuyen_nganh')->where('nganh_id', $nganhCNTT)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhKHMT = DB::table('chuyen_nganh')->where('nganh_id', $nganhKHMT)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhATTT = DB::table('chuyen_nganh')->where('nganh_id', $nganhATTT)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhQTKD = DB::table('chuyen_nganh')->where('nganh_id', $nganhQTKD)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhTCNH = DB::table('chuyen_nganh')->where('nganh_id', $nganhTCNH)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhKT = DB::table('chuyen_nganh')->where('nganh_id', $nganhKT)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhNNA = DB::table('chuyen_nganh')->where('nganh_id', $nganhNNA)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhNNJ = DB::table('chuyen_nganh')->where('nganh_id', $nganhNNJ)->orderBy('id')->pluck('id')->toArray();
        $chuyenNganhNNC = DB::table('chuyen_nganh')->where('nganh_id', $nganhNNC)->orderBy('id')->pluck('id')->toArray();

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
                'nganh_id' => $nganhCNTT,
                'chuyen_nganh_id' => $chuyenNganhCNTT[0] ?? null,
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
                'nganh_id' => $nganhCNTT,
                'chuyen_nganh_id' => $chuyenNganhCNTT[0] ?? null,
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
                'nganh_id' => $nganhCNTT,
                'chuyen_nganh_id' => $chuyenNganhCNTT[1] ?? $chuyenNganhCNTT[0] ?? null,
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
                'nganh_id' => $nganhKHMT,
                'chuyen_nganh_id' => $chuyenNganhKHMT[0] ?? null,
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
                'nganh_id' => $nganhKHMT,
                'chuyen_nganh_id' => $chuyenNganhKHMT[1] ?? $chuyenNganhKHMT[0] ?? null,
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
                'nganh_id' => $nganhATTT,
                'chuyen_nganh_id' => $chuyenNganhATTT[0] ?? null,
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
                'nganh_id' => $nganhATTT,
                'chuyen_nganh_id' => $chuyenNganhATTT[1] ?? $chuyenNganhATTT[0] ?? null,
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
                'nganh_id' => $nganhQTKD,
                'chuyen_nganh_id' => $chuyenNganhQTKD[0] ?? null,
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
                'nganh_id' => $nganhQTKD,
                'chuyen_nganh_id' => $chuyenNganhQTKD[0] ?? null,
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
                'nganh_id' => $nganhQTKD,
                'chuyen_nganh_id' => $chuyenNganhQTKD[1] ?? $chuyenNganhQTKD[0] ?? null,
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
                'nganh_id' => $nganhTCNH,
                'chuyen_nganh_id' => $chuyenNganhTCNH[0] ?? null,
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
                'nganh_id' => $nganhTCNH,
                'chuyen_nganh_id' => $chuyenNganhTCNH[1] ?? $chuyenNganhTCNH[0] ?? null,
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
                'nganh_id' => $nganhKT,
                'chuyen_nganh_id' => $chuyenNganhKT[0] ?? null,
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
                'nganh_id' => $nganhKT,
                'chuyen_nganh_id' => $chuyenNganhKT[1] ?? $chuyenNganhKT[0] ?? null,
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
                'nganh_id' => $nganhNNA,
                'chuyen_nganh_id' => $chuyenNganhNNA[0] ?? null,
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
                'nganh_id' => $nganhNNA,
                'chuyen_nganh_id' => $chuyenNganhNNA[1] ?? $chuyenNganhNNA[0] ?? null,
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
                'nganh_id' => $nganhNNJ,
                'chuyen_nganh_id' => $chuyenNganhNNJ[0] ?? null,
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
                'nganh_id' => $nganhNNJ,
                'chuyen_nganh_id' => $chuyenNganhNNJ[1] ?? $chuyenNganhNNJ[0] ?? null,
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
                'nganh_id' => $nganhNNC,
                'chuyen_nganh_id' => $chuyenNganhNNC[0] ?? null,
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
                'nganh_id' => $nganhNNC,
                'chuyen_nganh_id' => $chuyenNganhNNC[1] ?? $chuyenNganhNNC[0] ?? null,
                'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                'ky_hien_tai' => 1,
            ],
        ];

        $count = 0;
        $countCNTT = 0;
        $countKT = 0;
        $countNN = 0;

        foreach ($sinhViens as $svData) {
            // Kiểm tra xem sinh viên đã tồn tại chưa
            $existingSinhVien = DB::table('sinh_vien')->where('ma_sinh_vien', $svData['ma_sinh_vien'])->first();
            if ($existingSinhVien) {
                continue; // Bỏ qua nếu đã tồn tại
            }

            // Kiểm tra email đã tồn tại chưa
            $existingUser = DB::table('users')->where('email', $svData['email'])->first();
            if ($existingUser) {
                continue; // Bỏ qua nếu email đã tồn tại
            }

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

            // Phân bổ kỳ học đều (1-8)
            $kyHienTai = (($count % 8) + 1);

            // Tạo sinh viên
            $sinhVienId = DB::table('sinh_vien')->insertGetId([
                'user_id' => $userId,
                'ma_sinh_vien' => $svData['ma_sinh_vien'],
                'ho_ten' => $svData['ho_ten'],
                'email' => $svData['email'],
                'ngay_sinh' => $svData['ngay_sinh'],
                'gioi_tinh' => $svData['gioi_tinh'],
                'so_dien_thoai' => $svData['so_dien_thoai'],
                'can_cuoc_cong_dan' => $svData['can_cuoc_cong_dan'],
                'khoa_hoc_id' => $svData['khoa_hoc_id'],
                'nganh_id' => $svData['nganh_id'],
                'chuyen_nganh_id' => $svData['chuyen_nganh_id'] ?? null,
                'ky_hien_tai' => $kyHienTai,
                'trang_thai_hoc_tap_id' => $svData['trang_thai_hoc_tap_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Nếu sinh viên ở kỳ > 1, tạo dữ liệu học tập cho các kỳ trước
            if ($kyHienTai > 1 && $svData['chuyen_nganh_id']) {
                $this->taoDuLieuHocTapCacKyTruoc($sinhVienId, $svData['chuyen_nganh_id'], $svData['khoa_hoc_id'], $kyHienTai);
            }

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

        // ========================================
        // THÊM 200 SINH VIÊN MỚI - CHIA ĐỀU CHO CÁC CHUYÊN NGÀNH
        // ========================================
        $this->create200Students($khoaHocK25, $trangThaiDangHoc, $vaiTroSinhVien);

        // ========================================
        // CẬP NHẬT DỮ LIỆU HỌC TẬP CHO TẤT CẢ SINH VIÊN
        // ========================================
        echo "\n📚 Đang cập nhật dữ liệu học tập cho tất cả sinh viên...\n";
        $this->capNhatDuLieuHocTapChoAllSinhVien();
    }

    /**
     * Tạo 200 sinh viên mới, chia đều cho các chuyên ngành
     */
    private function create200Students($khoaHocK25, $trangThaiDangHoc, $vaiTroSinhVien): void
    {
        // Lấy tất cả chuyên ngành
        $chuyenNganhs = DB::table('chuyen_nganh')
            ->join('nganh', 'chuyen_nganh.nganh_id', '=', 'nganh.id')
            ->select('chuyen_nganh.id as chuyen_nganh_id', 'chuyen_nganh.nganh_id', 'nganh.ma_nganh')
            ->orderBy('chuyen_nganh.id')
            ->get();

        if ($chuyenNganhs->isEmpty()) {
            echo "❌ Không tìm thấy chuyên ngành nào\n";
            return;
        }

        $totalChuyenNganh = $chuyenNganhs->count();
        $studentsPerChuyenNganh = intval(200 / $totalChuyenNganh); // 11 sinh viên/chuyên ngành
        $remainder = 200 % $totalChuyenNganh; // 2 sinh viên còn lại


        // Tìm số bắt đầu từ mã sinh viên lớn nhất hiện có
        $maxMaSinhVien = DB::table('sinh_vien')
            ->where('ma_sinh_vien', 'LIKE', 'SV25%')
            ->orderBy('ma_sinh_vien', 'desc')
            ->value('ma_sinh_vien');

        if ($maxMaSinhVien) {
            // Lấy số từ mã sinh viên (ví dụ: SV25020 -> 25020)
            $studentNumber = intval(substr($maxMaSinhVien, 2)) + 1;
        } else {
            $studentNumber = 25021; // Bắt đầu từ SV25021 nếu chưa có
        }

        // Danh sách họ và tên phổ biến
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Đỗ', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đinh', 'Lý', 'Phan', 'Trịnh', 'Cao', 'Hồ', 'Dương', 'Ngô', 'Lương', 'Tôn'];
        $tenDem = ['Văn', 'Thị', 'Minh', 'Đức', 'Quang', 'Thanh', 'Hữu', 'Công', 'Đình', 'Xuân', 'Hồng', 'Thu', 'Bảo', 'Anh', 'Tuấn', 'Hùng', 'Duy', 'Khánh', 'Thành', 'Phúc'];
        $ten = ['An', 'Bình', 'Cường', 'Dũng', 'Em', 'Giang', 'Hoa', 'Hùng', 'I', 'Khang', 'Long', 'Mai', 'Nam', 'Oanh', 'Phong', 'Quỳnh', 'Rộng', 'Sơn', 'Tâm', 'Uyên', 'Việt', 'Xuân', 'Yên', 'Ánh', 'Bảo', 'Cẩm', 'Đạt', 'Giang', 'Hạnh', 'Khoa'];
        $count = 0;
        $countByChuyenNganh = [];

        foreach ($chuyenNganhs as $index => $chuyenNganh) {
            // Số sinh viên cho chuyên ngành này (11 hoặc 12)
            $numStudents = $studentsPerChuyenNganh + ($index < $remainder ? 1 : 0);

            // Lấy ngành
            $nganhId = $chuyenNganh->nganh_id;

            $countByChuyenNganh[$chuyenNganh->chuyen_nganh_id] = 0;

            $created = 0;
            while ($created < $numStudents) {
                // Tạo mã sinh viên
                $maSinhVien = 'SV' . $studentNumber;

                // Kiểm tra xem mã sinh viên đã tồn tại chưa
                $existingSinhVien = DB::table('sinh_vien')->where('ma_sinh_vien', $maSinhVien)->first();
                if ($existingSinhVien) {
                    $studentNumber++;
                    continue; // Bỏ qua nếu đã tồn tại
                }

                // Tạo email
                $email = 'sv' . $studentNumber . '@sis.edu.vn';

                // Kiểm tra email đã tồn tại chưa
                $existingUser = DB::table('users')->where('email', $email)->first();
                if ($existingUser) {
                    $studentNumber++;
                    continue; // Bỏ qua nếu email đã tồn tại
                }


                // Tạo tên ngẫu nhiên
                $hoTen = $ho[array_rand($ho)] . ' ' . $tenDem[array_rand($tenDem)] . ' ' . $ten[array_rand($ten)];

                // Tạo ngày sinh ngẫu nhiên (2004-2006)
                $year = rand(2004, 2006);
                $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
                $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
                $ngaySinh = "{$year}-{$month}-{$day}";

                // Giới tính ngẫu nhiên
                $gioiTinh = rand(0, 1) == 0 ? 'nam' : 'nu';

                // Số điện thoại
                $soDienThoai = '09' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

                // CCCD (đảm bảo unique)
                $canCuocCongDan = str_pad($year % 100, 2, '0', STR_PAD_LEFT) . $month . $day . str_pad($studentNumber, 6, '0', STR_PAD_LEFT);

                // Tạo user
                $userId = DB::table('users')->insertGetId([
                    'name' => $hoTen,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'trang_thai' => 'hoat_dong',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Gán vai trò sinh viên
                DB::table('tai_khoan_vai_tro')->insert([
                    'tai_khoan_id' => $userId,
                    'vai_tro_id' => $vaiTroSinhVien,
                    'ngay_gan' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Phân bổ kỳ học đều (1-8)
                $kyHienTai = (($count % 8) + 1);

                // Tạo sinh viên
                $sinhVienId = DB::table('sinh_vien')->insertGetId([
                    'user_id' => $userId,
                    'ma_sinh_vien' => $maSinhVien,
                    'ho_ten' => $hoTen,
                    'email' => $email,
                    'ngay_sinh' => $ngaySinh,
                    'gioi_tinh' => $gioiTinh,
                    'so_dien_thoai' => $soDienThoai,
                    'can_cuoc_cong_dan' => $canCuocCongDan,
                    'khoa_hoc_id' => $khoaHocK25,
                    'nganh_id' => $nganhId,
                    'chuyen_nganh_id' => $chuyenNganh->chuyen_nganh_id,
                    'ky_hien_tai' => $kyHienTai,
                    'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Nếu sinh viên ở kỳ > 1, tạo dữ liệu học tập cho các kỳ trước
                if ($kyHienTai > 1) {
                    $this->taoDuLieuHocTapCacKyTruoc($sinhVienId, $chuyenNganh->chuyen_nganh_id, $khoaHocK25, $kyHienTai);
                }

                $studentNumber++;
                $count++;
                $created++;
                $countByChuyenNganh[$chuyenNganh->chuyen_nganh_id]++;
            }
        }

        echo "\n✅ Đã tạo thêm {$count} sinh viên mới (tổng cộng: " . ($count + 20) . " sinh viên)\n";
        echo "   🔑 Mật khẩu đăng nhập: password\n";
        echo "   📧 Email đăng nhập: sv[STT]@sis.edu.vn (ví dụ: sv25021@sis.edu.vn)\n";
        echo "   📊 Phân bổ theo chuyên ngành:\n";

        // Hiển thị thống kê theo chuyên ngành
        foreach ($chuyenNganhs as $chuyenNganh) {
            $tenChuyenNganh = DB::table('chuyen_nganh')->where('id', $chuyenNganh->chuyen_nganh_id)->value('ten_chuyen_nganh');
            $soLuong = $countByChuyenNganh[$chuyenNganh->chuyen_nganh_id] ?? 0;
            echo "      • {$tenChuyenNganh}: {$soLuong} sinh viên\n";
        }
    }

    /**
     * Tạo dữ liệu học tập cho các kỳ trước của sinh viên
     * Sinh viên ở kỳ N sẽ có dữ liệu học tập từ kỳ 1 đến kỳ N-1
     * Một số môn đã qua, một số môn đang nợ (chưa qua)
     */
    private function taoDuLieuHocTapCacKyTruoc($sinhVienId, $chuyenNganhId, $khoaHocId, $kyHienTai): void
    {
        // Lấy chương trình khung của chuyên ngành
        $chuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $chuyenNganhId)
            ->with('monHoc')
            ->orderBy('hoc_ky_goi_y')
            ->orderBy('thu_tu_hoc')
            ->get();

        if ($chuongTrinhKhung->isEmpty()) {
            return; // Không có chương trình khung
        }

        // Lấy khóa học để tính năm học
        $khoaHoc = DB::table('khoa_hoc')->where('id', $khoaHocId)->first();
        if (!$khoaHoc) {
            return;
        }

        $namBatDau = $khoaHoc->nam_bat_dau;
        $soMonDaTao = 0;
        $soMonDaQua = 0;
        $soMonNo = 0;

        // Duyệt qua các kỳ từ 1 đến kỳ hiện tại - 1
        // Logic: Sinh viên kỳ x sẽ có dữ liệu học tập cho các kỳ từ 1 đến x-1
        // Lưu ý: Dữ liệu phải được tạo trong HỌC KỲ QUÁ KHỨ (không được dùng học kỳ hiện tại)

        // Lấy tất cả học kỳ quá khứ (chỉ lấy học kỳ đã bắt đầu), sắp xếp từ gần nhất đến xa nhất
        $hocKyQuaKhu = HocKy::where('la_hoc_ky_hien_tai', false)
            ->where('ngay_bat_dau', '<', now()) // CHỈ LẤY HỌC KỲ ĐÃ QUA, KHÔNG LẤY HỌC KỲ TƯƠNG LAI
            ->orderBy('ngay_bat_dau', 'desc')
            ->get();

        if ($hocKyQuaKhu->isEmpty()) {
            return; // Không có học kỳ quá khứ nào
        }

        for ($ky = 1; $ky < $kyHienTai; $ky++) {
            // Chiến lược: Sử dụng học kỳ quá khứ gần nhất
            // Sinh viên kỳ 2 cần dữ liệu kỳ 1 → dùng học kỳ thứ 0 (gần nhất) = Học kỳ 2 2024-2025
            // Sinh viên kỳ 3 cần dữ liệu kỳ 1 → dùng học kỳ thứ 1 = Học kỳ 1 2024-2025
            // Sinh viên kỳ 3 cần dữ liệu kỳ 2 → dùng học kỳ thứ 0 = Học kỳ 2 2024-2025

            // Tính index của học kỳ cần dùng (đếm ngược từ hiện tại)
            // Kỳ 1 của sinh viên kỳ N nằm ở vị trí (N-2) kỳ trước hiện tại
            // Kỳ 2 của sinh viên kỳ N nằm ở vị trí (N-3) kỳ trước hiện tại
            $viTriLuiVe = $kyHienTai - $ky - 1; // Sinh viên kỳ 2, cần dữ liệu kỳ 1 → lùi 0 kỳ (học kỳ trước liền kề)

            // Lấy học kỳ từ danh sách
            if ($viTriLuiVe >= 0 && $viTriLuiVe < $hocKyQuaKhu->count()) {
                $hocKy = $hocKyQuaKhu[$viTriLuiVe];
            } else {
                // Nếu không đủ học kỳ quá khứ, bỏ qua
                continue;
            }            // Lấy các môn học ở kỳ này (chỉ lấy môn bắt buộc)
            $monHocsTrongKy = $chuongTrinhKhung->where('hoc_ky_goi_y', $ky)
                ->where('bat_buoc', true); // CHỈ TẠO DỮ LIỆU CHO MÔN BẮT BUỘC

            // Debug: Kiểm tra số môn học trong kỳ
            if ($monHocsTrongKy->isEmpty()) {
                // Nếu kỳ này không có môn học, bỏ qua kỳ này
                // Điều này có thể xảy ra nếu chương trình khung chưa có môn cho kỳ này
                continue;
            }

            foreach ($monHocsTrongKy as $ctk) {
                $monHoc = $ctk->monHoc;
                if (!$monHoc) {
                    continue;
                }

                // Kiểm tra xem sinh viên đã có kết quả học tập cho môn này chưa
                // (kiểm tra qua KetQuaHocTap - nếu đã có kết quả thì môn đã được học)
                // Vì môn này có hoc_ky_goi_y = $ky và sinh viên đang ở kỳ > $ky, nên môn phải đã được học
                $daCoKetQua = DB::table('ket_qua_hoc_tap')
                    ->join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
                    ->join('lop_hoc_phan', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
                    ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVienId)
                    ->where('lop_hoc_phan.mon_hoc_id', $monHoc->id)
                    ->exists();

                // Nếu đã có kết quả học tập, bỏ qua
                // Lưu ý: Không bỏ qua nếu chưa có kết quả - cần tạo dữ liệu học tập
                if ($daCoKetQua) {
                    continue;
                }

                // Tìm hoặc tạo lớp học phần cho môn học này ở học kỳ này
                $lopHocPhan = LopHocPhan::where('mon_hoc_id', $monHoc->id)
                    ->where('hoc_ky_id', $hocKy->id)
                    ->first();

                if (!$lopHocPhan) {
                    // Tạo lớp học phần nếu chưa có
                    $ngayBatDauHoc = Carbon::parse($hocKy->ngay_bat_dau);
                    $ngayKetThucHoc = Carbon::parse($hocKy->ngay_ket_thuc);

                    $lopHocPhan = LopHocPhan::create([
                        'mon_hoc_id' => $monHoc->id,
                        'hoc_ky_id' => $hocKy->id,
                        'ma_lop_hp' => $monHoc->ma_mon . '-' . $hocKy->nam_hoc . '-' . $hocKy->ten_hoc_ky,
                        'ten_lop_hp' => $monHoc->ten_mon . ' - ' . $hocKy->ten_hoc_ky . ' ' . $hocKy->nam_hoc,
                        'nhom_lop' => 1,
                        'suc_chua' => 50,
                        'so_luong_dang_ky' => 0,
                        'so_luong_toi_thieu' => 10,
                        'hinh_thuc' => 'offline',
                        'ngay_bat_dau' => $ngayBatDauHoc,
                        'ngay_ket_thuc' => $ngayKetThucHoc,
                        'trang_thai_lop' => 'da_duyet_diem',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Phân công giảng viên cho lớp học phần
                    // Tìm giảng viên có thể dạy môn học này
                    $giangVien = GiangVien::whereHas('monHocs', function ($query) use ($monHoc) {
                        $query->where('mon_hoc.id', $monHoc->id);
                    })->inRandomOrder()->first();

                    if ($giangVien) {
                        // Kiểm tra xem đã phân công chưa
                        $existingPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhan->id)
                            ->where('giang_vien_id', $giangVien->id)
                            ->first();

                        if (!$existingPhanCong) {
                            PhanCongGiangDay::create([
                                'lop_hoc_phan_id' => $lopHocPhan->id,
                                'giang_vien_id' => $giangVien->id,
                                'vai_tro' => 'giang_vien_chinh',
                                'ngay_phan_cong' => $ngayBatDauHoc,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // Kiểm tra xem sinh viên đã đăng ký lớp này chưa
                $lopHocPhanSV = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                    ->where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->first();

                if (!$lopHocPhanSV) {
                    // Tạo đăng ký môn học tạm (DangKyMonHocTam) trước
                    $ngayBatDau = Carbon::parse($hocKy->ngay_bat_dau);
                    $ngayKetThuc = Carbon::parse($hocKy->ngay_ket_thuc);
                    $ngayDangKy = $ngayBatDau->copy()->addDays(rand(1, 7));

                    $dangKyTam = DangKyMonHocTam::create([
                        'sinh_vien_id' => $sinhVienId,
                        'mon_hoc_id' => $monHoc->id,
                        'hoc_ky_id' => $hocKy->id,
                        'ngay_dang_ky' => $ngayDangKy,
                        'uu_tien' => rand(1, 10),
                        'trang_thai' => 'da_xep_lop',
                        'created_at' => $ngayDangKy,
                        'updated_at' => $ngayDangKy,
                    ]);

                    // Tạo kết quả học tập TRƯỚC để xác định trạng thái
                    // 70% môn đã qua, 30% môn đang nợ
                    // Lưu ý: Luôn tạo điểm cho tất cả môn học, không bỏ qua
                    $quaMon = (rand(1, 100) <= 70);

                    // Tạo đăng ký lớp học phần (link với đăng ký tạm)
                    // Nếu môn không qua (failed), set status là 'hoc_lai'
                    $lopHocPhanSV = LopHocPhanSinhVien::create([
                        'sinh_vien_id' => $sinhVienId,
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'dang_ky_tam_id' => $dangKyTam->id,
                        'ngay_dang_ky' => $ngayDangKy,
                        'ngay_xep_lop' => $ngayBatDau->copy()->addDays(rand(8, 14)),
                        'phuong_thuc_xep' => 'tu_dong',
                        'trang_thai' => $quaMon ? 'da_hoan_thanh' : 'hoc_lai',
                        'created_at' => $ngayBatDau,
                        'updated_at' => $ngayKetThuc,
                    ]);

                    // Cập nhật số lượng đăng ký của lớp học phần (đảm bảo không vượt quá sức chứa)
                    $soLuongDangKy = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)->count();
                    $lopHocPhan->update([
                        'so_luong_dang_ky' => min($soLuongDangKy, $lopHocPhan->suc_chua)
                    ]);

                    $soMonDaTao++;

                    // Tạo cấu hình đầu điểm cho lớp học phần (nếu chưa có)
                    $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhan->id)->get();
                    if ($cauHinhs->isEmpty()) {
                        // Tạo cấu hình đầu điểm mặc định: Chuyên cần 10%, Giữa kỳ 30%, Cuối kỳ 60%
                        CauHinhDauDiem::create([
                            'lop_hoc_phan_id' => $lopHocPhan->id,
                            'ten_dau_diem' => 'Chuyên cần',
                            'ty_le' => 10,
                            'so_cot' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        CauHinhDauDiem::create([
                            'lop_hoc_phan_id' => $lopHocPhan->id,
                            'ten_dau_diem' => 'Giữa kỳ',
                            'ty_le' => 30,
                            'so_cot' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        CauHinhDauDiem::create([
                            'lop_hoc_phan_id' => $lopHocPhan->id,
                            'ten_dau_diem' => 'Cuối kỳ',
                            'ty_le' => 60,
                            'so_cot' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Lấy lại cấu hình vừa tạo
                        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhan->id)->get();
                    }

                    if ($quaMon) {
                        // Môn đã qua - có điểm
                        $diemHe10 = rand(50, 100) / 10; // Điểm từ 5.0 đến 10.0
                        $diemChu = KetQuaHocTap::tinhDiemChu($diemHe10);
                        $diemHe4 = KetQuaHocTap::tinhDiemHe4($diemHe10);

                        KetQuaHocTap::create([
                            'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                            'diem_he_10' => $diemHe10,
                            'diem_he_4' => $diemHe4,
                            'diem_chu' => $diemChu,
                            'qua_mon' => true,
                            'created_at' => $ngayKetThuc,
                            'updated_at' => $ngayKetThuc,
                        ]);

                        // Tạo điểm chi tiết cho các đầu điểm
                        $this->taoDiemChiTiet($lopHocPhanSV, $cauHinhs, $diemHe10);

                        $soMonDaQua++;
                    } else {
                        // Môn đang nợ - không có điểm hoặc điểm < 4.0
                        $diemHe10 = rand(0, 39) / 10; // Điểm từ 0.0 đến 3.9
                        $diemChu = KetQuaHocTap::tinhDiemChu($diemHe10);
                        $diemHe4 = KetQuaHocTap::tinhDiemHe4($diemHe10);

                        KetQuaHocTap::create([
                            'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                            'diem_he_10' => $diemHe10,
                            'diem_he_4' => $diemHe4,
                            'diem_chu' => $diemChu,
                            'qua_mon' => false,
                            'ghi_chu' => 'Chưa đạt, cần học lại',
                            'created_at' => $ngayKetThuc,
                            'updated_at' => $ngayKetThuc,
                        ]);

                        // Tạo điểm chi tiết cho các đầu điểm (điểm thấp)
                        $this->taoDiemChiTiet($lopHocPhanSV, $cauHinhs, $diemHe10);

                        $soMonNo++;
                    }
                }
            }

            // Tạo bảng điểm cho học kỳ này (nếu chưa có)
            $bangDiem = BangDiem::where('sinh_vien_id', $sinhVienId)
                ->where('hoc_ky_id', $hocKy->id)
                ->first();

            if (!$bangDiem) {
                // Tính điểm trung bình học kỳ và tín chỉ
                $ketQuaTrongKy = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId, $hocKy) {
                    $q->where('sinh_vien_id', $sinhVienId)
                        ->whereHas('lopHocPhan', function ($q2) use ($hocKy) {
                            $q2->where('hoc_ky_id', $hocKy->id);
                        });
                })->get();

                $tongDiem = 0;
                $tongHeSo = 0;
                $tongTinChiDat = 0;
                $tongTinChiDangKy = 0;

                foreach ($ketQuaTrongKy as $kq) {
                    $lopHocPhanSV = $kq->lopHocPhanSinhVien;
                    $lopHocPhan = $lopHocPhanSV->lopHocPhan;
                    $monHoc = $lopHocPhan->monHoc;

                    $tinChi = $monHoc->so_tin_chi ?? 0;
                    $tongTinChiDangKy += $tinChi;

                    if ($kq->qua_mon) {
                        $tongDiem += $kq->diem_he_10 * $tinChi;
                        $tongHeSo += $tinChi;
                        $tongTinChiDat += $tinChi;
                    }
                }

                $diemTBHK = $tongHeSo > 0 ? $tongDiem / $tongHeSo : 0;

                // Tính điểm TB hệ 4
                $tongDiemHe4 = 0;
                foreach ($ketQuaTrongKy as $kq) {
                    if ($kq->qua_mon) {
                        $lopHocPhanSV = $kq->lopHocPhanSinhVien;
                        $lopHocPhan = $lopHocPhanSV->lopHocPhan;
                        $monHoc = $lopHocPhan->monHoc;
                        $tinChi = $monHoc->so_tin_chi ?? 0;
                        $tongDiemHe4 += $kq->diem_he_4 * $tinChi;
                    }
                }
                $diemTBHe4 = $tongHeSo > 0 ? $tongDiemHe4 / $tongHeSo : 0;

                // Tính điểm TB tích lũy (lấy từ các học kỳ trước)
                $tongDiemTichLuy = 0;
                $tongHeSoTichLuy = 0;
                $tongTinChiDatTichLuy = 0;

                $ketQuaTichLuy = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId, $hocKy) {
                    $q->where('sinh_vien_id', $sinhVienId)
                        ->whereHas('lopHocPhan', function ($q2) use ($hocKy) {
                            $q2->where('hoc_ky_id', '<=', $hocKy->id);
                        });
                })->get();

                foreach ($ketQuaTichLuy as $kq) {
                    $lopHocPhanSV = $kq->lopHocPhanSinhVien;
                    $lopHocPhan = $lopHocPhanSV->lopHocPhan;
                    $monHoc = $lopHocPhan->monHoc;

                    $tinChi = $monHoc->so_tin_chi ?? 0;

                    if ($kq->qua_mon) {
                        $tongDiemTichLuy += $kq->diem_he_10 * $tinChi;
                        $tongHeSoTichLuy += $tinChi;
                        $tongTinChiDatTichLuy += $tinChi;
                    }
                }

                $diemTBTichLuy = $tongHeSoTichLuy > 0 ? $tongDiemTichLuy / $tongHeSoTichLuy : 0;

                // Xác định xếp loại
                $xepLoai = 'yeu';
                if ($diemTBTichLuy >= 9.0) {
                    $xepLoai = 'xuat_sac';
                } elseif ($diemTBTichLuy >= 8.0) {
                    $xepLoai = 'gioi';
                } elseif ($diemTBTichLuy >= 7.0) {
                    $xepLoai = 'kha';
                } elseif ($diemTBTichLuy >= 5.0) {
                    $xepLoai = 'trung_binh';
                }

                BangDiem::create([
                    'sinh_vien_id' => $sinhVienId,
                    'hoc_ky_id' => $hocKy->id,
                    'tong_tin_chi_dang_ky' => $tongTinChiDangKy,
                    'tong_tin_chi_dat' => $tongTinChiDat,
                    'diem_trung_binh_he_10' => round($diemTBHK, 2),
                    'diem_trung_binh_he_4' => round($diemTBHe4, 2),
                    'diem_trung_binh_tich_luy' => round($diemTBTichLuy, 2),
                    'xep_loai_hoc_tap' => $xepLoai,
                    'da_cong_bo' => true,
                    'ngay_cong_bo' => $hocKy->ngay_ket_thuc,
                    'created_at' => $hocKy->ngay_ket_thuc,
                    'updated_at' => $hocKy->ngay_ket_thuc,
                ]);
            }
        }
    }

    /**
     * Cập nhật dữ liệu học tập cho tất cả sinh viên đang học
     */
    private function capNhatDuLieuHocTapChoAllSinhVien(): void
    {
        // Lấy tất cả sinh viên đang học
        $trangThaiDangHoc = DB::table('trang_thai_hoc_tap')
            ->whereIn('ten_trang_thai', ['Đang học', 'Bảo lưu'])
            ->pluck('id');

        $sinhViens = SinhVien::whereIn('trang_thai_hoc_tap_id', $trangThaiDangHoc)
            ->whereNotNull('chuyen_nganh_id')
            ->whereNotNull('khoa_hoc_id')
            ->get();

        $count = 0;
        $countLoi = 0;
        foreach ($sinhViens as $sinhVien) {
            // Luôn cập nhật dữ liệu học tập cho sinh viên ở kỳ > 1
            // Đảm bảo tạo dữ liệu cho tất cả các kỳ từ 1 đến kỳ hiện tại - 1
            // Logic: Sinh viên kỳ x sẽ có dữ liệu học tập cho các kỳ từ 1 đến x-1
            if ($sinhVien->ky_hien_tai > 1) {
                try {
                    $this->taoDuLieuHocTapCacKyTruoc(
                        $sinhVien->id,
                        $sinhVien->chuyen_nganh_id,
                        $sinhVien->khoa_hoc_id,
                        $sinhVien->ky_hien_tai
                    );
                    $count++;
                } catch (\Exception $e) {
                    // Log lỗi nhưng tiếp tục với sinh viên khác
                    $countLoi++;
                    if ($countLoi <= 5) { // Chỉ hiển thị 5 lỗi đầu tiên để không spam
                        echo "⚠️  Lỗi khi tạo dữ liệu học tập cho sinh viên {$sinhVien->ma_sinh_vien} (kỳ {$sinhVien->ky_hien_tai}): " . $e->getMessage() . "\n";
                    }
                }
            }
        }

        if ($countLoi > 5) {
            echo "⚠️  ... và {$countLoi} lỗi khác\n";
        }

        echo "✅ Đã cập nhật dữ liệu học tập cho {$count} sinh viên\n";

        // Tạo DangKyMonHocTam cho các sinh viên đã có LopHocPhanSinhVien nhưng chưa có DangKyMonHocTam
        // (Bỏ qua môn trượt trong học kỳ hiện tại để sinh viên có thể đăng ký học lại)
        echo "📝 Đang tạo lịch sử đăng ký môn học...\n";
        $this->taoDangKyMonHocTamChoAllSinhVien();
    }

    /**
     * Tạo DangKyMonHocTam cho tất cả sinh viên đã có LopHocPhanSinhVien
     * Bỏ qua môn trượt trong học kỳ hiện tại (để sinh viên có thể đăng ký học lại)
     */
    private function taoDangKyMonHocTamChoAllSinhVien(): void
    {
        // Lấy học kỳ hiện tại
        $hocKyHienTai = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        $hocKyHienTaiId = $hocKyHienTai ? $hocKyHienTai->id : null;

        $lopHocPhanSinhViens = LopHocPhanSinhVien::whereNull('dang_ky_tam_id')
            ->with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy'])
            ->get();

        $count = 0;
        $countBoQua = 0;

        foreach ($lopHocPhanSinhViens as $lhpsv) {
            if (!$lhpsv->lopHocPhan || !$lhpsv->lopHocPhan->monHoc || !$lhpsv->lopHocPhan->hocKy) {
                continue;
            }

            // Bỏ qua môn trượt trong học kỳ hiện tại
            if ($hocKyHienTaiId && $lhpsv->lopHocPhan->hoc_ky_id == $hocKyHienTaiId) {
                // Kiểm tra xem môn này có trượt không
                $monTruot = DB::table('ket_qua_hoc_tap')
                    ->join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
                    ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $lhpsv->sinh_vien_id)
                    ->where('lop_hoc_phan_sinh_vien.lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
                    ->where('ket_qua_hoc_tap.qua_mon', false)
                    ->exists();

                if ($monTruot) {
                    $countBoQua++;
                    continue; // Bỏ qua môn trượt trong học kỳ hiện tại
                }
            }

            $dangKyTam = DangKyMonHocTam::create([
                'sinh_vien_id' => $lhpsv->sinh_vien_id,
                'mon_hoc_id' => $lhpsv->lopHocPhan->mon_hoc_id,
                'hoc_ky_id' => $lhpsv->lopHocPhan->hoc_ky_id,
                'ngay_dang_ky' => $lhpsv->ngay_dang_ky ?? $lhpsv->created_at,
                'uu_tien' => rand(1, 10),
                'trang_thai' => 'da_xep_lop',
                'created_at' => $lhpsv->created_at,
                'updated_at' => $lhpsv->updated_at,
            ]);

            $lhpsv->update(['dang_ky_tam_id' => $dangKyTam->id]);
            $count++;
        }

        echo "✅ Đã tạo {$count} bản ghi đăng ký môn học tạm\n";
        if ($countBoQua > 0) {
            echo "   ⏭️  Đã bỏ qua {$countBoQua} môn trượt trong học kỳ hiện tại (để sinh viên có thể đăng ký học lại)\n";
        }

        // Cập nhật so_luong_dang_ky và suc_chua cho tất cả lớp học phần
        echo "📊 Đang cập nhật số lượng đăng ký cho lớp học phần...\n";
        $this->capNhatSoLuongDangKy();
    }

    /**
     * Cập nhật số lượng đăng ký và sức chứa cho tất cả lớp học phần
     */
    private function capNhatSoLuongDangKy(): void
    {
        $lopHocPhans = LopHocPhan::all();
        $count = 0;

        foreach ($lopHocPhans as $lopHocPhan) {
            $soLuongDangKy = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)->count();

            // Đảm bảo sức chứa đủ lớn (ít nhất bằng số lượng đăng ký, tối đa 100)
            // Nếu số lượng đăng ký > 100, giới hạn ở 100
            $sucChuaMoi = max(10, min(100, max($lopHocPhan->suc_chua, min($soLuongDangKy, 100))));
            $soLuongDangKyHienThi = min($soLuongDangKy, $sucChuaMoi);

            $lopHocPhan->update([
                'so_luong_dang_ky' => $soLuongDangKyHienThi,
                'suc_chua' => $sucChuaMoi,
            ]);

            $count++;
        }

        echo "✅ Đã cập nhật {$count} lớp học phần\n";
    }


    /**
     * Tạo điểm chi tiết cho sinh viên
     */
    private function taoDiemChiTiet($lopHocPhanSV, $cauHinhs, $diemTongKet): void
    {
        // Tính điểm cho từng đầu điểm dựa trên tỷ lệ và điểm tổng kết
        // Giả sử điểm được phân bổ đều theo tỷ lệ
        $tongTyLe = $cauHinhs->sum('ty_le');

        foreach ($cauHinhs as $cauHinh) {
            // Tính điểm cho đầu điểm này (tỷ lệ phần trăm của điểm tổng kết)
            // Thêm một chút ngẫu nhiên để điểm không quá đều
            $tyLePhanTram = $cauHinh->ty_le / $tongTyLe;
            $diemDauDiem = $diemTongKet * $tyLePhanTram + (rand(-20, 20) / 100);

            // Đảm bảo điểm trong khoảng 0-10
            $diemDauDiem = max(0, min(10, $diemDauDiem));

            // Làm tròn đến 1 chữ số thập phân
            $diemDauDiem = round($diemDauDiem, 1);

            // Tạo điểm cho từng cột (nếu có nhiều cột)
            for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                // Nếu có nhiều cột, chia điểm đều
                $diemCot = $cauHinh->so_cot > 1 ? $diemDauDiem / $cauHinh->so_cot : $diemDauDiem;
                $diemCot = round($diemCot, 1);

                // Kiểm tra xem đã có điểm chưa
                $existingDiem = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)
                    ->where('cau_hinh_id', $cauHinh->id)
                    ->where('cot_diem', $cot)
                    ->first();

                if (!$existingDiem) {
                    NhapDiem::create([
                        'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                        'cau_hinh_id' => $cauHinh->id,
                        'cot_diem' => $cot,
                        'diem_so' => $diemCot,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Tạo lịch học chi tiết và điểm danh cho lớp học phần
     */
    private function taoLichHocVaDiemDanh($lopHocPhan, $lopHocPhanSV, $ngayBatDau, $ngayKetThuc, $quaMon): void
    {
        // Lấy môn học để biết số tín chỉ
        $monHoc = $lopHocPhan->monHoc;
        if (!$monHoc) {
            return;
        }

        // Tính số buổi học (1 tín chỉ = 15 tiết = khoảng 5 buổi học, mỗi buổi 3 tiết)
        $soTinChi = $monHoc->so_tin_chi ?? 3;
        $soBuoiHoc = $soTinChi * 5; // Ví dụ: 3 tín chỉ = 15 buổi học

        // Lấy ca học ngẫu nhiên
        $caHocs = DB::table('ca_hoc')->pluck('id')->toArray();
        if (empty($caHocs)) {
            return;
        }

        // Lấy phòng học ngẫu nhiên
        $phongHocs = DB::table('phong_hoc')->pluck('id')->toArray();
        if (empty($phongHocs)) {
            // Không có phòng học, bỏ qua
            return;
        }

        // Tạo lịch học (2 buổi/tuần)
        $ngayHocHienTai = Carbon::parse($ngayBatDau);
        $ngayKetThucCarbon = Carbon::parse($ngayKetThuc);

        $buoiHocDaTao = 0;
        $lichHocChiTiets = [];

        while ($buoiHocDaTao < $soBuoiHoc && $ngayHocHienTai <= $ngayKetThucCarbon) {
            // Chỉ tạo lịch học vào thứ 2, 4, 6 (hoặc 3, 5, 7)
            $thu = $ngayHocHienTai->dayOfWeek; // 0 = CN, 1 = T2, ...

            if (in_array($thu, [1, 3, 5])) { // Thứ 2, 4, 6
                $caHocId = $caHocs[array_rand($caHocs)];
                $phongHocId = $phongHocs[array_rand($phongHocs)];

                // Lấy thông tin ca học
                $caHoc = DB::table('ca_hoc')->where('id', $caHocId)->first();
                $gioBatDau = $caHoc ? $caHoc->gio_bat_dau : '07:00:00';
                $gioKetThuc = $caHoc ? $caHoc->gio_ket_thuc : '09:00:00';

                // Tạo datetime đầy đủ
                $gioBatDauFull = $ngayHocHienTai->copy()->setTimeFromTimeString($gioBatDau);
                $gioKetThucFull = $ngayHocHienTai->copy()->setTimeFromTimeString($gioKetThuc);

                // Lấy giảng viên phụ trách lớp học phần
                $giangVienId = DB::table('lop_hoc_phan_giang_vien')
                    ->where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('vai_tro', 'giang_vien_chinh')
                    ->value('giang_vien_id');

                // Nếu không có giảng viên, lấy ngẫu nhiên
                if (!$giangVienId) {
                    $giangVienId = DB::table('giang_vien')->inRandomOrder()->value('id');
                }

                // Kiểm tra xem lịch học đã tồn tại chưa (unique_phong_ngay_tiet)
                $existingLich = DB::table('lich_hoc_chi_tiet')
                    ->where('ngay_hoc', $ngayHocHienTai->format('Y-m-d'))
                    ->where('tiet_bat_dau', 1)
                    ->where('phong_hoc_id', $phongHocId)
                    ->exists();

                // Nếu trùng lịch, chuyển sang ngày khác hoặc phòng khác
                if ($existingLich) {
                    $ngayHocHienTai->addDay();
                    continue;
                }

                // Tạo lịch học chi tiết
                $lichHocChiTiet = DB::table('lich_hoc_chi_tiet')->insertGetId([
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'ca_hoc_id' => $caHocId,
                    'ngay_hoc' => $ngayHocHienTai->format('Y-m-d'),
                    'tiet_bat_dau' => 1,
                    'tiet_ket_thuc' => 3,
                    'gio_bat_dau' => $gioBatDauFull,
                    'gio_ket_thuc' => $gioKetThucFull,
                    'phong_hoc_id' => $phongHocId,
                    'giang_vien_id' => $giangVienId,
                    'hinh_thuc' => 'offline',
                    'trang_thai' => 'da_hoan_thanh',
                    'created_at' => $ngayHocHienTai,
                    'updated_at' => $ngayHocHienTai,
                ]);

                $lichHocChiTiets[] = $lichHocChiTiet;
                $buoiHocDaTao++;
            }

            // Chuyển sang ngày tiếp theo
            $ngayHocHienTai->addDay();
        }

        // Tạo điểm danh cho sinh viên
        // Nếu qua môn: 80-95% có mặt
        // Nếu không qua: 50-70% có mặt
        $tyLeCoMat = $quaMon ? rand(80, 95) / 100 : rand(50, 70) / 100;

        foreach ($lichHocChiTiets as $lichHocId) {
            $coMat = (rand(1, 100) / 100) <= $tyLeCoMat;

            // Enum: co_mat, vang, di_tre, nghi_phep
            if ($coMat) {
                $trangThai = rand(0, 100) < 10 ? 'di_tre' : 'co_mat'; // 10% đi trễ
            } else {
                $trangThai = rand(0, 100) < 20 ? 'nghi_phep' : 'vang'; // 20% nghỉ phép, 80% vắng
            }

            // Kiểm tra xem đã có điểm danh chưa
            $existingDiemDanh = DB::table('diem_danh')
                ->where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)
                ->where('lich_hoc_chi_tiet_id', $lichHocId)
                ->exists();

            if (!$existingDiemDanh) {
                DB::table('diem_danh')->insert([
                    'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                    'lich_hoc_chi_tiet_id' => $lichHocId,
                    'trang_thai' => $trangThai,
                    'thoi_gian_diem_danh' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
