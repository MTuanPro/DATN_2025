<?php

namespace Database\Seeders;

use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SinhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // TẠO TÀI KHOẢN SINH VIÊN TEST
        // ========================================
        $this->createTestStudent();

        // ========================================
        // TẠO SINH VIÊN CHO CÁC LỚP HÀNH CHÍNH
        // ========================================
        $lopHanhChinhs = LopHanhChinh::with(['khoaHoc', 'nganh'])->get();

        if ($lopHanhChinhs->isEmpty()) {
            $this->command->warn('Cần chạy LopHanhChinhSeeder trước!');
            return;
        }

        // Lấy trạng thái học tập mặc định (ID 1 thường là "Đang học")
        $trangThaiDangHoc = \App\Models\DaoTao\TrangThaiHocTap::first();
        if (!$trangThaiDangHoc) {
            $this->command->error('Không tìm thấy trạng thái học tập!');
            return;
        }

        // Lấy danh sách ID chuyên ngành có sẵn để tránh tham chiếu tới ID không tồn tại
        $chuyenNganhIds = \App\Models\DaoTao\ChuyenNganh::pluck('id')->toArray();
        // Map chuyên ngành theo nganh để chọn chuyên ngành phù hợp với ngành của lớp
        $chuyenNganhByNganh = \App\Models\DaoTao\ChuyenNganh::select('id', 'nganh_id')
            ->get()
            ->groupBy('nganh_id')
            ->map(function ($group) {
                return $group->pluck('id')->toArray();
            })->toArray();

        // Lấy vai trò sinh viên một lần
        $vaiTroSinhVien = \App\Models\VaiTro::where('ma_vai_tro', 'sinh_vien')->first();
        if (!$vaiTroSinhVien) {
            $this->command->error('Không tìm thấy vai trò sinh_vien!');
            return;
        }

        // Hash password một lần để tái sử dụng
        $hashedPassword = Hash::make('password');

        // Batch collections
        $users = [];
        $sinhViens = [];
        $lopUpdates = [];
        $totalCreated = 0;
        $batchSize = 200; // Tăng batch size lên 200

        // Bắt đầu transaction để tăng tốc
        DB::beginTransaction();

        try {
            foreach ($lopHanhChinhs as $lop) {
                if (!$lop->khoaHoc) {
                    $this->command->warn("Lớp {$lop->ma_lop} không có khóa học!");
                    continue;
                }

                // Số sinh viên cho lớp này (70-90% sĩ số tối đa)
                $soSinhVien = rand(
                    (int)($lop->si_so * 0.7),
                    (int)($lop->si_so * 0.9)
                );

                for ($i = 1; $i <= $soSinhVien; $i++) {
                    $maSinhVien = $this->generateMaSinhVien($lop, $i);
                    $email = $maSinhVien . '@student.edu.vn';

                    // Thêm user vào batch
                    $users[] = [
                        'name' => $maSinhVien,
                        'email' => $email,
                        'password' => $hashedPassword,
                        'email_verified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Xác định kỳ hiện tại dựa trên năm bắt đầu
                    $namHienTai = now()->year;
                    $namBatDau = $lop->khoaHoc->nam_bat_dau;
                    $soNamHoc = $namHienTai - $namBatDau;
                    $kyHienTai = min(8, max(1, ($soNamHoc * 2) + (now()->month >= 8 ? 1 : 0)));

                    // Chọn chuyên ngành (nếu sinh viên từ năm 3 trở lên)
                    $selectedChuyenNganhId = null;
                    if ($kyHienTai >= 5) {
                        $idsForNganh = $chuyenNganhByNganh[$lop->nganh_id] ?? [];
                        if (!empty($idsForNganh)) {
                            $selectedChuyenNganhId = $idsForNganh[array_rand($idsForNganh)];
                        } elseif (!empty($chuyenNganhIds)) {
                            $selectedChuyenNganhId = $chuyenNganhIds[array_rand($chuyenNganhIds)];
                        }
                    }

                    // Lưu thông tin để tạo sinh viên sau khi insert users
                    $sinhViens[] = [
                        'ma_sinh_vien' => $maSinhVien,
                        'email' => $email, // Giữ lại email cho bảng sinh_vien
                        'ho_ten' => $this->generateHoTen(),
                        'ngay_sinh' => $this->generateNgaySinh($namBatDau),
                        'gioi_tinh' => rand(0, 1) ? 'nam' : 'nu',
                        'so_dien_thoai' => '0' . rand(300000000, 999999999),
                        'so_nha_duong' => rand(1, 200) . ' Đường Số ' . rand(1, 30),
                        'phuong_xa' => 'Phường ' . rand(1, 20),
                        'quan_huyen' => 'Quận ' . rand(1, 12),
                        'tinh_thanh' => $this->getRandomProvince(),
                        'can_cuoc_cong_dan' => $this->generateCCCD(),
                        'ngay_cap_cccd' => now()->subYears(rand(1, 3))->format('Y-m-d'),
                        'noi_cap_cccd' => 'Công an ' . $this->getRandomProvince(),
                        'anh_dai_dien' => null,
                        'khoa_hoc_id' => $lop->khoa_hoc_id,
                        'lop_hanh_chinh_id' => $lop->id,
                        'nganh_id' => $lop->nganh_id,
                        'chuyen_nganh_id' => $selectedChuyenNganhId,
                        'ky_hien_tai' => $kyHienTai,
                        'trang_thai_hoc_tap_id' => $trangThaiDangHoc->id,
                        'giang_vien_chu_nhiem_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $totalCreated++;

                    // Batch insert users và sinh viên
                    if (count($users) >= $batchSize) {
                        $this->insertBatchUsersAndSinhViens($users, $sinhViens, $vaiTroSinhVien->id);
                        $users = [];
                        $sinhViens = [];
                        $this->command->info("  Đã tạo {$totalCreated} sinh viên...");
                    }
                }

                // Lưu thông tin cập nhật lớp
                $lopUpdates[] = [
                    'id' => $lop->id,
                    'si_so' => $soSinhVien,
                ];
            }

            // Insert batch cuối cùng
            if (!empty($users)) {
                $this->insertBatchUsersAndSinhViens($users, $sinhViens, $vaiTroSinhVien->id);
            }

            // Batch update lớp hành chính
            if (!empty($lopUpdates)) {
                foreach ($lopUpdates as $update) {
                    DB::table('lop_hanh_chinh')
                        ->where('id', $update['id'])
                        ->update(['si_so' => $update['si_so']]);
                }
            }

            DB::commit();
            $this->command->info("✓ Đã tạo {$totalCreated} sinh viên");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Lỗi khi tạo sinh viên: " . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Batch insert users và sinh viên
     */
    private function insertBatchUsersAndSinhViens(array &$users, array &$sinhViens, int $vaiTroId): void
    {
        // Lọc các user đã tồn tại
        $emails = array_column($users, 'email');
        $existingEmails = User::whereIn('email', $emails)->pluck('email')->toArray();
        
        // Chỉ insert các user chưa tồn tại
        $newUsers = array_filter($users, function($user) use ($existingEmails) {
            return !in_array($user['email'], $existingEmails);
        });
        
        if (!empty($newUsers)) {
            User::insert(array_values($newUsers));
        }

        // Lấy các user vừa insert để lấy ID
        $emails = array_column($users, 'email');
        $insertedUsers = User::whereIn('email', $emails)
            ->pluck('id', 'email')
            ->toArray();

        // Tạo vai trò inserts
        $vaiTroInserts = [];
        $now = now();
        foreach ($insertedUsers as $email => $userId) {
            $vaiTroInserts[] = [
                'tai_khoan_id' => $userId,
                'vai_tro_id' => $vaiTroId,
                'ngay_gan' => $now,
                'nguoi_gan_id' => 1, // Admin
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Gán user_id cho sinh viên
        foreach ($sinhViens as &$sv) {
            $sv['user_id'] = $insertedUsers[$sv['email']] ?? null;
            // Giữ lại email vì bảng sinh_vien yêu cầu trường này
        }

        // Insert vai trò batch
        if (!empty($vaiTroInserts)) {
            DB::table('tai_khoan_vai_tro')->insert($vaiTroInserts);
        }

        // Insert sinh viên batch
        \App\Models\DaoTao\SinhVien::insert($sinhViens);
    }

    /**
     * Generate mã sinh viên theo format: YYLLnnnn (VD: 17010001, 17010035, 17020001)
     * YY: Năm vào học (2 số cuối)
     * LL: Mã lớp (2 số)
     * nnnn: Số thứ tự trong lớp (4 số)
     */
    private function generateMaSinhVien(LopHanhChinh $lop, int $index): string
    {
        // Load khoaHoc nếu chưa có
        if (!$lop->khoaHoc) {
            $lop->load('khoaHoc');
        }

        $namVaoHoc = $lop->khoaHoc->nam_bat_dau % 100; // 2017 -> 17
        $maLop = str_pad($lop->id, 2, '0', STR_PAD_LEFT); // Lớp ID 1 -> 01
        $stt = str_pad($index, 4, '0', STR_PAD_LEFT); // Index 1 -> 0001

        return $namVaoHoc . $maLop . $stt;
    }

    /**
     * Generate họ tên ngẫu nhiên
     */
    private function generateHoTen(): string
    {
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương'];
        $tenDem = ['Văn', 'Thị', 'Hữu', 'Minh', 'Thanh', 'Quang', 'Anh', 'Đức', 'Tuấn', 'Hoài', 'Kim', 'Bảo'];
        $ten = [
            'An',
            'Bình',
            'Chi',
            'Dũng',
            'Hà',
            'Hùng',
            'Khoa',
            'Linh',
            'Long',
            'Mai',
            'Nam',
            'Phương',
            'Quân',
            'Sơn',
            'Tâm',
            'Thảo',
            'Trang',
            'Tuấn',
            'Vy',
            'Yến'
        ];

        return $ho[array_rand($ho)] . ' ' .
            (rand(0, 1) ? $tenDem[array_rand($tenDem)] . ' ' : '') .
            $ten[array_rand($ten)];
    }

    /**
     * Generate ngày sinh hợp lý (18-22 tuổi khi vào đại học)
     */
    private function generateNgaySinh(int $namVaoHoc): string
    {
        $namSinh = $namVaoHoc - rand(18, 20);
        $thang = rand(1, 12);
        $ngay = rand(1, 28);

        return sprintf('%04d-%02d-%02d', $namSinh, $thang, $ngay);
    }

    /**
     * Generate địa chỉ ngẫu nhiên
     */
    private function generateDiaChi(): string
    {
        $tinh = [
            'Hà Nội',
            'TP. Hồ Chí Minh',
            'Đà Nẵng',
            'Hải Phòng',
            'Cần Thơ',
            'Nghệ An',
            'Thanh Hóa',
            'Hải Dương',
            'Nam Định',
            'Thái Bình',
            'Bắc Ninh',
            'Bắc Giang',
            'Vĩnh Phúc',
            'Quảng Ninh',
            'Lạng Sơn'
        ];

        return rand(1, 200) . ' Đường Số ' . rand(1, 30) . ', ' . $tinh[array_rand($tinh)];
    }

    /**
     * Get random province
     */
    private function getRandomProvince(): string
    {
        $provinces = [
            'Hà Nội',
            'TP. Hồ Chí Minh',
            'Đà Nẵng',
            'Hải Phòng',
            'Cần Thơ',
            'Nghệ An',
            'Thanh Hóa',
            'Hải Dương',
            'Nam Định',
            'Thái Bình',
            'Bắc Ninh',
            'Bắc Giang',
            'Vĩnh Phúc',
            'Quảng Ninh',
            'Lạng Sơn'
        ];
        return $provinces[array_rand($provinces)];
    }

    /**
     * Generate CCCD ngẫu nhiên (12 số)
     */
    private function generateCCCD(): string
    {
        return str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
    }

    /**
     * Tạo tài khoản sinh viên test cố định
     */
    private function createTestStudent(): void
    {
        // Kiểm tra tài khoản đã tồn tại chưa
        $existingUser = User::where('email', 'sinhvien@smis.edu.vn')->first();

        if ($existingUser) {
            $this->command->warn('Tài khoản sinhvien@smis.edu.vn đã tồn tại!');

            // Cập nhật password
            $existingUser->update([
                'password' => Hash::make('password'),
            ]);

            $userId = $existingUser->id;
        } else {
            // Tạo user mới
            $user = User::create([
                'name' => 'Sinh Viên Test',
                'email' => 'sinhvien@smis.edu.vn',
                'password' => Hash::make('password'),
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
            ]);

            $userId = $user->id;
        }

        // Gán vai trò sinh viên
        $vaiTroSinhVien = \App\Models\VaiTro::where('ma_vai_tro', 'sinh_vien')->first();
        if ($vaiTroSinhVien) {
            $hasRole = DB::table('tai_khoan_vai_tro')
                ->where('tai_khoan_id', $userId)
                ->where('vai_tro_id', $vaiTroSinhVien->id)
                ->exists();

            if (!$hasRole) {
                DB::table('tai_khoan_vai_tro')->insert([
                    'tai_khoan_id' => $userId,
                    'vai_tro_id' => $vaiTroSinhVien->id,
                    'ngay_gan' => now(),
                    'nguoi_gan_id' => 1, // Admin
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Kiểm tra record sinh_vien đã tồn tại chưa
        $existingSinhVien = SinhVien::where('user_id', $userId)->first();

        if (!$existingSinhVien) {
            // Lấy dữ liệu cần thiết
            $lopHanhChinh = LopHanhChinh::first();
            $khoaHoc = \App\Models\DaoTao\KhoaHoc::first();
            $nganh = \App\Models\DaoTao\Nganh::first();
            $chuyenNganh = \App\Models\DaoTao\ChuyenNganh::where('nganh_id', $nganh->id)->first();
            $trangThaiHocTap = \App\Models\DaoTao\TrangThaiHocTap::where('ten_trang_thai', 'Đang học')->first();

            if ($lopHanhChinh && $khoaHoc && $nganh && $chuyenNganh && $trangThaiHocTap) {
                // Tạo record sinh_vien
                SinhVien::create([
                    'user_id' => $userId,
                    'ma_sinh_vien' => 'SV2025001',
                    'ho_ten' => 'Nguyễn Văn Sinh Viên',
                    'email' => 'sinhvien@smis.edu.vn',
                    'ngay_sinh' => '2003-01-15',
                    'gioi_tinh' => 'nam',
                    'so_dien_thoai' => '0987654321',
                    'so_nha_duong' => '123 Nguyễn Huệ',
                    'phuong_xa' => 'Phường Bến Nghé',
                    'quan_huyen' => 'Quận 1',
                    'tinh_thanh' => 'TP. Hồ Chí Minh',
                    'can_cuoc_cong_dan' => '001203012345',
                    'ngay_cap_cccd' => now()->subYears(2),
                    'noi_cap_cccd' => 'Công an TP. Hồ Chí Minh',
                    'khoa_hoc_id' => $khoaHoc->id,
                    'lop_hanh_chinh_id' => $lopHanhChinh->id,
                    'nganh_id' => $nganh->id,
                    'chuyen_nganh_id' => $chuyenNganh->id,
                    'ky_hien_tai' => 1,
                    'trang_thai_hoc_tap_id' => $trangThaiHocTap->id,
                ]);

                $this->command->info('========================================');
                $this->command->info('TÀI KHOẢN SINH VIÊN TEST ĐÃ TẠO/CẬP NHẬT:');
                $this->command->info('========================================');
                $this->command->info('Email: sinhvien@smis.edu.vn');
                $this->command->info('Password: password');
                $this->command->info('Mã SV: SV2025001');
                $this->command->info('========================================');
            }
        } else {
            $this->command->info('Sinh viên test đã có record trong bảng sinh_vien.');
        }
    }
}
