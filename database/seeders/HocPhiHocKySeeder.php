<?php

namespace Database\Seeders;

use App\Models\HocPhiHocKy;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
use App\Models\CauHinhHocPhi;
use App\Models\LopHocPhanSinhVien;
use App\Models\DaoTao\MonHoc;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HocPhiHocKySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💰 Bắt đầu tạo học phí học kỳ...');

        // Lấy cấu hình học phí
        $cauHinhHocPhi = CauHinhHocPhi::where('ap_dung_tu_ngay', '<=', now())
            ->where('ap_dung_den_ngay', '>=', now())
            ->first();

        if (!$cauHinhHocPhi) {
            $this->command->warn('⚠️  Không tìm thấy cấu hình học phí, sử dụng giá mặc định');
            $donGiaTinChi = 500000;
            $phiDichVu = 1000000;
        } else {
            $donGiaTinChi = $cauHinhHocPhi->don_gia_tren_tin_chi;
            $phiDichVu = $cauHinhHocPhi->phi_dich_vu;
        }

        $hocKys = HocKy::all();
        $sinhViens = SinhVien::all();

        $count = 0;

        foreach ($hocKys as $hocKy) {
            foreach ($sinhViens as $sinhVien) {
                // Kiểm tra xem đã có học phí chưa
                if (HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
                    ->where('hoc_ky_id', $hocKy->id)
                    ->exists()) {
                    continue;
                }

                // Lấy tất cả lớp học phần sinh viên đã đăng ký trong học kỳ này
                $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                    ->whereHas('lopHocPhan', function ($q) use ($hocKy) {
                        $q->where('hoc_ky_id', $hocKy->id);
                    })
                    ->get();

                if ($lopHocPhanSinhViens->isEmpty()) {
                    continue;
                }

                // Tính tổng tín chỉ đăng ký (tối đa 24 tín chỉ/học kỳ)
                $tongTinChiDangKy = 0;
                foreach ($lopHocPhanSinhViens as $lopHocPhanSV) {
                    $monHoc = $lopHocPhanSV->lopHocPhan->monHoc;
                    $tongTinChiDangKy += $monHoc->so_tin_chi;
                    
                    // Giới hạn tối đa 24 tín chỉ
                    if ($tongTinChiDangKy >= 24) {
                        $tongTinChiDangKy = 24;
                        break;
                    }
                }

                // Đảm bảo không vượt quá 24 tín chỉ
                $tongTinChiDangKy = min($tongTinChiDangKy, 24);

                // Tính học phí
                $tongHocPhiMonHoc = $tongTinChiDangKy * $donGiaTinChi;
                $tongSoTien = $tongHocPhiMonHoc + $phiDichVu;

                // Random trạng thái thanh toán (70% đã đóng, 20% đóng một phần, 10% chưa đóng)
                $rand = rand(1, 100);
                $soTienDaDong = 0;
                $trangThai = 'chua_nop';

                if ($rand <= 70) {
                    // Đã đóng đủ
                    $soTienDaDong = $tongSoTien;
                    $trangThai = 'da_nop_du';
                } elseif ($rand <= 90) {
                    // Đóng một phần (50-90% tổng số tiền)
                    $soTienDaDong = round($tongSoTien * (rand(50, 90) / 100), -3);
                    $trangThai = 'da_nop_mot_phan';
                }

                $soTienConLai = $tongSoTien - $soTienDaDong;

                // Hạn đóng (30 ngày sau khi học kỳ bắt đầu)
                $hanDong = Carbon::parse($hocKy->ngay_bat_dau)->addDays(30);
                
                // Nếu quá hạn và chưa đóng đủ
                if ($soTienConLai > 0 && $hanDong < now()) {
                    $trangThai = 'qua_han';
                }

                // Ngày đóng lần cuối (nếu đã đóng)
                $ngayDongLanCuoi = null;
                if ($soTienDaDong > 0) {
                    $ngayDongLanCuoi = now()->subDays(rand(1, 60));
                }

                HocPhiHocKy::create([
                    'sinh_vien_id' => $sinhVien->id,
                    'hoc_ky_id' => $hocKy->id,
                    'tong_tin_chi_dang_ky' => $tongTinChiDangKy,
                    'tong_hoc_phi_mon_hoc' => $tongHocPhiMonHoc,
                    'phi_dich_vu' => $phiDichVu,
                    'tong_so_tien' => $tongSoTien,
                    'so_tien_da_dong' => $soTienDaDong,
                    'so_tien_con_lai' => $soTienConLai,
                    'han_dong' => $hanDong,
                    'ngay_dong_lan_cuoi' => $ngayDongLanCuoi,
                    'trang_thai' => $trangThai,
                    'ghi_chu' => $this->getGhiChu($trangThai),
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Đã tạo {$count} học phí học kỳ");
    }

    private function getGhiChu($trangThai)
    {
        $ghiChus = [
            'da_nop_du' => 'Đã thanh toán đủ học phí',
            'da_nop_mot_phan' => 'Đã thanh toán một phần',
            'chua_nop' => 'Chưa thanh toán',
            'qua_han' => 'Quá hạn thanh toán',
        ];

        return $ghiChus[$trangThai] ?? null;
    }
}

