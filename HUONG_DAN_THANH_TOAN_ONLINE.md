# Hướng dẫn cấu hình Thanh toán Online

## Tổng quan
Hệ thống SMIS đã tích hợp 2 cổng thanh toán:
- **VNPay** - Thanh toán qua ATM, Visa, MasterCard, QR Code
- **MoMo** - Thanh toán qua ví điện tử, QR Code

## 1. Cấu hình VNPay

### Bước 1: Đăng ký tài khoản
- **Sandbox (Test):** https://sandbox.vnpayment.vn/
- **Production:** https://vnpay.vn/

### Bước 2: Lấy thông tin tích hợp
Sau khi đăng ký, bạn sẽ nhận được:
- `TMN Code` (Terminal ID / Mã website)
- `Hash Secret` (Khóa bí mật)

### Bước 3: Cấu hình trong file `.env`
```env
VNPAY_TMN_CODE=YOUR_TMN_CODE_HERE
VNPAY_HASH_SECRET=YOUR_HASH_SECRET_HERE
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/sinh-vien/hoc-phi/payment-return
```

### Bước 4: Test trên Sandbox
**Thẻ test VNPay Sandbox:**
- Ngân hàng: NCB
- Số thẻ: `9704198526191432198`
- Tên chủ thẻ: `NGUYEN VAN A`
- Ngày phát hành: `07/15`
- Mật khẩu OTP: `123456`

---

## 2. Cấu hình MoMo

### Bước 1: Đăng ký tài khoản
- **Business:** https://business.momo.vn/

### Bước 2: Lấy thông tin tích hợp
Sau khi đăng ký, bạn sẽ nhận được:
- `Partner Code`
- `Access Key`
- `Secret Key`

### Bước 3: Cấu hình trong file `.env`
```env
MOMO_PARTNER_CODE=YOUR_PARTNER_CODE
MOMO_ACCESS_KEY=YOUR_ACCESS_KEY
MOMO_SECRET_KEY=YOUR_SECRET_KEY
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
MOMO_RETURN_URL=http://localhost:8000/sinh-vien/hoc-phi/payment-return
MOMO_NOTIFY_URL=http://localhost:8000/sinh-vien/hoc-phi/payment-callback
```

### Bước 4: Test trên Sandbox
**Tài khoản MoMo test:**
- Tải app MoMo sandbox tại: https://developers.momo.vn/
- Sử dụng số điện thoại test được cung cấp bởi MoMo

---

## 3. Cấu hình chung

### Bật/Tắt thanh toán online
```env
PAYMENT_ENABLED=true
```

### Cấu hình trong `config/payment.php`
File này đã được tạo tự động, các tham số:
- `min_amount`: Số tiền tối thiểu (mặc định: 10,000 VNĐ)
- `max_amount`: Số tiền tối đa (mặc định: 500,000,000 VNĐ)
- `transaction_timeout`: Thời gian timeout (mặc định: 15 phút)

---

## 4. Luồng thanh toán

### Cho Sinh viên:
1. Truy cập: **Sinh viên → Học phí → Xem chi tiết**
2. Click nút **"Thanh toán online"**
3. Nhập số tiền cần đóng
4. Chọn phương thức: VNPay hoặc MoMo
5. Chuyển đến trang thanh toán
6. Hoàn tất thanh toán
7. Quay lại hệ thống → Học phí được cập nhật tự động

### Luồng kỹ thuật:
```
Sinh viên → Modal thanh toán 
         → POST /sinh-vien/hoc-phi/{id}/thanh-toan-online
         → PaymentService tạo URL
         → Redirect đến VNPay/MoMo
         → Sinh viên thanh toán
         → Gateway callback (IPN)
         → POST /sinh-vien/hoc-phi/payment-callback
         → PaymentService xác thực & lưu
         → Redirect sinh viên
         → GET /sinh-vien/hoc-phi/payment-return
         → Hiển thị kết quả
```

---

## 5. Database

### Bảng `lich_su_dong_hoc_phi` đã sẵn sàng:
- `phuong_thuc_thanh_toan`: 'VNPay' hoặc 'MoMo'
- `ma_giao_dich`: Mã giao dịch từ gateway
- `ngan_hang`: Tên ngân hàng/ví

Không cần chạy migration thêm.

---

## 6. Bảo mật

### Quan trọng:
1. **KHÔNG commit** `.env` lên Git
2. **KHÔNG share** Secret Key, Hash Secret
3. Sử dụng **HTTPS** trên production
4. Verify **signature** từ gateway trong callback

### Whitelist IP (Production):
- VNPay sẽ cung cấp danh sách IP
- Cấu hình firewall chỉ cho phép IP của VNPay/MoMo callback

---

## 7. Test

### Test local:
```bash
# Cách 1: Sử dụng ngrok để expose localhost
ngrok http 8000

# Cập nhật Return URL và Notify URL trong .env
MOMO_RETURN_URL=https://your-ngrok-url.ngrok.io/sinh-vien/hoc-phi/payment-return
MOMO_NOTIFY_URL=https://your-ngrok-url.ngrok.io/sinh-vien/hoc-phi/payment-callback
```

### Kiểm tra logs:
```bash
# Xem logs thanh toán
tail -f storage/logs/laravel.log | grep -i payment
```

---

## 8. Production Checklist

- [ ] Đổi sang Production URL của VNPay
- [ ] Đổi sang Production credentials của MoMo  
- [ ] Cấu hình HTTPS
- [ ] Whitelist IP gateway
- [ ] Test thanh toán thật với số tiền nhỏ
- [ ] Monitor logs và transactions
- [ ] Backup database trước khi deploy

---

## 9. Support

### VNPay:
- Docs: https://sandbox.vnpayment.vn/apis/docs/
- Email: support@vnpay.vn
- Hotline: 1900 5555 77

### MoMo:
- Docs: https://developers.momo.vn/
- Email: developers@momo.vn
- Hotline: 1900 54 54 41

---

## 10. Troubleshooting

### Lỗi "Chữ ký không hợp lệ":
- Kiểm tra Secret Key/Hash Secret đúng chưa
- Kiểm tra thứ tự tham số khi tạo signature
- Xem logs để debug

### Callback không nhận được:
- Kiểm tra URL có public không (dùng ngrok)
- Kiểm tra firewall
- Xem logs của gateway

### Thanh toán thành công nhưng không cập nhật:
- Kiểm tra logs trong `storage/logs/laravel.log`
- Kiểm tra table `lich_su_dong_hoc_phi`
- Debug method `PaymentService::handleCallback()`
