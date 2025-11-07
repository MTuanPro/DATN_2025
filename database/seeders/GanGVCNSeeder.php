<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\SinhVien;
use App\Models\GiangVien;
use Illuminate\Support\Facades\DB;

class GanGVCNSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Bắt đầu gán GVCN cho các lớp hành chính...');

        // Lấy tất cả lớp hành chính chưa có GVCN
        $lopHanhChinhs = LopHanhChinh::with(['nganh.khoa'])
            ->whereNull('giang_vien_chu_nhiem_id')
            ->get();

        if ($lopHanhChinhs->isEmpty()) {
            $this->command->warn('Không có lớp nào cần gán GVCN!');
            return;
        }

        $this->command->info("Tìm thấy {$lopHanhChinhs->count()} lớp cần gán GVCN");

        $tongLopGan = 0;
        $tongSVCapNhat = 0;

        foreach ($lopHanhChinhs as $lop) {
            // Lấy khoa của ngành này
            $khoaId = $lop->nganh->khoa_id ?? null;

            if (!$khoaId) {
                $this->command->warn("Lớp {$lop->ma_lop} không có thông tin khoa, bỏ qua!");
                continue;
            }

            // Lấy giảng viên thuộc cùng khoa
            $giangVien = GiangVien::where('khoa_id', $khoaId)
                ->inRandomOrder()
                ->first();

            // Nếu không có GV cùng khoa, lấy GV bất kỳ
            if (!$giangVien) {
                $giangVien = GiangVien::inRandomOrder()->first();
                $this->command->warn("Lớp {$lop->ma_lop}: Không có GV cùng khoa, gán GV ngẫu nhiên");
            }

            if (!$giangVien) {
                $this->command->error("Không tìm thấy giảng viên nào trong hệ thống!");
                continue;
            }

            // Gán GVCN cho lớp
            $lop->update([
                'giang_vien_chu_nhiem_id' => $giangVien->id
            ]);

            // Cập nhật GVCN cho tất cả sinh viên trong lớp
            $soSVCapNhat = SinhVien::where('lop_hanh_chinh_id', $lop->id)
                ->update([
                    'giang_vien_chu_nhiem_id' => $giangVien->id
                ]);

            $tongLopGan++;
            $tongSVCapNhat += $soSVCapNhat;

            $this->command->info("✓ Lớp {$lop->ma_lop} -> GVCN: {$giangVien->ho_ten} ({$soSVCapNhat} SV)");
        }

        $this->command->info('');
        $this->command->info("=== HOÀN THÀNH ===");
        $this->command->info("✓ Đã gán GVCN cho {$tongLopGan} lớp hành chính");
        $this->command->info("✓ Đã cập nhật GVCN cho {$tongSVCapNhat} sinh viên");
        $this->command->info('');

        // Thống kê chi tiết
        $this->showStatistics();
    }

    /**
     * Hiển thị thống kê GVCN
     */
    private function showStatistics(): void
    {
        $this->command->info("=== THỐNG KÊ GVCN ===");

        $giangViens = GiangVien::withCount('lopHanhChinhChuNhiem')->get();

        foreach ($giangViens as $gv) {
            $soLop = $gv->lop_hanh_chinh_chu_nhiem_count;
            if ($soLop > 0) {
                $this->command->info("- {$gv->ho_ten} ({$gv->ma_giang_vien}): {$soLop} lớp");
            }
        }
    }
}
