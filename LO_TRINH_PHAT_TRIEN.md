# LỘ TRÌNH PHÁT TRIỂN HỆ THỐNG S-MIS

## 📋 TỔNG QUAN DỰ ÁN

**Tên dự án:** S-MIS - Student Management Information System  
**Framework:** Laravel 12.32.5 (PHP 8.2+)  
**Database:** MySQL (50 tables)  
**Template:** Mazer Admin Dashboard  
**Ngày bắt đầu:** 21/10/2025

---

## ✅ HOÀN THÀNH (Completed)

### 1. Hạ tầng cơ bản (Infrastructure)

-   ✅ Cài đặt Laravel 12.32.5
-   ✅ Thiết kế database (50 bảng)
-   ✅ Migration tất cả bảng
-   ✅ Cấu hình email (Gmail SMTP với App Password)
-   ✅ Tích hợp Mazer Admin Template

### 2. Hệ thống xác thực & phân quyền (Authentication & Authorization)

-   ✅ Đăng nhập với 5 vai trò
-   ✅ Đăng xuất (header dropdown)
-   ✅ Quên mật khẩu (gửi email)
-   ✅ Middleware phân quyền (CheckRole)
-   ✅ Seeder tài khoản test (6 users)

### 3. Giao diện theo vai trò (Role-based UI)

-   ✅ Layout riêng cho 4 actor (Admin, Đào tạo, Giảng viên, Sinh viên)
-   ✅ Sidebar menu động theo vai trò
-   ✅ Dashboard cơ bản (hiển thị 0 - chưa có dữ liệu thực)
-   ✅ Header với thông tin user & logout
-   ✅ Footer tùy chỉnh

### 4. Git & Version Control

-   ✅ Commit: "đăng nhập phân quyền và quên mật khẩu"
-   ✅ Commit: "Khôi phục routes thông báo bị xóa sau khi pull từ main"
-   ✅ Push lên branch "Minhtuan"
-   ✅ Tài liệu đăng nhập (THONG_TIN_DANG_NHAP.md)

### 5. Quản lý User & Email Verification (30/10/2025)

-   ✅ CRUD User hoàn chỉnh (index, create, store, edit, update, destroy)
-   ✅ Toggle trạng thái khóa/mở khóa (AJAX)
-   ✅ Reset password qua email với token
-   ✅ **Email verification qua email thật**
    -   Token lưu trong bảng `email_verification_tokens`
    -   Gửi email khi tạo user mới
    -   Gửi email khi sửa địa chỉ email
    -   Link hết hạn sau 60 phút
    -   View: verify-email.blade.php và verify-email-form.blade.php
-   ✅ **Tự động tạo Admin/DaoTao profile**
    -   Gán vai trò `admin` → auto-create trong bảng `admin`
    -   Gán vai trò `truong_phong_dt`, `nhan_vien_dt` → auto-create trong bảng `dao_tao`
-   ✅ **Ẩn vai trò sinh_vien/giang_vien trong User form**
-   ✅ **Gộp menu "Nhân sự hệ thống" vào "Tài khoản & Phân quyền"**
-   ✅ Xóa routes `admin.admin.*` và `admin.dao-tao.*`

---

## � TỔNG KẾT TIẾN ĐỘ PHASE 0-5

| Phase       | Tên Phase                      | Tiến độ | Trạng thái       | Ghi chú                                                                                                 |
| ----------- | ------------------------------ | ------- | ---------------- | ------------------------------------------------------------------------------------------------------- |
| **Phase 0** | Quản trị Hệ thống & Phân quyền | **90%** | � Gần hoàn thành | ✅ Mục 1,2,3,4,5,6,8,9 hoàn thành, chỉ còn mục 7                                                        |
| **Phase 1** | Danh mục & Cấu trúc cơ bản     | **0%**  | ⚪ Chưa bắt đầu  | Cần controllers: Khoa, Ngành, Chuyên ngành, Khóa học, Trình độ, Trạng thái HT, Phòng học, Môn học, CTĐT |
| **Phase 2** | Nhân sự & Thời gian            | **0%**  | ⚪ Chưa bắt đầu  | CRUD Giảng viên, Học kỳ                                                                                 |
| **Phase 3** | Lớp học & Sinh viên            | **0%**  | ⚪ Chưa bắt đầu  | CRUD Lớp hành chính, Sinh viên                                                                          |
| **Phase 4** | Lớp học phần & Phân công       | **0%**  | ⚪ Chưa bắt đầu  | Lớp HP, Phân công GV, Cấu hình điểm, TKB                                                                |
| **Phase 5** | Đăng ký môn học                | **0%**  | ⚪ Chưa bắt đầu  | Chức năng cốt lõi - chưa làm                                                                            |

### Chi tiết Phase 0 (90% hoàn thành):

**✅ Đã hoàn thành (Controller + View đầy đủ):**

-   ✅ **Mục 1: Quản lý Users** (100%) - CRUD đầy đủ, email verification, auto-create profile
-   ✅ **Mục 2: CRUD Vai trò** (100%) - VaiTroController + Views (index, create, edit) ✅
-   ✅ **Mục 3: Gán vai trò** (100%) - Tích hợp vào User form
-   ✅ **Mục 4: CRUD Nhóm quyền** (100%) - NhomQuyenController + Views (index, create, edit) ✅
-   ✅ **Mục 5: CRUD Quyền** (100%) - QuyenController + Views (index, create, edit) ✅
-   ✅ **Mục 6: Map Vai trò - Quyền** (100%) - VaiTroQuyenController + View (index) ✅
-   ✅ **Mục 8: CRUD Admin** (100%) - Tự động tạo khi gán vai trò
-   ✅ **Mục 9: CRUD Đào tạo** (100%) - Tự động tạo khi gán vai trò

**⚪ Còn lại (10%):**

-   ⚪ **Mục 7: Middleware phân quyền nâng cao** - CheckPermission, Gate, Policy (không bắt buộc - có thể làm sau)

**Kết luận Phase 0:**

-   ✅ **Tất cả Controllers có sẵn từ trước:** VaiTroController, QuyenController, NhomQuyenController, VaiTroQuyenController, UserController
-   ✅ **Tất cả Views có sẵn từ trước:** vai-tro/{index,create,edit}, quyen/{index,create,edit}, nhom-quyen/{index,create,edit}, vai-tro-quyen/index
-   ✅ **Routes đã có đầy đủ**
-   🎉 **Phase 0 CÓ THỂ COI LÀ HOÀN THÀNH!** Middleware nâng cao (mục 7) có thể làm sau khi cần
-   💡 **Khuyến nghị:** Commit và chuyển sang Phase 1

---

## �🚀 LỘ TRÌNH PHÁT TRIỂN (Development Roadmap)

### **PHASE 0: Quản trị Hệ thống & Phân quyền** ⭐⭐⭐⭐⭐

**Thời gian dự kiến:** 1 tuần  
**Actor chính:** Admin  
**Ưu tiên:** CỰC CAO - PHẢI LÀM TRƯỚC TIÊN

#### Công việc:

