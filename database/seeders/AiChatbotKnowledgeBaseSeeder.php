<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiChatbotKnowledgeBase;
use App\Models\User;

class AiChatbotKnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::whereHas('vaiTro', function($q) {
            $q->where('ma_vai_tro', 'admin');
        })->first();

        $adminId = $admin ? $admin->id : null;

        $knowledgeData = [
            // Đăng ký môn học
            [
                'chu_de' => 'Đăng ký môn học',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Làm thế nào để đăng ký môn học?',
                'cau_tra_loi' => "Để đăng ký môn học, bạn thực hiện các bước sau:\n\n1. Đăng nhập vào hệ thống S-MIS\n2. Vào menu \"Đăng ký môn học\"\n3. Chọn học kỳ cần đăng ký\n4. Chọn các môn học muốn đăng ký (lưu ý kiểm tra môn tiên quyết)\n5. Xác nhận đăng ký\n\n📌 Lưu ý: Chỉ đăng ký được trong thời gian mở đăng ký của học kỳ.",
                'tu_khoa' => 'đăng ký môn, đăng ký học phần, đăng ký lớp, đky',
                'do_uu_tien' => 100,
            ],
            [
                'chu_de' => 'Đăng ký môn học',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Khi nào được đăng ký môn học?',
                'cau_tra_loi' => "Thời gian đăng ký môn học sẽ được thông báo trước mỗi học kỳ.\n\nThông thường:\n- Đăng ký chính: Tuần đầu tiên trước khi học kỳ bắt đầu\n- Đăng ký bổ sung: Tuần đầu tiên của học kỳ\n\n📢 Theo dõi thông báo từ Phòng Đào tạo để biết chính xác thời gian đăng ký.",
                'tu_khoa' => 'thời gian đăng ký, khi nào đăng ký, đăng ký môn, deadline',
                'do_uu_tien' => 95,
            ],
            [
                'chu_de' => 'Đăng ký môn học',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Đăng ký sai môn học phải làm sao?',
                'cau_tra_loi' => "Nếu đăng ký sai môn học, bạn có thể:\n\n1. Hủy đăng ký trong thời gian cho phép (thường là trước khi học kỳ bắt đầu)\n2. Vào \"Đăng ký môn học\" → \"Danh sách đã đăng ký\" → Hủy môn không muốn học\n3. Đăng ký lại môn đúng\n\n⚠️ Lưu ý: Không thể hủy đăng ký sau khi học kỳ đã bắt đầu. Liên hệ Phòng Đào tạo nếu cần hỗ trợ.",
                'tu_khoa' => 'hủy đăng ký, xóa môn, sai môn, đăng ký nhầm',
                'do_uu_tien' => 90,
            ],
            [
                'chu_de' => 'Đăng ký môn học',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Môn tiên quyết là gì?',
                'cau_tra_loi' => "Môn tiên quyết là môn học bắt buộc phải hoàn thành trước khi được đăng ký môn khác.\n\nVí dụ:\n- Phải học Toán 1 trước khi học Toán 2\n- Phải học Lập trình cơ bản trước khi học Lập trình hướng đối tượng\n\n✅ Hệ thống sẽ tự động kiểm tra và chỉ cho phép đăng ký nếu đã hoàn thành môn tiên quyết.",
                'tu_khoa' => 'môn tiên quyết, môn học trước, điều kiện môn học, prerequisite',
                'do_uu_tien' => 85,
            ],
            [
                'chu_de' => 'Đăng ký môn học',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Đăng ký được tối đa bao nhiêu tín chỉ?',
                'cau_tra_loi' => "Số tín chỉ tối đa được đăng ký mỗi học kỳ phụ thuộc vào:\n\n- Học lực học kỳ trước (GPA)\n- Quy định của trường\n\nThông thường:\n- GPA >= 3.2: Tối đa 24 tín chỉ\n- GPA 2.5-3.2: Tối đa 20 tín chỉ\n- GPA < 2.5: Tối đa 16 tín chỉ\n\n📌 Sinh viên năm 1 học kỳ 1: Tối đa 18 tín chỉ",
                'tu_khoa' => 'số tín chỉ, tín chỉ tối đa, đăng ký bao nhiêu tín, giới hạn tín chỉ',
                'do_uu_tien' => 80,
            ],

            // Lịch học, Lịch thi
            [
                'chu_de' => 'Lịch học & Lịch thi',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Xem lịch học ở đâu?',
                'cau_tra_loi' => "Bạn có thể xem lịch học tại:\n\n1. Đăng nhập S-MIS\n2. Vào menu \"Thời khóa biểu\"\n3. Chọn học kỳ cần xem\n\n📅 Lịch học hiển thị theo tuần, bao gồm:\n- Môn học\n- Thời gian (Thứ, Tiết)\n- Phòng học\n- Giảng viên\n\n💡 Có thể xuất lịch học ra PDF để in hoặc lưu.",
                'tu_khoa' => 'lịch học, thời khóa biểu, TKB, xem lịch, lịch học phần',
                'do_uu_tien' => 95,
            ],
            [
                'chu_de' => 'Lịch học & Lịch thi',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Lịch thi được công bố khi nào?',
                'cau_tra_loi' => "Lịch thi thường được công bố:\n\n- Thi giữa kỳ: 1 tuần trước khi thi\n- Thi cuối kỳ: 2 tuần trước khi thi\n\n📢 Sinh viên cần thường xuyên kiểm tra:\n1. Mục \"Lịch thi\" trên S-MIS\n2. Thông báo từ Phòng Đào tạo\n3. Email sinh viên\n\n⚠️ Lưu ý: Không được vắng thi khi không có lý do chính đáng.",
                'tu_khoa' => 'lịch thi, lịch kiểm tra, thi giữa kỳ, thi cuối kỳ, xem lịch thi',
                'do_uu_tien' => 90,
            ],
            [
                'chu_de' => 'Lịch học & Lịch thi',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Trùng lịch thi phải làm sao?',
                'cau_tra_loi' => "Nếu bị trùng lịch thi, bạn cần:\n\n1. Liên hệ ngay với Phòng Đào tạo (càng sớm càng tốt)\n2. Cung cấp thông tin: Mã sinh viên, tên 2 môn bị trùng lịch\n3. Chờ phòng Đào tạo sắp xếp lịch thi riêng\n\n📞 Hotline Đào tạo: 024.xxxx.xxxx\n📧 Email: daotao@smis.edu.vn",
                'tu_khoa' => 'trùng lịch thi, thi trùng giờ, 2 môn cùng giờ thi',
                'do_uu_tien' => 85,
            ],

            // Học phí
            [
                'chu_de' => 'Học phí',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Học phí được tính như thế nào?',
                'cau_tra_loi' => "Học phí được tính theo công thức:\n\n💰 Học phí = Số tín chỉ × Đơn giá tín chỉ\n\nVí dụ:\n- Đăng ký 18 tín chỉ\n- Đơn giá: 500.000đ/tín chỉ\n- Học phí = 18 × 500.000 = 9.000.000đ\n\n📌 Đơn giá có thể khác nhau tùy theo:\n- Ngành học\n- Khóa học\n- Môn học (một số môn có đơn giá riêng)",
                'tu_khoa' => 'học phí, tính học phí, tiền học, học phí bao nhiêu, đơn giá',
                'do_uu_tien' => 100,
            ],
            [
                'chu_de' => 'Học phí',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Hạn nộp học phí khi nào?',
                'cau_tra_loi' => "Hạn nộp học phí thường là:\n\n- 📅 Trong vòng 2 tuần sau khi bắt đầu học kỳ\n- Ngày cụ thể sẽ được thông báo qua:\n  + Email sinh viên\n  + Thông báo trên S-MIS\n  + Website trường\n\n⚠️ Lưu ý:\n- Nộp muộn có thể bị phạt\n- Không nộp học phí có thể bị khóa tài khoản và không được thi",
                'tu_khoa' => 'hạn nộp học phí, deadline học phí, nộp học phí khi nào, thời hạn',
                'do_uu_tien' => 95,
            ],
            [
                'chu_de' => 'Học phí',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Nộp học phí ở đâu?',
                'cau_tra_loi' => "Có nhiều cách nộp học phí:\n\n1. 🏦 Chuyển khoản ngân hàng:\n   - STK: XXXXXXX\n   - Ngân hàng: XXX\n   - Nội dung: HOCPHI_MASV_HOTEN\n\n2. 🏢 Nộp trực tiếp tại phòng Kế toán:\n   - Giờ làm việc: 8h-11h30, 13h30-17h\n   - Thứ 2-6\n\n3. 💳 Thanh toán online qua cổng thanh toán trên S-MIS (sắp ra mắt)\n\n📌 Lưu biên lai sau khi nộp!",
                'tu_khoa' => 'nộp học phí, đóng học phí, thanh toán học phí, chuyển khoản',
                'do_uu_tien' => 90,
            ],
            [
                'chu_de' => 'Học phí',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Đã nộp học phí nhưng chưa cập nhật trên hệ thống?',
                'cau_tra_loi' => "Nếu đã nộp học phí nhưng chưa cập nhật:\n\n1. ⏰ Chờ 1-2 ngày làm việc (hệ thống cập nhật định kỳ)\n\n2. 📧 Nếu quá 2 ngày chưa cập nhật, liên hệ:\n   - Email: ketoan@smis.edu.vn\n   - Hotline: 024.xxxx.xxxx\n   - Mang theo biên lai nộp tiền\n\n3. ✅ Phòng Kế toán sẽ kiểm tra và cập nhật cho bạn",
                'tu_khoa' => 'học phí chưa cập nhật, đã nộp mà chưa có, kiểm tra học phí',
                'do_uu_tien' => 85,
            ],

            // Điểm & Kết quả học tập
            [
                'chu_de' => 'Điểm & Kết quả học tập',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Xem điểm ở đâu?',
                'cau_tra_loi' => "Để xem điểm:\n\n1. Đăng nhập S-MIS\n2. Vào menu \"Xem điểm\"\n3. Chọn:\n   - Xem điểm từng môn học\n   - Xem bảng điểm tổng hợp\n\n📊 Thông tin hiển thị:\n- Điểm chuyên cần, kiểm tra, thi\n- Điểm tổng kết môn\n- Điểm chữ, điểm số\n- Kết quả (Đạt/Không đạt)\n\n💡 Có thể xuất bảng điểm ra PDF",
                'tu_khoa' => 'xem điểm, điểm số, kết quả học tập, tra cứu điểm, bảng điểm',
                'do_uu_tien' => 100,
            ],
            [
                'chu_de' => 'Điểm & Kết quả học tập',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Khi nào có điểm?',
                'cau_tra_loi' => "Thời gian công bố điểm:\n\n- Điểm giữa kỳ: Trong vòng 1 tuần sau khi thi\n- Điểm cuối kỳ: Trong vòng 2 tuần sau khi thi\n\n📢 Quy trình:\n1. Giảng viên nhập điểm\n2. Phòng Đào tạo duyệt điểm\n3. Sinh viên xem được điểm\n\n⚠️ Điểm chính thức chỉ hiển thị sau khi được duyệt",
                'tu_khoa' => 'khi nào có điểm, điểm lên, công bố điểm, thời gian điểm',
                'do_uu_tien' => 95,
            ],
            [
                'chu_de' => 'Điểm & Kết quả học tập',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Điểm bị sai phải làm sao?',
                'cau_tra_loi' => "Nếu nghi ngờ điểm bị sai:\n\n1. ✅ Liên hệ giảng viên môn học để phúc tra điểm\n2. 📝 Nộp đơn phúc tra điểm (nếu cần)\n3. ⏰ Hạn phúc tra: Trong vòng 1 tuần sau khi công bố điểm\n\n📞 Liên hệ:\n- Email giảng viên (xem trong \"Lớp học phần\")\n- Hoặc qua Phòng Đào tạo: daotao@smis.edu.vn",
                'tu_khoa' => 'điểm sai, phúc tra điểm, khiếu nại điểm, kiểm tra lại điểm',
                'do_uu_tien' => 90,
            ],

            // Quy chế đào tạo
            [
                'chu_de' => 'Quy chế đào tạo',
                'danh_muc' => 'Quy định',
                'cau_hoi_mau' => 'Điều kiện để được thi là gì?',
                'cau_tra_loi' => "Để được dự thi, sinh viên phải đáp ứng:\n\n✅ Điều kiện:\n1. Điểm danh đủ ít nhất 80% buổi học\n2. Nộp đủ học phí (hoặc được xác nhận hoãn nộp)\n3. Không vi phạm quy chế thi cử\n4. Hoàn thành các bài tập, thực hành (nếu có)\n\n⚠️ Không đủ điều kiện → Không được thi → Điểm 0 và phải học lại",
                'tu_khoa' => 'điều kiện thi, được thi, dự thi, tham gia thi',
                'do_uu_tien' => 90,
            ],
            [
                'chu_de' => 'Quy chế đào tạo',
                'danh_muc' => 'Quy định',
                'cau_hoi_mau' => 'GPA là gì?',
                'cau_tra_loi' => "GPA (Grade Point Average) là điểm trung bình tích lũy.\n\n📊 Công thức:\nGPA = Tổng (Điểm môn × Số tín chỉ) / Tổng số tín chỉ\n\n🏆 Xếp loại:\n- Xuất sắc: GPA >= 3.6\n- Giỏi: 3.2 <= GPA < 3.6\n- Khá: 2.5 <= GPA < 3.2\n- Trung bình: 2.0 <= GPA < 2.5\n- Yếu: GPA < 2.0\n\n⚠️ GPA < 1.5 trong 2 học kỳ liên tiếp → Cảnh báo học vụ",
                'tu_khoa' => 'GPA, điểm trung bình, điểm tích lũy, xếp loại',
                'do_uu_tien' => 95,
            ],

            // Thủ tục hành chính
            [
                'chu_de' => 'Thủ tục hành chính',
                'danh_muc' => 'Hướng dẫn',
                'cau_hoi_mau' => 'Xin giấy xác nhận sinh viên ở đâu?',
                'cau_tra_loi' => "Để xin giấy xác nhận sinh viên:\n\n1. 📝 Nộp đơn tại Phòng Đào tạo hoặc qua email\n2. ⏰ Thời gian xử lý: 2-3 ngày làm việc\n3. 🎫 Lấy giấy tại Phòng Đào tạo (mang theo thẻ sinh viên)\n\n📞 Liên hệ:\n- Phòng Đào tạo\n- Email: daotao@smis.edu.vn\n- Hotline: 024.xxxx.xxxx\n\n💡 Một số giấy tờ có thể lấy ngay (theo yêu cầu khẩn cấp)",
                'tu_khoa' => 'giấy xác nhận, xác nhận sinh viên, giấy tờ, thủ tục',
                'do_uu_tien' => 85,
            ],
            [
                'chu_de' => 'Thủ tục hành chính',
                'danh_muc' => 'Hướng dẫn',
                'cau_hoi_mau' => 'Làm lại thẻ sinh viên khi mất?',
                'cau_tra_loi' => "Khi mất thẻ sinh viên:\n\n1. 📝 Viết đơn xin cấp lại thẻ\n2. 🖼️ Chuẩn bị ảnh 3×4 (2 ảnh)\n3. 💰 Đóng phí làm lại thẻ\n4. ⏰ Thời gian: 5-7 ngày làm việc\n\n📍 Nộp đơn tại:\n- Phòng Công tác sinh viên\n- Giờ làm việc: 8h-11h30, 13h30-17h (Thứ 2-6)\n\n📞 Hotline: 024.xxxx.xxxx",
                'tu_khoa' => 'mất thẻ, làm lại thẻ, thẻ sinh viên, cấp lại',
                'do_uu_tien' => 80,
            ],

            // Chương trình đào tạo
            [
                'chu_de' => 'Chương trình đào tạo',
                'danh_muc' => 'Hướng dẫn',
                'cau_hoi_mau' => 'Xem chương trình đào tạo ở đâu?',
                'cau_tra_loi' => "Để xem chương trình đào tạo:\n\n1. 🌐 Website trường → Mục \"Đào tạo\"\n2. 📱 Hệ thống S-MIS → Mục \"Chương trình đào tạo\"\n3. 📧 Liên hệ Phòng Đào tạo\n\n📚 Nội dung bao gồm:\n- Danh sách môn học theo từng học kỳ\n- Số tín chỉ từng môn\n- Môn bắt buộc, môn tự chọn\n- Môn tiên quyết\n- Tổng số tín chỉ cần hoàn thành\n\n💡 Nên xem CTĐT để lập kế hoạch học tập",
                'tu_khoa' => 'chương trình đào tạo, CTĐT, khung chương trình, môn học',
                'do_uu_tien' => 85,
            ],
            [
                'chu_de' => 'Chương trình đào tạo',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Cần bao nhiêu tín chỉ để tốt nghiệp?',
                'cau_tra_loi' => "Số tín chỉ cần tích lũy để tốt nghiệp:\n\n📊 Tùy theo hệ đào tạo:\n- Đại học chính quy: 120-140 tín chỉ (tùy ngành)\n- Liên thông: 60-70 tín chỉ\n- Văn bằng 2: 60-90 tín chỉ\n\n📚 Bao gồm:\n- Kiến thức giáo dục đại cương\n- Kiến thức cơ sở ngành\n- Kiến thức chuyên ngành\n- Thực tập tốt nghiệp/Khóa luận\n\n⚠️ Kiểm tra chính xác trong chương trình đào tạo của ngành bạn học!",
                'tu_khoa' => 'tín chỉ tốt nghiệp, số tín chỉ, tích lũy tín chỉ, hoàn thành chương trình',
                'do_uu_tien' => 90,
            ],
            [
                'chu_de' => 'Chương trình đào tạo',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Môn tiên quyết là gì?',
                'cau_tra_loi' => "Môn tiên quyết (prerequisite) là môn học bắt buộc phải hoàn thành TRƯỚC khi được đăng ký môn học tiếp theo.\n\n📚 Ví dụ:\n- Toán cao cấp 1 là tiên quyết của Toán cao cấp 2\n- Lập trình cơ bản là tiên quyết của Cấu trúc dữ liệu\n\n✅ Điều kiện:\n- Phải ĐẠT điểm môn tiên quyết (≥ 4.0)\n- Mới được đăng ký môn học tiếp theo\n\n💡 Xem môn tiên quyết trong CTĐT hoặc khi đăng ký môn học",
                'tu_khoa' => 'môn tiên quyết, prerequisite, môn học trước, điều kiện học',
                'do_uu_tien' => 88,
            ],
            [
                'chu_de' => 'Chương trình đào tạo',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Khác nhau giữa môn bắt buộc và môn tự chọn?',
                'cau_tra_loi' => "📚 MÔN BẮT BUỘC:\n- Bắt buộc phải học và đạt\n- Không được thay thế bởi môn khác\n- Liên quan trực tiếp đến kiến thức cốt lõi của ngành\n\n🎯 MÔN TỰ CHỌN:\n- Được chọn trong danh sách môn tự chọn của CTĐT\n- Có thể chọn theo sở thích/định hướng nghề nghiệp\n- Phải đủ số tín chỉ tự chọn quy định\n\n💡 Lưu ý:\n- Một số môn tự chọn có môn tiên quyết\n- Nên chọn môn phù hợp với định hướng nghề nghiệp",
                'tu_khoa' => 'môn bắt buộc, môn tự chọn, môn tùy chọn, elective',
                'do_uu_tien' => 85,
            ],
            [
                'chu_de' => 'Chương trình đào tạo',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Có thể học vượt chương trình không?',
                'cau_tra_loi' => "Có thể học vượt nếu đáp ứng điều kiện:\n\n✅ ĐIỀU KIỆN:\n- GPA từ 3.0 trở lên\n- Không có môn nào bị điểm F\n- Đã hoàn thành các môn tiên quyết\n- Lịch học không trùng nhau\n\n⚠️ GIỚI HẠN:\n- Tối đa 24-28 tín chỉ/học kỳ (tùy quy định)\n- Phải đảm bảo chất lượng học tập\n\n📝 THỦ TỤC:\n1. Viết đơn xin học vượt\n2. Nộp cho Phòng Đào tạo\n3. Chờ phê duyệt\n\n💡 Lợi ích: Rút ngắn thời gian học, sớm tốt nghiệp",
                'tu_khoa' => 'học vượt, học trước, vượt chương trình, học nhanh',
                'do_uu_tien' => 82,
            ],
            [
                'chu_de' => 'Chương trình đào tạo',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Chuyên ngành học từ khi nào?',
                'cau_tra_loi' => "Thời gian học chuyên ngành:\n\n📅 THÔNG THƯỜNG:\n- Năm thứ 3 (Học kỳ 5-6)\n- Sau khi hoàn thành kiến thức đại cương và cơ sở ngành\n\n🎯 CHỌN CHUYÊN NGÀNH:\n- Thường được thông báo cuối năm 2\n- Dựa vào: GPA, nguyện vọng, chỉ tiêu\n- Một số ngành có điều kiện riêng\n\n📚 CÁC CHUYÊN NGÀNH (ví dụ):\n- CNTT: Phần mềm, Mạng & An ninh, AI...\n- Kinh tế: Tài chính, Marketing, KT Quốc tế...\n\n💡 Tìm hiểu kỹ về các chuyên ngành để chọn đúng định hướng!",
                'tu_khoa' => 'chuyên ngành, chọn chuyên ngành, định hướng ngành, major',
                'do_uu_tien' => 87,
            ],
            [
                'chu_de' => 'Chương trình đào tạo',
                'danh_muc' => 'FAQ',
                'cau_hoi_mau' => 'Có được chuyển ngành không?',
                'cau_tra_loi' => "Có thể chuyển ngành nếu đáp ứng điều kiện:\n\n✅ ĐIỀU KIỆN:\n- GPA ≥ 3.0\n- Chưa quá năm thứ 2\n- Ngành chuyển đến còn chỉ tiêu\n- Đáp ứng điều kiện đầu vào của ngành mới\n\n📝 THỦ TỤC:\n1. Viết đơn xin chuyển ngành\n2. Nộp bảng điểm\n3. Phỏng vấn (nếu có)\n4. Chờ phê duyệt\n\n⚠️ LÀM RÕ:\n- Thời gian: Thường đầu học kỳ\n- Có thể phải học bù các môn thiếu\n- Thời gian tốt nghiệp có thể kéo dài\n\n📞 Liên hệ Phòng Đào tạo để biết chi tiết!",
                'tu_khoa' => 'chuyển ngành, đổi ngành, chuyển chuyên ngành',
                'do_uu_tien' => 84,
            ],
        ];

        foreach ($knowledgeData as $data) {
            $data['nguoi_tao_id'] = $adminId;
            $data['ngay_cap_nhat'] = now();
            $data['kich_hoat'] = true;
            $data['luot_truy_cap'] = 0;
            $data['huu_ich'] = 0;

            AiChatbotKnowledgeBase::create($data);
        }

        $this->command->info('Đã tạo ' . count($knowledgeData) . ' bản ghi knowledge base cho AI Chatbot!');
    }
}
