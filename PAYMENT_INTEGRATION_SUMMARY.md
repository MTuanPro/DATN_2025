# TÍNH NĂNG THANH TOÁN ONLINE - SMIS

## ✅ ĐÃ HOÀN THÀNH

Tính năng **Thanh toán học phí online** đã được tích hợp đầy đủ cho **Actor: Sinh viên**

---

## 📦 CÁC FILE ĐÃ TẠO/SỬA

### 1. Services (Business Logic)
- ✅ `app/Services/VNPayService.php` - Tích hợp VNPay gateway
- ✅ `app/Services/MoMoService.php` - Tích hợp MoMo gateway  
- ✅ `app/Services/PaymentService.php` - Quản lý chung payment

### 2. Controller
- ✅ `app/Http/Controllers/SinhVien/HocPhiController.php` - Thêm 3 methods:
  - `thanhToanOnline()` - Tạo payment URL
  - `paymentCallback()` - Xử lý IPN từ gateway
  - `paymentReturn()` - Trang return sau thanh toán

### 3. Routes
- ✅ `routes/web.php` - Thêm routes:
  - `POST /sinh-vien/hoc-phi/{id}/thanh-toan-online`
  - `GET|POST /sinh-vien/hoc-phi/payment-callback`
  - `GET /sinh-vien/hoc-phi/payment-return`

### 4. Views
- ✅ `resources/views/sinhvien/hoc-phi/show.blade.php` - Thêm:
  - Nút "Thanh toán online"
  - Modal chọn phương thức (VNPay/MoMo)
  - JavaScript xử lý AJAX

### 5. Configuration
- ✅ `config/payment.php` - Cấu hình VNPay và MoMo
- ✅ `.env.example` - Thêm biến môi trường:
  ```env
  VNPAY_TMN_CODE=
  VNPAY_HASH_SECRET=
  MOMO_PARTNER_CODE=
  MOMO_ACCESS_KEY=
  MOMO_SECRET_KEY=
  ```

### 6. Documentation
- ✅ `HUONG_DAN_THANH_TOAN_ONLINE.md` - Hướng dẫn chi tiết

---

## 🎯 TÍNH NĂNG

### Cho Sinh viên:
1. Xem chi tiết học phí
2. Click "Thanh toán online"
3. Nhập số tiền (có thể đóng 1 phần)
4. Chọn VNPay hoặc MoMo
5. Thanh toán và tự động cập nhật

### Payment Gateways:
- **VNPay**: ATM, Visa, MasterCard, QR Code
- **MoMo**: Ví điện tử, QR Code, Deeplink

### Auto Update:
- Cập nhật `hoc_phi_hoc_ky`:
  - `so_tien_da_dong` += số tiền đóng
  - `so_tien_con_lai` -= số tiền đóng
  - `trang_thai` (chua_nop_du → da_nop_du)
  
- Lưu `lich_su_dong_hoc_phi`:
  - `phuong_thuc_thanh_toan`: VNPay/MoMo
  - `ma_giao_dich`: Transaction ID
  - `ngan_hang`: Bank code/MoMo

---

## 🚀 CÁCH SỬ DỤNG

### 1. Cấu hình môi trường
```bash
# Copy .env.example sang .env và điền thông tin
cp .env.example .env

# Cấu hình VNPay (đăng ký tại sandbox.vnpayment.vn)
VNPAY_TMN_CODE=your_tmn_code
VNPAY_HASH_SECRET=your_hash_secret

# Cấu hình MoMo (đăng ký tại business.momo.vn)
MOMO_PARTNER_CODE=your_partner_code
MOMO_ACCESS_KEY=your_access_key
MOMO_SECRET_KEY=your_secret_key
```

### 2. Test trên Sandbox

**VNPay Test Card:**
- Số thẻ: `9704198526191432198`
- Ngân hàng: NCB
- OTP: `123456`

**MoMo Test:**
- Dùng app MoMo sandbox
- Tài khoản test do MoMo cung cấp

### 3. Deploy Production
```bash
# Chuyển sang URL production
VNPAY_URL=https://vnpay.vn/paymentv2/vpcpay.html
MOMO_ENDPOINT=https://payment.momo.vn/v2/gateway/api/create

# Sử dụng credentials production
# Cấu hình HTTPS
# Whitelist IP của gateway
```

---

## 📊 DATABASE

**Không cần migration mới** - Table `lich_su_dong_hoc_phi` đã sẵn sàng:
```sql
- phuong_thuc_thanh_toan: 'VNPay', 'MoMo'
- ma_giao_dich: unique transaction ID
- ngan_hang: bank code/wallet name
```

---

## 🔒 BẢO MẬT

✅ Signature verification (HMAC SHA256/SHA512)  
✅ Transaction amount validation  
✅ Duplicate transaction check  
✅ Callback IP verification (production)  
✅ HTTPS required (production)  

---

## 📝 LOGS

Tất cả giao dịch được log tại:
```bash
storage/logs/laravel.log
```

Tìm kiếm: `Payment`, `VNPay`, `MoMo`

---

## 🧪 TEST FLOW

1. **Đăng nhập**: sinh viên (sv001@smis.edu.vn / sv001)
2. **Truy cập**: Sinh viên → Học phí → Xem chi tiết
3. **Click**: "Thanh toán online"
4. **Nhập**: Số tiền (VD: 1,000,000 VNĐ)
5. **Chọn**: VNPay hoặc MoMo
6. **Thanh toán**: Dùng thẻ test
7. **Kiểm tra**: Học phí đã cập nhật

---

## ⚠️ LƯU Ý

### Production:
- [ ] Đổi sang credentials production
- [ ] Enable HTTPS
- [ ] Cấu hình webhook/callback URL public
- [ ] Whitelist IP gateway
- [ ] Test với số tiền thật nhỏ trước
- [ ] Monitor logs 24/7

### Local Development:
```bash
# Sử dụng ngrok để test callback
ngrok http 8000

# Cập nhật URL trong .env
MOMO_NOTIFY_URL=https://abc123.ngrok.io/sinh-vien/hoc-phi/payment-callback
```

---

## 📞 SUPPORT

**VNPay:**  
- Docs: https://sandbox.vnpayment.vn/apis/docs/
- Hotline: 1900 5555 77

**MoMo:**  
- Docs: https://developers.momo.vn/
- Hotline: 1900 54 54 41

**Xem chi tiết:** `HUONG_DAN_THANH_TOAN_ONLINE.md`

---

## 🎉 HOÀN THÀNH

✅ Tích hợp VNPay  
✅ Tích hợp MoMo  
✅ Auto update học phí  
✅ Lưu lịch sử giao dịch  
✅ Security & Validation  
✅ Error handling  
✅ Logging  
✅ Documentation  

**Ready for testing!** 🚀
