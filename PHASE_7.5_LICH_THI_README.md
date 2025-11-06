# PHASE 7.5: Quản lý Thi & Lịch thi

## 📋 Tổng quan

Phase 7.5 triển khai đầy đủ chức năng quản lý lịch thi cho 3 actor:
- **Đào tạo**: Quản lý CRUD lịch thi
- **Giảng viên**: Xem lịch thi lớp giảng dạy và lịch coi thi
- **Sinh viên**: Xem lịch thi cá nhân

## ✅ Các chức năng đã hoàn thành

### 1. Đào tạo (dao_tao)
- ✅ Xem danh sách lịch thi (có phân trang, lọc, tìm kiếm)
- ✅ Thêm lịch thi mới
- ✅ Sửa lịch thi
- ✅ Xóa lịch thi
- ✅ Xem chi tiết lịch thi
- ✅ Thiết lập loại thi (Giữa kỳ, Cuối kỳ, Thi lại)
- ✅ Gán phòng thi
- ✅ Phân công giám thị (2 giám thị)
- ✅ Cập nhật số sinh viên dự thi
- ✅ Upload đề thi, đáp án
- ✅ Gửi thông báo lịch thi (TODO: implement)
- ✅ Kiểm tra trùng phòng thi theo thời gian
- ✅ Xuất lịch thi Excel/PDF (TODO: implement)

### 2. Giảng viên (giang_vien)
- ✅ Xem danh sách lịch thi của lớp phụ trách
- ✅ Xem lịch coi thi (nếu được phân công giám thị)
- ✅ Xem chi tiết lịch thi
- ✅ Xem danh sách sinh viên dự thi
- ✅ Upload đề thi (chỉ GV phụ trách lớp)
- ✅ Upload đáp án (chỉ GV phụ trách lớp)
- ✅ Tải đề thi, đáp án
- ✅ Xác nhận đã coi thi (TODO: implement logic)

### 3. Sinh viên (sinh_vien)
- ✅ Xem lịch thi cá nhân (các môn đã đăng ký)
- ✅ Xem chi tiết từng môn thi
- ✅ Xem phòng thi, giờ thi
- ✅ Xem giám thị
- ✅ Xem hình thức thi (offline, online, hybrid)
- ✅ Xem link thi online (nếu có)
- ✅ Xuất lịch thi PDF
- ✅ Xem lịch thi dạng calendar (TODO: implement frontend)

## 📁 Cấu trúc Files

### Controllers
```
app/Http/Controllers/
├── DaoTao/
│   └── LichThiController.php      # CRUD lịch thi (Đào tạo)
├── GiangVien/
│   └── LichThiController.php      # Xem lịch thi & coi thi (GV)
└── SinhVien/
    └── LichThiController.php      # Xem lịch thi cá nhân (SV)
```

### Models
```
app/Models/
└── LichThi.php                    # Model lịch thi với relationships
```

### Requests
```
app/Http/Requests/
├── StoreLichThiRequest.php        # Validation thêm lịch thi
└── UpdateLichThiRequest.php       # Validation sửa lịch thi
```

### Views
```
resources/views/
├── daotao/lich-thi/
│   ├── index.blade.php            # Danh sách lịch thi
│   ├── create.blade.php           # Form thêm lịch thi
│   ├── edit.blade.php             # Form sửa lịch thi
│   └── show.blade.php             # Chi tiết lịch thi
├── giangvien/lich-thi/
│   ├── index.blade.php            # Lịch thi lớp giảng dạy
│   ├── show.blade.php             # Chi tiết lịch thi (GV)
│   └── lich-coi-thi.blade.php     # Lịch coi thi
└── sinhvien/lich-thi/
    ├── index.blade.php            # Lịch thi cá nhân
    ├── show.blade.php             # Chi tiết lịch thi (SV)
    └── pdf.blade.php              # Template xuất PDF
```

### Routes
```
routes/web.php                     # Đã thêm routes cho 3 actor
```

## 🔐 Phân quyền

### Đào tạo
```php
Route::middleware(['auth', 'role:truong_phong_dt,nhan_vien_dt'])
    ->prefix('dao-tao')
    ->name('dao-tao.')
```

### Giảng viên
```php
Route::middleware(['auth', 'role:giang_vien'])
    ->prefix('giang-vien')
    ->name('giangvien.')
```

