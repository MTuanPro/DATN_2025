<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\HocPhiHocKy;
use App\Models\ChiTietHocPhiMon;
use App\Models\LichSuDongHocPhi;
use App\Services\HocPhiService;
use App\Services\ZaloPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class HocPhiController extends Controller
{
    /**
     * Display student's tuition fees
     */
    public function index()
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin sinh viÃªn!');
        }

        $hocPhis = HocPhiHocKy::with(['hocKy'])
            ->where('sinh_vien_id', $sinhVien->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate summary
        $tongHocPhi = $hocPhis->sum('tong_so_tien');
        $daDong = $hocPhis->sum('so_tien_da_dong');
        $conLai = $hocPhis->sum('so_tien_con_lai');

        return view('sinhvien.hoc-phi.index', compact('hocPhis', 'tongHocPhi', 'daDong', 'conLai'));
    }

    /**
     * Display details of a specific tuition fee
     */
    public function show($id)
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin sinh viÃªn!');
        }

        $hocPhi = HocPhiHocKy::with([
            'hocKy',
            'chiTietHocPhiMon.monHoc',
            'chiTietHocPhiMon.lopHocPhanSinhVien.lopHocPhan'
        ])
            ->where('sinh_vien_id', $sinhVien->id)
            ->findOrFail($id);

        return view('sinhvien.hoc-phi.show', compact('hocPhi'));
    }

    /**
     * Display payment history
     */
    public function lichSu($id)
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin sinh viÃªn!');
        }

        $hocPhi = HocPhiHocKy::with(['hocKy'])
            ->where('sinh_vien_id', $sinhVien->id)
            ->findOrFail($id);

        // Lấy lịch sử đóng học phí, loại bỏ các giao dịch đang chờ xác nhận
        $lichSuDong = LichSuDongHocPhi::where('hoc_phi_hoc_ky_id', $hocPhi->id)
            ->where(function($query) {
                $query->where('ghi_chu', 'not like', '%Đang chờ%')
                      ->orWhereNull('ghi_chu');
            })
            ->orderBy('ngay_dong', 'desc')
            ->paginate(10);

        return view('sinhvien.hoc-phi.lich-su', compact('hocPhi', 'lichSuDong'));
    }

    /**
     * Export tuition fee invoice to PDF
     */
    public function exportPdf($id)
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            abort(403, 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin sinh viÃªn!');
        }

        $hocPhi = HocPhiHocKy::with([
            'sinhVien.user',
            'sinhVien.chuyenNganh.nganh.khoa',
            'sinhVien.nganh.khoa',
            'sinhVien.khoaHoc',
            'hocKy',
            'chiTietHocPhiMon.monHoc',
            'lichSuDongHocPhi'
        ])
            ->where('sinh_vien_id', $sinhVien->id)
            ->findOrFail($id);

        $pdf = Pdf::loadView('sinhvien.hoc-phi.pdf', compact('hocPhi'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        $fileName = 'HocPhi_' . $sinhVien->ma_sinh_vien . '_' . $hocPhi->hocKy->ten_hoc_ky . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * View payment guide
     */
    public function huongDan()
    {
        return view('sinhvien.hoc-phi.huong-dan');
    }

    /**
     * Show form to pay tuition via ZaloPay
     */
    public function showZaloPayPayment(Request $request, $id)
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin sinh viÃªn!');
        }

        $hocPhi = HocPhiHocKy::with(['hocKy'])
            ->where('sinh_vien_id', $sinhVien->id)
            ->findOrFail($id);

        // Check if there's remaining amount to pay
        if ($hocPhi->so_tien_con_lai <= 0) {
            return redirect()
                ->route('sinh-vien.hoc-phi.show', $id)
                ->with('info', 'Bạn đã thanh toán đủ học phí cho học kỳ này.');
        }

        // Check if user wants to create a new order (from query parameter or if no orderUrl in session)
        $createNew = $request->has('new') || $request->has('refresh');
        
        // Get orderurl from session if exists (after creating order)
        // Clear session if user explicitly wants new order
        if ($createNew) {
            session()->forget(['zalopay_orderurl', 'zalopay_zptranstoken', 'zalopay_app_trans_id', 'zalopay_hoc_phi_id']);
            $orderUrl = null;
            $zpTransToken = null;
            Log::info('ZaloPay - User requested new order, cleared session');
        } else {
            $orderUrl = session('zalopay_orderurl');
            $zpTransToken = session('zalopay_zptranstoken');
            
            // Check if existing order is still valid (not expired)
            // ZaloPay orders typically expire after 15 minutes
            $appTransId = session('zalopay_app_trans_id');
            if ($orderUrl && $appTransId) {
                // Check if order was created more than 14 minutes ago (safety margin)
                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)
                    ->where('ghi_chu', 'like', '%Đang chờ%')
                    ->first();
                
                if (!$lichSu) {
                    // Order already processed or deleted, clear session
                    session()->forget(['zalopay_orderurl', 'zalopay_zptranstoken', 'zalopay_app_trans_id', 'zalopay_hoc_phi_id']);
                    $orderUrl = null;
                    $zpTransToken = null;
                    Log::info('ZaloPay - Order already processed, cleared session');
                } elseif ($lichSu->created_at->diffInMinutes(now()) > 14) {
                    // Order expired (more than 14 minutes old)
                    session()->forget(['zalopay_orderurl', 'zalopay_zptranstoken', 'zalopay_app_trans_id', 'zalopay_hoc_phi_id']);
                    $orderUrl = null;
                    $zpTransToken = null;
                    Log::info('ZaloPay - Order expired, cleared session');
                }
            }
        }
        
        // Debug: Log to check if orderUrl exists
        if ($orderUrl) {
            Log::info('ZaloPay OrderUrl in session:', ['orderurl' => $orderUrl]);
        } else {
            Log::info('ZaloPay OrderUrl not found in session - will show form to create new order');
        }

        return view('sinhvien.hoc-phi.zalopay-payment', compact('hocPhi', 'orderUrl', 'zpTransToken'));
    }

    /**
     * Initiate ZaloPay payment
     */
    public function initiateZaloPayPayment(Request $request, $id)
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin sinh viÃªn!');
        }

        $hocPhi = HocPhiHocKy::with(['hocKy'])
            ->where('sinh_vien_id', $sinhVien->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'so_tien_dong' => 'required|numeric|min:1000|max:' . $hocPhi->so_tien_con_lai,
        ], [
            'so_tien_dong.required' => 'Vui lÃ²ng nháº­p sá»‘ tiá»n thanh toÃ¡n',
            'so_tien_dong.numeric' => 'Sá»‘ tiá»n pháº£i lÃ  sá»‘',
            'so_tien_dong.min' => 'Sá»‘ tiá»n tá»‘i thiá»ƒu lÃ  1,000 Ä‘',
            'so_tien_dong.max' => 'Sá»‘ tiá»n khÃ´ng Ä‘Æ°á»£c vÆ°á»£t quÃ¡ sá»‘ tiá»n cÃ²n láº¡i',
        ]);

        $amount = (int) $validated['so_tien_dong'];

        // Create order info - thêm tên trường để hiển thị rõ ràng hơn trong QR code
        // Lưu ý: Tên tài khoản người nhận được cấu hình trong ZaloPay Merchant Portal
        // không phải trong code. Cần đăng nhập vào https://merchant.zalopay.vn/ để cấu hình.
        $tenTruong = config('app.name', 'Trường Đại Học');
        $orderInfo = "{$tenTruong} - Thanh toan hoc phi - {$hocPhi->hocKy->ten_hoc_ky} - {$sinhVien->ma_sinh_vien}";
        
        // Create order ID (app_trans_id format: yyMMdd_xxxxx)
        $appTransId = date('ymd') . '_' . $hocPhi->id . '_' . $sinhVien->id . '_' . time();

        // Create temporary payment record (pending)
        try {
            DB::beginTransaction();

            $lichSu = LichSuDongHocPhi::create([
                'hoc_phi_hoc_ky_id' => $hocPhi->id,
                'so_tien_dong' => $amount,
                'ngay_dong' => now(),
                'phuong_thuc_thanh_toan' => 'ZaloPay',
                'ma_giao_dich' => $appTransId,
                'ghi_chu' => 'Đang chờ xác nhận từ ZaloPay',
            ]);

            DB::commit();

            // Prepare items for ZaloPay
            $items = [
                [
                    'itemid' => 'hocphi_' . $hocPhi->id,
                    'itemname' => 'Học phí ' . $hocPhi->hocKy->ten_hoc_ky,
                    'itemprice' => $amount,
                    'itemquantity' => 1,
                ]
            ];

            // Embed data - thêm thông tin để hỗ trợ đầy đủ các phương thức thanh toán
            $embedData = [
                'merchantinfo' => json_encode([
                    'hoc_phi_id' => $hocPhi->id,
                    'sinh_vien_id' => $sinhVien->id,
                    'ma_sinh_vien' => $sinhVien->ma_sinh_vien,
                ], JSON_UNESCAPED_UNICODE),
                // Không thêm preferred_payment_method vì API v1 không hỗ trợ
                // Để trống bankcode sẽ cho phép user chọn tất cả phương thức thanh toán
            ];

            // Initiate ZaloPay payment (API v1)
            // Lưu ý: Để trống bankcode sẽ cho phép user chọn tất cả phương thức thanh toán
            // bao gồm ZaloPay wallet, ngân hàng, thẻ ATM/Visa/Mastercard
            // Nếu không thấy tùy chọn ZaloPay wallet, có thể do:
            // 1. Merchant account chưa được kích hoạt ZaloPay wallet trong ZaloPay merchant portal
            // 2. Môi trường sandbox có thể hạn chế một số phương thức thanh toán
            // 3. Cần kiểm tra cấu hình trong ZaloPay merchant portal
            $zaloPayService = new ZaloPayService();
            $result = $zaloPayService->createOrder(
                $appTransId,
                $amount,
                $orderInfo,
                $sinhVien->ma_sinh_vien, // appuser
                $items,
                $embedData,
                '' // bankcode - để trống để user chọn tất cả phương thức (ZaloPay wallet, ngân hàng, thẻ)
            );
            
            Log::info('ZaloPay Create Order - Payment method selection:', [
                'bankcode' => 'empty (all methods allowed)',
                'expected_methods' => 'ZaloPay wallet, Bank transfer, ATM/Visa/Mastercard',
                'app_trans_id' => $appTransId
            ]);

            if (isset($result['returncode']) && $result['returncode'] == 1) {
                // Clear old session first to ensure fresh order
                session()->forget(['zalopay_app_trans_id', 'zalopay_hoc_phi_id', 'zalopay_orderurl', 'zalopay_zptranstoken']);
                
                // Store transaction info in session for verification
                session(['zalopay_app_trans_id' => $appTransId]);
                session(['zalopay_hoc_phi_id' => $hocPhi->id]);

                // Store orderurl to display QR code
                if (isset($result['orderurl']) && !empty($result['orderurl'])) {
                    // ✅ Sử dụng URL gốc từ ZaloPay, không decode hay modify
                    // URL từ ZaloPay đã được encode đúng và cần giữ nguyên
                    $orderUrl = trim($result['orderurl']);
                    
                    // Validate URL format
                    if (empty($orderUrl) || strlen($orderUrl) < 10) {
                        Log::error('ZaloPay orderurl too short or empty:', [
                            'orderurl' => $orderUrl,
                            'original' => $result['orderurl'] ?? null
                        ]);
                        throw new \Exception('URL thanh toán từ ZaloPay không hợp lệ');
                    }
                    
                    // Validate URL starts with http/https
                    if (!preg_match('/^https?:\/\//i', $orderUrl)) {
                        Log::error('ZaloPay orderurl invalid format:', [
                            'orderurl' => $orderUrl,
                            'preview' => substr($orderUrl, 0, 50)
                        ]);
                        throw new \Exception('URL thanh toán từ ZaloPay không đúng định dạng');
                    }
                    
                    // Log URL để debug
                    Log::info('ZaloPay Order URL validated:', [
                        'url_length' => strlen($orderUrl),
                        'url_preview' => substr($orderUrl, 0, 80) . '...',
                        'url_starts_with' => substr($orderUrl, 0, 30),
                        'has_qcgateway' => strpos($orderUrl, 'qcgateway.zalopay.vn') !== false
                    ]);
                    
                    session(['zalopay_orderurl' => $orderUrl]);
                    session(['zalopay_zptranstoken' => $result['zptranstoken'] ?? null]);
                    
                    Log::info('ZaloPay Order created successfully:', [
                        'app_trans_id' => $appTransId,
                        'orderurl' => $orderUrl,
                        'orderurl_length' => strlen($orderUrl),
                        'orderurl_preview' => substr($orderUrl, 0, 50) . '...',
                        'zptranstoken' => isset($result['zptranstoken']) ? 'present' : 'missing',
                        'result_keys' => array_keys($result)
                    ]);
                    
                    // Kiểm tra nếu muốn redirect trực tiếp đến trang thanh toán ZaloPay
                    $redirectDirect = $request->get('redirect_direct', false);
                    
                    if ($redirectDirect) {
                        // Redirect trực tiếp đến trang thanh toán ZaloPay
                        return redirect($orderUrl);
                    }
                    
                    // Return to payment page with QR code (mặc định)
                    return redirect()
                        ->route('sinh-vien.hoc-phi.zalopay-payment', $id)
                        ->with('success', 'Đã tạo đơn hàng thành công. Vui lòng quét QR code để thanh toán.');
                } else {
                    Log::error('ZaloPay response missing orderurl:', ['result' => $result]);
                    throw new \Exception('Không nhận được URL thanh toán từ ZaloPay. Response: ' . json_encode($result));
                }
            } else {
                // Delete the pending record
                $lichSu->delete();

                // Get error message from response
                $errorMessage = $result['returnmessage'] ?? 'Không thể tạo yêu cầu thanh toán.';
                
                // Provide more specific error messages
                if (isset($result['returncode'])) {
                    switch ($result['returncode']) {
                        case -1:
                            $errorMessage = 'ZaloPay chưa được cấu hình. Vui lòng liên hệ quản trị viên.';
                            break;
                        case -2:
                            $errorMessage = 'Không thể kết nối đến ZaloPay. Vui lòng thử lại sau hoặc liên hệ quản trị viên.';
                            break;
                        default:
                            if (empty($errorMessage)) {
                                $errorMessage = 'Không thể tạo yêu cầu thanh toán. Mã lỗi: ' . $result['returncode'];
                            }
                    }
                }
                
                Log::error('ZaloPay Create Order Failed:', [
                    'returncode' => $result['returncode'] ?? 'unknown',
                    'returnmessage' => $errorMessage,
                    'result' => $result,
                    'app_trans_id' => $appTransId
                ]);
                
                return redirect()
                    ->route('sinh-vien.hoc-phi.zalopay-payment', $id)
                    ->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('sinh-vien.hoc-phi.zalopay-payment', $id)
                ->with('error', 'CÃ³ lá»—i xáº£y ra: ' . $e->getMessage());
        }
    }

    /**
     * Handle ZaloPay payment callback (return URL)
     */
    public function zaloPayCallback(Request $request)
    {
        $zaloPayService = new ZaloPayService();
        $appTransId = $request->get('apptransid');
        $returncode = $request->get('returncode');
        $returnmessage = $request->get('returnmessage');
        $zpTransId = $request->get('zptransid'); // ZaloPay transaction ID từ result page
        $hocPhiId = session('zalopay_hoc_phi_id');

        Log::info('ZaloPay Callback received:', [
            'apptransid' => $appTransId,
            'returncode' => $returncode,
            'returnmessage' => $returnmessage,
            'zptransid' => $zpTransId,
            'all_params' => $request->all(),
            'hoc_phi_id_session' => $hocPhiId
        ]);

        // Nếu có zptransid nhưng không có apptransid, cần query để tìm apptransid
        if (!$appTransId && $zpTransId) {
            // Tìm trong database bằng zptransid (nếu có lưu)
            // Hoặc query từ ZaloPay để lấy apptransid
            Log::info('ZaloPay Callback - Has zptransid but no apptransid, trying to find from session or database');
            
            // Thử tìm từ session
            $sessionAppTransId = session('zalopay_app_trans_id');
            if ($sessionAppTransId) {
                $appTransId = $sessionAppTransId;
                Log::info('ZaloPay Callback - Found apptransid from session:', ['apptransid' => $appTransId]);
            }
        }

        // Lưu hoc_phi_id từ session trước khi clear (nếu có)
        // Nếu không có trong session, sẽ tìm từ database sau
        
        // Xử lý trường hợp returncode=1 (thành công) từ URL parameter
        // Trường hợp này xảy ra khi ZaloPay redirect về result page với returncode=1
        if ($returncode == 1) {
            // Tìm apptransid từ session nếu chưa có
            if (!$appTransId) {
                $appTransId = session('zalopay_app_trans_id');
            }
            
            // Nếu vẫn không có apptransid, tìm từ database
            if (!$appTransId) {
                // Tìm từ hoc_phi_id trong session
                if ($hocPhiId) {
                    $lichSu = LichSuDongHocPhi::where('hoc_phi_hoc_ky_id', $hocPhiId)
                        ->where('phuong_thuc_thanh_toan', 'ZaloPay')
                        ->where('ghi_chu', 'like', '%Đang chờ%')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($lichSu) {
                        $appTransId = $lichSu->ma_giao_dich;
                        Log::info('ZaloPay Callback - Found apptransid from pending payment:', [
                            'apptransid' => $appTransId,
                            'hoc_phi_id' => $hocPhiId
                        ]);
                    }
                }
                
                // Nếu vẫn không có, tìm từ user hiện tại
                if (!$appTransId) {
                    try {
                        $user = auth()->user();
                        if ($user && $user->sinhVien) {
                            $lichSu = LichSuDongHocPhi::whereHas('hocPhiHocKy', function($query) use ($user) {
                                $query->where('sinh_vien_id', $user->sinhVien->id);
                            })
                            ->where('phuong_thuc_thanh_toan', 'ZaloPay')
                            ->where('ghi_chu', 'like', '%Đang chờ%')
                            ->orderBy('created_at', 'desc')
                            ->first();
                            
                            if ($lichSu) {
                                $appTransId = $lichSu->ma_giao_dich;
                                $hocPhiId = $lichSu->hoc_phi_hoc_ky_id;
                                Log::info('ZaloPay Callback - Found apptransid from user pending payment:', [
                                    'apptransid' => $appTransId,
                                    'hoc_phi_id' => $hocPhiId
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('ZaloPay Callback - Error finding apptransid from user:', [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            // Nếu tìm được apptransid, tiếp tục xử lý như bình thường
            if ($appTransId) {
                Log::info('ZaloPay Callback - Processing success with apptransid from session/database:', [
                    'apptransid' => $appTransId,
                    'returncode' => $returncode,
                    'hoc_phi_id' => $hocPhiId
                ]);
                // Tiếp tục xử lý ở dưới (không return ở đây)
            } else {
                // Nếu không tìm được, redirect về trang học phí với thông báo
                Log::warning('ZaloPay Callback - Success but cannot find apptransid', [
                    'hoc_phi_id' => $hocPhiId,
                    'returncode' => $returncode,
                    'zptransid' => $zpTransId
                ]);
                
                if ($hocPhiId) {
                    return redirect()
                        ->route('sinh-vien.hoc-phi.show', $hocPhiId)
                        ->with('warning', 'Thanh toán thành công nhưng không tìm thấy thông tin giao dịch. Vui lòng kiểm tra lại trạng thái.');
                }
                
                return redirect()
                    ->route('sinh-vien.hoc-phi.index')
                    ->with('warning', 'Thanh toán thành công. Vui lòng kiểm tra lại trạng thái học phí.');
            }
        }

        // Xử lý trường hợp hủy giao dịch hoặc lỗi từ URL parameter
        if ($returncode && $returncode != 1) {
            // Các mã lỗi thường gặp:
            // -6012: Giao dịch đã bị hủy bởi người dùng
            // -6013: Giao dịch đã hết hạn
            // -6014: Giao dịch thất bại
            // Các mã lỗi khác < 0: Lỗi từ ZaloPay
            
            Log::warning('ZaloPay Callback - Transaction failed or cancelled:', [
                'apptransid' => $appTransId,
                'returncode' => $returncode,
                'returnmessage' => $returnmessage
            ]);

            // Tìm và xóa record pending nếu có
            if ($appTransId) {
                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)
                    ->where('ghi_chu', 'like', '%Đang chờ%')
                    ->first();
                
                if ($lichSu) {
                    // Lưu hoc_phi_id trước khi xóa
                    if (!$hocPhiId) {
                        $hocPhiId = $lichSu->hoc_phi_hoc_ky_id;
                    }
                    $lichSu->delete();
                    Log::info('ZaloPay Callback - Deleted pending payment record:', [
                        'apptransid' => $appTransId,
                        'hoc_phi_id' => $hocPhiId
                    ]);
                } elseif (!$hocPhiId) {
                    // Nếu không tìm thấy pending, thử tìm bất kỳ record nào với apptransid
                    $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
                    if ($lichSu) {
                        $hocPhiId = $lichSu->hoc_phi_hoc_ky_id;
                    }
                }
            }

            // Tạo thông báo lỗi phù hợp
            $errorMessage = 'Giao dịch đã bị hủy hoặc thất bại.';
            if ($returncode == -6012) {
                $errorMessage = 'Giao dịch đã bị hủy bởi người dùng.';
            } elseif ($returncode == -6013) {
                $errorMessage = 'Giao dịch đã hết hạn. Vui lòng tạo đơn hàng mới.';
            } elseif ($returnmessage) {
                $errorMessage = urldecode($returnmessage);
            }

            // Redirect về trang học phí - đảm bảo luôn tìm được hoc_phi_id
            $redirectHocPhiId = $hocPhiId;
            
            // Nếu không có trong session, tìm từ database
            if (!$redirectHocPhiId && $appTransId) {
                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
                if ($lichSu) {
                    $redirectHocPhiId = $lichSu->hoc_phi_hoc_ky_id;
                }
            }
            
            if ($redirectHocPhiId) {
                // Clear session sau khi xử lý
                session()->forget(['zalopay_app_trans_id', 'zalopay_hoc_phi_id', 'zalopay_orderurl', 'zalopay_zptranstoken']);
                
                return redirect()
                    ->route('sinh-vien.hoc-phi.show', $redirectHocPhiId)
                    ->with('error', $errorMessage);
            }

            // Nếu vẫn không tìm thấy, redirect về index
            session()->forget(['zalopay_app_trans_id', 'zalopay_hoc_phi_id', 'zalopay_orderurl', 'zalopay_zptranstoken']);
            
            return redirect()
                ->route('sinh-vien.hoc-phi.index')
                ->with('error', $errorMessage);
        }

        // Nếu không có apptransid, thử tìm từ session hoặc từ database
        if (!$appTransId) {
            // Thử lấy từ session
            $appTransId = session('zalopay_app_trans_id');
            
            if (!$appTransId && $hocPhiId) {
                // Tìm giao dịch pending gần nhất
                $lichSu = LichSuDongHocPhi::where('hoc_phi_hoc_ky_id', $hocPhiId)
                    ->where('phuong_thuc_thanh_toan', 'ZaloPay')
                    ->where('ghi_chu', 'like', '%Đang chờ%')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($lichSu) {
                    $appTransId = $lichSu->ma_giao_dich;
                    Log::info('ZaloPay Callback - Found apptransid from pending payment:', [
                        'apptransid' => $appTransId,
                        'hoc_phi_id' => $hocPhiId
                    ]);
                }
            }
            
            if (!$appTransId) {
                Log::warning('ZaloPay Callback - Missing apptransid after all attempts', [
                    'hoc_phi_id' => $hocPhiId,
                    'returncode' => $returncode,
                    'zptransid' => $zpTransId
                ]);
                
                // Nếu có returncode=1 (thành công) nhưng không tìm được apptransid
                if ($returncode == 1 && $hocPhiId) {
                    return redirect()
                        ->route('sinh-vien.hoc-phi.show', $hocPhiId)
                        ->with('warning', 'Thanh toán thành công nhưng không tìm thấy thông tin giao dịch. Vui lòng kiểm tra lại trạng thái.');
                }
                
                return redirect()
                    ->route('sinh-vien.hoc-phi.index')
                    ->with('error', 'Giao dịch không hợp lệ. Vui lòng kiểm tra lại.');
            }
        }

        // Nếu có returncode=1 từ URL parameter, xử lý ngay mà không cần query
        // (vì đã biết thanh toán thành công từ URL)
        if ($returncode == 1) {
            Log::info('ZaloPay Callback - Processing success from URL parameter (returncode=1):', [
                'apptransid' => $appTransId,
                'hoc_phi_id' => $hocPhiId
            ]);
            
            // Query để lấy thông tin chi tiết (nhưng đã biết là thành công)
            $statusResult = $zaloPayService->queryOrder($appTransId);
            
            Log::info('ZaloPay Callback - Query result:', [
                'apptransid' => $appTransId,
                'status_result' => $statusResult,
                'returncode' => $statusResult['returncode'] ?? 'not_set',
                'returnmessage' => $statusResult['returnmessage'] ?? 'not_set'
            ]);
            
            // Xử lý thanh toán thành công
            if (isset($statusResult['returncode']) && $statusResult['returncode'] == 1) {
                // Gọi logic xử lý thành công (sẽ được xử lý ở dưới)
                // Không return ở đây, để tiếp tục xử lý ở dưới
            } else {
                // Nếu query không thành công, vẫn xử lý như thành công vì URL đã có returncode=1
                Log::warning('ZaloPay Callback - URL has returncode=1 but query failed, still processing as success', [
                    'apptransid' => $appTransId,
                    'query_result' => $statusResult
                ]);
                // Tạo statusResult giả để xử lý
                $statusResult = ['returncode' => 1, 'returnmessage' => 'success'];
            }
        } else {
            // Query payment status from ZaloPay (trường hợp không có returncode trong URL)
            $statusResult = $zaloPayService->queryOrder($appTransId);
            
            Log::info('ZaloPay Callback - Query result:', [
                'apptransid' => $appTransId,
                'status_result' => $statusResult,
                'returncode' => $statusResult['returncode'] ?? 'not_set',
                'returnmessage' => $statusResult['returnmessage'] ?? 'not_set'
            ]);
        }

        // Xử lý trường hợp thanh toán thành công (returncode = 1)
        if (isset($statusResult['returncode']) && $statusResult['returncode'] == 1) {
            // Payment successful
            try {
                DB::beginTransaction();

                // Find the payment record
                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();

                if ($lichSu) {
                    // Kiểm tra xem đã được xử lý chưa (tránh xử lý trùng lặp)
                    $isPending = str_contains($lichSu->ghi_chu ?? '', 'Đang chờ');
                    
                    if ($isPending) {
                        $hocPhi = $lichSu->hocPhiHocKy;

                        // Update payment record
                        $lichSu->update([
                            'ngay_dong' => now(),
                            'ghi_chu' => 'Thanh toán thành công qua ZaloPay. Mã giao dịch: ' . $appTransId,
                        ]);

                        // Update HocPhiHocKy - reload để tránh race condition
                        $hocPhi->refresh();
                        $hocPhi->so_tien_da_dong += $lichSu->so_tien_dong;
                        $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
                        $hocPhi->ngay_dong_lan_cuoi = now();
                        $hocPhi->save();

                        // Update status
                        $hocPhi->updateTrangThai();

                        // Update chi tiết học phí môn thành đã thanh toán (nếu thanh toán đủ)
                        if ($hocPhi->so_tien_con_lai == 0) {
                        ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                            ->where('trang_thai', 'chua_thanh_toan')
                            ->update(['trang_thai' => 'da_thanh_toan']);

                        // âœ… KHI ÄÃ“NG Äá»¦ Há»ŒC PHÃ: Tá»± Ä‘á»™ng thÃªm vÃ o danh sÃ¡ch chá» xáº¿p lá»›p
                        $hocPhiService = new HocPhiService();
                        $hocPhiService->themVaoDanhSachChoXepLop($hocPhi->sinh_vien_id, $hocPhi->hoc_ky_id);
                    }

                    DB::commit();

                    // Clear ZaloPay session after successful payment
                    session()->forget(['zalopay_app_trans_id', 'zalopay_hoc_phi_id', 'zalopay_orderurl', 'zalopay_zptranstoken']);

                    $redirectHocPhiId = $hocPhi->id;
                    
                    Log::info('ZaloPay Callback - Payment processed successfully, redirecting to:', [
                        'hoc_phi_id' => $redirectHocPhiId,
                        'apptransid' => $appTransId
                    ]);
                    
                    return redirect()
                        ->route('sinh-vien.hoc-phi.show', $redirectHocPhiId)
                        ->with('success', 'Thanh toán thành công! Mã giao dịch: ' . $appTransId);
                    } else {
                        // Đã được xử lý trước đó
                        DB::rollBack();
                        Log::info('ZaloPay Callback - Payment already processed:', [
                            'apptransid' => $appTransId,
                            'ghi_chu' => $lichSu->ghi_chu
                        ]);
                        
                        return redirect()
                            ->route('sinh-vien.hoc-phi.show', $lichSu->hoc_phi_hoc_ky_id)
                            ->with('info', 'Giao dịch đã được xử lý trước đó.');
                    }
                } else {
                    // Payment record not found
                    DB::rollBack();
                    
                    Log::warning('ZaloPay Callback - Record not found:', [
                        'apptransid' => $appTransId,
                        'hoc_phi_id_from_session' => $hocPhiId
                    ]);
                    
                    // Try to find hoc_phi_id from database
                    if (!$hocPhiId && $appTransId) {
                        $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
                        if ($lichSu) {
                            $hocPhiId = $lichSu->hoc_phi_hoc_ky_id;
                            return redirect()
                                ->route('sinh-vien.hoc-phi.show', $hocPhiId)
                                ->with('info', 'Giao dịch đã được xử lý trước đó. Mã giao dịch: ' . $appTransId);
                        }
                    }
                    
                    // Nếu vẫn không tìm thấy, redirect về index hoặc trang chi tiết nếu có hoc_phi_id
                    if ($hocPhiId) {
                        return redirect()
                            ->route('sinh-vien.hoc-phi.show', $hocPhiId)
                            ->with('warning', 'Không tìm thấy giao dịch trong hệ thống. Mã giao dịch: ' . $appTransId);
                    }
                    
                    return redirect()
                        ->route('sinh-vien.hoc-phi.index')
                        ->with('error', 'Không tìm thấy giao dịch. Mã giao dịch: ' . $appTransId);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('ZaloPay Callback Error: ' . $e->getMessage(), [
                    'apptransid' => $appTransId,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Try to find and redirect to correct page
                $redirectHocPhiId = $hocPhiId;
                
                if (!$redirectHocPhiId && $appTransId) {
                    $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
                    if ($lichSu) {
                        $redirectHocPhiId = $lichSu->hoc_phi_hoc_ky_id;
                    }
                }
                
                if ($redirectHocPhiId) {
                    return redirect()
                        ->route('sinh-vien.hoc-phi.show', $redirectHocPhiId)
                        ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán. Vui lòng liên hệ quản trị viên. Mã giao dịch: ' . $appTransId);
                }
                
                return redirect()
                    ->route('sinh-vien.hoc-phi.index')
                    ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
            }
        } elseif (isset($statusResult['returncode']) && $statusResult['returncode'] == 2) {
            // Payment failed or cancelled
            // Delete pending payment record
            $deleted = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)
                ->where('ghi_chu', 'like', '%Đang chờ%')
                ->delete();

            Log::info('ZaloPay Callback - Payment failed/cancelled:', [
                'apptransid' => $appTransId,
                'records_deleted' => $deleted
            ]);

            // Tìm hoc_phi_id từ record nếu còn tồn tại
            $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
            $redirectHocPhiId = $hocPhiId ?? ($lichSu ? $lichSu->hoc_phi_hoc_ky_id : null);

            if ($redirectHocPhiId) {
                return redirect()
                    ->route('sinh-vien.hoc-phi.show', $redirectHocPhiId)
                    ->with('error', 'Giao dịch thất bại hoặc đã bị hủy.');
            }

            return redirect()
                ->route('sinh-vien.hoc-phi.index')
                ->with('error', 'Giao dịch thất bại hoặc đã bị hủy.');
        } else {
            // Payment processing or unknown status
            $statusCode = $statusResult['returncode'] ?? 'unknown';
            $statusMessage = $statusResult['returnmessage'] ?? 'Giao dịch đang được xử lý.';
            
            Log::warning('ZaloPay Callback - Payment processing or unknown status:', [
                'apptransid' => $appTransId,
                'returncode' => $statusCode,
                'returnmessage' => $statusMessage,
                'full_result' => $statusResult
            ]);

            // Tìm hoc_phi_id từ record
            $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
            $redirectHocPhiId = $hocPhiId ?? ($lichSu ? $lichSu->hoc_phi_hoc_ky_id : null);

            // Xử lý các trường hợp đặc biệt
            if ($statusCode == 3) {
                // Giao dịch đang chờ xử lý
                $statusMessage = 'Giao dịch đang được xử lý. Vui lòng đợi vài phút rồi kiểm tra lại.';
            } elseif ($statusCode == -1) {
                // Lỗi query
                $statusMessage = 'Không thể kiểm tra trạng thái giao dịch. Vui lòng thử lại sau.';
            } elseif (is_numeric($statusCode) && $statusCode < 0) {
                // Các lỗi khác
                $statusMessage = 'Giao dịch thất bại. ' . ($statusMessage ?: 'Mã lỗi: ' . $statusCode);
                
                // Xóa record pending nếu có
                if ($lichSu && str_contains($lichSu->ghi_chu ?? '', 'Đang chờ')) {
                    $lichSu->delete();
                }
            }

            if ($redirectHocPhiId) {
                return redirect()
                    ->route('sinh-vien.hoc-phi.show', $redirectHocPhiId)
                    ->with('info', $statusMessage);
            }

            return redirect()
                ->route('sinh-vien.hoc-phi.index')
                ->with('info', $statusMessage);
        }
    }

    /**
     * Handle ZaloPay IPN (Callback from ZaloPay server)
     * 
     * Theo tài liệu ZaloPay API v1:
     * - ZaloPay Server POST callback với: data (JSON string), mac (HMAC)
     * - Merchant dùng key2 để xác thực MAC: HMAC(sha256, key2, data)
     * - Response format:
     *   + returncode = 1, returnmessage = "success" nếu hợp lệ và xử lý thành công
     *   + returncode = -1, returnmessage = "invalid callback" nếu MAC không hợp lệ
     *   + returncode = 0, returnmessage = "exception" nếu có lỗi (ZaloPay sẽ callback lại tối đa 3 lần)
     */
    public function zaloPayIpn(Request $request)
    {
        $zaloPayService = new ZaloPayService();
        
        // Log incoming callback để debug
        Log::info('ZaloPay IPN Callback received:', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'all_params' => $request->all(),
            'raw_body' => substr($request->getContent(), 0, 500)
        ]);
        
        // Get POST data from ZaloPay
        // ZaloPay gửi: data (JSON string), mac (HMAC)
        $postData = $request->all();
        
        // Validate required fields
        if (!isset($postData['data']) || !isset($postData['mac'])) {
            Log::warning('ZaloPay IPN - Missing required fields:', [
                'has_data' => isset($postData['data']),
                'has_mac' => isset($postData['mac']),
                'received_params' => array_keys($postData)
            ]);
            
            return response()->json([
                'returncode' => -1,
                'returnmessage' => 'invalid callback'
            ], 200); // Luôn trả về 200 để ZaloPay không retry
        }
        
        // Verify MAC using key2 (theo tài liệu ZaloPay)
        if (!$zaloPayService->verifyCallback($postData)) {
            Log::warning('ZaloPay IPN - MAC verification failed:', [
                'received_mac' => $postData['mac'] ?? 'N/A',
                'data_preview' => substr($postData['data'] ?? '', 0, 100) . '...'
            ]);
            
            return response()->json([
                'returncode' => -1,
                'returnmessage' => 'invalid callback'
            ], 200);
        }

        // Parse callback data (JSON string)
        $dataJson = $zaloPayService->parseCallbackData($postData['data']);
        
        if (!$dataJson || !is_array($dataJson)) {
            Log::error('ZaloPay IPN - Invalid callback data format:', [
                'data_string' => substr($postData['data'] ?? '', 0, 200)
            ]);
            
            return response()->json([
                'returncode' => 0,
                'returnmessage' => 'exception'
            ], 200);
        }

        $appTransId = $dataJson['apptransid'] ?? '';
        $zpTransId = $dataJson['zptransid'] ?? '';
        $amount = $dataJson['amount'] ?? 0;
        
        Log::info('ZaloPay IPN - Processing payment:', [
            'apptransid' => $appTransId,
            'zptransid' => $zpTransId,
            'amount' => $amount,
            'callback_data' => $dataJson
        ]);

        if (empty($appTransId)) {
            Log::error('ZaloPay IPN - Missing apptransid in callback data');
            return response()->json([
                'returncode' => 0,
                'returnmessage' => 'exception'
            ], 200);
        }

        // Payment successful - update database
        try {
            DB::beginTransaction();

            $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();

            if (!$lichSu) {
                Log::warning('ZaloPay IPN - Order not found:', [
                    'apptransid' => $appTransId
                ]);
                
                DB::rollBack();
                return response()->json([
                    'returncode' => 0,
                    'returnmessage' => 'exception'
                ], 200);
            }

            // Chỉ xử lý nếu chưa được xử lý (trạng thái "Đang chờ")
            if (str_contains($lichSu->ghi_chu ?? '', 'Đang chờ')) {
                $hocPhi = $lichSu->hocPhiHocKy;

                // Update payment record
                $lichSu->update([
                    'ngay_dong' => now(),
                    'ghi_chu' => 'Thanh toán thành công qua ZaloPay IPN. Mã giao dịch: ' . $appTransId . (empty($zpTransId) ? '' : ' | ZP: ' . $zpTransId),
                ]);

                // Update HocPhiHocKy
                $hocPhi->so_tien_da_dong += $lichSu->so_tien_dong;
                $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
                $hocPhi->ngay_dong_lan_cuoi = now();
                $hocPhi->save();

                // Update status
                $hocPhi->updateTrangThai();

                // Update chi tiáº¿t há»c phÃ­ mÃ´n thÃ nh Ä‘Ã£ thanh toÃ¡n (náº¿u thanh toÃ¡n Ä‘á»§)
                if ($hocPhi->so_tien_con_lai == 0) {
                    ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                        ->where('trang_thai', 'chua_thanh_toan')
                        ->update(['trang_thai' => 'da_thanh_toan']);

                    // âœ… KHI ÄÃ“NG Äá»¦ Há»ŒC PHÃ: Tá»± Ä‘á»™ng thÃªm vÃ o danh sÃ¡ch chá» xáº¿p lá»›p
                    $hocPhiService = new HocPhiService();
                    $hocPhiService->themVaoDanhSachChoXepLop($hocPhi->sinh_vien_id, $hocPhi->hoc_ky_id);
                }

                DB::commit();

                Log::info('ZaloPay IPN - Payment processed successfully:', [
                    'apptransid' => $appTransId,
                    'zptransid' => $zpTransId,
                    'amount' => $amount
                ]);

                // Trả về success theo tài liệu ZaloPay
                return response()->json([
                    'returncode' => 1,
                    'returnmessage' => 'success'
                ], 200);
            } else {
                // Đã được xử lý trước đó
                Log::info('ZaloPay IPN - Order already processed:', [
                    'apptransid' => $appTransId,
                    'ghi_chu' => $lichSu->ghi_chu
                ]);
                
                DB::rollBack();
                
                // Vẫn trả về success vì đã xử lý rồi
                return response()->json([
                    'returncode' => 1,
                    'returnmessage' => 'success'
                ], 200);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ZaloPay IPN Error: ' . $e->getMessage(), [
                'apptransid' => $appTransId ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            
            // Trả về exception để ZaloPay callback lại (tối đa 3 lần)
            return response()->json([
                'returncode' => 0,
                'returnmessage' => 'exception'
            ], 200);
        }
    }

    /**
     * Check ZaloPay payment status manually
     */
    public function checkZaloPayStatus(Request $request, $id)
    {
        try {
            $appTransId = $request->input('app_trans_id');
            
            if (!$appTransId) {
                return redirect()
                    ->route('sinh-vien.hoc-phi.show', $id)
                    ->with('error', 'Vui lòng nhập mã giao dịch.');
            }

            $zaloPayService = new ZaloPayService();
            $statusResult = $zaloPayService->queryOrder($appTransId);

            Log::info('ZaloPay Manual Check Status:', [
                'apptransid' => $appTransId,
                'status_result' => $statusResult
            ]);

            // Check if payment is successful
            if (isset($statusResult['returncode']) && $statusResult['returncode'] == 1) {
                // Payment successful - update database
                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
                
                if ($lichSu) {
                    // Kiểm tra xem đã được xử lý chưa (tránh xử lý trùng lặp)
                    $isPending = str_contains($lichSu->ghi_chu ?? '', 'Đang chờ');
                    
                    if ($isPending) {
                        DB::beginTransaction();
                        
                        $hocPhi = $lichSu->hocPhiHocKy;
                        
                        // Update payment record
                        $lichSu->update([
                            'ngay_dong' => now(),
                            'ghi_chu' => 'Thanh toán thành công qua ZaloPay (đã kiểm tra lại). Mã giao dịch: ' . $appTransId,
                        ]);

                        // Update HocPhiHocKy - reload để tránh race condition
                        $hocPhi->refresh();
                        $hocPhi->so_tien_da_dong += $lichSu->so_tien_dong;
                        $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
                        $hocPhi->ngay_dong_lan_cuoi = now();
                        $hocPhi->save();

                        // Update status
                        $hocPhi->updateTrangThai();

                        // Update chi tiết học phí môn thành đã thanh toán (nếu thanh toán đủ)
                        if ($hocPhi->so_tien_con_lai == 0) {
                            ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                                ->where('trang_thai', 'chua_thanh_toan')
                                ->update(['trang_thai' => 'da_thanh_toan']);

                            $hocPhiService = new HocPhiService();
                            $hocPhiService->themVaoDanhSachChoXepLop($hocPhi->sinh_vien_id, $hocPhi->hoc_ky_id);
                        }

                        DB::commit();

                        return redirect()
                            ->route('sinh-vien.hoc-phi.show', $id)
                            ->with('success', 'Đã cập nhật trạng thái thanh toán thành công! Mã giao dịch: ' . $appTransId);
                    } else {
                        return redirect()
                            ->route('sinh-vien.hoc-phi.show', $id)
                            ->with('info', 'Giao dịch đã được xử lý trước đó hoặc không tồn tại.');
                    }
                } else {
                    return redirect()
                        ->route('sinh-vien.hoc-phi.show', $id)
                        ->with('error', 'Không tìm thấy giao dịch với mã: ' . $appTransId);
                }
            } else {
                $errorMessage = $statusResult['returnmessage'] ?? 'Thanh toán chưa hoàn tất hoặc đã thất bại.';
                return redirect()
                    ->route('sinh-vien.hoc-phi.show', $id)
                    ->with('warning', 'Trạng thái thanh toán: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('ZaloPay Check Status Error: ' . $e->getMessage());
            return redirect()
                ->route('sinh-vien.hoc-phi.show', $id)
                ->with('error', 'Có lỗi xảy ra khi kiểm tra trạng thái: ' . $e->getMessage());
        }
    }
}
