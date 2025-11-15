<?php

namespace Database\Seeders;

use App\Models\NhapDiem;
use App\Models\LopHocPhanSinhVien;
use App\Models\CauHinhDauDiem;
use Illuminate\Database\Seeder;

class NhapDiemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Bắt đầu tạo điểm chi tiết...');

        // Lấy tất cả lớp học phần sinh viên đã đăng ký
        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('trang_thai', 'dang_hoc')
            ->with(['lopHocPhan'])
            ->get();

        $count = 0;

        foreach ($lopHocPhanSinhViens as $lopHocPhanSV) {
            // Lấy cấu hình đầu điểm của lớp học phần
            $cauHinhDauDiems = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanSV->lop_hoc_phan_id)->get();
            
            if ($cauHinhDauDiems->isEmpty()) {
                continue;
            }

            foreach ($cauHinhDauDiems as $cauHinh) {
                // Kiểm tra xem đã có điểm chưa
                if (NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)
                    ->where('cau_hinh_id', $cauHinh->id)
                    ->exists()) {
                    continue;
                }

                // Tạo điểm cho từng cột (nếu có nhiều cột)
                for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                    // Random điểm từ 4.0 đến 10.0 (phân bố đều)
                    $diemSo = round(mt_rand(40, 100) / 10, 1);

                    // Điều chỉnh điểm theo loại đầu điểm
                    $diemSo = $this->tinhDiemTheoLoai($cauHinh->ten_dau_diem, $diemSo);

                    NhapDiem::create([
                        'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                        'cau_hinh_id' => $cauHinh->id,
                        'cot_diem' => $cot,
                        'diem_so' => $diemSo,
                        'ghi_chu' => $this->getGhiChu($cauHinh->ten_dau_diem, $cot),
                    ]);

                    $count++;
                }
            }
        }

        $this->command->info("✅ Đã tạo {$count} điểm chi tiết");
    }

    /**
     * Tính điểm theo loại đầu điểm
     */
    private function tinhDiemTheoLoai($tenDauDiem, $diemGoc)
    {
        // Điều chỉnh điểm theo loại
        switch (strtolower($tenDauDiem)) {
            case 'chuyên cần':
            case 'chuyen can':
                // Chuyên cần thường cao hơn (7.0 - 10.0)
                return max(7.0, min(10.0, $diemGoc + 1.0));
            
            case 'giữa kỳ':
            case 'giua ky':
                // Giữa kỳ (5.0 - 9.5)
                return max(5.0, min(9.5, $diemGoc));
            
            case 'cuối kỳ':
            case 'cuoi ky':
                // Cuối kỳ (4.0 - 10.0)
                return max(4.0, min(10.0, $diemGoc));
            
            case 'thực hành':
            case 'thuc hanh':
                // Thực hành (6.0 - 10.0)
                return max(6.0, min(10.0, $diemGoc + 0.5));
            
            case 'tiểu luận':
            case 'tieu luan':
                // Tiểu luận (5.5 - 9.5)
                return max(5.5, min(9.5, $diemGoc));
            
            default:
                return $diemGoc;
        }
    }

    /**
     * Lấy ghi chú cho điểm
     */
    private function getGhiChu($tenDauDiem, $cot)
    {
        if ($cot > 1) {
            return "Cột điểm {$cot} - {$tenDauDiem}";
        }
        return $tenDauDiem;
    }
}

