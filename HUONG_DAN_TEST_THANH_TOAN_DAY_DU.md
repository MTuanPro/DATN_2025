# 🎓 HƯỚNG DẪN TEST THANH TOÁN ONLINE - LUỒNG ĐẦY ĐỦ

## 📋 TỔNG QUAN

Luồng hoàn chỉnh: **Sinh viên đăng ký môn học → Đào tạo xếp lớp → Hệ thống tự động tính học phí → Sinh viên thanh toán online**

---

## 🔧 BƯỚC 1: CÀI ĐẶT & KHỞI TẠO DỮ LIỆU

### 1.1. Chạy Seeder để tạo dữ liệu cơ bản

```bash
php artisan db:seed --class=QuanLySinhVienSeeder
```

**Dữ liệu được tạo:**
- ✅ Sinh viên (SinhVien)
- ✅ Môn học (MonHoc)
- ✅ Học kỳ (HocKy)
- ✅ Lớp học phần (LopHocPhan)
- ✅ **Cấu hình học phí** (CauHinhHocPhi) - **QUAN TRỌNG!**
  - Đơn giá: 650,000 VNĐ/tín chỉ
  - Phí dịch vụ: 500,000 VNĐ/học kỳ

### 1.2. Cấu hình Payment Gateway (.env)

Thêm vào file `.env`:

```env
# VNPay Configuration
VNPAY_TMN_CODE=your_tmn_code_here
VNPAY_HASH_SECRET=your_hash_secret_here
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/sinh-vien/hoc-phi/payment-return

# MoMo Configuration
MOMO_PARTNER_CODE=your_partner_code_here
MOMO_ACCESS_KEY=your_access_key_here
MOMO_SECRET_KEY=your_secret_key_here
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
MOMO_RETURN_URL=http://localhost:8000/sinh-vien/hoc-phi/payment-return
MOMO_NOTIFY_URL=http://localhost:8000/sinh-vien/hoc-phi/payment-callback
```

⚠️ **LƯU Ý:** Với sandbox/test, có thể để credentials giả để test flow, nhưng thanh toán thật cần đăng ký:
- VNPay: https://sandbox.vnpayment.vn
- MoMo: https://business.momo.vn

---

## 👨‍🎓 BƯỚC 2: SINH VIÊN ĐĂNG KÝ MÔN HỌC

### 2.1. Đăng nhập Sinh viên

1. Truy cập: `http://localhost:8000/login`
2. Đăng nhập tài khoản sinh viên:
   - Email: `sv001@smis.edu.vn` (hoặc sinh viên khác)
   - Mật khẩu: `password` (mặc định)

### 2.2. Đăng ký môn học

1. Vào menu: **Đăng ký học phần**
2. Chọn **Học kỳ** đang mở đăng ký (ví dụ: "Học kỳ 1 - 2024-2025")
3. Chọn các môn học muốn đăng ký (tick checkbox):
   - Ví dụ: Lập trình Web (3 TC), Cơ sở dữ liệu (3 TC), Mạng máy tính (2 TC)
4. Click nút **"Đăng ký"**

**✅ Kết quả:** Hiển thị thông báo "Đăng ký thành công! Vui lòng chờ phòng Đào tạo xếp lớp."

**📊 Dữ liệu trong DB:**
```sql
-- Kiểm tra
SELECT * FROM dang_ky_mon_hoc_tam 
WHERE sinh_vien_id = 1 AND trang_thai = 'cho_xep_lop';
```

---

## 👔 BƯỚC 3: ĐÀO TẠO XẾP LỚP

### 3.1. Đăng nhập Đào tạo

1. Đăng xuất tài khoản sinh viên
2. Đăng nhập tài khoản Đào tạo:
   - Email: `daotao@smis.edu.vn`
   - Mật khẩu: `password`

### 3.2. Xếp lớp tự động

1. Vào menu: **Đào tạo → Xếp lớp**
2. Chọn **Học kỳ** (ví dụ: "Học kỳ 1 - 2024-2025")
3. Click nút **"Xếp lớp tự động"**

**✅ Kết quả:** 
- Hiển thị: "Xếp lớp hoàn tất! Thành công: 5, Thất bại: 0"
- Hệ thống đã:
  1. Xếp sinh viên vào lớp học phần
  2. **Tự động tính học phí cho sinh viên** ⭐

**📊 Dữ liệu trong DB:**

```sql
-- Kiểm tra đã xếp lớp
SELECT * FROM lop_hoc_phan_sinh_vien 
WHERE sinh_vien_id = 1;

-- Kiểm tra học phí được tạo tự động
SELECT * FROM hoc_phi_hoc_ky 
WHERE sinh_vien_id = 1;

-- Kiểm tra chi tiết học phí từng môn
SELECT hp.*, m.ten_mon_hoc, hp.so_tin_chi, hp.thanh_tien
FROM chi_tiet_hoc_phi_mon hp
JOIN mon_hoc m ON hp.mon_hoc_id = m.id
WHERE hp.hoc_phi_hoc_ky_id = (
    SELECT id FROM hoc_phi_hoc_ky 
    WHERE sinh_vien_id = 1 
    LIMIT 1
);
```

