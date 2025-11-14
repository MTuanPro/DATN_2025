<?php

namespace Database\Seeders;

use App\Models\AiChatbotKnowledgeBase;
use Illuminate\Database\Seeder;

class AiChatbotKnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $knowledgeData = [
            // Đăng ký học phần
            [
                'chu_de' => 'dang_ky',
                'cau_hoi_mau' => 'Làm thế nào để đăng ký học phần?',
                'cau_tra_loi' => "Để đăng ký học phần, bạn thực hiện theo các bước sau:\n\n1. Đăng nhập vào hệ thống S-MIS\n2. Vào menu \"Đăng ký học phần\"\n3. Chọn học kỳ cần đăng ký\n4. Chọn các môn học muốn đăng ký\n5. Kiểm tra lịch học và xác nhận đăng ký\n\nLưu ý: Chỉ đăng ký được trong thời gian mở đăng ký học phần.",
                'tu_khoa' => 'đăng ký, đăng ký môn học, đăng ký học phần, dang ky hoc phan',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'dang_ky',
                'cau_hoi_mau' => 'Khi nào mở đăng ký học phần?',
                'cau_tra_loi' => "Thời gian đăng ký học phần thường được mở vào:\n\n- Học kỳ 1: Cuối tháng 7 - đầu tháng 8\n- Học kỳ 2: Cuối tháng 12 - đầu tháng 1\n- Học kỳ hè: Cuối tháng 5 - đầu tháng 6\n\nBạn nên theo dõi thông báo trên hệ thống để biết thời gian chính xác.",
                'tu_khoa' => 'thời gian đăng ký, khi nào đăng ký, mở đăng ký, thoi gian dang ky',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'dang_ky',
                'cau_hoi_mau' => 'Tôi có thể hủy môn đã đăng ký không?',
                'cau_tra_loi' => "Có, bạn có thể hủy môn học đã đăng ký trong thời gian điều chỉnh đăng ký.\n\nCách hủy:\n1. Vào \"Đăng ký học phần\"\n2. Chọn môn học muốn hủy\n3. Click nút \"Hủy đăng ký\"\n\nLưu ý: Sau thời gian điều chỉnh, bạn không thể tự hủy mà phải liên hệ phòng Đào tạo.",
                'tu_khoa' => 'hủy môn, hủy đăng ký, xóa môn học, huy mon hoc',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            
            // Học phí
            [
                'chu_de' => 'hoc_phi',
                'cau_hoi_mau' => 'Làm sao để kiểm tra học phí?',
                'cau_tra_loi' => "Để kiểm tra học phí, bạn làm như sau:\n\n1. Đăng nhập vào hệ thống S-MIS\n2. Vào menu \"Học phí\"\n3. Xem chi tiết học phí theo từng học kỳ\n\nHệ thống sẽ hiển thị:\n- Tổng học phí phải đóng\n- Số tiền đã đóng\n- Số tiền còn thiếu\n- Hạn đóng học phí",
                'tu_khoa' => 'học phí, kiểm tra học phí, xem học phí, hoc phi',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'hoc_phi',
                'cau_hoi_mau' => 'Học phí một tín chỉ là bao nhiêu?',
                'cau_tra_loi' => "Học phí một tín chỉ hiện tại là:\n\n- Khối ngành Kinh tế, Quản trị: 500.000đ/tín chỉ\n- Khối ngành Công nghệ thông tin: 550.000đ/tín chỉ\n- Khối ngành Kỹ thuật: 550.000đ/tín chỉ\n\nLưu ý: Mức học phí có thể thay đổi theo từng năm học. Vui lòng kiểm tra thông báo chính thức từ nhà trường.",
                'tu_khoa' => 'học phí tín chỉ, giá tín chỉ, chi phí học, muc hoc phi',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'hoc_phi',
                'cau_hoi_mau' => 'Nộp học phí ở đâu?',
                'cau_tra_loi' => "Bạn có thể nộp học phí qua các hình thức:\n\n1. **Chuyển khoản ngân hàng:**\n   - Tên TK: Trường Đại học ABC\n   - Số TK: 1234567890\n   - Ngân hàng: Vietcombank Chi nhánh XYZ\n   - Nội dung: Mã SV + Họ tên + Học phí HK\n\n2. **Nộp trực tiếp:** Tại phòng Kế toán (Tòa nhà A, tầng 1)\n\n3. **Cổng thanh toán online:** Trên hệ thống S-MIS (đang phát triển)",
                'tu_khoa' => 'nộp học phí, đóng học phí, thanh toán học phí, chuyen khoan hoc phi',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            
            // Lịch thi
            [
                'chu_de' => 'lich_thi',
                'cau_hoi_mau' => 'Xem lịch thi ở đâu?',
                'cau_tra_loi' => "Để xem lịch thi, bạn làm như sau:\n\n1. Đăng nhập vào hệ thống S-MIS\n2. Vào menu \"TKB & Lịch thi\"\n3. Chọn tab \"Lịch thi\"\n4. Chọn học kỳ cần xem\n\nHệ thống sẽ hiển thị:\n- Môn thi\n- Ngày thi, giờ thi\n- Phòng thi, số báo danh\n- Hình thức thi (Viết/Viva/Trắc nghiệm)",
                'tu_khoa' => 'lịch thi, xem lịch thi, thời gian thi, lich thi',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'lich_thi',
                'cau_hoi_mau' => 'Thi lại thì làm thế nào?',
                'cau_tra_loi' => "Nếu bạn bị điểm F (dưới 4.0) hoặc vắng thi, bạn cần thi lại:\n\n1. Đăng ký thi lại trong đợt đăng ký thi lại\n2. Nộp lệ phí thi lại (100.000đ/môn)\n3. Xem lịch thi lại được thông báo riêng\n4. Tham gia thi lại đúng lịch\n\nLưu ý: Chỉ được thi lại tối đa 2 lần cho mỗi môn học.",
                'tu_khoa' => 'thi lại, thi lai, học lại, môn F, hoc lai',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            
            // Kết quả học tập
            [
                'chu_de' => 'diem',
                'cau_hoi_mau' => 'Xem điểm thi ở đâu?',
                'cau_tra_loi' => "Để xem điểm thi, bạn thực hiện:\n\n1. Đăng nhập vào hệ thống S-MIS\n2. Vào menu \"Kết quả học tập\"\n3. Chọn học kỳ cần xem điểm\n\nHệ thống hiển thị:\n- Điểm từng môn (chuyên cần, giữa kỳ, cuối kỳ)\n- Điểm tổng kết môn\n- GPA học kỳ và tích lũy\n\nLưu ý: Điểm thường được công bố sau 2 tuần kể từ ngày thi.",
                'tu_khoa' => 'điểm thi, xem điểm, kết quả học tập, diem thi, ket qua hoc tap',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'diem',
                'cau_hoi_mau' => 'GPA là gì?',
                'cau_tra_loi' => "GPA (Grade Point Average) là điểm trung bình tích lũy.\n\n**Cách tính:**\nGPA = Tổng (Điểm môn × Số tín chỉ) / Tổng số tín chỉ\n\n**Phân loại học lực:**\n- Xuất sắc: GPA ≥ 3.6\n- Giỏi: 3.2 ≤ GPA < 3.6\n- Khá: 2.5 ≤ GPA < 3.2\n- Trung bình: 2.0 ≤ GPA < 2.5\n- Yếu: GPA < 2.0\n\nGPA rất quan trọng cho học bổng và xếp loại tốt nghiệp.",
                'tu_khoa' => 'GPA, điểm trung bình, điểm tích lũy, diem trung binh',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            
            // Thời khóa biểu
            [
                'chu_de' => 'thoi_khoa_bieu',
                'cau_hoi_mau' => 'Xem thời khóa biểu ở đâu?',
                'cau_tra_loi' => "Để xem thời khóa biểu, bạn làm như sau:\n\n1. Đăng nhập vào hệ thống S-MIS\n2. Vào menu \"TKB & Lịch thi\"\n3. Chọn tuần cần xem\n\nThời khóa biểu hiển thị:\n- Môn học từng ngày\n- Giờ học (tiết mấy)\n- Phòng học\n- Giảng viên\n- Trạng thái (Bình thường/Nghỉ/Đổi lịch)",
                'tu_khoa' => 'thời khóa biểu, tkb, xem tkb, lịch học, thoi khoa bieu',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            
            // Thông tin chung
            [
                'chu_de' => 'thong_tin',
                'cau_hoi_mau' => 'Liên hệ phòng Đào tạo như thế nào?',
                'cau_tra_loi' => "Bạn có thể liên hệ phòng Đào tạo qua:\n\n📞 **Hotline:** 024.xxxx.xxxx\n📧 **Email:** daotao@smis.edu.vn\n🏢 **Địa chỉ:** Tòa nhà B, tầng 2, phòng 201\n🕐 **Giờ làm việc:**\n   - Thứ 2 - Thứ 6: 8h00 - 17h00\n   - Thứ 7: 8h00 - 12h00\n   - Chủ nhật: Nghỉ",
                'tu_khoa' => 'liên hệ, phòng đào tạo, dao tao, hotline, email',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'thong_tin',
                'cau_hoi_mau' => 'Quên mật khẩu thì làm sao?',
                'cau_tra_loi' => "Nếu quên mật khẩu, bạn có thể:\n\n1. **Tự lấy lại mật khẩu:**\n   - Click \"Quên mật khẩu\" ở trang đăng nhập\n   - Nhập email đã đăng ký\n   - Nhận link reset qua email\n   - Đặt mật khẩu mới\n\n2. **Liên hệ IT Support:**\n   - Email: itsupport@smis.edu.vn\n   - Hotline: 024.xxxx.xxxx (máy lẻ 102)\n   - Mang theo thẻ sinh viên đến phòng IT",
                'tu_khoa' => 'quên mật khẩu, reset password, đổi mật khẩu, quen mat khau',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'thong_tin',
                'cau_hoi_mau' => 'Làm thẻ sinh viên ở đâu?',
                'cau_tra_loi' => "Để làm thẻ sinh viên, bạn liên hệ:\n\n📍 **Địa điểm:** Phòng Công tác sinh viên (Tòa B, tầng 1)\n\n📝 **Thủ tục:**\n1. Mang theo 2 ảnh 3×4 (nền trắng)\n2. Điền form đăng ký\n3. Nộp phí làm thẻ: 50.000đ\n\n⏰ **Thời gian nhận thẻ:** 5-7 ngày làm việc\n\n**Trường hợp làm lại thẻ (mất/hư):** Phí 100.000đ",
                'tu_khoa' => 'thẻ sinh viên, làm thẻ, the sinh vien, lam the',
                'do_uu_tien' => 75,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            
            // Chào hỏi
            [
                'chu_de' => 'chao_hoi',
                'cau_hoi_mau' => 'Xin chào',
                'cau_tra_loi' => "Xin chào! 👋\n\nTôi là Trợ lý ảo của S-MIS, sẵn sàng hỗ trợ bạn 24/7.\n\nBạn có thể hỏi tôi về:\n✅ Đăng ký học phần\n✅ Học phí\n✅ Lịch thi, lịch học\n✅ Kết quả học tập\n✅ Thông tin chung\n\nHãy đặt câu hỏi của bạn nhé! 😊",
                'tu_khoa' => 'xin chào, chào, hello, hi, hey',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
        ];

        foreach ($knowledgeData as $data) {
            AiChatbotKnowledgeBase::create($data);
        }

        $this->command->info('✅ Đã thêm ' . count($knowledgeData) . ' câu hỏi mẫu vào Knowledge Base');
    }
}

