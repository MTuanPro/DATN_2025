<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;
use App\Models\LichHocChiTiet;
use App\Models\DanhMuc\PhongHoc;
use Carbon\Carbon;

class TaoBuoiHocSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📅 Tạo buổi học cho các lớp chưa có lịch...');
        $this->command->newLine();

        // Tạm thời disable foreign key và unique constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Lấy tất cả lớp học phần
        $lopHocPhans = LopHocPhan::all();
        
        if ($lopHocPhans->isEmpty()) {
            $this->command->error('❌ Không có lớp học phần nào');
            return;
        }

        $this->command->info("✅ Tìm thấy {$lopHocPhans->count()} lớp học phần");

        // Lấy phòng học mẫu
        $phongHocs = PhongHoc::limit(10)->get();
        
        if ($phongHocs->isEmpty()) {
            $this->command->warn('⚠️  Không có phòng học. Tạo buổi học không có phòng');
        }

        $totalCreated = 0;
        $lopProcessed = 0;

        foreach ($lopHocPhans as $lop) {
            // Kiểm tra xem lớp đã có buổi học chưa
            $existingCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
            
            if ($existingCount > 0) {
                // Đã có buổi học, bỏ qua
                continue;
            }

            $lopProcessed++;

            // Lấy giảng viên được phân công cho lớp này
            $phanCong = \App\Models\PhanCongGiangDay::where('lop_hoc_phan_id', $lop->id)
                ->where('vai_tro', 'giang_vien_chinh')
                ->first();
            
            if (!$phanCong) {
                // Không có phân công, bỏ qua
                $lopProcessed--;
                continue;
            }

            // Tạo 10 buổi học mẫu cho lớp này
            $startDate = Carbon::now()->startOfWeek()->addWeeks(-2); // Bắt đầu từ 2 tuần trước
            $phong = $phongHocs->isNotEmpty() ? $phongHocs->random() : null;

            for ($i = 0; $i < 10; $i++) {
                $ngayHoc = $startDate->copy()->addWeeks($i)->addDays(rand(0, 4)); // Thứ 2-6 ngẫu nhiên
                $tietBatDau = rand(1, 5);
                $tietKetThuc = $tietBatDau + 2; // 3 tiết
                
                // Tính giờ bắt đầu và kết thúc
                $gioBatDau = 7 + ($tietBatDau - 1) * 0.75; // Mỗi tiết 45 phút
                $gioKetThuc = 7 + ($tietKetThuc - 1) * 0.75;
                
                // Format giờ
                $gioBatDauStr = sprintf('%02d:%02d', floor($gioBatDau), ($gioBatDau - floor($gioBatDau)) * 60);
                $gioKetThucStr = sprintf('%02d:%02d', floor($gioKetThuc), ($gioKetThuc - floor($gioKetThuc)) * 60);
                
                // 50% buổi đã dạy, 50% chưa dạy
                $trangThai = $i < 5 ? 'da_day' : 'chua_day';
                
                try {
                    LichHocChiTiet::create([
                        'lich_hoc_co_dinh_id' => 1,
                        'lop_hoc_phan_id' => $lop->id,
                        'giang_vien_id' => $phanCong->giang_vien_id,
                        'ngay_hoc' => $ngayHoc,
                        'tiet_bat_dau' => $tietBatDau,
                        'tiet_ket_thuc' => $tietKetThuc,
                        'gio_bat_dau' => $gioBatDauStr,
                        'gio_ket_thuc' => $gioKetThucStr,
                        'phong_hoc_id' => $phong ? $phong->id : null,
                        'hinh_thuc' => 'offline',
                        'trang_thai' => $trangThai,
                        'noi_dung_giang_day' => $trangThai === 'da_day' ? "Nội dung buổi học số " . ($i + 1) : null,
                    ]);
                    
                    $totalCreated++;
                } catch (\Exception $e) {
                    // Bỏ qua lỗi unique constraint
                    continue;
                }
            }

            if ($lopProcessed % 20 == 0) {
                $this->command->info("  → Đã xử lý {$lopProcessed} lớp, tạo {$totalCreated} buổi học");
            }
        }

        $this->command->newLine();
        $this->command->info("🎉 HOÀN THÀNH!");
        $this->command->info("✅ Đã tạo {$totalCreated} buổi học cho {$lopProcessed} lớp");
        $this->command->info("📌 Mỗi lớp có 10 buổi (5 đã dạy, 5 chưa dạy)");
        
        // Bật lại constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