**💰 Ví dụ kết quả:**
- Lập trình Web: 3 TC × 650,000 = 1,950,000 VNĐ
- Cơ sở dữ liệu: 3 TC × 650,000 = 1,950,000 VNĐ
- Mạng máy tính: 2 TC × 650,000 = 1,300,000 VNĐ
- Phí dịch vụ: 500,000 VNĐ
- **TỔNG: 5,700,000 VNĐ**

---

## 💳 BƯỚC 4: SINH VIÊN THANH TOÁN ONLINE

### 4.1. Đăng nhập lại Sinh viên

1. Đăng xuất tài khoản Đào tạo
2. Đăng nhập lại sinh viên: `sv001@smis.edu.vn`

### 4.2. Xem học phí

1. Vào menu: **Học phí**
2. Sẽ thấy danh sách học phí các học kỳ:

| Học kỳ | Tổng tiền | Đã nộp | Còn lại | Trạng thái | Hạn đóng |
|--------|-----------|---------|---------|------------|----------|
| HK1 2024-2025 | 5,700,000₫ | 0₫ | 5,700,000₫ | Chưa nộp | 30/12/2024 |

3. Click **"Chi tiết"** để xem chi tiết học phí

### 4.3. Thanh toán Online

1. Trong trang chi tiết học phí, click nút **"Thanh toán online"** (màu xanh dương)

2. **Modal thanh toán** hiện ra:
   - Tổng còn lại: 5,700,000₫
   - Nhập số tiền muốn thanh toán (ví dụ: `2000000` - 2 triệu)
   - Chọn phương thức:
     - **VNPay** (ATM/Visa/MasterCard/QR) - nút xanh
     - **MoMo** (Ví điện tử/QR) - nút đỏ

3. Click chọn phương thức → **Tự động chuyển sang cổng thanh toán**

### 4.4. Thanh toán tại cổng

**Nếu chọn VNPay:**
- Chọn ngân hàng (ví dụ: NCB - Ngân hàng Quốc Dân)
- Nhập thông tin thẻ test:
  - Số thẻ: `9704198526191432198`
  - Tên: `NGUYEN VAN A`
  - Ngày phát hành: `07/15`
  - Mật khẩu: `123456`
  - OTP: `123456`

**Nếu chọn MoMo:**
- Quét mã QR bằng app MoMo
- Hoặc nhập số điện thoại test
- Xác nhận thanh toán

### 4.5. Quay lại hệ thống

Sau khi thanh toán thành công:

1. Tự động chuyển về trang **"Kết quả thanh toán"**
2. Hiển thị:
   - ✅ **"Thanh toán thành công!"**
   - Mã giao dịch: `HP_1_20241120_123456`
   - Số tiền: 2,000,000₫
   - Phương thức: VNPay / MoMo
   - Thời gian: 20/11/2024 14:30:25

3. Click **"Quay lại danh sách học phí"**

### 4.6. Kiểm tra kết quả

Vào lại **Học phí → Chi tiết**, sẽ thấy:

| Thông tin | Giá trị |
|-----------|---------|
| Tổng học phí | 5,700,000₫ |
| Đã thanh toán | **2,000,000₫** ⭐ |
| Còn lại | **3,700,000₫** |
| Trạng thái | **Nộp một phần** |

**Lịch sử thanh toán:**

| STT | Mã GD | Số tiền | Phương thức | Thời gian | Trạng thái |
|-----|-------|---------|-------------|-----------|------------|
| 1 | HP_1_20241120_123456 | 2,000,000₫ | VNPay | 20/11/2024 14:30 | Thành công |

---

## 🔍 KIỂM TRA DATABASE

### Bảng `hoc_phi_hoc_ky`

```sql
SELECT 
    hf.id,
    sv.ten AS sinh_vien,
    hk.ten_hoc_ky,
    hf.tong_so_tien,
    hf.so_tien_da_dong,
    hf.so_tien_con_lai,
    hf.trang_thai
FROM hoc_phi_hoc_ky hf
JOIN sinh_vien sv ON hf.sinh_vien_id = sv.id
JOIN hoc_ky hk ON hf.hoc_ky_id = hk.id
WHERE sv.id = 1;
```

**Kết quả mong đợi:**
```
id: 1
sinh_vien: Nguyễn Văn A
ten_hoc_ky: Học kỳ 1 - 2024-2025
tong_so_tien: 5700000
so_tien_da_dong: 2000000
so_tien_con_lai: 3700000
trang_thai: nop_mot_phan
```

