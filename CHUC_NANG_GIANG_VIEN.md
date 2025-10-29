# CHỨC NĂNG GIẢNG VIÊN

**Phạm vi:** Giảng dạy lớp học phần, chuyên môn, điểm danh/điểm, thông tin lớp

---

## 1. QUẢN LÝ THÔNG TIN CÁ NHÂN

### 1.1. Hồ sơ cá nhân (giang_vien)

-   Xem thông tin cá nhân (hồ sơ, email, SĐT, trình độ, chuyên môn, khoa)
-   Cập nhật thông tin liên hệ (email, SĐT)
-   Cập nhật ảnh đại diện (anh_dai_dien)
-   Đổi mật khẩu (users)

---

## 2. LỚP PHỤ TRÁCH

### 2.1. Xem Lớp học phần (lop_hoc_phan_giang_vien)

-   Xem danh sách lớp được phân công dạy
-   Xem chi tiết lớp học phần (môn học, học kỳ, sức chứa, trạng thái)
-   Xem vai trò phân công (vai_tro: giang_vien_chinh, giang_vien_phu, tro_giang)
-   Xem phần công giảng dạy (phan_cong_giang_day)
-   Xem thông tin người phân công (nguoi_phan_cong_id, ngay_phan_cong)

### 2.2. Xem Danh sách Sinh viên (lop_hoc_phan_sinh_vien)

-   Xem danh sách sinh viên trong lớp HP
-   Xem thông tin sinh viên (mã SV, họ tên, email, SĐT, lớp hành chính)
-   Xem trạng thái đăng ký (trang_thai: da_xep_lop, dang_hoc, da_hoan_thanh, bo_hoc, huy_dang_ky)
-   Xuất danh sách sinh viên Excel/PDF

---

## 3. LỊCH DẠY CÁ NHÂN

### 3.1. Xem Lịch dạy (lich_hoc_co_dinh, lich_hoc_chi_tiet)

-   Xem lịch dạy cá nhân
-   Xem lịch dạy theo tuần
-   Xem lịch dạy theo tháng
-   Xem lịch dạy theo ngày
-   Xem thông tin buổi học (thứ, tiết, giờ, phòng, lớp HP, link online)
-   Xuất lịch dạy

---

## 4. QUẢN LÝ BUỔI HỌC (lich_hoc_chi_tiet)

### 4.1. Cập nhật nội dung buổi học

-   Xem danh sách buổi học
-   Cập nhật nội dung giảng dạy (noi_dung_giang_day)
-   Đính kèm tài liệu (tai_lieu_dinh_kem)
-   Cập nhật trạng thái buổi học (trang_thai: chua_day, dang_day, da_day, huy)
-   Xem lịch sử các buổi đã dạy

---

## 5. QUẢN LÝ ĐIỂM DANH

### 5.1. Điểm danh Sinh viên (diem_danh)

-   Xem danh sách sinh viên cần điểm danh theo buổi
-   Điểm danh sinh viên (trang_thai: co_mat, vang, di_tre, nghi_phep)
-   Ghi nhận thời gian điểm danh (thoi_gian_diem_danh)
-   Sửa điểm danh (nếu trong thời gian cho phép)
-   Ghi chú điểm danh (ghi_chu)

### 5.2. Báo cáo điểm danh

-   Xem lịch sử điểm danh theo lớp
-   Xem thống kê vắng mặt từng sinh viên
-   Xem tỷ lệ chuyên cần lớp
-   Xuất báo cáo điểm danh Excel/PDF

---

## 6. CẤU HÌNH ĐIỂM (Theo phân quyền Đào tạo)

### 6.1. Xem/Đề xuất Cấu hình đầu điểm (cau_hinh_dau_diem)

-   Xem cấu hình đầu điểm của lớp (Chuyên cần, Giữa kỳ, Cuối kỳ, Thực hành, Tiểu luận)
-   Xem tỷ lệ % từng đầu điểm (ty_le)
-   Xem số cột điểm (so_cot)
-   Đề xuất/Yêu cầu điều chỉnh cấu hình (nếu được phép)

### 6.2. Thiết lập cấu hình (Nếu có quyền)

