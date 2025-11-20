<?php

namespace App\Observers;

use App\Models\LopHocPhanSinhVien;
use App\Models\LopHocPhan;
use App\Models\ChiTietHocPhiMon;
use App\Services\HocPhiService;
use Illuminate\Support\Facades\Log;

class LopHocPhanSinhVienObserver
{
    protected $hocPhiService;

    public function __construct(HocPhiService $hocPhiService)
    {
        $this->hocPhiService = $hocPhiService;
    }

    /**
     * Handle the LopHocPhanSinhVien "created" event.
     * Tự động tăng số lượng đăng ký khi thêm sinh viên
     * Tự động tính học phí khi sinh viên được xếp lớp
     */
    public function created(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            // 1. Tăng số lượng đăng ký
            $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
            if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                $lopHocPhan->increment('so_luong_dang_ky');
            }

            // 2. Tự động tính học phí cho sinh viên
            $this->tinhHocPhiTuDong($lhpsv);
        }
    }

    /**
     * Tính học phí tự động cho sinh viên khi được xếp lớp
     * LƯU Ý: Học phí đã được tính khi đăng ký môn, bây giờ chỉ cần liên kết với LopHocPhanSinhVien
     */
    protected function tinhHocPhiTuDong(LopHocPhanSinhVien $lhpsv): void
    {
        try {
            $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
            
            if (!$lopHocPhan) {
                Log::warning("⚠️ Không tìm thấy lớp học phần ID: {$lhpsv->lop_hoc_phan_id}");
                return;
            }

            // Lấy học phí của sinh viên trong học kỳ này
            $hocPhi = \App\Models\HocPhiHocKy::where('sinh_vien_id', $lhpsv->sinh_vien_id)
                ->where('hoc_ky_id', $lopHocPhan->hoc_ky_id)
                ->first();

            if (!$hocPhi) {
                Log::warning("⚠️ Không tìm thấy học phí cho sinh viên ID: {$lhpsv->sinh_vien_id} - Học kỳ: {$lopHocPhan->hoc_ky_id}");
                return;
            }

            // Cập nhật ChiTietHocPhiMon để liên kết với LopHocPhanSinhVien
            $chiTiet = ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                ->where('mon_hoc_id', $lopHocPhan->mon_hoc_id)
                ->whereNull('lop_hoc_phan_sinh_vien_id')
                ->first();

            if ($chiTiet) {
                $chiTiet->lop_hoc_phan_sinh_vien_id = $lhpsv->id;
                $chiTiet->save();
                Log::info("✅ [AUTO] Đã liên kết học phí với lớp học phần cho sinh viên ID: {$lhpsv->sinh_vien_id} - Lớp: {$lopHocPhan->ma_lop_hp}");
            } else {
                // Nếu không tìm thấy chi tiết học phí, có thể học phí chưa được tính khi đăng ký
                // Trong trường hợp này, tính lại học phí (fallback)
                Log::warning("⚠️ Không tìm thấy chi tiết học phí cho môn {$lopHocPhan->mon_hoc_id}. Tính lại học phí...");
                $result = $this->hocPhiService->tinhHocPhiKhiDangKy(
                    $lhpsv->sinh_vien_id,
                    $lopHocPhan->hoc_ky_id,
                    [$lhpsv->id]
                );

                if ($result) {
                    Log::info("✅ [AUTO] Đã tính lại học phí cho sinh viên ID: {$lhpsv->sinh_vien_id} - Lớp: {$lopHocPhan->ma_lop_hp}");
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ [AUTO] Lỗi xử lý học phí tự động cho sinh viên ID: {$lhpsv->sinh_vien_id} - Lỗi: {$e->getMessage()}");
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "updated" event.
     * Cập nhật số lượng khi thay đổi trạng thái
     * Tự động tính học phí khi trạng thái chuyển sang "đã xếp lớp"
     */
    public function updated(LopHocPhanSinhVien $lhpsv): void
    {
        if ($lhpsv->isDirty('trang_thai')) {
            $oldStatus = $lhpsv->getOriginal('trang_thai');
            $newStatus = $lhpsv->trang_thai;

            // Trạng thái cũ không tính, trạng thái mới có tính -> tăng + tính học phí
            if (
                !in_array($oldStatus, ['da_xep_lop', 'dang_hoc']) &&
                in_array($newStatus, ['da_xep_lop', 'dang_hoc'])
            ) {
                $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
                if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                    $lopHocPhan->increment('so_luong_dang_ky');
                }

                // Tính học phí tự động khi chuyển trạng thái sang "đã xếp lớp"
                $this->tinhHocPhiTuDong($lhpsv);
            }

            // Trạng thái cũ có tính, trạng thái mới không tính -> giảm + hủy học phí
            if (
                in_array($oldStatus, ['da_xep_lop', 'dang_hoc']) &&
                !in_array($newStatus, ['da_xep_lop', 'dang_hoc'])
            ) {
                LopHocPhan::where('id', $lhpsv->lop_hoc_phan_id)
                    ->decrement('so_luong_dang_ky');

                // Hủy học phí khi hủy lớp
                $this->huyHocPhiTuDong($lhpsv);
            }
        }
    }

    /**
     * Hủy học phí tự động khi sinh viên rút khỏi lớp
     */
    protected function huyHocPhiTuDong(LopHocPhanSinhVien $lhpsv): void
    {
        try {
            $this->hocPhiService->huyHocPhiMonHoc($lhpsv->id);
            Log::info("✅ [AUTO] Đã hủy học phí cho sinh viên ID: {$lhpsv->sinh_vien_id}");
        } catch (\Exception $e) {
            Log::error("❌ [AUTO] Lỗi hủy học phí tự động: {$e->getMessage()}");
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "deleted" event.
     * Tự động giảm số lượng đăng ký khi xóa sinh viên
     */
    public function deleted(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            LopHocPhan::where('id', $lhpsv->lop_hoc_phan_id)
                ->decrement('so_luong_dang_ky');
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "restored" event.
     */
    public function restored(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);
            if ($lopHocPhan && $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua) {
                $lopHocPhan->increment('so_luong_dang_ky');
            }
        }
    }

    /**
     * Handle the LopHocPhanSinhVien "force deleted" event.
     */
    public function forceDeleted(LopHocPhanSinhVien $lhpsv): void
    {
        if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
            LopHocPhan::where('id', $lhpsv->lop_hoc_phan_id)
                ->decrement('so_luong_dang_ky');
        }
    }
}
