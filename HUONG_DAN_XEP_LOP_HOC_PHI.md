# Hướng dẫn Xếp lớp tự động với Học phí

## Luồng hoạt động

### 1. Sinh viên đăng ký môn học

**File:** `app/Http/Controllers/SinhVien/DangKyMonHocController.php` - Method `store()`

Khi sinh viên đăng ký môn học:

1. ✅ Kiểm tra các điều kiện đăng ký (môn tiên quyết, tín chỉ tối đa, v.v.)
2. ✅ Tạo bản ghi đăng ký với trạng thái `cho_dong_hoc_phi`
3. ✅ **TỰ ĐỘNG TÍNH HỌC PHÍ** cho môn học vừa đăng ký
   - Gọi service: `HocPhiService::tinhHocPhiKhiDangKyMonHoc()`
   - Tạo/cập nhật bản ghi trong bảng `hoc_phi_hoc_ky`
   - Tạo chi tiết học phí trong bảng `chi_tiet_hoc_phi_mon`
4. ✅ **GỬI THÔNG BÁO** yêu cầu đóng học phí
   - Thông báo qua hệ thống
   - Hạn đóng: 1 tuần kể từ ngày đăng ký

**Trạng thái:** `cho_dong_hoc_phi` (Chờ đóng học phí)

---

### 2. Sinh viên đóng học phí

**File:** `app/Observers/LichSuDongHocPhiObserver.php`

Khi sinh viên đóng học phí (tạo bản ghi trong `lich_su_dong_hoc_phi`):

1. ✅ Observer tự động kiểm tra `so_tien_con_lai` trong `hoc_phi_hoc_ky`
2. ✅ Nếu đã đóng đủ học phí (`so_tien_con_lai <= 0`):
   - **TỰ ĐỘNG CẬP NHẬT** trạng thái tất cả đăng ký môn học từ `cho_dong_hoc_phi` → `cho_xep_lop`

**Trạng thái:** `cho_xep_lop` (Chờ xếp lớp - đã đóng học phí)

---

### 3. Đào tạo xếp lớp

**File:** `app/Http/Controllers/DaoTao/XepLopController.php`

Phòng đào tạo có thể:

#### A. Xếp lớp tự động
- Route: `/dao-tao/xep-lop/auto-assign`
- Chỉ xếp các sinh viên có trạng thái `cho_xep_lop` (đã đóng học phí)
- Sắp xếp theo độ ưu tiên và thời gian đăng ký
- Tự động tìm lớp còn chỗ và xếp sinh viên vào

#### B. Xếp lớp thủ công
- Route: `/dao-tao/xep-lop/manual-assign`
- Chọn sinh viên và lớp học phần cụ thể
- Kiểm tra lớp còn chỗ trống

**Trạng thái sau khi xếp:** `da_xep_lop` (Đã xếp lớp)

---

## Các trạng thái đăng ký

| Trạng thái | Ý nghĩa | Màu hiển thị |
|------------|---------|--------------|
| `cho_dong_hoc_phi` | Sinh viên đã đăng ký môn nhưng chưa đóng học phí | Xanh dương (info) |
| `cho_xep_lop` | Sinh viên đã đóng học phí, chờ đào tạo xếp lớp | Vàng (warning) |
| `da_xep_lop` | Đã được xếp vào lớp học phần | Xanh lá (success) |
| `that_bai` | Không xếp được lớp (hết chỗ) | Đỏ (danger) |

---

## Trang xếp lớp của Đào tạo

**Route:** `/dao-tao/xep-lop`

### Thống kê hiển thị:
1. **Chờ đóng học phí:** Số sinh viên đã đăng ký nhưng chưa đóng tiền
2. **Chờ xếp lớp:** Số sinh viên đã đóng học phí, sẵn sàng xếp lớp ⭐
3. **Đã xếp lớp:** Số sinh viên đã được xếp vào lớp
4. **Thất bại:** Số đăng ký không xếp được lớp

### Bảng danh sách:
- Hiển thị tất cả đăng ký (có thể lọc theo trạng thái)
- Cột **Học phí** hiển thị:
  - ✅ Trạng thái: Đã đóng / Chưa đóng / Đã hủy
  - 💰 Số tiền học phí của môn đó
