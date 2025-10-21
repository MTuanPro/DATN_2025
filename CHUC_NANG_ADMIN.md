# CHỨC NĂNG ADMIN (Quản trị hệ thống)

**Phạm vi:** Tài khoản & phân quyền, nhân sự hệ thống, thông báo hệ thống, nhật ký & vận hành

---

## 1. QUẢN LÝ TÀI KHOẢN & PHÂN QUYỀN

### 1.1. Quản lý Users (users)

-   Xem danh sách tài khoản
-   Tạo tài khoản mới
-   Sửa thông tin tài khoản
-   Xóa tài khoản
-   Khóa/Mở khóa tài khoản (trang_thai)
-   Đặt lại mật khẩu
-   Xác thực email (email_verified_at)
-   Xem lịch sử đăng nhập (lan_dang_nhap_cuoi)
-   Force logout tài khoản

### 1.2. Quản lý Vai trò (vai_tro)

-   Xem danh sách vai trò
-   Thêm vai trò mới
-   Sửa vai trò
-   Xóa vai trò
-   Thiết lập mức độ ưu tiên vai trò (muc_do_uu_tien)

### 1.3. Gán Tài khoản - Vai trò (tai_khoan_vai_tro)

-   Gán vai trò cho tài khoản
-   Xóa vai trò khỏi tài khoản
-   Xem lịch sử gán vai trò (ngay_gan, nguoi_gan_id)

### 1.4. Quản lý Nhóm quyền (nhom_quyen)

-   Xem danh sách nhóm quyền
-   Thêm nhóm quyền
-   Sửa nhóm quyền
-   Xóa nhóm quyền

### 1.5. Quản lý Quyền (quyen)

-   Xem danh sách quyền
-   Thêm quyền mới
-   Sửa quyền
-   Xóa quyền

### 1.6. Map Vai trò - Quyền (vai_tro_quyen)

-   Gán quyền cho vai trò
-   Xóa quyền khỏi vai trò
-   Xem ma trận quyền theo vai trò

---

## 2. QUẢN LÝ NHÂN SỰ HỆ THỐNG

### 2.1. Quản lý Admin (admin)

-   Xem danh sách admin
-   Thêm admin mới
-   Sửa thông tin admin (hồ sơ, email, SĐT, ảnh đại diện)
-   Xóa admin (soft delete)
-   Gán/hủy user_id cho admin

### 2.2. Quản lý Đào tạo (dao_tao)

-   Xem danh sách nhân viên đào tạo
-   Thêm nhân viên đào tạo
-   Sửa thông tin nhân viên đào tạo
-   Xóa nhân viên đào tạo (soft delete)
-   Gán/hủy user_id cho nhân viên đào tạo

---

## 3. QUẢN LÝ THÔNG BÁO HỆ THỐNG

### 3.1. Quản lý Thông báo (thong_bao)

-   Xem danh sách thông báo
-   Tạo thông báo mới
-   Sửa thông báo
-   Xóa thông báo (trang_thai: da_xoa)
-   Ghim/Bỏ ghim thông báo (ghim_dau_trang)
-   Gửi thông báo (đối tượng: all, sinh_vien, giang_vien, dao_tao, khoa, nganh, lop_hanh_chinh, lop_hoc_phan)
-   Thiết lập hẹn giờ gửi (hien_thi_tu_ngay)
-   Đính kèm file (file_dinh_kem)
-   Cấu hình gửi email/SMS/web notification
-   Xem thống kê lượt xem (so_luot_xem)

### 3.2. Theo dõi Người nhận (nguoi_nhan_thong_bao)

-   Xem danh sách người nhận thông báo
-   Xem trạng thái đã đọc/chưa đọc
-   Xem thời gian đọc (ngay_doc)
-   Xem trạng thái gửi email/SMS

### 3.3. Mẫu thông báo tự động (mau_thong_bao_tu_dong)

-   Xem danh sách mẫu thông báo tự động
-   Thêm mẫu thông báo
-   Sửa mẫu thông báo (tieu_de_mau, noi_dung_mau với biến)
-   Xóa mẫu thông báo
-   Bật/Tắt mẫu thông báo (kich_hoat)
-   Thiết lập mức độ ưu tiên (muc_do_uu_tien)
-   Cấu hình gửi email/SMS mặc định
-   Test mẫu thông báo

