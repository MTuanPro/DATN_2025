<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\DanhMuc\TrangThaiHocTap;
use App\Models\User;
use App\Models\VaiTro;

class Phase3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // 1. Tạo Lớp hành chính
            echo "🔵 Creating Lớp hành chính...\n";

            $khoaHoc = KhoaHoc::first();
            $nganh = Nganh::first();

            if (!$khoaHoc || !$nganh) {
                echo "❌ Không tìm thấy Khóa học hoặc Ngành. Vui lòng chạy seeder trước đó.\n";
                return;
            }

            $lopHanhChinh = [
                [
                    'ma_lop' => 'CNTT-K15-01',
                    'ten_lop' => 'Công nghệ thông tin K15 - Lớp 01',
                    'khoa_hoc_id' => $khoaHoc->id,
                    'nganh_id' => $nganh->id,
                    'si_so' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ma_lop' => 'CNTT-K15-02',
                    'ten_lop' => 'Công nghệ thông tin K15 - Lớp 02',
                    'khoa_hoc_id' => $khoaHoc->id,
                    'nganh_id' => $nganh->id,
                    'si_so' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'ma_lop' => 'KTPM-K15-01',
                    'ten_lop' => 'Kỹ thuật phần mềm K15 - Lớp 01',
                    'khoa_hoc_id' => $khoaHoc->id,
                    'nganh_id' => $nganh->id,
                    'si_so' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($lopHanhChinh as $lop) {
                LopHanhChinh::create($lop);
            }

            echo "✅ Created " . count($lopHanhChinh) . " lớp hành chính\n";

            // 2. Tạo Sinh viên
            echo "🔵 Creating Sinh viên...\n";

            $trangThai = TrangThaiHocTap::first();
            if (!$trangThai) {
                echo "❌ Không tìm thấy Trạng thái học tập. Vui lòng tạo trước.\n";
                return;
            }

            $lopCNTT01 = LopHanhChinh::where('ma_lop', 'CNTT-K15-01')->first();
            $lopCNTT02 = LopHanhChinh::where('ma_lop', 'CNTT-K15-02')->first();

            $vaiTroSV = VaiTro::where('ma_vai_tro', 'sinh_vien')->first();

            $sinhViens = [
                // Lớp CNTT-K15-01
                [
                    'ma_sinh_vien' => '2021600001',
                    'ho_ten' => 'Nguyễn Văn An',
                    'email' => 'nva2021@student.edu.vn',
                    'ngay_sinh' => '2003-01-15',
                    'gioi_tinh' => 'nam',
                    'so_dien_thoai' => '0901234561',
                    'can_cuoc_cong_dan' => '001203001001',
                    'lop_hanh_chinh_id' => $lopCNTT01->id,
                ],
                [
                    'ma_sinh_vien' => '2021600002',
                    'ho_ten' => 'Trần Thị Bình',
                    'email' => 'ttb2021@student.edu.vn',
                    'ngay_sinh' => '2003-03-20',
                    'gioi_tinh' => 'nu',
                    'so_dien_thoai' => '0901234562',
                    'can_cuoc_cong_dan' => '001203001002',
                    'lop_hanh_chinh_id' => $lopCNTT01->id,
                ],
                [
                    'ma_sinh_vien' => '2021600003',
                    'ho_ten' => 'Lê Văn Cường',
                    'email' => 'lvc2021@student.edu.vn',
                    'ngay_sinh' => '2003-05-12',
                    'gioi_tinh' => 'nam',
                    'so_dien_thoai' => '0901234563',
                    'can_cuoc_cong_dan' => '001203001003',
                    'lop_hanh_chinh_id' => $lopCNTT01->id,
                ],
                [
                    'ma_sinh_vien' => '2021600004',
                    'ho_ten' => 'Phạm Thị Diệu',
                    'email' => 'ptd2021@student.edu.vn',
                    'ngay_sinh' => '2003-07-08',
                    'gioi_tinh' => 'nu',
                    'so_dien_thoai' => '0901234564',
                    'can_cuoc_cong_dan' => '001203001004',
                    'lop_hanh_chinh_id' => $lopCNTT01->id,
                ],
                [
                    'ma_sinh_vien' => '2021600005',
                    'ho_ten' => 'Hoàng Văn Em',
                    'email' => 'hve2021@student.edu.vn',
                    'ngay_sinh' => '2003-09-25',
                    'gioi_tinh' => 'nam',
                    'so_dien_thoai' => '0901234565',
                    'can_cuoc_cong_dan' => '001203001005',
                    'lop_hanh_chinh_id' => $lopCNTT01->id,
                ],
                // Lớp CNTT-K15-02
                [
                    'ma_sinh_vien' => '2021600006',
                    'ho_ten' => 'Vũ Thị Phượng',
                    'email' => 'vtp2021@student.edu.vn',
                    'ngay_sinh' => '2003-02-14',
                    'gioi_tinh' => 'nu',
                    'so_dien_thoai' => '0901234566',
                    'can_cuoc_cong_dan' => '001203001006',
                    'lop_hanh_chinh_id' => $lopCNTT02->id,
                ],
                [
                    'ma_sinh_vien' => '2021600007',
                    'ho_ten' => 'Đỗ Văn Giang',
                    'email' => 'dvg2021@student.edu.vn',
                    'ngay_sinh' => '2003-04-30',
                    'gioi_tinh' => 'nam',
                    'so_dien_thoai' => '0901234567',
                    'can_cuoc_cong_dan' => '001203001007',
                    'lop_hanh_chinh_id' => $lopCNTT02->id,
                ],
                [
                    'ma_sinh_vien' => '2021600008',
                    'ho_ten' => 'Bùi Thị Hà',
                    'email' => 'bth2021@student.edu.vn',
                    'ngay_sinh' => '2003-06-18',
                    'gioi_tinh' => 'nu',
                    'so_dien_thoai' => '0901234568',
                    'can_cuoc_cong_dan' => '001203001008',
                    'lop_hanh_chinh_id' => $lopCNTT02->id,
                ],
                [
                    'ma_sinh_vien' => '2021600009',
                    'ho_ten' => 'Đinh Văn Ích',
                    'email' => 'dvi2021@student.edu.vn',
                    'ngay_sinh' => '2003-08-22',
                    'gioi_tinh' => 'nam',
                    'so_dien_thoai' => '0901234569',
                    'can_cuoc_cong_dan' => '001203001009',
                    'lop_hanh_chinh_id' => $lopCNTT02->id,
                ],
                [
                    'ma_sinh_vien' => '2021600010',
                    'ho_ten' => 'Ngô Thị Kim',
                    'email' => 'ntk2021@student.edu.vn',
                    'ngay_sinh' => '2003-10-05',
                    'gioi_tinh' => 'nu',
                    'so_dien_thoai' => '0901234570',
                    'can_cuoc_cong_dan' => '001203001010',
                    'lop_hanh_chinh_id' => $lopCNTT02->id,
                ],
            ];

            $created = 0;
            foreach ($sinhViens as $svData) {
                // Tạo User
                $user = User::create([
                    'name' => $svData['ho_ten'],
                    'email' => $svData['email'],
                    'password' => Hash::make($svData['ma_sinh_vien']),
                    'trang_thai' => 'hoat_dong',
                    'email_verified_at' => now(),
                ]);

                // Gán vai trò sinh viên
                if ($vaiTroSV) {
                    $user->vaiTro()->attach($vaiTroSV->id, [
                        'nguoi_gan_id' => 1,
                        'ngay_gan' => now(),
                    ]);
                }

                // Tạo Sinh viên
                SinhVien::create([
                    'ma_sinh_vien' => $svData['ma_sinh_vien'],
                    'ho_ten' => $svData['ho_ten'],
                    'email' => $svData['email'],
                    'ngay_sinh' => $svData['ngay_sinh'],
                    'gioi_tinh' => $svData['gioi_tinh'],
                    'so_dien_thoai' => $svData['so_dien_thoai'],
                    'can_cuoc_cong_dan' => $svData['can_cuoc_cong_dan'],
                    'khoa_hoc_id' => $khoaHoc->id,
                    'lop_hanh_chinh_id' => $svData['lop_hanh_chinh_id'],
                    'nganh_id' => $nganh->id,
                    'ky_hien_tai' => 1,
                    'trang_thai_hoc_tap_id' => $trangThai->id,
                    'user_id' => $user->id,
                ]);

                // Tăng sĩ số lớp
                LopHanhChinh::find($svData['lop_hanh_chinh_id'])->increment('si_so');

                $created++;
            }

            echo "✅ Created {$created} sinh viên\n";
            echo "📝 Tài khoản test:\n";
            echo "   - Email: nva2021@student.edu.vn | Password: 2021600001\n";
            echo "   - Email: ttb2021@student.edu.vn | Password: 2021600002\n";

            DB::commit();
            echo "✅ Phase 3 Seeder completed successfully!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
}
