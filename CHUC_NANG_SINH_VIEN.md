# CHỨC NĂNG SINH VIÊN

**Phạm vi:** Học tập cá nhân, đăng ký học phần, xem lịch/thi/điểm, học phí, thông báo

---

## 1. HỒ SƠ & TRẠNG THÁI

### 1.1. Thông tin cá nhân (sinh_vien)

-   Xem thông tin cá nhân (mã SV, họ tên, ngày sinh, giới tính, CCCD)
-   Xem thông tin liên hệ (email, SĐT, địa chỉ)
-   Cập nhật thông tin liên hệ (SĐT, địa chỉ)
-   Cập nhật ảnh đại diện (anh_dai_dien)
-   Đổi mật khẩu (users)

### 1.2. Thông tin học tập

-   Xem lớp hành chính (lop_hanh_chinh)
-   Xem ngành/chuyên ngành (nganh, chuyen_nganh)
-   Xem khóa học (khoa_hoc)
-   Xem giảng viên chủ nhiệm (giang_vien_chu_nhiem_id)
-   Xem kỳ hiện tại (ky_hien_tai)
-   Xem trạng thái học tập (trang_thai_hoc_tap: Đang học, Bảo lưu, Thôi học, Tốt nghiệp)

---

## 2. CHƯƠNG TRÌNH ĐÀO TẠO

### 2.1. Xem Chương trình khung (chuong_trinh_khung)

-   Xem chương trình khung theo chuyên ngành
-   Xem danh sách môn học bắt buộc (bat_buoc = true)
-   Xem danh sách môn tự chọn (bat_buoc = false)
-   Xem phân loại môn (loai_mon_hoc: dai_cuong, co_so_nganh, chuyen_nganh_bat_buoc, chuyen_nganh_tu_chon, thuc_tap, do_an_tot_nghiep)
-   Xem học kỳ gợi ý (hoc_ky_goi_y)
-   Xem thứ tự học (thu_tu_hoc)

### 2.2. Xem Môn học (mon_hoc)

-   Xem thông tin môn học (tên môn, số tín chỉ, mô tả)
-   Xem số tín chỉ lý thuyết/thực hành (so_tin_chi_ly_thuyet, so_tin_chi_thuc_hanh)
-   Xem hình thức dạy (hinh_thuc_day: offline, online, hybrid)
-   Xem thời lượng học, số buổi học

### 2.3. Xem Môn tiên quyết (mon_hoc_tien_quyet)

-   Xem môn học tiên quyết
-   Xem loại tiên quyết (loai_tien_quyet: bat_buoc, khuyen_nghi)
-   Xem điều kiện qua môn (dieu_kien_qua_mon)

---

## 3. ĐĂNG KÝ HỌC PHẦN

### 3.1. Đăng ký môn học tạm (dang_ky_mon_hoc_tam)

-   Xem lịch mở đăng ký (hoc_ky: ngay_bat_dau_dang_ky, ngay_ket_thuc_dang_ky)
-   Xem danh sách môn học có thể đăng ký (kiểm tra tiên quyết, tín chỉ tối đa)
-   Đăng ký môn học (tạo dang_ky_mon_hoc_tam)
-   Hủy đăng ký môn học (trong thời gian cho phép)
-   Xem danh sách môn đã đăng ký tạm
-   Xem trạng thái (trang_thai: cho_xep_lop, da_xep_lop, that_bai)
-   Xem lý do xếp lớp thất bại (ly_do_that_bai)
-   Xem độ ưu tiên (uu_tien)

### 3.2. Theo dõi xếp lớp

-   Xem kết quả xếp lớp (lop_hoc_phan_sinh_vien)
-   Xem phương thức xếp (phuong_thuc_xep: tu_dong, thu_cong)
-   Xem ngày xếp lớp (ngay_xep_lop)
-   Xem người duyệt (nếu xếp thủ công)

### 3.3. Thao tác đổi/hủy (Trong giai đoạn cho phép)

