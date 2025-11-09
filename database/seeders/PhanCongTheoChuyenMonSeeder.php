<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GiangVien;
use App\Models\Daotao\MonHoc;
use App\Models\LopHocPhan;
use App\Models\PhanCongGiangDay;

class PhanCongTheoChuyenMonSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔧 Phân công TẤT CẢ giảng viên theo đúng chuyên môn...');
        $this->command->newLine();

        // Lấy tất cả giảng viên có khoa_id
        $giangViens = GiangVien::whereNotNull('khoa_id')->get();
        
        if ($giangViens->isEmpty()) {
            $this->command->error('❌ Không có giảng viên nào có khoa_id');
            return;
        }

        $this->command->info("✅ Tìm thấy {$giangViens->count()} giảng viên có chuyên môn");
        $this->command->newLine();

        $totalAssigned = 0;
        $totalRestored = 0;

        // Nhóm giảng viên theo khoa
        $giangViensByKhoa = $giangViens->groupBy('khoa_id');

        foreach ($giangViensByKhoa as $khoaId => $gvList) {
            $khoa = DB::table('khoa')->find($khoaId);
            
            if (!$khoa) {
                $this->command->warn("⚠️  Bỏ qua khoa_id = {$khoaId} (không tồn tại)");
                continue;
            }

            $this->command->info("📚 Khoa: {$khoa->ten_khoa} ({$gvList->count()} giảng viên)");

            // Lấy môn học của khoa này (bao gồm soft delete)
            $monHocs = MonHoc::withTrashed()->where('khoa_id', $khoaId)->get();
            
            if ($monHocs->isEmpty()) {
                $this->command->warn("  ⚠️  Không có môn học nào thuộc khoa này");
                continue;
            }

            // Restore các môn bị soft delete
            foreach ($monHocs as $mon) {
                if ($mon->trashed()) {
                    $mon->restore();
                    $totalRestored++;
                }
            }

            // Lấy lớp học phần sử dụng môn của khoa này
            $lopHocPhans = LopHocPhan::whereIn('mon_hoc_id', $monHocs->pluck('id'))
                ->get();

            if ($lopHocPhans->isEmpty()) {
                $this->command->warn("  ⚠️  Không có lớp học phần nào");
                continue;
            }

            $this->command->info("  → {$monHocs->count()} môn học, {$lopHocPhans->count()} lớp học phần");

            // Phân công cho từng giảng viên
            $lopsPerGV = max(1, intval($lopHocPhans->count() / $gvList->count()));
            
            foreach ($gvList as $index => $gv) {
                // Xóa phân công cũ không đúng chuyên môn
                $deleted = PhanCongGiangDay::where('giang_vien_id', $gv->id)
                    ->whereHas('lopHocPhan.monHoc', function($q) use ($khoaId) {
                        $q->where('khoa_id', '!=', $khoaId);
                    })
                    ->delete();

                if ($deleted > 0) {
                    $this->command->warn("    → Xóa {$deleted} phân công SAI chuyên môn của {$gv->ma_giang_vien}");
                }

                // Lấy danh sách lớp đã phân công (đúng chuyên môn)
                $assignedIds = PhanCongGiangDay::where('giang_vien_id', $gv->id)
                    ->pluck('lop_hoc_phan_id')
                    ->toArray();

                // Phân công thêm nếu chưa đủ
                $needMore = $lopsPerGV - count($assignedIds);
                
                if ($needMore > 0) {
                    $availableLops = $lopHocPhans
                        ->whereNotIn('id', $assignedIds)
                        ->random(min($needMore, $lopHocPhans->whereNotIn('id', $assignedIds)->count()));

                    foreach ($availableLops as $lop) {
                        PhanCongGiangDay::create([
                            'lop_hoc_phan_id' => $lop->id,
                            'giang_vien_id' => $gv->id,
                            'vai_tro' => 'giang_vien_chinh',
                        ]);
                        $totalAssigned++;
                        $assignedIds[] = $lop->id;
                    }
                }

                $this->command->info("    ✅ {$gv->ma_giang_vien} ({$gv->ho_ten}): " . count($assignedIds) . " lớp");
            }

            $this->command->newLine();
        }

        $this->command->info("🎉 HOÀN THÀNH!");
        $this->command->info("✅ Khôi phục {$totalRestored} môn học");
        $this->command->info("✅ Tạo {$totalAssigned} phân công mới ĐÚNG chuyên môn");
        $this->command->info("📌 Tất cả giảng viên chỉ được phân công môn học thuộc khoa của mình");
    }
}
