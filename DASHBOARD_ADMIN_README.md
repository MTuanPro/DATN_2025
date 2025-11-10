# Dashboard Admin Mới - Hướng Dẫn

## 📊 Tổng Quan
Dashboard admin mới đã được nâng cấp với các thống kê giáo dục đầy đủ theo yêu cầu.

## ✅ Các Chức Năng Đã Triển Khai

### 1. Thống Kê Tổng Số
- ✅ **Tổng số sinh viên** - Hiển thị trên thẻ màu teal với icon mortarboard
- ✅ **Tổng số giảng viên** - Hiển thị trên thẻ màu indigo với icon person-bounding-box
- ✅ **Số lượng lớp học phần** - Hiển thị trên thẻ màu cyan với icon journal-bookmark

### 2. Biểu Đồ Đăng Ký Môn Học
- ✅ **Biểu đồ cột (Bar Chart)** - Top 8 môn học có nhiều đăng ký nhất
- Sử dụng ApexCharts, màu chủ đạo: #435ebe
- Dữ liệu: Lấy từ bảng `dang_ky_mon_hocs` JOIN với `lop_hoc_phan` và `mon_hoc`

### 3. Biểu Đồ Phân Bố Điểm
- ✅ **Biểu đồ tròn (Donut Chart)** - Phân bố theo điểm chữ (A, B+, B, C+, C, D+, D, F)
- Màu sắc: Gradient từ xanh (A) đến đỏ (F)
- Dữ liệu: Lấy từ bảng `ket_qua_hoc_tap` theo cột `diem_chu`

### 4. Tỷ Lệ Đỗ/Trượt
- ✅ **Biểu đồ tròn (Donut Chart)** - Qua môn vs Không qua môn
- Màu: Xanh (#198754) cho "Qua môn", Đỏ (#dc3545) cho "Không qua"
- Dữ liệu: Lấy từ bảng `ket_qua_hoc_tap` theo cột `qua_mon`

### 5. Biểu Đồ Tình Hình Học Phí
- ✅ **Progress Bar** - Hiển thị % học phí đã thu / tổng
- Hiển thị chi tiết:
  - Tổng học phí
  - Đã thu được
  - Còn lại
- Dữ liệu: Lấy từ bảng `hoc_phi_hoc_ky` (sum các cột `tong_so_tien`, `so_tien_da_dong`, `so_tien_con_lai`)

### 6. Biểu Đồ Cảnh Báo Học Vụ
- ✅ **Danh sách cảnh báo** - 5 cảnh báo mới nhất
- Hiển thị:
  - Tổng số cảnh báo
  - Số cảnh báo chưa xử lý
  - Chi tiết từng cảnh báo (sinh viên, loại, mức độ, ngày)
- Badge màu theo mức độ: warning (vàng), danger (đỏ), dark (đen)

## 📁 Files Đã Thay Đổi

### Controller
**File:** `app/Http/Controllers/Admin/DashboardController.php`
- Thêm use statements cho models: `SinhVien`, `GiangVien`, `LopHocPhan`, `DangKyMonHoc`, `KetQuaHocTap`, `HocPhiHocKy`, `CanhBaoHocVu`
- Thêm các truy vấn thống kê giáo dục
- Chuẩn bị dữ liệu cho biểu đồ (labels + series)

### View
**File:** `resources/views/admin/dashboard.blade.php`
- Thêm 4 thẻ thống kê mới (sinh viên, giảng viên, lớp học phần)
- Thêm 3 containers cho biểu đồ ApexCharts
- Thêm section tình hình học phí (progress bar)
- Thêm section cảnh báo học vụ (danh sách)
- Thêm scripts render charts trong `@push('scripts')`

## 🗂️ Backup Files
Dashboard cũ đã được backup tại:
- **View:** `resources/views/admin/dashboard_backup.blade.php`
- **Controller:** `backup/Admin/DashboardController_backup.php`

## 🚀 Cách Kiểm Tra

### 1. Seed Dữ Liệu (Nếu Chưa Có)
```powershell
php artisan migrate:fresh --seed
```
⚠️ **Lưu ý:** Lệnh này sẽ xóa toàn bộ dữ liệu hiện có.

### 2. Truy Cập Dashboard
- Đăng nhập với tài khoản admin
- Truy cập: `http://localhost:8000/admin/dashboard` (hoặc domain của bạn)

### 3. Kiểm Tra Các Thành Phần
- [x] Xem 4 thẻ thống kế đầu trang (tài khoản + sinh viên + giảng viên + lớp)
- [x] Kiểm tra biểu đồ đăng ký môn học (bar chart, top 8)
- [x] Kiểm tra biểu đồ phân bố điểm (donut chart, 8 loại điểm)
- [x] Kiểm tra biểu đồ đỗ/trượt (donut chart, 2 phần)
- [x] Kiểm tra progress bar học phí
- [x] Kiểm tra danh sách cảnh báo học vụ

## 🔧 Troubleshooting

### Biểu Đồ Không Hiển Thị
- Kiểm tra ApexCharts đã được load: `public/assets/vendors/apexcharts/apexcharts.js`
- Mở Console trình duyệt (F12) để xem lỗi JavaScript
- Đảm bảo có dữ liệu trong database (chạy seeder)

### Dữ Liệu Trống
- Chạy lại seeder: `php artisan db:seed`
- Hoặc tạo dữ liệu thủ công qua giao diện

### Lỗi Query
- Kiểm tra bảng `dang_ky_mon_hocs`, `ket_qua_hoc_tap`, `hoc_phi_hoc_ky`, `canh_bao_hoc_vu` đã tồn tại
- Chạy migrations: `php artisan migrate`

## 📈 Mở Rộng Tương Lai
- [ ] Thêm filter theo học kỳ/năm học
- [ ] Cache kết quả truy vấn để tăng tốc độ
- [ ] Export báo cáo PDF/Excel
- [ ] Thêm biểu đồ timeline (đăng ký theo thời gian)
- [ ] Dashboard real-time với WebSocket

## 📞 Liên Hệ Hỗ Trợ
Nếu có vấn đề, kiểm tra:
1. Logs Laravel: `storage/logs/laravel.log`
2. Browser Console (F12)
3. Route list: `php artisan route:list --name=admin.dashboard`

---
**Ngày cập nhật:** 10/11/2025  
**Phiên bản:** Dashboard Admin v2.0