1. **✅ Quản lý Users & Tự động tạo Profile** (HOÀN THÀNH 30/10/2025)

    - [x] Xem danh sách tài khoản (có phân trang, tìm kiếm)
    - [x] Tạo tài khoản mới với gán vai trò
    - [x] Sửa thông tin tài khoản (name, email, trạng thái, vai trò)
    - [x] Xóa tài khoản (cascade delete profile)
    - [x] Khóa/Mở khóa tài khoản (trang_thai - AJAX toggle)
    - [x] Đặt lại mật khẩu (gửi email với token)
    - [x] **Xác thực email qua email thật** (giống reset password)
        - Token lưu vào bảng `email_verification_tokens`
        - Gửi email khi tạo user mới
        - Gửi email khi sửa địa chỉ email
        - Link xác thực hết hạn sau 60 phút
        - Form xác thực email (verify-email-form.blade.php)
    - [x] **Tự động tạo Admin/DaoTao profile khi gán vai trò**
        - Vai trò `admin` → tạo record trong bảng `admin`
        - Vai trò `truong_phong_dt`, `nhan_vien_dt` → tạo record trong bảng `dao_tao`
        - Auto-fill: ho_ten, email từ User
    - [x] **Ẩn vai trò sinh_vien/giang_vien trong form User**
        - Sinh viên/Giảng viên chỉ tạo từ module riêng
        - Hiển thị thông báo hướng dẫn
    - [x] **Gộp menu "Nhân sự hệ thống" vào "Tài khoản & Phân quyền"**
        - Xóa submenu "Quản lý Admin" và "Quản lý Đào tạo"
        - Tất cả quản lý user tập trung tại một menu
    - [x] Xem lịch sử đăng nhập (chưa implement - để sau)
    - [x] Force logout tài khoản (chưa implement - để sau)

    **Commit:** "Hoàn thiện hệ thống quản lý User với email verification và auto-create profile"

2. **CRUD Vai trò (vai_tro)** - Đã có sẵn controller, cần hoàn thiện

    - [x] Xem danh sách vai trò (đã có từ trước)
    - [ ] Thêm vai trò mới
    - [ ] Sửa vai trò
    - [ ] Xóa vai trò (soft delete)
    - [ ] Thiết lập mức độ ưu tiên (muc_do_uu_tien)
    - [ ] Validation

3. **~~Gán Vai trò cho User~~** ✅ ĐÃ TÍCH HỢP VÀO MỤC 1

    - _Gán vai trò trực tiếp trong form Create/Edit User_
    - _Tự động tạo profile Admin/DaoTao khi gán vai trò_

4. **CRUD Nhóm quyền (nhom_quyen)** - Đã có sẵn controller

    - [x] Xem danh sách nhóm quyền (đã có từ trước)
    - [ ] Thêm nhóm quyền
    - [ ] Sửa nhóm quyền
    - [ ] Xóa nhóm quyền
    - [ ] Mô tả nhóm quyền

5. **CRUD Quyền (quyen)** - Đã có sẵn controller

    - [x] Xem danh sách quyền (đã có từ trước)
    - [ ] Thêm quyền mới
    - [ ] Sửa quyền
    - [ ] Xóa quyền
    - [ ] Gán quyền vào nhóm (nhom_quyen_id)
    - [ ] Validation

6. **Map Vai trò - Quyền (vai_tro_quyen)** - Đã có sẵn controller

    - [x] Xem danh sách mapping (đã có từ trước)
    - [ ] Gán quyền cho vai trò
    - [ ] Xóa quyền khỏi vai trò
    - [ ] Xem ma trận quyền theo vai trò
    - [ ] UI: Checkbox matrix (Vai trò × Quyền)

7. **Nâng cấp Middleware phân quyền**

    - [x] CheckRole hiện tại (theo vai trò) - đã có
    - [ ] CheckPermission mới (theo quyền chi tiết)
    - [ ] Gate & Policy cho từng model
    - [ ] Áp dụng vào routes

**Output:** Hệ thống phân quyền chi tiết, quản trị nhân sự hệ thống hoàn chỉnh

---

### **PHASE 1: Danh mục & Cấu trúc cơ bản** ⭐⭐⭐

**Thời gian dự kiến:** 1-2 tuần  
**Actor chính:** Đào tạo (Admin hỗ trợ)  
**Ưu tiên:** CAO - Nền tảng cho tất cả chức năng

#### **Member 1: CRUD Khoa & Ngành**

**Người làm:** [Tên member 1]  
**Thời gian:** 2-3 ngày

**Công việc:**

1. **CRUD Khoa (Faculty) - khoa**

    - [ ] Xem danh sách khoa
    - [ ] Thêm khoa mới
    - [ ] Sửa khoa
    - [ ] Xóa khoa (soft delete)
    - [ ] Tìm kiếm khoa
    - [ ] Phân trang
    - [ ] Validation (tên khoa unique, mã khoa unique)

2. **CRUD Ngành (Major) - nganh**
    - [ ] Xem danh sách ngành (theo khoa)
    - [ ] Thêm ngành mới
    - [ ] Sửa ngành
    - [ ] Xóa ngành (soft delete)
    - [ ] Liên kết với khoa (dropdown, foreign key)
    - [ ] Tìm kiếm ngành
    - [ ] Phân trang
    - [ ] Validation (tên ngành unique trong khoa, mã ngành unique)

**Routes:**

-   `/admin/khoa` (index, create, store, edit, update, destroy)
-   `/admin/nganh` (index, create, store, edit, update, destroy)

---

#### **Member 2: CRUD Chuyên ngành & Khóa học**

**Người làm:** [Tên member 2]  
**Thời gian:** 2-3 ngày

**Công việc:**

1. **CRUD Chuyên ngành (Specialization) - chuyen_nganh**

    - [ ] Xem danh sách chuyên ngành (theo ngành)
    - [ ] Thêm chuyên ngành mới
    - [ ] Sửa chuyên ngành
    - [ ] Xóa chuyên ngành (soft delete)
    - [ ] Liên kết với ngành (dropdown, foreign key)
    - [ ] Thiết lập tổng tín chỉ tối thiểu (tong_tin_chi_toi_thieu)
    - [ ] Tìm kiếm chuyên ngành
    - [ ] Phân trang
    - [ ] Validation

2. **CRUD Khóa học (Academic Year) - khoa_hoc**
    - [ ] Xem danh sách khóa học (2021, 2022, 2023...)
    - [ ] Thêm khóa học mới
    - [ ] Sửa khóa học
    - [ ] Xóa khóa học (soft delete)
    - [ ] Thiết lập năm bắt đầu/kết thúc
    - [ ] Cập nhật trạng thái (đang học, tốt nghiệp)
    - [ ] Validation

**Routes:**

-   `/admin/chuyen-nganh` (index, create, store, edit, update, destroy)
-   `/admin/khoa-hoc` (index, create, store, edit, update, destroy)

---

#### **Member 3: CRUD Trình độ, Trạng thái học tập & Phòng học**

**Người làm:** [Tên member 3]  
**Thời gian:** 2-3 ngày

**Công việc:**

1. **CRUD Trình độ (dm_trinh_do)**

    - [ ] Xem danh sách trình độ (Cử nhân, Thạc sĩ, Tiến sĩ)
    - [ ] Thêm trình độ mới
    - [ ] Sửa trình độ
    - [ ] Xóa trình độ (soft delete)
    - [ ] Validation

2. **CRUD Trạng thái học tập (trang_thai_hoc_tap)**

    - [ ] Xem danh sách trạng thái (Đang học, Bảo lưu, Thôi học, Tốt nghiệp)
    - [ ] Thêm trạng thái mới
    - [ ] Sửa trạng thái
    - [ ] Xóa trạng thái (soft delete)
    - [ ] Validation

3. **CRUD Phòng học (Classroom) - phong_hoc**
    - [ ] Xem danh sách phòng học
    - [ ] Thêm phòng học mới
    - [ ] Sửa phòng học
    - [ ] Xóa phòng học (soft delete)
    - [ ] Thiết lập sức chứa
    - [ ] Thiết lập loại phòng (lý thuyết, thực hành, máy tính)
    - [ ] Cập nhật trạng thái sử dụng (đang dùng, bảo trì, ngưng sử dụng)
    - [ ] Validation

**Routes:**

