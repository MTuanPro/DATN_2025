<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LichHocCoDinh;
use App\Models\LopHocPhan;
use App\Models\DaoTao\PhongHoc;
use App\Models\GiangVien;

class LichHocCoDinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy lớp học phần, phòng học và giảng viên để tạo lịch
        $lopHocPhans = LopHocPhan::take(5)->get();
        $phongHocs = PhongHoc::all();
        $giangViens = GiangVien::all();

        if ($lopHocPhans->isEmpty() || $phongHocs->isEmpty() || $giangViens->isEmpty()) {
            $this->command->warn('Cần có dữ liệu Lớp học phần, Phòng học và Giảng viên trước!');
            return;
        }

        $lichHocs = [];

        foreach ($lopHocPhans as $index => $lopHocPhan) {
            // Mỗi lớp có 2-3 buổi học trong tuần
            $schedules = [
                // Lớp 1: Thứ 2 và Thứ 4
                [
                    ['thu' => 2, 'tiet_bd' => 1, 'tiet_kt' => 3, 'gio_bd' => '07:00', 'gio_kt' => '09:30'],
                    ['thu' => 4, 'tiet_bd' => 1, 'tiet_kt' => 3, 'gio_bd' => '07:00', 'gio_kt' => '09:30'],
                ],
                // Lớp 2: Thứ 3 và Thứ 5
                [
                    ['thu' => 3, 'tiet_bd' => 4, 'tiet_kt' => 6, 'gio_bd' => '09:45', 'gio_kt' => '12:15'],
                    ['thu' => 5, 'tiet_bd' => 4, 'tiet_kt' => 6, 'gio_bd' => '09:45', 'gio_kt' => '12:15'],
                ],
                // Lớp 3: Thứ 2, Thứ 4, Thứ 6
                [
                    ['thu' => 2, 'tiet_bd' => 7, 'tiet_kt' => 9, 'gio_bd' => '13:00', 'gio_kt' => '15:30'],
                    ['thu' => 4, 'tiet_bd' => 7, 'tiet_kt' => 9, 'gio_bd' => '13:00', 'gio_kt' => '15:30'],
                    ['thu' => 6, 'tiet_bd' => 7, 'tiet_kt' => 9, 'gio_bd' => '13:00', 'gio_kt' => '15:30'],
                ],
                // Lớp 4: Thứ 3 và Thứ 6
                [
                    ['thu' => 3, 'tiet_bd' => 1, 'tiet_kt' => 3, 'gio_bd' => '07:00', 'gio_kt' => '09:30'],
                    ['thu' => 6, 'tiet_bd' => 4, 'tiet_kt' => 6, 'gio_bd' => '09:45', 'gio_kt' => '12:15'],
                ],
                // Lớp 5: Thứ 5 và Thứ 7
                [
                    ['thu' => 5, 'tiet_bd' => 7, 'tiet_kt' => 9, 'gio_bd' => '13:00', 'gio_kt' => '15:30'],
                    ['thu' => 7, 'tiet_bd' => 1, 'tiet_kt' => 3, 'gio_bd' => '07:00', 'gio_kt' => '09:30'],
                ],
            ];

            $schedule = $schedules[$index % 5];

            foreach ($schedule as $buoi) {
                $phongHoc = $phongHocs->random();
                $giangVien = $giangViens->random();

                $lichHocs[] = [
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'thu_trong_tuan' => $buoi['thu'],
                    'tiet_bat_dau' => $buoi['tiet_bd'],
                    'tiet_ket_thuc' => $buoi['tiet_kt'],
                    'gio_bat_dau' => $buoi['gio_bd'],
                    'gio_ket_thuc' => $buoi['gio_kt'],
                    'phong_hoc_id' => $phongHoc->id,
                    'giang_vien_id' => $giangVien->id,
                    'hinh_thuc' => ['offline', 'online', 'hybrid'][array_rand(['offline', 'online', 'hybrid'])],
                    'link_online' => rand(0, 1) ? 'https://meet.google.com/abc-defg-hij' : null,
                    'ghi_chu' => 'Lịch học cố định hàng tuần',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($lichHocs as $lichHoc) {
            try {
                LichHocCoDinh::create($lichHoc);
            } catch (\Exception $e) {
                // Bỏ qua nếu bị trùng unique constraint
                continue;
            }
        }

        $this->command->info('Đã tạo ' . count($lichHocs) . ' lịch học cố định!');
    }
}
