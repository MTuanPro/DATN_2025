<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VNPayService
{
    private $tmnCode;
    private $hashSecret;
    private $url;
    private $returnUrl;
    private $ipnUrl;
    private $environment; // 'sandbox' or 'production'

    public function __construct()
    {
        $this->tmnCode = config('services.vnpay.tmn_code');
        $this->hashSecret = config('services.vnpay.hash_secret');
        $this->environment = config('services.vnpay.environment', 'sandbox');
        
        // VNPay URLs
        $this->url = $this->environment === 'production'
            ? 'https://www.vnpay.vn/paymentv2/vpcpay.html'
            : 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        
        $this->returnUrl = config('services.vnpay.return_url');
        $this->ipnUrl = config('services.vnpay.ipn_url');
    }

    /**
     * Create payment URL for VNPay
     *
     * @param string $orderId Order ID (unique)
     * @param int $amount Amount in VND
     * @param string $orderDescription Order description
     * @param string $orderType Order type (default: 'other')
     * @param string $locale Locale (default: 'vn')
     * @return array
     */
    public function createPaymentUrl($orderId, $amount, $orderDescription, $orderType = 'other', $locale = 'vn')
    {
        try {
            $vnp_TxnRef = $orderId; // Mã tham chiếu giao dịch
            $vnp_OrderInfo = $orderDescription; // Thông tin mô tả
            $vnp_OrderType = $orderType; // Loại đơn hàng
            $vnp_Amount = $amount * 100; // VNPay yêu cầu số tiền nhân 100
            $vnp_Locale = $locale; // Ngôn ngữ
            $vnp_IpAddr = request()->ip(); // IP của khách hàng
            $vnp_CreateDate = date('YmdHis'); // Ngày tạo đơn
            $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes')); // Hết hạn sau 15 phút

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $this->tmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $this->returnUrl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => $vnp_ExpireDate,
            ];

            // Add IPN URL if provided
            if ($this->ipnUrl) {
                $inputData["vnp_IpAddr"] = $vnp_IpAddr;
            }

            // Sort data by key
            ksort($inputData);
            
            // Create query string
            $query = http_build_query($inputData);
            
            // Create hash
            $hashdata = $query;
            if (isset($this->hashSecret)) {
                $vnp_SecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
                $inputData['vnp_SecureHash'] = $vnp_SecureHash;
            }

            // Build payment URL
            $paymentUrl = $this->url . '?' . http_build_query($inputData);

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'order_id' => $orderId,
                'message' => 'Tạo URL thanh toán thành công'
            ];

        } catch (\Exception $e) {
            Log::error('VNPay Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment callback from VNPay
     *
     * @param array $data Callback data from VNPay
     * @return array
     */
    public function verifyCallback($data)
    {
        try {
            $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
            unset($data['vnp_SecureHash']);

            // Sort data by key
            ksort($data);
            
            // Create query string
            $query = http_build_query($data);
            
            // Create hash
            $hashdata = $query;
            $vnp_HashSecret = $this->hashSecret;
            $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

            // Verify signature
            if ($secureHash !== $vnp_SecureHash) {
                Log::warning('VNPay Callback Signature Mismatch', [
                    'received' => $vnp_SecureHash,
                    'calculated' => $secureHash,
                    'data' => $data
                ]);

                return [
                    'valid' => false,
                    'success' => false,
                    'message' => 'Chữ ký không hợp lệ'
                ];
            }

            $vnp_ResponseCode = $data['vnp_ResponseCode'] ?? '';
            $vnp_TransactionStatus = $data['vnp_TransactionStatus'] ?? '';
            $vnp_TxnRef = $data['vnp_TxnRef'] ?? '';
            $vnp_Amount = $data['vnp_Amount'] ?? 0;
            $vnp_BankCode = $data['vnp_BankCode'] ?? '';
            $vnp_TransactionNo = $data['vnp_TransactionNo'] ?? '';
            $vnp_OrderInfo = $data['vnp_OrderInfo'] ?? '';

            // Check response code
            // 00 = Success
            if ($vnp_ResponseCode == '00' && $vnp_TransactionStatus == '00') {
                return [
                    'valid' => true,
                    'success' => true,
                    'orderId' => $vnp_TxnRef,
                    'amount' => $vnp_Amount / 100, // Convert back from VNPay format
                    'bankCode' => $vnp_BankCode,
                    'transactionNo' => $vnp_TransactionNo,
                    'orderInfo' => $vnp_OrderInfo,
                    'responseCode' => $vnp_ResponseCode,
                    'message' => 'Giao dịch thành công'
                ];
            } else {
                $errorMessages = [
                    '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
                    '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
                    '10' => 'Xác thực thông tin thẻ/tài khoản không đúng. Quá 3 lần',
                    '11' => 'Đã hết hạn chờ thanh toán. Xin vui lòng thực hiện lại giao dịch',
                    '12' => 'Thẻ/Tài khoản bị khóa.',
                    '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP). Quá 3 lần',
                    '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch.',
                    '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày.',
                    '75' => 'Ngân hàng thanh toán đang bảo trì.',
                    '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
                ];

                $message = $errorMessages[$vnp_ResponseCode] ?? 'Giao dịch thất bại. Mã lỗi: ' . $vnp_ResponseCode;

                return [
                    'valid' => true,
                    'success' => false,
                    'orderId' => $vnp_TxnRef,
                    'responseCode' => $vnp_ResponseCode,
                    'message' => $message
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay Verify Callback Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return [
                'valid' => false,
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác thực: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query transaction status from VNPay
     *
     * @param string $orderId Order ID
     * @return array
     */
    public function queryTransaction($orderId)
    {
        try {
            $vnp_TxnRef = $orderId;
            $vnp_RequestId = Str::uuid()->toString();
            $vnp_Command = "querydr";
            $vnp_TmnCode = $this->tmnCode;
            $vnp_TransactionDate = date('YmdHis');
            $vnp_CreateDate = date('YmdHis');
            $vnp_IpAddr = request()->ip();

            $inputData = [
                "vnp_RequestId" => $vnp_RequestId,
                "vnp_Version" => "2.1.0",
                "vnp_Command" => $vnp_Command,
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_OrderInfo" => "Truy van giao dich",
                "vnp_TransactionDate" => $vnp_TransactionDate,
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_IpAddr" => $vnp_IpAddr,
            ];

            ksort($inputData);
            $query = http_build_query($inputData);
            $hashdata = $query;
            $vnp_SecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);

            $inputData['vnp_SecureHash'] = $vnp_SecureHash;

            $url = $this->environment === 'production'
                ? 'https://www.vnpay.vn/merchant_webapi/api/transaction'
                : 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction';

            $response = \Illuminate\Support\Facades\Http::post($url, $inputData);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'data' => $result
                ];
            }

            return [
                'success' => false,
                'message' => 'Không thể truy vấn giao dịch'
            ];

        } catch (\Exception $e) {
            Log::error('VNPay Query Transaction Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }
}

