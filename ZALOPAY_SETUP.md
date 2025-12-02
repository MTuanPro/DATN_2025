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

### 3. Xử lý Callback (IPN)

**Route:** `POST /sinh-vien/zalopay/callback`

**MAC Verification:** `HMAC(sha256, key2, data)`

Callback từ ZaloPay server sẽ được xác thực và tự động cập nhật trạng thái thanh toán.

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

## 📞 Liên hệ hỗ trợ

- **Email:** support@zalopay.vn
- **Hotline:** 1900 5555 77
- **Docs:** https://developers.zalopay.vn/v1/general/overview.html

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
