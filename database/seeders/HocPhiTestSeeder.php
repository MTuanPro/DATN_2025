<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HocPhiHocKy;
use App\Models\ChiTietHocPhiMon;
use App\Models\SinhVien;
use App\Models\HocKy;
use App\Models\LopHocPhanSinhVien;
use Carbon\Carbon;

class HocPhiTestSeeder extends Seeder
{
    /**
     * Tạo dữ liệu học phí test cho sinh viên
     */
    public function run(): void
    {
        // Lấy sinh viên đầu tiên (hoặc sinh viên bạn đang dùng để test)
        $sinhVien = SinhVien::first();
        
        if (!$sinhVien) {
            // Nếu không có sinh viên nào, tạo sinh viên test
            $this->command->warn('Không tìm thấy sinh viên. Đang tạo sinh viên test...');
            
            // Lấy user đầu tiên hoặc tạo mới
            $user = \App\Models\User::firstOrCreate(
                ['email' => 'sv001@smis.edu.vn'],
                [
                    'name' => 'Nguyễn Văn Sinh Viên Test',
                    'password' => bcrypt('sv001'),
                    'email_verified_at' => now(),
                ]
            );

            $sinhVien = SinhVien::firstOrCreate(
                ['ma_sinh_vien' => 'SV001'],
                [
                    'user_id' => $user->id,
                    'ho_ten' => 'Nguyễn Văn Sinh Viên Test',
                    'ngay_sinh' => '2000-01-01',
                    'gioi_tinh' => 'Nam',
                    'email' => 'sv001@smis.edu.vn',
                    'trang_thai' => 'dang_hoc',
                ]
            );
        }

        // Lấy học kỳ hiện tại hoặc tạo mới
        $hocKy = HocKy::firstOrCreate(
            ['ten_hoc_ky' => 'Học kỳ 1', 'nam_hoc' => '2025-2026'],
            [
                'ngay_bat_dau' => Carbon::create(2025, 9, 1),
                'ngay_ket_thuc' => Carbon::create(2026, 1, 15),
                'trang_thai' => 'dang_hoc',
            ]
        );

        // Xóa học phí cũ nếu có (để test lại)
        HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
            ->where('hoc_ky_id', $hocKy->id)
            ->delete();

        // Tạo học phí mới
        $hocPhi = HocPhiHocKy::create([
            'sinh_vien_id' => $sinhVien->id,
            'hoc_ky_id' => $hocKy->id,
            'tong_hoc_phi_mon_hoc' => 6500000,
            'phi_dich_vu' => 500000,
            'tong_so_tien' => 7000000,
            'so_tien_da_dong' => 0, // Chưa đóng gì
            'so_tien_con_lai' => 7000000,
            'han_dong' => Carbon::now()->addDays(30), // Hạn đóng sau 30 ngày
            'trang_thai' => 'chua_nop_du',
        ]);

        $this->command->info("✅ Đã tạo học phí test cho sinh viên {$sinhVien->ma_sinh_vien}");
        $this->command->info("   - Tổng học phí: 7,000,000 VNĐ");
        $this->command->info("   - Đã đóng: 0 VNĐ");
        $this->command->info("   - Còn lại: 7,000,000 VNĐ");

        // Lấy các lớp học phần sinh viên đã đăng ký
        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->with('lopHocPhan.monHoc')
            ->limit(5) // Lấy tối đa 5 môn
            ->get();

        if ($lopHocPhanSinhViens->isNotEmpty()) {
            foreach ($lopHocPhanSinhViens as $lhpsv) {
                $monHoc = $lhpsv->lopHocPhan->monHoc ?? null;
                if (!$monHoc) continue;

                $soTinChi = $monHoc->so_tin_chi ?? 3;
                $donGiaTinChi = 400000; // 400k/tín chỉ
                $thanhTien = $soTinChi * $donGiaTinChi;

                ChiTietHocPhiMon::create([
                    'hoc_phi_hoc_ky_id' => $hocPhi->id,
                    'mon_hoc_id' => $monHoc->id,
                    'lop_hoc_phan_sinh_vien_id' => $lhpsv->id,
                    'so_tin_chi' => $soTinChi,
                    'don_gia_tin_chi' => $donGiaTinChi,
                    'thanh_tien' => $thanhTien,
                    'trang_thai' => 'chua_thanh_toan',
                ]);

                $this->command->info("   + {$monHoc->ten_mon}: {$soTinChi} TC x " . number_format($donGiaTinChi) . " = " . number_format($thanhTien) . " VNĐ");
            }
        } else {
            // Nếu không có môn học, tạo chi tiết giả
            $this->command->warn("   ⚠ Sinh viên chưa đăng ký môn nào. Tạo dữ liệu giả...");
            
            // Giả sử có 5 môn học, mỗi môn 3 tín chỉ
            for ($i = 1; $i <= 5; $i++) {
                ChiTietHocPhiMon::create([
                    'hoc_phi_hoc_ky_id' => $hocPhi->id,
                    'mon_hoc_id' => $i, // ID môn học giả
                    'lop_hoc_phan_sinh_vien_id' => null,
                    'so_tin_chi' => 3,
                    'don_gia_tin_chi' => 400000,
                    'thanh_tien' => 1200000,
                    'trang_thai' => 'chua_thanh_toan',
                ]);
            }
        }

        $this->command->info("\n🎉 Hoàn tất! Bây giờ bạn có thể test thanh toán online.");
        $this->command->info("📌 Truy cập: Sinh viên → Học phí → Chi tiết");
    }
}
