<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;

class CauHinhDauDiemSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        echo "🔄 Đang cấu hình đầu điểm...\n";

        $lopHocPhans = LopHocPhan::all();

        if ($lopHocPhans->isEmpty()) {
            echo "⚠️  Cần có dữ liệu LopHocPhan trước!\n";
            return;
        }

        // 3 template cấu hình điểm
        $templates = [
            [ // Template 1: Cơ bản
                ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
                ['ten_dau_diem' => 'Giữa kỳ', 'ty_le' => 30, 'so_cot' => 1],
                ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 60, 'so_cot' => 1],
            ],
            [ // Template 2: Có thực hành
                ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
                ['ten_dau_diem' => 'Thực hành', 'ty_le' => 20, 'so_cot' => 3],
                ['ten_dau_diem' => 'Giữa kỳ', 'ty_le' => 20, 'so_cot' => 1],
                ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 50, 'so_cot' => 1],
            ],
            [ // Template 3: Có tiểu luận
                ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
                ['ten_dau_diem' => 'Bài tập', 'ty_le' => 20, 'so_cot' => 2],
                ['ten_dau_diem' => 'Tiểu luận', 'ty_le' => 20, 'so_cot' => 1],
                ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 50, 'so_cot' => 1],
            ],
        ];

        $cauHinhDauDiems = [];
        $count = 0;

        foreach ($lopHocPhans as $lop) {
            // Kiểm tra đã cấu hình chưa
            $daCauHinh = DB::table('cau_hinh_dau_diem')
                ->where('lop_hoc_phan_id', $lop->id)
                ->exists();

            if ($daCauHinh) {
                continue;
            }

            // Chọn random 1 template
            $template = $templates[array_rand($templates)];

            foreach ($template as $config) {
                $cauHinhDauDiems[] = [
                    'lop_hoc_phan_id' => $lop->id,
                    'ten_dau_diem' => $config['ten_dau_diem'],
                    'ty_le' => $config['ty_le'],
                    'so_cot' => $config['so_cot'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $count++;
        }

        if (!empty($cauHinhDauDiems)) {
            DB::table('cau_hinh_dau_diem')->insert($cauHinhDauDiems);
            echo "✅ Đã cấu hình điểm cho " . $count . " lớp học phần (" . count($cauHinhDauDiems) . " đầu điểm)\n";
        } else {
            echo "ℹ️  Không có cấu hình điểm mới để tạo\n";
        }
    }
}