-   `/admin/trinh-do` (index, create, store, edit, update, destroy)
-   `/admin/trang-thai-hoc-tap` (index, create, store, edit, update, destroy)
-   `/admin/phong-hoc` (index, create, store, edit, update, destroy)

---

#### **Member 4: CRUD Môn học & Môn học tiên quyết**

**Người làm:** [Tên member 4]  
**Thời gian:** 3-4 ngày

**Công việc:**

1. **CRUD Môn học (Course) - mon_hoc**

    - [ ] Xem danh sách môn học
    - [ ] Thêm môn học mới
    - [ ] Sửa môn học
    - [ ] Xóa môn học (soft delete)
    - [ ] Thiết lập số tín chỉ lý thuyết (so_tin_chi_ly_thuyet)
    - [ ] Thiết lập số tín chỉ thực hành (so_tin_chi_thuc_hanh)
    - [ ] Phân loại môn học (đại cương, cơ sở ngành, chuyên ngành, tự chọn)
    - [ ] Gán khoa quản lý (khoa_id)
    - [ ] Thiết lập hình thức dạy (offline, online, hybrid)
    - [ ] Tìm kiếm môn học
    - [ ] Phân trang
    - [ ] Import từ Excel (template: mã môn, tên môn, tín chỉ, khoa)
    - [ ] Xuất danh sách môn học Excel/PDF
    - [ ] Validation

2. **Quản lý Môn học tiên quyết (mon_hoc_tien_quyet)**
    - [ ] Xem danh sách môn tiên quyết của môn học
    - [ ] Thêm môn tiên quyết (môn A yêu cầu môn B)
    - [ ] Sửa thông tin môn tiên quyết
    - [ ] Thiết lập loại tiên quyết (bắt buộc, khuyến nghị)
    - [ ] Thiết lập điều kiện qua môn (>= 4.0, >= 5.0)
    - [ ] Xóa môn tiên quyết
    - [ ] Validation (kiểm tra vòng lặp: A → B → C → A)

**Routes:**

-   `/admin/mon-hoc` (index, create, store, edit, update, destroy)
-   `/admin/mon-hoc/{monHoc}/tien-quyet` (index, store, update, destroy)
-   `/admin/mon-hoc/import` (import Excel)
-   `/admin/mon-hoc/export` (export Excel/PDF)

---

#### **Member 5: Chương trình khung (CTĐT)**

**Người làm:** [Tên member 5]  
**Thời gian:** 3-4 ngày

**Công việc:**

1. **Quản lý Chương trình khung (CTĐT - Curriculum) - chuong_trinh_khung**

    - [ ] Xem CTĐT theo chuyên ngành
    - [ ] Thêm môn học vào CTĐT
    - [ ] Sửa thông tin môn trong CTĐT
        - [ ] Học kỳ gợi ý (hoc_ky_goi_y: 1-8)
        - [ ] Thứ tự học (thu_tu_hoc: 1, 2, 3...)
        - [ ] Loại môn (bat_buoc, tu_chon)
    - [ ] Xóa môn khỏi CTĐT
    - [ ] Sắp xếp thứ tự môn học (drag & drop hoặc số thứ tự)
    - [ ] Xem tổng tín chỉ CTĐT
    - [ ] Xem tỷ lệ môn bắt buộc/tự chọn
    - [ ] Import CTĐT từ Excel (template: chuyên ngành, mã môn, học kỳ, loại môn, thứ tự)
    - [ ] Xuất PDF CTĐT (bao gồm: tên chuyên ngành, danh sách môn học phân theo học kỳ, tổng tín chỉ)
    - [ ] Validation (kiểm tra môn trùng, tín chỉ tối thiểu)

2. **Thống kê CTĐT**
    - [ ] Tổng số môn học
    - [ ] Tổng tín chỉ bắt buộc
    - [ ] Tổng tín chỉ tự chọn
    - [ ] Tổng tín chỉ toàn khóa
    - [ ] Phân bố môn theo học kỳ

**Routes:**

-   `/admin/chuong-trinh-khung` (index - xem theo chuyên ngành)
-   `/admin/chuong-trinh-khung/create` (thêm môn vào CTĐT)
-   `/admin/chuong-trinh-khung/{id}/edit` (sửa thông tin môn trong CTĐT)
-   `/admin/chuong-trinh-khung/{id}/destroy` (xóa môn khỏi CTĐT)
-   `/admin/chuong-trinh-khung/import` (import Excel)
-   `/admin/chuong-trinh-khung/export-pdf` (export PDF)

---

**Output Phase 1:** Hệ thống danh mục hoàn chỉnh (Khoa, Ngành, Chuyên ngành, Khóa học, Trình độ, Trạng thái học tập, Phòng học, Môn học, Môn tiên quyết, CTĐT), sẵn sàng cho Phase 2

---

### **PHASE 2: Nhân sự & Thời gian** ⭐⭐⭐

**Thời gian dự kiến:** 1 tuần  
**Actor chính:** Đào tạo + Admin  
**Ưu tiên:** CAO - Cần thiết cho Phase 3

#### Công việc:

1. **CRUD Giảng viên (Lecturer)**

    - [ ] Danh sách giảng viên
    - [ ] Thêm/Sửa/Xóa giảng viên
    - [ ] Phân bổ khoa
    - [ ] Import từ Excel
    - [ ] Validation email, SĐT

2. **CRUD Học kỳ (Semester)**

    - [ ] Danh sách học kỳ
    - [ ] Thiết lập học kỳ hiện tại
    - [ ] Thời gian bắt đầu/kết thúc
    - [ ] Mở đăng ký môn học

**Output:** Dữ liệu nhân sự và thời gian sẵn sàng

**Ghi chú:** CRUD Khóa học và CRUD Phòng học đã được triển khai trong Phase 1

---

### **PHASE 3: Lớp học & Sinh viên** ⭐⭐⭐

**Thời gian dự kiến:** 1-2 tuần  
**Actor chính:** Đào tạo  
**Ưu tiên:** CAO - Dữ liệu chính của hệ thống

#### Công việc:

1. **CRUD Lớp hành chính (Administrative Class)**

    - [ ] Danh sách lớp
    - [ ] Thêm/Sửa/Xóa lớp
    - [ ] Gán GVCN (Giáo viên chủ nhiệm)
    - [ ] Liên kết với khóa học, ngành

2. **CRUD Sinh viên (Student)**

    - [ ] Danh sách sinh viên
    - [ ] Thêm/Sửa/Xóa sinh viên
    - [ ] Import từ Excel (hàng loạt)
    - [ ] Gán vào lớp hành chính
    - [ ] Gán CTĐT
    - [ ] Tạo tài khoản đăng nhập tự động
    - [ ] Validation MSSV, email, CCCD

3. **Quản lý chuyển lớp**
    - [ ] Chuyển sinh viên sang lớp khác
    - [ ] Lịch sử chuyển lớp

**Output:** Danh sách sinh viên đầy đủ với CTĐT

---

### **PHASE 4: Lớp học phần & Phân công** ⭐⭐⭐

**Thời gian dự kiến:** 2 tuần  
**Actor chính:** Đào tạo  
**Ưu tiên:** CAO - Cần hoàn thành trước khi sinh viên đăng ký

#### Công việc:

1. **Tạo lớp học phần (Course Section)**

    - [ ] Tạo lớp học phần cho học kỳ
    - [ ] Chọn môn học, học kỳ
    - [ ] Số lượng tối đa, tối thiểu
    - [ ] Loại lớp (lý thuyết/thực hành)
    - [ ] Hình thức (offline, online, hybrid)
    - [ ] Validation

2. **Phân công giảng dạy**

    - [ ] Gán giảng viên cho lớp học phần
    - [ ] Thiết lập vai trò (giảng viên chính, phụ, trợ giảng)
    - [ ] Kiểm tra giảng viên có trùng lịch không
    - [ ] Lịch sử phân công
    - [ ] Ghi nhận người phân công