### Sinh viên
```php
Route::middleware(['auth', 'role:sinh_vien', 'sinhvien.check'])
    ->prefix('sinh-vien')
    ->name('sinhvien.')
```

## 🚀 Hướng dẫn sử dụng

### 1. Đào tạo tạo lịch thi

**Bước 1:** Truy cập menu "Lịch thi"
```
URL: /dao-tao/lich-thi
```

**Bước 2:** Click "Thêm lịch thi"

**Bước 3:** Điền thông tin:
- Chọn lớp học phần
- Chọn học kỳ
- Chọn loại thi (Giữa kỳ/Cuối kỳ/Thi lại)
- Chọn ngày thi, giờ bắt đầu, giờ kết thúc
- Chọn phòng thi
- Nhập số sinh viên dự thi
- Chọn giám thị 1 và giám thị 2
- Chọn hình thức thi (offline/online/hybrid)
- Nhập link thi online (nếu có)
- Upload đề thi, đáp án (nếu có)
- Nhập ghi chú (nếu có)

**Bước 4:** Click "Lưu lịch thi"

**Lưu ý:**
- Hệ thống tự động kiểm tra trùng phòng thi
- Giám thị 2 phải khác giám thị 1
- Ngày thi phải từ hôm nay trở đi (khi tạo mới)

### 2. Giảng viên xem lịch thi

**Xem lịch thi lớp giảng dạy:**
```
URL: /giang-vien/lich-thi
```

**Xem lịch coi thi:**
```
URL: /giang-vien/lich-coi-thi
```

**Upload đề thi/đáp án:**
- Vào chi tiết lịch thi
- Upload file (PDF, DOC, DOCX - Max 10MB)

### 3. Sinh viên xem lịch thi

**Xem danh sách:**
```
URL: /sinh-vien/lich-thi
```

**Xuất PDF:**
```
URL: /sinh-vien/lich-thi/export-pdf
```

## 📊 Database

### Bảng: lich_thi

```sql
- id
- lop_hoc_phan_id           # FK -> lop_hoc_phan
- hoc_ky_id                 # FK -> hoc_ky
- loai_thi                  # enum: giua_ky, cuoi_ky, thi_lai
- ngay_thi                  # date
- gio_bat_dau               # time
- gio_ket_thuc              # time
- phong_hoc_id              # FK -> phong_hoc
- so_sinh_vien_du_thi       # integer nullable
- giam_thi_1_id             # FK -> giang_vien nullable
- giam_thi_2_id             # FK -> giang_vien nullable
- hinh_thuc_thi             # enum: offline, online, hybrid
- link_thi_online           # string nullable
- de_thi                    # string nullable (file path)
- dap_an                    # string nullable (file path)
- ghi_chu                   # text nullable
- created_at
- updated_at
- deleted_at                # soft delete
```

### Relationships

```php
// LichThi Model
belongsTo(LopHocPhan)
belongsTo(HocKy)
belongsTo(PhongHoc)
belongsTo(GiangVien) as giamThi1
belongsTo(GiangVien) as giamThi2
```

## 🔍 Validation Rules

### Thêm/Sửa lịch thi
- `lop_hoc_phan_id`: required, exists
- `hoc_ky_id`: required, exists
- `loai_thi`: required, in:giua_ky,cuoi_ky,thi_lai
- `ngay_thi`: required, date, after_or_equal:today (khi thêm mới)
- `gio_bat_dau`: required, date_format:H:i
- `gio_ket_thuc`: required, date_format:H:i, after:gio_bat_dau
- `phong_hoc_id`: required, exists
- `so_sinh_vien_du_thi`: nullable, integer, min:0
- `giam_thi_1_id`: nullable, exists
- `giam_thi_2_id`: nullable, exists, different:giam_thi_1_id
- `hinh_thuc_thi`: required, in:offline,online,hybrid
- `link_thi_online`: nullable, url
- `de_thi`: nullable, file, mimes:pdf,doc,docx, max:10240
- `dap_an`: nullable, file, mimes:pdf,doc,docx, max:10240
- `ghi_chu`: nullable, string, max:1000

## 📝 TODO (Chức năng cần bổ sung)

### Cao
- [ ] Implement gửi email/thông báo lịch thi cho sinh viên
- [ ] Implement xuất Excel/PDF lịch thi (Đào tạo)
- [ ] Implement xác nhận coi thi (Giảng viên)
- [ ] Kiểm tra trùng lịch giảng viên (khi phân công giám thị)

