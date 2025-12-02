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

        $lichSuDong = LichSuDongHocPhi::where('hoc_phi_hoc_ky_id', $hocPhi->id)
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
    public function showZaloPayPayment($id)
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

        // Get orderurl from session if exists (after creating order)
        // Don't clear session here - keep it so QR code can be displayed
        $orderUrl = session('zalopay_orderurl');
        $zpTransToken = session('zalopay_zptranstoken');
        
        // Debug: Log to check if orderUrl exists
        if ($orderUrl) {
            Log::info('ZaloPay OrderUrl in session:', ['orderurl' => $orderUrl]);
        } else {
            Log::info('ZaloPay OrderUrl not found in session');
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

        // Create order info
        $orderInfo = "Thanh toan hoc phi - {$hocPhi->hocKy->ten_hoc_ky} - {$sinhVien->ma_sinh_vien}";
        
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

            // Embed data
            $embedData = [
                'merchantinfo' => json_encode([
                    'hoc_phi_id' => $hocPhi->id,
                    'sinh_vien_id' => $sinhVien->id,
                    'ma_sinh_vien' => $sinhVien->ma_sinh_vien,
                ], JSON_UNESCAPED_UNICODE)
            ];

            // Initiate ZaloPay payment (API v1)
            $zaloPayService = new ZaloPayService();
            $result = $zaloPayService->createOrder(
                $appTransId,
                $amount,
                $orderInfo,
                $sinhVien->ma_sinh_vien, // appuser
                $items,
                $embedData,
                '' // bankcode - để trống để user chọn
            );

            if (isset($result['returncode']) && $result['returncode'] == 1) {
                // Store transaction info in session for verification
                session(['zalopay_app_trans_id' => $appTransId]);
                session(['zalopay_hoc_phi_id' => $hocPhi->id]);

                // Store orderurl to display QR code
                if (isset($result['orderurl'])) {
                    $orderUrl = $result['orderurl'];
                    session(['zalopay_orderurl' => $orderUrl]);
                    session(['zalopay_zptranstoken' => $result['zptranstoken'] ?? null]);
                    
                    Log::info('ZaloPay Order created successfully:', [
                        'app_trans_id' => $appTransId,
                        'orderurl' => $orderUrl,
                        'result' => $result
                    ]);
                    
                    // Return to payment page with QR code
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

                $errorMessage = $result['returnmessage'] ?? 'Không thể tạo yêu cầu thanh toán. Vui lòng thử lại.';
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
        $hocPhiId = session('zalopay_hoc_phi_id');

        // Clear session
        session()->forget(['zalopay_app_trans_id', 'zalopay_hoc_phi_id']);

        if (!$appTransId) {
            return redirect()
                ->route('sinh-vien.hoc-phi.index')
                ->with('error', 'Giao dá»‹ch khÃ´ng há»£p lá»‡.');
        }

        // Query payment status from ZaloPay
        $statusResult = $zaloPayService->queryOrder($appTransId);

        if (isset($statusResult['returncode']) && $statusResult['returncode'] == 1) {
            // Payment successful
            try {
                DB::beginTransaction();

                // Find the payment record
                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();

                if ($lichSu && str_contains($lichSu->ghi_chu ?? '', 'Đang chờ')) {
                    $hocPhi = $lichSu->hocPhiHocKy;

                    // Update payment record
                    $lichSu->update([
                        'ngay_dong' => now(),
                        'ghi_chu' => 'Thanh toán thành công qua ZaloPay. Mã giao dịch: ' . $appTransId,
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

                    // Clear ZaloPay session after successful payment
                    session()->forget(['zalopay_app_trans_id', 'zalopay_hoc_phi_id', 'zalopay_orderurl', 'zalopay_zptranstoken']);

                    return redirect()
                        ->route('sinh-vien.hoc-phi.show', $hocPhi->id)
                        ->with('success', 'Thanh toán thành công! Mã giao dịch: ' . $appTransId);
                } else {
                    DB::rollBack();
                    return redirect()
                        ->route('sinh-vien.hoc-phi.index')
                        ->with('error', 'KhÃ´ng tÃ¬m tháº¥y giao dá»‹ch hoáº·c Ä‘Ã£ Ä‘Æ°á»£c xá»­ lÃ½.');
                }

            } catch (\Exception $e) {
                DB::rollBack();
                
                return redirect()
                    ->route('sinh-vien.hoc-phi.index')
                    ->with('error', 'CÃ³ lá»—i xáº£y ra khi xá»­ lÃ½ thanh toÃ¡n: ' . $e->getMessage());
            }
        } elseif (isset($statusResult['returncode']) && $statusResult['returncode'] == 2) {
            // Payment failed or cancelled
            // Delete pending payment record
            LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->delete();

            return redirect()
                ->route('sinh-vien.hoc-phi.show', $hocPhiId ?? 0)
                ->with('error', 'Giao dá»‹ch tháº¥t báº¡i hoáº·c Ä‘Ã£ bá»‹ há»§y.');
        } else {
            // Payment processing
            return redirect()
                ->route('sinh-vien.hoc-phi.show', $hocPhiId ?? 0)
                ->with('info', 'Giao dá»‹ch Ä‘ang Ä‘Æ°á»£c xá»­ lÃ½. Vui lÃ²ng kiá»ƒm tra láº¡i sau.');
        }
    }

    /**
     * Handle ZaloPay IPN (Callback from ZaloPay server)
     */
    public function zaloPayIpn(Request $request)
    {
        $zaloPayService = new ZaloPayService();
        
        // Get JSON data from request body
        $postData = $request->all();
        
        // Verify MAC
        if (!$zaloPayService->verifyCallback($postData)) {
            return response()->json([
                'returncode' => -1,
                'returnmessage' => 'mac not equal'
            ]);
        }

        // Parse callback data
        $dataJson = $zaloPayService->parseCallbackData($postData['data'] ?? '{}');
        
        if (!$dataJson) {
            return response()->json([
                'returncode' => 0,
                'returnmessage' => 'Invalid callback data'
            ]);
        }

        $appTransId = $dataJson['apptransid'] ?? '';

        // Payment successful - update database
        try {
            DB::beginTransaction();

            $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();

            if ($lichSu && str_contains($lichSu->ghi_chu ?? '', 'Đang chờ')) {
                $hocPhi = $lichSu->hocPhiHocKy;

                // Update payment record
                $lichSu->update([
                    'ngay_dong' => now(),
                    'ghi_chu' => 'Thanh toán thành công qua ZaloPay. Mã giao dịch: ' . $appTransId,
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

                return response()->json([
                    'returncode' => 1,
                    'returnmessage' => 'success'
                ]);
            }

            DB::rollBack();
            return response()->json([
                'returncode' => 0,
                'returnmessage' => 'Order not found'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ZaloPay IPN Error: ' . $e->getMessage());
            
            return response()->json([
                'returncode' => 0, // ZaloPay server sẽ callback lại (tối đa 3 lần)
                'returnmessage' => $e->getMessage()
            ]);
        }
    }
}
