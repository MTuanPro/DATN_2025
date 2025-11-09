<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaoTao\SinhVien;
use App\Models\CanhBaoHocVu;
use App\Models\KetQuaHocTap;
use App\Models\HocKy;
use App\Models\DiemDanh;
use App\Models\HocPhiHocKy;
use App\Models\LopHocPhanSinhVien;
use Illuminate\Support\Facades\DB;

class CanhBaoHocVuTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Bắt đầu tạo dữ liệu test cho Cảnh báo học vụ...');

        // Lấy học kỳ hiện tại
        $hocKyHienTai = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        if (!$hocKyHienTai) {
            // Nếu không có, lấy học kỳ mới nhất
            $hocKyHienTai = HocKy::orderBy('id', 'desc')->first();
        }
        
        if (!$hocKyHienTai) {
            $this->command->error('❌ Không tìm thấy học kỳ nào trong hệ thống!');
            return;
        }
        
        $this->command->info("📅 Học kỳ: {$hocKyHienTai->ten_hoc_ky} - {$hocKyHienTai->nam_hoc}");

        // Lấy 10 sinh viên đầu tiên
        $sinhViens = SinhVien::with('user')->limit(10)->get();
        
        if ($sinhViens->isEmpty()) {
            $this->command->error('❌ Không có sinh viên nào trong hệ thống!');
            return;
        }

        $this->command->info("📊 Tìm thấy {$sinhViens->count()} sinh viên để tạo test data");

        foreach ($sinhViens as $index => $sinhVien) {
            $scenario = $index % 4; // 4 loại cảnh báo
            
            $this->command->info("\n👤 Sinh viên: {$sinhVien->ma_sinh_vien} - {$sinhVien->user->name}");
            
            switch ($scenario) {
                case 0:
                    $this->createDiemThapScenario($sinhVien, $hocKyHienTai);
                    break;
                case 1:
                    $this->createVangNhieuScenario($sinhVien, $hocKyHienTai);
                    break;
                case 2:
                    $this->createNoHocPhiScenario($sinhVien, $hocKyHienTai);
                    break;
                case 3:
                    $this->createHocKyLienTiepScenario($sinhVien, $hocKyHienTai);
                    break;
            }
        }

        $this->command->info("\n✅ Hoàn thành! Tổng số cảnh báo: " . CanhBaoHocVu::count());
    }

    /**
     * CASE 1: GPA < 1.0 (Buộc thôi học)
     */
    private function createDiemThapScenario($sinhVien, $hocKy)
    {
        // Tạo kết quả học tập với GPA thấp
        $ketQua = KetQuaHocTap::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVien->id,
                'hoc_ky_id' => $hocKy->id,
            ],
            [
                'gpa_hoc_ky' => 0.85,
                'gpa_tich_luy' => 0.92,
                'tong_tin_chi_hoc_ky' => 15,
                'tong_tin_chi_tich_luy' => 45,
                'tin_chi_dat' => 8,
                'tin_chi_khong_dat' => 7,
                'xep_loai' => 'yeu',
            ]
        );

        // Tạo cảnh báo
        $canhBao = CanhBaoHocVu::create([
            'sinh_vien_id' => $sinhVien->id,
            'loai' => 'diem_thap',
            'muc_do' => 'buoc_thoi_hoc',
            'ly_do' => "GPA học kỳ {$hocKy->ten_hoc_ky}: {$ketQua->gpa_hoc_ky}/4.0 (< 1.0). GPA tích lũy: {$ketQua->gpa_tich_luy}/4.0. Sinh viên có nguy cơ bị buộc thôi học.",
            'trang_thai' => 'chua_xu_ly',
            'ngay_canh_bao' => now(),
            'nguoi_tao_id' => 1, // Admin/System
        ]);

        $this->command->warn("   ⚠️  Cảnh báo: GPA thấp ({$ketQua->gpa_hoc_ky}) - Buộc thôi học");
    }

    /**
     * CASE 2: Vắng > 20% (Đình chỉ)
     */
    private function createVangNhieuScenario($sinhVien, $hocKy)
    {
        // Lấy 1 lớp học phần của sinh viên
        $lopHocPhanSV = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)->first();
        
        if (!$lopHocPhanSV) {
            $this->command->info("   ℹ️  Sinh viên chưa đăng ký lớp học phần nào");
            return;
        }

        // Giả sử có 30 buổi học, vắng 10 buổi = 33%
        $tongBuoi = 30;
        $soLanVang = 10;
        $tiLeVang = ($soLanVang / $tongBuoi) * 100;

        $canhBao = CanhBaoHocVu::create([
            'sinh_vien_id' => $sinhVien->id,
            'loai' => 'vang_nhieu',
            'muc_do' => 'dinh_chi',
            'ly_do' => "Vắng mặt {$soLanVang}/{$tongBuoi} buổi học (" . number_format($tiLeVang, 1) . "%) trong học kỳ {$hocKy->ten_hoc_ky}. Vượt quá quy định 20%. Sinh viên có nguy cơ bị đình chỉ học tập.",
            'trang_thai' => 'chua_xu_ly',
            'ngay_canh_bao' => now(),
            'nguoi_tao_id' => 1,
        ]);

        $this->command->warn("   ⚠️  Cảnh báo: Vắng nhiều ({$tiLeVang}%) - Đình chỉ");
    }

    /**
     * CASE 3: Nợ học phí > 2 học kỳ (Cảnh cáo)
     */
    private function createNoHocPhiScenario($sinhVien, $hocKy)
    {
        // Lấy học kỳ trước
        $hocKyTruoc = HocKy::where('id', '<', $hocKy->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$hocKyTruoc) {
            $this->command->info("   ℹ️  Không có học kỳ trước để tạo nợ học phí");
            return;
        }

        // Tạo nợ học phí cho 2 học kỳ
        HocPhiHocKy::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVien->id,
                'hoc_ky_id' => $hocKyTruoc->id,
            ],
            [
                'tong_hoc_phi' => 5000000,
                'da_dong' => 0,
                'con_no' => 5000000,
                'trang_thai' => 'chua_dong',
                'han_dong' => now()->subMonths(2),
            ]
        );

        HocPhiHocKy::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVien->id,
                'hoc_ky_id' => $hocKy->id,
            ],
            [
                'tong_hoc_phi' => 5500000,
                'da_dong' => 0,
                'con_no' => 5500000,
                'trang_thai' => 'chua_dong',
                'han_dong' => now()->subMonths(1),
            ]
        );

        $tongNo = 10500000;

        $canhBao = CanhBaoHocVu::create([
            'sinh_vien_id' => $sinhVien->id,
            'loai' => 'no_hoc_phi',
            'muc_do' => 'canh_cao',
            'ly_do' => "Nợ học phí 2 học kỳ liên tiếp ({$hocKyTruoc->ten_hoc_ky}, {$hocKy->ten_hoc_ky}). Tổng số tiền nợ: " . number_format($tongNo) . " VNĐ. Sinh viên cần thanh toán ngay để tránh bị khóa đăng ký môn học.",
            'trang_thai' => 'chua_xu_ly',
            'ngay_canh_bao' => now(),
            'nguoi_tao_id' => 1,
        ]);

        $this->command->warn("   ⚠️  Cảnh báo: Nợ học phí 2 HK (" . number_format($tongNo) . " VNĐ) - Cảnh cáo");
    }

    /**
     * CASE 4: 2 học kỳ liên tiếp không đạt (Đình chỉ)
     */
    private function createHocKyLienTiepScenario($sinhVien, $hocKy)
    {
        // Lấy học kỳ trước
        $hocKyTruoc = HocKy::where('id', '<', $hocKy->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$hocKyTruoc) {
            $this->command->info("   ℹ️  Không có học kỳ trước để tạo học kỳ không đạt");
            return;
        }

        // Tạo kết quả học tập không đạt cho 2 học kỳ
        KetQuaHocTap::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVien->id,
                'hoc_ky_id' => $hocKyTruoc->id,
            ],
            [
                'gpa_hoc_ky' => 1.2,
                'gpa_tich_luy' => 1.5,
                'tong_tin_chi_hoc_ky' => 15,
                'tong_tin_chi_tich_luy' => 30,
                'tin_chi_dat' => 8,
                'tin_chi_khong_dat' => 7,
                'xep_loai' => 'yeu',
            ]
        );

        KetQuaHocTap::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVien->id,
                'hoc_ky_id' => $hocKy->id,
            ],
            [
                'gpa_hoc_ky' => 1.1,
                'gpa_tich_luy' => 1.3,
                'tong_tin_chi_hoc_ky' => 15,
                'tong_tin_chi_tich_luy' => 45,
                'tin_chi_dat' => 7,
                'tin_chi_khong_dat' => 8,
                'xep_loai' => 'yeu',
            ]
        );

        $canhBao = CanhBaoHocVu::create([
            'sinh_vien_id' => $sinhVien->id,
            'loai' => 'hoc_ky_lien_tiep',
            'muc_do' => 'dinh_chi',
            'ly_do' => "GPA 2 học kỳ liên tiếp ({$hocKyTruoc->ten_hoc_ky}: 1.2, {$hocKy->ten_hoc_ky}: 1.1) đều < 1.5. GPA tích lũy hiện tại: 1.3/4.0. Sinh viên có nguy cơ bị đình chỉ học tập.",
            'trang_thai' => 'chua_xu_ly',
            'ngay_canh_bao' => now(),
            'nguoi_tao_id' => 1,
        ]);

        $this->command->warn("   ⚠️  Cảnh báo: 2 HK liên tiếp không đạt - Đình chỉ");
    }
}
