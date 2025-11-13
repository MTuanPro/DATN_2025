# TÀI KHOẢN TEST - HỆ THỐNG S-MIS

**Ngày:** 13/11/2025

---

## 🔐 TÀI KHOẢN ADMIN

### Admin System
- **Email:** `admin@smis.edu.vn`
- **Password:** `password`
- **Mã Admin:** AD001
- **Vai trò:** Administrator (Quản trị hệ thống)

**Truy cập:**
- URL đăng nhập: `http://localhost:8000/admin/login`
- Dashboard: `http://localhost:8000/admin/dashboard`

**Chức năng AI Chatbot:**
- Knowledge Base: `http://localhost:8000/admin/ai-chatbot/knowledge-base`
- Quản lý hội thoại: `http://localhost:8000/admin/ai-chatbot/conversation`
- Feedback: `http://localhost:8000/admin/ai-chatbot/feedback`

---

## 👨‍🎓 TÀI KHOẢN SINH VIÊN

### Sinh Viên Test
- **Email:** `sinhvien@smis.edu.vn`
- **Password:** `password`
- **Mã SV:** SV2025001
- **Họ tên:** Nguyễn Văn Sinh Viên
- **Vai trò:** Sinh viên

**Truy cập:**
- URL đăng nhập: `http://localhost:8000/sinh-vien/login`
- Dashboard: `http://localhost:8000/sinh-vien/dashboard`

**Chức năng AI Chatbot:**
- Chat với AI: `http://localhost:8000/sinh-vien/chatbot`
- Lịch sử chat: `http://localhost:8000/sinh-vien/chatbot/history`

---

## 👨‍🏫 TÀI KHOẢN GIẢNG VIÊN (Nếu cần)

### Giảng Viên Test
- **Email:** Kiểm tra trong GiangVienSeeder
- **Password:** `password`

**Truy cập:**
- URL đăng nhập: `http://localhost:8000/giang-vien/login`

---

## 🏢 TÀI KHOẢN ĐÀO TẠO (Nếu cần)

### Trưởng Phòng Đào Tạo
- **Email:** `truongphong@smis.edu.vn`
- **Password:** `password`

### Nhân Viên Đào Tạo
- **Email:** `nhanvien@smis.edu.vn`
- **Password:** `password`

**Truy cập:**
- URL đăng nhập: `http://localhost:8000/dao-tao/login`
- Dashboard: `http://localhost:8000/dao-tao/dashboard`

---

## 🧪 HƯỚNG DẪN TEST AI CHATBOT

### Bước 1: Đăng nhập Admin
1. Truy cập: `http://localhost:8000/admin/login`
2. Nhập:
   - Email: `admin@smis.edu.vn`
   - Password: `password`
3. Vào menu "AI Chatbot" → "Knowledge Base"
4. Kiểm tra có **20 bản ghi** knowledge base

### Bước 2: Đăng nhập Sinh viên
1. Truy cập: `http://localhost:8000/sinh-vien/login`
2. Nhập:
   - Email: `sinhvien@smis.edu.vn`
   - Password: `password`
3. Vào menu "Trợ lý ảo AI" (có badge màu xanh "New")
4. Click "Bắt đầu chat"

### Bước 3: Test Chat
Thử hỏi các câu sau:
- "Làm thế nào để đăng ký môn học?"
- "Thời gian đăng ký môn học khi nào?"
- "Học phí tính như thế nào?"
- "Xem lịch thi ở đâu?"
- "Điểm thi khi nào có?"
- "Làm đơn xin nghỉ học ở đâu?"

### Bước 4: Đánh giá Bot
- Sau mỗi câu trả lời, click 👍 (hữu ích) hoặc 👎 (không hữu ích)
- Ghi lý do đánh giá (tùy chọn)

### Bước 5: Kiểm tra từ Admin
1. Quay lại tài khoản Admin
2. Vào "Hội thoại" → Xem cuộc trò chuyện vừa tạo
3. Vào "Feedback" → Xem đánh giá từ sinh viên
4. Vào "Thống kê" → Xem KB nào được sử dụng nhiều

---

## 📝 GHI CHÚ

- Tất cả mật khẩu đều là: **`password`**
- Nếu quên mật khẩu, chạy lại seeder tương ứng
- Database đã có 20 câu hỏi mẫu về:
  - Đăng ký môn học
  - Lịch học & Lịch thi
  - Học phí
  - Điểm & Kết quả học tập
  - Quy chế đào tạo
  - Thủ tục hành chính
  - Chương trình đào tạo

---

## 🔧 KHẮC PHỤC SỰ CỐ

Nếu không đăng nhập được:

### Chạy lại Seeder Admin:
```bash
php artisan db:seed --class=AdminSeeder
```

### Chạy lại Seeder Sinh viên:
```bash
php artisan db:seed --class=SinhVienSeeder
```

### Xóa cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

**Chúc bạn test thành công! 🎉**
