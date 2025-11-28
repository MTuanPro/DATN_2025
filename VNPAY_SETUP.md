# Hướng dẫn tích hợp VNPay Payment

## 📋 Các bước tích hợp

1. ✅ Đăng ký tài khoản VNPay (trên điện thoại hoặc máy tính)
2. ✅ Lấy TMN_CODE và HASH_SECRET từ tài khoản VNPay
3. ✅ Cấu hình trong file `.env`
4. ✅ Test thanh toán

---

## 1. Đăng ký tài khoản VNPay

### ⚠️ QUAN TRỌNG: Dùng website trên máy tính, không dùng app điện thoại

**App VNPay trên điện thoại không có chức năng quản lý merchant.** Bạn cần dùng website trên máy tính để:

-   Đăng ký tài khoản merchant
-   Tạo Website/Ứng dụng
-   Lấy TMN_CODE và HASH_SECRET

### Cách đăng ký:

1. **Truy cập website trên máy tính**: https://www.vnpay.vn/
    - ⚠️ **Không dùng app điện thoại** - app không có chức năng merchant
2. **Đăng ký tài khoản merchant**:
    - Click "Đăng ký" / "Đăng nhập"
    - **Đăng ký bằng số điện thoại** (không cần email)
    - Điền thông tin: số điện thoại, mật khẩu, thông tin cá nhân/doanh nghiệp
    - Xác nhận OTP qua SMS (nếu có)
    - Chờ VNPay duyệt (có thể mất vài ngày)

### ✅ Đăng ký bằng số điện thoại:

-   **Không cần email xác nhận** - chỉ cần xác nhận OTP qua SMS
-   Có thể đăng nhập bằng số điện thoại và mật khẩu
-   Sau khi được duyệt, bạn vẫn có thể lấy TMN_CODE và HASH_SECRET

### Sau khi được duyệt:

1. **Đăng nhập vào website VNPay trên máy tính**:

    **Cách 1: Tìm nút đăng nhập trên trang chủ**

    - Truy cập: https://www.vnpay.vn/
    - Tìm nút "Đăng nhập" / "Login" (thường ở góc trên bên phải, có thể là icon người dùng)
    - Hoặc scroll xuống cuối trang, tìm link "Đăng nhập"

    **Cách 2: Truy cập trực tiếp merchant portal** (nếu có)

    - Thử các link sau:
        - https://merchant.vnpay.vn/
        - https://www.vnpay.vn/merchant/
        - https://www.vnpay.vn/login
        - https://www.vnpay.vn/dang-nhap

    **Cách 3: Click vào "Dịch vụ" → "Dành cho Doanh nghiệp"**

    - Từ menu "Dịch vụ" trên trang chủ
    - Chọn "Dành cho Doanh nghiệp" → "Cổng thanh toán VNPAY-QR"
    - Có thể có link đăng nhập merchant ở đó

    **Cách 4: Liên hệ VNPay để được hướng dẫn**

    - Email: support@vnpay.vn
    - Hotline: 1900 5555 77
    - Yêu cầu: "Xin link đăng nhập merchant portal để lấy thông tin tích hợp"
    - Cung cấp: Số điện thoại đã đăng ký

    - Đăng nhập bằng số điện thoại và mật khẩu (nếu đăng ký bằng SĐT)
    - Hoặc đăng nhập bằng email và mật khẩu (nếu đăng ký bằng email)
    - ⚠️ **Phải dùng website trên máy tính**, không dùng app điện thoại

2. **Tìm phần quản lý Website/Ứng dụng** (trên website, không phải app):

    - Menu "Merchant" / "Thương gia"
    - Hoặc "Website Management" / "Quản lý Website"
    - Hoặc "Integration" / "Tích hợp"
    - Hoặc "Thông tin tích hợp"
    - Hoặc "Merchant Portal" / "Cổng thương gia"
    - ⚠️ **Nếu không thấy**: Có thể tài khoản chưa được duyệt hoặc chưa kích hoạt merchant

3. **Tạo Website/Ứng dụng mới**:

    - Click "Thêm mới" / "Add New" / "Tạo mới"
    - Điền thông tin:
        - Tên website: Tên dự án của bạn (ví dụ: "S-MIS DATN 2025")
        - URL website: URL của bạn (ví dụ: `http://localhost:8000` cho test)
        - Mô tả: Mô tả ngắn về website
    - Lưu lại

4. **Lấy thông tin tích hợp** (QUAN TRỌNG):
    - Sau khi tạo website, VNPay sẽ hiển thị:
        - **TmnCode** (Terminal Code) - Ví dụ: `2QXUI4J4`
        - **HashSecret** (Secret Key) - Ví dụ: `RAOCTZKRVJODGIAFUBXWYEQKYSKBYJTX`
    - **⚠️ Lưu lại 2 thông tin này ngay** - HashSecret chỉ hiển thị một lần!
    - Copy và lưu vào file text an toàn

---

## 2. Cấu hình trong file .env

