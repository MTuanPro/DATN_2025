<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LichHocChiTiet;
use App\Models\PhanCongGiangDay;

class UpdateGiangVienInLichHocSeeder extends Seeder
{
    /**
     * Cập nhật giang_vien_id trong lich_hoc_chi_tiet theo phân công mới
     */
    public function run(): void
    {
        echo "🔄 Đang cập nhật giảng viên trong lịch học chi tiết...\n";

        // Lấy tất cả buổi học
        $lichHocs = LichHocChiTiet::all();
        
        echo "✅ Tìm thấy {$lichHocs->count()} buổi học\n";

        $updated = 0;
        $notFound = 0;

        foreach ($lichHocs as $lichHoc) {
            // Lấy phân công giảng viên chính cho lớp này
            $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lichHoc->lop_hoc_phan_id)
                ->where('vai_tro', 'giang_vien_chinh')
                ->first();

            if ($phanCong) {
                // Cập nhật giang_vien_id
                $oldGvId = $lichHoc->giang_vien_id;
                $newGvId = $phanCong->giang_vien_id;

                if ($oldGvId != $newGvId) {
                    $lichHoc->giang_vien_id = $newGvId;
                    $lichHoc->save();
                    $updated++;
                }
            } else {
                $notFound++;
                echo "⚠️  Không tìm thấy phân công cho lớp ID: {$lichHoc->lop_hoc_phan_id}\n";
            }
        }

        echo "\n✅ Đã cập nhật {$updated} buổi học\n";
        if ($notFound > 0) {
            echo "⚠️  {$notFound} buổi học không tìm thấy phân công\n";
        }

        // Thống kê
        $thongKe = DB::select("
            SELECT 
                gv.ma_giang_vien,
                gv.ho_ten,
                gv.chuyen_mon,
                COUNT(lhct.id) as so_buoi_hoc
            FROM lich_hoc_chi_tiet lhct
            JOIN giang_vien gv ON lhct.giang_vien_id = gv.id
            GROUP BY gv.id, gv.ma_giang_vien, gv.ho_ten, gv.chuyen_mon
            ORDER BY gv.ma_giang_vien
        ");

        echo "\n📊 Thống kê buổi học theo giảng viên:\n";
        foreach ($thongKe as $tk) {
            echo "   - {$tk->ma_giang_vien} ({$tk->ho_ten}): {$tk->so_buoi_hoc} buổi\n";
        }
    }
}
