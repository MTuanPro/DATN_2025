<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GiangVien;
use App\Models\Daotao\MonHoc;
use App\Models\LopHocPhan;
use App\Models\PhanCongGiangDay;
use App\Models\LichHocChiTiet;
use App\Models\Daotao\Khoa;
use App\Models\HocKy;
use App\Models\DanhMuc\PhongHoc;
use Carbon\Carbon;

class ChuyenMonSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Cập nhật dữ liệu ĐÚNG CHUYÊN MÔN...');

        // Lấy Khoa CNTT
        $khoaCNTT = Khoa::where('ma_khoa', 'CNTT')->first();
        if (!$khoaCNTT) {
            $this->command->error('❌ Không tìm thấy Khoa CNTT');
            return;
        }

        // Lấy học kỳ
        $hocKy = HocKy::latest()->first();
        if (!$hocKy) {
            $this->command->error('❌ Không có học kỳ');
            return;
        }

        // 1. CẬP NHẬT GIẢNG VIÊN → KHOA CNTT
        $this->command->info('👨‍🏫 Cập nhật giảng viên...');
        GiangVien::whereIn('ma_giang_vien', ['GV001', 'GV002'])
            ->update(['khoa_id' => $khoaCNTT->id]);
        
        $giangViens = GiangVien::whereIn('ma_giang_vien', ['GV001', 'GV002'])->get();
        $this->command->info('✅ Đã cập nhật ' . $giangViens->count() . ' giảng viên → Khoa CNTT');

        // 2. CẬP NHẬT MÔN HỌC → KHOA CNTT
        $this->command->info('📚 Cập nhật môn học...');
        
        // Lấy tất cả môn CNTT (CNTT01, CNTT02, ..., CNTT10)
        $updated = MonHoc::where('ma_mon', 'LIKE', 'CNTT%')
            ->update(['khoa_id' => $khoaCNTT->id]);
        
        $this->command->info("✅ Đã cập nhật {$updated} môn học → Khoa CNTT");

        // 3. TẠO LỊCH HỌC CHI TIẾT (nếu chưa có)
        $this->command->info('📅 Kiểm tra lịch học chi tiết...');
        
        $lopHocPhans = LopHocPhan::whereHas('monHoc', function($q) use ($khoaCNTT) {
            $q->where('khoa_id', $khoaCNTT->id);
        })->get();

        foreach ($lopHocPhans as $lop) {
            $existing = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
            
            if ($existing == 0) {
                // Tạo 10 buổi học mẫu
                $phongHoc = PhongHoc::first();
                if (!$phongHoc) continue;

                $startDate = Carbon::now()->startOfWeek();
                
                for ($i = 0; $i < 10; $i++) {
                    LichHocChiTiet::create([
                        'lich_hoc_co_dinh_id' => 1,
                        'lop_hoc_phan_id' => $lop->id,
                        'ngay_hoc' => $startDate->copy()->addWeeks($i)->addDays(1), // Thứ 3
                        'tiet_bat_dau' => 1,
                        'so_tiet' => 3,
                        'phong_hoc_id' => $phongHoc->id,
                        'hinh_thuc' => 'offline',
                        'trang_thai' => $i < 5 ? 'da_day' : 'chua_day',
                        'noi_dung_giang_day' => $i < 5 ? "Bài $i: Đã dạy" : null,
                    ]);
                }
                
                $this->command->info("  → Tạo 10 buổi học cho lớp {$lop->ma_lop_hp}");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 HOÀN THÀNH!');
        $this->command->info('✅ Giảng viên và Môn học ĐÃ CÙNG KHOA CNTT');
        $this->command->info('📌 Bây giờ có thể BẬT LẠI filter theo chuyên môn');
    }
}