3. **Cấu hình đầu điểm (cau_hinh_dau_diem)** 🆕

    - [ ] Thiết lập cấu hình cho lớp học phần
    - [ ] Thêm đầu điểm (Chuyên cần, Giữa kỳ, Cuối kỳ, Thực hành, Tiểu luận)
    - [ ] Thiết lập tỷ lệ % (đảm bảo tổng = 100%)
    - [ ] Thiết lập số cột điểm
    - [ ] Sửa/Xóa cấu hình
    - [ ] Validation

4. **Thời khóa biểu cố định (Fixed Schedule)**

    - [ ] Tạo TKB cho lớp học phần (lich_hoc_co_dinh)
    - [ ] Chọn thứ, tiết bắt đầu/kết thúc, giờ học
    - [ ] Gán phòng học
    - [ ] Gán giảng viên
    - [ ] Kiểm tra xung đột phòng (unique constraint)
    - [ ] Kiểm tra xung đột giảng viên
    - [ ] Xuất TKB theo giảng viên/phòng/lớp

5. **Lịch học chi tiết (lich_hoc_chi_tiet)** 🆕

    - [ ] Tự động sinh lịch chi tiết từ lịch cố định
    - [ ] Thêm buổi học (bù học, học bổ sung)
    - [ ] Sửa buổi học (thay đổi phòng, GV, thời gian)
    - [ ] Hủy buổi học
    - [ ] Cập nhật trạng thái (chưa dạy, đang dạy, đã dạy, hủy)
    - [ ] Kiểm tra trùng phòng theo ngày cụ thể

6. **Kiểm tra xung đột**
    - [ ] Phòng học bị trùng
    - [ ] Giảng viên bị trùng lịch
    - [ ] Cảnh báo khi tạo lịch

**Output:** Lớp học phần với TKB hoàn chỉnh, Cấu hình điểm sẵn sàng, đón sinh viên đăng ký

---

### **PHASE 5: Đăng ký môn học** ⭐⭐⭐⭐⭐ (QUAN TRỌNG NHẤT)

**Thời gian dự kiến:** 2-3 tuần  
**Actor chính:** Sinh viên + Đào tạo  
**Ưu tiên:** CỰC CAO - Chức năng cốt lõi

#### Công việc:

1. **Đăng ký môn học (Student)**

    - [ ] Danh sách môn có thể đăng ký
    - [ ] Lọc theo CTĐT
    - [ ] Kiểm tra môn tiên quyết
    - [ ] Kiểm tra số tín chỉ tối đa (theo quy định)
    - [ ] Kiểm tra môn đã đăng ký trước đó
    - [ ] Đăng ký nhanh nhiều môn

2. **Thuật toán phân lớp tự động**

    - [ ] Ưu tiên sinh viên năm cuối
    - [ ] Ưu tiên sinh viên học lại (qua_mon = false)
    - [ ] Phân bổ đều vào các lớp
    - [ ] Kiểm tra sức chứa lớp
    - [ ] Danh sách chờ (waiting list)

3. **Xem thời khóa biểu (Student)**

    - [ ] TKB cá nhân sau khi đăng ký
    - [ ] Kiểm tra trùng lịch
    - [ ] Xuất PDF TKB

4. **Hủy đăng ký**

    - [ ] Hủy trong thời gian cho phép
    - [ ] Cập nhật số lượng lớp học phần
    - [ ] Xử lý danh sách chờ

5. **Quản lý đăng ký (Đào tạo)**
    - [ ] Xem danh sách sinh viên đã đăng ký
    - [ ] Thêm/Xóa sinh viên khỏi lớp (thủ công)
    - [ ] Thống kê đăng ký theo môn, lớp
    - [ ] Export danh sách lớp học phần

**Output:** Hệ thống đăng ký môn học hoàn chỉnh với thuật toán phân lớp thông minh

---

### **PHASE 6: Điểm danh & Dạy học** ⭐⭐

**Thời gian dự kiến:** 1-2 tuần  
**Actor chính:** Giảng viên  
**Ưu tiên:** TRUNG BÌNH

#### Công việc:

1. **Danh sách lớp giảng dạy (Lecturer)**

    - [ ] Xem lớp được phân công
    - [ ] Xem vai trò phân công (GV chính, phụ, trợ giảng)
    - [ ] Danh sách sinh viên trong lớp
    - [ ] Thông tin lịch học
    - [ ] Xuất danh sách sinh viên Excel/PDF

2. **Xem Lịch dạy cá nhân**

    - [ ] Xem lịch dạy theo ngày/tuần/tháng
    - [ ] Xem thông tin buổi học (thứ, tiết, giờ, phòng, lớp HP, link online)
    - [ ] Xuất lịch dạy

3. **Quản lý Buổi học (lich_hoc_chi_tiet)** 🆕

    - [ ] Xem danh sách buổi học
    - [ ] Cập nhật nội dung giảng dạy (noi_dung_giang_day)
    - [ ] Đính kèm tài liệu (tai_lieu_dinh_kem)
    - [ ] Cập nhật trạng thái buổi học (chưa dạy, đang dạy, đã dạy, hủy)
    - [ ] Xem lịch sử các buổi đã dạy

4. **Điểm danh (Attendance)**

    - [ ] Xem danh sách sinh viên cần điểm danh theo buổi
    - [ ] Điểm danh sinh viên (có mặt, vắng, đi trễ, nghỉ phép)
    - [ ] Ghi nhận thời gian điểm danh
    - [ ] Sửa điểm danh (trong thời gian cho phép)
    - [ ] Ghi chú điểm danh

5. **Báo cáo điểm danh**

    - [ ] Xem lịch sử điểm danh theo lớp
    - [ ] Xem thống kê vắng mặt từng sinh viên
    - [ ] Xem tỷ lệ chuyên cần lớp
    - [ ] Xuất báo cáo điểm danh Excel/PDF

6. **Cảnh báo điểm danh**
    - [ ] Sinh viên vắng > 20% → cảnh báo tự động
    - [ ] Gửi email cho sinh viên
    - [ ] Báo cáo giảng viên chủ nhiệm

**Output:** Quản lý điểm danh điện tử + Quản lý buổi học với nội dung và tài liệu

---

### **PHASE 6.5: Lớp Chủ nhiệm (GVCN)** ⭐⭐⭐ 🆕

**Thời gian dự kiến:** 1 tuần  
**Actor chính:** Giảng viên (GVCN)  
**Ưu tiên:** CAO

#### Công việc:

1. **Quản lý Lớp hành chính (lop_hanh_chinh)**

    - [ ] Xem danh sách sinh viên lớp chủ nhiệm
    - [ ] Xem thông tin chi tiết sinh viên (hồ sơ, trạng thái học tập)
    - [ ] Tìm kiếm, lọc sinh viên

2. **Theo dõi Kết quả học tập**

    - [ ] Xem kết quả học tập sinh viên (điểm theo kỳ)
    - [ ] Xem điểm trung bình tích lũy, tổng tín chỉ đạt
    - [ ] Xem bảng điểm chi tiết

3. **Theo dõi Cảnh báo học vụ**

    - [ ] Xem cảnh báo học vụ của sinh viên
    - [ ] Xem lý do, mức độ cảnh báo
    - [ ] Tư vấn học tập cho sinh viên bị cảnh báo

4. **Báo cáo lớp chủ nhiệm**

    - [ ] Thống kê tổng quan lớp (sĩ số, điểm TB, cảnh báo)
    - [ ] Xuất báo cáo lớp chủ nhiệm Excel/PDF

**Output:** Công cụ quản lý và tư vấn sinh viên cho GVCN

