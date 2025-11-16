<?php

namespace Database\Seeders;

use App\Models\MauThongBaoTuDong;
use Illuminate\Database\Seeder;

class MauThongBaoTuDongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Bắt đầu tạo mẫu thông báo tự động...');

        $mauThongBaos = [
            [
                'loai_thong_bao' => 'lich_hoc_moi',
                'tieu_de_mau' => 'Lịch học mới: {mon_hoc} - {lop_hoc_phan}',
                'noi_dung_mau' => 'Bạn đã được phân vào lớp học phần:

📚 Môn học: {mon_hoc}
📝 Lớp học phần: {lop_hoc_phan}
👨‍🏫 Giảng viên: {giang_vien}
📅 Lịch học: {lich_hoc}
📍 Phòng học: {phong_hoc}

Vui lòng kiểm tra và tham gia đầy đủ các buổi học.',
                'doi_tuong_mac_dinh' => 'sinh_vien',
                'muc_do_uu_tien' => 'binh_thuong',
                'gui_email_mac_dinh' => false,
                'gui_sms_mac_dinh' => false,
                'kich_hoat' => true,
                'ghi_chu' => 'Gửi khi sinh viên được phân vào lớp học phần mới',
            ],
            [
                'loai_thong_bao' => 'lich_thi_sap_toi',
                'tieu_de_mau' => 'Nhắc nhở: Lịch thi {mon_hoc} sắp tới',
                'noi_dung_mau' => 'Bạn có lịch thi sắp tới:

📚 Môn thi: {mon_hoc}
📅 Ngày thi: {ngay_thi}
⏰ Giờ thi: {gio_thi}
📍 Phòng thi: {phong_thi}
🔢 Số báo danh: {so_bao_danh}

Vui lòng chuẩn bị và có mặt tại phòng thi trước 15 phút.',
                'doi_tuong_mac_dinh' => 'sinh_vien',
                'muc_do_uu_tien' => 'quan_trong',
                'gui_email_mac_dinh' => true,
                'gui_sms_mac_dinh' => false,
                'kich_hoat' => true,
                'ghi_chu' => 'Gửi trước 3 ngày khi có lịch thi',
            ],
            [
                'loai_thong_bao' => 'hoc_phi_sap_het_han',
                'tieu_de_mau' => 'Cảnh báo: Học phí {hoc_ky} sắp hết hạn',
                'noi_dung_mau' => 'Thông báo về học phí:

💰 Tổng học phí: {tong_hoc_phi} VNĐ
💵 Đã đóng: {da_dong} VNĐ
❌ Còn nợ: {con_no} VNĐ
📅 Hạn đóng: {han_dong}

Vui lòng thanh toán trước hạn để tránh bị khóa chức năng đăng ký học phần.',
                'doi_tuong_mac_dinh' => 'sinh_vien',
                'muc_do_uu_tien' => 'quan_trong',
                'gui_email_mac_dinh' => true,
                'gui_sms_mac_dinh' => false,
                'kich_hoat' => true,
                'ghi_chu' => 'Gửi trước 7 ngày khi học phí sắp hết hạn',
            ],
            [
                'loai_thong_bao' => 'diem_da_cap_nhat',
                'tieu_de_mau' => 'Điểm {mon_hoc} đã được cập nhật',
                'noi_dung_mau' => 'Điểm môn học của bạn đã được cập nhật:

📚 Môn học: {mon_hoc}
📝 Lớp học phần: {lop_hoc_phan}
📊 Điểm tổng kết: {diem_tong_ket}
📈 Điểm chữ: {diem_chu}
✅ Trạng thái: {qua_mon}

Vui lòng kiểm tra trên hệ thống.',
                'doi_tuong_mac_dinh' => 'sinh_vien',
                'muc_do_uu_tien' => 'binh_thuong',
                'gui_email_mac_dinh' => false,
                'gui_sms_mac_dinh' => false,
                'kich_hoat' => true,
                'ghi_chu' => 'Gửi khi giảng viên cập nhật điểm',
            ],
            [
                'loai_thong_bao' => 'dang_ky_mon_thanh_cong',
                'tieu_de_mau' => 'Đăng ký môn {mon_hoc} thành công',
                'noi_dung_mau' => 'Bạn đã đăng ký thành công:

📚 Môn học: {mon_hoc}
📝 Lớp học phần: {lop_hoc_phan}
👨‍🏫 Giảng viên: {giang_vien}
📅 Lịch học: {lich_hoc}

Vui lòng kiểm tra lịch học và tham gia đầy đủ.',
                'doi_tuong_mac_dinh' => 'sinh_vien',
                'muc_do_uu_tien' => 'binh_thuong',
                'gui_email_mac_dinh' => false,
                'gui_sms_mac_dinh' => false,
                'kich_hoat' => true,
                'ghi_chu' => 'Gửi khi sinh viên đăng ký môn thành công',
            ],
            [
                'loai_thong_bao' => 'dang_ky_mon_that_bai',
                'tieu_de_mau' => 'Đăng ký môn {mon_hoc} thất bại',
                'noi_dung_mau' => 'Đăng ký môn học không thành công:

📚 Môn học: {mon_hoc}
❌ Lý do: {ly_do}

Vui lòng kiểm tra và thử lại hoặc liên hệ phòng Đào tạo.',
                'doi_tuong_mac_dinh' => 'sinh_vien',
                'muc_do_uu_tien' => 'quan_trong',
                'gui_email_mac_dinh' => false,
                'gui_sms_mac_dinh' => false,
                'kich_hoat' => true,
                'ghi_chu' => 'Gửi khi đăng ký môn thất bại',
            ],
            [
                'loai_thong_bao' => 'canh_bao_hoc_vu',
                'tieu_de_mau' => 'Cảnh báo học vụ: {loai_canh_bao}',
                'noi_dung_mau' => 'Bạn nhận được cảnh báo học vụ:

⚠️ Loại cảnh báo: {loai_canh_bao}
📊 Mức độ: {muc_do}
📝 Lý do: {ly_do}

Vui lòng liên hệ phòng Đào tạo để được tư vấn và hỗ trợ.',
                'doi_tuong_mac_dinh' => 'sinh_vien',
                'muc_do_uu_tien' => 'rat_quan_trong',
                'gui_email_mac_dinh' => true,
                'gui_sms_mac_dinh' => true,
                'kich_hoat' => true,
                'ghi_chu' => 'Gửi khi có cảnh báo học vụ',
            ],
        ];

        foreach ($mauThongBaos as $mau) {
            MauThongBaoTuDong::create($mau);
        }

        $this->command->info('✅ Đã tạo ' . count($mauThongBaos) . ' mẫu thông báo tự động');
    }
}

