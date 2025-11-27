<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHocPhan;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;
use App\Models\GiangVien;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\PhanCongGiangDay;
use Carbon\Carbon;

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
                $maLopHp = $monHoc->ma_mon_hoc . '.' . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                // Kiểm tra xem lớp đã tồn tại chưa
                if (LopHocPhan::where('ma_lop_hp', $maLopHp)->exists()) {
                    continue;
                }

                // Tính ngày bắt đầu và kết thúc dựa trên học kỳ
                $ngayBatDau = Carbon::parse($hocKy->ngay_bat_dau);
                $ngayKetThuc = Carbon::parse($hocKy->ngay_ket_thuc);

                // Tạo lớp học phần
                $lopHocPhan = LopHocPhan::create([
                    'ma_lop_hp' => $maLopHp,
                    'ten_lop_hp' => $monHoc->ten_mon . ' - Nhóm ' . $i,
                    'mon_hoc_id' => $monHoc->id,
                    'hoc_ky_id' => $hocKy->id,
                    'nhom_lop' => $i,
                    'suc_chua' => rand(30, 60),
                    'so_luong_dang_ky' => 0,
                    'so_luong_toi_thieu' => 10,
                    'hinh_thuc' => 'offline',
                    'link_online' => null,
                    'ngay_bat_dau' => $ngayBatDau,
                    'ngay_ket_thuc' => $ngayKetThuc,
                    'trang_thai_lop' => 'mo_dang_ky',
                    'ghi_chu' => null,
                ]);

                // Phân công giảng viên chính
                PhanCongGiangDay::create([
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'giang_vien_id' => $giangVien->id,
                    'vai_tro' => 'giang_vien_chinh',
                    'ngay_phan_cong' => Carbon::now(),
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Đã tạo {$count} lớp học phần cho học kỳ: {$hocKy->ten_hoc_ky} - {$hocKy->nam_hoc}");
    }
}
