<?php

namespace App\Services;

use App\Models\DiemDanh;
use App\Models\CauHinhDauDiem;
use App\Models\NhapDiem;
use App\Models\LopHocPhanSinhVien;

class DieuKienThiService
{
    /**
     * Kiểm tra điều kiện được thi của sinh viên trong một lớp học phần
     * 
     * @param int $lopHocPhanSinhVienId ID của bản ghi lớp học phần sinh viên
     * @param int $lopHocPhanId ID của lớp học phần
     * @return array ['du_dieu_kien' => bool, 'ly_do' => string, 'ty_le_co_mat' => float, 'diem_trung_binh' => float|null]
     */
    public function kiemTraDieuKienThi($lopHocPhanSinhVienId, $lopHocPhanId)
    {
        // 1. Kiểm tra chuyên cần (vắng quá 20% = có mặt < 80%)
        // Đếm tổng số buổi đã có điểm danh của TẤT CẢ sinh viên trong lớp
        $tongBuoi = DiemDanh::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId);
        })
            ->distinct('lich_hoc_chi_tiet_id')
            ->count('lich_hoc_chi_tiet_id');

        // Đếm số buổi có mặt của sinh viên cụ thể
        $buoiCoMat = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
            ->where('trang_thai', 'co_mat')
            ->count();

        // Tính tỷ lệ có mặt
        $tyLeCoMat = $tongBuoi > 0
            ? round(($buoiCoMat / $tongBuoi) * 100, 1)
            : 100; // Nếu chưa có buổi học nào thì coi như đủ điều kiện

        // Vắng quá 20% = có mặt < 80%
        $khongDatChuyenCan = $tyLeCoMat < 80;

        // 2. Kiểm tra điểm trung bình các đầu điểm >= 2.0
        $diemTrungBinh = null;
        $khongDatDiem = false;

        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        if ($cauHinhs->isNotEmpty()) {
            $tongDiem = 0;
            $tongTyLe = 0;
            $coDiem = false;

            foreach ($cauHinhs as $cauHinh) {
                // Lấy điểm đã nhập
                $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
                    ->where('cau_hinh_id', $cauHinh->id)
                    ->get();

                if ($diems->isEmpty()) {
                    continue;
                }

                $coDiem = true;

                // Tính điểm trung bình của đầu điểm
                $diemTrungBinhDauDiem = $diems->avg('diem_so');

                if ($diemTrungBinhDauDiem !== null) {
                    $tongDiem += $diemTrungBinhDauDiem * ($cauHinh->ty_le / 100);
                    $tongTyLe += $cauHinh->ty_le;
                }
            }

            // Tính điểm trung bình (chuẩn hóa về thang 10 nếu tổng tỷ lệ < 100%)
            if ($coDiem && $tongTyLe > 0) {
                if ($tongTyLe < 100) {
                    $diemTrungBinh = round(($tongDiem / $tongTyLe) * 100, 2);
                } else {
                    $diemTrungBinh = round($tongDiem, 2);
                }

                // Không đạt nếu điểm < 2.0
                $khongDatDiem = $diemTrungBinh < 2.0;
            }
        }

        // Sinh viên KHÔNG đủ điều kiện thi nếu: vắng quá 20% HOẶC điểm < 2.0
        $duDieuKienThi = !$khongDatChuyenCan && !$khongDatDiem;

        // Tạo lý do không đủ điều kiện
        $lyDo = $this->taoLyDoKhongDuDieuKien($khongDatChuyenCan, $khongDatDiem, $tyLeCoMat, $diemTrungBinh);

        return [
            'du_dieu_kien' => $duDieuKienThi,
            'ly_do' => $lyDo,
            'ty_le_co_mat' => $tyLeCoMat,
            'diem_trung_binh' => $diemTrungBinh,
            'khong_dat_chuyen_can' => $khongDatChuyenCan,
            'khong_dat_diem' => $khongDatDiem,
            'tong_buoi' => $tongBuoi,
            'buoi_co_mat' => $buoiCoMat,
        ];
    }

    /**
     * Tạo lý do không đủ điều kiện thi
     */
    private function taoLyDoKhongDuDieuKien($khongDatChuyenCan, $khongDatDiem, $tyLeCoMat, $diemTrungBinh)
    {
        $lyDo = [];

        if ($khongDatChuyenCan) {
            $lyDo[] = "Tỷ lệ điểm danh không đạt yêu cầu (có mặt {$tyLeCoMat}%, yêu cầu tối thiểu 80%)";
        }

        if ($khongDatDiem && $diemTrungBinh !== null) {
            $lyDo[] = "Điểm trung bình chưa đạt yêu cầu (điểm: {$diemTrungBinh}/10, yêu cầu tối thiểu 2.0/10)";
        }

        if (empty($lyDo)) {
            return null;
        }

        return implode('; ', $lyDo);
    }

    /**
     * Kiểm tra hàng loạt sinh viên trong lớp học phần
     * 
     * @param int $lopHocPhanId ID của lớp học phần
     * @return array Mảng kết quả với key là sinh_vien_id
     */
    public function kiemTraHangLoat($lopHocPhanId)
    {
        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->get();

        $ketQua = [];

        foreach ($lopHocPhanSinhViens as $lhpsv) {
            $kiemTra = $this->kiemTraDieuKienThi($lhpsv->id, $lopHocPhanId);
            $ketQua[$lhpsv->sinh_vien_id] = [
                'lop_hoc_phan_sinh_vien_id' => $lhpsv->id,
                'sinh_vien_id' => $lhpsv->sinh_vien_id,
                'du_dieu_kien' => $kiemTra['du_dieu_kien'],
                'ly_do' => $kiemTra['ly_do'],
                'ty_le_co_mat' => $kiemTra['ty_le_co_mat'],
                'diem_trung_binh' => $kiemTra['diem_trung_binh'],
            ];
        }

        return $ketQua;
    }
}
