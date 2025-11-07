<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GiangVien;
use App\Models\Daotao\MonHoc;
use App\Models\LopHocPhan;
use App\Models\PhanCongGiangDay;

class FixPhanCongGiangVienSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔧 Sửa lại phân công giảng viên theo đúng chuyên môn...');

        // Xóa phân công cũ của GV001, GV002
        $giangViens = GiangVien::whereIn('ma_giang_vien', ['GV001', 'GV002'])->get();
        
        foreach ($giangViens as $gv) {
            $deleted = PhanCongGiangDay::where('giang_vien_id', $gv->id)->delete();
            $this->command->info("  → Xóa {$deleted} phân công cũ của {$gv->ma_giang_vien}");
        }

        // Lấy khoa CNTT (khoa_id = 1)
        $khoaCNTT = DB::table('khoa')->find(1);
        
        if (!$khoaCNTT) {
            $this->command->error('❌ Không tìm thấy Khoa CNTT (id=1)');
            return;
        }

        // Lấy môn học CNTT (bao gồm cả bị soft delete)
        $monHocCNTT = MonHoc::withTrashed()->where('khoa_id', 1)->limit(5)->get();
        
        // Restore các môn học bị xóa mềm
        foreach ($monHocCNTT as $mon) {
            if ($mon->trashed()) {
                $mon->restore();
                $this->command->info("  → Khôi phục môn: {$mon->ma_mon}");
            }
        }
        
        if ($monHocCNTT->isEmpty()) {
            $this->command->error('❌ Không có môn học thuộc Khoa CNTT');
            return;
        }

        $this->command->info("✅ Tìm thấy {$monHocCNTT->count()} môn học CNTT");

        // Lấy lớp học phần sử dụng môn CNTT
        $lopHocPhanCNTT = LopHocPhan::whereIn('mon_hoc_id', $monHocCNTT->pluck('id'))
            ->limit(10)
            ->get();

        if ($lopHocPhanCNTT->isEmpty()) {
            $this->command->warn('⚠️ Không có lớp học phần nào dùng môn CNTT. Tạo mới...');
            
            // Tạo lớp học phần mới
            $hocKy = DB::table('hoc_ky')->latest()->first();
            
            foreach ($monHocCNTT->take(3) as $index => $monHoc) {
                $lopHocPhan = LopHocPhan::create([
                    'ma_lop_hp' => $monHoc->ma_mon . '.HK' . $hocKy->id . '.CNTT',
                    'ten_lop_hp' => 'Lớp ' . $monHoc->ten_mon,
                    'mon_hoc_id' => $monHoc->id,
                    'hoc_ky_id' => $hocKy->id,
                    'so_luong_sv_toi_da' => 40,
                    'trang_thai' => 'dang_mo',
                ]);
                
                $lopHocPhanCNTT->push($lopHocPhan);
                $this->command->info("  ✅ Tạo lớp: {$lopHocPhan->ma_lop_hp}");
            }
        }

        // Phân công cho GV001 và GV002
        $count = 0;
        foreach ($giangViens as $index => $gv) {
            // Mỗi giảng viên được phân công 3-4 lớp
            $assigned = $lopHocPhanCNTT->slice($index * 3, 3);
            
            foreach ($assigned as $lop) {
                PhanCongGiangDay::create([
                    'lop_hoc_phan_id' => $lop->id,
                    'giang_vien_id' => $gv->id,
                    'vai_tro' => 'giang_vien_chinh',
                ]);
                $count++;
                
                $this->command->info("  ✅ Phân công {$gv->ma_giang_vien} → {$lop->ma_lop_hp}");
            }
        }

        $this->command->newLine();
        $this->command->info("🎉 HOÀN THÀNH!");
        $this->command->info("✅ Đã tạo {$count} phân công mới ĐÚNG CHUYÊN MÔN");
        $this->command->info("📌 Giảng viên Khoa CNTT chỉ dạy môn CNTT");
    }
}
