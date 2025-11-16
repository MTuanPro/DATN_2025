<?php

namespace Database\Seeders;

use App\Models\CanhBaoHocVu;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use App\Models\HocPhiHocKy;
use App\Models\DiemDanh;
use App\Models\LopHocPhanSinhVien;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CanhBaoHocVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⚠️  Bắt đầu tạo cảnh báo học vụ...');

        // Lấy học kỳ hiện tại
        $hocKyHienTai = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        if (!$hocKyHienTai) {
            $hocKyHienTai = HocKy::orderBy('id', 'desc')->first();
        }

        if (!$hocKyHienTai) {
            $this->command->warn('⚠️  Không tìm thấy học kỳ, bỏ qua tạo cảnh báo');
            return;
        }

        $sinhViens = SinhVien::all();
        $count = 0;

        foreach ($sinhViens as $index => $sinhVien) {
            // Tạo cảnh báo cho một số sinh viên (khoảng 10%)
            if ($index % 10 !== 0) {
                continue;
            }

            $loaiCanhBao = $this->xacDinhLoaiCanhBao($sinhVien, $hocKyHienTai);
            
            if (!$loaiCanhBao) {
                continue;
            }

            $canhBao = $this->taoCanhBao($sinhVien, $hocKyHienTai, $loaiCanhBao);
            
            if ($canhBao) {
                $count++;
            }
        }

        $this->command->info("✅ Đã tạo {$count} cảnh báo học vụ");
    }

    /**
     * Xác định loại cảnh báo cho sinh viên
     */
    private function xacDinhLoaiCanhBao($sinhVien, $hocKy)
    {
        // Kiểm tra điểm thấp
        $ketQua = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVien, $hocKy) {
            $q->where('sinh_vien_id', $sinhVien->id)
                ->whereHas('lopHocPhan', function ($q2) use ($hocKy) {
                    $q2->where('hoc_ky_id', $hocKy->id);
                });
        })->get();

        if ($ketQua->isNotEmpty()) {
            $diemTrungBinh = $ketQua->avg('diem_he_10');
            if ($diemTrungBinh < 1.0) {
                return 'diem_thap';
            }
        }

        // Kiểm tra vắng nhiều
        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($q) use ($hocKy) {
                $q->where('hoc_ky_id', $hocKy->id);
            })
            ->get();

        foreach ($lopHocPhanSinhViens as $lopHocPhanSV) {
            $tongBuoi = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)->count();
            $soLanVang = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)
                ->where('trang_thai', 'vang')
                ->count();

            if ($tongBuoi > 0 && ($soLanVang / $tongBuoi) > 0.2) {
                return 'vang_nhieu';
            }
        }

        // Kiểm tra nợ học phí
        $hocPhi = HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
            ->where('hoc_ky_id', $hocKy->id)
            ->first();

        if ($hocPhi && $hocPhi->so_tien_con_lai > 0 && $hocPhi->han_dong < now()) {
            // Kiểm tra nợ nhiều học kỳ
            $soHocKyNo = HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
                ->where('so_tien_con_lai', '>', 0)
                ->count();

            if ($soHocKyNo >= 2) {
                return 'no_hoc_phi';
            }
        }

        return null;
    }

    /**
     * Tạo cảnh báo
     */
    private function taoCanhBao($sinhVien, $hocKy, $loaiCanhBao)
    {
        $lyDo = '';
        $mucDo = 'canh_cao';

        switch ($loaiCanhBao) {
            case 'diem_thap':
                $ketQua = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVien, $hocKy) {
                    $q->where('sinh_vien_id', $sinhVien->id)
                        ->whereHas('lopHocPhan', function ($q2) use ($hocKy) {
                            $q2->where('hoc_ky_id', $hocKy->id);
                        });
                })->get();
                
                $diemTrungBinh = $ketQua->avg('diem_he_10');
                $lyDo = "GPA học kỳ {$hocKy->ten_hoc_ky}: " . number_format($diemTrungBinh, 2) . "/10.0 (< 1.0). Sinh viên có nguy cơ bị buộc thôi học.";
                $mucDo = 'buoc_thoi_hoc';
                break;

            case 'vang_nhieu':
                $lopHocPhanSV = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                    ->whereHas('lopHocPhan', function ($q) use ($hocKy) {
                        $q->where('hoc_ky_id', $hocKy->id);
                    })
                    ->first();

                if ($lopHocPhanSV) {
                    $tongBuoi = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)->count();
                    $soLanVang = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)
                        ->where('trang_thai', 'vang')
                        ->count();
                    $tiLeVang = $tongBuoi > 0 ? ($soLanVang / $tongBuoi) * 100 : 0;
                    
                    $lyDo = "Vắng mặt {$soLanVang}/{$tongBuoi} buổi học (" . number_format($tiLeVang, 1) . "%) trong học kỳ {$hocKy->ten_hoc_ky}. Vượt quá quy định 20%.";
                    $mucDo = 'dinh_chi';
                }
                break;

            case 'no_hoc_phi':
                $hocPhis = HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
                    ->where('so_tien_con_lai', '>', 0)
                    ->with('hocKy')
                    ->get();

                $tongNo = $hocPhis->sum('so_tien_con_lai');
                $danhSachHocKy = $hocPhis->pluck('hocKy.ten_hoc_ky')->implode(', ');
                
                $lyDo = "Nợ học phí {$hocPhis->count()} học kỳ ({$danhSachHocKy}). Tổng số tiền nợ: " . number_format($tongNo) . " VNĐ.";
                $mucDo = 'canh_cao';
                break;
        }

        if (empty($lyDo)) {
            return null;
        }

        return CanhBaoHocVu::create([
            'sinh_vien_id' => $sinhVien->id,
            'hoc_ky_id' => $hocKy->id,
            'loai_canh_bao' => $loaiCanhBao,
            'muc_do' => $mucDo,
            'ly_do' => $lyDo,
            'trang_thai' => 'chua_xu_ly',
            'ngay_canh_bao' => now(),
            'nguoi_tao_id' => 1, // System
        ]);
    }
}

