<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MoMoService
{
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $endpoint;
    protected $returnUrl;
    protected $notifyUrl;

    public function __construct()
    {
        $this->partnerCode = config('payment.momo.partner_code');
        $this->accessKey = config('payment.momo.access_key');
        $this->secretKey = config('payment.momo.secret_key');
        $this->endpoint = config('payment.momo.endpoint');
        $this->returnUrl = config('payment.momo.return_url');
        $this->notifyUrl = config('payment.momo.notify_url');
    }

    /**
     * Tạo URL thanh toán MoMo
     * 
     * @param string $orderId Mã đơn hàng (hoc_phi_hoc_ky_id)
     * @param int $amount Số tiền (VNĐ)
     * @param string $orderInfo Thông tin đơn hàng
     * @return array Kết quả tạo payment
     */
    public function createPaymentUrl($orderId, $amount, $orderInfo)
    {
        $requestId = $orderId . '_' . time();
        $orderInfo = $orderInfo;
        $redirectUrl = $this->returnUrl;
        $ipnUrl = $this->notifyUrl;
        $extraData = "";
        $requestType = "captureWallet";

        // Tạo chữ ký
        $rawHash = "accessKey=" . $this->accessKey . 
                   "&amount=" . $amount . 
                   "&extraData=" . $extraData . 
                   "&ipnUrl=" . $ipnUrl . 
                   "&orderId=" . $orderId . 
                   "&orderInfo=" . $orderInfo . 
                   "&partnerCode=" . $this->partnerCode . 
                   "&redirectUrl=" . $redirectUrl . 
                   "&requestId=" . $requestId . 
                   "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => "SMIS - Student Management Information System",
            'storeId' => "SMIS_STORE",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->endpoint, $data);

            $result = $response->json();

            Log::info('MoMo Payment Request', [
                'order_id' => $orderId,
                'request_id' => $requestId,
                'amount' => $amount,
                'response' => $result
            ]);

            if (isset($result['payUrl'])) {
                return [
                    'success' => true,
                    'payUrl' => $result['payUrl'],
                    'qrCodeUrl' => $result['qrCodeUrl'] ?? null,
                    'deeplink' => $result['deeplink'] ?? null,
                    'message' => 'Tạo link thanh toán thành công'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Không thể tạo link thanh toán',
                    'errorCode' => $result['resultCode'] ?? 'unknown'
                ];
            }
        } catch (\Exception $e) {
            Log::error('MoMo Payment Error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi kết nối với MoMo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xác thực callback từ MoMo (IPN - Instant Payment Notification)
     * 
     * @param array $data Dữ liệu từ MoMo
     * @return array Kết quả xác thực
     */
    public function verifyCallback($data)
    {
        $signature = $data['signature'] ?? '';
        
        // Tạo lại chữ ký để verify
        $rawHash = "accessKey=" . $this->accessKey . 
                   "&amount=" . ($data['amount'] ?? '') . 
                   "&extraData=" . ($data['extraData'] ?? '') . 
                   "&message=" . ($data['message'] ?? '') . 
                   "&orderId=" . ($data['orderId'] ?? '') . 
                   "&orderInfo=" . ($data['orderInfo'] ?? '') . 
                   "&orderType=" . ($data['orderType'] ?? '') . 
                   "&partnerCode=" . ($data['partnerCode'] ?? '') . 
                   "&payType=" . ($data['payType'] ?? '') . 
                   "&requestId=" . ($data['requestId'] ?? '') . 
                   "&responseTime=" . ($data['responseTime'] ?? '') . 
                   "&resultCode=" . ($data['resultCode'] ?? '') . 
                   "&transId=" . ($data['transId'] ?? '');

        $checkSignature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $result = [
            'valid' => false,
            'message' => '',
            'data' => []
        ];

        if ($signature === $checkSignature) {
            if (isset($data['resultCode']) && $data['resultCode'] == 0) {
                $result['valid'] = true;
                $result['message'] = 'Giao dịch thành công';
                $result['data'] = [
                    'order_id' => $data['orderId'],
                    'request_id' => $data['requestId'],
                    'amount' => $data['amount'],
                    'transaction_id' => $data['transId'],
                    'pay_type' => $data['payType'] ?? 'qr',
                    'response_time' => $data['responseTime'] ?? '',
                    'order_info' => $data['orderInfo'] ?? '',
                ];
            } else {
                $result['message'] = $this->getResponseMessage($data['resultCode'] ?? 99);
                $result['data'] = [
                    'result_code' => $data['resultCode'] ?? 99,
                    'message' => $data['message'] ?? '',
                ];
            }
        } else {
            $result['message'] = 'Chữ ký không hợp lệ';
        }

        Log::info('MoMo Callback Verified', $result);

        return $result;
    }

    /**
     * Kiểm tra trạng thái giao dịch
     */
    public function queryTransaction($orderId, $requestId)
    {
        $rawHash = "accessKey=" . $this->accessKey . 
                   "&orderId=" . $orderId . 
                   "&partnerCode=" . $this->partnerCode . 
                   "&requestId=" . $requestId;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'orderId' => $orderId,
            'requestId' => $requestId,
            'signature' => $signature,
            'lang' => 'vi'
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->endpoint . '/query', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('MoMo Query Error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);

            return [
                'resultCode' => 99,
                'message' => 'Lỗi truy vấn giao dịch'
            ];
        }
    }

    /**
     * Lấy thông báo lỗi từ mã phản hồi
     */
    protected function getResponseMessage($resultCode)
    {
        $messages = [
            0 => 'Giao dịch thành công',
            9000 => 'Giao dịch được khởi tạo, chờ người dùng xác nhận thanh toán',
            8000 => 'Giao dịch đang được xử lý',
            7000 => 'Giao dịch đang chờ xác nhận từ người dùng',
            1000 => 'Giao dịch đã được khởi tạo, chờ người dùng xác nhận thanh toán',
            11 => 'Truy cập bị từ chối',
            12 => 'Phiên bản API không được hỗ trợ cho yêu cầu này',
            13 => 'Xác thực dữ liệu thất bại',
            20 => 'Số tiền không hợp lệ',
            21 => 'Số tiền giao dịch không hợp lệ',
            40 => 'RequestId bị trùng',
            41 => 'OrderId bị trùng',
            42 => 'OrderId không hợp lệ hoặc không được tìm thấy',
            43 => 'Yêu cầu bị từ chối vì xung đột trong quá trình xử lý giao dịch',
            1001 => 'Giao dịch thanh toán thất bại do tài khoản người dùng không đủ tiền',
            1002 => 'Giao dịch bị từ chối do nhà phát hành tài khoản thanh toán',
            1003 => 'Giao dịch bị hủy',
            1004 => 'Giao dịch thất bại do số tiền thanh toán vượt quá hạn mức thanh toán của người dùng',
            1005 => 'Giao dịch thất bại do url hoặc QR code đã hết hạn',
            1006 => 'Giao dịch thất bại do người dùng đã từ chối xác nhận thanh toán',
            1007 => 'Giao dịch bị từ chối vì tài khoản người dùng đang ở trạng thái tạm khóa',
            1026 => 'Giao dịch bị hạn chế theo thể lệ chương trình khuyến mãi',
            1080 => 'Giao dịch hoàn tiền bị từ chối. Giao dịch thanh toán ban đầu không được tìm thấy',
            1081 => 'Giao dịch hoàn tiền bị từ chối. Giao dịch thanh toán ban đầu đã được hoàn',
            2001 => 'Giao dịch thất bại do sai thông tin liên kết',
            2007 => 'Giao dịch thất bại do nhà cung cấp dịch vụ (Merchant) không tồn tại',
            3001 => 'Liên kết thanh toán không tồn tại',
            3002 => 'Liên kết thanh toán đã được sử dụng để thanh toán thành công',
            3003 => 'Liên kết thanh toán đã hết hạn',
            3004 => 'Mã giao dịch không tồn tại',
            4001 => 'Giao dịch bị giới hạn theo ngày',
            4010 => 'Đối tác không được cấu hình để sử dụng dịch vụ thanh toán định kỳ',
            4011 => 'Tài khoản của người dùng chưa được kích hoạt cho dịch vụ thanh toán định kỳ',
            4015 => 'Người dùng đã hủy dịch vụ thanh toán định kỳ',
            4100 => 'Giao dịch thất bại do người dùng không đồng ý điều khoản thanh toán',
            10 => 'Hệ thống đang được bảo trì',
            99 => 'Lỗi không xác định',
        ];

        return $messages[$resultCode] ?? 'Lỗi không xác định';
    }
}
