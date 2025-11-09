<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CauHinhHocPhi;

class CauHinhHocPhiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing configs first
        CauHinhHocPhi::truncate();

        CauHinhHocPhi::create([
            'nam_hoc' => '2024-2025',
            'don_gia_tren_tin_chi' => 500000, // 500k per credit
            'phi_dich_vu' => 1000000, // 1M service fee
            'ap_dung_tu_ngay' => '2024-08-01', // Start date
            'ap_dung_den_ngay' => '2025-12-31', // End date (extended to cover current date)
            'ghi_chu' => 'Cấu hình học phí năm học 2024-2025',
        ]);

        echo "✅ Created tuition config: 500,000đ/credit + 1,000,000đ service fee\n";
        echo "   Valid from 2024-08-01 to 2025-12-31\n";
    }
}
