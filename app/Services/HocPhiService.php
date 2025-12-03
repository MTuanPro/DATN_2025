<?php

namespace App\Services;

use App\Models\CauHinhHocPhi;
use App\Models\HocPhiHocKy;
use App\Models\ChiTietHocPhiMon;
use App\Models\LopHocPhanSinhVien;
use App\Models\DangKyMonHocTam;
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

            // ✅ Cập nhật phi_dich_vu từ cấu hình hiện tại nếu học phí đã tồn tại và chưa thanh toán
            // Chỉ cập nhật khi chưa thanh toán để tránh ảnh hưởng đến các giao dịch đã thực hiện
            if ($hocPhi->wasRecentlyCreated === false && $hocPhi->so_tien_da_dong == 0) {
                $hocPhi->phi_dich_vu = $cauHinh->phi_dich_vu;
                $hocPhi->save();
            }

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

            // Calculate total credits (only non-cancelled courses)
            $tongTinChi = $hocPhi->chiTietHocPhiMon
                ->where('trang_thai', '!=', 'huy')
                ->sum('so_tin_chi');

            // Calculate total course tuition (only non-cancelled courses)
            $tongHocPhiMon = $hocPhi->chiTietHocPhiMon
                ->where('trang_thai', '!=', 'huy')
                ->sum('thanh_tien');

            // ✅ CHỈ TÍNH PHÍ DỊCH VỤ KHI CÓ ÍT NHẤT 1 MÔN HỌC (không bị hủy)
            // Nếu không có môn nào hoặc tất cả môn đã bị hủy, thì không tính phí dịch vụ
            $phiDichVu = ($tongTinChi > 0) ? $hocPhi->phi_dich_vu : 0;

            // Update HocPhiHocKy
            $hocPhi->tong_tin_chi_dang_ky = $tongTinChi;
            $hocPhi->tong_hoc_phi_mon_hoc = $tongHocPhiMon;
            $hocPhi->tong_so_tien = $tongHocPhiMon + $phiDichVu;
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
        // Hạn đóng = ngày đăng ký (now) + 7 ngày (1 tuần)
        // Sinh viên cần đóng học phí trong vòng 1 tuần để được xếp lớp
        return now()->addWeek()->toDateString();
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

    /**
     * Calculate tuition when student registers for a course (based on subject, not class)
     * This is called immediately when student registers, before class assignment
     * 
     * @param int $sinhVienId
     * @param int $hocKyId
     * @param int $monHocId
     * @return HocPhiHocKy|null
     */
    public function tinhHocPhiKhiDangKyMonHoc($sinhVienId, $hocKyId, $monHocId)
    {
        try {
            DB::beginTransaction();

            // Get current tuition config
            $cauHinh = CauHinhHocPhi::getCauHinhHienTai();
            if (!$cauHinh) {
                $now = now()->toDateString();
                Log::error("Không tìm thấy cấu hình học phí hiện tại. Ngày hiện tại: {$now}");
                
                // Log tất cả cấu hình để debug
                $allConfigs = CauHinhHocPhi::orderBy('ap_dung_tu_ngay', 'desc')->get();
                if ($allConfigs->count() > 0) {
                    $configInfo = $allConfigs->map(function($c) {
                        $active = $c->isActive() ? 'Có' : 'Không';
                        return "ID:{$c->id} Từ:{$c->ap_dung_tu_ngay} Đến:" . ($c->ap_dung_den_ngay ?? 'null') . " Active:{$active}";
                    })->implode(' | ');
                    Log::error("Danh sách cấu hình: {$configInfo}");
                } else {
                    Log::error("Không có cấu hình học phí nào trong hệ thống!");
                }
                
                DB::rollBack();
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

            // ✅ Cập nhật phi_dich_vu từ cấu hình hiện tại nếu học phí đã tồn tại và chưa thanh toán
            // Chỉ cập nhật khi chưa thanh toán để tránh ảnh hưởng đến các giao dịch đã thực hiện
            if ($hocPhi->wasRecentlyCreated === false && $hocPhi->so_tien_da_dong == 0) {
                $hocPhi->phi_dich_vu = $cauHinh->phi_dich_vu;
                $hocPhi->save();
            }

            // Get subject information
            $monHoc = \App\Models\DaoTao\MonHoc::find($monHocId);
            if (!$monHoc) {
                Log::error("Không tìm thấy môn học ID: {$monHocId}");
                DB::rollBack();
                return null;
            }

            $soTinChi = $monHoc->so_tin_chi_ly_thuyet + $monHoc->so_tin_chi_thuc_hanh;
            $thanhTien = $soTinChi * $cauHinh->don_gia_tren_tin_chi;

            // Create ChiTietHocPhiMon with mon_hoc_id only (lop_hoc_phan_sinh_vien_id will be null until class assignment)
            // Unique constraint: (hoc_phi_hoc_ky_id, mon_hoc_id)
            ChiTietHocPhiMon::updateOrCreate(
                [
                    'hoc_phi_hoc_ky_id' => $hocPhi->id,
                    'mon_hoc_id' => $monHocId,
                ],
                [
                    'lop_hoc_phan_sinh_vien_id' => null, // Chưa xếp lớp nên để null
                    'so_tin_chi' => $soTinChi,
                    'don_gia_tin_chi' => $cauHinh->don_gia_tren_tin_chi,
                    'thanh_tien' => $thanhTien,
                    'ngay_tinh' => now(),
                    'trang_thai' => 'chua_thanh_toan',
                ]
            );

            // Recalculate totals
            $this->recalculateHocPhi($hocPhi->id);

            DB::commit();

            Log::info("✅ Đã tính học phí cho sinh viên {$sinhVienId} - Môn: {$monHoc->ten_mon} - Số tiền: " . number_format($thanhTien, 0, ',', '.') . " VND");

            return $hocPhi->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error calculating tuition for subject registration: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update ChiTietHocPhiMon when student is assigned to a class
     * Links the tuition detail to the LopHocPhanSinhVien record
     * 
     * @param int $monHocId
     * @param int $lopHocPhanSinhVienId
     * @param int $hocPhiHocKyId
     * @return bool
     */
    public function capNhatChiTietHocPhiKhiXepLop($monHocId, $lopHocPhanSinhVienId, $hocPhiHocKyId)
    {
        try {
            $chiTiet = ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhiHocKyId)
                ->where('mon_hoc_id', $monHocId)
                ->whereNull('lop_hoc_phan_sinh_vien_id')
                ->first();

            if ($chiTiet) {
                $chiTiet->lop_hoc_phan_sinh_vien_id = $lopHocPhanSinhVienId;
                $chiTiet->save();
                Log::info("✅ Đã cập nhật chi tiết học phí cho LopHocPhanSinhVien ID: {$lopHocPhanSinhVienId}");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error updating tuition detail when assigning class: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Add student to waiting list for class assignment after full payment
     * 
     * @param int $sinhVienId
     * @param int $hocKyId
     * @return void
     */
    public function themVaoDanhSachChoXepLop($sinhVienId, $hocKyId)
    {
        try {
            // Lấy tất cả đăng ký đang chờ đóng học phí của sinh viên trong học kỳ này
            $dangKys = DangKyMonHocTam::where('sinh_vien_id', $sinhVienId)
                ->where('hoc_ky_id', $hocKyId)
                ->where('trang_thai', 'cho_dong_hoc_phi')
                ->get();

            foreach ($dangKys as $dangKy) {
                // Kiểm tra xem sinh viên đã đóng đủ học phí cho môn này chưa
                $hocPhi = HocPhiHocKy::where('sinh_vien_id', $sinhVienId)
                    ->where('hoc_ky_id', $hocKyId)
                    ->first();

                if ($hocPhi && $hocPhi->trang_thai == 'da_nop_du') {
                    // Kiểm tra xem môn này có trong chi tiết học phí không
                    $chiTiet = ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                        ->where('mon_hoc_id', $dangKy->mon_hoc_id)
                        ->first();

                    if ($chiTiet) {
                        // Chuyển trạng thái từ 'cho_dong_hoc_phi' sang 'cho_xep_lop'
                        $dangKy->trang_thai = 'cho_xep_lop';
                        $dangKy->save();

                        Log::info("✅ Đã thêm sinh viên {$sinhVienId} - Môn {$dangKy->mon_hoc_id} vào danh sách chờ xếp lớp sau khi đóng đủ học phí");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ Lỗi khi thêm vào danh sách chờ xếp lớp: " . $e->getMessage());
        }
    }
}
