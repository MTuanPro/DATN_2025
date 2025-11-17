<?php

namespace Database\Seeders;

use App\Models\CaHoc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CaHocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caHocData = [
            [
                'ten_ca' => 'Ca 1',
                'thu_tu' => 1,
                'gio_bat_dau' => '07:00',
                'gio_ket_thuc' => '08:50',
                'trang_thai' => true,
                'ghi_chu' => 'Ca học buổi sáng - Tiết 1,2',
            ],
            [
                'ten_ca' => 'Ca 2',
                'thu_tu' => 2,
                'gio_bat_dau' => '09:00',
                'gio_ket_thuc' => '10:50',
                'trang_thai' => true,
                'ghi_chu' => 'Ca học buổi sáng - Tiết 3,4',
            ],
            [
                'ten_ca' => 'Ca 3',
                'thu_tu' => 3,
                'gio_bat_dau' => '11:00',
                'gio_ket_thuc' => '12:50',
                'trang_thai' => true,
                'ghi_chu' => 'Ca học buổi sáng - Tiết 5,6',
            ],
            [
                'ten_ca' => 'Ca 4',
                'thu_tu' => 4,
                'gio_bat_dau' => '13:00',
                'gio_ket_thuc' => '14:50',
                'trang_thai' => true,
                'ghi_chu' => 'Ca học buổi chiều - Tiết 7,8',
            ],
            [
                'ten_ca' => 'Ca 5',
                'thu_tu' => 5,
                'gio_bat_dau' => '15:00',
                'gio_ket_thuc' => '16:50',
                'trang_thai' => true,
                'ghi_chu' => 'Ca học buổi chiều - Tiết 9,10',
            ],
            [
                'ten_ca' => 'Ca 6',
                'thu_tu' => 6,
                'gio_bat_dau' => '17:00',
                'gio_ket_thuc' => '18:50',
                'trang_thai' => true,
                'ghi_chu' => 'Ca học buổi chiều - Tiết 11,12',
            ],
        ];

        foreach ($caHocData as $data) {
            CaHoc::updateOrCreate(
                ['thu_tu' => $data['thu_tu']],
                $data
            );
        }
    }
}
