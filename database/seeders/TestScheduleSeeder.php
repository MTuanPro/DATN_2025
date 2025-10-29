<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestScheduleSeeder extends Seeder
{
    /**
     * Seed a small, safe set of test data for a giảng viên and schedules.
     */
    public function run(): void
    {
        // Test account
        $email = 'test.giangvien@smis.test';

        $existingUser = DB::table('users')->where('email', $email)->first();
        if ($existingUser) {
            $userId = $existingUser->id;
        } else {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Giảng viên Test',
                'email' => $email,
                'password' => Hash::make('password'),
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ensure role mapping exists (assumes vai_tro 'giang_vien' exists from other seeders)
        $vaiTro = DB::table('vai_tro')->where('ma_vai_tro', 'giang_vien')->first();
        if ($vaiTro) {
            $exists = DB::table('tai_khoan_vai_tro')
                ->where('tai_khoan_id', $userId)
                ->where('vai_tro_id', $vaiTro->id)
                ->exists();
            if (! $exists) {
                DB::table('tai_khoan_vai_tro')->insert([
                    'tai_khoan_id' => $userId,
                    'vai_tro_id' => $vaiTro->id,
                    'ngay_gan' => now(),
                    'nguoi_gan_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create or find giang_vien
        $giangVien = DB::table('giang_vien')->where('user_id', $userId)->first();
        if (! $giangVien) {
            // try to pick some defaults
            $trinhDo = DB::table('dm_trinh_do')->first();
            $khoa = DB::table('khoa')->first();

            $gvId = DB::table('giang_vien')->insertGetId([
                'ma_giang_vien' => 'TEST_GV',
                'ho_ten' => 'Giảng viên Test',
                'email' => $email,
                'so_dien_thoai' => '0900000000',
                'trinh_do_id' => $trinhDo->id ?? null,
                'chuyen_mon' => 'Lập trình',
                'khoa_id' => $khoa->id ?? null,
                'ngay_vao_truong' => now()->toDateString(),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $gvId = $giangVien->id;
        }

        // Pick a room
        $phong = DB::table('phong_hoc')->first();
        if (! $phong) {
            $phongId = DB::table('phong_hoc')->insertGetId([
                'ma_phong' => 'P-101',
                'ten_phong' => 'Phòng 101',
                'ghi_chu' => 'Phòng test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $phongId = $phong->id;
        }

        // Try to find any lop_hoc_phan; if none, create a minimal one (requires mon_hoc and hoc_ky)
        $lopHP = DB::table('lop_hoc_phan')->first();
        if ($lopHP) {
            $lopHocPhanId = $lopHP->id;
        } else {
            // Ensure MonHoc exists
            $mon = DB::table('mon_hoc')->first();
            if (! $mon) {
                $monId = DB::table('mon_hoc')->insertGetId([
                    'ma_mon' => 'TEST101',
                    'ten_mon' => 'Môn Test',
                    'so_tin_chi' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $monId = $mon->id;
            }

            // Ensure HocKy exists
            $hk = DB::table('hoc_ky')->first();
            if (! $hk) {
                $hkId = DB::table('hoc_ky')->insertGetId([
                    'ten_hoc_ky' => 'HK Test',
                    'ngay_bat_dau' => Carbon::today()->subMonth()->toDateString(),
                    'ngay_ket_thuc' => Carbon::today()->addMonth()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $hkId = $hk->id;
            }

            $maLopHp = 'TEST.' . ($hkId ?? 1) . '.01';
            $lopHocPhanId = DB::table('lop_hoc_phan')->insertGetId([
                'ma_lop_hp' => $maLopHp,
                'ten_lop_hp' => 'Lớp Test',
                'mon_hoc_id' => $monId,
                'hoc_ky_id' => $hkId,
                'nhom_lop' => 1,
                'suc_chua' => 50,
                'so_luong_dang_ky' => 0,
                'so_luong_toi_thieu' => 10,
                'hinh_thuc' => 'offline',
                'link_online' => null,
                'ngay_bat_dau' => Carbon::today()->toDateString(),
                'ngay_ket_thuc' => Carbon::today()->addMonths(3)->toDateString(),
                'ghi_chu' => 'Lớp test tạo tự động',
                'trang_thai_lop' => 'mo_dang_ky',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create a LichHocCoDinh for the current weekday
        $today = Carbon::today();
        $iso = $today->isoWeekday(); // 1..7
        $thu_trong_tuan = $iso + 1; // 2..8

        // Insert a recurring slot (if not exists similar)
        $existsCoDinh = DB::table('lich_hoc_co_dinh')
            ->where('giang_vien_id', $gvId)
            ->where('thu_trong_tuan', $thu_trong_tuan)
            ->where('tiet_bat_dau', 1)
            ->exists();

        if (! $existsCoDinh) {
            DB::table('lich_hoc_co_dinh')->insert([
                'lop_hoc_phan_id' => $lopHocPhanId,
                'thu_trong_tuan' => $thu_trong_tuan,
                'tiet_bat_dau' => 1,
                'tiet_ket_thuc' => 3,
                'gio_bat_dau' => '08:00:00',
                'gio_ket_thuc' => '10:30:00',
                'phong_hoc_id' => $phongId,
                'giang_vien_id' => $gvId,
                'hinh_thuc' => 'offline',
                'link_online' => 'https://meet.google.com/' . substr(md5($email), 0, 10),
                'ghi_chu' => 'Buổi test cố định',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create a LichHocChiTiet for today (explicit)
        $existsChiTiet = DB::table('lich_hoc_chi_tiet')
            ->where('giang_vien_id', $gvId)
            ->where('ngay_hoc', $today->toDateString())
            ->exists();

        if (! $existsChiTiet) {
            DB::table('lich_hoc_chi_tiet')->insert([
                'lich_hoc_co_dinh_id' => null,
                'lop_hoc_phan_id' => $lopHocPhanId,
                'ngay_hoc' => $today->toDateString(),
                'tiet_bat_dau' => 1,
                'tiet_ket_thuc' => 3,
                'gio_bat_dau' => '08:00:00',
                'gio_ket_thuc' => '10:30:00',
                'phong_hoc_id' => $phongId,
                'giang_vien_id' => $gvId,
                'hinh_thuc' => 'offline',
                'link_online' => 'https://meet.google.com/' . substr(md5($email . 'chi_tiet'), 0, 10),
                'noi_dung_giang_day' => 'Buổi test chi tiết',
                'tai_lieu_dinh_kem' => null,
                'trang_thai' => 'chua_day',
                'ghi_chu' => 'Sinh dữ liệu test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // If records already existed but had no link_online, update the first one to include a sample link
        $updated = DB::table('lich_hoc_chi_tiet')
            ->where('giang_vien_id', $gvId)
            ->whereNull('link_online')
            ->limit(1)
            ->update(['link_online' => 'https://meet.google.com/' . substr(md5($email . 'update'), 0, 10)]);
        if (! $updated) {
            // try updating a co_dinh if none in chi_tiet
            DB::table('lich_hoc_co_dinh')
                ->where('giang_vien_id', $gvId)
                ->whereNull('link_online')
                ->limit(1)
                ->update(['link_online' => 'https://meet.google.com/' . substr(md5($email . 'update'), 0, 10)]);
        }

        $this->command->info('Test giảng viên and schedule seeded. Login with: '.$email.' / password');
    }
}
