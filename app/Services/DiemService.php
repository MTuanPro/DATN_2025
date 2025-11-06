<?php

namespace App\Services;

use App\Models\CauHinhDauDiem;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Models\NhapDiem;
use App\Models\BangDiem;
use Illuminate\Support\Facades\DB;

class DiemService
{
    /**
     * Tính điểm tổng kết cho sinh viên trong lớp học phần
     */
    public function tinhDiemTong($lopHocPhanSinhVienId)
    {
        $lhpsv = LopHocPhanSinhVien::with('lopHocPhan')->find($lopHocPhanSinhVienId);

        if (!$lhpsv) {
            return false;
        }

        // 1. Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
            ->orderBy('id')
            ->get();

        if ($cauHinhs->isEmpty()) {
            return false;
        }

        $tongDiem = 0;
        $daCoTatCaDiem = true;

        // 2. Tính điểm từng đầu
        foreach ($cauHinhs as $cauHinh) {
            // Lấy điểm đã nhập
            $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
                ->where('cau_hinh_id', $cauHinh->id)
                ->orderBy('cot_diem')
                ->get();

            // Kiểm tra đủ cột chưa
            if ($diems->count() < $cauHinh->so_cot) {
                $daCoTatCaDiem = false;
                continue;
            }

            // Tính trung bình các cột
            $diemTrungBinh = $diems->avg('diem_so');

            // Nhân với tỷ lệ %
            $tongDiem += $diemTrungBinh * ($cauHinh->ty_le / 100);
        }

        // 3. Nếu chưa đủ điểm thì không tính
        if (!$daCoTatCaDiem) {
            return false;
        }

        // 4. Làm tròn 2 chữ số
        $diemHe10 = round($tongDiem, 2);

        // 5. Tính điểm hệ 4 và điểm chữ
        $diemHe4 = $this->chuyenDoiHe4($diemHe10);
        $diemChu = $this->chuyenDoiDiemChu($diemHe10);

        // 6. Qua môn?
        $quaMon = $diemHe10 >= 4.0;

        // 7. Update hoặc tạo mới kết quả học tập
        KetQuaHocTap::updateOrCreate(
            ['lop_hoc_phan_sinh_vien_id' => $lopHocPhanSinhVienId],
            [
                'diem_he_10' => $diemHe10,
                'diem_he_4' => $diemHe4,
                'diem_chu' => $diemChu,
                'qua_mon' => $quaMon,
            ]
        );

        return true;
    }

