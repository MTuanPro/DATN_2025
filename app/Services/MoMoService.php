<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MoMoService
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;
    private $returnUrl;
    private $notifyUrl;
    private $environment; // 'sandbox' or 'production'

    public function __construct()
    {
        $this->partnerCode = config('services.momo.partner_code');
        $this->accessKey = config('services.momo.access_key');
        $this->secretKey = config('services.momo.secret_key');
        $this->environment = config('services.momo.environment', 'sandbox');
        $this->endpoint = $this->environment === 'production' 
            ? 'https://payment.momo.vn/v2/gateway/api/create'
            : 'https://test-payment.momo.vn/v2/gateway/api/create';
        $this->returnUrl = config('services.momo.return_url');
        $this->notifyUrl = config('services.momo.notify_url');
    }

    /**
     * Create payment request to MoMo
     *
     * @param string $orderId Order ID (unique)
     * @param int $amount Amount in VND
     * @param string $orderInfo Order description
     * @param string $extraData Additional data (JSON string)
     * @return array
     */
    public function createPayment($orderId, $amount, $orderInfo, $extraData = '')
    {
        try {
            $requestId = Str::uuid()->toString();
            $orderId = $orderId . '_' . time(); // Ensure uniqueness

            $rawHash = "accessKey={$this->accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$this->notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$this->partnerCode}&redirectUrl={$this->returnUrl}&requestId={$requestId}&requestType=captureWallet";

            $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

            $data = [
                'partnerCode' => $this->partnerCode,
                'partnerName' => config('services.momo.partner_name', 'Trường Đại Học'),
                'storeId' => config('services.momo.store_id', $this->partnerCode),
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $this->returnUrl,
                'ipnUrl' => $this->notifyUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => 'captureWallet',
                'signature' => $signature,
            ];

            $response = Http::timeout(30)->post($this->endpoint, $data);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['resultCode']) && $result['resultCode'] == 0) {
                    return [
                        'success' => true,
                        'payUrl' => $result['payUrl'],
                        'orderId' => $orderId,
                        'requestId' => $requestId,
                        'message' => 'Tạo yêu cầu thanh toán thành công'
                    ];
                } else {
                    Log::error('MoMo Payment Error', [
                        'resultCode' => $result['resultCode'] ?? 'unknown',
                        'message' => $result['message'] ?? 'Unknown error',
                        'data' => $data
                    ]);

                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Có lỗi xảy ra khi tạo yêu cầu thanh toán'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Không thể kết nối đến MoMo. Vui lòng thử lại sau.'
            ];

        } catch (\Exception $e) {
            Log::error('MoMo Service Exception', [
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
     * Verify payment callback from MoMo
     *
     * @param array $data Callback data from MoMo
     * @return array
     */
    public function verifyCallback($data)
    {
        try {
            $amount = $data['amount'] ?? 0;
            $extraData = $data['extraData'] ?? '';
            $message = $data['message'] ?? '';
            $orderId = $data['orderId'] ?? '';
            $orderInfo = $data['orderInfo'] ?? '';
            $orderType = $data['orderType'] ?? '';
            $requestId = $data['requestId'] ?? '';
            $resultCode = $data['resultCode'] ?? -1;
            $transId = $data['transId'] ?? '';
            $responseTime = $data['responseTime'] ?? '';
            $payType = $data['payType'] ?? '';

            // Create signature to verify
            $rawHash = "accessKey={$this->accessKey}&amount={$amount}&extraData={$extraData}&message={$message}&orderId={$orderId}&orderInfo={$orderInfo}&orderType={$orderType}&partnerCode={$this->partnerCode}&payType={$payType}&requestId={$requestId}&responseTime={$responseTime}&resultCode={$resultCode}&transId={$transId}";
            
            $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

            if ($signature !== ($data['signature'] ?? '')) {
                Log::warning('MoMo Callback Signature Mismatch', [
                    'expected' => $signature,
                    'received' => $data['signature'] ?? '',
                    'data' => $data
                ]);

                return [
                    'success' => false,
                    'valid' => false,
                    'message' => 'Chữ ký không hợp lệ'
                ];
            }

            // Check result code
            // 0 = Success, 1000 = Pending, 1001 = Processing, 1002 = Failed, 1003 = Cancelled
            if ($resultCode == 0) {
                return [
                    'success' => true,
                    'valid' => true,
                    'orderId' => $orderId,
                    'transId' => $transId,
                    'amount' => $amount,
                    'message' => $message,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'valid' => true,
                    'orderId' => $orderId,
                    'resultCode' => $resultCode,
                    'message' => $message,
                    'data' => $data
                ];
            }

        } catch (\Exception $e) {
            Log::error('MoMo Verify Callback Exception', [
                'message' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'valid' => false,
                'message' => 'Lỗi xác thực: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query transaction status
     *
     * @param string $orderId
     * @param string $requestId
     * @return array
     */
    public function queryTransaction($orderId, $requestId)
    {
        try {
            $endpoint = $this->environment === 'production'
                ? 'https://payment.momo.vn/v2/gateway/api/query'
                : 'https://test-payment.momo.vn/v2/gateway/api/query';

            $rawHash = "accessKey={$this->accessKey}&orderId={$orderId}&partnerCode={$this->partnerCode}&requestId={$requestId}";
            $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

            $data = [
                'partnerCode' => $this->partnerCode,
                'requestId' => $requestId,
                'orderId' => $orderId,
                'signature' => $signature,
                'lang' => 'vi',
            ];

            $response = Http::timeout(30)->post($endpoint, $data);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'data' => $result
                ];
            }

            return [
                'success' => false,
                'message' => 'Không thể truy vấn trạng thái giao dịch'
            ];

        } catch (\Exception $e) {
            Log::error('MoMo Query Transaction Exception', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . $e->getMessage()
            ];
        }
    }
}

