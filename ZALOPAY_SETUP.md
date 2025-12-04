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
   - **Return URL:** `http://127.0.0.1:8000/payment/zalopay/callback` (cho localhost)
   - **IPN URL:** `http://127.0.0.1:8000/payment/zalopay/callback` (cho localhost)
   - **Lưu ý:** Nếu deploy lên server thật, thay bằng domain của bạn:
     - `https://your-domain.com/payment/zalopay/callback`
4. Lưu cấu hình

**⚠️ QUAN TRỌNG - Nếu Callback URL chưa được cấu hình đúng:**
- ZaloPay sẽ redirect về trang result (`https://qcgateway.zalopay.vn/pay/v2/result?returncode=1&...`)
- Trang này không tự động redirect về hệ thống
- **Giải pháp:** Sau khi thanh toán thành công, bạn cần:
  1. Copy URL từ thanh địa chỉ (có `returncode=1`)
  2. Thêm `?returncode=1` vào URL callback và truy cập: `http://127.0.0.1:8000/payment/zalopay/callback?returncode=1`
  3. Hoặc sử dụng nút "Kiểm tra lại thanh toán ZaloPay" trên trang chi tiết học phí

**⚠️ QUAN TRỌNG - Sửa lỗi Callback URL:**

### Vấn đề: Lỗi "can't reach this page" với URL `newwaycode.tech5s.net:1555`

**Nguyên nhân:**
- URL callback cũ được cấu hình trong ZaloPay Merchant Portal
- Server này không hoạt động hoặc không thể truy cập
- ZaloPay đang cố redirect về URL này sau khi thanh toán

**Giải pháp:**

#### Cách 1: Cập nhật Callback URL trong ZaloPay Merchant Portal (Khuyến nghị)

1. **Đăng nhập vào ZaloPay Merchant Portal:**
   - Truy cập: https://merchant.zalopay.vn/
   - Đăng nhập bằng tài khoản merchant của bạn

2. **Cập nhật Callback URL:**
   - Vào "Cài đặt" → "Thông tin tích hợp"
   - **Return URL:** `http://127.0.0.1:8000/payment/zalopay/callback` (localhost)
   - **IPN URL:** `http://127.0.0.1:8000/payment/zalopay/callback` (localhost)
   - **Production:** Thay bằng domain thật của bạn

3. **Lưu cấu hình**

#### Cách 2: Kiểm tra thủ công nếu không thể truy cập Merchant Portal

Nếu đang dùng sandbox credentials công khai và không có quyền truy cập Merchant Portal:

1. **Sau khi thanh toán thành công:**
   - Lưu lại mã giao dịch (app_trans_id)
   - Quay lại trang học phí

2. **Kiểm tra trạng thái thủ công:**
   ```bash
   php artisan zalopay:check-payment {mã_giao_dịch}
   ```
   
   Ví dụ:
   ```bash
   php artisan zalopay:check-payment 251204_1_220_1764817708
   ```

3. **Hoặc sử dụng trang web:**
   - Vào trang chi tiết học phí
   - Tìm giao dịch vừa tạo
   - Click "Kiểm tra lại thanh toán" (nếu có)

#### Cách 3: Test mà không cần callback

Trong môi trường sandbox, bạn có thể:
- Test thanh toán thành công
- Sau đó kiểm tra trạng thái thủ công bằng command hoặc trang web
- Hệ thống sẽ tự động cập nhật khi kiểm tra

**Lưu ý:**
- Callback URL chỉ ảnh hưởng đến việc tự động redirect sau thanh toán
- Bạn vẫn có thể kiểm tra và cập nhật trạng thái thủ công
- IPN callback (từ ZaloPay server) vẫn hoạt động nếu được cấu hình đúng

**Sau khi thanh toán thành công:**
- User sẽ được redirect về: `/sinh-vien/hoc-phi/{id}` (trang chi tiết học phí)
- Hiển thị thông báo: "Thanh toán thành công! Mã giao dịch: xxx"

**⚠️ Nếu vẫn bị quay về trang result của ZaloPay:**
- Đây là do Callback URL chưa được cấu hình đúng trong ZaloPay Merchant Portal
- **Giải pháp tạm thời:** Sau khi thanh toán thành công trên trang ZaloPay:
  1. Copy URL từ thanh địa chỉ (có `returncode=1`)
  2. Thay `https://qcgateway.zalopay.vn/pay/v2/result` bằng `http://127.0.0.1:8000/payment/zalopay/callback`
  3. Truy cập URL mới để hệ thống xử lý và redirect về trang chi tiết học phí