-   Đổi lớp học phần (nếu hệ thống cho phép)
-   Hủy đăng ký lớp học phần (trang_thai: huy_dang_ky)
-   Xem lý do hủy (ly_do_huy)

---

## 4. LỚP HỌC PHẦN

### 4.1. Xem Lớp học phần đã đăng ký (lop_hoc_phan_sinh_vien)

-   Xem danh sách lớp học phần đã đăng ký
-   Xem thông tin lớp HP (mã lớp, tên lớp, môn học, học kỳ)
-   Xem trạng thái (trang_thai: da_xep_lop, dang_hoc, da_hoan_thanh, bo_hoc, huy_dang_ky)

### 4.2. Xem chi tiết Lớp học phần (lop_hoc_phan)

-   Xem thông tin lớp (sức chứa, số lượng đăng ký, nhóm lớp)
-   Xem hình thức (hinh_thuc: offline, online, hybrid)
-   Xem link học online (link_online)
-   Xem thời gian (ngay_bat_dau, ngay_ket_thuc)
-   Xem trạng thái lớp (trang_thai_lop: mo_dang_ky, dang_hoc, ket_thuc, huy)

### 4.3. Xem Giảng viên (lop_hoc_phan_giang_vien)

-   Xem danh sách giảng viên dạy
-   Xem vai trò giảng viên (vai_tro: giang_vien_chinh, giang_vien_phu, tro_giang)
-   Xem thông tin giảng viên (họ tên, email, SĐT, khoa)

### 4.4. Xem Sinh viên cùng lớp (lop_hoc_phan_sinh_vien)

-   Xem danh sách sinh viên cùng lớp

### 4.5. Xem Cấu hình điểm (cau_hinh_dau_diem)

-   Xem cấu hình đầu điểm (Chuyên cần, Giữa kỳ, Cuối kỳ, Thực hành, Tiểu luận)
-   Xem tỷ lệ % từng đầu điểm (ty_le)
-   Xem số cột điểm (so_cot)

---

## 5. THỜI KHÓA BIỂU & LỊCH THI

### 5.1. Thời khóa biểu (lich_hoc_chi_tiet)

-   Xem thời khóa biểu cá nhân
-   Xem lịch học theo ngày
-   Xem lịch học theo tuần
-   Xem lịch học theo tháng
-   Xem chi tiết từng buổi học (ngày, thứ, tiết, giờ, phòng, giảng viên, lớp HP)
-   Xem nội dung giảng dạy (noi_dung_giang_day)
-   Xem tài liệu đính kèm (tai_lieu_dinh_kem)
-   Xem link học online (link_online)
-   Xem trạng thái buổi học (trang_thai: chua_day, dang_day, da_day, huy)
-   Xuất lịch học PDF

### 5.2. Lịch thi (lich_thi)

-   Xem lịch thi cá nhân
-   Xem chi tiết từng môn thi (loại thi, ngày thi, giờ, phòng)
-   Xem loại thi (loai_thi: Giữa kỳ, Cuối kỳ, Thi lại)
-   Xem phòng thi (phong_thi_id)
-   Xem giám thị (giam_thi_1_id, giam_thi_2_id)
-   Xem hình thức thi (hinh_thuc: offline, online, hybrid)
-   Xem link thi online (link_online)
-   Xuất lịch thi PDF

---

## 6. ĐIỂM DANH

### 6.1. Xem Điểm danh (diem_danh)

-   Xem lịch sử điểm danh theo lớp HP
-   Xem chi tiết điểm danh từng buổi
-   Xem trạng thái điểm danh (trang_thai: co_mat, vang, di_tre, nghi_phep)
-   Xem thời gian điểm danh (thoi_gian_diem_danh)
-   Xem ghi chú (ghi_chu)

### 6.2. Thống kê điểm danh

-   Xem thống kê vắng mặt theo môn
-   Xem tỷ lệ chuyên cần (%)
-   Xem số buổi có mặt/vắng/đi trễ/nghỉ phép