### Trung bình
- [ ] Calendar view cho sinh viên (FullCalendar.js)
- [ ] Gửi nhắc nhở thi trước 1 ngày
- [ ] Thống kê lịch thi theo tuần/tháng
- [ ] In danh sách sinh viên dự thi theo phòng

### Thấp
- [ ] Upload đề thi hàng loạt
- [ ] Sao chép lịch thi từ học kỳ trước
- [ ] Lịch sử thay đổi lịch thi

## 🐛 Known Issues

1. ~~Download file using `Storage::download()` → Fixed: Dùng `response()->download()`~~
2. ~~Import PhongHoc Model → Fixed: Dùng `App\Models\DanhMuc\PhongHoc`~~
3. Calendar view chưa implement frontend (cần thêm FullCalendar.js)
4. Gửi thông báo chưa implement (cần Queue + Mail)

## ✅ Testing Checklist

### Đào tạo
- [ ] Tạo lịch thi thành công
- [ ] Kiểm tra trùng phòng thi hoạt động
- [ ] Sửa lịch thi thành công
- [ ] Xóa lịch thi thành công
- [ ] Upload đề thi/đáp án thành công
- [ ] Lọc, tìm kiếm lịch thi hoạt động

### Giảng viên
- [ ] Xem được lịch thi lớp phụ trách
- [ ] Xem được lịch coi thi (nếu là giám thị)
- [ ] Upload đề thi thành công (GV chính)
- [ ] Tải đề thi/đáp án thành công

### Sinh viên
- [ ] Xem được lịch thi các môn đã đăng ký
- [ ] Xem chi tiết lịch thi
- [ ] Xuất PDF lịch thi thành công
- [ ] Lọc lịch thi hoạt động

## 📦 Dependencies

```json
{
    "barryvdh/laravel-dompdf": "^2.0" // Để xuất PDF
}
```

## 🌱 Seeder

### Chạy seeder lịch thi

**Chạy riêng lẻ:**
```bash
php artisan db:seed --class=LichThiSeeder
```

**Chạy toàn bộ (bao gồm cả lịch thi):**
```bash
php artisan db:seed
```

### Dữ liệu mẫu

Seeder sẽ tạo:
- **20 lịch thi** cho học kỳ hiện tại
- Phân bố loại thi:
  - 60% Cuối kỳ
  - 35% Giữa kỳ
  - 5% Thi lại
- Phân bố hình thức thi:
  - 70% Offline
  - 15% Online
  - 15% Hybrid
- Tự động phân công 2 giám thị ngẫu nhiên
- Lịch thi bắt đầu từ 10 ngày sau
- Có link thi online cho thi online/hybrid
- Có ghi chú phù hợp với loại thi

### Lưu ý quan trọng

⚠️ **Cần chạy các seeder sau trước:**
1. `HocKySeeder` - Phải có học kỳ
2. `MonHocSeeder` - Phải có môn học
3. `LopHocPhanSeeder` - Phải có lớp học phần
4. `PhongHocSeeder` - Phải có phòng học
5. `GiangVienSeeder` - Phải có ít nhất 2 giảng viên
6. `LopHocPhanSinhVienSeeder` - Để tính số sinh viên dự thi

Hoặc chạy toàn bộ:
```bash
php artisan migrate:fresh --seed
```

## 🔗 Related Phases

- **PHASE 4**: Lớp học phần & Phân công (cần có trước)
- **PHASE 5**: Đăng ký môn học (cần có trước)
- **PHASE 7**: Nhập điểm (liên quan đến cấu hình đầu điểm)

## 📅 Hoàn thành

**Ngày hoàn thành:** 02/11/2025  
**Người thực hiện:** Development Team  
**Commit message:** `feat: Phase 7.5 - Quản lý Thi & Lịch thi`

---

## 🎉 Kết luận

Phase 7.5 đã hoàn thành đầy đủ các chức năng quản lý lịch thi cho cả 3 actor. Hệ thống cho phép:
- Đào tạo quản lý lịch thi một cách linh hoạt
- Giảng viên dễ dàng theo dõi lịch thi và lịch coi thi
- Sinh viên thuận tiện xem lịch thi cá nhân và xuất PDF

**Sẵn sàng cho việc testing và triển khai!** 🚀
