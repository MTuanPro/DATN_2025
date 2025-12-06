<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;
use App\Models\PhanCongGiangDay;
use App\Models\CauHinhDauDiem;
use App\Models\CauHinhDauDiemMacDinh;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\DaoTao\ChuyenNganh;
use Carbon\Carbon;

class LopHocPhanSeeder extends Seeder
{
    /**
     * Tạo lớp học phần cho học kỳ 1 năm 2025-2026
     * Tạo lớp cho tất cả môn học mà sinh viên kỳ 1-8 cần học (theo chương trình khung)
     * Mỗi môn học có 2 lớp học phần để đủ cho sinh viên các ngành đăng ký
     * Phân công giảng viên phù hợp với môn học (từ bảng giang_vien_mon_hoc)
     * Không tạo lịch học
     */
    public function run(): void
    {
        // Tìm học kỳ hiện tại hoặc học kỳ gần nhất
        $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        
        // Nếu không có học kỳ hiện tại, lấy học kỳ gần nhất (theo ngày bắt đầu)
        if (!$hocKy) {
            $hocKy = HocKy::orderBy('ngay_bat_dau', 'desc')->first();
        }
        
        // Nếu vẫn không có, thử tìm học kỳ 1 năm 2025-2026 (fallback)
        if (!$hocKy) {
            $hocKy = HocKy::where('ten_hoc_ky', 'Học kỳ 1')
                ->where('nam_hoc', '2025-2026')
                ->first();
        }
        
        if (!$hocKy) {
            $this->command->error('Không tìm thấy học kỳ nào để tạo lớp học phần!');
            $this->command->info('💡 Vui lòng chạy HocKySeeder trước để tạo học kỳ.');
            return;
        }
        
        $this->command->info("📚 Tạo lớp học phần cho: {$hocKy->ten_hoc_ky} - {$hocKy->nam_hoc}");
        $this->command->info("🎓 Tạo lớp cho môn học mà sinh viên kỳ 1-8 cần học");

        // Xóa các lớp học phần cũ của học kỳ này (bao gồm cả soft deleted)
        $soLopCu = LopHocPhan::withTrashed()->where('hoc_ky_id', $hocKy->id)->count();
        if ($soLopCu > 0) {
            // Xóa phân công giảng viên trước
            $lopHocPhanIds = LopHocPhan::withTrashed()->where('hoc_ky_id', $hocKy->id)->pluck('id');
            PhanCongGiangDay::whereIn('lop_hoc_phan_id', $lopHocPhanIds)->delete();
            
            // Force delete lớp học phần (bao gồm cả soft deleted)
            LopHocPhan::withTrashed()->where('hoc_ky_id', $hocKy->id)->forceDelete();
            $this->command->info("🗑️  Đã xóa {$soLopCu} lớp học phần cũ");
        }

        // Lấy tất cả chuyên ngành
        $chuyenNganhs = ChuyenNganh::all();
        
        if ($chuyenNganhs->isEmpty()) {
            $this->command->error('Không có chuyên ngành nào trong hệ thống!');
            return;
        }

        // Tập hợp tất cả môn học mà sinh viên kỳ 1-8 cần học
        $monHocIds = collect();
        
        foreach ($chuyenNganhs as $chuyenNganh) {
            // Lấy chương trình khung của chuyên ngành (kỳ 1-8)
            $chuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $chuyenNganh->id)
                ->whereBetween('hoc_ky_goi_y', [1, 8]) // Lấy môn học của kỳ 1-8
                ->pluck('mon_hoc_id');
            
            $monHocIds = $monHocIds->merge($chuongTrinhKhung);
        }
        
        // Loại bỏ trùng lặp
        $monHocIds = $monHocIds->unique();
        
        // Lấy thông tin môn học
        $monHocs = MonHoc::whereIn('id', $monHocIds)->get();
        
        if ($monHocs->isEmpty()) {
            $this->command->error('Không tìm thấy môn học nào cho sinh viên kỳ 1-8!');
            return;
        }
        
        $this->command->info("📖 Tìm thấy {$monHocs->count()} môn học cần tạo lớp (cho sinh viên kỳ 1-8)");

        $count = 0;
        $countKhongCoGiangVien = 0;

