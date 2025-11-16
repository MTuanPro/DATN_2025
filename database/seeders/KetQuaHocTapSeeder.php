<?php

namespace Database\Seeders;

use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Models\CauHinhDauDiem;
use Illuminate\Database\Seeder;

class KetQuaHocTapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Bắt đầu tạo kết quả học tập...');

        // Lấy tất cả lớp học phần sinh viên đã đăng ký
        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('trang_thai', 'dang_hoc')
            ->with(['lopHocPhan.monHoc'])
            ->get();

        $count = 0;

        foreach ($lopHocPhanSinhViens as $lopHocPhanSV) {
            // Kiểm tra xem đã có kết quả học tập chưa
            if (KetQuaHocTap::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)->exists()) {
                continue;
            }

            // Lấy cấu hình đầu điểm của lớp học phần
            $cauHinhDauDiems = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanSV->lop_hoc_phan_id)->get();
            
            if ($cauHinhDauDiems->isEmpty()) {
                continue;
            }

            // Tính điểm tổng kết từ các đầu điểm (giả sử đã có điểm)
            // Nếu chưa có điểm, bỏ qua
            $diemTongKet = $this->tinhDiemTongKet($lopHocPhanSV->id, $cauHinhDauDiems);
            
            if ($diemTongKet === null) {
                continue; // Chưa có điểm, bỏ qua
            }

            // Tính điểm chữ và điểm hệ 4
            $diemChu = KetQuaHocTap::tinhDiemChu($diemTongKet);
            $diemHe4 = KetQuaHocTap::tinhDiemHe4($diemTongKet);
            $quaMon = $diemTongKet >= 4.0;

            KetQuaHocTap::create([
                'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                'diem_he_10' => $diemTongKet,
                'diem_he_4' => $diemHe4,
                'diem_chu' => $diemChu,
                'qua_mon' => $quaMon,
                'ghi_chu' => $quaMon ? 'Đạt' : 'Không đạt',
            ]);

            $count++;
        }

        $this->command->info("✅ Đã tạo {$count} kết quả học tập");
    }

    /**
     * Tính điểm tổng kết từ các đầu điểm
     */
    private function tinhDiemTongKet($lopHocPhanSinhVienId, $cauHinhDauDiems)
    {
        $tongDiem = 0;
        $tongTyLe = 0;
        $coDiem = false;

        foreach ($cauHinhDauDiems as $cauHinh) {
            // Lấy điểm từ bảng nhap_diem
            $nhapDiems = \App\Models\NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
                ->where('cau_hinh_id', $cauHinh->id)
                ->get();

            if ($nhapDiems->isEmpty()) {
                continue; // Chưa có điểm cho đầu điểm này
            }

            $coDiem = true;
            
            // Tính điểm trung bình của đầu điểm (nếu có nhiều cột)
            $diemTrungBinh = $nhapDiems->avg('diem_so');
            
            if ($diemTrungBinh !== null) {
                $tongDiem += $diemTrungBinh * ($cauHinh->ty_le / 100);
                $tongTyLe += $cauHinh->ty_le;
            }
        }

        // Chỉ tính điểm tổng kết nếu đã có ít nhất một đầu điểm
        if (!$coDiem || $tongTyLe == 0) {
            return null;
        }

        // Chuẩn hóa điểm về thang 10 (nếu tổng tỷ lệ chưa đủ 100%)
        if ($tongTyLe < 100) {
            $tongDiem = ($tongDiem / $tongTyLe) * 100;
        }

        return round($tongDiem, 2);
    }
}