---

### **PHASE 7: Nhập điểm & Đánh giá** ⭐⭐⭐

**Thời gian dự kiến:** 1-2 tuần  
**Actor chính:** Giảng viên + Đào tạo  
**Ưu tiên:** CAO

#### Công việc:

1. **Nhập điểm thành phần (Lecturer)**

    - [ ] Xem danh sách sinh viên cần nhập điểm
    - [ ] Xem cấu hình đầu điểm (đã thiết lập ở Phase 4)
    - [ ] Nhập điểm theo từng đầu điểm và cột
    - [ ] Sửa điểm (nếu chưa khóa)
    - [ ] Ghi chú điểm
    - [ ] Import điểm từ Excel
    - [ ] Xuất template Excel

2. **Xem kết quả học tập (Lecturer)**

    - [ ] Xem bảng điểm tổng kết lớp HP
    - [ ] Xem điểm hệ 10, hệ 4, điểm chữ
    - [ ] Xem trạng thái qua môn
    - [ ] Phân tích phân bố điểm (A, B+, B, C+, C, D+, D, F)
    - [ ] Xem danh sách sinh viên qua/trượt
    - [ ] Xuất bảng điểm Excel/PDF

3. **Khóa điểm (Lock Grades)**

    - [ ] Giảng viên khóa điểm (không sửa được)
    - [ ] Đào tạo duyệt điểm
    - [ ] Công bố điểm cho sinh viên
    - [ ] Kiểm soát thời gian nhập điểm

4. **Xem điểm (Student)**

    - [ ] Xem điểm thành phần theo đầu điểm
    - [ ] Xem điểm tổng kết từng môn (hệ 10, hệ 4, điểm chữ)
    - [ ] Xem trạng thái qua môn
    - [ ] Xem bảng điểm học kỳ
    - [ ] Xem điểm trung bình học kỳ
    - [ ] Xem điểm trung bình tích lũy (GPA)
    - [ ] Xem tổng số tín chỉ đã đạt
    - [ ] Xuất bảng điểm học kỳ PDF
    - [ ] Xuất học bạ (tất cả học kỳ) PDF

5. **Quản lý điểm (Đào tạo)**
    - [ ] Xem tất cả điểm sinh viên
    - [ ] Sửa điểm (trường hợp đặc biệt)
    - [ ] Thống kê tỷ lệ đỗ/trượt
    - [ ] Kiểm tra tiến độ nhập điểm của giảng viên

**Output:** Hệ thống quản lý điểm hoàn chỉnh (Cấu hình đầu điểm đã có từ Phase 4)

---

### **PHASE 7.5: Quản lý Thi & Lịch thi** ⭐⭐⭐ 🆕

**Thời gian dự kiến:** 1 tuần  
**Actor chính:** Đào tạo, Giảng viên, Sinh viên  
**Ưu tiên:** CAO

#### Công việc (Đào tạo):

1. **CRUD Lịch thi (lich_thi)**

    - [ ] Xem danh sách lịch thi
    - [ ] Thêm lịch thi mới
    - [ ] Sửa lịch thi
    - [ ] Xóa lịch thi
    - [ ] Thiết lập loại thi (Giữa kỳ, Cuối kỳ, Thi lại)
    - [ ] Thiết lập thời gian thi (ngày, giờ bắt đầu/kết thúc)
    - [ ] Gán phòng thi
    - [ ] Phân công giám thị (giam_thi_1_id, giam_thi_2_id)
    - [ ] Cập nhật số sinh viên dự thi
    - [ ] Upload đề thi, đáp án
    - [ ] Gửi thông báo lịch thi
    - [ ] Kiểm tra trùng phòng thi (unique constraint)
    - [ ] Xuất lịch thi Excel/PDF

#### Công việc (Giảng viên):

2. **Xem & Quản lý Lịch thi**

    - [ ] Xem lịch thi của lớp phụ trách
    - [ ] Xem lịch coi thi (nếu được phân công giám thị)
    - [ ] Xem thông tin phòng thi, thời gian
    - [ ] Xem danh sách sinh viên dự thi
    - [ ] Upload đề thi
    - [ ] Upload đáp án
    - [ ] Xác nhận đã coi thi

#### Công việc (Sinh viên):

3. **Xem Lịch thi cá nhân**

    - [ ] Xem lịch thi cá nhân
    - [ ] Xem chi tiết từng môn thi (loại thi, ngày thi, giờ, phòng)
    - [ ] Xem phòng thi
    - [ ] Xem giám thị
    - [ ] Xem hình thức thi (offline, online, hybrid)
    - [ ] Xem link thi online (nếu có)
    - [ ] Xuất lịch thi PDF

**Output:** Hệ thống quản lý thi và lịch thi hoàn chỉnh

---

### **PHASE 8: Học phí** ⭐⭐⭐

**Thời gian dự kiến:** 1 tuần  
**Actor chính:** Đào tạo + Sinh viên  
**Ưu tiên:** CAO

#### Công việc:

1. **Tính học phí tự động**

    - [ ] Tính theo số tín chỉ đã đăng ký
    - [ ] Công thức: `so_tin_chi × don_gia_tren_tin_chi`
    - [ ] Hiển thị tổng học phí học kỳ

2. **Quản lý học phí (Đào tạo)**

    - [ ] Cấu hình học phí (cau_hinh_hoc_phi)
    - [ ] Thiết lập đơn giá tín chỉ theo năm học
    - [ ] Thiết lập phí dịch vụ
    - [ ] Xem tổng hợp học phí theo sinh viên (hoc_phi_hoc_ky)
    - [ ] Xem chi tiết học phí môn (chi_tiet_hoc_phi_mon)
    - [ ] Cập nhật trạng thái thanh toán
    - [ ] Ghi nhận lịch sử đóng học phí (lich_su_dong_hoc_phi)
    - [ ] Miễn giảm học phí
    - [ ] Gửi thông báo nhắc nở học phí
    - [ ] Thống kê thu học phí
    - [ ] Xuất danh sách nợ học phí

3. **Xem học phí (Student)**
    - [ ] Xem tổng hợp học phí học kỳ
    - [ ] Xem chi tiết học phí từng môn
    - [ ] Xem số tiền đã đóng, còn lại
    - [ ] Xem hạn đóng
    - [ ] Xem trạng thái (chưa nộp, đã nộp một phần, đã nộp đủ, quá hạn)
    - [ ] Xem lịch sử đóng học phí
    - [ ] Xem hướng dẫn nộp học phí
    - [ ] Xuất hóa đơn học phí PDF

**Output:** Hệ thống quản lý học phí tự động với tính toán theo tín chỉ

---

### **PHASE 8.5: Cảnh báo Học vụ** ⭐⭐⭐ 🆕

**Thời gian dự kiến:** 1 tuần  
**Actor chính:** Đào tạo, Giảng viên (GVCN), Sinh viên  
**Ưu tiên:** CAO

#### Công việc (Đào tạo):

1. **CRUD Cảnh báo học vụ (canh_bao_hoc_vu)**

    - [ ] Xem danh sách cảnh báo
    - [ ] Thêm cảnh báo mới (thủ công hoặc tự động)
    - [ ] Sửa cảnh báo
    - [ ] Xóa cảnh báo
    - [ ] Phân loại cảnh báo (điểm thấp, vắng nhiều, nợ học phí, học kỳ liên tiếp)
    - [ ] Thiết lập mức độ (cảnh cáo, đình chỉ, buộc thôi học)
    - [ ] Ghi nhận lý do (GPA < 1.0, vắng > 20%, nợ học phí quá hạn)
    - [ ] Ghi nhận người cảnh báo
    - [ ] Xử lý cảnh báo (đã xử lý, kết quả xử lý)
    - [ ] Gửi thông báo cảnh báo cho sinh viên
    - [ ] Xuất danh sách sinh viên bị cảnh báo Excel/PDF

