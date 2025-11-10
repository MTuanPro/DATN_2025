<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DangKyMonHoc;
use App\Models\LopHocPhanSinhVien;
use App\Models\KetQuaHocTap;
use App\Models\HocPhiHocKy;
use App\Models\CanhBaoHocVu;
use App\Models\HocKy;
use App\Models\DaoTao\SinhVien;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        $hocKy = HocKy::first();
        if (!$hocKy) {
            $this->command->error('Không tìm thấy học kỳ!');
            return;
        }

        // 1. Tạo DangKyMonHoc từ LopHocPhanSinhVien
        $this->command->info('Đang tạo dữ liệu Đăng ký môn học...');
        $lhpsv = LopHocPhanSinhVien::with('lopHocPhan')->limit(200)->get();
        foreach ($lhpsv as $item) {
            DangKyMonHoc::create([
                'sinh_vien_id' => $item->sinh_vien_id,
                'lop_hoc_phan_id' => $item->lop_hoc_phan_id,
                'hoc_ky_id' => $hocKy->id,
                'trang_thai' => 'da_duyet',
                'thoi_gian_dang_ky' => now(),
            ]);
        }
        $this->command->info('✅ Đã tạo ' . DangKyMonHoc::count() . ' đăng ký môn học');

        // 2. Tạo KetQuaHocTap (điểm) cho các sinh viên
        $this->command->info('Đang tạo dữ liệu Kết quả học tập...');
        $lhpsv2 = LopHocPhanSinhVien::limit(150)->get();
        foreach ($lhpsv2 as $item) {
            $diem = rand(40, 100) / 10; // Điểm từ 4.0 đến 10.0
            KetQuaHocTap::create([
                'lop_hoc_phan_sinh_vien_id' => $item->id,
                'diem_he_10' => $diem,
            ]);
        }
        $this->command->info('✅ Đã tạo ' . KetQuaHocTap::count() . ' kết quả học tập');

        // 3. Tạo HocPhiHocKy
        $this->command->info('Đang tạo dữ liệu Học phí...');
        $sinhViens = SinhVien::limit(80)->get();
        foreach ($sinhViens as $sv) {
            $tongTien = rand(5000000, 15000000);
            $daDong = rand(0, $tongTien);
            HocPhiHocKy::create([
                'sinh_vien_id' => $sv->id,
                'hoc_ky_id' => $hocKy->id,
                'tong_hoc_phi_mon_hoc' => $tongTien,
                'phi_dich_vu' => 0,
                'tong_so_tien' => $tongTien,
                'so_tien_da_dong' => $daDong,
                'so_tien_con_lai' => $tongTien - $daDong,
                'han_dong' => now()->addDays(30),
                'trang_thai' => $daDong >= $tongTien ? 'da_nop_du' : ($daDong > 0 ? 'da_nop_mot_phan' : 'chua_nop'),
            ]);
        }
        $this->command->info('✅ Đã tạo ' . HocPhiHocKy::count() . ' học phí');

        // 4. Tạo CanhBaoHocVu
        $this->command->info('Đang tạo dữ liệu Cảnh báo học vụ...');
        $sinhViensCanh = SinhVien::limit(10)->get();
        foreach ($sinhViensCanh as $sv) {
            CanhBaoHocVu::create([
                'sinh_vien_id' => $sv->id,
                'hoc_ky_id' => $hocKy->id,
                'loai_canh_bao' => ['diem_thap', 'vang_nhieu', 'no_hoc_phi'][rand(0, 2)],
                'muc_do' => ['canh_cao', 'dinh_chi'][rand(0, 1)],
                'ly_do' => 'Cảnh báo tự động từ hệ thống',
                'ngay_canh_bao' => now()->subDays(rand(1, 30)),
                'da_xu_ly' => rand(0, 1),
            ]);
        }
        $this->command->info('✅ Đã tạo ' . CanhBaoHocVu::count() . ' cảnh báo học vụ');

        $this->command->info('');
        $this->command->info('🎉 Hoàn tất! Dashboard đã có dữ liệu.');
    }
}