- **Giải pháp lâu dài:** Cấu hình đúng Callback URL trong ZaloPay Merchant Portal (xem hướng dẫn ở trên)

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

## ⚠️ Xử lý sự cố

### Vấn đề: Không có tên tài khoản khi quét QR code

**Triệu chứng:** Khi quét QR code và chuyển tiền qua ngân hàng, không thấy tên tài khoản người nhận.

**Nguyên nhân:**
- Tên tài khoản người nhận được cấu hình trong **ZaloPay Merchant Portal**, không phải trong code
- Cần đăng nhập vào merchant portal để cấu hình thông tin tài khoản

**Giải pháp:**
1. Đăng nhập vào https://merchant.zalopay.vn/
2. Vào phần "Cài đặt" → "Thông tin tài khoản" hoặc "Thông tin merchant"
3. Cập nhật tên tài khoản người nhận
4. Lưu cấu hình

**Lưu ý:** Sau khi cập nhật, có thể cần đợi vài phút để thay đổi có hiệu lực.

### Vấn đề: Không thấy các phương thức thanh toán khác (ZaloPay wallet, thẻ ATM/Visa/Mastercard)

**Triệu chứng:** Khi quét QR code hoặc vào trang thanh toán, chỉ thấy một phương thức (thường là chuyển khoản ngân hàng), không thấy:
- ZaloPay wallet
- Thẻ ATM nội địa
- Thẻ tín dụng quốc tế (Visa, Mastercard, JCB)

**Nguyên nhân chính:**

1. **Merchant account chưa được kích hoạt đầy đủ các phương thức thanh toán:**
   - Đây là nguyên nhân phổ biến nhất
   - Cần đăng nhập vào ZaloPay Merchant Portal để kích hoạt

2. **Môi trường Sandbox:**
   - Trong môi trường sandbox, một số phương thức có thể bị hạn chế
   - Sandbox có thể chỉ hỗ trợ một số phương thức cơ bản

3. **Merchant account chưa được duyệt đầy đủ:**
   - Merchant account cần được ZaloPay duyệt và kích hoạt đầy đủ
   - Một số phương thức cần thời gian để kích hoạt

**Giải pháp chi tiết:**

#### Bước 1: Kiểm tra và kích hoạt trong ZaloPay Merchant Portal

1. **Đăng nhập vào ZaloPay Merchant Portal:**
   - Truy cập: https://merchant.zalopay.vn/
   - Đăng nhập bằng tài khoản merchant của bạn

2. **Kiểm tra cấu hình phương thức thanh toán:**
   - Vào **"Cài đặt"** → **"Phương thức thanh toán"** hoặc **"Payment Methods"**
   - Kiểm tra các phương thức đã được kích hoạt:
     - ✅ ZaloPay Wallet
     - ✅ Ngân hàng (Bank Transfer)
     - ✅ Thẻ ATM nội địa
     - ✅ Thẻ tín dụng quốc tế (Visa, Mastercard, JCB)

3. **Kích hoạt các phương thức chưa được bật:**
   - Bật tất cả các phương thức bạn muốn hỗ trợ
   - Lưu cấu hình
   - **Lưu ý:** Có thể cần đợi vài phút để thay đổi có hiệu lực

#### Bước 2: Kiểm tra cấu hình Merchant Account

1. **Vào "Thông tin tài khoản" hoặc "Account Information":**
   - Kiểm tra merchant account đã được duyệt chưa
   - Đảm bảo trạng thái là "Đã duyệt" hoặc "Approved"

2. **Kiểm tra "Thông tin tích hợp" hoặc "Integration Info":**
   - Đảm bảo AppID và Keys đã được cấu hình đúng
   - Kiểm tra callback URL đã được cấu hình

#### Bước 3: Liên hệ ZaloPay Support (nếu cần)

Nếu sau khi kiểm tra và kích hoạt mà vẫn không thấy các phương thức:

1. **Liên hệ ZaloPay Support:**
   - Email: support@zalopay.vn
   - Hotline: 1900 5555 77
   - Yêu cầu: "Kích hoạt đầy đủ các phương thức thanh toán cho merchant account"

2. **Cung cấp thông tin:**
   - AppID: 554 (hoặc AppID của bạn)
   - Mô tả vấn đề: Chỉ thấy một phương thức thanh toán, không thấy ZaloPay wallet và thẻ

#### Bước 4: Kiểm tra Code (đã đúng)

Code hiện tại đã được cấu hình đúng:
- ✅ Không truyền `bankcode` → Cho phép user chọn tất cả phương thức
- ✅ Sử dụng đúng endpoint sandbox/production
- ✅ Request format đúng theo tài liệu ZaloPay

