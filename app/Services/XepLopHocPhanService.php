<?php

namespace App\Services;

use App\Models\LopHocPhanSinhVien;
use App\Models\DangKyMonHocTam;
use App\Models\HocKy;
use App\Models\LopHocPhan;
use App\Models\LichHocCoDinh;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Collection;

/**
 * Service xếp lớp học phần tự động
 */
class XepLopHocPhanService 
{
    protected $hocPhiService;

    public function __construct(HocPhiService $hocPhiService)
    {
        $this->hocPhiService = $hocPhiService;
    }

    /**
     * Xếp lớp tự động cho sinh viên
     */
    public function xepLopTuDong(HocKy $hocKy): array
    {
        $startTime = Carbon::now();

        // 1. Lấy tất cả đăng ký chờ xếp lớp
        $dangKyList = DangKyMonHocTam::query()
            ->where('hoc_ky_id', $hocKy->id)
            ->where('trang_thai', 'cho_xep_lop')
            ->orderBy('uu_tien', 'desc')
            ->orderBy('ngay_dang_ky', 'asc')
            ->get();

        $stats = [
            'total' => $dangKyList->count(),
            'success' => 0,
            'failed' => 0
        ];

        try {
            DB::beginTransaction();
            
            foreach ($dangKyList as $dangKy) {
                $ketQua = $this->xepLopChoMotSinhVien($dangKy);
                
                if ($ketQua['success']) {
                    $stats['success']++;
                } else {
                    $stats['failed']++;
                }
            }

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("Lỗi khi xếp lớp tự động: " . $e->getMessage());
            throw $e;
        }

        $endTime = Carbon::now();
        
        // Ghi log kết quả xếp lớp
        $this->ghiLogXepLop($hocKy, $stats, $startTime, $endTime);

        return $stats;
    }

