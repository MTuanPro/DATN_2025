<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LichThi;
use App\Models\LichThiSinhVien;
use App\Models\LopHocPhanSinhVien;

class LichThiSinhVienSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        echo "🔄 Đang phân sinh viên vào lịch thi...\n";

        $lichThis = LichThi::with('lopHocPhan.lopHocPhanSinhViens.sinhVien')->get();

        if ($lichThis->isEmpty()) {
            echo "⚠️  Chưa có lịch thi nào!\n";
            return;
        }

        $count = 0;
        $soBaoDanhCounter = 1;

        foreach ($lichThis as $lichThi) {
            // Lấy danh sách sinh viên đã đăng ký lớp học phần này
            $lopHocPhanSinhViens = $lichThi->lopHocPhan->lopHocPhanSinhViens;

            if ($lopHocPhanSinhViens->isEmpty()) {
                echo "⚠️  Lịch thi ID {$lichThi->id} - Lớp chưa có sinh viên\n";
                continue;
            }

            foreach ($lopHocPhanSinhViens as $lhpsv) {
                // Kiểm tra đã tồn tại chưa
                $exists = LichThiSinhVien::where('lich_thi_id', $lichThi->id)
                    ->where('sinh_vien_id', $lhpsv->sinh_vien_id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Tạo bản ghi mới
                LichThiSinhVien::create([
                    'lich_thi_id' => $lichThi->id,
                    'sinh_vien_id' => $lhpsv->sinh_vien_id,
                    'phong_thi_id' => $lichThi->phong_thi_id, // Mặc định phòng của lịch thi
                    'so_bao_danh' => str_pad($soBaoDanhCounter, 4, '0', STR_PAD_LEFT), // SBD: 0001, 0002...
                    'trang_thai' => 'du_thi',
                ]);

                $count++;
                $soBaoDanhCounter++;
            }

            // Cập nhật số sinh viên dự thi
            $soSinhVien = LichThiSinhVien::where('lich_thi_id', $lichThi->id)->count();
            $lichThi->update(['so_sinh_vien_du_thi' => $soSinhVien]);
        }

        echo "✅ Đã phân {$count} sinh viên vào các lịch thi\n";
    }
}
