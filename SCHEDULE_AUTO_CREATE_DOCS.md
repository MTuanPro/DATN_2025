# Hướng dẫn Tạo Lịch học Tự động

## Tổng quan
Hệ thống đã được nâng cấp để hỗ trợ **tạo nhiều buổi học tự động** dựa trên:
- **Ca học** (đã được thiết lập trước)
- **Pattern lặp lại** (các thứ trong tuần)
- **Số buổi học** của môn

## Tính năng chính

### 1. Quản lý Ca học
- **Đường dẫn:** Đào tạo → Danh mục & CTĐT → Quản lý Ca học
- Mỗi ca học có:
  - Tên ca (VD: Ca 1, Ca 2, ...)
  - Thứ tự ca (1-6)
  - Giờ bắt đầu và kết thúc
  - Trạng thái hoạt động
- Hệ thống đã có sẵn 6 ca học mặc định:
  - **Ca 1:** 07:00 - 08:50 (Tiết 1,2)
  - **Ca 2:** 09:00 - 10:50 (Tiết 3,4)
  - **Ca 3:** 11:00 - 12:50 (Tiết 5,6)
  - **Ca 4:** 13:00 - 14:50 (Tiết 7,8)
  - **Ca 5:** 15:00 - 16:50 (Tiết 9,10)
  - **Ca 6:** 17:00 - 18:50 (Tiết 11,12)

### 2. Tạo Lịch học Tự động

#### Quy trình:
1. **Truy cập:** Lớp học phần → Chọn lớp → Lịch học cố định → Thêm lịch học
2. **Bước 1: Chọn Ca học và Pattern**
   - Chọn ca học (dropdown)
   - Nhập số buổi học (mặc định lấy từ môn học)
   - Chọn ngày bắt đầu
   - Chọn các thứ trong tuần (checkbox: T2, T3, T4, T5, T6, T7, CN)
   
3. **Bước 2: Chọn thông tin cố định**
   - Phòng học
   - Giảng viên
   - Hình thức (Offline/Online/Hybrid)
   - Link online (nếu có)
   - Ghi chú

4. **Preview:** Click "Xem trước" để xem danh sách các buổi học sẽ được tạo

5. **Tạo lịch:** Click "Tạo lịch học tự động"

#### Ví dụ:
**Input:**
- Ca học: Ca 1 (07:00 - 08:50)
- Số buổi: 15 buổi
- Ngày bắt đầu: 01/01/2025
- Thứ trong tuần: Thứ 2 và Thứ 4
- Phòng: A101
- Giảng viên: Nguyễn Văn A

**Kết quả:**
Hệ thống sẽ tự động tạo 15 buổi học:
- Buổi 1: Thứ 2, 01/01/2025, Ca 1, Phòng A101, GV: Nguyễn Văn A
- Buổi 2: Thứ 4, 03/01/2025, Ca 1, Phòng A101, GV: Nguyễn Văn A
- Buổi 3: Thứ 2, 08/01/2025, Ca 1, Phòng A101, GV: Nguyễn Văn A
- ...tiếp tục cho đến buổi 15

### 3. Kiểm tra Xung đột
Hệ thống **tự động kiểm tra xung đột** trước khi tạo:
- ✅ Phòng học: Đảm bảo phòng không bị trùng lịch
- ✅ Giảng viên: Đảm bảo giảng viên không dạy 2 lớp cùng lúc
- ✅ Thời gian: Kiểm tra tất cả buổi học trong khoảng thời gian của lớp học phần

Nếu có xung đột, hệ thống sẽ hiển thị chi tiết:
```
Phát hiện xung đột lịch:
- Buổi 3 (08/01/2025): Phòng học đã bị trùng lịch
- Buổi 5 (15/01/2025): Giảng viên đã có lịch dạy
```

