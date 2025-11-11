<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LopHocPhan;
use App\Models\SinhVien;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\HocKy;
use Carbon\Carbon;

class BaoCaoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Bắt đầu tạo dữ liệu mẫu cho báo cáo...\n";

        // 1. Tạo dữ liệu điểm danh
        $this->createDiemDanhData();
        
        // 2. Tạo dữ liệu kết quả học tập
        $this->createKetQuaHocTapData();
        
        // 3. Tạo dữ liệu học phí
        $this->createHocPhiData();
        
        // 4. Tạo dữ liệu cảnh báo học vụ
        $this->createCanhBaoData();

        echo "Hoàn thành tạo dữ liệu mẫu!\n";
    }

    /**
     * Tạo dữ liệu điểm danh
     */
    private function createDiemDanhData()
    {
        echo "Tạo dữ liệu điểm danh...\n";

        // Xóa dữ liệu cũ
        DB::table('diem_danh')->truncate();

        $diemDanhData = [];
        $count = 0;

        // Lấy danh sách lớp học phần sinh viên (với thông tin lớp)
        $lopHocPhanSinhViens = DB::table('lop_hoc_phan_sinh_vien')
            ->select('id', 'lop_hoc_phan_id')
            ->limit(500)
            ->get();

        foreach ($lopHocPhanSinhViens as $lhpsv) {
            // Lấy lịch học chi tiết của ĐÚNG lớp học phần
            $lichHocChiTiets = DB::table('lich_hoc_chi_tiet')
                ->where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
                ->where('ngay_hoc', '<=', now())
                ->get();

            if ($lichHocChiTiets->isEmpty()) {
                continue;
            }

            // Chọn random một số buổi học (tối đa 10 buổi)
            $soBuoiDiemDanh = min(10, $lichHocChiTiets->count());
            $selectedBuoiHoc = $lichHocChiTiets->random($soBuoiDiemDanh);

            foreach ($selectedBuoiHoc as $lichHoc) {
                // Random trạng thái điểm danh: co_mat, vang, di_tre, nghi_phep
                $rand = rand(1, 100);
                if ($rand <= 80) {
                    $trangThai = 'co_mat';
                    $ghiChu = null;
                } elseif ($rand <= 90) {
                    $trangThai = 'nghi_phep';
                    $ghiChu = 'Có đơn xin phép';
                } elseif ($rand <= 95) {
                    $trangThai = 'di_tre';
                    $ghiChu = 'Đến muộn ' . rand(5, 30) . ' phút';
                } else {
                    $trangThai = 'vang';
                    $ghiChu = null;
                }
                
                $diemDanhData[] = [
                    'lop_hoc_phan_sinh_vien_id' => $lhpsv->id,
                    'lich_hoc_chi_tiet_id' => $lichHoc->id,
                    'trang_thai' => $trangThai,
                    'ghi_chu' => $ghiChu,
                    'thoi_gian_diem_danh' => $lichHoc->ngay_hoc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $count++;
                if ($count >= 1000) break 2; // Tạo tối đa 1000 bản ghi
            }
        }

        if (!empty($diemDanhData)) {
            // Insert theo batch
            foreach (array_chunk($diemDanhData, 100) as $chunk) {
                DB::table('diem_danh')->insert($chunk);
            }
            echo "Đã tạo " . count($diemDanhData) . " bản ghi điểm danh\n";
        }
    }

    /**
     * Tạo dữ liệu kết quả học tập
     */
    private function createKetQuaHocTapData()
    {
        echo "Tạo dữ liệu kết quả học tập...\n";

        // Lấy danh sách lớp học phần sinh viên
        $lopHocPhanSinhViens = DB::table('lop_hoc_phan_sinh_vien')
            ->select('id')
            ->limit(500)
            ->get();

        $ketQuaData = [];

        foreach ($lopHocPhanSinhViens as $lhpsv) {
            // Random điểm hệ 10
            $diemHe10 = round(rand(30, 100) / 10, 1); // 3.0 - 10.0
            
            // Quy đổi điểm hệ 4
            $diemHe4 = $this->convertToGPA($diemHe10);
            
            // Điểm chữ
            $diemChu = $this->getDiemChu($diemHe10);
            
            // Qua môn nếu >= 4.0
            $quaMon = $diemHe10 >= 4.0;

            $ketQuaData[] = [
                'lop_hoc_phan_sinh_vien_id' => $lhpsv->id,
                'diem_he_10' => $diemHe10,
                'diem_he_4' => round($diemHe4, 2),
                'diem_chu' => $diemChu,
                'qua_mon' => $quaMon,
                'ghi_chu' => !$quaMon ? 'Chưa đạt' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($ketQuaData)) {
            // Xóa dữ liệu cũ
            DB::table('ket_qua_hoc_tap')->truncate();
            
            // Insert theo batch
            foreach (array_chunk($ketQuaData, 100) as $chunk) {
                DB::table('ket_qua_hoc_tap')->insert($chunk);
            }
            echo "Đã tạo " . count($ketQuaData) . " bản ghi kết quả học tập\n";
        }
    }

    /**
     * Tạo dữ liệu học phí
     */
    private function createHocPhiData()
    {
        echo "Tạo dữ liệu học phí...\n";

        $hocKys = HocKy::all();
        $sinhViens = SinhVien::limit(200)->get();

        $hocPhiData = [];

        // Xóa dữ liệu cũ với foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('chi_tiet_hoc_phi_mon')->truncate();
        DB::table('lich_su_dong_hoc_phi')->truncate();
        DB::table('hoc_phi_hoc_ky')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($sinhViens as $sv) {
            foreach ($hocKys->random(min(2, $hocKys->count())) as $hocKy) {
                // Tính học phí
                $soTinChi = rand(12, 20);
                $hocPhiMonHoc = $soTinChi * 500000; // 500k/tín chỉ
                $phiDichVu = 500000; // Phí dịch vụ cố định
                $tongSoTien = $hocPhiMonHoc + $phiDichVu;
                $daThanhToan = rand(0, 100) <= 70 ? $tongSoTien : rand(0, $tongSoTien);
                $conLai = $tongSoTien - $daThanhToan;

                $trangThai = $daThanhToan == 0 ? 'chua_nop' : 
                             ($conLai == 0 ? 'da_nop_du' : 'da_nop_mot_phan');

                $hocPhiData[] = [
                    'sinh_vien_id' => $sv->id,
                    'hoc_ky_id' => $hocKy->id,
                    'tong_tin_chi_dang_ky' => $soTinChi,
                    'tong_hoc_phi_mon_hoc' => $hocPhiMonHoc,
                    'phi_dich_vu' => $phiDichVu,
                    'tong_so_tien' => $tongSoTien,
                    'so_tien_da_dong' => $daThanhToan,
                    'so_tien_con_lai' => $conLai,
                    'han_dong' => Carbon::parse($hocKy->ngay_bat_dau)->addMonths(2),
                    'ngay_dong_lan_cuoi' => $daThanhToan > 0 ? Carbon::parse($hocKy->ngay_bat_dau)->addDays(rand(1, 60)) : null,
                    'trang_thai' => $trangThai,
                    'ghi_chu' => $conLai > 0 ? "Còn nợ " . number_format($conLai) . " VNĐ" : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($hocPhiData)) {
            foreach (array_chunk($hocPhiData, 100) as $chunk) {
                DB::table('hoc_phi_hoc_ky')->insert($chunk);
            }
            echo "Đã tạo " . count($hocPhiData) . " bản ghi học phí\n";
        }
    }

    /**
     * Tạo dữ liệu cảnh báo học vụ
     */
    private function createCanhBaoData()
    {
        echo "Tạo dữ liệu cảnh báo học vụ...\n";

        $sinhViens = SinhVien::limit(100)->get();
        $hocKys = HocKy::all();

        $canhBaoData = [];

        foreach ($sinhViens->random(min(30, $sinhViens->count())) as $sv) {
            $loaiCanhBao = ['diem_thap', 'vang_nhieu', 'no_hoc_phi', 'hoc_ky_lien_tiep'][rand(0, 3)];
            $mucDo = ['canh_cao', 'dinh_chi', 'buoc_thoi_hoc'][rand(0, 2)];

            $lyDo = match($loaiCanhBao) {
                'diem_thap' => 'Điểm trung bình tích lũy dưới 1.0',
                'vang_nhieu' => 'Vắng mặt quá 20% số buổi học',
                'no_hoc_phi' => 'Nợ học phí quá hạn',
                'hoc_ky_lien_tiep' => 'Học kém nhiều học kỳ liên tiếp',
            };

            $daXuLy = rand(0, 100) <= 30;

            $canhBaoData[] = [
                'sinh_vien_id' => $sv->id,
                'hoc_ky_id' => $hocKys->random()->id,
                'loai_canh_bao' => $loaiCanhBao,
                'muc_do' => $mucDo,
                'ly_do' => $lyDo,
                'ngay_canh_bao' => now()->subDays(rand(1, 90)),
                'nguoi_canh_bao_id' => null,
                'da_xu_ly' => $daXuLy,
                'ngay_xu_ly' => $daXuLy ? now()->subDays(rand(1, 30)) : null,
                'ket_qua_xu_ly' => $daXuLy ? 'Đã nhắc nhở và cam kết học tập tốt hơn' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($canhBaoData)) {
            DB::table('canh_bao_hoc_vu')->truncate();
            DB::table('canh_bao_hoc_vu')->insert($canhBaoData);
            echo "Đã tạo " . count($canhBaoData) . " bản ghi cảnh báo học vụ\n";
        }
    }

    /**
     * Tạo dữ liệu lịch thi
     */
    private function createLichThiData()
    {
        echo "Tạo dữ liệu lịch thi...\n";

        $lopHocPhans = LopHocPhan::with('monHoc')->limit(50)->get();
        $phongHocs = DB::table('phong_hoc')->get();

        $lichThiData = [];
        $lichThiSVData = [];

        foreach ($lopHocPhans as $lhp) {
            $phongHoc = $phongHocs->random();
            $ngayThi = now()->addDays(rand(10, 60));

            $lichThi = [
                'lop_hoc_phan_id' => $lhp->id,
                'phong_hoc_id' => $phongHoc->id,
                'ngay_thi' => $ngayThi,
                'gio_bat_dau' => '07:00:00',
                'gio_ket_thuc' => '09:00:00',
                'hinh_thuc' => ['tu_luan', 'trac_nghiem', 'thuc_hanh'][rand(0, 2)],
                'ghi_chu' => 'Sinh viên mang theo CMND/CCCD',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $lichThiId = DB::table('lich_thi')->insertGetId($lichThi);
            $lichThiData[] = $lichThi;

            // Tạo lịch thi cho sinh viên
            $sinhViensInClass = DB::table('lop_hoc_phan_sinh_vien')
                ->where('lop_hoc_phan_id', $lhp->id)
                ->limit(30)
                ->get();

            foreach ($sinhViensInClass as $lhpsv) {
                $lichThiSVData[] = [
                    'lich_thi_id' => $lichThiId,
                    'sinh_vien_id' => $lhpsv->sinh_vien_id,
                    'trang_thai' => 'chua_thi',
                    'ghi_chu' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($lichThiSVData)) {
            DB::table('lich_thi_sinh_vien')->truncate();
            foreach (array_chunk($lichThiSVData, 100) as $chunk) {
                DB::table('lich_thi_sinh_vien')->insert($chunk);
            }
            echo "Đã tạo " . count($lichThiData) . " lịch thi và " . count($lichThiSVData) . " bản ghi lịch thi sinh viên\n";
        }
    }

    /**
     * Quy đổi điểm hệ 10 sang hệ 4
     */
    private function convertToGPA($diem10)
    {
        if ($diem10 >= 8.5) return 4.0;
        if ($diem10 >= 7.0) return 3.0 + (($diem10 - 7.0) / 1.5);
        if ($diem10 >= 5.5) return 2.0 + (($diem10 - 5.5) / 1.5);
        if ($diem10 >= 4.0) return 1.0 + (($diem10 - 4.0) / 1.5);
        return 0.0;
    }

    /**
     * Lấy điểm chữ
     */
    private function getDiemChu($diem10)
    {
        if ($diem10 >= 8.5) return 'A';
        if ($diem10 >= 8.0) return 'B+';
        if ($diem10 >= 7.0) return 'B';
        if ($diem10 >= 6.5) return 'C+';
        if ($diem10 >= 5.5) return 'C';
        if ($diem10 >= 5.0) return 'D+';
        if ($diem10 >= 4.0) return 'D';
        return 'F';
    }
}