### 2.1. Mở file `.env`

File `.env` nằm ở thư mục gốc của project Laravel.

### 2.2. Thêm cấu hình VNPay

Thêm các dòng sau vào cuối file `.env`:

```env
# ============================================
# VNPay Payment Configuration
# ============================================
VNPAY_TMN_CODE=your_tmn_code_here
VNPAY_HASH_SECRET=your_hash_secret_here
VNPAY_ENVIRONMENT=production
VNPAY_RETURN_URL=http://localhost:8000/payment/vnpay/callback
VNPAY_IPN_URL=http://localhost:8000/payment/vnpay/ipn
```

**Thay thế**:

-   `your_tmn_code_here` → TMN_CODE bạn lấy từ VNPay
-   `your_hash_secret_here` → HASH_SECRET bạn lấy từ VNPay
-   `http://localhost:8000` → URL thực tế của bạn (nếu deploy lên server thì dùng domain thật)

**Ví dụ thực tế**:

```env
VNPAY_TMN_CODE=2QXUI4J4
VNPAY_HASH_SECRET=RAOCTZKRVJODGIAFUBXWYEQKYSKBYJTX
VNPAY_ENVIRONMENT=production
VNPAY_RETURN_URL=http://localhost:8000/payment/vnpay/callback
VNPAY_IPN_URL=http://localhost:8000/payment/vnpay/ipn
```

### 2.3. Clear cache

Sau khi cập nhật `.env`, chạy lệnh:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 3. Test thanh toán

### 3.1. Kiểm tra tích hợp

1. Đăng nhập vào hệ thống với tài khoản sinh viên
2. Vào trang "Học phí" → Chọn một học kỳ
3. Click "Thanh toán qua VNPay"
4. Nhập số tiền thanh toán
5. Click "Thanh toán qua VNPay"
6. Kiểm tra xem có redirect đến trang thanh toán VNPay không

### 3.2. Thực hiện thanh toán test

-   Sử dụng thẻ ngân hàng thật để test (số tiền nhỏ)
-   Hoặc liên hệ VNPay để xin thẻ test

---

## 4. Xử lý lỗi thường gặp

### Lỗi: "Invalid signature"

**Nguyên nhân**: HashSecret không đúng

**Giải pháp**:

-   Kiểm tra lại `VNPAY_HASH_SECRET` trong `.env`
-   Đảm bảo không có ký tự thừa (khoảng trắng, xuống dòng)
-   Clear cache: `php artisan config:clear`

### Lỗi: "Cannot connect to VNPay"

**Nguyên nhân**: URL không đúng

**Giải pháp**:

-   Kiểm tra `VNPAY_ENVIRONMENT` đúng chưa (`production` hoặc `sandbox`)
-   Kiểm tra kết nối mạng

### Lỗi: "Transaction expired"

**Nguyên nhân**: Giao dịch hết hạn (mặc định 15 phút)

**Giải pháp**: Tạo giao dịch mới

---

## 5. Mã lỗi VNPay thường gặp

| Mã lỗi | Mô tả                                              |
| ------ | -------------------------------------------------- |
| 00     | Giao dịch thành công                               |
| 07     | Trừ tiền thành công. Giao dịch bị nghi ngờ         |
| 09     | Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking |
| 10     | Xác thực thông tin thẻ/tài khoản không đúng        |
| 11     | Đã hết hạn chờ thanh toán                          |
| 12     | Thẻ/Tài khoản bị khóa                              |
| 13     | Nhập sai mật khẩu xác thực giao dịch (OTP)         |
| 51     | Tài khoản không đủ số dư                           |
| 65     | Tài khoản đã vượt quá hạn mức giao dịch trong ngày |
| 75     | Ngân hàng thanh toán đang bảo trì                  |
| 79     | Nhập sai mật khẩu thanh toán quá số lần quy định   |

---

## 6. Lưu ý quan trọng

-   **HashSecret**: Là thông tin bảo mật, không chia sẻ công khai, không commit vào git
-   **Số tiền**: VNPay yêu cầu số tiền nhân 100 (ví dụ: 100,000 VND → 10000000)
-   **IPN URL**: Phải có thể truy cập được từ internet (localhost không được, cần dùng ngrok hoặc deploy lên server)
-   **HTTPS**: Sử dụng HTTPS cho production

---

## 7. Liên hệ hỗ trợ

-   **Email**: support@vnpay.vn
-   **Tài liệu**: https://sandbox.vnpayment.vn/apis/

---

## 8. Flow thanh toán

1. Sinh viên chọn "Thanh toán qua VNPay"
2. Nhập số tiền thanh toán
3. Submit form → Tạo giao dịch pending trong database
4. Redirect đến VNPay
5. Sinh viên thanh toán trên VNPay
6. VNPay gọi Return URL (callback) → Cập nhật giao dịch
7. VNPay gọi IPN URL (server-to-server) → Xác nhận lại giao dịch
8. Cập nhật học phí và trạng thái
