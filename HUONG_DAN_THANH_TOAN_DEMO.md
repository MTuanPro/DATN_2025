# 💳 THANH TOÁN ONLINE - PHIÊN BẢN DEMO

## 🎯 MỤC ĐÍCH

Đây là phiên bản **DEMO** cho mục đích trình bày và thi. **KHÔNG CÓ GIAO DỊCH TIỀN THẬT**.

## ⚡ CÁCH HOẠT ĐỘNG

### 1. Thanh toán ngay lập tức
- Không cần chuyển sang cổng VNPay/MoMo
- Không cần nhập thông tin thẻ
- Thanh toán thành công **NGAY LẬP TỨC** khi click nút

### 2. Luồng thanh toán

```
Sinh viên click "Thanh toán online"
    ↓
Nhập số tiền + chọn phương thức (VNPay/MoMo)
    ↓
Click nút thanh toán
    ↓
✅ Thanh toán thành công NGAY LẬP TỨC
    ↓
Hiển thị kết quả + Mã giao dịch
    ↓
Cập nhật học phí trong database
```

## 🚀 HƯỚNG DẪN SỬ DỤNG

### Bước 1: Đăng nhập Sinh viên
```
URL: http://localhost:8000/login
Email: sv001@smis.edu.vn (hoặc sinh viên khác)
Password: password
```

### Bước 2: Vào Học phí
1. Menu → **Học phí**
2. Click **"Chi tiết"** để xem chi tiết học phí học kỳ

### Bước 3: Thanh toán
1. Click nút **"Thanh toán online"** (màu xanh dương)
2. Nhập số tiền (ví dụ: `1000000` - 1 triệu)
3. Chọn phương thức:
   - **VNPay** (nút xanh)
   - **MoMo** (nút đỏ)
4. Click vào nút phương thức đã chọn

### Bước 4: Xem kết quả
Hệ thống hiển thị popup kết quả ngay lập tức:

| Thông tin | Giá trị |
|-----------|---------|
| **Mã giao dịch** | DEMO_VNPAY_1_1732099234 |
| **Số tiền** | 1,000,000₫ |
| **Cổng thanh toán** | VNPay / MoMo |
| **Thời gian** | 20/11/2024 15:30:45 |
| **Còn lại** | 6,000,000₫ |
| **Trạng thái** | nop_mot_phan |

⚠️ **Lưu ý:** Đây là giao dịch demo - Không có thanh toán thực tế

### Bước 5: Kiểm tra lịch sử
1. Click **"Đóng"** → Trang tự động reload
2. Xem phần **"Lịch sử thanh toán"** bên dưới
3. Thấy giao dịch vừa tạo với:
   - Mã giao dịch
   - Số tiền
   - Phương thức
   - Trạng thái: **Thành công**

## 🧪 TEST CASE

### TC1: Thanh toán 1 phần
```
Tổng học phí: 7,000,000₫
Thanh toán lần 1: 2,000,000₫ → Còn lại: 5,000,000₫
Thanh toán lần 2: 3,000,000₫ → Còn lại: 2,000,000₫
Trạng thái: "Nộp một phần"
```

### TC2: Thanh toán đủ
```
Tổng học phí: 7,000,000₫
Thanh toán: 7,000,000₫ → Còn lại: 0₫
Trạng thái: "Đã nộp đủ"
Nút "Thanh toán online" biến mất
```

### TC3: Test 2 phương thức
```
Lần 1: VNPay - 1,000,000₫ → Mã: DEMO_VNPAY_1_xxx
Lần 2: MoMo - 2,000,000₫ → Mã: DEMO_MOMO_1_xxx
```

### TC4: Validation
```
❌ Số tiền < 10,000: "Số tiền tối thiểu là 10,000 VNĐ"
❌ Số tiền > còn lại: "Số tiền thanh toán không được lớn hơn số tiền còn lại!"
❌ Đã nộp đủ: Nút thanh toán bị ẩn
```

## 📊 DỮ LIỆU TRONG DATABASE

### Bảng: `lich_su_dong_hoc_phi`

```sql
SELECT 
    ma_giao_dich,
    so_tien,
    phuong_thuc_thanh_toan,
    trang_thai,
    ngay_thanh_toan,
    response_data
FROM lich_su_dong_hoc_phi
WHERE hoc_phi_hoc_ky_id = 1
ORDER BY ngay_thanh_toan DESC;
```

**Kết quả mẫu:**
```
ma_giao_dich: DEMO_VNPAY_1_1732099234
so_tien: 1000000
phuong_thuc_thanh_toan: VNPay
trang_thai: thanh_cong
ngay_thanh_toan: 2024-11-20 15:30:45
response_data: {"demo":true,"gateway":"vnpay","message":"Giao dịch demo - Không có thanh toán thực tế"}
```

### Bảng: `hoc_phi_hoc_ky`

```sql
SELECT 
    tong_so_tien,
    so_tien_da_dong,
    so_tien_con_lai,
    trang_thai
FROM hoc_phi_hoc_ky
WHERE id = 1;
```

**Kết quả sau thanh toán:**
```
tong_so_tien: 7000000
so_tien_da_dong: 1000000
so_tien_con_lai: 6000000
trang_thai: nop_mot_phan
```

## 🎨 GIAO DIỆN

