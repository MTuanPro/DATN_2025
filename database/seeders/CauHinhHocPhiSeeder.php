<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CauHinhHocPhiSeeder extends Seeder
{
    /**
     * Tạo cấu hình học phí cho các năm học
     */
    public function run(): void
    {
        $cauHinhs = [
            // Năm học 2023-2024 (đã kết thúc)
            [
                'nam_hoc' => '2023-2024',
                'don_gia_tren_tin_chi' => 350000, // 350,000 VNĐ/tín chỉ
                'phi_dich_vu' => 500000, // 500,000 VNĐ (phí dịch vụ, bảo hiểm)
                'ap_dung_tu_ngay' => Carbon::create(2023, 9, 1),
                'ap_dung_den_ngay' => Carbon::create(2024, 8, 31),
                'ghi_chu' => 'Cấu hình học phí năm học 2023-2024',
            ],
            
            // Năm học 2024-2025 (đã kết thúc)
            [
                'nam_hoc' => '2024-2025',
                'don_gia_tren_tin_chi' => 380000, // 380,000 VNĐ/tín chỉ
                'phi_dich_vu' => 550000, // 550,000 VNĐ
                'ap_dung_tu_ngay' => Carbon::create(2024, 9, 1),
                'ap_dung_den_ngay' => Carbon::create(2025, 8, 31),
                'ghi_chu' => 'Cấu hình học phí năm học 2024-2025',
            ],
            
            // Năm học 2025-2026 (năm học hiện tại - đang áp dụng)
            [
                'nam_hoc' => '2025-2026',
                'don_gia_tren_tin_chi' => 400000, // 400,000 VNĐ/tín chỉ
                'phi_dich_vu' => 600000, // 600,000 VNĐ
                'ap_dung_tu_ngay' => Carbon::create(2025, 9, 1),
                'ap_dung_den_ngay' => null, // Chưa có ngày kết thúc (đang áp dụng)
                'ghi_chu' => 'Cấu hình học phí năm học 2025-2026 - Đang áp dụng',
            ],
            
            // Năm học 2026-2027 (dự kiến)
            [
                'nam_hoc' => '2026-2027',
                'don_gia_tren_tin_chi' => 420000, // 420,000 VNĐ/tín chỉ
                'phi_dich_vu' => 650000, // 650,000 VNĐ
                'ap_dung_tu_ngay' => Carbon::create(2026, 9, 1),
                'ap_dung_den_ngay' => null,
                'ghi_chu' => 'Cấu hình học phí năm học 2026-2027 - Dự kiến',
            ],
        ];

        $count = 0;
        foreach ($cauHinhs as $cauHinh) {
            DB::table('cau_hinh_hoc_phi')->updateOrInsert(
                [
                    'nam_hoc' => $cauHinh['nam_hoc'],
                ],
                array_merge($cauHinh, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
            $count++;
        }

        echo "✅ Đã tạo/cập nhật {$count} cấu hình học phí\n";
        echo "   📅 Năm học 2023-2024: 350,000 VNĐ/tín chỉ\n";
        echo "   📅 Năm học 2024-2025: 380,000 VNĐ/tín chỉ\n";
        echo "   📅 Năm học 2025-2026: 400,000 VNĐ/tín chỉ (Đang áp dụng)\n";
        echo "   📅 Năm học 2026-2027: 420,000 VNĐ/tín chỉ (Dự kiến)\n";
    }
}