- **Nút "Xếp":** Chỉ hiển thị với sinh viên có trạng thái `cho_xep_lop`

---

## Lưu ý quan trọng

### ⚠️ Chính sách xếp lớp:
- **KHÔNG XẾP LỚP** nếu sinh viên chưa đóng học phí
- Chỉ sinh viên có trạng thái `cho_xep_lop` mới được xếp
- Sinh viên có thể **hủy đăng ký** khi:
  - Trạng thái `cho_dong_hoc_phi` (chưa đóng tiền)
  - Trạng thái `cho_xep_lop` (đã đóng tiền nhưng chưa xếp lớp)

### 💡 Độ ưu tiên xếp lớp:
- Sinh viên năm cuối (kỳ >= 7): +100 điểm
- Sinh viên học lại (đã học nhưng chưa qua): +50 điểm

---

## Files liên quan

### Controllers:
- `app/Http/Controllers/SinhVien/DangKyMonHocController.php`
- `app/Http/Controllers/DaoTao/XepLopController.php`

### Models:
- `app/Models/DangKyMonHocTam.php`
- `app/Models/HocPhiHocKy.php`
- `app/Models/ChiTietHocPhiMon.php`
- `app/Models/LichSuDongHocPhi.php`
- `app/Models/LopHocPhanSinhVien.php`

### Observers:
- `app/Observers/LichSuDongHocPhiObserver.php` ⭐ (Tự động cập nhật trạng thái khi đóng học phí)

### Services:
- `app/Services/HocPhiService.php` (Tính học phí)
- `app/Services/NotificationService.php` (Gửi thông báo)

### Views:
- `resources/views/daotao/xep-lop/index.blade.php`
- `resources/views/sinhvien/dang-ky-mon-hoc/index.blade.php`

---

## Kiểm tra hoạt động

### Test case 1: Đăng ký môn học
1. Đăng nhập với tài khoản sinh viên
2. Vào trang "Đăng ký môn học"
3. Đăng ký 1 môn bất kỳ
4. ✅ Kiểm tra: Xuất hiện thông báo yêu cầu đóng học phí
5. ✅ Kiểm tra: Trạng thái = `cho_dong_hoc_phi`
6. ✅ Kiểm tra: Có bản ghi trong `hoc_phi_hoc_ky` và `chi_tiet_hoc_phi_mon`

### Test case 2: Đóng học phí
1. Đăng nhập với tài khoản đào tạo/admin
2. Vào quản lý học phí
3. Tạo lịch sử đóng học phí cho sinh viên (đóng đủ số tiền)
4. ✅ Kiểm tra: Trạng thái đăng ký tự động chuyển từ `cho_dong_hoc_phi` → `cho_xep_lop`

### Test case 3: Xếp lớp
1. Đăng nhập với tài khoản đào tạo
2. Vào trang "Xếp lớp tự động" (`/dao-tao/xep-lop`)
3. ✅ Kiểm tra: Chỉ thấy sinh viên có trạng thái `cho_xep_lop` có nút "Xếp"
4. ✅ Kiểm tra: Sinh viên `cho_dong_hoc_phi` hiển thị "Chờ đóng học phí"
5. Nhấn "Xếp lớp tự động"
6. ✅ Kiểm tra: Sinh viên được xếp vào lớp, trạng thái = `da_xep_lop`

---

## Troubleshooting

### Vấn đề: Sinh viên đã đóng học phí nhưng vẫn là "Chờ đóng học phí"
**Giải pháp:**
- Kiểm tra `so_tien_con_lai` trong bảng `hoc_phi_hoc_ky`
- Nếu > 0: Sinh viên chưa đóng đủ
- Kiểm tra Observer `LichSuDongHocPhiObserver` đã được đăng ký chưa

### Vấn đề: Không thấy nút "Xếp" trong trang xếp lớp
**Giải pháp:**
- Kiểm tra trạng thái đăng ký phải là `cho_xep_lop`
- Kiểm tra filter/lọc có đúng không

### Vấn đề: Xếp lớp tự động không hoạt động
**Giải pháp:**
- Kiểm tra lớp học phần còn chỗ trống không
- Kiểm tra trạng thái lớp phải là `mo_dang_ky` hoặc `dang_hoc`
- Xem log file: `storage/logs/laravel.log`