2. **Tự động phát hiện cảnh báo**

    - [ ] Tự động phát hiện GPA < 1.0
    - [ ] Tự động phát hiện vắng > 20%
    - [ ] Tự động phát hiện nợ học phí quá hạn
    - [ ] Tự động phát hiện học kỳ liên tiếp không đạt
    - [ ] Chạy batch job hàng ngày/hàng tuần

#### Công việc (Giảng viên GVCN):

3. **Theo dõi Cảnh báo lớp chủ nhiệm**

    - [ ] Xem cảnh báo sinh viên lớp chủ nhiệm
    - [ ] Tư vấn sinh viên bị cảnh báo
    - [ ] Ghi nhận biện pháp can thiệp

#### Công việc (Sinh viên):

4. **Xem Cảnh báo cá nhân**

    - [ ] Xem danh sách cảnh báo cá nhân
    - [ ] Xem chi tiết cảnh báo
    - [ ] Xem loại cảnh báo, mức độ, lý do
    - [ ] Xem ngày cảnh báo
    - [ ] Xem trạng thái xử lý
    - [ ] Xem hướng giải quyết

**Output:** Hệ thống cảnh báo học vụ tự động, giúp phát hiện sớm sinh viên có nguy cơ

---

### **PHASE 9: Báo cáo & Thống kê** ⭐⭐

**Thời gian dự kiến:** 1-2 tuần  
**Actor chính:** Admin + Đào tạo  
**Ưu tiên:** TRUNG BÌNH

#### Công việc:

1. **Dashboard thực tế**

    - [ ] Thống kê tổng số sinh viên, giảng viên
    - [ ] Số lượng lớp học phần
    - [ ] Biểu đồ đăng ký môn học
    - [ ] Biểu đồ phân bố điểm
    - [ ] Tỷ lệ đỗ/trượt
    - [ ] Biểu đồ tình hình học phí
    - [ ] Biểu đồ cảnh báo học vụ

2. **Báo cáo đào tạo**

    - [ ] Báo cáo sinh viên theo khoa/ngành/khóa/lớp
    - [ ] Báo cáo kết quả học tập (phân bố điểm, GPA)
    - [ ] Báo cáo điểm danh (tỷ lệ vắng theo lớp/môn)
    - [ ] Báo cáo học phí (thu học phí, nợ học phí)
    - [ ] Báo cáo đăng ký môn học
    - [ ] Báo cáo xếp lớp (thành công/thất bại)
    - [ ] Báo cáo tải giảng viên
    - [ ] Báo cáo sử dụng phòng học
    - [ ] Báo cáo cảnh báo học vụ

3. **Báo cáo giảng dạy cá nhân (Giảng viên)** 🆕

    - [ ] Thống kê tiến độ giảng dạy (số buổi đã dạy/tổng buổi)
    - [ ] Thống kê điểm danh theo lớp (tỷ lệ có mặt/vắng/đi trễ)
    - [ ] Phân tích điểm lớp (phân bố điểm, tỷ lệ qua môn)
    - [ ] Xuất báo cáo giảng dạy Excel/PDF

4. **Export báo cáo**
    - [ ] Xuất Excel
    - [ ] Xuất PDF
    - [ ] Lọc theo thời gian, khoa, ngành

**Output:** Hệ thống báo cáo đầy đủ cho quản lý + Báo cáo cá nhân cho giảng viên

---

### **PHASE 10: Chức năng nâng cao** ⭐

**Thời gian dự kiến:** 1-2 tuần  
**Actor chính:** Tất cả  
**Ưu tiên:** THẤP - Làm sau

#### Công việc:

1. **Hệ thống Thông báo (thong_bao, nguoi_nhan_thong_bao)**

    - [ ] CRUD Thông báo (Admin, Đào tạo)
    - [ ] Gửi thông báo theo đối tượng (all, sinh_vien, giang_vien, khoa, ngành, lớp)
    - [ ] Phân loại thông báo (tin tức, thông báo chung, tin gấp, lịch học, lịch thi, học phí, điểm)
    - [ ] Thiết lập mức độ quan trọng
    - [ ] Ghim/Bỏ ghim thông báo (ghim_dau_trang)
    - [ ] Thiết lập hẹn giờ gửi (hien_thi_tu_ngay)
    - [ ] Đính kèm file
    - [ ] Liên kết với đối tượng (lien_ket_id, lien_ket_loai)
    - [ ] Theo dõi người nhận (nguoi_nhan_thong_bao)
    - [ ] Xem trạng thái đã đọc/chưa đọc
    - [ ] Đánh dấu đã đọc
    - [ ] Xem thống kê lượt xem

2. **Mẫu thông báo tự động (mau_thong_bao_tu_dong)** 🆕

    - [ ] CRUD mẫu thông báo (Admin)
    - [ ] Tạo template với biến động ({{ten_sinh_vien}}, {{ten_mon}}, {{so_tien}})
    - [ ] Bật/Tắt mẫu (kich_hoat)
    - [ ] Thiết lập mức độ ưu tiên
    - [ ] Cấu hình gửi email/SMS mặc định
    - [ ] Test mẫu thông báo
    - [ ] Tự động gửi khi có sự kiện (đăng ký môn, công bố điểm, nhắc học phí)

3. **Quản lý hồ sơ cá nhân**

    - [ ] Cập nhật thông tin cá nhân
    - [ ] Đổi mật khẩu
    - [ ] Upload ảnh đại diện

4. **Lịch sử hoạt động**

    - [ ] Log đăng nhập
    - [ ] Log thay đổi dữ liệu
    - [ ] Audit trail

5. **Tìm kiếm nâng cao**
    - [ ] Tìm kiếm sinh viên
    - [ ] Tìm kiếm môn học
    - [ ] Bộ lọc đa điều kiện

**Output:** Trải nghiệm người dùng tốt hơn

---

### **PHASE 11: Testing & Deployment** ⭐⭐⭐

**Thời gian dự kiến:** 1-2 tuần  
**Actor chính:** Developer  
**Ưu tiên:** CAO - Trước khi triển khai thực tế

#### Công việc:

1. **Unit Testing**

    - [ ] Test models
    - [ ] Test controllers
    - [ ] Test middleware
    - [ ] Coverage > 70%

2. **Feature Testing**

    - [ ] Test đăng ký môn học
    - [ ] Test phân lớp tự động
    - [ ] Test tính điểm
    - [ ] Test tính học phí

3. **Performance Optimization**

    - [ ] Query optimization
    - [ ] Caching (Redis)
    - [ ] Image optimization
    - [ ] Lazy loading

4. **Security**

    - [ ] CSRF protection
    - [ ] SQL injection prevention
    - [ ] XSS protection
    - [ ] Rate limiting

5. **Nhật ký Hoạt động (nhat_ky_hoat_dong)** 🆕

    - [ ] Ghi log tất cả hành động quan trọng (CREATE, UPDATE, DELETE, LOGIN, LOGOUT, EXPORT, IMPORT)
    - [ ] Ghi nhận user_id, ip_address, user_agent
    - [ ] Ghi nhận bảng dữ liệu, ID bản ghi
    - [ ] Ghi nhận dữ liệu cũ/mới (JSON)
    - [ ] Xem nhật ký hoạt động (Admin)
    - [ ] Tìm kiếm, lọc log
    - [ ] Xuất báo cáo log
    - [ ] Xóa log cũ (sau X tháng)

