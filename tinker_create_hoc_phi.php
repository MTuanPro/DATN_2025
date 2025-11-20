<?php
/**
 * Chạy trong PHP Artisan Tinker
 * Command: php artisan tinker
 * Sau đó paste đoạn code này vào
 */

// Lấy sinh viên
$sv = \App\Models\SinhVien::where('ma_sinh_vien', 'SV001')->first();

// Lấy hoặc tạo học kỳ
$hocKy = \App\Models\HocKy::firstOrCreate(
    ['ten_hoc_ky' => 'Học kỳ 1', 'nam_hoc' => '2025-2026'],
    [
        'ngay_bat_dau' => '2025-09-01',
        'ngay_ket_thuc' => '2026-01-15',
        'trang_thai' => 'dang_hoc',
    ]
);

// Xóa học phí cũ nếu có
\App\Models\HocPhiHocKy::where('sinh_vien_id', $sv->id)->where('hoc_ky_id', $hocKy->id)->delete();

// Tạo học phí mới
$hocPhi = \App\Models\HocPhiHocKy::create([
    'sinh_vien_id' => $sv->id,
    'hoc_ky_id' => $hocKy->id,
    'tong_hoc_phi_mon_hoc' => 6500000,
    'phi_dich_vu' => 500000,
    'tong_so_tien' => 7000000,
    'so_tien_da_dong' => 0,
    'so_tien_con_lai' => 7000000,
    'han_dong' => now()->addDays(30),
    'trang_thai' => 'chua_nop_du',
]);

// Tạo chi tiết môn học (5 môn)
for ($i = 1; $i <= 5; $i++) {
    \App\Models\ChiTietHocPhiMon::create([
        'hoc_phi_hoc_ky_id' => $hocPhi->id,
        'mon_hoc_id' => $i,
        'lop_hoc_phan_sinh_vien_id' => null,
        'so_tin_chi' => 3,
        'don_gia_tin_chi' => 400000,
        'thanh_tien' => 1200000,
        'trang_thai' => 'chua_thanh_toan',
    ]);
}

echo "✅ Đã tạo học phí test thành công!\n";
echo "Tổng học phí: 7,000,000 VNĐ\n";
echo "ID học phí: " . $hocPhi->id . "\n";
