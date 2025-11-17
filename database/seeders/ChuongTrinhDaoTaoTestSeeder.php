<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\LopHocPhanSinhVien;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use Illuminate\Support\Facades\DB;

/**
 * Seeder chuyên biệt cho test chức năng Chương trình đào tạo
 * 
 * Seeder này đảm bảo:
 * 1. Sinh viên test có chuyên ngành được gán
 * 2. Sinh viên có kết quả học tập từ các môn trong CTĐT
 * 3. Có đủ dữ liệu để test các tính năng:
 *    - Xem CTĐT theo học kỳ
 *    - Thống kê tiến độ học tập
 *    - Điều kiện tốt nghiệp
 */
class ChuongTrinhDaoTaoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎓 Bắt đầu seed dữ liệu test cho Chương trình đào tạo...');
        $this->command->newLine();

        // ========================================
        // 1. Đảm bảo sinh viên test có chuyên ngành
        // ========================================
        $this->command->info('📋 Bước 1: Gán chuyên ngành cho sinh viên test...');
        $this->ganChuyenNganhChoSinhVienTest();
        $this->command->info('✅ Hoàn thành bước 1');
        $this->command->newLine();

        // ========================================
        // 2. Tạo kết quả học tập cho sinh viên từ các môn trong CTĐT
        // ========================================
        $this->command->info('📊 Bước 2: Tạo kết quả học tập từ CTĐT...');
        $this->taoKetQuaHocTapTuCTDT();
        $this->command->info('✅ Hoàn thành bước 2');
        $this->command->newLine();

        // ========================================
        // 3. Tạo kết quả học tập cho các sinh viên khác
        // ========================================
        $this->command->info('👥 Bước 3: Tạo kết quả học tập cho các sinh viên khác...');
        $this->taoKetQuaHocTapChoSinhVienKhac();
        $this->command->info('✅ Hoàn thành bước 3');
        $this->command->newLine();

        $this->command->info('🎉 Hoàn thành seed dữ liệu test cho Chương trình đào tạo!');
        $this->displaySummary();
    }

    /**
     * Gán chuyên ngành cho sinh viên test
     */
    private function ganChuyenNganhChoSinhVienTest(): void
    {
        // Tìm sinh viên test
        $sinhVienTest = SinhVien::where('email', 'sinhvien@smis.edu.vn')
            ->orWhere('ma_sinh_vien', 'SV2025001')
            ->first();

        if (!$sinhVienTest) {
            $this->command->warn('Không tìm thấy sinh viên test. Vui lòng chạy SinhVienSeeder trước.');
            return;
        }

        // Nếu chưa có chuyên ngành, gán một chuyên ngành phù hợp
        if (!$sinhVienTest->chuyen_nganh_id) {
            // Tìm chuyên ngành phù hợp với ngành của sinh viên
            $chuyenNganh = ChuyenNganh::where('nganh_id', $sinhVienTest->nganh_id)->first();
            
            if (!$chuyenNganh) {
                // Nếu không có chuyên ngành phù hợp, lấy chuyên ngành đầu tiên
                $chuyenNganh = ChuyenNganh::first();
            }

            if ($chuyenNganh) {
                $sinhVienTest->update([
                    'chuyen_nganh_id' => $chuyenNganh->id,
                ]);
                $this->command->info("  ✓ Đã gán chuyên ngành: {$chuyenNganh->ten_chuyen_nganh} cho sinh viên test");
            } else {
                $this->command->warn('  ⚠ Không tìm thấy chuyên ngành nào để gán');
            }
        } else {
            $chuyenNganh = ChuyenNganh::find($sinhVienTest->chuyen_nganh_id);
            $this->command->info("  ✓ Sinh viên test đã có chuyên ngành: {$chuyenNganh->ten_chuyen_nganh}");
        }

        // Gán chuyên ngành cho các sinh viên khác (nếu từ năm 3 trở lên)
        $sinhViensKhongCoChuyenNganh = SinhVien::whereNull('chuyen_nganh_id')
            ->where('ky_hien_tai', '>=', 5)
            ->get();

        $count = 0;
        foreach ($sinhViensKhongCoChuyenNganh as $sv) {
            $chuyenNganh = ChuyenNganh::where('nganh_id', $sv->nganh_id)->first();
            if ($chuyenNganh) {
                $sv->update(['chuyen_nganh_id' => $chuyenNganh->id]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->command->info("  ✓ Đã gán chuyên ngành cho {$count} sinh viên khác");
        }
    }

    /**
     * Tạo kết quả học tập từ CTĐT cho sinh viên test
     */
    private function taoKetQuaHocTapTuCTDT(): void
    {
        // Tìm sinh viên test
        $sinhVienTest = SinhVien::with(['chuyenNganh'])
            ->where('email', 'sinhvien@smis.edu.vn')
            ->orWhere('ma_sinh_vien', 'SV2025001')
            ->first();

        if (!$sinhVienTest || !$sinhVienTest->chuyen_nganh_id) {
            $this->command->warn('Sinh viên test chưa có chuyên ngành. Bỏ qua bước này.');
            return;
        }

        // Lấy chương trình khung của chuyên ngành
        $chuongTrinhKhung = ChuongTrinhKhung::with(['monHoc'])
            ->where('chuyen_nganh_id', $sinhVienTest->chuyen_nganh_id)
            ->orderBy('hoc_ky_goi_y')
            ->orderBy('thu_tu_hoc')
            ->get();

        if ($chuongTrinhKhung->isEmpty()) {
            $this->command->warn('Chuyên ngành chưa có chương trình khung. Vui lòng chạy ChuongTrinhKhungSeeder trước.');
            return;
        }

        $this->command->info("  Tìm thấy {$chuongTrinhKhung->count()} môn trong CTĐT");

        // Lấy học kỳ hiện tại và các học kỳ trước
        $hocKyHienTai = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        if (!$hocKyHienTai) {
            $hocKyHienTai = HocKy::orderBy('nam_hoc', 'desc')
                ->orderBy('hoc_ky', 'desc')
                ->first();
        }

        $count = 0;
        $kyHienTai = $sinhVienTest->ky_hien_tai ?? 1;

        // Tạo kết quả học tập cho các môn từ HK 1 đến HK hiện tại
        foreach ($chuongTrinhKhung as $ctk) {
            // Chỉ tạo kết quả cho các môn đã học (học kỳ <= kỳ hiện tại)
            if ($ctk->hoc_ky_goi_y > $kyHienTai) {
                continue;
            }

            // Tìm lớp học phần của môn này trong các học kỳ đã qua
            // Tìm học kỳ tương ứng với học kỳ gợi ý (học kỳ 1-8)
            // Học kỳ được tạo theo năm học, cần tìm học kỳ phù hợp
            $hocKy = $this->timHocKyTheoHocKyGoiY($ctk->hoc_ky_goi_y, $sinhVienTest);
            
            $lopHocPhan = null;
            if ($hocKy) {
                $lopHocPhan = LopHocPhan::where('mon_hoc_id', $ctk->mon_hoc_id)
                    ->where('hoc_ky_id', $hocKy->id)
                    ->first();
            }

            if (!$lopHocPhan) {
                // Nếu không có lớp học phần, tạo một lớp học phần giả lập
                if (!$hocKy) {
                    // Tạo học kỳ nếu chưa có
                    $hocKy = $this->taoHocKyTheoHocKyGoiY($ctk->hoc_ky_goi_y, $sinhVienTest);
                    if (!$hocKy) {
                        continue;
                    }
                }

                $lopHocPhan = LopHocPhan::create([
                    'ma_lop_hp' => 'LHP' . $ctk->mon_hoc_id . '-' . $hocKy->id,
                    'ten_lop_hp' => $ctk->monHoc->ten_mon . ' - ' . $hocKy->ten_hoc_ky,
                    'mon_hoc_id' => $ctk->mon_hoc_id,
                    'hoc_ky_id' => $hocKy->id,
                    'nhom_lop' => 1,
                    'suc_chua' => 50,
                    'so_luong_dang_ky' => 0,
                    'so_luong_toi_thieu' => 10,
                    'hinh_thuc' => 'offline',
                    'trang_thai_lop' => 'ket_thuc',
                    'ngay_bat_dau' => now()->subMonths(6),
                    'ngay_ket_thuc' => now()->subMonths(1),
                ]);
            }

            // Kiểm tra xem đã có lop_hoc_phan_sinh_vien chưa
            $lopHocPhanSV = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('sinh_vien_id', $sinhVienTest->id)
                ->first();

            if (!$lopHocPhanSV) {
                // Tạo lop_hoc_phan_sinh_vien
                $lopHocPhanSV = LopHocPhanSinhVien::create([
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'sinh_vien_id' => $sinhVienTest->id,
                    'ngay_dang_ky' => now()->subMonths(6),
                    'ngay_xep_lop' => now()->subMonths(6),
                    'phuong_thuc_xep' => 'tu_dong',
                    'trang_thai' => 'da_hoan_thanh',
                ]);
            }

            // Kiểm tra xem đã có kết quả học tập chưa
            $ketQuaHocTap = KetQuaHocTap::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSV->id)->first();

            if (!$ketQuaHocTap) {
                // Tạo điểm ngẫu nhiên (từ 4.0 đến 10.0)
                // Môn bắt buộc: 70% đạt (>= 4.0), 30% chưa đạt
                // Môn tự chọn: 80% đạt
                $isBatBuoc = $ctk->bat_buoc;
                $diemHe10 = $isBatBuoc 
                    ? (rand(1, 10) <= 7 ? rand(40, 100) / 10 : rand(20, 39) / 10)
                    : (rand(1, 10) <= 8 ? rand(40, 100) / 10 : rand(20, 39) / 10);

                $diemChu = KetQuaHocTap::tinhDiemChu($diemHe10);
                $diemHe4 = KetQuaHocTap::tinhDiemHe4($diemHe10);
                $quaMon = $diemHe10 >= 4.0;

                KetQuaHocTap::create([
                    'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                    'diem_he_10' => $diemHe10,
                    'diem_he_4' => $diemHe4,
                    'diem_chu' => $diemChu,
                    'qua_mon' => $quaMon,
                    'ghi_chu' => $quaMon ? 'Đạt' : 'Không đạt',
                ]);

                $count++;
            }
        }

        $this->command->info("  ✓ Đã tạo/cập nhật {$count} kết quả học tập cho sinh viên test");
    }

    /**
     * Tạo kết quả học tập cho các sinh viên khác
     */
    private function taoKetQuaHocTapChoSinhVienKhac(): void
    {
        // Lấy các sinh viên có chuyên ngành và đã đăng ký lớp học phần
        $sinhViens = SinhVien::whereNotNull('chuyen_nganh_id')
            ->whereHas('lopHocPhanSinhViens', function ($query) {
                $query->where('trang_thai', 'da_hoan_thanh');
            })
            ->with(['chuyenNganh'])
            ->limit(50) // Giới hạn 50 sinh viên để không quá lâu
            ->get();

        if ($sinhViens->isEmpty()) {
            $this->command->warn('Không tìm thấy sinh viên có chuyên ngành và đã đăng ký lớp học phần.');
            return;
        }

        $count = 0;
        foreach ($sinhViens as $sinhVien) {
            // Lấy các lớp học phần sinh viên đã hoàn thành nhưng chưa có kết quả
            $lopHocPhanSVs = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                ->where('trang_thai', 'da_hoan_thanh')
                ->whereDoesntHave('ketQuaHocTap')
                ->with(['lopHocPhan.monHoc'])
                ->limit(5) // Tối đa 5 môn mỗi sinh viên
                ->get();

            foreach ($lopHocPhanSVs as $lopHocPhanSV) {
                // Tạo điểm ngẫu nhiên
                $diemHe10 = rand(40, 100) / 10; // 4.0 - 10.0
                $diemChu = KetQuaHocTap::tinhDiemChu($diemHe10);
                $diemHe4 = KetQuaHocTap::tinhDiemHe4($diemHe10);
                $quaMon = $diemHe10 >= 4.0;

                KetQuaHocTap::create([
                    'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSV->id,
                    'diem_he_10' => $diemHe10,
                    'diem_he_4' => $diemHe4,
                    'diem_chu' => $diemChu,
                    'qua_mon' => $quaMon,
                    'ghi_chu' => $quaMon ? 'Đạt' : 'Không đạt',
                ]);

                $count++;
            }
        }

        $this->command->info("  ✓ Đã tạo {$count} kết quả học tập cho các sinh viên khác");
    }

    /**
     * Hiển thị tóm tắt
     */
    private function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->info('📊 TÓM TẮT DỮ LIỆU TEST:');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Sinh viên có chuyên ngành
        $svCoChuyenNganh = SinhVien::whereNotNull('chuyen_nganh_id')->count();
        $this->command->line("  Sinh viên có chuyên ngành: {$svCoChuyenNganh}");

        // Sinh viên test
        $svTest = SinhVien::where('email', 'sinhvien@smis.edu.vn')
            ->orWhere('ma_sinh_vien', 'SV2025001')
            ->with(['chuyenNganh'])
            ->first();

        if ($svTest) {
            $this->command->line("  Sinh viên test: {$svTest->ma_sinh_vien} - {$svTest->ho_ten}");
            if ($svTest->chuyenNganh) {
                $this->command->line("    Chuyên ngành: {$svTest->chuyenNganh->ten_chuyen_nganh}");
                
                // Đếm số môn trong CTĐT
                $soMonCTDT = ChuongTrinhKhung::where('chuyen_nganh_id', $svTest->chuyen_nganh_id)->count();
                $this->command->line("    Số môn trong CTĐT: {$soMonCTDT}");

                // Đếm số kết quả học tập
                $soKetQua = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($query) use ($svTest) {
                    $query->where('sinh_vien_id', $svTest->id);
                })->count();
                $this->command->line("    Số môn đã có kết quả: {$soKetQua}");
            }
        }

        // Tổng số kết quả học tập
        $tongKetQua = KetQuaHocTap::count();
        $this->command->line("  Tổng số kết quả học tập: {$tongKetQua}");

        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();

        $this->command->info('💡 HƯỚNG DẪN TEST:');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('  1. Đăng nhập với tài khoản: sinhvien@smis.edu.vn / password');
        $this->command->line('  2. Vào menu "Chương trình ĐT" > "CTĐT của lớp"');
        $this->command->line('  3. Kiểm tra:');
        $this->command->line('     - Hiển thị CTĐT theo học kỳ');
        $this->command->line('     - Thống kê tiến độ học tập');
        $this->command->line('     - Trạng thái môn học (đã đạt/chưa đạt/chưa học)');
        $this->command->line('  4. Vào "Điều kiện tốt nghiệp" để kiểm tra:');
        $this->command->line('     - Tín chỉ tích lũy');
        $this->command->line('     - Môn học bắt buộc');
        $this->command->line('     - Điểm trung bình tích lũy');
        $this->command->line('     - Danh sách môn nợ');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * Tìm học kỳ theo học kỳ gợi ý (1-8)
     * Học kỳ gợi ý 1-8 tương ứng với HK1, HK2 của các năm học
     */
    private function timHocKyTheoHocKyGoiY(int $hocKyGoiY, SinhVien $sinhVien): ?HocKy
    {
        // Tính năm học dựa trên khóa học của sinh viên
        $khoaHoc = $sinhVien->khoaHoc;
        if (!$khoaHoc) {
            // Load relationship nếu chưa có
            $sinhVien->load('khoaHoc');
            $khoaHoc = $sinhVien->khoaHoc;
            if (!$khoaHoc) {
                return null;
            }
        }

        // Học kỳ gợi ý 1-8: 
        // 1-2: Năm 1, 3-4: Năm 2, 5-6: Năm 3, 7-8: Năm 4
        $namHoc = (int)(($hocKyGoiY - 1) / 2) + 1; // Năm học (1-4)
        $hocKyTrongNam = (($hocKyGoiY - 1) % 2) + 1; // HK1 hoặc HK2 trong năm

        // Tính năm học (VD: 2025-2026)
        $namBatDau = $khoaHoc->nam_bat_dau;
        $namHocString = ($namBatDau + $namHoc - 1) . '-' . ($namBatDau + $namHoc);
        $tenHocKy = 'Học kỳ ' . $hocKyTrongNam;

        // Tìm học kỳ
        $hocKy = HocKy::where('ten_hoc_ky', $tenHocKy)
            ->where('nam_hoc', $namHocString)
            ->first();

        return $hocKy;
    }

    /**
     * Tạo học kỳ theo học kỳ gợi ý nếu chưa có
     */
    private function taoHocKyTheoHocKyGoiY(int $hocKyGoiY, SinhVien $sinhVien): ?HocKy
    {
        $khoaHoc = $sinhVien->khoaHoc;
        if (!$khoaHoc) {
            // Load relationship nếu chưa có
            $sinhVien->load('khoaHoc');
            $khoaHoc = $sinhVien->khoaHoc;
            if (!$khoaHoc) {
                return null;
            }
        }

        // Tính năm học
        $namHoc = (int)(($hocKyGoiY - 1) / 2) + 1;
        $hocKyTrongNam = (($hocKyGoiY - 1) % 2) + 1;

        $namBatDau = $khoaHoc->nam_bat_dau;
        $namHocString = ($namBatDau + $namHoc - 1) . '-' . ($namBatDau + $namHoc);
        $tenHocKy = 'Học kỳ ' . $hocKyTrongNam;

        // Tính ngày bắt đầu và kết thúc
        $namHocBatDau = $namBatDau + $namHoc - 1;
        if ($hocKyTrongNam == 1) {
            // HK1: Tháng 9 - Tháng 1
            $ngayBatDau = \Carbon\Carbon::create($namHocBatDau, 9, 1);
            $ngayKetThuc = \Carbon\Carbon::create($namHocBatDau + 1, 1, 15);
        } else {
            // HK2: Tháng 2 - Tháng 6
            $ngayBatDau = \Carbon\Carbon::create($namHocBatDau + 1, 2, 1);
            $ngayKetThuc = \Carbon\Carbon::create($namHocBatDau + 1, 6, 15);
        }

        // Tạo học kỳ
        $hocKy = HocKy::updateOrCreate(
            [
                'ten_hoc_ky' => $tenHocKy,
                'nam_hoc' => $namHocString,
            ],
            [
                'ngay_bat_dau' => $ngayBatDau,
                'ngay_ket_thuc' => $ngayKetThuc,
                'ngay_bat_dau_dang_ky' => $ngayBatDau->copy()->subMonth(),
                'ngay_ket_thuc_dang_ky' => $ngayBatDau->copy()->addDays(30),
                'la_hoc_ky_hien_tai' => false,
                'dang_mo_dang_ky' => false,
                'mo_ta' => "{$tenHocKy} năm học {$namHocString}",
            ]
        );

        return $hocKy;
    }
}

