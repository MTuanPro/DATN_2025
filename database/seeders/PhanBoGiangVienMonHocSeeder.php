<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhanBoGiangVienMonHocSeeder extends Seeder
{
    /**
     * Phân bổ giảng viên cho tất cả môn học
     * - Đảm bảo mỗi môn có ít nhất 2 giảng viên
     * - Chia đều giữa các khoa
     */
    public function run(): void
    {
        // Lấy tất cả môn học
        $monHocs = DB::table('mon_hoc')->get();
        
        // Lấy tất cả giảng viên
        $giangViens = DB::table('giang_vien')->get();
        
        if ($monHocs->isEmpty()) {
            echo "❌ Không có môn học nào trong hệ thống\n";
            return;
        }
        
        if ($giangViens->isEmpty()) {
            echo "❌ Không có giảng viên nào trong hệ thống\n";
            echo "   💡 Vui lòng chạy GiangVienSeeder trước\n";
            return;
        }
        
        // Xóa dữ liệu cũ trong bảng pivot
        DB::table('giang_vien_mon_hoc')->truncate();
        
        $totalAssigned = 0;
        $monHocKhongDuGiangVien = [];
        
        // Nhóm giảng viên theo khoa
        $giangVienTheoKhoa = [];
        foreach ($giangViens as $gv) {
            $khoaId = $gv->khoa_id;
            if (!isset($giangVienTheoKhoa[$khoaId])) {
                $giangVienTheoKhoa[$khoaId] = [];
            }
            $giangVienTheoKhoa[$khoaId][] = $gv;
        }
        
        // Đếm số môn học đã được phân bổ cho mỗi giảng viên (để chia đều)
        $soMonHocCuaGiangVien = [];
        foreach ($giangViens as $gv) {
            $soMonHocCuaGiangVien[$gv->id] = 0;
        }
        
        // Phân bổ giảng viên cho từng môn học
        foreach ($monHocs as $monHoc) {
            $monHocId = $monHoc->id;
            $khoaId = $monHoc->khoa_id;
            
            // Lấy giảng viên cùng khoa với môn học (ưu tiên)
            $giangVienCungKhoa = $giangVienTheoKhoa[$khoaId] ?? [];
            
            // Lấy giảng viên từ các khoa khác (bổ sung)
            $giangVienKhacKhoa = [];
            foreach ($giangVienTheoKhoa as $khoaIdKhac => $gvList) {
                if ($khoaIdKhac != $khoaId) {
                    $giangVienKhacKhoa = array_merge($giangVienKhacKhoa, $gvList);
                }
            }
            
            // Đảm bảo mỗi môn có ít nhất 2 giảng viên
            $soGiangVienCan = 2;
            $giangVienDaChon = [];
            
            // Lọc giảng viên chưa đủ 2 môn (mỗi giảng viên chỉ dạy tối đa 2 môn)
            $soMonToiDa = 2;
            $giangVienCungKhoaHopLe = array_filter($giangVienCungKhoa, function($gv) use ($soMonHocCuaGiangVien, $soMonToiDa) {
                $gvId = is_object($gv) ? $gv->id : $gv['id'];
                return ($soMonHocCuaGiangVien[$gvId] ?? 0) < $soMonToiDa;
            });
            
            // Sắp xếp giảng viên cùng khoa theo số môn đã dạy (ít nhất trước)
            usort($giangVienCungKhoaHopLe, function($a, $b) use ($soMonHocCuaGiangVien) {
                $aId = is_object($a) ? $a->id : $a['id'];
                $bId = is_object($b) ? $b->id : $b['id'];
                return ($soMonHocCuaGiangVien[$aId] ?? 0) - ($soMonHocCuaGiangVien[$bId] ?? 0);
            });
            
            // Ưu tiên chọn giảng viên cùng khoa
            if (count($giangVienCungKhoaHopLe) > 0) {
                // Chọn tối đa 2 giảng viên cùng khoa (ưu tiên người ít môn nhất)
                $soGiangVienCungKhoa = min($soGiangVienCan, count($giangVienCungKhoaHopLe));
                $selectedCungKhoa = array_slice($giangVienCungKhoaHopLe, 0, $soGiangVienCungKhoa);
                $giangVienDaChon = array_merge($giangVienDaChon, $selectedCungKhoa);
            }
            
            // Nếu chưa đủ, bổ sung từ khoa khác
            if (count($giangVienDaChon) < $soGiangVienCan && count($giangVienKhacKhoa) > 0) {
                // Lọc giảng viên khác khoa chưa đủ 2 môn
                $giangVienKhacKhoaHopLe = array_filter($giangVienKhacKhoa, function($gv) use ($soMonHocCuaGiangVien, $soMonToiDa) {
                    $gvId = is_object($gv) ? $gv->id : $gv['id'];
                    return ($soMonHocCuaGiangVien[$gvId] ?? 0) < $soMonToiDa;
                });
                
                // Sắp xếp giảng viên khác khoa theo số môn đã dạy (ít nhất trước)
                usort($giangVienKhacKhoaHopLe, function($a, $b) use ($soMonHocCuaGiangVien) {
                    $aId = is_object($a) ? $a->id : $a['id'];
                    $bId = is_object($b) ? $b->id : $b['id'];
                    return ($soMonHocCuaGiangVien[$aId] ?? 0) - ($soMonHocCuaGiangVien[$bId] ?? 0);
                });
                
                $soGiangVienCanBoSung = $soGiangVienCan - count($giangVienDaChon);
                $selectedKhacKhoa = array_slice($giangVienKhacKhoaHopLe, 0, min($soGiangVienCanBoSung, count($giangVienKhacKhoaHopLe)));
                $giangVienDaChon = array_merge($giangVienDaChon, $selectedKhacKhoa);
            }
            
            // Nếu vẫn chưa đủ 2 giảng viên, thử lấy thêm từ tất cả giảng viên
            if (count($giangVienDaChon) < $soGiangVienCan) {
                $allGiangVien = $giangViens->toArray();
                
                // Lọc giảng viên chưa đủ 2 môn
                $allGiangVienHopLe = array_filter($allGiangVien, function($gv) use ($soMonHocCuaGiangVien, $soMonToiDa) {
                    $gvId = is_object($gv) ? $gv->id : $gv['id'];
                    return ($soMonHocCuaGiangVien[$gvId] ?? 0) < $soMonToiDa;
                });
                
                // Sắp xếp theo số môn đã dạy
                usort($allGiangVienHopLe, function($a, $b) use ($soMonHocCuaGiangVien) {
                    $aId = is_object($a) ? $a->id : $a['id'];
                    $bId = is_object($b) ? $b->id : $b['id'];
                    return ($soMonHocCuaGiangVien[$aId] ?? 0) - ($soMonHocCuaGiangVien[$bId] ?? 0);
                });
                
                $idsDaChon = array_map(function($gv) {
                    return is_object($gv) ? $gv->id : $gv['id'];
                }, $giangVienDaChon);
                
                foreach ($allGiangVienHopLe as $gv) {
                    $gvId = is_object($gv) ? $gv->id : $gv['id'];
                    if (!in_array($gvId, $idsDaChon) && count($giangVienDaChon) < $soGiangVienCan) {
                        $giangVienDaChon[] = $gv;
                    }
                }
            }
            
            // Gán giảng viên cho môn học
            if (count($giangVienDaChon) >= 2) {
                foreach ($giangVienDaChon as $gv) {
                    $gvId = is_object($gv) ? $gv->id : $gv['id'];
                    
                    // Kiểm tra xem đã có chưa (tránh trùng lặp)
                    $exists = DB::table('giang_vien_mon_hoc')
                        ->where('giang_vien_id', $gvId)
                        ->where('mon_hoc_id', $monHocId)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('giang_vien_mon_hoc')->insert([
                            'giang_vien_id' => $gvId,
                            'mon_hoc_id' => $monHocId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $totalAssigned++;
                        // Cập nhật số môn học của giảng viên
                        $soMonHocCuaGiangVien[$gvId] = ($soMonHocCuaGiangVien[$gvId] ?? 0) + 1;
                    }
                }
            } else {
                $monHocKhongDuGiangVien[] = [
                    'ma_mon' => $monHoc->ma_mon,
                    'ten_mon' => $monHoc->ten_mon,
                    'so_gv' => count($giangVienDaChon)
                ];
            }
        }
        
        // Thống kê
        echo "✅ Đã phân bổ giảng viên cho môn học\n";
        echo "   📚 Tổng số môn học: {$monHocs->count()}\n";
        echo "   👨‍🏫 Tổng số giảng viên: {$giangViens->count()}\n";
        echo "   🔗 Tổng số phân công: {$totalAssigned}\n\n";
        
        // Thống kê theo khoa
        $stats = DB::table('giang_vien_mon_hoc')
            ->join('mon_hoc', 'giang_vien_mon_hoc.mon_hoc_id', '=', 'mon_hoc.id')
            ->join('khoa', 'mon_hoc.khoa_id', '=', 'khoa.id')
            ->select('khoa.ten_khoa', DB::raw('COUNT(DISTINCT mon_hoc.id) as so_mon'), DB::raw('COUNT(*) as so_phan_cong'))
            ->groupBy('khoa.id', 'khoa.ten_khoa')
            ->get();
        
        echo "   📊 Thống kê theo khoa:\n";
        foreach ($stats as $stat) {
            echo "      - {$stat->ten_khoa}: {$stat->so_mon} môn, {$stat->so_phan_cong} phân công\n";
        }
        
        // Kiểm tra môn học có đủ giảng viên
        $monHocStats = DB::table('mon_hoc')
            ->leftJoin('giang_vien_mon_hoc', 'mon_hoc.id', '=', 'giang_vien_mon_hoc.mon_hoc_id')
            ->select('mon_hoc.ma_mon', 'mon_hoc.ten_mon', DB::raw('COUNT(giang_vien_mon_hoc.giang_vien_id) as so_gv'))
            ->groupBy('mon_hoc.id', 'mon_hoc.ma_mon', 'mon_hoc.ten_mon')
            ->having('so_gv', '<', 2)
            ->get();
        
        if ($monHocStats->isNotEmpty()) {
            echo "\n   ⚠️  Các môn học chưa đủ 2 giảng viên:\n";
            foreach ($monHocStats as $stat) {
                echo "      - {$stat->ma_mon} - {$stat->ten_mon}: {$stat->so_gv} giảng viên\n";
            }
            echo "\n   💡 Cần thêm giảng viên hoặc điều chỉnh phân bổ\n";
        } else {
            echo "\n   ✅ Tất cả môn học đều có ít nhất 2 giảng viên\n";
        }
        
        // Thống kê giảng viên có nhiều môn nhất
        $gvStats = DB::table('giang_vien_mon_hoc')
            ->join('giang_vien', 'giang_vien_mon_hoc.giang_vien_id', '=', 'giang_vien.id')
            ->select('giang_vien.ma_giang_vien', 'giang_vien.ho_ten', DB::raw('COUNT(*) as so_mon'))
            ->groupBy('giang_vien.id', 'giang_vien.ma_giang_vien', 'giang_vien.ho_ten')
            ->orderByDesc('so_mon')
            ->get();
        
        // Kiểm tra giảng viên có quá 2 môn
        $gvQua2Mon = $gvStats->filter(function($stat) {
            return $stat->so_mon > 2;
        });
        
        if ($gvQua2Mon->isNotEmpty()) {
            echo "\n   ⚠️  Các giảng viên có quá 2 môn (cần điều chỉnh):\n";
            foreach ($gvQua2Mon as $stat) {
                echo "      - {$stat->ma_giang_vien} - {$stat->ho_ten}: {$stat->so_mon} môn\n";
            }
        } else {
            echo "\n   ✅ Tất cả giảng viên đều có tối đa 2 môn\n";
        }
        
        // Thống kê phân bổ số môn
        $phanBo = [
            0 => 0,
            1 => 0,
            2 => 0,
        ];
        foreach ($gvStats as $stat) {
            $soMon = min($stat->so_mon, 2);
            $phanBo[$soMon] = ($phanBo[$soMon] ?? 0) + 1;
        }
        
        echo "\n   📊 Phân bổ số môn theo giảng viên:\n";
        echo "      - 0 môn: {$phanBo[0]} giảng viên\n";
        echo "      - 1 môn: {$phanBo[1]} giảng viên\n";
        echo "      - 2 môn: {$phanBo[2]} giảng viên\n";
    }
}