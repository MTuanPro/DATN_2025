<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiemDanh;
use App\Models\LichHocChiTiet;
use App\Models\LopHocPhanSinhVien;
use Carbon\Carbon;

class DiemDanhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các buổi học đã qua (để có dữ liệu điểm danh)
        $buoiHocDaQua = LichHocChiTiet::where('ngay_hoc', '<=', Carbon::now())
            ->orderBy('ngay_hoc', 'desc')
            ->take(30) // Lấy 30 buổi gần nhất
            ->get();

        $trangThais = ['co_mat', 'vang', 'di_tre', 'nghi_phep'];
        $count = 0;

        foreach ($buoiHocDaQua as $buoiHoc) {
            // Lấy danh sách sinh viên trong lớp
            $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
                ->where('trang_thai', 'dang_hoc')
                ->get();

            foreach ($sinhViens as $sv) {
                // Random trạng thái (85% có mặt, 10% vắng, 3% đi trễ, 2% nghỉ phép)
                $rand = rand(1, 100);
                if ($rand <= 85) {
                    $trangThai = 'co_mat';
                } elseif ($rand <= 95) {
                    $trangThai = 'vang';
                } elseif ($rand <= 98) {
                    $trangThai = 'di_tre';
                } else {
                    $trangThai = 'nghi_phep';
                }

                DiemDanh::create([
                    'lop_hoc_phan_sinh_vien_id' => $sv->id,
                    'lich_hoc_chi_tiet_id' => $buoiHoc->id,
                    'trang_thai' => $trangThai,
                    'thoi_gian_diem_danh' => Carbon::parse($buoiHoc->ngay_hoc)
                        ->setTimeFromTimeString($buoiHoc->gio_bat_dau)
                        ->addMinutes(rand(5, 15)),
                    'ghi_chu' => $trangThai !== 'co_mat' && rand(1, 3) === 1 
                        ? $this->getGhiChu($trangThai) 
                        : null,
                ]);

                $count++;
            }
        }

        $this->command->info("✓ Đã tạo {$count} bản ghi điểm danh cho {$buoiHocDaQua->count()} buổi học");
    }

    private function getGhiChu($trangThai)
    {
        $ghiChus = [
            'vang' => [
                'Vắng không phép',
                'Không rõ lý do',
                'Liên hệ không được',
            ],
            'di_tre' => [
                'Đến muộn 10 phút',
                'Đến muộn 15 phút',
                'Kẹt xe',
            ],
            'nghi_phep' => [
                'Ốm, có giấy bác sĩ',
                'Có việc gia đình',
                'Đã xin phép trước',
            ],
        ];

        return $ghiChus[$trangThai][array_rand($ghiChus[$trangThai])] ?? null;
    }
}
