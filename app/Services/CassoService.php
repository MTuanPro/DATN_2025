<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CassoService
{
    private $apiKey;
    private $apiEndpoint;
    private $memoPrefix;
    private $acceptableDifference;

    public function __construct()
    {
        $this->apiKey = env('CASSO_API_KEY', '');
        $this->apiEndpoint = env('CASSO_API_ENDPOINT', 'https://oauth.casso.vn/v2');
        $this->memoPrefix = env('CASSO_MEMO_PREFIX', 'HP');
        $this->acceptableDifference = (int) env('CASSO_ACCEPTABLE_DIFFERENCE', 10000);
        
        // Log initialization for debugging
        Log::info('CassoService initialized', [
            'has_api_key' => !empty($this->apiKey),
            'api_endpoint' => $this->apiEndpoint,
            'memo_prefix' => $this->memoPrefix
        ]);
    }

    /**
     * Verify webhook request from Casso
     * 
     * Note: Secure-Token được cấu hình trong Casso dashboard khi setup webhook
     * Nếu không có Secure-Token trong header, có thể bỏ qua verification (tùy chọn)
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function verifyWebhook($request)
    {
        try {
            $secureToken = $request->header('Secure-Token');
            
            // Nếu có Secure-Token trong header, kiểm tra với API key (hoặc có thể bỏ qua nếu không cần)
            // Secure-Token thường được cấu hình riêng trong Casso dashboard
            if (!empty($secureToken)) {
                // Log để debug
                Log::info('Casso Webhook - Received Secure-Token', [
                    'has_token' => !empty($secureToken),
                    'token_length' => strlen($secureToken)
                ]);
                
                // Nếu bạn đã cấu hình Secure-Token trong Casso dashboard,
                // bạn có thể so sánh ở đây. Hiện tại tạm thời chấp nhận nếu có token
                // (Bạn có thể thêm env('CASSO_WEBHOOK_TOKEN') nếu cần verify chặt chẽ hơn)
                return true;
            }

            // Nếu không có Secure-Token, vẫn chấp nhận (tùy chọn - có thể thay đổi)
            // Trong production, nên verify chặt chẽ hơn
            Log::warning('Casso Webhook - No Secure-Token in header', [
                'headers' => $request->headers->all()
            ]);
            
            // Tạm thời chấp nhận nếu không có token (có thể thay đổi thành false nếu cần bảo mật hơn)
            return true;

        } catch (\Exception $e) {
            Log::error('Casso Verify Webhook Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse order ID from transaction description
     * 
     * @param string $description Nội dung chuyển khoản (ví dụ: "HP123" hoặc "Thanh toan HP123")
     * @return int|null Order ID hoặc null nếu không tìm thấy
     */
    public function parseOrderId($description)
    {
        try {
            // Tìm pattern: HP + số (không phân biệt hoa thường)
            $pattern = '/' . preg_quote($this->memoPrefix, '/') . '\d+/i';
            preg_match_all($pattern, $description, $matches, PREG_SET_ORDER, 0);

            if (count($matches) == 0) {
                return null;
            }

            // Lấy match đầu tiên
            $orderCode = $matches[0][0];
            
            // Loại bỏ prefix để lấy số
            $prefixLength = strlen($this->memoPrefix);
            $orderId = intval(substr($orderCode, $prefixLength));

            return $orderId;

        } catch (\Exception $e) {
            Log::error('Casso Parse Order ID Error: ' . $e->getMessage(), [
                'description' => $description
            ]);
            return null;
        }
    }

    /**
     * Get user info from Casso API
     * 
     * @return array
     */
    public function getUserInfo()
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('Casso API Key not configured');
                return [
                    'error' => 1,
                    'message' => 'Casso API Key chưa được cấu hình'
                ];
            }

            $url = $this->apiEndpoint . '/userInfo';
            Log::info('Casso - Calling UserInfo API', [
                'url' => $url,
                'has_api_key' => !empty($this->apiKey)
            ]);
            
            $response = Http::timeout(10)
                ->retry(1, 100)
                ->withHeaders([
                    'Authorization' => 'Apikey ' . $this->apiKey
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::error('Casso Get User Info HTTP Error:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'error' => 1,
                    'message' => 'Không thể kết nối đến Casso API'
                ];
            }

            $result = $response->json();
            Log::info('Casso Get User Info Response:', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('Casso Get User Info Error: ' . $e->getMessage());
            
            return [
                'error' => 1,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get bank accounts from Casso API
     * 
     * @return array
     */
    public function getBankAccounts()
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('Casso API Key not configured');
                return [
                    'error' => 1,
                    'message' => 'Casso API Key chưa được cấu hình'
                ];
            }

            // Đầu tiên, thử gọi /userInfo để verify API key hoạt động
            $userInfoUrl = $this->apiEndpoint . '/userInfo';
            Log::info('Casso - Verifying API key with userInfo', [
                'url' => $userInfoUrl
            ]);
            
            try {
                $userInfoResponse = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Apikey ' . $this->apiKey,
                        'Content-Type' => 'application/json'
                    ])
                    ->get($userInfoUrl);
                
                if (!$userInfoResponse->successful()) {
                    Log::error('Casso - API Key verification failed', [
                        'status' => $userInfoResponse->status(),
                        'body' => $userInfoResponse->body()
                    ]);
                    
                    return [
                        'error' => 1,
                        'message' => 'API Key không hợp lệ hoặc không có quyền truy cập. Status: ' . $userInfoResponse->status(),
                        'details' => $userInfoResponse->body()
                    ];
                }
                
                $userInfo = $userInfoResponse->json();
                Log::info('Casso - API Key verified successfully', [
                    'user_info' => $userInfo
                ]);
            } catch (\Exception $e) {
                Log::error('Casso - Error verifying API key', [
                    'error' => $e->getMessage()
                ]);
                
                return [
                    'error' => 1,
                    'message' => 'Không thể xác thực API Key: ' . $e->getMessage()
                ];
            }

            // Sau đó, thử các endpoint khác nhau để lấy thông tin tài khoản ngân hàng
            $endpoints = [
                '/sync/banks',  // Endpoint phổ biến nhất
                '/banks',
                '/bank-accounts',
                '/accounts'
            ];
            
            $result = null;
            $lastError = null;
            
            foreach ($endpoints as $endpoint) {
                $url = $this->apiEndpoint . $endpoint;
                Log::info('Casso - Trying bank accounts endpoint', [
                    'url' => $url
                ]);
                
                try {
                    $response = Http::timeout(10)
                        ->retry(1, 100)
                        ->withHeaders([
                            'Authorization' => 'Apikey ' . $this->apiKey,
                            'Content-Type' => 'application/json'
                        ])
                        ->get($url);

                    if ($response->successful()) {
                        $result = $response->json();
                        Log::info('Casso Get Bank Accounts Success:', [
                            'endpoint' => $endpoint,
                            'response_keys' => is_array($result) ? array_keys($result) : 'not_array'
                        ]);
                        break; // Thành công, dừng lại
                    } else {
                        $lastError = [
                            'endpoint' => $endpoint,
                            'status' => $response->status(),
                            'body' => substr($response->body(), 0, 500) // Giới hạn độ dài
                        ];
                        Log::warning('Casso Get Bank Accounts Failed:', $lastError);
                    }
                } catch (\Exception $e) {
                    $lastError = [
                        'endpoint' => $endpoint,
                        'error' => $e->getMessage()
                    ];
                    Log::warning('Casso Get Bank Accounts Exception:', $lastError);
                }
            }
            
            if (!$result) {
                Log::error('Casso Get Bank Accounts - All endpoints failed', [
                    'last_error' => $lastError
                ]);
                
                return [
                    'error' => 1,
                    'message' => 'Không thể lấy thông tin tài khoản ngân hàng. API Key đã được xác thực nhưng không tìm thấy endpoint phù hợp.',
                    'details' => $lastError
                ];
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Casso Get Bank Accounts Error: ' . $e->getMessage());
            
            return [
                'error' => 1,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if payment amount is acceptable
     * 
     * @param int $paidAmount Số tiền đã nhận
     * @param int $requiredAmount Số tiền cần thanh toán
     * @return array ['status' => 'exact|under|over', 'message' => string]
     */
    public function checkPaymentAmount($paidAmount, $requiredAmount)
    {
        $difference = abs($paidAmount - $requiredAmount);
        $acceptableDiff = abs($this->acceptableDifference);

        if ($paidAmount < $requiredAmount - $acceptableDiff) {
            return [
                'status' => 'under',
                'message' => 'Thanh toán thiếu',
                'difference' => $requiredAmount - $paidAmount
            ];
        } elseif ($paidAmount <= $requiredAmount + $acceptableDiff) {
            return [
                'status' => 'exact',
                'message' => 'Thanh toán đủ',
                'difference' => $difference
            ];
        } else {
            return [
                'status' => 'over',
                'message' => 'Thanh toán dư',
                'difference' => $paidAmount - $requiredAmount
            ];
        }
    }

    /**
     * Generate payment memo for transaction
     * Format: {PREFIX}{ORDER_ID}
     * 
     * @param int $orderId
     * @return string
     */
    public function generatePaymentMemo($orderId)
    {
        return strtoupper($this->memoPrefix) . $orderId;
    }

    /**
     * Get transactions from Casso API
     * 
     * @param array $params Query parameters (fromDate, toDate, etc.)
     * @return array
     */
    public function getTransactions($params = [])
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('Casso API Key not configured');
                return [
                    'error' => 1,
                    'message' => 'Casso API Key chưa được cấu hình'
                ];
            }

            $url = $this->apiEndpoint . '/transactions';
            
            // Thêm query parameters nếu có
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            
            Log::info('Casso - Getting transactions', [
                'url' => $url
            ]);
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Apikey ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::error('Casso Get Transactions HTTP Error:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'error' => 1,
                    'message' => 'Không thể lấy danh sách giao dịch từ Casso API'
                ];
            }

            $result = $response->json();
            
            // Log chi tiết để debug
            Log::info('Casso Get Transactions Response:', [
                'has_data' => isset($result['data']),
                'data_count' => isset($result['data']) ? count($result['data']) : 0,
                'response_keys' => is_array($result) ? array_keys($result) : 'not_array',
                'error' => $result['error'] ?? 'no_error',
                'sample_transactions' => isset($result['data']) && is_array($result['data']) 
                    ? array_slice($result['data'], 0, 3) 
                    : 'no_data'
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Casso Get Transactions Error: ' . $e->getMessage());
            
            return [
                'error' => 1,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Find transaction by payment memo (order ID in description)
     * 
     * @param string $paymentMemo Mã đơn hàng (ví dụ: HP1)
     * @param int $amount Số tiền cần tìm (optional)
     * @return array|null Transaction data or null if not found
     */
    public function findTransactionByMemo($paymentMemo, $amount = null)
    {
        try {
            // Lấy giao dịch trong 30 ngày gần nhất (tăng từ 7 ngày)
            $fromDate = now()->subDays(30)->format('Y-m-d');
            $toDate = now()->format('Y-m-d');
            
            Log::info('Casso - Searching transactions', [
                'payment_memo' => $paymentMemo,
                'memo_length' => strlen($paymentMemo),
                'memo_upper' => strtoupper($paymentMemo),
                'memo_lower' => strtolower($paymentMemo),
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'amount_filter' => $amount
            ]);
            
            $result = $this->getTransactions([
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ]);

            if (isset($result['error']) && $result['error'] != 0) {
                Log::error('Casso - Get transactions failed', [
                    'error' => $result['error'] ?? 'unknown',
                    'message' => $result['message'] ?? 'No message'
                ]);
                return null;
            }

            // Casso API trả về format: {error: 0, data: {records: [...]}}
            // Hoặc có thể là: {error: 0, data: [...]}
            $transactions = [];
            
            if (isset($result['data'])) {
                // Nếu data có records (format mới) - ĐÂY LÀ FORMAT ĐÚNG
                if (isset($result['data']['records']) && is_array($result['data']['records'])) {
                    $transactions = $result['data']['records'];
                    Log::info('Casso - Using data.records format', [
                        'records_count' => count($transactions),
                        'first_record_keys' => !empty($transactions) ? array_keys($transactions[0]) : []
                    ]);
                }
                // Nếu data là array trực tiếp (format cũ)
                elseif (is_array($result['data']) && !isset($result['data']['records'])) {
                    $transactions = $result['data'];
                    Log::info('Casso - Using data array format (old)', [
                        'data_count' => count($transactions)
                    ]);
                }
            }
            
            Log::info('Casso - Transactions retrieved', [
                'count' => count($transactions),
                'payment_memo' => $paymentMemo,
                'first_transaction_sample' => !empty($transactions) ? [
                    'description' => $transactions[0]['description'] ?? 'no_description',
                    'amount' => $transactions[0]['amount'] ?? 'no_amount',
                    'keys' => array_keys($transactions[0] ?? []),
                    'has_description' => isset($transactions[0]['description']),
                    'full_first_transaction' => $transactions[0] // Log toàn bộ để debug
                ] : 'no_transactions'
            ]);
            
            // Tìm giao dịch có mô tả chứa payment memo
            $foundTransactions = [];
            foreach ($transactions as $index => $transaction) {
                $description = $transaction['description'] ?? $transaction['Description'] ?? '';
                $transactionAmount = (int) ($transaction['amount'] ?? $transaction['Amount'] ?? 0);
                
                // Kiểm tra xem mô tả có chứa mã đơn hàng không (không phân biệt hoa thường)
                // CÁCH ĐƠN GIẢN NHẤT - chỉ cần có chứa memo trong description
                $hasMemo = stripos($description, $paymentMemo) !== false;
                
                // Log để debug - LOG TẤT CẢ TRANSACTIONS (ít nhất 10 đầu tiên)
                if ($index < 10) {
                    $hasHP = stripos($description, 'HP') !== false;
                    Log::info('Casso - Checking transaction', [
                        'index' => $index,
                        'payment_memo' => $paymentMemo,
                        'description_preview' => substr($description, 0, 150),
                        'has_memo' => $hasMemo ? 'YES' : 'NO',
                        'has_hp' => $hasHP ? 'YES' : 'NO',
                        'amount' => $transactionAmount,
                        'stripos_result' => stripos($description, $paymentMemo) !== false ? 'FOUND' : 'NOT_FOUND'
                    ]);
                }
                
                // Log khi tìm thấy
                if ($hasMemo) {
                    Log::info('Casso - ✅ FOUND MATCHING TRANSACTION', [
                        'payment_memo' => $paymentMemo,
                        'description' => $description,
                        'amount' => $transactionAmount,
                        'transaction_id' => $transaction['id'] ?? 'unknown'
                    ]);
                }
                
                if ($hasMemo) {
                    // Thêm vào danh sách tìm thấy (sẽ check amount sau)
                    $foundTransactions[] = [
                        'transaction' => $transaction,
                        'amount' => $transactionAmount,
                        'description' => $description,
                        'when' => $transaction['when'] ?? $transaction['When'] ?? null,
                        'id' => $transaction['id'] ?? 'unknown'
                    ];
                }
            }
            
            // Trả về giao dịch tìm thấy (ưu tiên transaction mới nhất)
            if (!empty($foundTransactions)) {
                Log::info('Casso - Found transactions with memo', [
                    'payment_memo' => $paymentMemo,
                    'total_found' => count($foundTransactions),
                    'transactions' => array_map(function($item) {
                        return [
                            'id' => $item['id'] ?? 'unknown',
                            'amount' => $item['amount'],
                            'when' => $item['when'] ?? 'N/A',
                            'description' => substr($item['description'], 0, 100)
                        ];
                    }, $foundTransactions)
                ]);
                
                // Sắp xếp theo thời gian (mới nhất trước) - ưu tiên transaction mới nhất
                usort($foundTransactions, function($a, $b) {
                    $whenA = $a['when'] ?? '';
                    $whenB = $b['when'] ?? '';
                    // So sánh thời gian (mới nhất trước)
                    return strcmp($whenB, $whenA);
                });
                
                // Nếu có chỉ định số tiền, lọc theo số tiền
                if ($amount !== null) {
                    $validTransactions = [];
                    foreach ($foundTransactions as $item) {
                        $amountCheck = $this->checkPaymentAmount($item['amount'], $amount);
                        if ($amountCheck['status'] !== 'under') {
                            $validTransactions[] = $item;
                        } else {
                            Log::info('Casso - Transaction filtered out due to insufficient amount', [
                                'transaction_id' => $item['id'],
                                'transaction_amount' => $item['amount'],
                                'required_amount' => $amount,
                                'status' => $amountCheck['status']
                            ]);
                        }
                    }
                    
                    if (!empty($validTransactions)) {
                        // Đã sắp xếp theo thời gian rồi, lấy transaction đầu tiên (mới nhất)
                        $selected = $validTransactions[0]['transaction'];
                        Log::info('Casso - ✅ Selected transaction (with amount check)', [
                            'transaction_id' => $selected['id'] ?? 'unknown',
                            'amount' => $selected['amount'] ?? 0,
                            'when' => $selected['when'] ?? 'N/A'
                        ]);
                        return $selected;
                    } else {
                        // Nếu không có transaction nào match amount, vẫn trả về transaction mới nhất
                        // (có thể số tiền đã thay đổi hoặc có nhiều lần thanh toán)
                        Log::warning('Casso - No transaction matches amount, returning newest transaction', [
                            'required_amount' => $amount,
                            'found_amounts' => array_column($foundTransactions, 'amount'),
                            'selected_transaction_id' => $foundTransactions[0]['id']
                        ]);
                        return $foundTransactions[0]['transaction'];
                    }
                } else {
                    // Không có amount check, trả về transaction mới nhất
                    $selected = $foundTransactions[0]['transaction'];
                    Log::info('Casso - ✅ Selected newest transaction (no amount check)', [
                        'transaction_id' => $selected['id'] ?? 'unknown',
                        'amount' => $selected['amount'] ?? 0,
                        'when' => $selected['when'] ?? 'N/A',
                        'total_found' => count($foundTransactions)
                    ]);
                    return $selected;
                }
            }

            // Log chi tiết để debug
            $sampleDescriptions = [];
            $memoFoundInDescriptions = [];
            foreach (array_slice($transactions, 0, 10) as $tx) {
                $desc = $tx['description'] ?? '';
                $sampleDescriptions[] = [
                    'description' => $desc,
                    'amount' => $tx['amount'] ?? 0,
                    'id' => $tx['id'] ?? 'unknown'
                ];
                // Kiểm tra xem memo có trong description không
                $memoFoundInDescriptions[] = stripos($desc, $paymentMemo) !== false ? 'YES' : 'NO';
            }
            
            Log::warning('Casso - Transaction not found by memo', [
                'payment_memo' => $paymentMemo,
                'memo_length' => strlen($paymentMemo),
                'searched_transactions' => count($transactions),
                'sample_descriptions' => $sampleDescriptions,
                'memo_found_in_descriptions' => $memoFoundInDescriptions,
                'memo_upper' => strtoupper($paymentMemo),
                'memo_lower' => strtolower($paymentMemo)
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Casso Find Transaction By Memo Error: ' . $e->getMessage());
            return null;
        }
    }
}