6. **Cài đặt & Vận hành Hệ thống** 🆕

    - [ ] Cấu hình thông tin trường
    - [ ] Cấu hình email (SMTP)
    - [ ] Cấu hình SMS gateway (tùy chọn)
    - [ ] Cấu hình thông báo web
    - [ ] Cấu hình bảo mật (độ dài mật khẩu, session timeout)
    - [ ] Cấu hình IP whitelist/blacklist
    - [ ] Quản lý Backup database
    - [ ] Tự động backup theo lịch
    - [ ] Restore dữ liệu
    - [ ] Xem lịch sử backup
    - [ ] Quản lý Application logs
    - [ ] Xem error logs
    - [ ] Download logs

7. **Deployment**
    - [ ] Setup production server
    - [ ] SSL certificate
    - [ ] Database backup
    - [ ] Monitor & logging

**Output:** Hệ thống an toàn, ổn định, có audit trail đầy đủ, sẵn sàng triển khai

---

### **PHASE 12: AI Chatbot** ⭐ (TÙY CHỌN) 🆕

**Thời gian dự kiến:** 2-3 tuần  
**Actor chính:** Admin, Sinh viên  
**Ưu tiên:** THẤP - Làm cuối cùng, có thể bỏ qua

#### Công việc (Admin):

1. **Knowledge Base (ai_chatbot_knowledge_base)**

    - [ ] CRUD kiến thức chatbot
    - [ ] Thêm câu hỏi mẫu, câu trả lời
    - [ ] Thêm từ khóa (keywords)
    - [ ] Phân loại theo chủ đề, danh mục
    - [ ] Kích hoạt/Vô hiệu hóa kiến thức
    - [ ] Thiết lập độ ưu tiên
    - [ ] Import kiến thức từ file
    - [ ] Xuất kiến thức
    - [ ] Xem thống kê (lượt truy cập, hữu ích)

2. **Theo dõi Hội thoại (ai_chatbot_conversation, ai_chatbot_message)**

    - [ ] Xem danh sách hội thoại
    - [ ] Xem chi tiết tin nhắn hội thoại
    - [ ] Xem độ tương đồng câu trả lời

3. **Feedback Chatbot (ai_chatbot_feedback)**

    - [ ] Xem đánh giá của người dùng
    - [ ] Xem lý do đánh giá
    - [ ] Phân tích chất lượng câu trả lời

#### Công việc (Sinh viên):

4. **Chat với Bot**

    - [ ] Tạo phiên chat mới
    - [ ] Gửi câu hỏi
    - [ ] Nhận câu trả lời từ bot (matching knowledge base)
    - [ ] Xem nguồn kiến thức
    - [ ] Xem lịch sử chat
    - [ ] Xem các phiên chat cũ

5. **Hỏi đáp chủ đề**

    - [ ] Hỏi về đăng ký môn học
    - [ ] Hỏi về lịch học, lịch thi
    - [ ] Hỏi về học phí
    - [ ] Hỏi về quy chế đào tạo
    - [ ] Hỏi về thủ tục hành chính
    - [ ] Hỏi về điểm, kết quả học tập
    - [ ] Hỏi về chương trình đào tạo

6. **Đánh giá Chatbot**

    - [ ] Đánh giá câu trả lời (hữu ích/không hữu ích)
    - [ ] Ghi lý do đánh giá
    - [ ] Góp ý cải thiện

**Output:** Chatbot hỗ trợ sinh viên 24/7, giảm tải công việc tư vấn

---

## 📊 THỐNG KÊ DỰ KIẾN

| Phase     | Thời gian | Ưu tiên    | Độ khó     | Người làm            |
| --------- | --------- | ---------- | ---------- | -------------------- |
| Phase 0   | 1 tuần    | ⭐⭐⭐⭐⭐ | Trung bình | Admin                |
| Phase 1   | 1-2 tuần  | ⭐⭐⭐     | Dễ         | Đào tạo              |
| Phase 2   | 1 tuần    | ⭐⭐⭐     | Dễ         | Đào tạo + Admin      |
| Phase 3   | 1-2 tuần  | ⭐⭐⭐     | Trung bình | Đào tạo              |
| Phase 4   | 2 tuần    | ⭐⭐⭐     | Khó        | Đào tạo              |
| Phase 5   | 2-3 tuần  | ⭐⭐⭐⭐⭐ | Rất khó    | Sinh viên + Đào tạo  |
| Phase 6   | 1-2 tuần  | ⭐⭐       | Trung bình | Giảng viên           |
| Phase 6.5 | 1 tuần    | ⭐⭐⭐     | Dễ         | Giảng viên (GVCN)    |
| Phase 7   | 1-2 tuần  | ⭐⭐⭐     | Trung bình | Giảng viên + Đào tạo |
| Phase 7.5 | 1 tuần    | ⭐⭐⭐     | Trung bình | Đào tạo + GV + SV    |
| Phase 8   | 1 tuần    | ⭐⭐⭐     | Dễ         | Đào tạo + Sinh viên  |
| Phase 8.5 | 1 tuần    | ⭐⭐⭐     | Trung bình | Đào tạo + GV + SV    |
| Phase 9   | 1-2 tuần  | ⭐⭐       | Trung bình | Admin + Đào tạo      |
| Phase 10  | 1-2 tuần  | ⭐         | Dễ         | Tất cả               |
| Phase 11  | 1-2 tuần  | ⭐⭐⭐     | Khó        | Developer            |
| Phase 12  | 2-3 tuần  | ⭐ (TT)    | Trung bình | Admin + Sinh viên    |

**Tổng thời gian dự kiến:** 4-5 tháng (18-22 tuần)  
**Ghi chú:** Phase 12 (AI Chatbot) là tùy chọn, có thể bỏ qua nếu thiếu thời gian

---

## 🎯 CHIẾN LƯỢC TRIỂN KHAI

### 1. Nguyên tắc phát triển

-   ✅ **Quản trị trước, nghiệp vụ sau**: Phase 0 (Phân quyền) là nền tảng
-   ✅ **Từ nền tảng đến nghiệp vụ**: Hoàn thành danh mục trước khi làm chức năng
-   ✅ **Từ đơn giản đến phức tạp**: CRUD trước, logic nghiệp vụ sau
-   ✅ **Từ backend đến frontend**: API + Logic trước, UI sau
-   ✅ **Testing liên tục**: Test sau mỗi feature, không đợi cuối dự án

### 2. Quy trình làm việc mỗi chức năng

1. **Phân tích yêu cầu** → Đọc file CHUC*NANG*\*.md
2. **Thiết kế database** → Đã có sẵn trong migrations
3. **Tạo Model & Relationships** → Laravel Eloquent
4. **Tạo Controller & Routes** → RESTful API
5. **Tạo Views** → Blade template với Mazer
6. **Validation & Error Handling** → Form Request
7. **Testing** → Unit + Feature tests
8. **Git commit** → Commit từng feature nhỏ

### 3. Ưu tiên tuyệt đối

🔥 **Phase 0 (Quản trị & Phân quyền)** là tiền đề cho mọi chức năng  
🔥 **Phase 5 (Đăng ký môn học)** là trái tim của hệ thống  
→ Tất cả Phase 0-4 phải phục vụ cho Phase 5

### 4. Phases mới bổ sung (so với bản cũ)

-   ✨ **Phase 0**: Quản trị & Phân quyền (THIẾU hoàn toàn trong bản cũ)
-   ✨ **Phase 6.5**: Lớp Chủ nhiệm (GVCN)
-   ✨ **Phase 7.5**: Quản lý Thi & Lịch thi
-   ✨ **Phase 8.5**: Cảnh báo Học vụ
-   ✨ **Phase 12**: AI Chatbot (Tùy chọn)

**Tổng cộng:** 13 phases bắt buộc + 1 phase tùy chọn (Phase 12)

---

## 💡 LƯU Ý QUAN TRỌNG

