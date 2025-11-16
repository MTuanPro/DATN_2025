<?php

namespace Database\Seeders;

use App\Models\ThongBao;
use App\Models\User;
use App\Models\NguoiNhanThongBao;
use App\Models\DaoTao\SinhVien;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ThongBaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📢 Bắt đầu tạo thông báo mẫu...');

        // Lấy admin để làm người gửi
        $admin = User::whereHas('vaiTro', function ($q) {
            $q->where('ma_vai_tro', 'admin');
        })->first();

        if (!$admin) {
            $this->command->warn('⚠️  Không tìm thấy admin, sử dụng user ID 1');
            $adminId = 1;
        } else {
            $adminId = $admin->id;
        }

        // 1. Thông báo chung cho tất cả sinh viên
        $thongBao1 = ThongBao::create([
            'tieu_de' => 'Thông báo về lịch đăng ký học phần học kỳ mới',
            'noi_dung' => 'Kính gửi các bạn sinh viên,

Hệ thống thông báo về lịch đăng ký học phần cho học kỳ mới:

📅 Thời gian đăng ký: Từ 01/12/2024 đến 15/12/2024
⏰ Giờ mở hệ thống: 8:00 - 22:00 hàng ngày
📍 Địa điểm: Đăng ký trực tuyến trên hệ thống S-MIS

Lưu ý:
- Sinh viên cần hoàn thành học phí học kỳ trước mới được đăng ký
- Kiểm tra kỹ lịch học để tránh trùng lịch
- Liên hệ phòng Đào tạo nếu có vấn đề: daotao@smis.edu.vn

Trân trọng!',
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'thong_bao_chung',
            'muc_do_quan_trong' => 'quan_trong',
            'ghim_dau_trang' => true,
            'doi_tuong' => 'all',
            'doi_tuong_cu_the_id' => null,
            'nguoi_gui_id' => $adminId,
            'ngay_gui' => now()->subDays(2),
            'ngay_het_han' => now()->addDays(30),
            'gui_web_notification' => true,
            'gui_email' => false,
            'gui_sms' => false,
            'trang_thai' => 'cong_khai',
        ]);

        // Gán cho tất cả sinh viên
        $sinhViens = SinhVien::with('user')->get();
        foreach ($sinhViens as $sv) {
            if ($sv->user_id) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao1->id,
                    'nguoi_nhan_id' => $sv->user_id,
                    'da_doc' => false,
                ]);
            }
        }

        // 2. Thông báo lịch thi
        $thongBao2 = ThongBao::create([
            'tieu_de' => 'Lịch thi cuối kỳ học kỳ 1 năm học 2024-2025',
            'noi_dung' => 'Thông báo lịch thi cuối kỳ đã được cập nhật trên hệ thống.

Các bạn sinh viên vui lòng:
✅ Kiểm tra lịch thi trên hệ thống S-MIS
✅ Xác nhận phòng thi và số báo danh
✅ Có mặt tại phòng thi trước 15 phút
✅ Mang theo thẻ sinh viên và giấy tờ tùy thân

Mọi thắc mắc vui lòng liên hệ phòng Đào tạo.',
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'lich_thi',
            'muc_do_quan_trong' => 'rat_quan_trong',
            'ghim_dau_trang' => true,
            'doi_tuong' => 'sinh_vien',
            'nguoi_gui_id' => $adminId,
            'ngay_gui' => now()->subDays(1),
            'ngay_het_han' => now()->addDays(60),
            'gui_web_notification' => true,
            'gui_email' => true,
            'trang_thai' => 'cong_khai',
        ]);

        foreach ($sinhViens as $sv) {
            if ($sv->user_id) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao2->id,
                    'nguoi_nhan_id' => $sv->user_id,
                    'da_doc' => false,
                ]);
            }
        }

        // 3. Thông báo học phí
        $thongBao3 = ThongBao::create([
            'tieu_de' => 'Thông báo hạn đóng học phí học kỳ 1 năm học 2024-2025',
            'noi_dung' => 'Kính gửi các bạn sinh viên,

Thông báo về hạn đóng học phí học kỳ 1 năm học 2024-2025:

💰 Hạn đóng: 30/11/2024
💳 Phương thức thanh toán:
   - Chuyển khoản ngân hàng
   - Nộp trực tiếp tại phòng Kế toán

⚠️ Lưu ý: Sinh viên chưa hoàn thành học phí sẽ bị khóa chức năng đăng ký học phần.

Mọi thắc mắc vui lòng liên hệ phòng Kế toán.',
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'hoc_phi',
            'muc_do_quan_trong' => 'quan_trong',
            'ghim_dau_trang' => false,
            'doi_tuong' => 'sinh_vien',
            'nguoi_gui_id' => $adminId,
            'ngay_gui' => now()->subDays(5),
            'ngay_het_han' => now()->addDays(10),
            'gui_web_notification' => true,
            'gui_email' => false,
            'trang_thai' => 'cong_khai',
        ]);

        foreach ($sinhViens as $sv) {
            if ($sv->user_id) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao3->id,
                    'nguoi_nhan_id' => $sv->user_id,
                    'da_doc' => false,
                ]);
            }
        }

        // 4. Tin tức
        $thongBao4 = ThongBao::create([
            'tieu_de' => 'Chương trình học bổng học kỳ 1 năm học 2024-2025',
            'noi_dung' => 'Nhà trường thông báo về chương trình học bổng cho học kỳ 1 năm học 2024-2025.

📋 Điều kiện:
- GPA học kỳ >= 3.5
- Không vi phạm nội quy
- Tham gia đầy đủ các hoạt động

💰 Mức học bổng:
- Xuất sắc (GPA >= 3.8): 5.000.000 VNĐ
- Giỏi (GPA >= 3.5): 3.000.000 VNĐ

📅 Hạn nộp hồ sơ: 15/12/2024

Chi tiết xem tại phòng Công tác sinh viên.',
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'tin_tuc',
            'muc_do_quan_trong' => 'binh_thuong',
            'ghim_dau_trang' => false,
            'doi_tuong' => 'all',
            'nguoi_gui_id' => $adminId,
            'ngay_gui' => now()->subDays(3),
            'ngay_het_han' => now()->addDays(20),
            'gui_web_notification' => true,
            'trang_thai' => 'cong_khai',
        ]);

        foreach ($sinhViens as $sv) {
            if ($sv->user_id) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao4->id,
                    'nguoi_nhan_id' => $sv->user_id,
                    'da_doc' => false,
                ]);
            }
        }

        $this->command->info('✅ Đã tạo ' . ThongBao::count() . ' thông báo mẫu');
    }
}

