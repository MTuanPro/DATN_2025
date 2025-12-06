<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HocKy;
use App\Models\LopHocPhan;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\ChuongTrinhKhung;

class KiemTraLopHocPhanSinhVien extends Command
{
    protected $signature = 'kiemtra:lop-hoc-phan-sinh-vien';
    protected $description = 'Kiểm tra lớp học phần cho sinh viên các kỳ 2-8';

    public function handle()
    {
        $this->info('=== KIỂM TRA LỚP HỌC PHẦN CHO SINH VIÊN KỲ 2-8 ===');
        $this->newLine();

        // Lấy tất cả học kỳ
        $hocKys = HocKy::withCount('lopHocPhans')
            ->orderBy('nam_hoc')
            ->orderBy('ten_hoc_ky')
            ->get();

        $this->info('📊 BÁO CÁO LỚP HỌC PHẦN THEO HỌC KỲ:');
        $this->newLine();

        foreach ($hocKys as $hk) {
            $this->line("Học kỳ: {$hk->ten_hoc_ky} - {$hk->nam_hoc} (ID: {$hk->id})");
            $this->line("  - Số lớp học phần: {$hk->lop_hoc_phans_count}");
            $this->line("  - Học kỳ hiện tại: " . ($hk->la_hoc_ky_hien_tai ? 'Có' : 'Không'));
            $this->line("  - Mở đăng ký: " . ($hk->dang_mo_dang_ky ? 'Có' : 'Không'));
            $this->newLine();
        }

        // Kiểm tra sinh viên các kỳ
        $this->info('👥 SỐ LƯỢNG SINH VIÊN THEO KỲ:');
        $this->newLine();

        for ($ky = 1; $ky <= 8; $ky++) {
            $soSinhVien = SinhVien::where('ky_hien_tai', $ky)->count();
            $this->line("Kỳ {$ky}: {$soSinhVien} sinh viên");
        }

        $this->newLine();
        $this->info('🔍 KIỂM TRA LỚP HỌC PHẦN CHO SINH VIÊN KỲ 2-8:');
        $this->newLine();

        // Lấy tất cả chuyên ngành
        $chuyenNganhs = \App\Models\DaoTao\ChuyenNganh::all();

        foreach ($chuyenNganhs as $chuyenNganh) {
            $this->line("Chuyên ngành: {$chuyenNganh->ten_chuyen_nganh}");
            
            // Lấy chương trình khung
            $chuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $chuyenNganh->id)
                ->with('monHoc')
                ->get();

            // Kiểm tra cho từng kỳ 2-8
            for ($ky = 2; $ky <= 8; $ky++) {
                $monHocsTrongKy = $chuongTrinhKhung->where('hoc_ky_goi_y', $ky);
                
                if ($monHocsTrongKy->isEmpty()) {
                    continue;
                }

                $soMon = $monHocsTrongKy->count();
                $soLopCo = 0;
                $soLopThieu = 0;

                foreach ($monHocsTrongKy as $ctk) {
                    $monHoc = $ctk->monHoc;
                    if (!$monHoc) {
                        continue;
                    }

                    // Tìm học kỳ tương ứng với kỳ học này
                    // Giả sử kỳ 1 = Học kỳ 1 năm đầu, kỳ 2 = Học kỳ 2 năm đầu, ...
                    // Cần logic phức tạp hơn để map kỳ học với học kỳ
                    // Tạm thời kiểm tra tất cả học kỳ
                    $lopHocPhans = LopHocPhan::where('mon_hoc_id', $monHoc->id)
                        ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
                        ->get();

                    if ($lopHocPhans->count() > 0) {
                        $soLopCo++;
                    } else {
                        $soLopThieu++;
                    }
                }

                if ($soMon > 0) {
                    $this->line("  Kỳ {$ky}: {$soMon} môn học - {$soLopCo} môn có lớp, {$soLopThieu} môn thiếu lớp");
                }
            }
            
            $this->newLine();
        }

        // Tổng kết
        $this->info('📋 TỔNG KẾT:');
        $this->newLine();

        $tongLopHocPhan = LopHocPhan::whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])->count();
        $tongSinhVienKy2_8 = SinhVien::whereBetween('ky_hien_tai', [2, 8])->count();

        $this->line("Tổng lớp học phần đang mở: {$tongLopHocPhan}");
        $this->line("Tổng sinh viên kỳ 2-8: {$tongSinhVienKy2_8}");

        return 0;
    }
}

