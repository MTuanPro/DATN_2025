<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\KhoaHoc;
use Carbon\Carbon;

class HocKySeeder extends Seeder
{
    public function run(): void
    {
        {10153DB7-673F-469A-A68C-2142D1DC9800}.png        // Lấy tất cả sinh viên đang học (trạng thái "Đang học" hoặc "Bảo lưu")
        $trangThaiDangHoc = DB::table('trang_thai_hoc_tap')
            ->whereIn('ten_trang_thai', ['Đang học', 'Bảo lưu'])
            ->pluck('id');

        if ($trangThaiDangHoc->isEmpty()) {
            echo "⚠️  Không tìm thấy trạng thái học tập 'Đang học' hoặc 'Bảo lưu'\n";
            return;
        }

        // Lấy tất cả sinh viên đang học
        $sinhViens = SinhVien::whereIn('trang_thai_hoc_tap_id', $trangThaiDangHoc)
            ->whereNotNull('khoa_hoc_id')
            ->with('khoaHoc')
            ->get();

        if ($sinhViens->isEmpty()) {
            echo "⚠️  Không có sinh viên nào đang học\n";
            return;
        }

        // Lấy tất cả khóa học duy nhất từ các sinh viên đang học
        $khoaHocIds = $sinhViens->pluck('khoa_hoc_id')->unique();
        $khoaHocs = KhoaHoc::whereIn('id', $khoaHocIds)->get();

        if ($khoaHocs->isEmpty()) {
            echo "⚠️  Không tìm thấy khóa học nào\n";
            return;
        }

        $hocKys = [];
        $namHienTai = Carbon::now()->year;
        $hocKyHienTai = null;

        // Tạo học kỳ cho từng khóa học
        foreach ($khoaHocs as $khoaHoc) {
            $namBatDau = $khoaHoc->nam_bat_dau;
            $namKetThuc = $khoaHoc->nam_ket_thuc;
            
            // Tạo học kỳ cho tất cả các năm từ năm bắt đầu đến năm kết thúc + 1 năm tương lai
            for ($nam = $namBatDau; $nam <= $namKetThuc + 1; $nam++) {
                // Học kỳ 1: Tháng 9 - Tháng 1 năm sau
                $namHocString = $nam . '-' . ($nam + 1);
                $tenHocKy = 'Học kỳ 1';
                
                $ngayBatDau = Carbon::create($nam, 9, 1);
                $ngayKetThuc = Carbon::create($nam + 1, 1, 15);
                $ngayBatDauDangKy = $ngayBatDau->copy()->subMonth();
                $ngayKetThucDangKy = $ngayBatDau->copy()->addDays(30);
                
                // Xác định học kỳ hiện tại (nếu chưa có)
                $laHocKyHienTai = false;
                if (!$hocKyHienTai) {
                    $now = Carbon::now();
                    if ($now->between($ngayBatDau, $ngayKetThuc)) {
                        $laHocKyHienTai = true;
                        $hocKyHienTai = true;
                    }
                }
                
                $hocKys[] = [
                    'ten_hoc_ky' => $tenHocKy,
                    'nam_hoc' => $namHocString,
                    'ngay_bat_dau' => $ngayBatDau,
                    'ngay_ket_thuc' => $ngayKetThuc,
                    'ngay_bat_dau_dang_ky' => $ngayBatDauDangKy,
                    'ngay_ket_thuc_dang_ky' => $ngayKetThucDangKy,
                    'la_hoc_ky_hien_tai' => $laHocKyHienTai,
                    'dang_mo_dang_ky' => $laHocKyHienTai && Carbon::now()->between($ngayBatDauDangKy, $ngayKetThucDangKy),
                    'mo_ta' => "{$tenHocKy} năm học {$namHocString}",
                ];

                // Học kỳ 2: Tháng 2 - Tháng 6
                $tenHocKy = 'Học kỳ 2';
                
                $ngayBatDau = Carbon::create($nam + 1, 2, 1);
                $ngayKetThuc = Carbon::create($nam + 1, 6, 15);
                $ngayBatDauDangKy = Carbon::create($nam + 1, 1, 20);
                $ngayKetThucDangKy = Carbon::create($nam + 1, 2, 10);
                
                // Xác định học kỳ hiện tại (nếu chưa có)
                $laHocKyHienTai = false;
                if (!$hocKyHienTai) {
                    $now = Carbon::now();
                    if ($now->between($ngayBatDau, $ngayKetThuc)) {
                        $laHocKyHienTai = true;
                        $hocKyHienTai = true;
                    }
                }
                
                $hocKys[] = [
                    'ten_hoc_ky' => $tenHocKy,
                    'nam_hoc' => $namHocString,
                    'ngay_bat_dau' => $ngayBatDau,
                    'ngay_ket_thuc' => $ngayKetThuc,
                    'ngay_bat_dau_dang_ky' => $ngayBatDauDangKy,
                    'ngay_ket_thuc_dang_ky' => $ngayKetThucDangKy,
                    'la_hoc_ky_hien_tai' => $laHocKyHienTai,
                    'dang_mo_dang_ky' => $laHocKyHienTai && Carbon::now()->between($ngayBatDauDangKy, $ngayKetThucDangKy),
                    'mo_ta' => "{$tenHocKy} năm học {$namHocString}",
                ];
            }
        }

        // Loại bỏ các học kỳ trùng lặp (cùng tên học kỳ và năm học)
        $hocKysMap = [];
        foreach ($hocKys as $hk) {
            $key = $hk['ten_hoc_ky'] . '|' . $hk['nam_hoc'];
            if (!isset($hocKysMap[$key])) {
                $hocKysMap[$key] = $hk;
            } else {
                // Nếu trùng, giữ lại học kỳ có la_hoc_ky_hien_tai = true
                if ($hk['la_hoc_ky_hien_tai']) {
                    $hocKysMap[$key] = $hk;
                }
            }
        }
        $hocKysUnique = array_values($hocKysMap);

        // Insert dữ liệu
        $count = 0;
        foreach ($hocKysUnique as $hk) {
            DB::table('hoc_ky')->updateOrInsert(
                [
                    'ten_hoc_ky' => $hk['ten_hoc_ky'],
                    'nam_hoc' => $hk['nam_hoc'],
                ],
                array_merge($hk, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
            $count++;
        }

        // Đảm bảo chỉ có 1 học kỳ hiện tại
        $hocKyHienTaiCount = DB::table('hoc_ky')->where('la_hoc_ky_hien_tai', true)->count();
        if ($hocKyHienTaiCount > 1) {
            // Nếu có nhiều học kỳ hiện tại, chỉ giữ lại học kỳ gần nhất
            $hocKyGanNhat = DB::table('hoc_ky')
                ->where('la_hoc_ky_hien_tai', true)
                ->orderBy('ngay_bat_dau', 'desc')
                ->first();
            
            if ($hocKyGanNhat) {
                DB::table('hoc_ky')
                    ->where('la_hoc_ky_hien_tai', true)
                    ->where('id', '!=', $hocKyGanNhat->id)
                    ->update(['la_hoc_ky_hien_tai' => false]);
            }
        }

        echo "✅ Đã tạo/cập nhật {$count} học kỳ\n";
        echo "   📊 Số sinh viên đang học: " . $sinhViens->count() . "\n";
        echo "   📚 Số khóa học: " . $khoaHocs->count() . "\n";
        
        $hocKyHienTai = DB::table('hoc_ky')->where('la_hoc_ky_hien_tai', true)->first();
        if ($hocKyHienTai) {
            echo "   📌 Học kỳ hiện tại: {$hocKyHienTai->ten_hoc_ky} - {$hocKyHienTai->nam_hoc}\n";
        }
    }
}