    /**
     * Xếp lớp cho một sinh viên
     */
    protected function xepLopChoMotSinhVien(DangKyMonHocTam $dangKy): array
    {
        // 1. Tìm lớp phù hợp
        $lopPhuHop = $this->timLopPhuHop($dangKy);

        if (!$lopPhuHop) {
            $this->capNhatThatBai($dangKy, 'Không tìm được lớp phù hợp');
            return ['success' => false];
        }

        // 2. Thêm sinh viên vào lớp
        try {
            $this->themSinhVienVaoLop($dangKy, $lopPhuHop);
            return ['success' => true];
        } catch (\Exception $e) {
            $this->capNhatThatBai($dangKy, 'Lỗi khi xếp lớp: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Tìm lớp học phần phù hợp cho sinh viên
     */
    protected function timLopPhuHop(DangKyMonHocTam $dangKy): ?LopHocPhan 
    {
        // 1. Lấy các lớp có thể xếp
        $dsLop = LopHocPhan::where('mon_hoc_id', $dangKy->getAttribute('mon_hoc_id'))
->where('hoc_ky_id', $dangKy->getAttribute('hoc_ky_id'))
            ->where('trang_thai_lop', 'mo_dang_ky')
            ->whereColumn('so_luong_dang_ky', '<', 'suc_chua')
            ->get();

        if ($dsLop->isEmpty()) {
            return null;
        }

        // 2. Lấy lịch học hiện tại của sinh viên
        $lichHienTai = $this->layLichHocHienTai(
            $dangKy->getAttribute('sinh_vien_id'),
            $dangKy->getAttribute('hoc_ky_id')
        );

        // 3. Tìm lớp không trùng lịch
        foreach ($dsLop as $lop) {
            if (!$this->kiemTraTrungLich($lichHienTai, $lop->getKey())) {
                return $lop;
            }
        }

        return null;
    }

    /**
     * Lấy lịch học hiện tại của sinh viên trong kỳ
     */
    protected function layLichHocHienTai(int $sinhVienId, int $hocKyId): array
    {
        $lichHoc = [];
        
        $lopDaDangKy = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
            ->whereHas('lopHocPhan', function(Builder $q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            })
            ->with('lopHocPhan.lichHocCoDinh')
            ->get();

        foreach ($lopDaDangKy as $lop) {
            foreach ($lop->lopHocPhan->lichHocCoDinh as $lich) {
                $lichHoc[] = [
                    'thu' => $lich->getAttribute('thu_trong_tuan'),
                    'tiet_bat_dau' => $lich->getAttribute('tiet_bat_dau'),
                    'tiet_ket_thuc' => $lich->getAttribute('tiet_ket_thuc')
                ];
            }
        }

        return $lichHoc;
    }

    /**
     * Kiểm tra trùng lịch giữa lịch hiện tại và lớp mới
     */
    protected function kiemTraTrungLich(array $lichHienTai, int $lopHocPhanId): bool
    {
        $lichMoi = LichHocCoDinh::where('lop_hoc_phan_id', $lopHocPhanId)->get();

        foreach ($lichHienTai as $lich1) {
            foreach ($lichMoi as $lich2) {
                if ($lich1['thu'] == $lich2->getAttribute('thu_trong_tuan')) {
                    if ($this->kiemTraTrungTiet(
                        $lich1['tiet_bat_dau'],
                        $lich1['tiet_ket_thuc'],
                        $lich2->getAttribute('tiet_bat_dau'),
                        $lich2->getAttribute('tiet_ket_thuc')
                    )) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Kiểm tra trùng tiết học
     */
    protected function kiemTraTrungTiet(int $start1, int $end1, int $start2, int $end2): bool
    {
        return !($end1 < $start2 || $start1 > $end2);
    }

    /**
     * Thêm sinh viên vào lớp học phần
     */
    protected function themSinhVienVaoLop(DangKyMonHocTam $dangKy, LopHocPhan $lop): void
    {
        try {
            DB::beginTransaction();
// 1. Tạo bản ghi lớp học phần sinh viên
            $lopHocPhanSinhVien = LopHocPhanSinhVien::create([
                'sinh_vien_id' => $dangKy->sinh_vien_id,
                'lop_hoc_phan_id' => $lop->id,
                'dang_ky_tam_id' => $dangKy->id,
                'trang_thai' => 'da_xep_lop',
                'ngay_dang_ky' => $dangKy->ngay_dang_ky,
                'ngay_xep_lop' => Carbon::now(),
                'phuong_thuc_xep' => 'tu_dong'
            ]);

            // 2. Cập nhật số lượng sinh viên trong lớp
            $lop->so_luong_dang_ky = (int)$lop->so_luong_dang_ky + 1;
            $lop->save();

            // 3. Cập nhật trạng thái đăng ký tạm
            $dangKy->trang_thai = 'da_xep_lop';
            $dangKy->save();

            // 4. PHASE 8: Tính học phí tự động khi xếp lớp thành công
            try {
                $this->hocPhiService->tinhHocPhiKhiDangKy(
                    $dangKy->sinh_vien_id,
                    $lop->hoc_ky_id,
                    [$lopHocPhanSinhVien->id] // Pass as array
                );
                Log::info("Đã tính học phí cho sinh viên {$dangKy->sinh_vien_id} môn {$lop->mon_hoc_id}");
            } catch (\Exception $e) {
                // Log lỗi nhưng không rollback vì xếp lớp đã thành công
                Log::error("Lỗi tính học phí: " . $e->getMessage());
            }

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("Lỗi khi thêm sinh viên vào lớp: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật thất bại cho đăng ký tạm
     */
    protected function capNhatThatBai(DangKyMonHocTam $dangKy, string $lyDo): void
    {
        $dangKy->trang_thai = 'that_bai';
        $dangKy->ly_do_that_bai = $lyDo;
        $dangKy->save();
    }

    /**
     * Ghi log kết quả xếp lớp
     */
    protected function ghiLogXepLop(HocKy $hocKy, array $stats, Carbon $startTime, Carbon $endTime): void
    {
        $duration = $endTime->diffInSeconds($startTime);

        DB::table('lich_su_xep_lop')->insert([
            'hoc_ky_id' => $hocKy->id,
            'tong_so_dang_ky' => $stats['total'], 
            'thanh_cong' => $stats['success'],
            'that_bai' => $stats['failed'],
            'thoi_gian_xu_ly' => $duration,
            'thoi_gian_chay' => $startTime,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}