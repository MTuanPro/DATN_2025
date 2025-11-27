<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHocPhan;
use App\Models\MonHoc;
use App\Models\HocKy;
use App\Models\GiangVien;
use App\Models\DaoTao\ChuongTrinhKhung;

class LopHocPhanSeeder extends Seeder
{
    /**
     * Tạo lớp học phần tự động từ chương trình khung
     */
    public function run(): void
    {
        $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        
        if (!$hocKy) {
            $this->command->error('Không tìm thấy học kỳ hiện tại!');
            return;
        }

        // Lấy tất cả môn học từ chương trình khung (unique)
        $monHocIds = ChuongTrinhKhung::distinct('mon_hoc_id')
            ->pluck('mon_hoc_id')
            ->toArray();

        $giangViens = GiangVien::all();
        
        if ($giangViens->isEmpty()) {
            $this->command->error('Không có giảng viên nào trong hệ thống!');
            return;
        }

        $count = 0;

        foreach ($monHocIds as $monHocId) {
            $monHoc = MonHoc::find($monHocId);
            
            if (!$monHoc) continue;

            // Tạo 1-2 lớp cho mỗi môn học
            $soLop = rand(1, 2);
            
            for ($i = 1; $i <= $soLop; $i++) {
                // Random giảng viên
                $giangVien = $giangViens->random();
                
                // Tạo mã lớp theo format: [MaMonHoc].[SoLop]
                $maLop = $monHoc->ma_mon_hoc . '.' . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                // Kiểm tra xem lớp đã tồn tại chưa
                if (LopHocPhan::where('ma_lop', $maLop)->exists()) {
                    continue;
                }

                LopHocPhan::create([
                    'ma_lop' => $maLop,
                    'mon_hoc_id' => $monHoc->id,
                    'hoc_ky_id' => $hocKy->id,
                    'giang_vien_id' => $giangVien->id,
                    'si_so_toi_da' => rand(30, 60),
                    'si_so_hien_tai' => 0,
                    'trang_thai_lop' => 'mo_dang_ky', // Mở đăng ký
                    'phong_hoc' => 'P' . rand(101, 599),
                    'thu_hoc' => rand(2, 6), // Thứ 2 - Thứ 6
                    'tiet_bat_dau' => rand(1, 8),
                    'so_tiet' => $monHoc->so_tin_chi * 15, // 1 tín chỉ = 15 tiết
                    'ghi_chu' => null,
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Đã tạo {$count} lớp học phần cho học kỳ: {$hocKy->ten_hoc_ky} - {$hocKy->nam_hoc}");
    }
}