---

## 7. KẾT QUẢ HỌC TẬP

### 7.1. Điểm thành phần (nhap_diem)

-   Xem điểm theo từng đầu điểm
-   Xem điểm theo từng cột (cot_diem)
-   Xem điểm số (diem_so)
-   Xem ghi chú (ghi_chu)

### 7.2. Điểm tổng kết môn học (ket_qua_hoc_tap)

-   Xem điểm tổng kết từng môn
-   Xem điểm hệ 10 (diem_he_10)
-   Xem điểm hệ 4 (diem_he_4)
-   Xem điểm chữ (diem_chu: A, B+, B, C+, C, D+, D, F)
-   Xem trạng thái qua môn (qua_mon)
-   Xem ghi chú (ghi_chu)

### 7.3. Bảng điểm học kỳ (bang_diem)

-   Xem bảng điểm theo học kỳ
-   Xem điểm trung bình học kỳ (diem_trung_binh_hoc_ky)
-   Xem điểm trung bình tích lũy (diem_trung_binh_tich_luy)
-   Xem tổng số tín chỉ đã đạt (tong_tin_chi_dat)

### 7.4. Xuất bảng điểm

-   Xuất bảng điểm học kỳ PDF
-   Xuất học bạ (tất cả học kỳ) PDF
-   Xuất bảng điểm Excel

---

## 8. HỌC PHÍ

### 8.1. Học phí học kỳ (hoc_phi_hoc_ky)

-   Xem tổng hợp học phí học kỳ
-   Xem tổng tín chỉ đăng ký (tong_tin_chi_dang_ky)
-   Xem tổng học phí môn học (tong_hoc_phi_mon_hoc)
-   Xem phí dịch vụ (phi_dich_vu)
-   Xem tổng số tiền (tong_so_tien)
-   Xem số tiền đã đóng (so_tien_da_dong)
-   Xem số tiền còn lại (so_tien_con_lai)
-   Xem hạn đóng (han_dong)
-   Xem trạng thái (trang_thai: chua_nop, da_nop_mot_phan, da_nop_du, qua_han)

### 8.2. Chi tiết học phí môn (chi_tiet_hoc_phi_mon)

-   Xem chi tiết học phí từng môn
-   Xem môn học (mon_hoc_id)
-   Xem số tín chỉ (so_tin_chi)
-   Xem đơn giá tín chỉ (don_gia_tin_chi)
-   Xem thành tiền (thanh_tien = so_tin_chi \* don_gia_tin_chi)
-   Xem trạng thái thanh toán (trang_thai: chua_thanh_toan, da_thanh_toan, huy)

### 8.3. Lịch sử đóng học phí (lich_su_dong_hoc_phi)

-   Xem lịch sử đóng học phí
-   Xem số tiền đóng từng lần (so_tien_dong)
-   Xem ngày đóng (ngay_dong)
-   Xem phương thức thanh toán (phuong_thuc_thanh_toan: Tiền mặt, Chuyển khoản, Ví điện tử, QR Code)
-   Xem mã giao dịch (ma_giao_dich)
-   Xem ngân hàng (ngan_hang)
-   Tải biên lai (bien_lai_file)

### 8.4. Nộp học phí & Xuất hóa đơn

-   Xem hướng dẫn nộp học phí
-   Tải biên lai đã nộp (nếu có)
-   Xuất hóa đơn học phí PDF

---

## 9. CẢNH BÁO HỌC VỤ

### 9.1. Xem Cảnh báo (canh_bao_hoc_vu)

-   Xem danh sách cảnh báo cá nhân
-   Xem chi tiết cảnh báo
-   Xem loại cảnh báo (loai_canh_bao: diem_thap, vang_nhieu, no_hoc_phi, hoc_ky_lien_tiep)
-   Xem mức độ cảnh báo (muc_do: canh_cao, dinh_chi, buoc_thoi_hoc)
-   Xem lý do cảnh báo (ly_do: GPA < 1.0, vắng > 20%, nợ học phí quá hạn)
-   Xem ngày cảnh báo (ngay_canh_bao)
-   Xem trạng thái xử lý (da_xu_ly, ngay_xu_ly, ket_qua_xu_ly)
-   Xem hướng giải quyết