-   Thiết lập cấu hình đầu điểm
-   Sửa cấu hình đầu điểm
-   Xóa cấu hình đầu điểm
-   Đảm bảo tổng tỷ lệ = 100%

---

## 7. NHẬP ĐIỂM

### 7.1. Nhập điểm thành phần (nhap_diem)

-   Xem danh sách sinh viên cần nhập điểm
-   Xem cấu hình đầu điểm (cau_hinh_dau_diem)
-   Nhập điểm theo từng đầu điểm và cột (diem_so)
-   Sửa điểm (nếu chưa khóa)
-   Ghi chú điểm (ghi_chu)
-   Import điểm từ Excel
-   Xuất template nhập điểm Excel

### 7.2. Kiểm tra nhập điểm

-   Kiểm tra ràng buộc điểm (0-10)
-   Kiểm tra đã nhập đủ cột điểm chưa
-   Xem trạng thái khóa/mở bảng điểm

---

## 8. XEM KẾT QUẢ HỌC TẬP (ket_qua_hoc_tap)

### 8.1. Bảng điểm tổng kết

-   Xem bảng điểm tổng kết lớp HP
-   Xem điểm hệ 10, hệ 4, điểm chữ (diem_he_10, diem_he_4, diem_chu)
-   Xem trạng thái qua môn (qua_mon)
-   Xem chi tiết điểm từng sinh viên

### 8.2. Phân tích điểm

-   Xem thống kê phân bố điểm (A, B+, B, C+, C, D+, D, F)
-   Xem danh sách sinh viên qua/trượt
-   Xem điểm trung bình lớp
-   Xem điểm cao nhất/thấp nhất

### 8.3. Xuất bảng điểm

-   Xuất bảng điểm Excel/PDF

---

## 9. THI & NGÂN HÀNG ĐỀ

### 9.1. Lịch thi (lich_thi)

-   Xem lịch thi của lớp phụ trách
-   Xem lịch coi thi (nếu được phân công giám thị)
-   Xem thông tin phòng thi, thời gian
-   Xem danh sách sinh viên dự thi (so_sinh_vien_du_thi)

### 9.2. Đề thi & Đáp án

-   Upload đề thi (de_thi_file)
-   Upload đáp án (dap_an_file)
-   Xác nhận đã coi thi (nếu là giám thị)

---

## 10. LỚP CHỦ NHIỆM (Nếu là GVCN)

### 10.1. Quản lý Lớp hành chính (lop_hanh_chinh)

-   Xem danh sách sinh viên lớp chủ nhiệm
-   Xem thông tin chi tiết sinh viên (hồ sơ, trạng thái học tập)
-   Xem kết quả học tập sinh viên (bang_diem theo kỳ)
-   Xem điểm trung bình tích lũy, tổng tín chỉ đạt
-   Xem cảnh báo học vụ sinh viên (canh_bao_hoc_vu)
-   Xem lịch sử học tập
-   Tư vấn học tập
-   Xuất báo cáo lớp chủ nhiệm

---

## 11. TRAO ĐỔI - THÔNG BÁO LỚP

### 11.1. Nhận thông báo (thong_bao, nguoi_nhan_thong_bao)

-   Xem thông báo từ hệ thống
-   Xem thông báo từ đào tạo
-   Xem thông báo lớp học phần
-   Đánh dấu đã đọc (da_doc)
-   Tìm kiếm, lọc thông báo

### 11.2. Gửi thông báo (Nếu có quyền)

-   Gửi thông báo cho sinh viên trong lớp HP
-   Gửi thông báo cho lớp chủ nhiệm
-   Đính kèm file
-   Xem lịch sử thông báo đã gửi

---

## 12. BÁO CÁO CÁ NHÂN

### 12.1. Báo cáo giảng dạy

-   Thống kê tiến độ giảng dạy (số buổi đã dạy/tổng buổi)
-   Thống kê điểm danh theo lớp (tỷ lệ có mặt/vắng/đi trễ)
-   Phân tích điểm lớp (phân bố điểm, tỷ lệ qua môn)
-   Xuất báo cáo giảng dạy
