<?php

namespace App\Observers;

use App\Models\LichHocCoDinh;
use App\Models\LichHocChiTiet;
use Illuminate\Support\Facades\Log;

class LichHocCoDinhObserver
{
    /**
     * Handle the LichHocCoDinh "created" event.
     */
    public function created(LichHocCoDinh $lichHocCoDinh): void
    {
        // Không cần tạo LichHocChiTiet ngay khi tạo LichHocCoDinh
        // Vì LichHocChiTiet được tạo thông qua chức năng "Tạo lịch học tự động" hoặc "Sinh lịch chi tiết"
    }

    /**
     * Handle the LichHocCoDinh "updated" event.
     * Tự động cập nhật tất cả LichHocChiTiet liên quan khi LichHocCoDinh thay đổi
     */
    public function updated(LichHocCoDinh $lichHocCoDinh): void
    {
        try {
            // Lấy danh sách các trường đã thay đổi (sử dụng getChanges() thay vì getDirty() vì đã được saved)
            $changedFields = $lichHocCoDinh->getChanges();
            
            // Loại bỏ các trường không cần cập nhật
            unset($changedFields['updated_at']);
            unset($changedFields['thu_trong_tuan']); // Thu trong tuan được xử lý riêng trong controller
            
            if (empty($changedFields)) {
                return; // Không có thay đổi quan trọng
            }
            
            // Chỉ cập nhật nếu có thay đổi các trường quan trọng
            $importantFields = [
                'tiet_bat_dau',
                'tiet_ket_thuc',
                'gio_bat_dau',
                'gio_ket_thuc',
                'phong_hoc_id',
                'giang_vien_id',
                'hinh_thuc',
                'link_online',
                'ca_hoc_id',
            ];
            
            $hasImportantChange = false;
            foreach ($importantFields as $field) {
                if (isset($changedFields[$field])) {
                    $hasImportantChange = true;
                    break;
                }
            }
            
            if (!$hasImportantChange) {
                return; // Không có thay đổi quan trọng, không cần cập nhật
            }
            
            // Cập nhật tất cả LichHocChiTiet liên quan (chỉ các buổi chưa dạy)
            $lichHocChiTiets = LichHocChiTiet::where('lich_hoc_co_dinh_id', $lichHocCoDinh->id)
                ->where('trang_thai', '!=', 'da_day') // Không cập nhật các buổi đã dạy
                ->get();
            
            if ($lichHocChiTiets->isEmpty()) {
                Log::info('Không có LichHocChiTiet nào cần cập nhật', [
                    'lich_hoc_co_dinh_id' => $lichHocCoDinh->id
                ]);
                return;
            }
            
            $updatedCount = 0;
            foreach ($lichHocChiTiets as $lichChiTiet) {
                $updateData = [];
                
                // Cập nhật các trường đã thay đổi
                if (isset($changedFields['tiet_bat_dau'])) {
                    $updateData['tiet_bat_dau'] = $lichHocCoDinh->tiet_bat_dau;
                }
                if (isset($changedFields['tiet_ket_thuc'])) {
                    $updateData['tiet_ket_thuc'] = $lichHocCoDinh->tiet_ket_thuc;
                }
                if (isset($changedFields['gio_bat_dau'])) {
                    $updateData['gio_bat_dau'] = $lichHocCoDinh->gio_bat_dau;
                }
                if (isset($changedFields['gio_ket_thuc'])) {
                    $updateData['gio_ket_thuc'] = $lichHocCoDinh->gio_ket_thuc;
                }
                if (isset($changedFields['phong_hoc_id'])) {
                    $updateData['phong_hoc_id'] = $lichHocCoDinh->phong_hoc_id;
                }
                if (isset($changedFields['giang_vien_id'])) {
                    $updateData['giang_vien_id'] = $lichHocCoDinh->giang_vien_id;
                }
                if (isset($changedFields['hinh_thuc'])) {
                    $updateData['hinh_thuc'] = $lichHocCoDinh->hinh_thuc;
                }
                if (isset($changedFields['link_online'])) {
                    $updateData['link_online'] = $lichHocCoDinh->link_online;
                }
                if (isset($changedFields['ca_hoc_id'])) {
                    $updateData['ca_hoc_id'] = $lichHocCoDinh->ca_hoc_id;
                    // Khi ca_hoc_id thay đổi, cũng cập nhật tiet và gio
                    if (!isset($updateData['tiet_bat_dau'])) {
                        // Tính lại từ ca học mới
                        $caHoc = \App\Models\CaHoc::find($lichHocCoDinh->ca_hoc_id);
                        if ($caHoc) {
                            $updateData['tiet_bat_dau'] = ($caHoc->thu_tu * 2) - 1;
                            $updateData['tiet_ket_thuc'] = $caHoc->thu_tu * 2;
                            $updateData['gio_bat_dau'] = $caHoc->gio_bat_dau;
                            $updateData['gio_ket_thuc'] = $caHoc->gio_ket_thuc;
                        }
                    }
                }
                
                if (!empty($updateData)) {
                    $lichChiTiet->update($updateData);
                    $updatedCount++;
                }
            }
            
            if ($updatedCount > 0) {
                Log::info('Đã tự động cập nhật LichHocChiTiet khi LichHocCoDinh thay đổi', [
                    'lich_hoc_co_dinh_id' => $lichHocCoDinh->id,
                    'so_lich_chi_tiet_da_cap_nhat' => $updatedCount,
                    'tong_so_lich_chi_tiet' => $lichHocChiTiets->count(),
                    'cac_truong_thay_doi' => array_keys($changedFields)
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Lỗi khi tự động cập nhật LichHocChiTiet', [
                'lich_hoc_co_dinh_id' => $lichHocCoDinh->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle the LichHocCoDinh "deleted" event.
     * LichHocChiTiet sẽ được xóa trong destroy method của controller
     */
    public function deleted(LichHocCoDinh $lichHocCoDinh): void
    {
        // LichHocChiTiet đã được xóa trong destroy method của controller
        // Foreign key constraint sẽ set null cho lich_hoc_co_dinh_id
    }

    /**
     * Handle the LichHocCoDinh "restored" event.
     */
    public function restored(LichHocCoDinh $lichHocCoDinh): void
    {
        //
    }

    /**
     * Handle the LichHocCoDinh "force deleted" event.
     */
    public function forceDeleted(LichHocCoDinh $lichHocCoDinh): void
    {
        //
    }
}