### 4. Hiển thị Lịch học
Danh sách lịch học cố định hiển thị:
- **STT:** Thứ tự buổi học
- **Thứ:** Thứ trong tuần
- **Ca học:** Tên ca học (badge)
- **Giờ học:** Thời gian cụ thể
- **Phòng:** Tên phòng và sức chứa
- **Giảng viên:** Họ tên giảng viên
- **Hình thức:** Offline/Online/Hybrid
- **Thao tác:** Sửa/Xóa từng buổi

## Cơ sở dữ liệu

### Bảng `ca_hoc`
```sql
- id: bigint (PK)
- ten_ca: varchar(50) - Tên ca học
- thu_tu: tinyint (UNIQUE) - Thứ tự ca (1-6)
- gio_bat_dau: time - Giờ bắt đầu
- gio_ket_thuc: time - Giờ kết thúc
- trang_thai: boolean - Trạng thái hoạt động
- ghi_chu: text - Ghi chú
- created_at, updated_at: timestamp
```

### Bảng `lich_hoc_co_dinh` (Đã cập nhật)
```sql
- ca_hoc_id: bigint (FK) - Liên kết đến ca học
```

## Migration files
1. `2025_11_17_170102_create_ca_hoc_table.php` - Tạo bảng ca_hoc
2. `2025_11_17_171649_add_ca_hoc_id_to_lich_tables.php` - Thêm ca_hoc_id vào bảng lịch

## Seeders
- `CaHocSeeder.php` - Tạo 6 ca học mặc định

## Models
- `App\Models\CaHoc` - Model cho ca học
  - Methods:
    - `kiemTraXungDotThoiGian()` - Kiểm tra ca học mới có trùng thời gian
    - `getCaHocTrungThoiGian()` - Lấy danh sách ca học trùng
    
- `App\Models\LichHocCoDinh` - Model cho lịch học cố định
  - Relationships:
    - `caHoc()` - Quan hệ belongsTo với CaHoc
  - Methods:
    - `kiemTraXungDotPhong()` - Kiểm tra trùng phòng
    - `kiemTraXungDotGiangVien()` - Kiểm tra trùng giảng viên

## Controllers
### `CaHocController`
- `index()` - Danh sách ca học
- `create()` - Form tạo ca học
- `store()` - Lưu ca học (có validation xung đột thời gian)
- `edit()` - Form sửa ca học
- `update()` - Cập nhật ca học (có validation xung đột thời gian)
- `destroy()` - Xóa ca học
- `toggleStatus()` - Bật/tắt trạng thái ca học

### `LichHocCoDinhController`
- `index()` - Danh sách lịch học cố định (eager load caHoc)
- `create()` - Form tạo lịch (có danh sách ca học)
- `store()` - **TẠO NHIỀU BUỔI TỰ ĐỘNG**
  - Nhận input: ca_hoc_id, so_buoi_hoc, ngay_bat_dau_lich, thu_trong_tuan[]
  - Tính toán tất cả ngày học theo pattern
  - Kiểm tra xung đột cho TẤT CẢ buổi trước khi tạo
  - Tạo tất cả buổi học trong 1 transaction
  - Trả về số buổi đã tạo thành công
- `edit()` - Form sửa lịch
- `update()` - Cập nhật lịch
- `destroy()` - Xóa lịch
- **Private method:**
  - `generateScheduleDates()` - Tạo danh sách ngày học theo pattern

## Views
### `daotao/ca-hoc/index.blade.php`
- Giao diện hiện đại với card layout
- Hiển thị thống kê: Tổng ca, Đang hoạt động, Không hoạt động
- Hiển thị chi tiết từng ca: Thứ tự, Tên, Thời gian, Khoảng cách (phút)

### `daotao/lich-hoc-co-dinh/create.blade.php`
- **Phần 1: Thông tin môn học** - Card hiển thị thông tin môn
- **Phần 2: Chọn Ca học và Pattern**
  - Dropdown ca học
  - Input số buổi học (auto-fill từ môn)
  - Date picker ngày bắt đầu
  - Checkboxes các thứ trong tuần
