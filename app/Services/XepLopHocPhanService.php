<?php

namespace App\Services;

use App\Models\LopHocPhanSinhVien;
use App\Models\DangKyMonHocTam;
use App\Models\HocPhiHocKy;
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
        // ✅ KIỂM TRA HỌC PHÍ: Chỉ xếp lớp cho sinh viên đã đóng đủ học phí
        if (!$this->kiemTraDaDongHocPhi($dangKy)) {
            $this->capNhatThatBai($dangKy, 'Sinh viên chưa đóng đủ học phí. Vui lòng đóng học phí để được xếp lớp.');
            return ['success' => false];
        }

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
     * Kiểm tra sinh viên đã đóng đủ học phí chưa
     * 
     * @param DangKyMonHocTam $dangKy
     * @return bool
     */
    protected function kiemTraDaDongHocPhi(DangKyMonHocTam $dangKy): bool
    {
        $hocPhi = HocPhiHocKy::where('sinh_vien_id', $dangKy->sinh_vien_id)
            ->where('hoc_ky_id', $dangKy->hoc_ky_id)
            ->first();

        if (!$hocPhi) {
            Log::warning("Không tìm thấy học phí cho sinh viên {$dangKy->sinh_vien_id} - Học kỳ {$dangKy->hoc_ky_id}");
            return false;
        }

        // Chỉ xếp lớp nếu đã đóng đủ học phí
        if ($hocPhi->trang_thai !== 'da_nop_du') {
            Log::info("Sinh viên {$dangKy->sinh_vien_id} chưa đóng đủ học phí. Trạng thái: {$hocPhi->trang_thai}");
            return false;
        }

        return true;
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
            ->get();

        if ($dsLop->isEmpty()) {
            return null;
        }

        // 2. Tính số lượng sinh viên hiện có trong từng lớp và sắp xếp theo ưu tiên
        // Ưu tiên lớp có nhiều sinh viên trước (lớp đã có sinh viên)
        $dsLopWithCount = $dsLop->map(function ($lop) {
            $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                ->count();
            $sucChua = $lop->suc_chua ?? 0;
            return [
                'lop' => $lop,
                'so_luong' => $soLuongThucTe,
                'suc_chua' => $sucChua,
                'con_trong' => $sucChua - $soLuongThucTe
            ];
        })
        ->filter(function ($item) {
            return $item['con_trong'] > 0; // Chỉ lấy lớp còn chỗ
        })
        ->sortByDesc('so_luong'); // Sắp xếp: lớp có nhiều sinh viên trước

        if ($dsLopWithCount->isEmpty()) {
            return null;
        }

        // 3. Đếm số lượng sinh viên đang chờ xếp cùng môn
        $soLuongChoXepCungMon = DangKyMonHocTam::where('hoc_ky_id', $dangKy->getAttribute('hoc_ky_id'))
            ->where('mon_hoc_id', $dangKy->getAttribute('mon_hoc_id'))
            ->where('trang_thai', 'cho_xep_lop')
            ->count();

        // 4. Lấy lịch học hiện tại của sinh viên
        $lichHienTai = $this->layLichHocHienTai(
            $dangKy->getAttribute('sinh_vien_id'),
            $dangKy->getAttribute('hoc_ky_id')
        );

        // 5. Tìm lớp không trùng lịch, ưu tiên lớp có sinh viên
        foreach ($dsLopWithCount as $item) {
            $lop = $item['lop'];
            $soLuongThucTe = $item['so_luong'];

            // Kiểm tra quy tắc: Nếu lớp trống (0 sinh viên) thì cần ít nhất 2 sinh viên đang chờ xếp
            if ($soLuongThucTe == 0 && $soLuongChoXepCungMon < 2) {
                continue; // Lớp trống nhưng chưa đủ 2 sinh viên, bỏ qua
            }

            // Kiểm tra không trùng lịch
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
        
        // Chỉ lấy các lớp đang hoạt động (đã xếp lớp hoặc đang học)
        // Không lấy các lớp đã hủy, kết thúc, hoặc chưa xếp lớp
        $lopDaDangKy = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
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
            // Observer sẽ tự động cập nhật so_luong_dang_ky, không cần cập nhật thủ công
            $lopHocPhanSinhVien = LopHocPhanSinhVien::create([
                'sinh_vien_id' => $dangKy->sinh_vien_id,
                'lop_hoc_phan_id' => $lop->id,
                'dang_ky_tam_id' => $dangKy->id,
                'trang_thai' => 'da_xep_lop',
                'ngay_dang_ky' => $dangKy->ngay_dang_ky,
                'ngay_xep_lop' => Carbon::now(),
                'phuong_thuc_xep' => 'tu_dong'
            ]);

            // 2. Refresh lại model để có số lượng mới nhất (Observer đã tự động cập nhật)
            $lop->refresh();

            // 3. Cập nhật trạng thái đăng ký tạm
            $dangKy->trang_thai = 'da_xep_lop';
            $dangKy->save();

            DB::commit();
            
            // 4. PHASE 8: Học phí sẽ được tính TỰ ĐỘNG qua LopHocPhanSinhVienObserver
            // Không cần gọi thủ công ở đây nữa
            Log::info("✅ Đã xếp lớp sinh viên {$dangKy->sinh_vien_id} vào lớp {$lop->ma_lop_hp}");
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