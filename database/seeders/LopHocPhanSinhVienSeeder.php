<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHocPhanSinhVien;
use App\Models\DangKyMonHocTam;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use App\Models\DaoTao\SinhVien;
use Illuminate\Support\Facades\DB;

class LopHocPhanSinhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Logic THỰC TẾ: Xếp sinh viên vào các lớp học phần hiện có trong học kỳ
     * dựa vào ngành của sinh viên (không bắt buộc phải có chương trình khung chi tiết)
     */
    public function run(): void
    {
        // Lấy học kỳ hiện tại
        $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();

        if (!$hocKy) {
            $this->command->warn('Không tìm thấy học kỳ hiện tại.');
            return;
        }

        $this->command->info('Học kỳ hiện tại: ' . $hocKy->ten_hoc_ky);

        // Lấy tất cả lớp học phần của học kỳ hiện tại
        $lopHocPhans = LopHocPhan::where('hoc_ky_id', $hocKy->id)->get();

        if ($lopHocPhans->isEmpty()) {
            $this->command->warn('Không có lớp học phần nào trong học kỳ hiện tại.');
            return;
        }

        $this->command->info('Tìm thấy ' . $lopHocPhans->count() . ' lớp học phần');

        // Lấy tất cả sinh viên
        $sinhViens = SinhVien::with(['nganh'])->get();

        if ($sinhViens->isEmpty()) {
            $this->command->warn('Không có sinh viên nào.');
            return;
        }

        $this->command->info('Tìm thấy ' . $sinhViens->count() . ' sinh viên');

        $lopSinhViens = [];
        $now = now();
        $totalAssigned = 0;

        // Với mỗi sinh viên, xếp vào 3-5 lớp ngẫu nhiên
        foreach ($sinhViens as $sinhVien) {
            // Số môn đăng ký: 3-5 môn
            $soMonDangKy = rand(3, 5);

            // Lấy ngẫu nhiên các lớp học phần
            $lopHocPhanSample = $lopHocPhans->random(min($soMonDangKy, $lopHocPhans->count()));

            foreach ($lopHocPhanSample as $lopHocPhan) {
                // Kiểm tra đã đăng ký chưa
                $exists = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('sinh_vien_id', $sinhVien->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Kiểm tra lớp còn chỗ không (soft check, cho phép over capacity một chút để test)
                $soLuongHienTai = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)->count();

                // Không vượt quá sức chứa (tôn trọng ràng buộc DB)
                if ($soLuongHienTai >= $lopHocPhan->suc_chua) {
                    continue;
                }

                // Tạo hoặc lấy đăng ký tạm nếu có
                $dangKyTamId = null;
                $dangKyTam = DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
                    ->where('mon_hoc_id', $lopHocPhan->mon_hoc_id)
                    ->where('hoc_ky_id', $hocKy->id)
                    ->first();

                if ($dangKyTam) {
                    $dangKyTamId = $dangKyTam->id;
                    $ngayDangKy = $dangKyTam->ngay_dang_ky;
                } else {
                    $ngayDangKy = now()->subDays(rand(7, 30));
                }

                // Xác định trạng thái
                // 70% đang học, 20% đã hoàn thành, 10% bỏ học
                $rand = rand(1, 10);
                if ($rand <= 7) {
                    $trangThai = 'dang_hoc';
                } elseif ($rand <= 9) {
                    $trangThai = 'da_hoan_thanh';
                } else {
                    $trangThai = 'bo_hoc';
                }

                // Phương thức xếp: 85% tự động, 15% thủ công
                $phuongThucXep = rand(1, 100) <= 85 ? 'tu_dong' : 'thu_cong';

                $ngayXepLop = \Carbon\Carbon::parse($ngayDangKy)->addDays(rand(1, 5));

                $lopSinhViens[] = [
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'sinh_vien_id' => $sinhVien->id,
                    'dang_ky_tam_id' => $dangKyTamId,
                    'ngay_dang_ky' => $ngayDangKy,
                    'ngay_xep_lop' => $ngayXepLop,
                    'nguoi_duyet_id' => $phuongThucXep == 'thu_cong' ? 1 : null,
                    'ngay_duyet' => $phuongThucXep == 'thu_cong' ? $ngayXepLop : null,
                    'phuong_thuc_xep' => $phuongThucXep,
                    'trang_thai' => $trangThai,
                    'ly_do_huy' => $trangThai == 'bo_hoc' ? 'Sinh viên xin bỏ học' : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $totalAssigned++;

                // Update capacity - Sửa tên cột đúng
                // Cập nhật số lượng đăng ký trong DB, kiểm tra trực tiếp giá trị hiện tại để tránh vi phạm ràng buộc
                $currentRegistered = DB::table('lop_hoc_phan')->where('id', $lopHocPhan->id)->value('so_luong_dang_ky');
                if ($currentRegistered >= $lopHocPhan->suc_chua) {
                    // nếu DB đã đầy (có thể do dữ liệu cũ), bỏ qua
                    continue;
                }

                DB::table('lop_hoc_phan')
                    ->where('id', $lopHocPhan->id)
                    ->increment('so_luong_dang_ky');

                // Insert batch mỗi 100 records
                if (count($lopSinhViens) >= 100) {
                    LopHocPhanSinhVien::insert($lopSinhViens);
                    $lopSinhViens = [];
                }
            }
        }

        // Insert records còn lại
        if (!empty($lopSinhViens)) {
            LopHocPhanSinhVien::insert($lopSinhViens);
        }

        $this->command->info("✅ Đã xếp {$totalAssigned} lượt sinh viên vào lớp học phần");
        $this->command->info("📊 Trung bình mỗi sinh viên: " . round($totalAssigned / $sinhViens->count(), 2) . " môn");
    }
}
