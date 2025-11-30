# Hướng dẫn tích hợp ZaloPay Payment Gateway

## 📋 Tổng quan

ZaloPay là cổng thanh toán trực tuyến phổ biến tại Việt Nam, hỗ trợ nhiều phương thức thanh toán:
- Ví điện tử ZaloPay
- Thẻ ATM nội địa
- Thẻ tín dụng quốc tế (Visa, Mastercard, JCB)
- Ngân hàng liên kết

---

## 🚀 Các bước tích hợp

### Bước 1: Đăng ký tài khoản Merchant ZaloPay

1. **Truy cập:** https://merchant.zalopay.vn/
2. **Đăng ký tài khoản** với thông tin doanh nghiệp/cá nhân
3. **Chờ duyệt** (1-3 ngày làm việc)

### Bước 2: Lấy thông tin tích hợp

Sau khi được duyệt, lấy thông tin:
- **APP_ID**: Mã định danh ứng dụng
- **KEY1**: Khóa cho việc tạo MAC
- **KEY2**: Khóa cho việc xác thực callback

### Bước 3: Cấu hình `.env`

```env
ZALOPAY_APP_ID=2553
ZALOPAY_KEY1=PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL
ZALOPAY_KEY2=kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz
ZALOPAY_ENDPOINT=https://sb-openapi.zalopay.vn/v2
```

**Production:** Đổi endpoint thành `https://openapi.zalopay.vn/v2`

### Bước 4: Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📞 Liên hệ hỗ trợ

- **Email:** support@zalopay.vn
- **Hotline:** 1900 5555 77
- **Docs:** https://docs.zalopay.vn/