---

## 4. QUẢN LÝ AI CHATBOT

### 4.1. Knowledge Base (ai_chatbot_knowledge_base)

-   Xem danh sách kiến thức
-   Thêm kiến thức mới
-   Sửa kiến thức (câu hỏi mẫu, câu trả lời, từ khóa)
-   Xóa kiến thức
-   Kích hoạt/Vô hiệu hóa kiến thức (kich_hoat)
-   Thiết lập độ ưu tiên (do_uu_tien)
-   Phân loại theo chủ đề, danh mục
-   Import kiến thức từ file
-   Xuất kiến thức
-   Xem thống kê (luot_truy_cap, huu_ich)

### 4.2. Theo dõi Hội thoại (ai_chatbot_conversation, ai_chatbot_message)

-   Xem danh sách hội thoại
-   Xem chi tiết tin nhắn hội thoại
-   Xem độ tương đồng câu trả lời (do_tuong_dong)

### 4.3. Feedback Chatbot (ai_chatbot_feedback)

-   Xem đánh giá của người dùng
-   Xem lý do đánh giá
-   Phân tích chất lượng câu trả lời

---

## 5. NHẬT KÝ HOẠT ĐỘNG (nhat_ky_hoat_dong)

-   Xem nhật ký hoạt động (audit log)
-   Tìm kiếm theo người dùng (user_id)
-   Tìm kiếm theo hành động (hanh_dong: CREATE, UPDATE, DELETE, LOGIN, LOGOUT, EXPORT, IMPORT)
-   Tìm kiếm theo bảng dữ liệu (bang_du_lieu)
-   Tìm kiếm theo thời gian (thoi_gian)
-   Lọc theo IP address (ip_address)
-   Xem dữ liệu cũ/mới (du_lieu_cu, du_lieu_moi - JSON)
-   Xem user agent (user_agent)
-   Xuất báo cáo nhật ký
-   Xóa nhật ký cũ

---

## 6. BÁO CÁO & THỐNG KÊ HỆ THỐNG

-   Thống kê tổng quan hệ thống
-   Thống kê người dùng theo vai trò
-   Thống kê hoạt động đăng nhập
-   Thống kê truy cập theo thời gian
-   Thống kê thông báo (gửi/đọc/tương tác)
-   Thống kê sử dụng chatbot
-   Thống kê lỗi hệ thống
-   Thống kê hiệu năng
-   Xuất báo cáo Excel/PDF

---

## 7. CÀI ĐẶT & VẬN HÀNH HỆ THỐNG

### 7.1. Cấu hình chung

-   Cấu hình thông tin trường
-   Cấu hình múi giờ
-   Cấu hình ngôn ngữ
-   Cấu hình định dạng ngày tháng
-   Cấu hình logo, favicon

### 7.2. Cấu hình Email

-   Cấu hình SMTP server
-   Cấu hình email template
-   Test gửi email
-   Xem log email

### 7.3. Cấu hình SMS

-   Cấu hình SMS gateway
-   Cấu hình SMS template
-   Test gửi SMS
-   Xem log SMS

### 7.4. Cấu hình thông báo

-   Cấu hình thông báo web
-   Cấu hình thông báo push
-   Cấu hình tần suất thông báo

### 7.5. Cấu hình bảo mật

-   Cấu hình mật khẩu (độ dài, độ phức tạp)
-   Cấu hình session timeout
-   Cấu hình số lần đăng nhập sai
-   Cấu hình IP whitelist/blacklist
-   Cấu hình 2FA (Two-Factor Authentication)

### 7.6. Quản lý dữ liệu

-   Backup toàn bộ database
-   Backup theo bảng
-   Restore dữ liệu
-   Xem lịch sử backup
-   Tự động backup theo lịch
-   Export dữ liệu
-   Import dữ liệu

### 7.7. Quản lý Logs hệ thống

-   Xem application logs
-   Xem error logs
-   Xem access logs
-   Lọc logs theo mức độ (info, warning, error, critical)
-   Xóa logs cũ
-   Download logs