**Lưu ý quan trọng:**
- Code không thể kiểm soát việc hiển thị phương thức thanh toán
- Việc hiển thị phương thức phụ thuộc vào cấu hình merchant account trong ZaloPay Merchant Portal
- Trong môi trường sandbox, một số phương thức có thể bị hạn chế

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

### Cách test thanh toán trong Sandbox (không chuyển tiền thật)

**Môi trường Sandbox cho phép test thanh toán mà không cần chuyển tiền thật:**

#### Thông tin thẻ test (Test Cards)

ZaloPay cung cấp các thẻ test để test thanh toán mà không cần tiền thật:

**1. Thẻ Visa, Master, JCB (Quốc tế):**
- **Số thẻ:** `4111111111111111`
- **Tên chủ thẻ:** `NGUYEN VAN A`
- **Ngày hết hạn:** `01/25` (hoặc bất kỳ ngày tương lai nào)
- **Mã CVV:** `123`

**2. Thẻ ATM nội địa (Test với ngân hàng SBI):**
- **Thẻ hợp lệ:**
  - Số thẻ: `9704540000000062` | Tên: `NGUYEN VAN A`
  - Số thẻ: `9704540000000070` | Tên: `NGUYEN VAN`
  - Số thẻ: `9704540000000088` | Tên: `NGUYEN VAN`

**3. Các trường hợp test khác:**
- **Thẻ hết tiền:** Dùng để test trường hợp thanh toán thất bại
- **Thẻ timeout:** Dùng để test trường hợp timeout
- **Thẻ bị đánh cắp:** Dùng để test trường hợp thẻ không hợp lệ

**Lưu ý:** Các thẻ này chỉ dùng trong môi trường **Sandbox**, không dùng được trong Production.

#### Bước 1: Kiểm tra cấu hình

1. **Đảm bảo `ZALOPAY_SANDBOX=true` trong file `.env`**
   ```env
   ZALOPAY_APP_ID=553
   ZALOPAY_KEY1=9phuAOYhan4urywHTh0ndEXiV3pKHr5Q
   ZALOPAY_KEY2=Iyz2habzyr7AG8SgvoBCbKwKi3UzlLi3
   ZALOPAY_SANDBOX=true
   ```

2. **Clear cache để áp dụng cấu hình mới**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

#### Bước 2: Tạo đơn hàng test

1. **Đăng nhập vào hệ thống** với tài khoản sinh viên
2. **Vào trang "Học phí"** → Chọn học phí cần thanh toán
3. **Click "Thanh toán qua ZaloPay"**
4. **Nhập số tiền test** (ví dụ: 3,000đ)
5. **Click "Thanh toán ngay qua ZaloPay"**
6. **Hệ thống sẽ hiển thị:**
   - ✅ Thông báo "Chế độ TEST" (màu vàng)
   - ✅ QR code để quét
   - ✅ URL thanh toán

#### Bước 3: Test thanh toán (KHÔNG CHUYỂN TIỀN THẬT)

**⚠️ Lưu ý về Mã OTP:**
- **Mã OTP là bắt buộc** trong quy trình thanh toán ZaloPay và **KHÔNG THỂ BỎ QUA** từ phía merchant
- OTP được ZaloPay yêu cầu để xác thực người dùng và bảo mật giao dịch
- **Đây là tính năng bảo mật của ZaloPay, không thể tắt hoặc bỏ qua**

**✅ Giải pháp để test không cần OTP:**
- **Sử dụng thẻ test (Visa/Master/ATM)** - **KHÔNG YÊU CẦU OTP** từ ZaloPay
- Thẻ test được ZaloPay cung cấp đặc biệt cho môi trường sandbox
- Khi thanh toán bằng thẻ test, bạn chỉ cần nhập thông tin thẻ, không cần OTP
  - Hoặc sử dụng tài khoản ZaloPay sandbox test (nếu có)

**Cách 1: Thanh toán bằng thẻ test (Khuyến nghị - KHÔNG CẦN OTP)**

**⚠️ QUAN TRỌNG:** Để **KHÔNG CẦN OTP**, bạn **PHẢI** chọn **"Thẻ quốc tế"** (Visa/Master/JCB), **KHÔNG PHẢI** "Thẻ ATM"!

**Lý do:** 
- **"Thẻ quốc tế"** (Visa/Master/JCB) với thẻ test: **KHÔNG CẦN OTP** ✅
- **"Thẻ ATM"** hoặc **"Thẻ/tài khoản nội địa"**: **VẪN YÊU CẦU OTP** ❌ (ngay cả với thẻ test)