### Modal thanh toán
```
┌─────────────────────────────────────┐
│   Thanh toán học phí                │
├─────────────────────────────────────┤
│ Tổng còn lại: 7,000,000₫           │
│                                     │
│ Nhập số tiền: [_________]          │
│                                     │
│ Chọn phương thức:                   │
│  [💳 VNPay]  [📱 MoMo]             │
│   (xanh)      (đỏ)                  │
│                                     │
│           [Đóng]                    │
└─────────────────────────────────────┘
```

### Popup kết quả thành công
```
┌─────────────────────────────────────┐
│   ✅ Thanh toán thành công!         │
├─────────────────────────────────────┤
│ Mã giao dịch:  DEMO_VNPAY_1_xxx    │
│ Số tiền:       1,000,000₫          │
│ Cổng TT:       VNPay               │
│ Thời gian:     20/11/2024 15:30    │
│ Còn lại:       6,000,000₫          │
│ Trạng thái:    Nộp một phần        │
│                                     │
│ ⚠️ Lưu ý: Đây là giao dịch demo    │
│   Không có thanh toán thực tế      │
│                                     │
│           [Đóng]                    │
└─────────────────────────────────────┘
```

## 🔍 DEBUG & KIỂM TRA

### 1. Xem log Laravel
```bash
tail -f storage/logs/laravel.log
```

### 2. Kiểm tra response API (Browser DevTools)
```javascript
// Network tab → XHR → thanh-toan-online
{
  "success": true,
  "demo": true,
  "message": "Thanh toán demo thành công!",
  "data": {
    "ma_giao_dich": "DEMO_VNPAY_1_1732099234",
    "so_tien": 1000000,
    "gateway": "VNPay",
    "so_tien_con_lai": 6000000,
    "trang_thai": "nop_mot_phan",
    "ngay_thanh_toan": "20/11/2024 15:30:45"
  }
}
```

### 3. Kiểm tra database realtime
```sql
-- Terminal MySQL
SELECT * FROM lich_su_dong_hoc_phi ORDER BY id DESC LIMIT 5;
SELECT * FROM hoc_phi_hoc_ky WHERE id = 1;
```

## ⚙️ THAY ĐỔI KỸ THUẬT

### File đã sửa:

1. **app/Http/Controllers/SinhVien/HocPhiController.php**
   - Method: `thanhToanOnline()`
   - Thay đổi: Bỏ tích hợp VNPay/MoMo, thanh toán thành công ngay
   - Lưu lịch sử với `ma_giao_dich` prefix "DEMO_"
   - `response_data` chứa flag `"demo": true`

2. **resources/views/sinhvien/hoc-phi/show.blade.php**
   - JavaScript xử lý response
   - Hiển thị popup kết quả thay vì redirect
   - Tự động reload sau khi đóng popup

### Code chính:

```php
// Controller
$maGiaoDich = 'DEMO_' . strtoupper($request->gateway) . '_' . $hocPhi->id . '_' . time();

LichSuDongHocPhi::create([
    'ma_giao_dich' => $maGiaoDich,
    'so_tien' => $soTien,
    'phuong_thuc_thanh_toan' => $gateway,
    'trang_thai' => 'thanh_cong',
    'response_data' => json_encode(['demo' => true])
]);
```

```javascript
// Frontend
if (result.success && result.demo) {
    // Hiển thị popup thành công
    Swal.fire({...}).then(() => location.reload());
}
```

## ✅ CHECKLIST DEMO

- [ ] Đăng nhập sinh viên thành công
- [ ] Có học phí với số tiền còn lại > 0
- [ ] Hiển thị nút "Thanh toán online"
- [ ] Click nút → Hiển thị modal
- [ ] Nhập số tiền hợp lệ
- [ ] Click VNPay → Thanh toán thành công ngay
- [ ] Click MoMo → Thanh toán thành công ngay
- [ ] Popup hiển thị đầy đủ thông tin
- [ ] Sau khi đóng → Trang reload
- [ ] Số tiền còn lại cập nhật đúng
- [ ] Lịch sử thanh toán hiển thị
- [ ] Database có bản ghi mới

## 🎓 TRÌNH BÀY KHI THI

### Kịch bản demo:

1. **Giới thiệu:**
   > "Em xin trình bày chức năng thanh toán học phí online qua VNPay và MoMo"

2. **Đăng nhập:**
   > "Em đăng nhập bằng tài khoản sinh viên"

3. **Xem học phí:**
   > "Đây là danh sách học phí các học kỳ. Em click Chi tiết để xem học kỳ 1"

4. **Giải thích:**
   > "Sinh viên thấy tổng học phí 7 triệu, đã nộp 0, còn lại 7 triệu"

5. **Thanh toán:**
   > "Em click Thanh toán online, nhập số tiền 2 triệu, chọn VNPay"

6. **Kết quả:**
   > "Giao dịch thành công, có mã giao dịch, còn lại 5 triệu"

7. **Lịch sử:**
   > "Lịch sử thanh toán hiển thị đầy đủ thông tin giao dịch"

8. **Kết luận:**
   > "Sinh viên có thể thanh toán nhiều lần cho đến khi đủ học phí"

## 📝 GHI CHÚ

- ✅ **Ưu điểm:** Nhanh, dễ demo, không cần internet, không cần credentials
- ⚠️ **Hạn chế:** Không có giao dịch thật, chỉ phù hợp demo/thi
- 🔄 **Nâng cấp:** Nếu cần tích hợp thật, xem file `HUONG_DAN_THANH_TOAN_ONLINE.md`

---

**🎉 Hoàn tất! Chúc bạn trình bày tốt!**
