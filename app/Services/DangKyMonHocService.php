<?php

namespace App\Services;

use App\Models\DaoTao\MonHocTienQuyet;
use App\Models\DaoTao\MonHoc;
use App\Models\KetQuaHocTap;
use App\Models\DangKyMonHocTam;
use App\Models\LichHocCoDinh;
use App\Models\LopHocPhanSinhVien;
use App\Models\LopHocPhan;
use App\Models\DaoTao\HocKy;

class DangKyMonHocService
{
    /**
     * Kiểm tra môn tiên quyết
     * 
     * @param int $sinhVienId
     * @param int $monHocId
     * @return array ['passed' => bool, 'missing_subjects' => array]
     */
    public function validatePrerequisites($sinhVienId, $monHocId): array
    {
        $monTienQuyet = MonHocTienQuyet::where('mon_hoc_id', $monHocId)
            ->where('loai_tien_quyet', 'bat_buoc')
            ->where('dieu_kien_qua_mon', true)
            ->get();

        $monChuaHoc = [];

        foreach ($monTienQuyet as $tq) {
            $ketQua = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId, $tq) {
                $q->where('sinh_vien_id', $sinhVienId)
                    ->whereHas('lopHocPhan', function ($q2) use ($tq) {
                        $q2->where('mon_hoc_id', $tq->mon_tien_quyet_id);
                    });
            })->where('qua_mon', true)->exists();

