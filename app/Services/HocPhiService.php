<?php

namespace App\Services;

use App\Models\CauHinhHocPhi;
use App\Models\HocPhiHocKy;
use App\Models\ChiTietHocPhiMon;
use App\Models\LopHocPhanSinhVien;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HocPhiService
{
    /**
     * Calculate tuition when student registers for courses
     * 
     * @param int $sinhVienId
     * @param int $hocKyId
     * @param array $lopHocPhanSinhVienIds Array of lop_hoc_phan_sinh_vien IDs
     * @return HocPhiHocKy|null
     */
    public function tinhHocPhiKhiDangKy($sinhVienId, $hocKyId, $lopHocPhanSinhVienIds)
    {
        try {
            DB::beginTransaction();

            // Get current tuition config
            $cauHinh = CauHinhHocPhi::getCauHinhHienTai();
            if (!$cauHinh) {
                Log::error('Không tìm thấy cấu hình học phí hiện tại');
                return null;
            }

            // Get or create HocPhiHocKy record
            $hocPhi = HocPhiHocKy::firstOrCreate(
                [
                    'sinh_vien_id' => $sinhVienId,
                    'hoc_ky_id' => $hocKyId,
                ],
                [
                    'tong_tin_chi_dang_ky' => 0,
                    'tong_hoc_phi_mon_hoc' => 0,
                    'phi_dich_vu' => $cauHinh->phi_dich_vu,
                    'tong_so_tien' => 0,
                    'so_tien_da_dong' => 0,
                    'so_tien_con_lai' => 0,
                    'han_dong' => $this->calculateHanDong($hocKyId),
                    'trang_thai' => 'chua_nop',
                ]
            );

            // Process each course registration
            foreach ($lopHocPhanSinhVienIds as $lopHocPhanSinhVienId) {
                $lopHocPhanSinhVien = LopHocPhanSinhVien::with(['lopHocPhan.monHoc'])
                    ->find($lopHocPhanSinhVienId);

                if (!$lopHocPhanSinhVien) {
                    continue;
                }

                $monHoc = $lopHocPhanSinhVien->lopHocPhan->monHoc;
                $soTinChi = $monHoc->so_tin_chi_ly_thuyet + $monHoc->so_tin_chi_thuc_hanh;
                $thanhTien = $soTinChi * $cauHinh->don_gia_tren_tin_chi;

                // Create or update ChiTietHocPhiMon
                ChiTietHocPhiMon::updateOrCreate(
                    [
                        'hoc_phi_hoc_ky_id' => $hocPhi->id,
                        'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSinhVienId,
                    ],
                    [
                        'mon_hoc_id' => $monHoc->id,
                        'so_tin_chi' => $soTinChi,
                        'don_gia_tin_chi' => $cauHinh->don_gia_tren_tin_chi,
                        'thanh_tien' => $thanhTien,
                        'ngay_tinh' => now(),
                        'trang_thai' => 'chua_thanh_toan',
                    ]
                );
            }

            // Recalculate totals
            $this->recalculateHocPhi($hocPhi->id);

            DB::commit();

            return $hocPhi->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error calculating tuition: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Recalculate tuition totals
     * 
     * @param int $hocPhiHocKyId
     * @return bool
     */
    public function recalculateHocPhi($hocPhiHocKyId)
    {
        try {
            $hocPhi = HocPhiHocKy::with('chiTietHocPhiMon')->findOrFail($hocPhiHocKyId);

            // Calculate total credits
            $tongTinChi = $hocPhi->chiTietHocPhiMon->sum('so_tin_chi');

            // Calculate total course tuition
            $tongHocPhiMon = $hocPhi->chiTietHocPhiMon
                ->where('trang_thai', '!=', 'huy')
                ->sum('thanh_tien');

            // Update HocPhiHocKy
            $hocPhi->tong_tin_chi_dang_ky = $tongTinChi;
            $hocPhi->tong_hoc_phi_mon_hoc = $tongHocPhiMon;
            $hocPhi->tong_so_tien = $tongHocPhiMon + $hocPhi->phi_dich_vu;
            $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
            $hocPhi->save();

            // Update status
            $hocPhi->updateTrangThai();

            return true;
        } catch (\Exception $e) {
            Log::error('Error recalculating tuition: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel tuition for a course (when student withdraws)
     * 
     * @param int $lopHocPhanSinhVienId
     * @return bool
     */
    public function huyHocPhiMonHoc($lopHocPhanSinhVienId)
    {
        try {
            DB::beginTransaction();

            $chiTiet = ChiTietHocPhiMon::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
                ->first();

            if ($chiTiet) {
                $chiTiet->trang_thai = 'huy';
                $chiTiet->save();

                // Recalculate totals
                $this->recalculateHocPhi($chiTiet->hoc_phi_hoc_ky_id);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error canceling course tuition: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate payment deadline
     * 
     * @param int $hocKyId
     * @return string
     */
    private function calculateHanDong($hocKyId)
    {
        $hocKy = HocKy::find($hocKyId);
        
        if (!$hocKy) {
            // Default: 30 days from now
            return now()->addDays(30)->toDateString();
        }

        // Set deadline to 2 weeks after semester start
        return $hocKy->ngay_bat_dau 
            ? date('Y-m-d', strtotime($hocKy->ngay_bat_dau . ' + 14 days'))
            : now()->addDays(30)->toDateString();
    }

    /**
     * Get tuition summary for a student
     * 
     * @param int $sinhVienId
     * @return array
     */
    public function getTongHopHocPhi($sinhVienId)
    {
        $hocPhis = HocPhiHocKy::where('sinh_vien_id', $sinhVienId)->get();

        return [
            'tong_hoc_phi' => $hocPhis->sum('tong_so_tien'),
            'da_dong' => $hocPhis->sum('so_tien_da_dong'),
            'con_lai' => $hocPhis->sum('so_tien_con_lai'),
            'so_hoc_ky' => $hocPhis->count(),
            'chua_nop' => $hocPhis->where('trang_thai', 'chua_nop')->count(),
            'da_nop_mot_phan' => $hocPhis->where('trang_thai', 'da_nop_mot_phan')->count(),
            'da_nop_du' => $hocPhis->where('trang_thai', 'da_nop_du')->count(),
            'qua_han' => $hocPhis->where('trang_thai', 'qua_han')->count(),
        ];
    }

    /**
     * Check if student has outstanding tuition
     * 
     * @param int $sinhVienId
     * @return bool
     */
    public function hasOutstandingTuition($sinhVienId)
    {
        return HocPhiHocKy::where('sinh_vien_id', $sinhVienId)
            ->where('so_tien_con_lai', '>', 0)
            ->exists();
    }

    /**
     * Get overdue tuition amount for a student
     * 
     * @param int $sinhVienId
     * @return float
     */
    public function getOverdueAmount($sinhVienId)
    {
        return HocPhiHocKy::where('sinh_vien_id', $sinhVienId)
            ->where('han_dong', '<', now())
            ->where('so_tien_con_lai', '>', 0)
            ->sum('so_tien_con_lai');
    }
}