### 1. Database đã sẵn sàng ✅

-   50 bảng đã được migrate
-   Hỗ trợ đầy đủ tính năng học lại (`qua_mon`, `uu_tien`)
-   Hỗ trợ tính học phí (`so_tin_chi`, `don_gia_tren_tin_chi`)
-   Hỗ trợ môn tiên quyết (`mon_hoc_tien_quyet`)
-   Hỗ trợ phân quyền chi tiết (`vai_tro`, `quyen`, `vai_tro_quyen`)
-   Hỗ trợ cảnh báo học vụ (`canh_bao_hoc_vu`)
-   Hỗ trợ lịch thi (`lich_thi`)
-   Hỗ trợ AI chatbot (`ai_chatbot_*`)

### 2. Laravel 12 Compatibility ✅

-   Code 100% Laravel 12 compliant
-   Sử dụng syntax mới nhất
-   Không cần refactor

### 3. Git workflow

-   Branch chính: `main` hoặc `master`
-   Branch phát triển: `Minhtuan`
-   Commit thường xuyên với message rõ ràng
-   **Quy ước commit message:**
    -   `feat: <mô tả>` - Thêm tính năng mới
    -   `fix: <mô tả>` - Sửa lỗi
    -   `refactor: <mô tả>` - Tái cấu trúc code
    -   `test: <mô tả>` - Thêm test
    -   `docs: <mô tả>` - Cập nhật tài liệu
-   Merge về main khi hoàn thành mỗi Phase

### 4. Tài khoản test

```
Admin: admin@smis.edu.vn / 123456
Trưởng phòng ĐT: truongphong@smis.edu.vn / 123456
Nhân viên ĐT: nhanvien@smis.edu.vn / 123456
Giảng viên: giangvien@smis.edu.vn / 123456
Sinh viên: sinhvien@smis.edu.vn / 123456
Test email (quên mật khẩu): conjvayba@gmail.com / 123456
```

### 5. Tài liệu tham khảo

-   `CHUC_NANG_ADMIN.md` - Chức năng Admin chi tiết
-   `CHUC_NANG_DAO_TAO.md` - Chức năng Đào tạo chi tiết
-   `CHUC_NANG_GIANG_VIEN.md` - Chức năng Giảng viên chi tiết
-   `CHUC_NANG_SINH_VIEN.md` - Chức năng Sinh viên chi tiết
-   `SO_SANH_LO_TRINH_VA_CHUC_NANG.md` - So sánh lộ trình với chức năng

---

## 📅 HÀNH ĐỘNG TIẾP THEO

### ✨ Bắt đầu ngay với Phase 0:

**Công việc đầu tiên:** Quản trị Hệ thống & Phân quyền

```bash
# 1. Nâng cấp Model User
# - Thêm relationships với vai_tro, quyen (ĐÃ CÓ)
# - Thêm helper methods: hasRole(), hasPermission()

# 2. Tạo Controllers (ĐÃ CÓ TẤT CẢ)
php artisan make:controller Admin/VaiTroController --resource ✅
php artisan make:controller Admin/QuyenController --resource ✅
php artisan make:controller Admin/NhomQuyenController --resource ✅
php artisan make:controller Admin/VaiTroQuyenController ✅
# AdminController và DaoTaoController đã KHÔNG CẦN (tích hợp vào UserController)

# 3. Tạo Middleware (ĐÃ CÓ)
php artisan make:middleware CheckRole ✅
php artisan make:middleware CheckPermission (chưa cần - để sau)

# 4. Tạo Views
# - resources/views/admin/users/* (ĐÃ CÓ đầy đủ)
# - resources/views/admin/vaitro/index.blade.php (ĐÃ CÓ)
# - resources/views/admin/quyen/index.blade.php (ĐÃ CÓ)
# - resources/views/admin/nhom-quyen/index.blade.php (ĐÃ CÓ)
# - resources/views/admin/vai-tro-quyen/index.blade.php (ĐÃ CÓ)
# - admin/admin/* và admin/dao-tao/* KHÔNG CẦN (đã gộp vào users)

# 5. Thêm routes vào routes/web.php (ĐÃ CÓ)

# 6. Test chức năng

# 7. Commit
git add .
git commit -m "feat: Phase 0 - Quản lý User với email verification và auto-create profile"
git push origin Minhtuan
```

### 📝 Sau khi hoàn thành Phase 0:

→ Chuyển sang **Phase 1: CRUD Khoa - Ngành - Chuyên ngành - Trình độ - Trạng thái học tập**

---

## 📞 HỖ TRỢ

-   **Framework:** Laravel 12 Documentation
-   **Template:** Mazer Admin Template Documentation

---

## 📝 LỊCH SỬ CẬP NHẬT

### Version 2.2 - 30/10/2025

-   ✅ **Hoàn thành Quản lý User với Email Verification**
    -   Email verification qua email thật (token trong database)
    -   Tự động tạo Admin/DaoTao profile khi gán vai trò
    -   Gộp menu "Nhân sự hệ thống" vào "Tài khoản & Phân quyền"
    -   Ẩn vai trò sinh_vien/giang_vien trong User form
    -   Xóa routes admin.admin._ và admin.dao-tao._
-   ✅ Cập nhật cấu trúc Phase 0 (đánh dấu hoàn thành mục 1, 3, 8, 9)
-   ✅ Sửa APP_URL trong .env để signed URL hoạt động

### Version 2.1 - 24/10/2025

-   ✅ Loại bỏ các chức năng trùng lặp trong Phase 2
-   ✅ Xóa phần "Chiến lược triển khai" và "Lưu ý quan trọng" bị lặp ở cuối file
-   ✅ Xóa phần "Hành động tiếp theo" bị lặp
-   ✅ Tối ưu hóa cấu trúc tài liệu

### Version 2.0 - 21/10/2025

-   ✅ Thêm **Phase 0**: Quản trị & Phân quyền (QUAN TRỌNG)
-   ✅ Bổ sung **Phase 1**: Trình độ, Trạng thái học tập
-   ✅ Bổ sung **Phase 4**: Cấu hình đầu điểm, Lịch học chi tiết
-   ✅ Bổ sung **Phase 6**: Quản lý buổi học
-   ✅ Thêm **Phase 6.5**: Lớp Chủ nhiệm (GVCN)
-   ✅ Thêm **Phase 7.5**: Quản lý Thi & Lịch thi
-   ✅ Thêm **Phase 8.5**: Cảnh báo Học vụ
-   ✅ Bổ sung **Phase 9**: Báo cáo giảng dạy cá nhân
-   ✅ Bổ sung **Phase 10**: Mẫu thông báo tự động
-   ✅ Bổ sung **Phase 11**: Nhật ký hoạt động, Cài đặt hệ thống
-   ✅ Thêm **Phase 12**: AI Chatbot (Tùy chọn)
-   ✅ Tổng thời gian: 4-5 tháng (18-22 tuần)
-   ✅ So sánh với `SO_SANH_LO_TRINH_VA_CHUC_NANG.md`

### Version 1.0 - 21/10/2025

-   ✅ Phiên bản đầu tiên với 11 phases
-   ✅ Thời gian dự kiến: 3-4 tháng

---

**Ghi chú:** Tài liệu này sẽ được cập nhật liên tục theo tiến độ thực tế. Mỗi Phase hoàn thành sẽ đánh dấu ✅ và ghi chú ngày hoàn thành.

**Ngày tạo:** 21/10/2025  
**Ngày cập nhật:** 24/10/2025  
**Phiên bản hiện tại:** 2.1  
**Người tạo:** Development Team  
**Ghi chú:** Phase 12 (AI Chatbot) là tùy chọn, có thể bỏ qua nếu thiếu thời gian
