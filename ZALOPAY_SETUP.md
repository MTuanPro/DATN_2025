# Hướng dẫn tích hợp ZaloPay Payment Gateway (API v1)

## 📋 Tổng quan

ZaloPay là cổng thanh toán trực tuyến phổ biến tại Việt Nam, hỗ trợ nhiều phương thức thanh toán:
- Ví điện tử ZaloPay
- Thẻ ATM nội địa
- Thẻ tín dụng quốc tế (Visa, Mastercard, JCB)
- Ngân hàng liên kết

**Tài liệu API:** https://developers.zalopay.vn/v1/general/overview.html

---

## 🚀 Các bước tích hợp

### Bước 1: Đăng ký tài khoản Merchant ZaloPay

1. **Truy cập:** https://merchant.zalopay.vn/
2. **Đăng ký tài khoản** với thông tin doanh nghiệp/cá nhân
3. **Chờ duyệt** (1-3 ngày làm việc)

### Bước 2: Lấy thông tin tích hợp

Sau khi được duyệt, lấy thông tin:
- **APP_ID**: Mã định danh ứng dụng
- **KEY1**: Khóa cho việc tạo MAC (dùng cho create order, query, refund)
- **KEY2**: Khóa cho việc xác thực callback (dùng cho IPN verification)

### Bước 3: Cấu hình `.env`

```env
# ZaloPay Configuration (API v1)
ZALOPAY_APP_ID=553
ZALOPAY_KEY1=9phuAOYhan4urywHTh0ndEXiV3pKHr5Q
ZALOPAY_KEY2=Iyz2habzyr7AG8SgvoBCbKwKi3UzlLi3
ZALOPAY_SANDBOX=true
```

**Production:** Đặt `ZALOPAY_SANDBOX=false`

### Bước 4: Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📝 Các API đã tích hợp

### 1. Tạo đơn hàng (`createOrder`)

**Endpoint:**
- Sandbox: `https://sandbox.zalopay.com.vn/v001/tpe/createorder`
- Production: `https://zalopay.com.vn/v001/tpe/createorder`

**MAC Format:** `appid|apptransid|appuser|amount|apptime|embeddata|item`

**Ví dụ:**
```php
$zaloPayService = new ZaloPayService();
$result = $zaloPayService->createOrder(
    $appTransId,      // Format: yyMMdd_xxxxx
    $amount,         // Số tiền (VND)
    $description,    // Mô tả đơn hàng
    $appUser,        // Thông tin người dùng
    $items,          // Danh sách sản phẩm
    $embedData,      // Dữ liệu nhúng
    $bankcode        // Mã ngân hàng (để trống để user chọn)
);
```

### 2. Truy vấn trạng thái đơn hàng (`queryOrder`)

**Endpoint:**
- Sandbox: `https://sandbox.zalopay.com.vn/v001/tpe/getstatusbyapptransid`
- Production: `https://zalopay.com.vn/v001/tpe/getstatusbyapptransid`

**MAC Format:** `appid|apptransid|key1`

### 3. Xử lý Callback (Return URL & IPN)

**Return URL (GET):** `GET /payment/zalopay/callback`
- URL đầy đủ: `http://your-domain.com/payment/zalopay/callback?apptransid=xxx`
- Sau khi thanh toán thành công, ZaloPay sẽ redirect user về URL này
- Hệ thống sẽ query trạng thái thanh toán và redirect về trang chi tiết học phí

**IPN Callback (POST):** `POST /payment/zalopay/callback`
- URL đầy đủ: `http://your-domain.com/payment/zalopay/callback`
- ZaloPay server sẽ gửi POST request đến URL này để thông báo kết quả thanh toán
- **MAC Verification:** `HMAC(sha256, key2, data)`
- Callback từ ZaloPay server sẽ được xác thực và tự động cập nhật trạng thái thanh toán

**Cấu hình Callback URL trong ZaloPay Merchant Portal:**
1. Đăng nhập vào https://merchant.zalopay.vn/
2. Vào phần "Cài đặt" → "Thông tin tích hợp"
3. Cấu hình:
   - **Return URL:** `http://your-domain.com/payment/zalopay/callback`
   - **IPN URL:** `http://your-domain.com/payment/zalopay/callback`
