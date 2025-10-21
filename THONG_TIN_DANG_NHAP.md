# THÔNG TIN ĐĂNG NHẬP HỆ THỐNG S-MIS21

## 🔐 Tài khoản đăng nhập mẫu

Tất cả tài khoản đều sử dụng mật khẩu: **123456**

### 1. Admin (Quản trị viên)

-   **Email:** admin@smis.edu.vn
-   **Password:** 123456
-   **Vai trò:** Quản trị hệ thống, quản lý tài khoản, phân quyền
-   **Dashboard:** `/admin/dashboard`

### 2. Trưởng phòng Đào tạo

-   **Email:** truongphong@smis.edu.vn
-   **Password:** 123456
-   **Vai trò:** Quản lý toàn bộ hoạt động đào tạo, phê duyệt các quyết định quan trọng
-   **Dashboard:** `/dao-tao/dashboard`

### 3. Nhân viên Đào tạo

-   **Email:** nhanvien@smis.edu.vn
-   **Password:** 123456
-   **Vai trò:** Thực hiện các nghiệp vụ đào tạo hàng ngày
-   **Dashboard:** `/dao-tao/dashboard`

### 4. Giảng viên

-   **Email:** giangvien@smis.edu.vn
-   **Password:** 123456
-   **Vai trò:** Giảng dạy, chấm điểm, quản lý lớp học
-   **Dashboard:** `/giang-vien/dashboard`

### 5. Sinh viên

-   **Email:** sinhvien@smis.edu.vn
-   **Password:** 123456
-   **Vai trò:** Học tập, đăng ký học phần, xem kết quả học tập
-   **Dashboard:** `/sinh-vien/dashboard`

---

## 🚀 Hướng dẫn sử dụng

### Bước 1: Khởi động Laragon

-   Mở Laragon
-   Start All (Apache + MySQL)

### Bước 2: Truy cập hệ thống

-   Mở trình duyệt và truy cập: `http://s-mis21.test` hoặc `http://localhost/S-MIS21/public`
-   Sẽ tự động chuyển đến trang đăng nhập

### Bước 3: Đăng nhập

-   Chọn một trong các tài khoản ở trên
-   Nhập email và password
-   Click "Đăng nhập"
-   Hệ thống sẽ tự động chuyển đến dashboard tương ứng với vai trò

---

## 📋 Cấu trúc vai trò và phân quyền

### Mức độ ưu tiên (1-5):

1. **Admin** (Mức 1 - Cao nhất)
    - Quản lý toàn bộ hệ thống
    - Không tham gia vào nghiệp vụ đào tạo
2. **Trưởng phòng Đào tạo** (Mức 2)
    - Quản lý toàn bộ hoạt động đào tạo
    - Phê duyệt các quyết định quan trọng
3. **Nhân viên Đào tạo** (Mức 3)
    - Thực hiện nghiệp vụ đào tạo hàng ngày
    - Không có quyền phê duyệt cấp cao
4. **Giảng viên** (Mức 4)
    - Giảng dạy và quản lý lớp học
    - Chấm điểm, điểm danh
5. **Sinh viên** (Mức 5 - Thấp nhất)
    - Đăng ký học phần
    - Xem kết quả học tập

---

## 🔧 Lệnh hữu ích

### Reset database và tạo lại dữ liệu mẫu:

```bash
php artisan migrate:fresh --seed
```

### Chỉ chạy seeder (không xóa dữ liệu):

```bash
php artisan db:seed
```

### Tạo thêm tài khoản mới:

```bash
php artisan tinker
```

Sau đó trong tinker:

```php
$user = \App\Models\User::create([
    'name' => 'Tên người dùng',
    'email' => 'email@example.com',
    'password' => bcrypt('123456'),
    'trang_thai' => 'hoat_dong'
]);

$vaiTro = \App\Models\VaiTro::where('ma_vai_tro', 'sinh_vien')->first();
$user->vaiTro()->attach($vaiTro->id, ['ngay_gan' => now()]);
```

---

## 📧 Cấu hình Email (Quên mật khẩu)

Để chức năng "Quên mật khẩu" hoạt động, cần cấu hình trong file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=conjvayba@gmail.com
MAIL_PASSWORD=your-gmail-app-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=conjvayba@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Lưu ý:** Cần tạo App Password từ Google Account để sử dụng Gmail SMTP.

---

## 🎨 Tính năng đã hoàn thành

-   ✅ Đăng nhập với phân quyền
-   ✅ Quên mật khẩu (gửi email)
-   ✅ Middleware kiểm tra vai trò
-   ✅ Dashboard riêng cho từng actor
-   ✅ Sidebar riêng cho từng actor
-   ✅ Layout riêng cho từng actor
-   ✅ Auto redirect theo vai trò sau khi đăng nhập

---

**Phát triển bởi:** Đồ án tốt nghiệp 2025
**Repository:** https://github.com/MTuanPro/DATN_2025
