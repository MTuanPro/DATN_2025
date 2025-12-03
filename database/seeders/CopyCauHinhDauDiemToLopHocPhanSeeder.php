<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHocPhan;
use App\Models\CauHinhDauDiem;
use App\Models\CauHinhDauDiemMacDinh;

class CopyCauHinhDauDiemToLopHocPhanSeeder extends Seeder
{
    /**
     * Copy cấu hình đầu điểm từ môn học sang tất cả lớp học phần hiện có
     */
    public function run(): void
    {
        $lopHocPhans = LopHocPhan::with('monHoc')->get();
        
        $count = 0;
        $countSkipped = 0;

        foreach ($lopHocPhans as $lopHocPhan) {
            // Kiểm tra xem lớp học phần đã có cấu hình chưa
            $existingCauHinh = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhan->id)->exists();
            
            if ($existingCauHinh) {
                $countSkipped++;
                continue;
            }

            // Lấy cấu hình mặc định của môn học
            $cauHinhMacDinhs = CauHinhDauDiemMacDinh::where('mon_hoc_id', $lopHocPhan->mon_hoc_id)->get();

            if ($cauHinhMacDinhs->isEmpty()) {
                // Nếu môn học chưa có cấu hình mặc định, tạo cấu hình mặc định
                $cauHinhMacDinh = [
                    ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
                    ['ten_dau_diem' => 'Giữa kỳ', 'ty_le' => 30, 'so_cot' => 1],
                    ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 60, 'so_cot' => 1],
                ];

                foreach ($cauHinhMacDinh as $cauHinh) {
                    CauHinhDauDiem::create([
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'ten_dau_diem' => $cauHinh['ten_dau_diem'],
                        'ty_le' => $cauHinh['ty_le'],
                        'so_cot' => $cauHinh['so_cot'],
                    ]);
                }
            } else {
                // Copy từ cấu hình mặc định
                foreach ($cauHinhMacDinhs as $cauHinhMacDinh) {
                    CauHinhDauDiem::create([
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'ten_dau_diem' => $cauHinhMacDinh->ten_dau_diem,
                        'ty_le' => $cauHinhMacDinh->ty_le,
                        'so_cot' => $cauHinhMacDinh->so_cot,
                    ]);
                }
            }

            $count++;
        }

        $this->command->info("✅ Đã copy cấu hình đầu điểm cho {$count} lớp học phần");
        if ($countSkipped > 0) {
            $this->command->info("⏭️  Đã bỏ qua {$countSkipped} lớp học phần (đã có cấu hình)");
        }
    }
}