4. Lưu cấu hình

**Sau khi thanh toán thành công:**
- User sẽ được redirect về: `/sinh-vien/hoc-phi/{id}` (trang chi tiết học phí)
- Hiển thị thông báo: "Thanh toán thành công! Mã giao dịch: xxx"

### 4. Hoàn tiền (`refund`)

**Endpoint:**
- Sandbox: `https://sandbox.zalopay.com.vn/v001/tpe/partialrefund`
- Production: `https://zalopay.com.vn/v001/tpe/partialrefund`

**MAC Format:** `appid|zptransid|amount|description|timestamp`

### 5. Truy vấn trạng thái hoàn tiền (`queryRefundStatus`)

**Endpoint:**
- Sandbox: `https://sandbox.zalopay.com.vn/v001/tpe/getpartialrefundstatus`
- Production: `https://zalopay.com.vn/v001/tpe/getpartialrefundstatus`

**MAC Format:** `appid|mrefundid|timestamp`

---

## 🔐 Bảo mật

1. **Không commit KEY1 và KEY2** vào Git
2. **Luôn verify MAC** trước khi xử lý callback
3. **Sử dụng HTTPS** cho tất cả các endpoint
4. **Log tất cả giao dịch** để audit

---

## 💰 Quản lý tiền thanh toán

### Tiền sẽ được chuyển vào đâu?

**Tiền thanh toán sẽ được chuyển vào:**
1. **Tài khoản Merchant ZaloPay** của bạn (tài khoản đã đăng ký tại https://merchant.zalopay.vn/)
2. Sau đó bạn có thể rút tiền về **tài khoản ngân hàng** đã liên kết

### Cấu hình tài khoản ngân hàng nhận tiền

1. **Đăng nhập vào ZaloPay Merchant Portal:**
   - Truy cập: https://merchant.zalopay.vn/
   - Đăng nhập bằng tài khoản merchant của bạn

2. **Vào phần "Quản lý tài chính" hoặc "Tài khoản":**
   - Chọn "Liên kết tài khoản ngân hàng"
   - Nhập thông tin tài khoản ngân hàng:
     - Tên chủ tài khoản
     - Số tài khoản
     - Ngân hàng
     - Chi nhánh
   - Xác thực thông tin

3. **Rút tiền:**
   - Tiền sẽ tự động tích lũy trong tài khoản ZaloPay Merchant
   - Bạn có thể rút tiền về tài khoản ngân hàng đã liên kết
   - Thời gian xử lý: 1-3 ngày làm việc

### Lưu ý quan trọng:

- **Phí giao dịch:** ZaloPay sẽ thu phí giao dịch (thường từ 1-3% tùy loại giao dịch)
- **Thời gian thanh toán:** Tiền sẽ được chuyển vào tài khoản merchant sau khi giao dịch thành công
- **Báo cáo:** Bạn có thể xem báo cáo giao dịch trong ZaloPay Merchant Portal

---

## 📞 Liên hệ hỗ trợ

- **Email:** support@zalopay.vn
- **Hotline:** 1900 5555 77
- **Docs:** https://developers.zalopay.vn/v1/general/overview.html
- **Merchant Portal:** https://merchant.zalopay.vn/

---

## 🧪 Testing

### Sandbox Credentials (Test)

```env
ZALOPAY_APP_ID=553
ZALOPAY_KEY1=9phuAOYhan4urywHTh0ndEXiV3pKHr5Q
ZALOPAY_KEY2=Iyz2habzyr7AG8SgvoBCbKwKi3UzlLi3
ZALOPAY_SANDBOX=true
```

**Lưu ý:** Các thông tin trên chỉ dùng cho môi trường test. Không sử dụng cho production.

---

## 📚 Tài liệu tham khảo

- [ZaloPay Developer Documentation](https://developers.zalopay.vn/v1/general/overview.html)
- [ZaloPay Merchant Portal](https://merchant.zalopay.vn/)