---

## 10. THÔNG BÁO

### 10.1. Nhận thông báo (thong_bao, nguoi_nhan_thong_bao)

-   Xem thông báo chung (đối tượng: all, sinh_vien)
-   Xem thông báo lớp học phần (doi_tuong: lop_hoc_phan)
-   Xem thông báo lớp hành chính (doi_tuong: lop_hanh_chinh)
-   Xem thông báo khoa/ngành (doi_tuong: khoa, nganh)
-   Xem thông báo cá nhân
-   Xem loại thông báo (loai_thong_bao: tin_tuc, thong_bao_chung, tin_gap, lich_hoc, lich_thi, hoc_phi, diem, dang_ky_mon)
-   Xem mức độ quan trọng (muc_do_quan_trong: rat_quan_trong, quan_trong, binh_thuong)
-   Xem thông báo ghim (ghim_dau_trang)
-   Đánh dấu đã đọc (da_doc, ngay_doc)
-   Xem file đính kèm (file_dinh_kem)
-   Xem liên kết liên quan (lien_ket_id, lien_ket_loai)
-   Tìm kiếm thông báo
-   Lọc thông báo theo loại, thời gian

---

## 11. AI CHATBOT HỖ TRỢ

### 11.1. Chat với Bot (ai_chatbot_conversation, ai_chatbot_message)

-   Tạo phiên chat mới (ai_chatbot_conversation)
-   Gửi câu hỏi (ai_chatbot_message với nguoi_gui = 'user')
-   Nhận câu trả lời từ bot (nguoi_gui = 'bot')
-   Xem độ tương đồng câu trả lời (do_tuong_dong)
-   Xem nguồn kiến thức (knowledge_base_id)
-   Xem lịch sử chat (session_id)
-   Xem các phiên chat cũ

### 11.2. Hỏi đáp chủ đề (ai_chatbot_knowledge_base)

-   Hỏi về đăng ký môn học
-   Hỏi về lịch học, lịch thi
-   Hỏi về học phí
-   Hỏi về quy chế đào tạo
-   Hỏi về thủ tục hành chính
-   Hỏi về điểm, kết quả học tập
-   Hỏi về chương trình đào tạo

### 11.3. Đánh giá Chatbot (ai_chatbot_feedback)

-   Đánh giá câu trả lời (danh_gia: huu_ich, khong_huu_ich)
-   Ghi lý do đánh giá (ly_do)
-   Góp ý cải thiện

---

## 12. TRA CỨU THÔNG TIN

### 12.1. Tra cứu Môn học (mon_hoc)

-   Tìm kiếm môn học theo mã/tên
-   Xem thông tin môn học
-   Xem môn tiên quyết

### 12.2. Tra cứu Giảng viên (giang_vien)

-   Tìm kiếm giảng viên theo tên
-   Xem thông tin giảng viên (khoa, trình độ, chuyên môn, email, SĐT)

### 12.3. Tra cứu Phòng học (phong_hoc)

-   Tìm kiếm phòng học
-   Xem thông tin phòng (vị trí, sức chứa, loại phòng, trạng thái)

### 12.4. Tra cứu Lịch sử học tập

-   Xem lịch sử đăng ký môn học
-   Xem lịch sử học phí
-   Xem lịch sử cảnh báo

---

## 13. XUẤT DỮ LIỆU

-   Xuất bảng điểm học kỳ PDF
-   Xuất học bạ PDF
-   Xuất lịch học PDF
-   Xuất lịch thi PDF
-   Xuất hóa đơn học phí PDF
-   Xuất báo cáo điểm danh PDF
