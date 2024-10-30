<?php

namespace App\Observers;

use App\Models\CaHoc;
use App\Models\LichHocCoDinh;
use App\Models\LichHocChiTiet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CaHocObserver
{
    /**
     * Handle the CaHoc "updated" event.
     * Tự động cập nhật tất cả LichHocCoDinh và LichHocChiTiet khi Ca học thay đổi giờ
     */
    public function updated(CaHoc $caHoc): void
    {
        try {
            // Lấy các trường đã thay đổi
            $changedFields = $caHoc->getChanges();
            
            // Chỉ cập nhật nếu giờ học thay đổi
            if (!isset($changedFields['gio_bat_dau']) && !isset($changedFields['gio_ket_thuc'])) {
                return; // Không có thay đổi về giờ học
            }
            
            DB::beginTransaction();
            
            // Cập nhật tất cả LichHocCoDinh sử dụng ca học này
            $lichHocCoDinhs = LichHocCoDinh::where('ca_hoc_id', $caHoc->id)->get();
            $updatedLichCoDinh = 0;
            
            foreach ($lichHocCoDinhs as $lichCoDinh) {
                $updateData = [];
                
                if (isset($changedFields['gio_bat_dau'])) {
                    $updateData['gio_bat_dau'] = $caHoc->gio_bat_dau;
                }
                
                if (isset($changedFields['gio_ket_thuc'])) {
                    $updateData['gio_ket_thuc'] = $caHoc->gio_ket_thuc;
                }
                
                // Nếu thu_tu thay đổi, cập nhật cả tiết
                if (isset($changedFields['thu_tu'])) {
                    $updateData['tiet_bat_dau'] = ($caHoc->thu_tu * 2) - 1;
                    $updateData['tiet_ket_thuc'] = $caHoc->thu_tu * 2;
                }
                
                if (!empty($updateData)) {
                    $lichCoDinh->update($updateData);
                    $updatedLichCoDinh++;
                }
            }
            
            // Cập nhật tất cả LichHocChiTiet sử dụng ca học này
            $lichHocChiTiets = LichHocChiTiet::where('ca_hoc_id', $caHoc->id)->get();
            $updatedLichChiTiet = 0;
            
            foreach ($lichHocChiTiets as $lichChiTiet) {
                $updateData = [];
                
                if (isset($changedFields['gio_bat_dau'])) {
                    $updateData['gio_bat_dau'] = $caHoc->gio_bat_dau;
                }
                
                if (isset($changedFields['gio_ket_thuc'])) {
                    $updateData['gio_ket_thuc'] = $caHoc->gio_ket_thuc;
                }
                
                // Nếu thu_tu thay đổi, cập nhật cả tiết
                if (isset($changedFields['thu_tu'])) {
                    $updateData['tiet_bat_dau'] = ($caHoc->thu_tu * 2) - 1;
                    $updateData['tiet_ket_thuc'] = $caHoc->thu_tu * 2;
                }
                
                if (!empty($updateData)) {
                    $lichChiTiet->update($updateData);
                    $updatedLichChiTiet++;
                }
            }
            
            DB::commit();
            
            if ($updatedLichCoDinh > 0 || $updatedLichChiTiet > 0) {
                Log::info('Đã tự động cập nhật lịch học khi Ca học thay đổi', [
                    'ca_hoc_id' => $caHoc->id,
                    'ten_ca' => $caHoc->ten_ca,
                    'so_lich_co_dinh_da_cap_nhat' => $updatedLichCoDinh,
                    'so_lich_chi_tiet_da_cap_nhat' => $updatedLichChiTiet,
                    'cac_truong_thay_doi' => array_keys($changedFields)
                ]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tự động cập nhật lịch học khi Ca học thay đổi', [
                'ca_hoc_id' => $caHoc->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

