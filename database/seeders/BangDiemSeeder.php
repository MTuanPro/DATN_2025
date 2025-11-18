<?php

namespace Database\Seeders;

use App\Models\BangDiem;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Models\DaoTao\MonHoc;
use App\Services\DiemService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BangDiemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📋 Bắt đầu tạo bảng điểm tổng hợp...');

        $hocKys = HocKy::all();
        $sinhViens = SinhVien::all();
        $diemService = new DiemService();

        $count = 0;

        foreach ($hocKys as $hocKy) {
            foreach ($sinhViens as $sinhVien) {
                // Kiểm tra xem đã có bảng điểm chưa
                if (BangDiem::where('sinh_vien_id', $sinhVien->id)
                    ->where('hoc_ky_id', $hocKy->id)
                    ->exists()) {
                    continue;
                }

                // Lấy tất cả lớp học phần sinh viên đã đăng ký trong học kỳ này
                $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                    ->whereHas('lopHocPhan', function ($q) use ($hocKy) {
                        $q->where('hoc_ky_id', $hocKy->id);
                    })
                    ->get();

                if ($lopHocPhanSinhViens->isEmpty()) {
                    continue;
                }

                // Tính tổng tín chỉ đăng ký và đạt
                $tongTinChiDangKy = 0;
                $tongTinChiDat = 0;
                $tongDiem = 0;
                $tongDiemHe4 = 0;
                $soMonCoDiem = 0;

                foreach ($lopHocPhanSinhViens as $lopHocPhanSV) {
                    $monHoc = $lopHocPhanSV->lopHocPhan->monHoc;
                    $tongTinChiDangKy += $monHoc->so_tin_chi;

                    // Lấy kết quả học tập
                    $ketQua = KetQuaHocTap::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)->first();
                    
                    if ($ketQua && $ketQua->diem_he_10 !== null) {
                        $tongDiem += $ketQua->diem_he_10 * $monHoc->so_tin_chi;
                        $tongDiemHe4 += $ketQua->diem_he_4 * $monHoc->so_tin_chi;
                        $soMonCoDiem++;

                        if ($ketQua->qua_mon) {
                            $tongTinChiDat += $monHoc->so_tin_chi;
                        }
                    }
                }

                // Tính điểm trung bình (theo migration thực tế: diem_trung_binh_hoc_ky và diem_trung_binh_tich_luy)
                $diemTrungBinhHocKy = $tongTinChiDangKy > 0 && $soMonCoDiem > 0 
                    ? round($tongDiemHe4 / $tongTinChiDangKy, 2) 
                    : null;

                // Chỉ tạo bảng điểm nếu có ít nhất một môn có điểm
                if ($soMonCoDiem == 0) {
                    continue;
                }

                // Tính điểm trung bình tích lũy (lấy từ tất cả học kỳ trước đó + học kỳ hiện tại)
                $diemTrungBinhTichLuy = $this->tinhDiemTrungBinhTichLuy($sinhVien->id, $hocKy->id, $diemTrungBinhHocKy);

                // Tính điểm trung bình hệ 10 từ điểm hệ 4
                $diemTrungBinhHe10 = $diemTrungBinhHocKy !== null 
                    ? $diemService->chuyenDoiHe4SangHe10($diemTrungBinhHocKy) 
                    : null;

                // Tính xếp loại học tập
                $xepLoaiHocTap = $diemTrungBinhHocKy !== null 
                    ? BangDiem::tinhXepLoai($diemTrungBinhHocKy, $tongTinChiDat, $tongTinChiDangKy) 
                    : null;

                BangDiem::create([
                    'sinh_vien_id' => $sinhVien->id,
                    'hoc_ky_id' => $hocKy->id,
                    'tong_tin_chi_dang_ky' => $tongTinChiDangKy,
                    'tong_tin_chi_dat' => $tongTinChiDat,
                    'diem_trung_binh_hoc_ky' => $diemTrungBinhHocKy,
                    'diem_trung_binh_tich_luy' => $diemTrungBinhTichLuy,
                    'diem_trung_binh_he_10' => $diemTrungBinhHe10,
                    'diem_trung_binh_he_4' => $diemTrungBinhHocKy,
                    'xep_loai_hoc_tap' => $xepLoaiHocTap,
                    'da_cong_bo' => false,
                    'ngay_cong_bo' => null,
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Đã tạo {$count} bảng điểm tổng hợp");
    }

    /**
     * Tính điểm trung bình tích lũy
     */
    private function tinhDiemTrungBinhTichLuy($sinhVienId, $hocKyId, $diemTrungBinhHocKy)
    {
        // Lấy tất cả bảng điểm của các học kỳ trước đó
        $bangDiemsTruoc = BangDiem::where('sinh_vien_id', $sinhVienId)
            ->where('hoc_ky_id', '<', $hocKyId)
            ->whereNotNull('diem_trung_binh_hoc_ky')
            ->get();

        if ($bangDiemsTruoc->isEmpty() && $diemTrungBinhHocKy === null) {
            return null;
        }

        // Tính trung bình cộng của tất cả học kỳ
        $tongDiem = $bangDiemsTruoc->sum('diem_trung_binh_hoc_ky');
        if ($diemTrungBinhHocKy !== null) {
            $tongDiem += $diemTrungBinhHocKy;
        }

        $soHocKy = $bangDiemsTruoc->count();
        if ($diemTrungBinhHocKy !== null) {
            $soHocKy++;
        }

        return $soHocKy > 0 ? round($tongDiem / $soHocKy, 2) : null;
    }
}