**Hướng dẫn chi tiết:**
1. Sau khi tạo đơn hàng, bạn sẽ được redirect đến trang thanh toán ZaloPay
2. **Tìm và chọn phương thức: "Thẻ quốc tế"** (International Card) - có logo Visa, Mastercard, JCB
3. **KHÔNG chọn "Thẻ ATM" hoặc "Thẻ/tài khoản nội địa"** - những phương thức này vẫn yêu cầu OTP
4. Nhập thông tin thẻ test:
   - **Số thẻ:** `4111111111111111`
   - **Tên chủ thẻ:** `NGUYEN VAN A`
   - **Ngày hết hạn:** `01/25` (hoặc bất kỳ ngày tương lai)
   - **CVV:** `123`
5. Click "Thanh toán" hoặc "Pay"
6. **✅ Kết quả:** Thanh toán thành công **KHÔNG CẦN OTP**!
7. **Lưu ý:** Trong sandbox, bạn có thể test thanh toán mà **KHÔNG CẦN CHUYỂN TIỀN THẬT**
8. Hệ thống sẽ tự động redirect về và cập nhật trạng thái

**Cách 2: Thanh toán bằng ZaloPay wallet (CẦN OTP)**
1. Chọn phương thức: **"ZaloPay"** hoặc **"Ví ZaloPay"**
2. Đăng nhập tài khoản ZaloPay sandbox (nếu có)
3. **⚠️ Sẽ yêu cầu mã OTP** để xác thực
4. Nhập mã OTP nhận được từ ZaloPay
5. Xác nhận thanh toán

**Cách 2: Quét QR code**
1. Chọn tab **"Quét QR Code"**
2. Mở ứng dụng ZaloPay trên điện thoại
3. Chọn "Quét mã" hoặc "Scan QR"
4. Quét QR code trên màn hình
5. Chọn phương thức thanh toán và test
6. **Lưu ý:** Trong sandbox, bạn có thể test thanh toán mà **KHÔNG CẦN CHUYỂN TIỀN THẬT**

**Cách 3: Mở trong tab mới**
1. Click vào nút "Mở trang thanh toán ZaloPay" hoặc "Mở trong tab mới"
2. Trang ZaloPay gateway sẽ mở ra
3. Chọn phương thức thanh toán và test với thẻ test

#### Bước 4: Kiểm tra kết quả

1. **Sau khi thanh toán thành công:**
   - Hệ thống sẽ tự động redirect về trang chi tiết học phí
   - Hiển thị thông báo "Thanh toán thành công!"
   - Số tiền đã đóng sẽ được cập nhật

2. **Kiểm tra trong database:**
   - Vào trang "Lịch sử thanh toán" để xem giao dịch
   - Ghi chú sẽ hiển thị: "Thanh toán thành công qua ZaloPay"

3. **Kiểm tra log (nếu cần):**
   ```bash
   tail -f storage/logs/laravel.log
   ```

#### Bước 5: Test các trường hợp khác

- ✅ Test thanh toán thành công
- ✅ Test hủy giao dịch
- ✅ Test timeout (để hết thời gian)
- ✅ Test với số tiền khác nhau
- ✅ Test callback và IPN

#### Lưu ý quan trọng:

⚠️ **Trong môi trường Sandbox:**
- ✅ **KHÔNG chuyển tiền thật** - đây chỉ là test
- ✅ Có thể test nhiều lần mà không lo mất tiền
- ✅ Tất cả giao dịch đều là giả lập
- ✅ Callback và IPN vẫn hoạt động bình thường

⚠️ **Khi chuyển sang Production:**
- Đặt `ZALOPAY_SANDBOX=false` trong `.env`
- Sử dụng credentials production từ ZaloPay merchant portal
- **CẢNH BÁO:** Trong production, thanh toán sẽ **CHUYỂN TIỀN THẬT**!

#### Troubleshooting:

**Nếu thanh toán thành công nhưng hệ thống không cập nhật:**
```bash
# Kiểm tra trạng thái thanh toán thủ công
php artisan zalopay:check-payment {mã_giao_dịch}

# Ví dụ:
php artisan zalopay:check-payment 251203_1_220_1764758053
```

**Lợi ích của Sandbox:**
- ✅ Test toàn bộ flow thanh toán mà không cần tiền thật
- ✅ Kiểm tra callback và IPN hoạt động đúng
- ✅ Test các trường hợp lỗi
- ✅ An toàn cho development và testing
- ✅ Có thể test nhiều lần không giới hạn

---

## 📚 Tài liệu tham khảo

- [ZaloPay Developer Documentation](https://developers.zalopay.vn/v1/general/overview.html)
- [ZaloPay Merchant Portal](https://merchant.zalopay.vn/)