### Bảng `lich_su_dong_hoc_phi`

```sql
SELECT 
    ls.ma_giao_dich,
    ls.so_tien,
    ls.phuong_thuc_thanh_toan,
    ls.trang_thai,
    ls.ngay_thanh_toan
FROM lich_su_dong_hoc_phi ls
WHERE ls.hoc_phi_hoc_ky_id = 1
ORDER BY ls.ngay_thanh_toan DESC;
```

---

## 🧪 TEST CASE ĐẦY ĐỦ

### TC1: Thanh toán toàn bộ

1. Nhập số tiền: `5700000` (đúng bằng tổng còn lại)
2. Thanh toán thành công
3. **Kết quả:** `trang_thai = 'da_nop_du'`

### TC2: Thanh toán từng phần

1. Lần 1: `2000000` → `trang_thai = 'nop_mot_phan'`
2. Lần 2: `1000000` → `trang_thai = 'nop_mot_phan'`
3. Lần 3: `2700000` → `trang_thai = 'da_nop_du'`

### TC3: Validation

1. Nhập số tiền âm: **Lỗi** "Số tiền phải lớn hơn 10,000₫"
2. Nhập số tiền > còn lại: **Lỗi** "Số tiền vượt quá số tiền còn lại"
3. Nhập số tiền < 10,000: **Lỗi** "Số tiền tối thiểu 10,000₫"
4. Nhập số tiền > 500,000,000: **Lỗi** "Số tiền tối đa 500,000,000₫"

### TC4: Thanh toán thất bại

1. Tại cổng thanh toán, click **"Hủy giao dịch"**
2. **Kết quả:** Quay về với thông báo "Giao dịch bị hủy"
3. Học phí **không thay đổi**
4. **Không** tạo lịch sử thanh toán

---

## 📊 CÁCH XEM LOG

### Log Laravel

```bash
tail -f storage/logs/laravel.log
```

Sẽ thấy:
```
[2024-11-20 14:30:25] local.INFO: Đã tính học phí cho sinh viên ID: 1, Học kỳ: 1
[2024-11-20 14:35:10] local.INFO: Payment created: {"order_id":"HP_1_20241120_143510", ...}
[2024-11-20 14:35:45] local.INFO: Payment successful: {"ma_giao_dich":"HP_1_20241120_143510", ...}
```

---

## 🐛 TROUBLESHOOTING

### Vấn đề 1: Không thấy nút "Thanh toán online"

**Nguyên nhân:** `so_tien_con_lai = 0`

**Giải pháp:** Kiểm tra:
```sql
SELECT so_tien_con_lai FROM hoc_phi_hoc_ky WHERE id = 1;
```

### Vấn đề 2: Không tính được học phí sau xếp lớp

**Nguyên nhân:** Chưa có cấu hình học phí

**Giải pháp:**
```bash
php artisan db:seed --class=CauHinhHocPhiSeeder
```

### Vấn đề 3: Lỗi "Invalid signature" từ VNPay/MoMo

**Nguyên nhân:** Sai `HASH_SECRET` trong `.env`

**Giải pháp:** Kiểm tra lại credentials hoặc dùng giá trị test

### Vấn đề 4: Học phí = 0

**Nguyên nhân:** Môn học chưa có số tín chỉ

**Giải pháp:**
```sql
UPDATE mon_hoc SET so_tin_chi_ly_thuyet = 3 WHERE id = 1;
```

---

## 📝 GHI CHÚ QUAN TRỌNG

1. **Callback URL phải public:** Khi deploy production, URL callback phải truy cập được từ internet để VNPay/MoMo gửi IPN
2. **Webhook cần HTTPS:** Production bắt buộc HTTPS
3. **Test sandbox trước:** Luôn test với sandbox trước khi chuyển production
4. **Đồng bộ trạng thái:** IPN (callback) mới là nguồn chính xác, return URL chỉ để hiển thị
5. **Log mọi giao dịch:** Lưu đầy đủ request/response để đối soát

---

## ✅ CHECKLIST

- [ ] Chạy Seeder tạo dữ liệu cơ bản
- [ ] Kiểm tra có `CauHinhHocPhi` trong DB
- [ ] Cấu hình `.env` với payment credentials
- [ ] Sinh viên đăng ký môn học thành công
- [ ] Đào tạo xếp lớp tự động
- [ ] Kiểm tra học phí được tạo trong DB
- [ ] Test thanh toán VNPay thành công
- [ ] Test thanh toán MoMo thành công
- [ ] Kiểm tra lịch sử thanh toán
- [ ] Kiểm tra trạng thái học phí cập nhật đúng
- [ ] Test thanh toán thất bại (hủy giao dịch)

---

**🎉 Hoàn tất! Bây giờ bạn có thể test toàn bộ luồng từ đăng ký môn học đến thanh toán online.**
