<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LichHocChiTiet;
use App\Models\LichHocCoDinh;
use Carbon\Carbon;

class LichHocChiTietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả lịch cố định
        $lichCoDinhs = LichHocCoDinh::with('lopHocPhan')->get();

        if ($lichCoDinhs->isEmpty()) {
            $this->command->warn('Cần chạy LichHocCoDinhSeeder trước!');
            return;
        }

        $ngayBatDau = Carbon::now()->startOfMonth(); // Đầu tháng này
        $ngayKetThuc = Carbon::now()->addMonths(2)->endOfMonth(); // Cuối tháng sau 2 tháng
        $count = 0;

        // Duyệt qua từng ngày
        for ($date = $ngayBatDau->copy(); $date->lte($ngayKetThuc); $date->addDay()) {
            // Lấy thứ trong tuần (2-8)
            $thu = $date->dayOfWeek == 0 ? 8 : $date->dayOfWeek + 1;

            // Tìm lịch cố định tương ứng với thứ này
            foreach ($lichCoDinhs as $lichCoDinh) {
                if ($lichCoDinh->thu_trong_tuan == $thu) {
                    // Kiểm tra xem đã có lịch này chưa
                    $exists = LichHocChiTiet::where('lop_hoc_phan_id', $lichCoDinh->lop_hoc_phan_id)
                        ->where('ngay_hoc', $date->format('Y-m-d'))
                        ->where('tiet_bat_dau', $lichCoDinh->tiet_bat_dau)
                        ->exists();

                    if (!$exists) {
                        // Xác định trạng thái
                        $ngayHoc = Carbon::parse($date);
                        $trangThai = 'chua_day';

                        if ($ngayHoc->lt(Carbon::now()->subDays(1))) {
                            // Ngày trong quá khứ
                            $trangThai = 'da_day';
                        } elseif ($ngayHoc->isToday()) {
                            // Hôm nay
                            $trangThai = 'dang_day';
                        }

                        try {
                            LichHocChiTiet::create([
                                'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                                'lop_hoc_phan_id' => $lichCoDinh->lop_hoc_phan_id,
                                'ngay_hoc' => $date->format('Y-m-d'),
                                'tiet_bat_dau' => $lichCoDinh->tiet_bat_dau,
                                'tiet_ket_thuc' => $lichCoDinh->tiet_ket_thuc,
                                'gio_bat_dau' => $lichCoDinh->gio_bat_dau,
                                'gio_ket_thuc' => $lichCoDinh->gio_ket_thuc,
                                'ca_hoc_id' => $lichCoDinh->ca_hoc_id,
                                'phong_hoc_id' => $lichCoDinh->phong_hoc_id,
                                'giang_vien_id' => $lichCoDinh->giang_vien_id,
                                'hinh_thuc' => $lichCoDinh->hinh_thuc,
                                'link_online' => $lichCoDinh->link_online,
                                'trang_thai' => $trangThai,
                                'noi_dung_giang_day' => $trangThai == 'da_day' ? 'Nội dung đã giảng dạy: ' . fake()->sentence(10) : null,
                                'ghi_chu' => 'Tự động tạo từ lịch cố định',
                            ]);
                            $count++;
                        } catch (\Exception $e) {
                            // Bỏ qua nếu bị trùng
                            continue;
                        }
                    }
                }
            }
        }

        $this->command->info("Đã tạo $count lịch học chi tiết từ ngày {$ngayBatDau->format('d/m/Y')} đến {$ngayKetThuc->format('d/m/Y')}!");
    }
}
