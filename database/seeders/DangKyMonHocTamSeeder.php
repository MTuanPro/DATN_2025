<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DangKyMonHocTam;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
use App\Models\LopHocPhan;
use Illuminate\Support\Facades\DB;

class DangKyMonHocTamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy học kỳ hiện tại
        $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();

        if (!$hocKy) {
            $this->command->warn('Không tìm thấy học kỳ hiện tại. Bỏ qua seeder DangKyMonHocTam.');
            return;
        }

        // Lấy tất cả sinh viên
        $sinhViens = SinhVien::with('chuyenNganh')->limit(50)->get();

        if ($sinhViens->isEmpty()) {
            $this->command->warn('Không có sinh viên. Chạy SinhVienSeeder trước.');
            return;
        }

        // Lấy tất cả lớp học phần trong học kỳ
        $lopHocPhans = LopHocPhan::where('hoc_ky_id', $hocKy->id)
            ->with('monHoc')
            ->get();

        if ($lopHocPhans->isEmpty()) {
            $this->command->warn('Không có lớp học phần. Chạy LopHocPhanSeeder trước.');
            return;
        }

        $dangKys = [];
        $now = now();

        foreach ($sinhViens as $sinhVien) {
            // Mỗi sinh viên đăng ký 3-5 môn
            $soMonDangKy = rand(3, 5);
            $lopDaDangKy = [];

            for ($i = 0; $i < $soMonDangKy; $i++) {
                // Chọn ngẫu nhiên lớp học phần
                $lopHocPhan = $lopHocPhans->random();

                // Tránh trùng môn học
                if (in_array($lopHocPhan->mon_hoc_id, $lopDaDangKy)) {
                    continue;
                }

                $lopDaDangKy[] = $lopHocPhan->mon_hoc_id;

                // Tính độ ưu tiên
                $uuTien = 0;

                // Sinh viên năm cuối (kỳ >= 7)
                if ($sinhVien->ky_hien_tai >= 7) {
                    $uuTien += 100;
                }

                // Ngẫu nhiên một số sinh viên học lại (có độ ưu tiên)
                if (rand(1, 10) <= 2) { // 20% sinh viên học lại
                    $uuTien += 50;
                }

                // Trạng thái: 70% chờ xếp lớp, 20% đã xếp lớp, 10% thất bại
                $rand = rand(1, 10);
                if ($rand <= 7) {
                    $trangThai = 'cho_xep_lop';
                    $lyDoThatBai = null;
                } elseif ($rand <= 9) {
                    $trangThai = 'da_xep_lop';
                    $lyDoThatBai = null;
                } else {
                    $trangThai = 'that_bai';
                    $lyDoThatBai = 'Không còn chỗ trong các lớp học phần';
                }

                // Ngày đăng ký trong khoảng thời gian đăng ký
                $ngayDangKy = $hocKy->ngay_bat_dau_dang_ky->copy()->addDays(rand(0, 7));

                $dangKys[] = [
                    'sinh_vien_id' => $sinhVien->id,
                    'mon_hoc_id' => $lopHocPhan->mon_hoc_id,
                    'hoc_ky_id' => $hocKy->id,
                    'ngay_dang_ky' => $ngayDangKy,
                    'uu_tien' => $uuTien,
                    'trang_thai' => $trangThai,
                    'ly_do_that_bai' => $lyDoThatBai,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert batch
        foreach (array_chunk($dangKys, 100) as $chunk) {
            DangKyMonHocTam::insert($chunk);
        }

        $this->command->info('✅ Đã tạo ' . count($dangKys) . ' đăng ký môn học tạm thời');
    }
}