        foreach ($monHocs as $monHoc) {
            // Lấy danh sách giảng viên có thể dạy môn này từ bảng giang_vien_mon_hoc
            $giangVienIds = DB::table('giang_vien_mon_hoc')
                ->where('mon_hoc_id', $monHoc->id)
                ->pluck('giang_vien_id')
                ->toArray();

            if (empty($giangVienIds)) {
                $countKhongCoGiangVien++;
                $this->command->warn("⚠️  Môn {$monHoc->ma_mon} - {$monHoc->ten_mon} không có giảng viên phù hợp");
                continue;
            }

            // Tạo 2 lớp cho mỗi môn học để đủ cho sinh viên các ngành đăng ký
            $soLop = 2;
            
            // Đảm bảo có đủ giảng viên cho 2 lớp
            if (count($giangVienIds) < $soLop) {
                $this->command->warn("⚠️  Môn {$monHoc->ma_mon} chỉ có " . count($giangVienIds) . " giảng viên, không đủ cho {$soLop} lớp");
            }
            
            // Shuffle để phân bổ giảng viên ngẫu nhiên
            shuffle($giangVienIds);
            
            for ($i = 1; $i <= $soLop; $i++) {
                // Chọn giảng viên khác nhau cho mỗi lớp (nếu có đủ)
                if (count($giangVienIds) >= $i) {
                    $giangVienId = $giangVienIds[$i - 1];
                } else {
                    // Nếu không đủ giảng viên, dùng lại giảng viên đầu tiên
                    $giangVienId = $giangVienIds[0];
                }
                
                // Tạo mã lớp theo format: [MaMonHoc].[SoLop]
                $maLopHp = $monHoc->ma_mon . '.' . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                // Kiểm tra xem lớp đã tồn tại chưa (kiểm tra unique constraint: mon_hoc_id + hoc_ky_id + nhom_lop)
                if (LopHocPhan::withTrashed()
                    ->where('mon_hoc_id', $monHoc->id)
                    ->where('hoc_ky_id', $hocKy->id)
                    ->where('nhom_lop', $i)
                    ->exists()) {
                    continue;
                }

                // Tính ngày bắt đầu và kết thúc dựa trên học kỳ
                $ngayBatDau = Carbon::parse($hocKy->ngay_bat_dau);
                $ngayKetThuc = Carbon::parse($hocKy->ngay_ket_thuc);

                // Tạo lớp học phần
                $lopHocPhan = LopHocPhan::create([
                    'ma_lop_hp' => $maLopHp,
                    'ten_lop_hp' => $monHoc->ten_mon . ' - Nhóm ' . $i,
                    'mon_hoc_id' => $monHoc->id,
                    'hoc_ky_id' => $hocKy->id,
                    'nhom_lop' => $i,
                    'suc_chua' => 50, // Sức chứa cố định 50 sinh viên/lớp (2 lớp = 100 SV/môn)
                    'so_luong_dang_ky' => 0,
                    'so_luong_toi_thieu' => 10,
                    'hinh_thuc' => $monHoc->hinh_thuc_day ?? 'offline',
                    'link_online' => null,
                    'ngay_bat_dau' => $ngayBatDau,
                    'ngay_ket_thuc' => $ngayKetThuc,
                    'trang_thai_lop' => 'mo_dang_ky',
                    'ghi_chu' => null,
                ]);

                // Phân công giảng viên chính (chỉ giảng viên có thể dạy môn này)
                PhanCongGiangDay::create([
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'giang_vien_id' => $giangVienId,
                    'vai_tro' => 'giang_vien_chinh',
                    'ngay_phan_cong' => Carbon::now(),
                ]);

                // Tự động copy cấu hình đầu điểm từ môn học sang lớp học phần
                $this->copyCauHinhDauDiemTuMonHoc($lopHocPhan->id, $monHoc->id);

                $count++;
            }
        }

        $this->command->info("✅ Đã tạo {$count} lớp học phần cho học kỳ: {$hocKy->ten_hoc_ky} - {$hocKy->nam_hoc}");
        $this->command->info("📊 Tổng số môn học: {$monHocs->count()} môn");
        $this->command->info("📊 Trung bình: " . round($count / $monHocs->count(), 2) . " lớp/môn");
        
        if ($countKhongCoGiangVien > 0) {
            $this->command->warn("⚠️  Có {$countKhongCoGiangVien} môn học không có giảng viên phù hợp (chưa được phân công trong bảng giang_vien_mon_hoc)");
        }
        
        $this->command->info("📝 Lưu ý: Seeder này không tạo lịch học. Vui lòng tạo lịch học thủ công sau khi tạo lớp học phần.");
    }

    /**
     * Copy cấu hình đầu điểm từ môn học sang lớp học phần
     */
    private function copyCauHinhDauDiemTuMonHoc($lopHocPhanId, $monHocId)
    {
        // Lấy cấu hình mặc định của môn học
        $cauHinhMacDinhs = CauHinhDauDiemMacDinh::where('mon_hoc_id', $monHocId)->get();

        if ($cauHinhMacDinhs->isEmpty()) {
            // Nếu môn học chưa có cấu hình mặc định, tạo cấu hình mặc định
            $cauHinhMacDinh = [
                ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
                ['ten_dau_diem' => 'Giữa kỳ', 'ty_le' => 30, 'so_cot' => 1],
                ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 60, 'so_cot' => 1],
            ];

            foreach ($cauHinhMacDinh as $cauHinh) {
                CauHinhDauDiem::create([
                    'lop_hoc_phan_id' => $lopHocPhanId,
                    'ten_dau_diem' => $cauHinh['ten_dau_diem'],
                    'ty_le' => $cauHinh['ty_le'],
                    'so_cot' => $cauHinh['so_cot'],
                ]);
            }
        } else {
            // Copy từ cấu hình mặc định
            foreach ($cauHinhMacDinhs as $cauHinhMacDinh) {
                CauHinhDauDiem::create([
                    'lop_hoc_phan_id' => $lopHocPhanId,
                    'ten_dau_diem' => $cauHinhMacDinh->ten_dau_diem,
                    'ty_le' => $cauHinhMacDinh->ty_le,
                    'so_cot' => $cauHinhMacDinh->so_cot,
                ]);
            }
        }
    }
}
