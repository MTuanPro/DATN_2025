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

    public function __construct()
    {
        $this->appId = env('ZALOPAY_APP_ID');
        $this->key1 = env('ZALOPAY_KEY1');
        $this->key2 = env('ZALOPAY_KEY2');
        $this->endpoint = env('ZALOPAY_ENDPOINT', 'https://sb-openapi.zalopay.vn/v2');
    }

    /**
     * Create ZaloPay order
     * 
     * @param string $appTransId Mã giao dịch của ứng dụng (format: yyMMdd_xxxxx)
     * @param int $amount Số tiền (VND)
     * @param string $description Mô tả đơn hàng
     * @param array $embedData Dữ liệu nhúng (tuỳ chọn)
     * @return array
     */
    public function createOrder($appTransId, $amount, $description, $embedData = [])
    {
        try {
            $transId = (int) (microtime(true) * 10000); // timestamp
            $appUser = 'user_' . time(); // Tên người dùng

            // Embed data - thông tin bổ sung
            $embedDataStr = json_encode(array_merge([
                'redirecturl' => route('payment.zalopay.callback')
            ], $embedData));

            // Tạo MAC để xác thực
            $data = [
                'app_id' => $this->appId,
                'app_trans_id' => $appTransId,
                'app_user' => $appUser,
                'app_time' => round(microtime(true) * 1000), // milliseconds
                'embed_data' => $embedDataStr,
                'item' => json_encode([]), // Danh sách sản phẩm (để trống)
                'amount' => $amount,
                'description' => $description,
                'bank_code' => '', // Để trống để user chọn
            ];

            // Tạo MAC theo công thức ZaloPay
            $macData = $data['app_id'] . '|' . $data['app_trans_id'] . '|' . $data['app_user'] . '|' 
                     . $data['amount'] . '|' . $data['app_time'] . '|' . $data['embed_data'] . '|' . $data['item'];
            $data['mac'] = hash_hmac('sha256', $macData, $this->key1);

            // Gửi request đến ZaloPay
            $response = Http::asForm()->post($this->endpoint . '/create', $data);

            $result = $response->json();

            Log::info('ZaloPay Create Order Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('ZaloPay Create Order Error: ' . $e->getMessage());
            
            return [
                'return_code' => 0,
                'return_message' => 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query order status from ZaloPay
     * 
     * @param string $appTransId Mã giao dịch của ứng dụng
     * @return array
     */
    public function queryOrder($appTransId)
    {
        try {
            $data = [
                'app_id' => $this->appId,
                'app_trans_id' => $appTransId,
            ];

            // Tạo MAC
            $macData = $data['app_id'] . '|' . $data['app_trans_id'] . '|' . $this->key1;
            $data['mac'] = hash_hmac('sha256', $macData, $this->key1);

            // Gửi request query
            $response = Http::asForm()->post($this->endpoint . '/query', $data);

            $result = $response->json();

            Log::info('ZaloPay Query Order Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('ZaloPay Query Order Error: ' . $e->getMessage());
            
            return [
                'return_code' => 0,
                'return_message' => 'Có lỗi xảy ra khi truy vấn đơn hàng: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify callback data from ZaloPay (for IPN)
     * 
     * @param array $data Dữ liệu callback từ ZaloPay
     * @return bool
     */
    public function verifyCallback($data)
    {
        try {
            $mac = $data['mac'] ?? '';
            
            // Tạo lại MAC từ dữ liệu nhận được
            $dataStr = $data['data'] ?? '';
            $reqMac = hash_hmac('sha256', $dataStr, $this->key2);

            // So sánh MAC
            return $mac === $reqMac;

        } catch (\Exception $e) {
            Log::error('ZaloPay Verify Callback Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refund order (if needed)
     * 
     * @param string $zpTransId Mã giao dịch ZaloPay
     * @param int $amount Số tiền hoàn (VND)
     * @param string $description Mô tả lý do hoàn tiền
     * @return array
     */
    public function refund($zpTransId, $amount, $description)
    {
        try {
            $timestamp = round(microtime(true) * 1000);
            $uid = $timestamp . ''; // unique id

            $data = [
                'app_id' => $this->appId,
                'm_refund_id' => date('ymd') . '_' . $this->appId . '_' . $uid,
                'timestamp' => $timestamp,
                'zp_trans_id' => $zpTransId,
                'amount' => $amount,
                'description' => $description,
            ];

            // Tạo MAC
            $macData = $data['app_id'] . '|' . $data['zp_trans_id'] . '|' . $data['amount'] 
                     . '|' . $description . '|' . $timestamp;
            $data['mac'] = hash_hmac('sha256', $macData, $this->key1);

            // Gửi request refund
            $response = Http::asForm()->post($this->endpoint . '/refund', $data);

            $result = $response->json();

            Log::info('ZaloPay Refund Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('ZaloPay Refund Error: ' . $e->getMessage());
            
            return [
                'return_code' => 0,
                'return_message' => 'Có lỗi xảy ra khi hoàn tiền: ' . $e->getMessage()
            ];
        }
    }
}
