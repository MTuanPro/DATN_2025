<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;
use App\Models\GiangVien;
use App\Models\DaoTao;
use Carbon\Carbon;

class LopHocPhanGiangVienSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        echo "🔄 Đang phân công giảng viên...\n";

        $lopHocPhans = LopHocPhan::all();
        $giangViens = GiangVien::all();
        $daoTaos = DaoTao::all();

        if ($lopHocPhans->isEmpty() || $giangViens->isEmpty()) {
            echo "⚠️  Cần có dữ liệu LopHocPhan và GiangVien trước!\n";
            return;
        }

        $nguoiPhanCongId = $daoTaos->first()?->id;
        $phanCongs = [];

        foreach ($lopHocPhans as $lop) {
            // Kiểm tra đã phân công chưa
            $daPhanCong = DB::table('lop_hoc_phan_giang_vien')
                ->where('lop_hoc_phan_id', $lop->id)
                ->exists();

            if ($daPhanCong) {
                continue;
            }

            // Danh sách giảng viên đã phân công cho lớp này
            $daPhanCongIds = [];

            // Mỗi lớp có 1 giảng viên chính
            $giangVienChinh = $giangViens->random();
            $daPhanCongIds[] = $giangVienChinh->id;

            $phanCongs[] = [
                'lop_hoc_phan_id' => $lop->id,
                'giang_vien_id' => $giangVienChinh->id,
                'vai_tro' => 'giang_vien_chinh',
                'phan_cong_giang_day' => 'Phụ trách giảng dạy toàn bộ môn học',
                'ngay_phan_cong' => Carbon::now()->subDays(rand(1, 30)),
                'nguoi_phan_cong_id' => $nguoiPhanCongId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 30% lớp có giảng viên phụ
            if (rand(1, 100) <= 30) {
                $giangVienPhu = $giangViens->whereNotIn('id', $daPhanCongIds)->random();
                $daPhanCongIds[] = $giangVienPhu->id;

                $phanCongs[] = [
                    'lop_hoc_phan_id' => $lop->id,
                    'giang_vien_id' => $giangVienPhu->id,
                    'vai_tro' => 'giang_vien_phu',
                    'phan_cong_giang_day' => 'Hỗ trợ giảng dạy một số chương',
                    'ngay_phan_cong' => Carbon::now()->subDays(rand(1, 30)),
                    'nguoi_phan_cong_id' => $nguoiPhanCongId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 20% lớp có trợ giảng
            if (rand(1, 100) <= 20) {
                $troGiang = $giangViens->whereNotIn('id', $daPhanCongIds)->random();

                $phanCongs[] = [
                    'lop_hoc_phan_id' => $lop->id,
                    'giang_vien_id' => $troGiang->id,
                    'vai_tro' => 'tro_giang',
                    'phan_cong_giang_day' => 'Hỗ trợ thực hành và chấm bài tập',
                    'ngay_phan_cong' => Carbon::now()->subDays(rand(1, 30)),
                    'nguoi_phan_cong_id' => $nguoiPhanCongId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($phanCongs)) {
            DB::table('lop_hoc_phan_giang_vien')->insert($phanCongs);
            echo "✅ Đã phân công " . count($phanCongs) . " giảng viên\n";
        } else {
            echo "ℹ️  Không có phân công mới để tạo\n";
        }
    }
}
