<?php

namespace Database\Seeders;

use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DaoTao\KhoaHoc;
use Illuminate\Database\Seeder;

class LopHanhChinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các khóa học và ngành
        $khoaHocs = KhoaHoc::orderBy('nam_bat_dau', 'desc')->get();
        $chuyenNganhs = ChuyenNganh::all();

        if ($khoaHocs->isEmpty() || $chuyenNganhs->isEmpty()) {
            $this->command->warn('Cần có dữ liệu KhoaHoc và ChuyenNganh trước!');
            return;
        }

        $lopHanhChinhs = [];

        // Tạo lớp cho mỗi chuyên ngành trong 3 khóa học gần nhất
        foreach ($chuyenNganhs->take(5) as $chuyenNganh) { // Lấy 5 chuyên ngành đầu
            foreach ($khoaHocs->take(3) as $khoaHoc) { // 3 khóa gần nhất
                // Tạo 2-3 lớp cho mỗi chuyên ngành mỗi khóa
                $soLop = rand(2, 3);

                for ($i = 1; $i <= $soLop; $i++) {
                    $maLop = $this->generateMaLop($chuyenNganh, $khoaHoc, $i);

                    $lopHanhChinhs[] = [
                        'ma_lop' => $maLop,
                        'ten_lop' => "Lớp {$maLop}",
                        'nganh_id' => $chuyenNganh->nganh_id,
                        'khoa_hoc_id' => $khoaHoc->id,
                        'si_so' => rand(35, 45),
                        'giang_vien_chu_nhiem_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert từng batch để tránh duplicate
        foreach ($lopHanhChinhs as $lop) {
            LopHanhChinh::updateOrInsert(
                ['ma_lop' => $lop['ma_lop']],
                $lop
            );
        }

        $this->command->info('✓ Đã tạo ' . count($lopHanhChinhs) . ' lớp hành chính');
    }
    /**
     * Generate mã lớp theo format chuẩn
     * VD: CNTT01-K2021, KTPM02-K2021
     */
    private function generateMaLop(ChuyenNganh $chuyenNganh, KhoaHoc $khoaHoc, int $thuTu): string
    {
        // Lấy viết tắt chuyên ngành từ mã (VD: CNTT, KTPM, KHMT)
        $vietTat = $chuyenNganh->ma_chuyen_nganh;

        // Số thứ tự lớp với 2 chữ số
        $soThuTu = str_pad($thuTu, 2, '0', STR_PAD_LEFT);

        // Năm khóa
        $namKhoa = 'K' . $khoaHoc->nam_bat_dau;

        return "{$vietTat}{$soThuTu}-{$namKhoa}";
    }
}
