<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;
use App\Models\GiangVien;
use Carbon\Carbon;

class PhanCongGiangVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả giảng viên và lớp học phần
        $giangViens = GiangVien::all();
        $lopHocPhans = LopHocPhan::all();

        if ($giangViens->isEmpty() || $lopHocPhans->isEmpty()) {
            $this->command->warn('Không có dữ liệu giảng viên hoặc lớp học phần để phân công!');
            return;
        }

        $this->command->info("Bắt đầu phân công {$giangViens->count()} giảng viên cho {$lopHocPhans->count()} lớp học phần...");

        $phanCongData = [];
        $count = 0;

        // Phân công ngẫu nhiên giảng viên cho mỗi lớp học phần
        foreach ($lopHocPhans as $lhp) {
            // Mỗi lớp có 1-2 giảng viên (chủ yếu 1, 20% có 2)
            $soGiangVien = rand(1, 100) <= 80 ? 1 : 2;
            
            // Lấy ngẫu nhiên giảng viên
            $giangViensChon = $giangViens->random($soGiangVien);

            foreach ($giangViensChon as $index => $gv) {
                $phanCongData[] = [
                    'lop_hoc_phan_id' => $lhp->id,
                    'giang_vien_id' => $gv->id,
                    'vai_tro' => $index === 0 ? 'giang_vien_chinh' : 'giang_vien_phu',
                    'phan_cong_giang_day' => $this->generatePhanCongNoiDung(),
                    'ngay_phan_cong' => $lhp->created_at ?? Carbon::now()->subDays(rand(1, 30)),
                    'nguoi_phan_cong_id' => 1, // Giả sử admin có ID = 1
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $count++;
            }
        }

        // Insert theo batch để nhanh hơn
        $chunks = array_chunk($phanCongData, 500);
        foreach ($chunks as $chunk) {
            DB::table('lop_hoc_phan_giang_vien')->insert($chunk);
        }

        $this->command->info("✅ Đã phân công thành công {$count} bản ghi!");
        $this->command->info("📊 Trung bình: " . round($count / $lopHocPhans->count(), 2) . " giảng viên/lớp");
    }

    /**
     * Tạo nội dung phân công ngẫu nhiên
     */
    private function generatePhanCongNoiDung(): ?string
    {
        $templates = [
            'Giảng dạy toàn bộ nội dung môn học',
            'Giảng dạy lý thuyết và thực hành',
            'Phụ trách phần lý thuyết',
            'Phụ trách phần thực hành',
            'Giảng dạy và chấm bài tập',
            'Giảng dạy chương 1-5',
            'Giảng dạy chương 6-10',
            'Phụ trách nhóm 1',
            'Phụ trách nhóm 2',
            null, // Một số không có ghi chú cụ thể
        ];

        return $templates[array_rand($templates)];
    }
}
