<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZaloPayService
{
    private $appId;
    private $key1;
    private $key2;
    private $endpoint;
    private $isSandbox;

    public function __construct()
    {
        $this->appId = env('ZALOPAY_APP_ID', '');
        $this->key1 = env('ZALOPAY_KEY1', '');
        $this->key2 = env('ZALOPAY_KEY2', '');
        $this->isSandbox = env('ZALOPAY_SANDBOX', true);
        
        // API v1 endpoints
        if ($this->isSandbox) {
            $this->endpoint = 'https://sandbox.zalopay.com.vn/v001/tpe';
        } else {
            $this->endpoint = 'https://zalopay.com.vn/v001/tpe';
        }
    }

    /**
     * Create ZaloPay order (API v1)
     * 
     * @param string $appTransId Mã giao dịch của ứng dụng (format: yyMMdd_xxxxx)
     * @param int $amount Số tiền (VND)
     * @param string $description Mô tả đơn hàng
     * @param string $appUser Thông tin người dùng (id/username/tên/số điện thoại/email)
     * @param array $items Danh sách sản phẩm (optional)
     * @param array $embedData Dữ liệu nhúng (optional)
     * @param string $bankcode Mã ngân hàng (optional, để trống để user chọn)
     * @return array
     */
    public function createOrder($appTransId, $amount, $description, $appUser = null, $items = [], $embedData = [], $bankcode = '')
    {
        try {
            // Validate credentials
            if (empty($this->appId) || empty($this->key1) || empty($this->key2)) {
                Log::error('ZaloPay credentials not configured', [
                    'appId' => !empty($this->appId),
                    'key1' => !empty($this->key1),
                    'key2' => !empty($this->key2)
                ]);
                return [
                    'returncode' => -1,
                    'returnmessage' => 'ZaloPay chưa được cấu hình. Vui lòng liên hệ quản trị viên.',
                    'orderurl' => ''
                ];
            }

            // Default appuser
            if (!$appUser) {
                $appUser = 'user_' . time();
            }

            // App time (unix timestamp in milliseconds)
            $appTime = round(microtime(true) * 1000);

            // Embed data - thông tin bổ sung
            $embedDataStr = !empty($embedData) ? json_encode($embedData, JSON_UNESCAPED_UNICODE) : '{}';

            // Items - danh sách sản phẩm
            $itemStr = !empty($items) ? json_encode($items, JSON_UNESCAPED_UNICODE) : '[]';

            // Tạo MAC theo công thức ZaloPay v1: appid|apptransid|appuser|amount|apptime|embeddata|item
            $data = $this->appId . '|' . $appTransId . '|' . $appUser . '|' . $amount 
                  . '|' . $appTime . '|' . $embedDataStr . '|' . $itemStr;
            $mac = hash_hmac('sha256', $data, $this->key1);

            // Prepare request parameters
            $params = [
                'appid' => $this->appId,
                'apptransid' => $appTransId,
                'appuser' => $appUser,
                'apptime' => $appTime,
                'amount' => $amount,
                'description' => $description,
                'embeddata' => $embedDataStr,
                'item' => $itemStr,
                'mac' => $mac,
            ];

            // Add bankcode if provided
            if (!empty($bankcode)) {
                $params['bankcode'] = $bankcode;
            }

            // Gửi request đến ZaloPay
            $response = Http::asForm()->post($this->endpoint . '/createorder', $params);

            $result = $response->json();

            Log::info('ZaloPay Create Order Request:', $params);
            Log::info('ZaloPay Create Order Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('ZaloPay Create Order Error: ' . $e->getMessage());
            
            return [
                'returncode' => 0,
                'returnmessage' => 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query order status from ZaloPay (API v1)
     * 
     * @param string $appTransId Mã giao dịch của ứng dụng
     * @return array
     */
    public function queryOrder($appTransId)
    {
        try {
            // Tạo MAC: appid|apptransid|key1
            $data = $this->appId . '|' . $appTransId . '|' . $this->key1;
            $mac = hash_hmac('sha256', $data, $this->key1);

            $params = [
                'appid' => $this->appId,
                'apptransid' => $appTransId,
                'mac' => $mac,
            ];

            // Gửi request query (GET method)
            $response = Http::get($this->endpoint . '/getstatusbyapptransid', $params);

            $result = $response->json();

            Log::info('ZaloPay Query Order Request:', $params);
            Log::info('ZaloPay Query Order Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('ZaloPay Query Order Error: ' . $e->getMessage());
            
            return [
                'returncode' => 0,
                'returnmessage' => 'Có lỗi xảy ra khi truy vấn đơn hàng: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify callback data from ZaloPay (for IPN) - API v1
     * 
     * @param array $callbackData Dữ liệu callback từ ZaloPay
     * @return bool
     */
    public function verifyCallback($callbackData)
    {
        try {
            $requestMac = $callbackData['mac'] ?? '';
            $dataStr = $callbackData['data'] ?? '';
            
            // Tạo lại MAC từ dữ liệu nhận được: HMAC(sha256, key2, data)
            $reqMac = hash_hmac('sha256', $dataStr, $this->key2);

            // So sánh MAC
            $isValid = strcmp($requestMac, $reqMac) === 0;

            if (!$isValid) {
                Log::warning('ZaloPay Callback MAC verification failed', [
                    'request_mac' => $requestMac,
                    'calculated_mac' => $reqMac,
                    'data' => $dataStr
                ]);
            }

            return $isValid;

        } catch (\Exception $e) {
            Log::error('ZaloPay Verify Callback Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse callback data from ZaloPay
     * 
     * @param string $dataStr JSON string from callback
     * @return array|null
     */
    public function parseCallbackData($dataStr)
    {
        try {
            return json_decode($dataStr, true);
        } catch (\Exception $e) {
            Log::error('ZaloPay Parse Callback Data Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Refund order (API v1)
     * 
     * @param int $zpTransId Mã giao dịch ZaloPay
     * @param int $amount Số tiền hoàn (VND)
     * @param string $description Mô tả lý do hoàn tiền
     * @return array
     */
    public function refund($zpTransId, $amount, $description)
    {
        try {
            $timestamp = round(microtime(true) * 1000); // milliseconds
            $uid = $timestamp . rand(111, 999); // unique id

            $mrefundid = date('ymd') . '_' . $this->appId . '_' . $uid;

            // Tạo MAC: appid|zptransid|amount|description|timestamp
            $data = $this->appId . '|' . $zpTransId . '|' . $amount 
                  . '|' . $description . '|' . $timestamp;
            $mac = hash_hmac('sha256', $data, $this->key1);

            $params = [
                'appid' => $this->appId,
                'mrefundid' => $mrefundid,
                'timestamp' => $timestamp,
                'zptransid' => $zpTransId,
                'amount' => $amount,
                'description' => $description,
                'mac' => $mac,
            ];

            // Gửi request refund
            $response = Http::asForm()->post($this->endpoint . '/partialrefund', $params);

            $result = $response->json();

            Log::info('ZaloPay Refund Request:', $params);
            Log::info('ZaloPay Refund Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('ZaloPay Refund Error: ' . $e->getMessage());
            
            return [
                'returncode' => 0,
                'returnmessage' => 'Có lỗi xảy ra khi hoàn tiền: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query refund status (API v1)
     * 
     * @param string $mrefundid Mã hoàn tiền
     * @return array
     */
    public function queryRefundStatus($mrefundid)
    {
        try {
            $timestamp = round(microtime(true) * 1000); // milliseconds

            // Tạo MAC: appid|mrefundid|timestamp
            $data = $this->appId . '|' . $mrefundid . '|' . $timestamp;
            $mac = hash_hmac('sha256', $data, $this->key1);

            $params = [
                'appid' => $this->appId,
                'mrefundid' => $mrefundid,
                'timestamp' => $timestamp,
                'mac' => $mac,
            ];

            // Gửi request query (GET method)
            $response = Http::get($this->endpoint . '/getpartialrefundstatus', $params);

            $result = $response->json();

            Log::info('ZaloPay Query Refund Status Request:', $params);
            Log::info('ZaloPay Query Refund Status Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('ZaloPay Query Refund Status Error: ' . $e->getMessage());
            
            return [
                'returncode' => 0,
                'returnmessage' => 'Có lỗi xảy ra khi truy vấn trạng thái hoàn tiền: ' . $e->getMessage()
            ];
        }
    }
}
