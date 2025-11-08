<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LichHocChiTiet;
use App\Models\LopHocPhanSinhVien;
use App\Models\DiemDanh;
use Carbon\Carbon;

class TaoDiemDanhSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('✅ Tạo dữ liệu điểm danh cho các buổi đã dạy...');
        $this->command->newLine();

        // Lấy các buổi đã dạy
        $buoiHocDaDay = LichHocChiTiet::where('trang_thai', 'da_day')->get();
        
        if ($buoiHocDaDay->isEmpty()) {
            $this->command->error('❌ Không có buổi học nào đã dạy');
            return;
        }

        $this->command->info("✅ Tìm thấy {$buoiHocDaDay->count()} buổi học đã dạy");

        $totalCreated = 0;

        foreach ($buoiHocDaDay as $buoiHoc) {
            // Lấy danh sách sinh viên trong lớp
            $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->get();

            if ($sinhViens->isEmpty()) {
                continue;
            }

            // Tạo điểm danh cho mỗi sinh viên
            foreach ($sinhViens as $svLop) {
                // Kiểm tra đã có điểm danh chưa
                $existing = DiemDanh::where('lich_hoc_chi_tiet_id', $buoiHoc->id)
                    ->where('lop_hoc_phan_sinh_vien_id', $svLop->id)
                    ->exists();

                if ($existing) {
                    continue;
                }

                // Random trạng thái điểm danh: 80% có mặt, 15% vắng, 5% đi trễ
                $rand = rand(1, 100);
                if ($rand <= 80) {
                    $trangThai = 'co_mat';
                } elseif ($rand <= 95) {
                    $trangThai = 'vang';
                } else {
                    $trangThai = 'di_tre';
                }

                try {
                    DiemDanh::create([
                        'lich_hoc_chi_tiet_id' => $buoiHoc->id,
                        'lop_hoc_phan_sinh_vien_id' => $svLop->id,
                        'trang_thai' => $trangThai,
                        'thoi_gian_diem_danh' => $buoiHoc->ngay_hoc->format('Y-m-d') . ' ' . $buoiHoc->gio_bat_dau,
                        'ghi_chu' => $trangThai === 'vang' ? 'Vắng không phép' : null,
                    ]);

                    $totalCreated++;
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($totalCreated > 0 && $totalCreated % 100 == 0) {
                $this->command->info("  → Đã tạo {$totalCreated} điểm danh...");
            }
        }

        $this->command->newLine();
        $this->command->info("🎉 HOÀN THÀNH!");
        $this->command->info("✅ Đã tạo {$totalCreated} bản ghi điểm danh");
        $this->command->info("📌 Tỷ lệ: 80% có mặt, 15% vắng, 5% đi trễ");
    }
}