            if (!$ketQua) {
                $monHoc = MonHoc::find($tq->mon_tien_quyet_id);
                $monChuaHoc[] = [
                    'id' => $monHoc->id,
                    'ma_mon' => $monHoc->ma_mon,
                    'ten_mon' => $monHoc->ten_mon
                ];
            }
        }

        return [
            'passed' => empty($monChuaHoc),
            'missing_subjects' => $monChuaHoc
        ];
    }

    /**
     * Kiểm tra tổng số tín chỉ đăng ký trong học kỳ
     * 
     * @param int $sinhVienId
     * @param int $hocKyId
     * @param int $soTinChiMoi
     * @return array ['passed' => bool, 'current_credits' => int, 'new_total' => int]
     */
    public function validateTotalCredits($sinhVienId, $hocKyId, $soTinChiMoi): array
    {
        $tongTinChi = DangKyMonHocTam::where('sinh_vien_id', $sinhVienId)
            ->where('hoc_ky_id', $hocKyId)
            ->whereIn('trang_thai', ['cho_xep_lop', 'da_xep_lop'])
            ->join('mon_hoc', 'dang_ky_mon_hoc_tam.mon_hoc_id', '=', 'mon_hoc.id')
            ->sum('mon_hoc.so_tin_chi');

        $newTotal = $tongTinChi + $soTinChiMoi;

        return [
            'passed' => $newTotal <= 24,
            'current_credits' => $tongTinChi,
            'new_total' => $newTotal,
            'max_credits' => 24
        ];
    }

    /**
     * Kiểm tra xem sinh viên đã đăng ký môn này trong học kỳ chưa
     * 
     * @param int $sinhVienId
     * @param int $monHocId
     * @param int $hocKyId
     * @return bool
     */
    public function checkDuplicateRegistration($sinhVienId, $monHocId, $hocKyId): bool
    {
        return DangKyMonHocTam::where('sinh_vien_id', $sinhVienId)
            ->where('mon_hoc_id', $monHocId)
            ->where('hoc_ky_id', $hocKyId)
            ->whereIn('trang_thai', ['cho_xep_lop', 'da_xep_lop'])
            ->exists();
    }

    /**
     * Kiểm tra trùng lịch học (sau khi đã xếp lớp)
     * 
     * @param int $sinhVienId
     * @param int $lopHocPhanId
     * @return array ['passed' => bool, 'conflicts' => array]
     */
    public function checkScheduleConflict($sinhVienId, $lopHocPhanId): array
    {
        $lichMoi = LichHocCoDinh::where('lop_hoc_phan_id', $lopHocPhanId)->get();

        $lopDaDangKy = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->pluck('lop_hoc_phan_id');

        $conflicts = [];

        foreach ($lichMoi as $lich) {
            $conflict = LichHocCoDinh::whereIn('lop_hoc_phan_id', $lopDaDangKy)
                ->where('thu_trong_tuan', $lich->thu_trong_tuan)
                ->where(function ($q) use ($lich) {
                    $q->whereBetween('tiet_bat_dau', [$lich->tiet_bat_dau, $lich->tiet_ket_thuc - 1])
                        ->orWhereBetween('tiet_ket_thuc', [$lich->tiet_bat_dau + 1, $lich->tiet_ket_thuc])
                        ->orWhere(function ($q2) use ($lich) {
                            $q2->where('tiet_bat_dau', '<=', $lich->tiet_bat_dau)
                                ->where('tiet_ket_thuc', '>=', $lich->tiet_ket_thuc);
                        });
                })
                ->with('lopHocPhan.monHoc')
                ->first();

            if ($conflict) {
                $conflicts[] = [
                    'thu' => $lich->thu_trong_tuan,
                    'tiet' => $lich->tiet_bat_dau . '-' . $lich->tiet_ket_thuc,
                    'mon_hoc' => $conflict->lopHocPhan->monHoc->ten_mon,
                    'lop_hoc_phan' => $conflict->lopHocPhan->ma_lop_hp
                ];
            }
        }

        return [
            'passed' => empty($conflicts),
            'conflicts' => $conflicts
        ];
    }

    /**
     * Kiểm tra sức chứa lớp học phần
     * 
     * @param int $lopHocPhanId
     * @return array ['passed' => bool, 'current' => int, 'max' => int]
     */
    public function checkClassCapacity($lopHocPhanId): array
    {
        $lopHocPhan = LopHocPhan::findOrFail($lopHocPhanId);

        return [
            'passed' => $lopHocPhan->so_luong_dang_ky < $lopHocPhan->suc_chua,
            'current' => $lopHocPhan->so_luong_dang_ky,
            'max' => $lopHocPhan->suc_chua,
            'available' => $lopHocPhan->suc_chua - $lopHocPhan->so_luong_dang_ky
        ];
    }

    /**
     * Kiểm tra tất cả điều kiện đăng ký môn học
     * 
     * @param int $sinhVienId
     * @param int $monHocId
     * @param int $hocKyId
     * @return array
     */
    public function validateRegistration($sinhVienId, $monHocId, $hocKyId): array
    {
        $errors = [];

        // 1. Kiểm tra trùng đăng ký
        if ($this->checkDuplicateRegistration($sinhVienId, $monHocId, $hocKyId)) {
            $errors[] = 'Bạn đã đăng ký môn học này trong học kỳ hiện tại.';
        }

        // 2. Kiểm tra môn tiên quyết
        $prerequisiteCheck = $this->validatePrerequisites($sinhVienId, $monHocId);
        if (!$prerequisiteCheck['passed']) {
            $monChuaHoc = collect($prerequisiteCheck['missing_subjects'])->pluck('ten_mon')->implode(', ');
            $errors[] = "Chưa hoàn thành môn tiên quyết: {$monChuaHoc}";
        }

        // 3. Kiểm tra tổng tín chỉ
        $monHoc = MonHoc::findOrFail($monHocId);
        $creditCheck = $this->validateTotalCredits($sinhVienId, $hocKyId, $monHoc->so_tin_chi);
        if (!$creditCheck['passed']) {
            $errors[] = "Vượt quá số tín chỉ tối đa ({$creditCheck['new_total']}/{$creditCheck['max_credits']} tín chỉ)";
        }

        return [
            'passed' => empty($errors),
            'errors' => $errors,
            'details' => [
                'prerequisite' => $prerequisiteCheck,
                'credits' => $creditCheck
            ]
        ];
    }
}