    /**
     * Tính điểm trung bình học kỳ (GPA)
     */
    public function tinhGPAHocKy($sinhVienId, $hocKyId)
    {
        $ketQuas = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId, $hocKyId) {
            $q->where('sinh_vien_id', $sinhVienId)
                ->whereHas('lopHocPhan', function ($q2) use ($hocKyId) {
                    $q2->where('hoc_ky_id', $hocKyId)
                        ->where('trang_thai_lop', 'da_duyet_diem');
                });
        })
            ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
            ->get();

        if ($ketQuas->isEmpty()) {
return 0;
        }

        $tongDiem = 0;
        $tongTinChi = 0;

        foreach ($ketQuas as $kq) {
            $tinChi = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
            $tongDiem += $kq->diem_he_4 * $tinChi;
            $tongTinChi += $tinChi;
        }

        return $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;
    }

    /**
     * Tính điểm trung bình tích lũy (GPA tích lũy)
     */
    public function tinhGPATichLuy($sinhVienId)
    {
        $ketQuas = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId) {
            $q->where('sinh_vien_id', $sinhVienId)
                ->whereHas('lopHocPhan', function ($q2) {
                    $q2->where('trang_thai_lop', 'da_duyet_diem');
                });
        })
            ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
            ->get();

        if ($ketQuas->isEmpty()) {
            return 0;
        }

        $tongDiem = 0;
        $tongTinChi = 0;

        foreach ($ketQuas as $kq) {
            $tinChi = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
            $tongDiem += $kq->diem_he_4 * $tinChi;
            $tongTinChi += $tinChi;
        }

        return $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;
    }

    /**
     * Tính tổng tín chỉ đạt
     */
    public function tinhTongTinChiDat($sinhVienId)
    {
        return KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId) {
            $q->where('sinh_vien_id', $sinhVienId)
                ->whereHas('lopHocPhan', function ($q2) {
                    $q2->where('trang_thai_lop', 'da_duyet_diem');
                });
        })
            ->where('qua_mon', true)
            ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
            ->get()
            ->sum(function ($kq) {
                return $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
            });
    }

    /**
     * Tính tổng tín chỉ đăng ký trong học kỳ
     */
    public function tinhTongTinChiDangKy($sinhVienId, $hocKyId)
    {
        return LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
            ->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            })
            ->with('lopHocPhan.monHoc')
            ->get()
            ->sum(function ($lhpsv) {
                return $lhpsv->lopHocPhan->monHoc->so_tin_chi;
            });
    }

    /**
     * Tạo hoặc cập nhật bảng điểm học kỳ
     */
    public function capNhatBangDiem($sinhVienId, $hocKyId)
    {
        $tinChiDangKy = $this->tinhTongTinChiDangKy($sinhVienId, $hocKyId);
        $tinChiDat = $this->tinhTongTinChiDat($sinhVienId);
        $gpaHocKy = $this->tinhGPAHocKy($sinhVienId, $hocKyId);
        
        // Tính điểm TB hệ 10 từ GPA hệ 4
        $diemTBHe10 = $this->chuyenDoiHe4SangHe10($gpaHocKy);
        
        // Xếp loại
$xepLoai = BangDiem::tinhXepLoai($gpaHocKy, $tinChiDat, $tinChiDangKy);

        BangDiem::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVienId,
                'hoc_ky_id' => $hocKyId,
            ],
            [
                'tong_tin_chi_dang_ky' => $tinChiDangKy,
                'tong_tin_chi_dat' => $tinChiDat,
                'diem_trung_binh_he_10' => $diemTBHe10,
                'diem_trung_binh_he_4' => $gpaHocKy,
                'xep_loai_hoc_tap' => $xepLoai,
            ]
        );

        return true;
    }

    /**
     * Chuyển đổi điểm hệ 10 sang hệ 4
     */
    public function chuyenDoiHe4($diemHe10)
    {
        if ($diemHe10 >= 9.0) return 4.0;
        if ($diemHe10 >= 8.5) return 3.7;
        if ($diemHe10 >= 8.0) return 3.5;
        if ($diemHe10 >= 7.0) return 3.0;
        if ($diemHe10 >= 6.5) return 2.5;
        if ($diemHe10 >= 5.5) return 2.0;
        if ($diemHe10 >= 5.0) return 1.5;
        if ($diemHe10 >= 4.0) return 1.0;
        return 0.0;
    }

    /**
     * Chuyển đổi điểm hệ 4 sang hệ 10 (ước lượng)
     */
    public function chuyenDoiHe4SangHe10($diemHe4)
    {
        if ($diemHe4 >= 3.7) return 9.0;
        if ($diemHe4 >= 3.5) return 8.5;
        if ($diemHe4 >= 3.0) return 8.0;
        if ($diemHe4 >= 2.5) return 7.0;
        if ($diemHe4 >= 2.0) return 6.5;
        if ($diemHe4 >= 1.5) return 5.5;
        if ($diemHe4 >= 1.0) return 5.0;
        return 4.0;
    }

    /**
     * Chuyển đổi điểm sang điểm chữ
     */
    public function chuyenDoiDiemChu($diemHe10)
    {
        if ($diemHe10 >= 9.0) return 'A+';
        if ($diemHe10 >= 8.5) return 'A';
        if ($diemHe10 >= 8.0) return 'B+';
        if ($diemHe10 >= 7.0) return 'B';
        if ($diemHe10 >= 6.5) return 'C+';
        if ($diemHe10 >= 5.5) return 'C';
        if ($diemHe10 >= 5.0) return 'D+';
        if ($diemHe10 >= 4.0) return 'D';
        return 'F';
    }

    /**
     * Xếp loại học tập
     */
    public function xepLoai($diemTB)
    {
        if ($diemTB >= 3.6) return 'xuat_sac';
        if ($diemTB >= 3.2) return 'gioi';
        if ($diemTB >= 2.5) return 'kha';
        if ($diemTB >= 2.0) return 'trung_binh';
        if ($diemTB >= 1.0) return 'yeu';
        return 'kem';
    }

    /**
     * Kiểm tra sinh viên đạt hay không
     */
    public function kiemTraDat($diemHe10)
    {
        return $diemHe10 >= 4.0;
    }
}