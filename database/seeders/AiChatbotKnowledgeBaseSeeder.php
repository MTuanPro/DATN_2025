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
        // Xóa dữ liệu cũ nếu chạy lại
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AiChatbotKnowledgeBase::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $knowledgeData = [
            // =====================================================
            // GIỚI THIỆU HỆ THỐNG S-MIS
            // =====================================================
            [
                'chu_de' => 'he_thong',
                'cau_hoi_mau' => 'S-MIS là gì?',
                'cau_tra_loi' => "**S-MIS (Student Management Information System)** là Hệ thống Quản lý Thông tin Sinh viên.\n\n📌 **Các chức năng chính:**\n\n**👨‍🎓 Dành cho Sinh viên:**\n- Đăng ký học phần\n- Xem thời khóa biểu, lịch thi\n- Xem kết quả học tập, điểm số\n- Tra cứu và thanh toán học phí\n- Nhận thông báo từ nhà trường\n- Tương tác với trợ lý AI chatbot\n\n**👨‍🏫 Dành cho Giảng viên:**\n- Quản lý lớp giảng dạy\n- Điểm danh sinh viên\n- Nhập điểm, quản lý điểm số\n- Xem lịch thi\n\n**🏫 Dành cho Phòng Đào tạo:**\n- Quản lý khoa, ngành, chuyên ngành\n- Quản lý sinh viên, giảng viên\n- Quản lý môn học, lớp học phần\n- Xếp lịch thi, duyệt điểm\n- Cấu hình học phí\n- Gửi thông báo",
                'tu_khoa' => 'S-MIS, hệ thống, giới thiệu, là gì, smis',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // THÔNG TIN CÁC KHOA
            // =====================================================
            [
                'chu_de' => 'khoa',
                'cau_hoi_mau' => 'Trường có những khoa nào?',
                'cau_tra_loi' => "Trường hiện có **3 khoa** đào tạo:\n\n**1. 🖥️ Khoa Công nghệ thông tin (CNTT)**\n- Đào tạo các ngành về phần mềm, mạng máy tính, an toàn thông tin\n- Các ngành: Công nghệ thông tin, Khoa học máy tính, An toàn thông tin\n\n**2. 💼 Khoa Kinh tế (KT)**\n- Đào tạo các ngành về quản trị kinh doanh, tài chính ngân hàng, kế toán\n- Các ngành: Quản trị kinh doanh, Tài chính - Ngân hàng, Kế toán\n\n**3. 🌏 Khoa Ngoại ngữ (NN)**\n- Đào tạo tiếng Anh, tiếng Nhật, tiếng Trung Quốc\n- Các ngành: Ngôn ngữ Anh, Ngôn ngữ Nhật, Ngôn ngữ Trung Quốc",
                'tu_khoa' => 'khoa, danh sách khoa, các khoa, CNTT, kinh tế, ngoại ngữ',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khoa',
                'cau_hoi_mau' => 'Khoa Công nghệ thông tin có gì?',
                'cau_tra_loi' => "**🖥️ Khoa Công nghệ thông tin (CNTT)**\n\n**📚 Các ngành đào tạo:**\n1. **Công nghệ thông tin** (Mã: 7480201)\n   - Chuyên ngành: Công nghệ phần mềm, Phát triển ứng dụng di động\n\n2. **Khoa học máy tính** (Mã: 7480202)\n   - Chuyên ngành: Trí tuệ nhân tạo (AI-ML), Khoa học dữ liệu (DS)\n\n3. **An toàn thông tin** (Mã: 7480299)\n   - Chuyên ngành: Bảo mật hệ thống, Mật mã học\n\n**🎓 Các môn học nổi bật:**\n- Nhập môn lập trình, Cấu trúc dữ liệu\n- Lập trình hướng đối tượng, Cơ sở dữ liệu\n- Phát triển ứng dụng Web, Mobile\n- Trí tuệ nhân tạo, Machine Learning\n- An toàn bảo mật, Cloud Computing, Blockchain",
                'tu_khoa' => 'khoa CNTT, công nghệ thông tin, ngành CNTT, lập trình',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khoa',
                'cau_hoi_mau' => 'Khoa Kinh tế đào tạo những gì?',
                'cau_tra_loi' => "**💼 Khoa Kinh tế (KT)**\n\n**📚 Các ngành đào tạo:**\n1. **Quản trị kinh doanh** (Mã: 7340101)\n   - Chuyên ngành: Quản trị doanh nghiệp, Quản trị Marketing\n\n2. **Tài chính - Ngân hàng** (Mã: 7340201)\n   - Chuyên ngành: Tài chính doanh nghiệp, Ngân hàng\n\n3. **Kế toán** (Mã: 7340301)\n   - Chuyên ngành: Kế toán doanh nghiệp, Kiểm toán\n\n**🎓 Các môn học nổi bật:**\n- Kinh tế vi mô, Kinh tế vĩ mô\n- Quản trị học, Marketing căn bản\n- Tài chính doanh nghiệp, Ngân hàng thương mại\n- Kế toán tài chính, Kiểm toán\n- Marketing số, Thương mại điện tử",
                'tu_khoa' => 'khoa kinh tế, quản trị, tài chính, ngân hàng, kế toán',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khoa',
                'cau_hoi_mau' => 'Khoa Ngoại ngữ có những ngành nào?',
                'cau_tra_loi' => "**🌏 Khoa Ngoại ngữ (NN)**\n\n**📚 Các ngành đào tạo:**\n1. **Ngôn ngữ Anh** (Mã: 7220201)\n   - Chuyên ngành: Sư phạm tiếng Anh, Biên - Phiên dịch tiếng Anh\n\n2. **Ngôn ngữ Nhật** (Mã: 7220203)\n   - Chuyên ngành: Sư phạm tiếng Nhật, Biên - Phiên dịch tiếng Nhật\n\n3. **Ngôn ngữ Trung Quốc** (Mã: 7220204)\n   - Chuyên ngành: Sư phạm tiếng Trung, Biên - Phiên dịch tiếng Trung\n\n**🎓 Các môn học nổi bật:**\n- Ngữ âm, Ngữ pháp\n- Kỹ năng nghe - nói, đọc - viết\n- Phương pháp giảng dạy ngoại ngữ\n- Kỹ thuật biên dịch, phiên dịch\n- Văn hóa và giao tiếp",
                'tu_khoa' => 'khoa ngoại ngữ, tiếng Anh, tiếng Nhật, tiếng Trung, ngôn ngữ',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // THÔNG TIN KHÓA HỌC
            // =====================================================
            [
                'chu_de' => 'khoa_hoc',
                'cau_hoi_mau' => 'Trường có những khóa học nào?',
                'cau_tra_loi' => "**📅 Các khóa học đang đào tạo:**\n\n| Khóa | Năm nhập học | Năm tốt nghiệp |\n|------|--------------|----------------|\n| K19 | 2019 | 2023 |\n| K20 | 2020 | 2024 |\n| K21 | 2021 | 2025 |\n| K22 | 2022 | 2026 |\n| K23 | 2023 | 2027 |\n| K24 | 2024 | 2028 |\n| K25 | 2025 | 2029 |\n\n📌 **Thời gian đào tạo:** 4 năm\n📌 **Hệ đào tạo:** Đại học chính quy theo tín chỉ",
                'tu_khoa' => 'khóa học, K19, K20, K21, K22, K23, K24, K25, năm nhập học',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // CA HỌC VÀ THỜI GIAN
            // =====================================================
            [
                'chu_de' => 'ca_hoc',
                'cau_hoi_mau' => 'Lịch ca học như thế nào?',
                'cau_tra_loi' => "**⏰ Lịch ca học trong ngày:**\n\n**🌅 Buổi sáng:**\n| Ca | Thời gian | Tiết |\n|-----|-----------|------|\n| Ca 1 | 07:00 - 08:50 | Tiết 1-2 |\n| Ca 2 | 09:00 - 10:50 | Tiết 3-4 |\n| Ca 3 | 11:00 - 12:50 | Tiết 5-6 |\n\n**🌇 Buổi chiều:**\n| Ca | Thời gian | Tiết |\n|-----|-----------|------|\n| Ca 4 | 13:00 - 14:50 | Tiết 7-8 |\n| Ca 5 | 15:00 - 16:50 | Tiết 9-10 |\n| Ca 6 | 17:00 - 18:50 | Tiết 11-12 |\n\n📌 Mỗi ca học kéo dài **1 giờ 50 phút** (2 tiết)",
                'tu_khoa' => 'ca học, giờ học, thời gian học, tiết học, lịch học',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // HỌC KỲ
            // =====================================================
            [
                'chu_de' => 'hoc_ky',
                'cau_hoi_mau' => 'Một năm có mấy học kỳ?',
                'cau_tra_loi' => "**📅 Cấu trúc năm học:**\n\nMỗi năm học có **2 học kỳ chính** và **1 học kỳ hè** (tùy chọn):\n\n**Học kỳ 1:**\n- Thời gian: Tháng 9 → Tháng 1 năm sau\n- Đăng ký: Cuối tháng 8\n\n**Học kỳ 2:**\n- Thời gian: Tháng 2 → Tháng 6\n- Đăng ký: Cuối tháng 1\n\n**Học kỳ hè (tùy chọn):**\n- Thời gian: Tháng 7 → Tháng 8\n- Dành cho sinh viên học cải thiện hoặc học vượt",
                'tu_khoa' => 'học kỳ, semester, năm học, học kì 1, học kì 2, học kỳ hè',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // TRẠNG THÁI HỌC TẬP
            // =====================================================
            [
                'chu_de' => 'trang_thai',
                'cau_hoi_mau' => 'Các trạng thái học tập của sinh viên?',
                'cau_tra_loi' => "**📋 Trạng thái học tập của sinh viên:**\n\n1. **🟢 Đang học** - Sinh viên đang theo học bình thường\n\n2. **🟡 Bảo lưu** - Tạm dừng học có thời hạn (tối đa 2 năm)\n   - Lý do: Bệnh, hoàn cảnh gia đình, nghĩa vụ quân sự...\n\n3. **🔴 Thôi học** - Không còn theo học tại trường\n   - Lý do: Tự nguyện, buộc thôi học, vi phạm quy chế...\n\n4. **🎓 Tốt nghiệp** - Đã hoàn thành chương trình đào tạo",
                'tu_khoa' => 'trạng thái, đang học, bảo lưu, thôi học, tốt nghiệp',
                'do_uu_tien' => 75,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // TRÌNH ĐỘ ĐÀO TẠO
            // =====================================================
            [
                'chu_de' => 'trinh_do',
                'cau_hoi_mau' => 'Trường đào tạo những trình độ nào?',
                'cau_tra_loi' => "**🎓 Các trình độ đào tạo:**\n\n1. **Cao đẳng** - 3 năm\n2. **Đại học** - 4 năm\n3. **Thạc sĩ** - 2 năm (sau đại học)\n4. **Tiến sĩ** - 3-4 năm (sau thạc sĩ)\n\n📌 Hệ thống S-MIS hiện quản lý chủ yếu bậc **Đại học**",
                'tu_khoa' => 'trình độ, cao đẳng, đại học, thạc sĩ, tiến sĩ',
                'do_uu_tien' => 70,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // HỌC PHÍ
            // =====================================================
            [
                'chu_de' => 'hoc_phi',
                'cau_hoi_mau' => 'Học phí một tín chỉ là bao nhiêu?',
                'cau_tra_loi' => "**💰 Cấu hình học phí theo năm học:**\n\n| Năm học | Học phí/tín chỉ | Phí dịch vụ |\n|---------|-----------------|-------------|\n| 2023-2024 | 350.000đ | 500.000đ |\n| 2024-2025 | 380.000đ | 550.000đ |\n| **2025-2026** | **400.000đ** | **600.000đ** |\n| 2026-2027 | 420.000đ (dự kiến) | 650.000đ |\n\n📌 **Công thức tính:**\n`Học phí = Số tín chỉ × Đơn giá + Phí dịch vụ`\n\n📌 **Ví dụ:** 20 tín chỉ năm 2025-2026:\n`20 × 400.000 + 600.000 = 8.600.000đ`",
                'tu_khoa' => 'học phí, tín chỉ, giá tín chỉ, phí dịch vụ, hoc phi',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'hoc_phi',
                'cau_hoi_mau' => 'Làm sao để kiểm tra học phí?',
                'cau_tra_loi' => "**📋 Cách kiểm tra học phí:**\n\n1. Đăng nhập vào hệ thống S-MIS\n2. Vào menu **\"Học phí\"**\n3. Xem chi tiết theo từng học kỳ\n\n**Hệ thống hiển thị:**\n- 💵 Tổng học phí phải đóng\n- ✅ Số tiền đã đóng\n- ⏳ Số tiền còn thiếu\n- 📅 Hạn đóng học phí\n- 📝 Lịch sử thanh toán\n\n⚠️ **Lưu ý:** Nếu không đóng học phí đúng hạn, bạn có thể bị:\n- Khóa tài khoản đăng ký học phần\n- Không được xem điểm\n- Không được dự thi",
                'tu_khoa' => 'kiểm tra học phí, xem học phí, tra cứu học phí',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'hoc_phi',
                'cau_hoi_mau' => 'Nộp học phí ở đâu?',
                'cau_tra_loi' => "**💳 Các hình thức nộp học phí:**\n\n**1. Chuyển khoản ngân hàng:**\n- Tên TK: Trường Đại học ABC\n- Số TK: 1234567890\n- Ngân hàng: Vietcombank\n- Nội dung: `MaSV_HoTen_HocPhiHK`\n\n**2. Cổng thanh toán online:**\n- Đăng nhập S-MIS → Học phí → Thanh toán\n- Hỗ trợ: VNPAY, MoMo, ZaloPay\n\n**3. Nộp trực tiếp:**\n- Phòng Kế toán (Tòa A, tầng 1)\n- Giờ làm việc: 8h-17h (Thứ 2-6)\n\n✅ **Khuyến nghị:** Sử dụng chuyển khoản hoặc cổng online để tiết kiệm thời gian!",
                'tu_khoa' => 'nộp học phí, đóng học phí, thanh toán, chuyển khoản',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // ĐĂNG KÝ HỌC PHẦN
            // =====================================================
            [
                'chu_de' => 'dang_ky',
                'cau_hoi_mau' => 'Làm thế nào để đăng ký học phần?',
                'cau_tra_loi' => "**📝 Hướng dẫn đăng ký học phần:**\n\n**Bước 1:** Đăng nhập S-MIS\n**Bước 2:** Vào menu **\"Đăng ký học phần\"**\n**Bước 3:** Chọn học kỳ cần đăng ký\n**Bước 4:** Chọn các môn học/lớp học phần\n**Bước 5:** Kiểm tra lịch học và xác nhận\n\n⚠️ **Lưu ý quan trọng:**\n- ✅ Chỉ đăng ký trong thời gian mở đăng ký\n- ✅ Phải hoàn thành môn tiên quyết (nếu có)\n- ✅ Không được trùng lịch học\n- ✅ Tối đa **25 tín chỉ/học kỳ**\n- ✅ Phải đóng đủ học phí kỳ trước",
                'tu_khoa' => 'đăng ký, đăng ký môn học, đăng ký học phần, dang ky',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'dang_ky',
                'cau_hoi_mau' => 'Khi nào mở đăng ký học phần?',
                'cau_tra_loi' => "**📅 Thời gian đăng ký học phần:**\n\n| Học kỳ | Thời gian đăng ký |\n|--------|-------------------|\n| Học kỳ 1 | Cuối tháng 8 (trước khai giảng) |\n| Học kỳ 2 | Cuối tháng 1 - đầu tháng 2 |\n| Học kỳ hè | Cuối tháng 5 - đầu tháng 6 |\n\n**📌 Quy trình:**\n1. **Đợt 1:** Đăng ký chính thức (3-5 ngày)\n2. **Đợt 2:** Điều chỉnh đăng ký (2-3 ngày)\n3. **Đợt 3:** Đăng ký muộn (nếu còn chỗ)\n\n💡 **Tip:** Theo dõi thông báo trên hệ thống để biết thời gian chính xác!",
                'tu_khoa' => 'thời gian đăng ký, khi nào đăng ký, mở đăng ký',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'dang_ky',
                'cau_hoi_mau' => 'Môn tiên quyết là gì?',
                'cau_tra_loi' => "**📚 Môn tiên quyết (Prerequisite):**\n\nLà môn học bạn **PHẢI hoàn thành** trước khi đăng ký môn khác.\n\n**Ví dụ:**\n- Muốn học **Cấu trúc dữ liệu** → Phải qua **Nhập môn lập trình**\n- Muốn học **Machine Learning** → Phải qua **Trí tuệ nhân tạo**\n- Muốn học **Phát triển Web** → Phải qua **OOP** và **CSDL**\n\n**🔍 Cách kiểm tra môn tiên quyết:**\n1. Vào menu **Đăng ký học phần**\n2. Xem chi tiết môn học\n3. Phần **\"Môn tiên quyết\"** sẽ hiển thị\n\n⚠️ Nếu chưa đạt môn tiên quyết, hệ thống sẽ **không cho phép** đăng ký!",
                'tu_khoa' => 'môn tiên quyết, prerequisite, điều kiện tiên quyết',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'dang_ky',
                'cau_hoi_mau' => 'Hủy đăng ký học phần như thế nào?',
                'cau_tra_loi' => "**❌ Cách hủy đăng ký học phần:**\n\n**Trong thời gian điều chỉnh:**\n1. Vào **\"Đăng ký học phần\"**\n2. Chọn môn cần hủy\n3. Click **\"Hủy đăng ký\"**\n4. Xác nhận hủy\n\n**Sau thời gian điều chỉnh:**\n- Phải làm đơn gửi Phòng Đào tạo\n- Có thể mất phí (tùy thời điểm)\n- Môn học sẽ tính điểm W (Withdrawn)\n\n⚠️ **Lưu ý:**\n- Hủy môn sẽ ảnh hưởng tiến độ học tập\n- Học phí đã đóng có thể không được hoàn",
                'tu_khoa' => 'hủy đăng ký, hủy môn, xóa môn học, huy dang ky',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // MÔN HỌC
            // =====================================================
            [
                'chu_de' => 'mon_hoc',
                'cau_hoi_mau' => 'Có những loại môn học nào?',
                'cau_tra_loi' => "**📚 Phân loại môn học:**\n\n**1. Môn đại cương** (bắt buộc)\n- Triết học Mác-Lênin, Kinh tế chính trị\n- Tư tưởng Hồ Chí Minh\n- Tiếng Anh 1, 2\n- Toán cao cấp\n- Giáo dục thể chất, Quốc phòng\n\n**2. Môn cơ sở ngành** (bắt buộc)\n- Nền tảng chuyên môn của từng ngành\n\n**3. Môn chuyên ngành bắt buộc**\n- Kiến thức cốt lõi của chuyên ngành\n\n**4. Môn chuyên ngành tự chọn**\n- Sinh viên lựa chọn theo sở thích\n\n**5. Thực tập** (4-6 tín chỉ)\n- Thực tập tại doanh nghiệp\n\n**6. Đồ án/Khóa luận tốt nghiệp** (10 tín chỉ)",
                'tu_khoa' => 'loại môn học, đại cương, cơ sở ngành, chuyên ngành, tự chọn',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'mon_hoc',
                'cau_hoi_mau' => 'Khoa CNTT có những môn học gì?',
                'cau_tra_loi' => "**🖥️ Danh sách môn học Khoa CNTT:**\n\n**Cơ sở ngành:**\n- CNTT01: Nhập môn lập trình (3TC)\n- CNTT02: Cấu trúc dữ liệu và giải thuật (3TC)\n- CNTT03: Lập trình hướng đối tượng (3TC)\n- CNTT04: Cơ sở dữ liệu (3TC)\n- CNTT05: Mạng máy tính (3TC)\n\n**Chuyên ngành:**\n- CNTT06: Công nghệ phần mềm (3TC)\n- CNTT07: Phát triển ứng dụng Web (4TC)\n- CNTT08: Lập trình di động (4TC)\n- CNTT09: Trí tuệ nhân tạo (3TC)\n- CNTT10: Machine Learning (3TC)\n- CNTT11: Khoa học dữ liệu (3TC)\n- CNTT12: An toàn bảo mật (3TC)\n- CNTT15: Cloud Computing (3TC)\n- CNTT16: Blockchain (3TC)\n\n**Tốt nghiệp:**\n- CNTT17: Thực tập (4TC)\n- CNTT18: Đồ án tốt nghiệp (10TC)",
                'tu_khoa' => 'môn CNTT, lập trình, web, mobile, AI, machine learning',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // KẾT QUẢ HỌC TẬP - ĐIỂM
            // =====================================================
            [
                'chu_de' => 'diem',
                'cau_hoi_mau' => 'Xem điểm ở đâu?',
                'cau_tra_loi' => "**📊 Cách xem điểm:**\n\n1. Đăng nhập S-MIS\n2. Vào menu **\"Kết quả học tập\"** hoặc **\"Điểm\"**\n3. Chọn học kỳ cần xem\n\n**Hệ thống hiển thị:**\n- 📝 Điểm chuyên cần\n- 📝 Điểm giữa kỳ\n- 📝 Điểm cuối kỳ\n- 📊 Điểm tổng kết môn (thang 10 và thang 4)\n- 🎯 GPA học kỳ\n- 🎯 GPA tích lũy\n\n⏰ **Thời gian công bố:** Thường sau **2 tuần** kể từ ngày thi cuối kỳ",
                'tu_khoa' => 'xem điểm, điểm thi, kết quả học tập, diem thi',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'diem',
                'cau_hoi_mau' => 'GPA là gì? Cách tính GPA?',
                'cau_tra_loi' => "**📊 GPA (Grade Point Average) - Điểm trung bình tích lũy**\n\n**Công thức:**\n```\nGPA = Σ(Điểm môn × Tín chỉ) / Σ(Tín chỉ)\n```\n\n**Thang điểm 4:**\n| Điểm 10 | Điểm chữ | Điểm 4 |\n|---------|----------|--------|\n| 9.0-10 | A+ | 4.0 |\n| 8.5-8.9 | A | 3.7 |\n| 8.0-8.4 | B+ | 3.5 |\n| 7.0-7.9 | B | 3.0 |\n| 6.5-6.9 | C+ | 2.5 |\n| 5.5-6.4 | C | 2.0 |\n| 5.0-5.4 | D+ | 1.5 |\n| 4.0-4.9 | D | 1.0 |\n| <4.0 | F | 0 |\n\n**Xếp loại học lực:**\n- 🏆 Xuất sắc: GPA ≥ 3.6\n- 🥇 Giỏi: 3.2 ≤ GPA < 3.6\n- 🥈 Khá: 2.5 ≤ GPA < 3.2\n- 🥉 Trung bình: 2.0 ≤ GPA < 2.5\n- ⚠️ Yếu: GPA < 2.0",
                'tu_khoa' => 'GPA, điểm trung bình, tính GPA, thang điểm 4, xếp loại',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'diem',
                'cau_hoi_mau' => 'Cấu trúc điểm một môn học như thế nào?',
                'cau_tra_loi' => "**📊 Cấu trúc điểm môn học (thông thường):**\n\n| Thành phần | Tỷ lệ | Ghi chú |\n|------------|-------|---------||\n| Chuyên cần | 10% | Điểm danh, tham gia lớp |\n| Bài tập/Thảo luận | 10% | Assignment, thuyết trình |\n| Giữa kỳ | 20% | Kiểm tra giữa kỳ |\n| Cuối kỳ | 60% | Thi cuối kỳ |\n\n**📌 Lưu ý:**\n- Cấu trúc điểm có thể **khác nhau** tùy môn học\n- Giảng viên sẽ **thông báo** đầu học kỳ\n- Xem chi tiết trong **Đề cương môn học**\n\n**❌ Điều kiện cấm thi:**\n- Điểm chuyên cần < 80% (nghỉ quá 20% số buổi)\n- Chưa nộp bài tập/đồ án bắt buộc",
                'tu_khoa' => 'cấu trúc điểm, tỷ lệ điểm, chuyên cần, giữa kỳ, cuối kỳ',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // LỊCH THI
            // =====================================================
            [
                'chu_de' => 'lich_thi',
                'cau_hoi_mau' => 'Xem lịch thi ở đâu?',
                'cau_tra_loi' => "**📅 Cách xem lịch thi:**\n\n1. Đăng nhập S-MIS\n2. Vào menu **\"Lịch thi\"** hoặc **\"TKB & Lịch thi\"**\n3. Chọn học kỳ cần xem\n\n**Thông tin hiển thị:**\n- 📚 Môn thi\n- 📅 Ngày thi, giờ thi\n- 🏫 Phòng thi\n- 🔢 Số báo danh\n- 📝 Hình thức thi (Tự luận/Trắc nghiệm/Vấn đáp)\n\n⚠️ **Lưu ý quan trọng:**\n- Đến phòng thi trước **15 phút**\n- Mang theo **thẻ sinh viên**\n- Mang theo **giấy báo thi** (nếu có)\n- Không mang tài liệu nếu không được phép",
                'tu_khoa' => 'lịch thi, xem lịch thi, ngày thi, phòng thi',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'lich_thi',
                'cau_hoi_mau' => 'Thi lại thì làm thế nào?',
                'cau_tra_loi' => "**🔄 Quy trình thi lại:**\n\n**Điều kiện thi lại:**\n- Điểm cuối kỳ < 4.0 (F)\n- Vắng thi có lý do chính đáng\n- Điểm tổng kết < 4.0\n\n**Các bước:**\n1. Đăng ký thi lại trong đợt đăng ký\n2. Nộp lệ phí thi lại: **100.000đ/môn**\n3. Xem lịch thi lại trên hệ thống\n4. Tham gia thi đúng lịch\n\n**⚠️ Lưu ý:**\n- Tối đa **2 lần** thi lại cho mỗi môn\n- Nếu vẫn F → Phải **học lại** môn đó\n- Điểm thi lại được ghi nhận vào bảng điểm",
                'tu_khoa' => 'thi lại, học lại, điểm F, thi lai',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // THỜI KHÓA BIỂU
            // =====================================================
            [
                'chu_de' => 'thoi_khoa_bieu',
                'cau_hoi_mau' => 'Xem thời khóa biểu ở đâu?',
                'cau_tra_loi' => "**📅 Cách xem thời khóa biểu:**\n\n1. Đăng nhập S-MIS\n2. Vào menu **\"TKB & Lịch thi\"** hoặc **\"Thời khóa biểu\"**\n3. Chọn tuần cần xem\n\n**Thông tin hiển thị:**\n- 📚 Môn học theo từng ngày\n- ⏰ Ca học (tiết mấy)\n- 🏫 Phòng học\n- 👨‍🏫 Giảng viên\n- 📝 Ghi chú (nếu có thay đổi)\n\n**Trạng thái buổi học:**\n- 🟢 Bình thường\n- 🟡 Đổi lịch/phòng\n- 🔴 Nghỉ học\n\n💡 **Tip:** Kiểm tra TKB **hàng tuần** vì có thể có thay đổi!",
                'tu_khoa' => 'thời khóa biểu, TKB, lịch học, xem TKB',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // ĐIỂM DANH
            // =====================================================
            [
                'chu_de' => 'diem_danh',
                'cau_hoi_mau' => 'Điểm danh như thế nào?',
                'cau_tra_loi' => "**✅ Về điểm danh:**\n\n**Cách thức điểm danh:**\n- Giảng viên điểm danh đầu/cuối buổi học\n- Có thể điểm danh qua app hoặc gọi tên\n\n**Trạng thái điểm danh:**\n- ✅ Có mặt (Present)\n- ❌ Vắng không phép (Absent)\n- 🟡 Vắng có phép (Excused)\n- ⏰ Đi muộn (Late)\n\n**⚠️ Quy định:**\n- Vắng > 20% số buổi → **Cấm thi**\n- Điểm chuyên cần ảnh hưởng 10% điểm tổng kết\n\n**🔄 Xin điểm danh bù:**\n- Nếu vắng có lý do chính đáng\n- Nộp đơn kèm giấy tờ minh chứng\n- Gửi qua hệ thống hoặc Phòng Đào tạo",
                'tu_khoa' => 'điểm danh, vắng học, cấm thi, điểm chuyên cần',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // CẢNH BÁO HỌC VỤ
            // =====================================================
            [
                'chu_de' => 'canh_bao',
                'cau_hoi_mau' => 'Cảnh báo học vụ là gì?',
                'cau_tra_loi' => "**⚠️ Cảnh báo học vụ:**\n\nLà thông báo khi kết quả học tập của sinh viên **không đạt yêu cầu**.\n\n**Điều kiện bị cảnh báo:**\n- GPA học kỳ < 1.0\n- GPA tích lũy < 1.5 (từ học kỳ 3 trở đi)\n- Số tín chỉ tích lũy không đủ theo tiến độ\n\n**Hậu quả:**\n- Lần 1: Cảnh báo\n- Lần 2: Cảnh báo nặng\n- Lần 3: Xem xét buộc thôi học\n\n**📌 Cách thoát cảnh báo:**\n- Cải thiện GPA học kỳ tiếp theo ≥ 2.0\n- Đăng ký học cải thiện các môn điểm thấp\n- Tham khảo tư vấn học tập từ cố vấn",
                'tu_khoa' => 'cảnh báo học vụ, buộc thôi học, GPA thấp, cảnh báo',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // THÔNG TIN LIÊN HỆ
            // =====================================================
            [
                'chu_de' => 'lien_he',
                'cau_hoi_mau' => 'Liên hệ phòng Đào tạo như thế nào?',
                'cau_tra_loi' => "**📞 Thông tin liên hệ Phòng Đào tạo:**\n\n📍 **Địa chỉ:** Tòa nhà B, tầng 2, phòng 201\n📞 **Hotline:** 024.xxxx.xxxx\n📧 **Email:** daotao@smis.edu.vn\n\n🕐 **Giờ làm việc:**\n- Thứ 2 - Thứ 6: 8h00 - 17h00\n- Thứ 7: 8h00 - 12h00\n- Chủ nhật: Nghỉ\n\n**📋 Các vấn đề hỗ trợ:**\n- Đăng ký học phần\n- Chuyển ngành, chuyển khoa\n- Bảo lưu, thôi học\n- Xác nhận sinh viên\n- Điểm số, học vụ",
                'tu_khoa' => 'liên hệ, phòng đào tạo, hotline, email, địa chỉ',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'lien_he',
                'cau_hoi_mau' => 'Quên mật khẩu thì làm sao?',
                'cau_tra_loi' => "**🔐 Cách lấy lại mật khẩu:**\n\n**Cách 1: Tự động qua email**\n1. Vào trang đăng nhập S-MIS\n2. Click **\"Quên mật khẩu\"**\n3. Nhập email đã đăng ký\n4. Nhận link reset qua email\n5. Đặt mật khẩu mới\n\n**Cách 2: Liên hệ IT Support**\n- 📧 Email: itsupport@smis.edu.vn\n- 📞 Hotline: 024.xxxx.xxxx (máy lẻ 102)\n- 📍 Phòng IT: Tòa C, tầng 1\n- Mang theo **thẻ sinh viên** để xác minh\n\n⚠️ **Lưu ý:** Không chia sẻ mật khẩu cho người khác!",
                'tu_khoa' => 'quên mật khẩu, reset password, đổi mật khẩu, lấy lại mật khẩu',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // CHÀO HỎI VÀ TRỢ GIÚP
            // =====================================================
            [
                'chu_de' => 'chao_hoi',
                'cau_hoi_mau' => 'Xin chào',
                'cau_tra_loi' => "Xin chào! 👋\n\nTôi là **Trợ lý ảo S-MIS**, sẵn sàng hỗ trợ bạn 24/7.\n\n**📌 Tôi có thể giúp bạn về:**\n\n✅ **Đăng ký học phần** - cách đăng ký, môn tiên quyết\n✅ **Học phí** - tra cứu, thanh toán, công nợ\n✅ **Kết quả học tập** - xem điểm, GPA, cảnh báo\n✅ **Lịch học & Lịch thi** - TKB, phòng thi\n✅ **Thông tin chung** - liên hệ, thủ tục\n\n💬 Hãy đặt câu hỏi của bạn nhé!",
                'tu_khoa' => 'xin chào, chào, hello, hi, hey, bot',
                'do_uu_tien' => 100,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'chao_hoi',
                'cau_hoi_mau' => 'Cảm ơn',
                'cau_tra_loi' => "Không có gì! 😊\n\nRất vui được hỗ trợ bạn. Nếu có câu hỏi khác, đừng ngần ngại hỏi tôi nhé!\n\n💡 **Một số câu hỏi phổ biến:**\n- Xem điểm ở đâu?\n- Khi nào đăng ký học phần?\n- Học phí bao nhiêu?\n- Xem lịch thi như thế nào?",
                'tu_khoa' => 'cảm ơn, thank, thanks, cám ơn',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'chao_hoi',
                'cau_hoi_mau' => 'Bạn có thể giúp gì?',
                'cau_tra_loi' => "**🤖 Tôi có thể hỗ trợ bạn:**\n\n**1. 📚 Học tập:**\n- Đăng ký học phần, môn tiên quyết\n- Xem điểm, GPA, kết quả học tập\n- Thông tin môn học, chương trình đào tạo\n\n**2. 📅 Lịch trình:**\n- Thời khóa biểu\n- Lịch thi\n- Ca học\n\n**3. 💰 Tài chính:**\n- Học phí, cách tính\n- Hình thức thanh toán\n- Tra cứu công nợ\n\n**4. ℹ️ Thông tin chung:**\n- Các khoa, ngành, chuyên ngành\n- Liên hệ phòng ban\n- Thủ tục hành chính\n\n💬 Hãy hỏi tôi bất cứ điều gì!",
                'tu_khoa' => 'giúp gì, hỗ trợ, help, trợ giúp',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'chao_hoi',
                'cau_hoi_mau' => 'Tạm biệt',
                'cau_tra_loi' => "Tạm biệt bạn! 👋\n\nChúc bạn học tập tốt! 📚✨\n\nNếu cần hỗ trợ, hãy quay lại bất cứ lúc nào nhé. Trợ lý ảo S-MIS luôn sẵn sàng! 🤖",
                'tu_khoa' => 'tạm biệt, bye, goodbye, chào tạm biệt',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // HỌC BỔNG
            // =====================================================
            [
                'chu_de' => 'hoc_bong',
                'cau_hoi_mau' => 'Có những loại học bổng nào?',
                'cau_tra_loi' => "**🏆 Các loại học bổng:**\n\n**1. Học bổng khuyến khích học tập:**\n- 🥇 Xuất sắc (GPA ≥ 3.6): 3.000.000đ/học kỳ\n- 🥈 Giỏi (GPA 3.2-3.59): 2.000.000đ/học kỳ\n- 🥉 Khá (GPA 2.8-3.19): 1.000.000đ/học kỳ\n\n**2. Học bổng tài năng:**\n- Sinh viên đạt giải nghiên cứu khoa học\n- Giải thưởng Olympic, cuộc thi chuyên môn\n- Mức: 2.000.000đ - 10.000.000đ\n\n**3. Học bổng xã hội:**\n- Hoàn cảnh khó khăn\n- Mức: 500.000đ - 2.000.000đ/học kỳ\n\n**4. Học bổng doanh nghiệp:**\n- Tài trợ từ đối tác, doanh nghiệp\n- Mức: Tùy nhà tài trợ\n\n📝 **Đăng ký:** Qua hệ thống S-MIS trong tuần đầu mỗi học kỳ",
                'tu_khoa' => 'học bổng, scholarship, học bổng khuyến khích, học bổng xã hội',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'hoc_bong',
                'cau_hoi_mau' => 'Điều kiện nhận học bổng?',
                'cau_tra_loi' => "**📋 Điều kiện nhận học bổng:**\n\n**Học bổng học tập:**\n✅ GPA học kỳ đạt yêu cầu (≥2.8)\n✅ Không có môn F trong học kỳ\n✅ Không vi phạm kỷ luật\n✅ Điểm rèn luyện ≥ 65/100\n\n**Học bổng xã hội:**\n✅ Hộ nghèo, cận nghèo (có giấy xác nhận)\n✅ Hoàn cảnh khó khăn đặc biệt\n✅ GPA tích lũy ≥ 2.0\n✅ Điểm rèn luyện ≥ 50/100\n\n**Học bổng tài năng:**\n✅ Có giải thưởng, thành tích xuất sắc\n✅ Giấy tờ chứng minh (bằng khen, giấy chứng nhận)\n\n⏰ **Thời gian:** Kết quả công bố sau 2 tuần từ khi đóng đăng ký",
                'tu_khoa' => 'điều kiện học bổng, xét học bổng, yêu cầu học bổng',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // CHUYỂN NGÀNH, CHUYỂN TRƯỜNG
            // =====================================================
            [
                'chu_de' => 'chuyen_nganh',
                'cau_hoi_mau' => 'Làm sao để chuyển ngành?',
                'cau_tra_loi' => "**🔄 Quy trình chuyển ngành:**\n\n**Điều kiện:**\n✅ Đã học ít nhất 1 năm (2 học kỳ)\n✅ GPA tích lũy ≥ 2.5\n✅ Không bị kỷ luật\n✅ Ngành nhận còn chỉ tiêu\n\n**Các bước:**\n1. Nộp đơn xin chuyển ngành (qua S-MIS)\n2. Nộp bảng điểm, giấy tờ liên quan\n3. Chờ xét duyệt (1-2 tuần)\n4. Thi/phỏng vấn (nếu ngành yêu cầu)\n5. Nhận kết quả và làm thủ tục chuyển\n\n⚠️ **Lưu ý:**\n- Phí chuyển ngành: 500.000đ\n- Có thể mất 1 số tín chỉ đã học\n- Cần học bổ sung môn cơ sở ngành mới\n\n📅 **Thời gian:** Đợt 1 (tháng 5-6), Đợt 2 (tháng 11-12)",
                'tu_khoa' => 'chuyển ngành, đổi ngành, transfer major',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'chuyen_nganh',
                'cau_hoi_mau' => 'Chuyển trường thì phải làm gì?',
                'cau_tra_loi' => "**🏫 Quy trình chuyển trường:**\n\n**A. Chuyển ĐI (từ trường này sang trường khác):**\n1. Nộp đơn xin chuyển trường\n2. Nhận giấy chuyển trường\n3. Nhận bảng điểm, hồ sơ học tập\n4. Làm thủ tục tại trường nhận\n\n**B. Chuyển ĐẾN (từ trường khác về):**\n1. Có giấy chuyển từ trường cũ\n2. Nộp hồ sơ, bảng điểm\n3. Xét tuyển chuyển trường\n4. Làm thủ tục nhập học\n\n**📋 Hồ sơ cần:**\n- Đơn xin chuyển trường\n- Bảng điểm đã học\n- Giấy chứng nhận tốt nghiệp THPT\n- Giấy khai sinh\n- Ảnh 3×4 (4 tấm)\n\n💰 **Phí:** 1.000.000đ - 2.000.000đ\n📅 **Thời gian xử lý:** 2-4 tuần",
                'tu_khoa' => 'chuyển trường, transfer school, đổi trường',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // XÁC NHẬN SINH VIÊN
            // =====================================================
            [
                'chu_de' => 'xac_nhan',
                'cau_hoi_mau' => 'Làm giấy xác nhận sinh viên ở đâu?',
                'cau_tra_loi' => "**📄 Giấy xác nhận sinh viên:**\n\n**Cách làm:**\n1. Đăng nhập S-MIS\n2. Vào **\"Hồ sơ\" → \"Xác nhận sinh viên\"**\n3. Chọn loại xác nhận cần\n4. Điền thông tin, nộp phí online\n5. Nhận giấy sau 2-3 ngày làm việc\n\n**Các loại xác nhận:**\n- ✅ Xác nhận đang là sinh viên\n- ✅ Xác nhận học lực, hạnh kiểm\n- ✅ Xác nhận vay vốn ngân hàng\n- ✅ Xác nhận xin visa du học\n- ✅ Bảng điểm tích lũy có dịch\n\n**💰 Lệ phí:**\n- Tiếng Việt: 20.000đ/bản\n- Tiếng Anh: 50.000đ/bản\n- Có công chứng: +100.000đ\n\n**📍 Nhận giấy:**\n- Phòng Đào tạo (tầng 2, tòa B)\n- Hoặc gửi qua đường bưu điện (phí 30.000đ)",
                'tu_khoa' => 'xác nhận sinh viên, giấy xác nhận, confirm student',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // BẢO VỆ KHÓA LUẬN, ĐỒ ÁN
            // =====================================================
            [
                'chu_de' => 'tot_nghiep',
                'cau_hoi_mau' => 'Điều kiện làm khóa luận tốt nghiệp?',
                'cau_tra_loi' => "**🎓 Điều kiện làm khóa luận tốt nghiệp:**\n\n**Yêu cầu:**\n✅ Đã tích lũy đủ ≥ 110 tín chỉ\n✅ GPA tích lũy ≥ 2.0\n✅ Đã hoàn thành tất cả môn đại cương\n✅ Đã hoàn thành thực tập (4-6TC)\n✅ Không nợ học phí\n✅ Không bị kỷ luật\n\n**Quy trình:**\n1. **Tuần 1-2:** Đăng ký đề tài, chọn GVHD\n2. **Tuần 3-12:** Thực hiện nghiên cứu\n3. **Tuần 13-14:** Nộp báo cáo tiến độ\n4. **Tuần 15:** Nộp báo cáo hoàn chỉnh\n5. **Tuần 16:** Bảo vệ trước hội đồng\n\n**📊 Cấu trúc điểm:**\n- GVHD đánh giá: 30%\n- GVPB đánh giá: 30%\n- Hội đồng đánh giá: 40%\n\n**🎯 Điều kiện đạt:** Điểm ≥ 5.0",
                'tu_khoa' => 'khóa luận tốt nghiệp, đồ án tốt nghiệp, KLTN, thesis',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'tot_nghiep',
                'cau_hoi_mau' => 'Điều kiện tốt nghiệp?',
                'cau_tra_loi' => "**🎓 Điều kiện tốt nghiệp:**\n\n**Yêu cầu bắt buộc:**\n✅ Tích lũy đủ **120 tín chỉ** trở lên\n✅ GPA tích lũy ≥ **2.0/4.0**\n✅ Không có môn F (hoặc đã học lại đạt)\n✅ Hoàn thành **thực tập** (4-6TC)\n✅ Hoàn thành **khóa luận/đồ án tốt nghiệp** (10TC)\n✅ Đạt chuẩn ngoại ngữ đầu ra (TOEIC ≥450 hoặc tương đương)\n✅ Đạt chuẩn tin học (MOS, IC3 hoặc tương đương)\n✅ Điểm rèn luyện trung bình ≥ 50/100\n✅ Không nợ học phí\n✅ Không vi phạm kỷ luật\n\n**📅 Thời gian:**\n- Xét tốt nghiệp: 2 đợt/năm (tháng 6 và tháng 12)\n- Nhận bằng: Sau 3-6 tháng kể từ ngày xét",
                'tu_khoa' => 'tốt nghiệp, điều kiện tốt nghiệp, graduation, tot nghiep',
                'do_uu_tien' => 95,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // RÈN LUYỆN SINH VIÊN
            // =====================================================
            [
                'chu_de' => 'ren_luyen',
                'cau_hoi_mau' => 'Điểm rèn luyện là gì?',
                'cau_tra_loi' => "**⭐ Điểm rèn luyện sinh viên:**\n\nLà điểm đánh giá **kết quả rèn luyện** của sinh viên mỗi học kỳ.\n\n**📊 Cấu trúc (100 điểm):**\n\n**1. Ý thức học tập (25đ)**\n- Đi học đầy đủ, đúng giờ\n- Nộp bài tập đúng hạn\n- Tham gia học tập nghiêm túc\n\n**2. Ý thức chấp hành nội quy (25đ)**\n- Không vi phạm quy chế\n- Chấp hành pháp luật\n- Đóng học phí đúng hạn\n\n**3. Tham gia hoạt động (30đ)**\n- Hoạt động đoàn thể, CLB\n- Tình nguyện, phong trào\n- Văn hóa, thể thao\n\n**4. Quan hệ cộng đồng (20đ)**\n- Tác phong văn hóa\n- Ý thức tập thể\n- Giúp đỡ bạn bè\n\n**🏆 Xếp loại:**\n- 90-100đ: Xuất sắc\n- 80-89đ: Tốt\n- 65-79đ: Khá\n- 50-64đ: Trung bình\n- <50đ: Yếu",
                'tu_khoa' => 'điểm rèn luyện, đánh giá rèn luyện, điểm RL',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'ren_luyen',
                'cau_hoi_mau' => 'Cách tính điểm rèn luyện?',
                'cau_tra_loi' => "**📝 Cách tính điểm rèn luyện:**\n\n**Quy trình:**\n\n**Bước 1:** Sinh viên tự chấm (cuối học kỳ)\n- Đăng nhập S-MIS\n- Vào **\"Rèn luyện\"**\n- Tự đánh giá theo từng tiêu chí\n- Cung cấp minh chứng (nếu có)\n\n**Bước 2:** Lớp trưởng/BCH lớp nhận xét\n\n**Bước 3:** Cố vấn học tập xét duyệt\n\n**Bước 4:** Khoa thông qua\n\n**📋 Minh chứng:**\n- Giấy chứng nhận hoạt động\n- Ảnh tham gia sự kiện\n- Giải thưởng, bằng khen\n\n**⏰ Thời gian:**\n- Tuần cuối mỗi học kỳ\n- Chậm nhất 2 tuần sau khi kết thúc học kỳ\n\n💡 **Lưu ý:** Điểm RL ảnh hưởng đến học bổng và xét tốt nghiệp!",
                'tu_khoa' => 'tính điểm rèn luyện, chấm điểm rèn luyện, đánh giá RL',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // THƯ VIỆN
            // =====================================================
            [
                'chu_de' => 'thu_vien',
                'cau_hoi_mau' => 'Thư viện mở cửa khi nào?',
                'cau_tra_loi' => "**📚 Thông tin Thư viện trường:**\n\n**🕐 Giờ mở cửa:**\n- Thứ 2 - Thứ 6: 7h00 - 21h00\n- Thứ 7: 8h00 - 17h00\n- Chủ nhật: 8h00 - 12h00\n\n**📍 Vị trí:**\n- Tòa nhà Thư viện (5 tầng)\n- Tầng 1-3: Khu đọc sách\n- Tầng 4: Khu tự học\n- Tầng 5: Phòng học nhóm\n\n**📖 Dịch vụ:**\n- Mượn/trả sách (tối đa 5 cuốn/lần, 14 ngày)\n- Tra cứu tài liệu online\n- Photocopy, in ấn\n- Phòng học nhóm (đặt trước)\n- WiFi miễn phí\n\n**💳 Thẻ thư viện:**\n- Sử dụng thẻ sinh viên\n- Phí quá hạn: 5.000đ/ngày/cuốn",
                'tu_khoa' => 'thư viện, library, mượn sách, giờ mở cửa thư viện',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // KÝ TÚC XÁ
            // =====================================================
            [
                'chu_de' => 'ky_tuc_xa',
                'cau_hoi_mau' => 'Làm sao để ở ký túc xá?',
                'cau_tra_loi' => "**🏠 Đăng ký ký túc xá:**\n\n**Điều kiện:**\n✅ Là sinh viên chính quy\n✅ Nơi ở cách trường > 10km\n✅ Không vi phạm nội quy\n\n**Quy trình:**\n1. Đăng ký online qua S-MIS\n2. Nộp hồ sơ (CMND, ảnh, giấy xác nhận địa chỉ)\n3. Chờ phân phòng (ưu tiên sinh viên xa)\n4. Nộp tiền cọc + tiền phòng tháng đầu\n5. Nhận phòng\n\n**💰 Giá phòng/tháng:**\n- Phòng 4 người: 500.000đ/người\n- Phòng 6 người: 400.000đ/người\n- Phòng 8 người: 350.000đ/người\n- Tiền cọc: 1.000.000đ (hoàn lại khi trả phòng)\n\n**🔌 Dịch vụ:**\n- Điện, nước (tính theo đồng hồ)\n- WiFi miễn phí\n- Máy giặt, sân phơi\n- Bảo vệ 24/7\n\n**⚠️ Nội quy:**\n- Giờ giấc: 22h30 đóng cửa\n- Không nấu ăn trong phòng\n- Không để người lạ qua đêm",
                'tu_khoa' => 'ký túc xá, KTX, dormitory, dorm, nội trú',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // BẢO LƯU
            // =====================================================
            [
                'chu_de' => 'bao_luu',
                'cau_hoi_mau' => 'Bảo lưu là gì? Điều kiện bảo lưu?',
                'cau_tra_loi' => "**⏸️ Bảo lưu học tập:**\n\nLà việc sinh viên **tạm dừng học** có thời hạn, sau đó quay lại tiếp tục.\n\n**Điều kiện:**\n✅ Có lý do chính đáng:\n   - Ốm đau, tai nạn\n   - Hoàn cảnh gia đình khó khăn\n   - Nghĩa vụ quân sự\n   - Thai sản\n✅ Có giấy tờ chứng minh\n✅ Đã học ≥ 1 học kỳ\n✅ Không bị kỷ luật\n\n**Quy trình:**\n1. Nộp đơn xin bảo lưu\n2. Đính kèm giấy tờ (giấy bác sĩ, giấy nghĩa vụ...)\n3. Chờ phê duyệt (1-2 tuần)\n4. Nhận quyết định bảo lưu\n\n**⏱️ Thời hạn:**\n- Tối đa **2 năm** liên tục hoặc gián đoạn\n- Có thể gia hạn nếu lý do đặc biệt\n\n**💰 Học phí:** Không phải đóng trong thời gian bảo lưu\n\n**🔄 Quay lại:** Nộp đơn xin nhập học lại trước 1 tháng",
                'tu_khoa' => 'bảo lưu, bao luu, tạm dừng học, nghỉ học',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // THẺ SINH VIÊN
            // =====================================================
            [
                'chu_de' => 'the_sv',
                'cau_hoi_mau' => 'Làm lại thẻ sinh viên khi mất?',
                'cau_tra_loi' => "**💳 Cấp lại thẻ sinh viên:**\n\n**Khi nào cần làm lại?**\n- Thẻ bị mất\n- Thẻ hư hỏng, mờ không đọc được\n- Thông tin thay đổi (đổi tên, ảnh...)\n\n**Quy trình:**\n1. Đăng nhập S-MIS → **\"Hồ sơ\" → \"Làm lại thẻ SV\"**\n2. Chọn lý do làm lại\n3. Upload ảnh 3×4 (nền trắng, mặc áo trắng)\n4. Thanh toán phí online\n5. Nhận thẻ mới sau 5-7 ngày\n\n**💰 Lệ phí:**\n- Lần 1 (mất/hỏng): 50.000đ\n- Lần 2 trở đi: 100.000đ\n- Thay đổi thông tin: 30.000đ\n\n**📍 Nhận thẻ:**\n- Phòng Công tác sinh viên (Tòa A, tầng 1)\n- Giờ làm việc: 8h-11h30, 13h30-17h (Thứ 2-6)\n\n⚠️ **Lưu ý:** Mang theo CMND/CCCD khi nhận thẻ",
                'tu_khoa' => 'thẻ sinh viên, làm lại thẻ, mất thẻ, student card',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // THỰC TẬP
            // =====================================================
            [
                'chu_de' => 'thuc_tap',
                'cau_hoi_mau' => 'Thực tập doanh nghiệp khi nào?',
                'cau_tra_loi' => "**💼 Thực tập doanh nghiệp:**\n\n**Thời điểm:**\n- Năm cuối (năm 4)\n- Sau khi hoàn thành ≥ 100 tín chỉ\n- Học kỳ 7 hoặc học kỳ 8\n\n**Thời lượng:**\n- 8-12 tuần (4-6 tín chỉ)\n- Tùy theo quy định của từng ngành\n\n**Quy trình:**\n1. **Tuần 1-2:** Đăng ký đề tài thực tập\n2. **Tuần 3:** Nhận văn bản giới thiệu\n3. **Tuần 4-15:** Thực tập tại doanh nghiệp\n4. **Tuần 16:** Nộp báo cáo + đánh giá DN\n5. **Tuần 17:** Báo cáo/bảo vệ thực tập\n\n**📊 Cấu trúc điểm:**\n- DN đánh giá: 40%\n- GVHD đánh giá báo cáo: 30%\n- Trình bày/bảo vệ: 30%\n\n**🏢 Tự tìm DN hoặc do khoa sắp xếp**\n\n**📋 Kết quả:** Đạt/Không đạt (cần ≥5.0)",
                'tu_khoa' => 'thực tập, internship, thực tập doanh nghiệp',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],

            // =====================================================
            // CÁC CÂU HỎI THỰC TẾ KHÁC
            // =====================================================
            [
                'chu_de' => 'khac',
                'cau_hoi_mau' => 'Tín chỉ là gì?',
                'cau_tra_loi' => "**📚 Tín chỉ (Credit) là gì?**\n\nLà đơn vị đo lường **khối lượng học tập** của một môn học.\n\n**📊 Quy đổi:**\n- **1 tín chỉ lý thuyết** = 15 tiết học trên lớp + 30 giờ tự học\n- **1 tín chỉ thực hành** = 30 tiết thực hành + 15 giờ tự học\n- **1 tín chỉ thực tập** = 45 giờ thực tập thực tế\n\n**Ví dụ:**\n- Môn 3 tín chỉ LT → 45 tiết lên lớp (~ 3 buổi/tuần × 15 tuần)\n- Môn 4 tín chỉ (3LT + 1TH) → 45 tiết LT + 30 tiết TH\n\n**🎓 Yêu cầu tốt nghiệp:**\n- Tối thiểu: **120 tín chỉ**\n- Tùy ngành có thể 120-140 TC\n\n**⚠️ Giới hạn đăng ký:**\n- Tối đa **25 TC/học kỳ** (trường hợp đặc biệt 28TC)\n- Tối thiểu **12 TC/học kỳ** (để giữ trạng thái sinh viên)",
                'tu_khoa' => 'tín chỉ, credit, TC, số tín chỉ',
                'do_uu_tien' => 90,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khac',
                'cau_hoi_mau' => 'Cố vấn học tập là gì?',
                'cau_tra_loi' => "**👨‍🏫 Cố vấn học tập (CVHT):**\n\nLà **giảng viên** được phân công hỗ trợ, tư vấn cho lớp sinh viên.\n\n**Vai trò:**\n- 📚 Tư vấn chương trình đào tạo, lựa chọn môn học\n- 📊 Theo dõi kết quả học tập\n- 💬 Tư vấn định hướng nghề nghiệp\n- ⚠️ Hỗ trợ sinh viên gặp khó khăn\n- ✍️ Xác nhận, ký giấy tờ hành chính\n- ⭐ Duyệt điểm rèn luyện\n\n**Cách liên hệ CVHT:**\n1. Xem thông tin trên S-MIS\n2. Email hoặc điện thoại\n3. Gặp trực tiếp (đặt lịch trước)\n\n**📅 Buổi sinh hoạt lớp:**\n- 1 lần/tháng\n- Thảo luận học tập, hoạt động\n\n💡 **Lưu ý:** CVHT là người hỗ trợ đắc lực, đừng ngại liên hệ khi cần!",
                'tu_khoa' => 'cố vấn học tập, CVHT, advisor, giáo viên chủ nhiệm',
                'do_uu_tien' => 80,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khac',
                'cau_hoi_mau' => 'Thi trắc nghiệm hay tự luận?',
                'cau_tra_loi' => "**📝 Hình thức thi:**\n\n**Tùy thuộc vào từng môn học:**\n\n**1. Thi trắc nghiệm (Multiple choice):**\n- Môn lý thuyết, đại cương\n- Thời gian: 60-90 phút\n- Số câu: 40-50 câu\n- Có thể thi trên máy tính hoặc giấy\n\n**2. Thi tự luận (Essay):**\n- Môn chuyên sâu, phân tích\n- Thời gian: 90-120 phút\n- Trả lời câu hỏi dạng luận\n\n**3. Thi thực hành:**\n- Môn kỹ năng, thực hành\n- Làm bài tập, dự án trực tiếp\n- Thời gian: 90-180 phút\n\n**4. Thi vấn đáp:**\n- Trình bày trước giảng viên/hội đồng\n- Thời gian: 15-30 phút/sinh viên\n\n**🔍 Cách biết hình thức thi:**\n- Xem lịch thi trên S-MIS\n- Giảng viên thông báo đầu học kỳ\n- Ghi trong đề cương môn học",
                'tu_khoa' => 'hình thức thi, trắc nghiệm, tự luận, exam format',
                'do_uu_tien' => 75,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khac',
                'cau_hoi_mau' => 'Điểm W là gì?',
                'cau_tra_loi' => "**📊 Điểm W (Withdrawn):**\n\nLà ký hiệu khi sinh viên **rút lui khỏi môn học** sau thời gian điều chỉnh đăng ký.\n\n**Khi nào có điểm W?**\n- Hủy môn sau thời gian điều chỉnh\n- Bị cấm thi (do vắng quá nhiều)\n- Rút lui vì lý do cá nhân\n\n**Ảnh hưởng:**\n❌ **KHÔNG tính vào GPA**\n✅ Vẫn hiển thị trên bảng điểm\n⚠️ Ảnh hưởng tiến độ học tập\n💰 Không được hoàn học phí\n\n**So sánh với điểm F:**\n- **F:** Tính vào GPA, kéo GPA xuống\n- **W:** Không tính GPA, nhưng mất tiến độ\n\n**Lưu ý:**\n- Nên cân nhắc kỹ trước khi rút lui\n- Tư vấn với CVHT trước khi quyết định",
                'tu_khoa' => 'điểm W, withdrawn, rút lui, hủy môn',
                'do_uu_tien' => 70,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khac',
                'cau_hoi_mau' => 'Email sinh viên ở đâu?',
                'cau_tra_loi' => "**📧 Email sinh viên:**\n\n**Định dạng:**\n`masinhvien@student.smis.edu.vn`\n\n**Ví dụ:**\n- Mã SV: 2021001234\n- Email: 2021001234@student.smis.edu.vn\n\n**Mật khẩu mặc định:**\n- Lần đầu: Ngày sinh dạng `DDMMYYYY`\n- VD: Sinh ngày 15/03/2003 → Mật khẩu: `15032003`\n- **Đổi ngay sau lần đăng nhập đầu!**\n\n**Cách sử dụng:**\n1. Truy cập: https://mail.smis.edu.vn\n2. Đăng nhập bằng email và mật khẩu\n3. Hoặc cấu hình trên Outlook/Gmail app\n\n**Công dụng:**\n- Nhận thông báo từ trường\n- Liên hệ giảng viên\n- Đăng ký dịch vụ Microsoft 365 miễn phí\n- Tải Office, OneDrive 1TB\n\n⚠️ **Kiểm tra email thường xuyên!**",
                'tu_khoa' => 'email sinh viên, mail, @student, outlook',
                'do_uu_tien' => 85,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khac',
                'cau_hoi_mau' => 'Đăng ký môn học vượt được không?',
                'cau_tra_loi' => "**🚀 Học vượt:**\n\nLà việc đăng ký học **nhiều hơn** tiến độ chuẩn để tốt nghiệp sớm.\n\n**Điều kiện:**\n✅ GPA tích lũy ≥ 2.8\n✅ Không có môn F trong 2 học kỳ gần nhất\n✅ Không bị cảnh báo học vụ\n✅ Có đơn xin phép học vượt\n\n**Giới hạn:**\n- Học kỳ thường: Tối đa **25 TC**\n- Học vượt: Được phép đến **28 TC**\n- Học kỳ hè: Tối đa **12 TC**\n\n**Lợi ích:**\n✅ Tốt nghiệp sớm (3.5 năm thay vì 4 năm)\n✅ Tiết kiệm thời gian\n✅ Sớm đi làm\n\n**Lưu ý:**\n⚠️ Áp lực học tập cao\n⚠️ Học phí tăng\n⚠️ Cần quản lý thời gian tốt",
                'tu_khoa' => 'học vượt, học nhanh, tốt nghiệp sớm, accelerated',
                'do_uu_tien' => 75,
                'kich_hoat' => true,
                'nguoi_tao_id' => 1,
            ],
            [
                'chu_de' => 'khac',
                'cau_hoi_mau' => 'Nghỉ học có phép thì làm thế nào?',
                'cau_tra_loi' => "**🏥 Xin nghỉ học có phép:**\n\n**Lý do chính đáng:**\n- Ốm đau (có giấy bác sĩ)\n- Tang gia (người thân)\n- Việc gia đình đột xuất\n- Dự thi, cuộc thi quan trọng\n\n**Quy trình:**\n\n**1. Nghỉ 1-2 buổi:**\n- Email/nhắn tin trực tiếp cho giảng viên\n- Gửi giấy xin phép + minh chứng\n\n**2. Nghỉ nhiều buổi (>1 tuần):**\n- Làm đơn xin phép\n- Nộp qua S-MIS hoặc Phòng Đào tạo\n- Đính kèm giấy tờ chứng minh\n- Chờ phê duyệt\n\n**📧 Email mẫu cho GV:**\n```\nKính gửi Thầy/Cô [Tên GV],\n\nEm là [Họ tên], lớp [Lớp], mã SV [Mã].\nEm xin phép nghỉ buổi học [Môn học] ngày [Ngày] vì lý do [Lý do].\nEm xin gửi kèm [giấy bác sĩ/giấy tờ...].\nEm xin cảm ơn!\n\nTrân trọng,\n[Họ tên]\n```\n\n⚠️ **Lưu ý:** Vẫn tính vào tổng số buổi vắng (cần <20%)",
                'tu_khoa' => 'xin nghỉ, nghỉ học, vắng học có phép, leave request',
                'do_uu_tien' => 80,
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