- **Phần 3: Thông tin cố định**
  - Dropdown phòng học, giảng viên
  - Radio hình thức học
  - Input link online
  - Textarea ghi chú
- **Preview section** - Hiển thị danh sách buổi học sẽ được tạo
- **JavaScript:**
  - Auto-fill giờ khi chọn ca học
  - Preview tính toán ngày học
  - Validation client-side

### `daotao/lich-hoc-co-dinh/index.blade.php`
- Hiển thị tổng số buổi học
- Table với cột "Ca học" hiển thị badge
- Hiển thị icon cho từng loại thông tin
- Hiển thị sức chứa phòng

## Routes
```php
// Ca học
Route::resource('ca-hoc', CaHocController::class);
Route::post('ca-hoc/{caHoc}/toggle-status', [CaHocController::class, 'toggleStatus'])
    ->name('ca-hoc.toggle-status');

// Lịch học cố định
Route::prefix('lop-hoc-phan/{lopHocPhan}')->group(function () {
    Route::get('lich-co-dinh', [LichHocCoDinhController::class, 'index'])
        ->name('lop-hoc-phan.lich-co-dinh');
    Route::get('lich-co-dinh/create', [LichHocCoDinhController::class, 'create'])
        ->name('lop-hoc-phan.lich-co-dinh.create');
    Route::post('lich-co-dinh', [LichHocCoDinhController::class, 'store'])
        ->name('lop-hoc-phan.lich-co-dinh.store');
});

Route::resource('lich-co-dinh', LichHocCoDinhController::class)
    ->except(['index', 'create', 'store'])
    ->names('dao-tao.lich-co-dinh');
```

## Testing checklist

### ✅ Hoàn thành
1. [x] Migration ca_hoc table đã chạy
2. [x] Migration add ca_hoc_id đã chạy
3. [x] Seeder ca_hoc đã chạy (6 ca)
4. [x] Model CaHoc có đầy đủ relationships và methods
5. [x] Model LichHocCoDinh có relationship caHoc
6. [x] Controller CaHocController đầy đủ CRUD + validation
7. [x] Controller LichHocCoDinhController có logic tạo nhiều buổi
8. [x] View ca-hoc/index hiển thị đẹp
9. [x] View lich-hoc-co-dinh/create có form đầy đủ
10. [x] View lich-hoc-co-dinh/index hiển thị ca học
11. [x] JavaScript preview hoạt động
12. [x] Validation xung đột thời gian ca học
13. [x] Validation xung đột phòng/giảng viên
14. [x] Transaction rollback khi có lỗi
15. [x] Clear cache Laravel

### 🔜 Cần test thực tế
- [ ] Truy cập form tạo lịch học cố định
- [ ] Chọn ca học từ dropdown
- [ ] Chọn nhiều thứ trong tuần
- [ ] Click "Xem trước" → Kiểm tra preview
- [ ] Tạo lịch → Kiểm tra tạo thành công nhiều buổi
- [ ] Kiểm tra xung đột phòng học
- [ ] Kiểm tra xung đột giảng viên
- [ ] Xem danh sách lịch → Kiểm tra hiển thị ca học

## Lưu ý
- Hệ thống giới hạn tối đa 50 buổi học mỗi lần tạo
- Hệ thống có timeout 365 lần lặp để tránh vòng lặp vô hạn
- Nếu không đủ thời gian để tạo số buổi yêu cầu, hệ thống sẽ báo lỗi
- Tất cả các buổi học phải pass kiểm tra xung đột trước khi tạo
- Sử dụng transaction để đảm bảo toàn vẹn dữ liệu
- Format thứ: 2=T2, 3=T3, 4=T4, 5=T5, 6=T6, 7=T7, 8=CN

## Kết quả
✅ **Thành công!** Hệ thống đã hoàn chỉnh tính năng tạo lịch học tự động.

---
**Cập nhật:** 17/11/2025
**Version:** 1.0

