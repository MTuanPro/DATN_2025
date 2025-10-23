<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaHocSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ten_khoa_hoc' => 'K17', 'nam_bat_dau' => 2017, 'nam_ket_thuc' => 2021, 'so_nam_dao_tao' => 4, 'trang_thai' => 'da_tot_nghiep', 'mo_ta' => 'Khóa 17 (2017-2021) - Đã tốt nghiệp'],
            ['ten_khoa_hoc' => 'K18', 'nam_bat_dau' => 2018, 'nam_ket_thuc' => 2022, 'so_nam_dao_tao' => 4, 'trang_thai' => 'da_tot_nghiep', 'mo_ta' => 'Khóa 18 (2018-2022) - Đã tốt nghiệp'],
            ['ten_khoa_hoc' => 'K19', 'nam_bat_dau' => 2019, 'nam_ket_thuc' => 2023, 'so_nam_dao_tao' => 4, 'trang_thai' => 'da_tot_nghiep', 'mo_ta' => 'Khóa 19 (2019-2023) - Đã tốt nghiệp'],
            ['ten_khoa_hoc' => 'K20', 'nam_bat_dau' => 2020, 'nam_ket_thuc' => 2024, 'so_nam_dao_tao' => 4, 'trang_thai' => 'da_tot_nghiep', 'mo_ta' => 'Khóa 20 (2020-2024) - Đã tốt nghiệp'],
            ['ten_khoa_hoc' => 'K21', 'nam_bat_dau' => 2021, 'nam_ket_thuc' => 2025, 'so_nam_dao_tao' => 4, 'trang_thai' => 'dang_hoc', 'mo_ta' => 'Khóa 21 (2021-2025) - Năm cuối'],
            ['ten_khoa_hoc' => 'K22', 'nam_bat_dau' => 2022, 'nam_ket_thuc' => 2026, 'so_nam_dao_tao' => 4, 'trang_thai' => 'dang_hoc', 'mo_ta' => 'Khóa 22 (2022-2026) - Năm 4'],
            ['ten_khoa_hoc' => 'K23', 'nam_bat_dau' => 2023, 'nam_ket_thuc' => 2027, 'so_nam_dao_tao' => 4, 'trang_thai' => 'dang_hoc', 'mo_ta' => 'Khóa 23 (2023-2027) - Năm 3'],
            ['ten_khoa_hoc' => 'K24', 'nam_bat_dau' => 2024, 'nam_ket_thuc' => 2028, 'so_nam_dao_tao' => 4, 'trang_thai' => 'dang_hoc', 'mo_ta' => 'Khóa 24 (2024-2028) - Năm 2'],
            ['ten_khoa_hoc' => 'K25', 'nam_bat_dau' => 2025, 'nam_ket_thuc' => 2029, 'so_nam_dao_tao' => 4, 'trang_thai' => 'dang_hoc', 'mo_ta' => 'Khóa 25 (2025-2029) - Năm 1'],
        ];

        foreach ($data as $item) {
            DB::table('khoa_hoc')->updateOrInsert(
                ['ten_khoa_hoc' => $item['ten_khoa_hoc']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
