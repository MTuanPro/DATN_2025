<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VNPayService
{
    protected $vnp_TmnCode;
    protected $vnp_HashSecret;
    protected $vnp_Url;
    protected $vnp_ReturnUrl;

    public function __construct()
    {
        $this->vnp_TmnCode = config('payment.vnpay.tmn_code');
        $this->vnp_HashSecret = config('payment.vnpay.hash_secret');
        $this->vnp_Url = config('payment.vnpay.url');
        $this->vnp_ReturnUrl = config('payment.vnpay.return_url');
    }

    /**
     * Tạo URL thanh toán VNPay
     * 
     * @param string $orderId Mã đơn hàng (hoc_phi_hoc_ky_id)
     * @param int $amount Số tiền (VNĐ)
     * @param string $orderInfo Thông tin đơn hàng
     * @param string $ipAddr IP address của người dùng
     * @return string URL thanh toán
     */
    public function createPaymentUrl($orderId, $amount, $orderInfo, $ipAddr)
    {
        $vnp_TxnRef = $orderId . '_' . time(); // Mã giao dịch unique
        $vnp_OrderInfo = $orderInfo;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100; // VNPay yêu cầu số tiền x 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = ''; // Để trống để hiển thị tất cả phương thức
        $vnp_IpAddr = $ipAddr;

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        Log::info('VNPay Payment URL Created', [
            'order_id' => $orderId,
            'txn_ref' => $vnp_TxnRef,
            'amount' => $amount,
        ]);

        return $vnp_Url;
    }

    /**
     * Xác thực callback từ VNPay
     * 
     * @param array $vnpayData Dữ liệu từ VNPay
     * @return array Kết quả xác thực
     */
    public function verifyCallback($vnpayData)
    {
        $vnp_SecureHash = $vnpayData['vnp_SecureHash'] ?? '';
        unset($vnpayData['vnp_SecureHash']);
        unset($vnpayData['vnp_SecureHashType']);

        ksort($vnpayData);
        $i = 0;
        $hashData = "";
        foreach ($vnpayData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);

        $result = [
            'valid' => false,
            'message' => '',
            'data' => []
        ];

        if ($secureHash == $vnp_SecureHash) {
            if ($vnpayData['vnp_ResponseCode'] == '00') {
                $result['valid'] = true;
                $result['message'] = 'Giao dịch thành công';
                $result['data'] = [
                    'txn_ref' => $vnpayData['vnp_TxnRef'],
                    'amount' => $vnpayData['vnp_Amount'] / 100,
                    'bank_code' => $vnpayData['vnp_BankCode'] ?? '',
                    'transaction_no' => $vnpayData['vnp_TransactionNo'] ?? '',
                    'pay_date' => $vnpayData['vnp_PayDate'] ?? '',
                    'order_info' => $vnpayData['vnp_OrderInfo'] ?? '',
                ];
            } else {
                $result['message'] = $this->getResponseMessage($vnpayData['vnp_ResponseCode']);
                $result['data'] = [
                    'response_code' => $vnpayData['vnp_ResponseCode'],
                ];
            }
        } else {
            $result['message'] = 'Chữ ký không hợp lệ';
        }

        Log::info('VNPay Callback Verified', $result);

        return $result;
    }

    /**
     * Lấy thông báo lỗi từ mã phản hồi
     */
    protected function getResponseMessage($responseCode)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Các lỗi khác',
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}
