# Hướng dẫn cấu hình MoMo Payment

## 1. Đăng ký tài khoản MoMo

1. Truy cập: https://developers.momo.vn/
2. Đăng ký tài khoản merchant
3. Tạo ứng dụng và lấy thông tin:
   - Partner Code
   - Access Key
   - Secret Key

## 2. Cấu hình trong file .env

Thêm các biến sau vào file `.env`:

```env
# MoMo Payment Configuration
MOMO_PARTNER_CODE=your_partner_code
MOMO_ACCESS_KEY=your_access_key
MOMO_SECRET_KEY=your_secret_key
MOMO_ENVIRONMENT=sandbox
MOMO_PARTNER_NAME=Trường Đại Học
MOMO_STORE_ID=your_store_id
MOMO_RETURN_URL=https://your-domain.com/payment/momo/callback
MOMO_NOTIFY_URL=https://your-domain.com/payment/momo/ipn
```

### Giải thích các biến:

- `MOMO_PARTNER_CODE`: Mã đối tác từ MoMo
- `MOMO_ACCESS_KEY`: Access Key từ MoMo
- `MOMO_SECRET_KEY`: Secret Key từ MoMo (bảo mật)
- `MOMO_ENVIRONMENT`: `sandbox` (test) hoặc `production` (thực tế)
- `MOMO_PARTNER_NAME`: Tên đối tác hiển thị trên MoMo
- `MOMO_STORE_ID`: ID cửa hàng (có thể dùng partner_code)
- `MOMO_RETURN_URL`: URL callback khi thanh toán xong (sinh viên quay lại)
- `MOMO_NOTIFY_URL`: URL IPN (Instant Payment Notification) - MoMo gọi server-to-server

## 3. Cấu hình URL Callback

### Return URL (Redirect URL)
- URL: `https://your-domain.com/payment/momo/callback`
- Method: GET
- Mục đích: Sinh viên quay lại sau khi thanh toán

### IPN URL (Notification URL)
- URL: `https://your-domain.com/payment/momo/ipn`
- Method: POST
- Mục đích: MoMo gọi server-to-server để xác nhận thanh toán

**Lưu ý:** 
- IPN URL phải là HTTPS và có thể truy cập công khai
- MoMo sẽ gọi IPN URL ngay cả khi Return URL không được gọi

## 4. Test trong môi trường Sandbox

1. Đặt `MOMO_ENVIRONMENT=sandbox`
2. Sử dụng thông tin test từ MoMo
3. Test các trường hợp:
   - Thanh toán thành công
   - Thanh toán thất bại
   - Hủy thanh toán

## 5. Chuyển sang Production

1. Đăng ký tài khoản production với MoMo
2. Lấy thông tin production (Partner Code, Access Key, Secret Key)
3. Cập nhật `.env`:
   ```env
   MOMO_ENVIRONMENT=production
   MOMO_PARTNER_CODE=production_partner_code
   MOMO_ACCESS_KEY=production_access_key
   MOMO_SECRET_KEY=production_secret_key
   ```
4. Đảm bảo URL callback là HTTPS và có thể truy cập công khai

## 6. Kiểm tra hoạt động

Sau khi cấu hình:
1. Đăng nhập với tài khoản sinh viên
2. Vào trang Học phí → Chi tiết học phí
3. Click "Thanh toán qua MoMo"
4. Kiểm tra xem có redirect đến MoMo không
5. Test thanh toán (trong sandbox có thể dùng số điện thoại test)

## 7. Xử lý lỗi thường gặp

### Lỗi: "Invalid signature"
- Kiểm tra lại `MOMO_SECRET_KEY` trong `.env`
- Đảm bảo không có khoảng trắng thừa

### Lỗi: "Cannot connect to MoMo"
- Kiểm tra kết nối internet
- Kiểm tra firewall có chặn không
- Kiểm tra URL endpoint (sandbox vs production)

### Lỗi: "Payment failed"
- Kiểm tra số tiền có hợp lệ không (tối thiểu 1,000 VND)
- Kiểm tra tài khoản MoMo có đủ tiền không (sandbox)
- Kiểm tra log trong `storage/logs/laravel.log`

## 8. Log và Debug

Tất cả log được lưu trong:
- `storage/logs/laravel.log`

Tìm kiếm với từ khóa: `MoMo` để xem các log liên quan.

## 9. Bảo mật

- **KHÔNG** commit file `.env` lên Git
- Bảo mật `MOMO_SECRET_KEY` - đây là thông tin nhạy cảm
- Sử dụng HTTPS cho production
- Kiểm tra signature trong callback để đảm bảo request từ MoMo

## 10. Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra log: `storage/logs/laravel.log`
2. Xem tài liệu MoMo: https://developers.momo.vn/
3. Liên hệ bộ phận IT

