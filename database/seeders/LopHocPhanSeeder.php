<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;
use App\Models\PhanCongGiangDay;
use Carbon\Carbon;

class LopHocPhanSeeder extends Seeder
{
    /**
     * Tạo lớp học phần cho học kỳ 1
     * Phân công giảng viên phù hợp với môn học (từ bảng giang_vien_mon_hoc)
     * Không tạo lịch học
     */
    public function run(): void
    {
        // Chỉ lấy học kỳ 1
        $hocKy = HocKy::where('ten_hoc_ky', 'Học kỳ 1')->first();
        
        if (!$hocKy) {
            $this->command->error('Không tìm thấy học kỳ 1!');
            return;
        }

        // Xóa các lớp học phần cũ của học kỳ 1 (bao gồm cả soft deleted)
        $soLopCu = LopHocPhan::withTrashed()->where('hoc_ky_id', $hocKy->id)->count();
        if ($soLopCu > 0) {
            // Xóa phân công giảng viên trước
            $lopHocPhanIds = LopHocPhan::withTrashed()->where('hoc_ky_id', $hocKy->id)->pluck('id');
            PhanCongGiangDay::whereIn('lop_hoc_phan_id', $lopHocPhanIds)->delete();
            
            // Force delete lớp học phần (bao gồm cả soft deleted)
            LopHocPhan::withTrashed()->where('hoc_ky_id', $hocKy->id)->forceDelete();
            $this->command->info("🗑️  Đã xóa {$soLopCu} lớp học phần cũ của học kỳ 1");
        }

        // Lấy tất cả môn học
        $monHocs = MonHoc::all();
        
        if ($monHocs->isEmpty()) {
            $this->command->error('Không có môn học nào trong hệ thống!');
            return;
        }

        $count = 0;
        $countKhongCoGiangVien = 0;

        foreach ($monHocs as $monHoc) {
            // Lấy danh sách giảng viên có thể dạy môn này từ bảng giang_vien_mon_hoc
            $giangVienIds = DB::table('giang_vien_mon_hoc')
                ->where('mon_hoc_id', $monHoc->id)
                ->pluck('giang_vien_id')
                ->toArray();

            if (empty($giangVienIds)) {
                $countKhongCoGiangVien++;
                $this->command->warn("⚠️  Môn {$monHoc->ma_mon} - {$monHoc->ten_mon} không có giảng viên phù hợp");
                continue;
            }

            // Tạo 1-2 lớp cho mỗi môn học
            $soLop = rand(1, 2);
            
            for ($i = 1; $i <= $soLop; $i++) {
                // Random chọn một giảng viên từ danh sách giảng viên có thể dạy môn này
                $randomGiangVienId = $giangVienIds[array_rand($giangVienIds)];
                
                // Tạo mã lớp theo format: [MaMonHoc].[SoLop]
                $maLopHp = $monHoc->ma_mon . '.' . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                // Kiểm tra xem lớp đã tồn tại chưa (kiểm tra unique constraint: mon_hoc_id + hoc_ky_id + nhom_lop)
                if (LopHocPhan::withTrashed()
                    ->where('mon_hoc_id', $monHoc->id)
                    ->where('hoc_ky_id', $hocKy->id)
                    ->where('nhom_lop', $i)
                    ->exists()) {
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
                    'hinh_thuc' => $monHoc->hinh_thuc_day ?? 'offline',
                    'link_online' => null,
                    'ngay_bat_dau' => $ngayBatDau,
                    'ngay_ket_thuc' => $ngayKetThuc,
                    'trang_thai_lop' => 'mo_dang_ky',
                    'ghi_chu' => null,
                ]);

                // Phân công giảng viên chính (chỉ giảng viên có thể dạy môn này)
                PhanCongGiangDay::create([
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'giang_vien_id' => $randomGiangVienId,
                    'vai_tro' => 'giang_vien_chinh',
                    'ngay_phan_cong' => Carbon::now(),
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Đã tạo {$count} lớp học phần cho học kỳ: {$hocKy->ten_hoc_ky} - {$hocKy->nam_hoc}");
        
        if ($countKhongCoGiangVien > 0) {
            $this->command->warn("⚠️  Có {$countKhongCoGiangVien} môn học không có giảng viên phù hợp (chưa được phân công trong bảng giang_vien_mon_hoc)");
        }
        
        $this->command->info("📝 Lưu ý: Seeder này không tạo lịch học. Vui lòng tạo lịch học thủ công sau khi tạo lớp học phần.");
    }
}
