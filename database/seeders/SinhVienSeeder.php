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
                'nganh_id' => $svData['nganh_id'],
                'chuyen_nganh_id' => $svData['chuyen_nganh_id'] ?? null,
                'ky_hien_tai' => $svData['ky_hien_tai'],
                'trang_thai_hoc_tap_id' => $svData['trang_thai_hoc_tap_id'],
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

        // ========================================
        // THÊM 200 SINH VIÊN MỚI - CHIA ĐỀU CHO CÁC CHUYÊN NGÀNH
        // ========================================
        $this->create200Students($khoaHocK25, $trangThaiDangHoc, $vaiTroSinhVien);
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

                // Tạo sinh viên
                DB::table('sinh_vien')->insert([
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
                    'ky_hien_tai' => 1,
                    'trang_thai_hoc_tap_id' => $trangThaiDangHoc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

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

}

