<?php

namespace App\Services;

use App\Models\HocPhiHocKy;
use App\Models\LichSuDongHocPhi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $vnpayService;
    protected $momoService;

    public function __construct(VNPayService $vnpayService, MoMoService $momoService)
    {
        $this->vnpayService = $vnpayService;
        $this->momoService = $momoService;
    }

    /**
     * Tạo URL thanh toán theo gateway
     * 
     * @param string $gateway 'vnpay' hoặc 'momo'
     * @param HocPhiHocKy $hocPhi
     * @param int $soTienDong Số tiền sinh viên muốn đóng
     * @param string $ipAddr IP address
     * @return array
     */
    public function createPayment($gateway, HocPhiHocKy $hocPhi, $soTienDong, $ipAddr = '127.0.0.1')
    {
        // Validate số tiền
        if ($soTienDong <= 0 || $soTienDong > $hocPhi->so_tien_con_lai) {
            return [
                'success' => false,
                'message' => 'Số tiền không hợp lệ. Vui lòng nhập số tiền từ 1 đến ' . number_format($hocPhi->so_tien_con_lai, 0, ',', '.') . ' VNĐ'
            ];
        }

        $orderId = 'HP' . $hocPhi->id . '_' . time();
        $orderInfo = sprintf(
            'Thanh toan hoc phi HK %s - SV %s',
            $hocPhi->hocKy->ten_hoc_ky ?? 'N/A',
            $hocPhi->sinhVien->ma_sinh_vien ?? 'N/A'
        );

        try {
            if ($gateway === 'vnpay') {
                $paymentUrl = $this->vnpayService->createPaymentUrl(
                    $orderId,
                    $soTienDong,
                    $orderInfo,
                    $ipAddr
                );

                return [
                    'success' => true,
                    'payment_url' => $paymentUrl,
                    'gateway' => 'vnpay',
                    'order_id' => $orderId
                ];
            } elseif ($gateway === 'momo') {
                $result = $this->momoService->createPaymentUrl(
                    $orderId,
                    $soTienDong,
                    $orderInfo
                );

                if ($result['success']) {
                    return [
                        'success' => true,
                        'payment_url' => $result['payUrl'],
                        'qr_code_url' => $result['qrCodeUrl'] ?? null,
                        'deeplink' => $result['deeplink'] ?? null,
                        'gateway' => 'momo',
                        'order_id' => $orderId
                    ];
                } else {
                    return $result;
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Gateway không hợp lệ'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Payment Creation Error', [
                'gateway' => $gateway,
                'hoc_phi_id' => $hocPhi->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo thanh toán: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xử lý callback từ payment gateway
     * 
     * @param string $gateway
     * @param array $data
     * @return array
     */
    public function handleCallback($gateway, $data)
    {
        try {
            if ($gateway === 'vnpay') {
                $result = $this->vnpayService->verifyCallback($data);
            } elseif ($gateway === 'momo') {
                $result = $this->momoService->verifyCallback($data);
            } else {
                return [
                    'success' => false,
                    'message' => 'Gateway không hợp lệ'
                ];
            }

            if ($result['valid']) {
                // Lấy hoc_phi_id từ order_id hoặc txn_ref
                $orderId = $gateway === 'vnpay' 
                    ? $result['data']['txn_ref'] 
                    : $result['data']['order_id'];
                
                // Parse order_id: HP{hoc_phi_id}_{timestamp}
                preg_match('/HP(\d+)_/', $orderId, $matches);
                $hocPhiId = $matches[1] ?? null;

                if (!$hocPhiId) {
                    return [
                        'success' => false,
                        'message' => 'Không tìm thấy thông tin học phí'
                    ];
                }

                // Lưu lịch sử đóng học phí
                $saved = $this->savePaymentHistory($hocPhiId, $result['data'], $gateway);

                return [
                    'success' => $saved,
                    'message' => $saved ? 'Thanh toán thành công' : 'Lỗi lưu dữ liệu thanh toán',
                    'hoc_phi_id' => $hocPhiId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['message']
                ];
            }
        } catch (\Exception $e) {
            Log::error('Payment Callback Error', [
                'gateway' => $gateway,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý thanh toán'
            ];
        }
    }

    /**
     * Lưu lịch sử đóng học phí
     */
    protected function savePaymentHistory($hocPhiId, $paymentData, $gateway)
    {
        DB::beginTransaction();
        try {
            $hocPhi = HocPhiHocKy::findOrFail($hocPhiId);

            $soTienDong = $paymentData['amount'];
            $maGiaoDich = $gateway === 'vnpay' 
                ? ($paymentData['transaction_no'] ?? $paymentData['txn_ref'])
                : $paymentData['transaction_id'];

            // Tạo lịch sử đóng
            LichSuDongHocPhi::create([
                'hoc_phi_hoc_ky_id' => $hocPhiId,
                'so_tien_dong' => $soTienDong,
                'ngay_dong' => now(),
                'phuong_thuc_thanh_toan' => $gateway === 'vnpay' ? 'VNPay' : 'MoMo',
                'ma_giao_dich' => $maGiaoDich,
                'ngan_hang' => $gateway === 'vnpay' ? ($paymentData['bank_code'] ?? 'VNPay') : 'MoMo',
                'ghi_chu' => 'Thanh toán online qua ' . strtoupper($gateway),
            ]);

            // Cập nhật học phí
            $soTienDaDongMoi = $hocPhi->so_tien_da_dong + $soTienDong;
            $soTienConLaiMoi = $hocPhi->tong_so_tien - $soTienDaDongMoi;

            $trangThaiMoi = 'chua_nop_du';
            if ($soTienConLaiMoi <= 0) {
                $trangThaiMoi = 'da_nop_du';
            } elseif (now() > $hocPhi->han_dong) {
                $trangThaiMoi = 'qua_han';
            }

            $hocPhi->update([
                'so_tien_da_dong' => $soTienDaDongMoi,
                'so_tien_con_lai' => $soTienConLaiMoi,
                'trang_thai' => $trangThaiMoi,
            ]);

            DB::commit();

            Log::info('Payment Saved Successfully', [
                'hoc_phi_id' => $hocPhiId,
                'amount' => $soTienDong,
                'gateway' => $gateway,
                'transaction_id' => $maGiaoDich
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Save Payment History Error', [
                'hoc_phi_id' => $hocPhiId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán (cho debug)
     */
    public function checkPaymentStatus($gateway, $orderId, $requestId = null)
    {
        if ($gateway === 'momo' && $requestId) {
            return $this->momoService->queryTransaction($orderId, $requestId);
        }

        return [
            'success' => false,
            'message' => 'Chức năng kiểm tra chỉ hỗ trợ cho MoMo'
        ];
    }
}
