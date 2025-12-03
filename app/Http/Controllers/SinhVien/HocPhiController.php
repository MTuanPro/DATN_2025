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
     * Hiển thị danh sách học phí của sinh viên theo từng học kỳ có phân trang
     *
     * Hiển thị:
     * - Tất cả các khoản học phí theo học kỳ (mới nhất lên đầu)
     * - Phân trang 10 khoản/trang
     * - Tính tổng kết: Tổng học phí, Đã đóng, Còn lại
     * - Kèm thông tin học kỳ (eager load)
     *
     * @return \Illuminate\View\View View danh sách học phí với thống kê
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy thông tin sinh viên
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
     * Hiển thị chi tiết một khoản học phí cụ thể
     *
     * Bao gồm:
     * - Thông tin học phí tổng quát (học kỳ, tổng tiền, đã đóng, còn lại)
     * - Chi tiết học phí từng môn học (chiTietHocPhiMon)
     * - Thông tin môn học và lớp học phần đã đăng ký (eager load 3 level)
     *
     * @param int $id ID của khoản học phí (hoc_phi_hoc_ky.id)
     * @return \Illuminate\View\View View chi tiết học phí
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu học phí không tồn tại
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
     * Hiển thị lịch sử thanh toán học phí của một học kỳ có phân trang
     *
     * Bao gồm:
     * - Tất cả các lần thanh toán (LichSuDongHocPhi)
     * - Thông tin: Ngày đóng, Số tiền, Phương thức (VNPay/Chuyển khoản/Tiền mặt), Mã giao dịch
     * - Sắp xếp theo ngày đóng giảm dần (mới nhất đầu tiên)
     * - Phân trang 10 giao dịch/trang
     *
     * @param int $id ID của khoản học phí cần xem lịch sử
     * @return \Illuminate\View\View View lịch sử thanh toán
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy
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
     * Xuất hóa đơn học phí ra file PDF để tải về
     *
     * Nội dung PDF bao gồm:
     * - Thông tin sinh viên (đầy đủ: MSSV, họ tên, lớp, ngành, khoa)
     * - Thông tin học kỳ và học phí (tổng, đã đóng, còn lại)
     * - Chi tiết học phí từng môn học
     * - Lịch sử thanh toán (nếu có)
     * - Định dạng chuẩn hóa đơn theo mẫu nhà trường
     *
     * Tên file: HocPhi_MSSV_TenHocKy.pdf
     *
     * @param int $id ID của khoản học phí cần xuất PDF
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File PDF download
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 nếu không có quyền
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
     * Hiển thị trang hướn dẫn thanh toán học phí
     *
     * Bao gồm hướn dẫn:
     * - Cách thanh toán qua VNPay (quét mã QR, thẻ ngân hàng)
     * - Cách thanh toán chuyển khoản (số tài khoản, nội dung chuyển khoản)
     * - Cách thanh toán bằng tiền mặt (phòng tài vụ)
     * - Lưu ý khi thanh toán
     *
     * @return \Illuminate\View\View Trang hướn dẫn thanh toán
     */
    public function huongDan()
    {
        return view('sinhvien.hoc-phi.huong-dan');
    }

    /**
<<<<<<< HEAD
     * Hiển thị form thanh toán học phí qua VNPay
     *
     * Kiểm tra:
     * - Sinh viên có quyền truy cập khoản học phí này không
     * - Còn số tiền cần thanh toán không (so_tien_con_lai > 0)
     * - Nếu đã thanh toán đủ: redirect về trang chi tiết với thông báo
     *
     * Form cho phép nhập số tiền muốn thanh toán (tối thiểu 1,000 đ, tối đa = so_tien_con_lai).
     *
     * @param int $id ID của khoản học phí cần thanh toán
     * @return \Illuminate\View\View Form thanh toán VNPay
     * @return \Illuminate\Http\RedirectResponse Redirect nếu đã thanh toán đủ hoặc không tìm thấy
=======
     * Show form to pay tuition via ZaloPay
>>>>>>> b05ccc9876a8b428a1cb263d9332ed1a628483f1
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
<<<<<<< HEAD
     * Khởi tạo yêu cầu thanh toán học phí qua cổng thanh toán VNPay
     *
     * Quy trình:
     * 1. Validate quyền truy cập và số tiền thanh toán:
     *    - Số tiền ≥ 1,000 đ (VNPay minimum)
     *    - Số tiền ≤ so_tien_con_lai (không cho thanh toán thừa)
     * 2. Tạo thông tin đơn hàng (orderInfo):
     *    - Nội dung: "Thanh toan hoc phi - [Ten hoc ky] - [MSSV]"
     *    - OrderID: HP_[hoc_phi_id]_[sinh_vien_id]_[timestamp]
     * 3. Sử dụng database transaction để đảm bảo data integrity:
     *    - Tạo bản ghi LichSuDongHocPhi với trạng thái 'Đang chờ xác nhận từ VNPay'
     *    - Lưu ma_giao_dich = orderId để tra cứu sau này
     * 4. Gọi VNPayService để tạo payment URL:
     *    - Thêm chữ ký số (HMAC SHA512) bảo mật
     *    - Encrypt dữ liệu theo chuẩn VNPay
     * 5. Lưu orderId và hocPhiId vào session để verify callback
     * 6. Redirect sinh viên đến trang thanh toán VNPay
     * 7. Nếu có lỗi: Rollback transaction, xóa bản ghi pending, hiển thông báo lỗi
     *
     * @param Request $request Chứa so_tien_dong (số tiền muốn thanh toán)
     * @param int $id ID của khoản học phí cần thanh toán
     * @return \Illuminate\Http\RedirectResponse Redirect đến VNPay payment URL hoặc về form với lỗi
     * @throws \Exception Khi có lỗi tạo giao dịch
=======
     * Initiate ZaloPay payment
>>>>>>> b05ccc9876a8b428a1cb263d9332ed1a628483f1
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
                if (isset($result['orderurl']) && !empty($result['orderurl'])) {
                    // ✅ Sử dụng URL gốc từ ZaloPay, không decode hay modify
                    // URL từ ZaloPay đã được encode đúng và cần giữ nguyên
                    $orderUrl = trim($result['orderurl']);
                    
                    // Chỉ validate cơ bản - không decode vì có thể làm hỏng URL
                    // URL gateway của ZaloPay có format đặc biệt và cần giữ nguyên
                    if (empty($orderUrl) || strlen($orderUrl) < 10) {
                        Log::error('ZaloPay orderurl too short or empty:', [
                            'orderurl' => $orderUrl,
                            'original' => $result['orderurl'] ?? null
                        ]);
                        throw new \Exception('URL thanh toán từ ZaloPay không hợp lệ');
                    }
                    
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

                // Get error message from response
                $errorMessage = $result['returnmessage'] ?? 'Không thể tạo yêu cầu thanh toán.';
                
                // Provide more specific error messages
                if (isset($result['returncode'])) {
                    switch ($result['returncode']) {
                        case -1:
                            $errorMessage = 'ZaloPay chưa được cấu hình. Vui lòng liên hệ quản trị viên.';
                            break;
                        case -2:
                            $errorMessage = 'Thông tin xác thực ZaloPay không hợp lệ. Vui lòng kiểm tra cấu hình.';
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
                    'result' => $result
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
<<<<<<< HEAD
     * Xử lý callback từ VNPay sau khi sinh viên hoàn tất thanh toán (Return URL)
     *
     * Quy trình:
     * 1. Nhận toàn bộ query params từ VNPay
     * 2. Xác thực chữ ký số (Secure Hash) bằng VNPayService:
     *    - Kiểm tra chữ ký HMAC SHA512
     *    - Đảm bảo dữ liệu không bị giả mạo
     * 3. Kiểm tra mã response (responseCode):
     *    - '00' = Giao dịch thành công
     *    - Khác '00' = Giao dịch thất bại/bị hủy
     * 4. Nếu thành công (responseCode = '00'):
     *    - Sử dụng database transaction
     *    - Tìm bản ghi LichSuDongHocPhi theo orderId
     *    - Cập nhật thông tin: ngay_dong, ngan_hang, ma_giao_dich VNPay, ghi_chu
     *    - Cập nhật HocPhiHocKy: so_tien_da_dong, so_tien_con_lai, ngay_dong_lan_cuoi
     *    - Gọi updateTrangThai() để tự động cập nhật trạng thái (chua_dong/dang_dong/da_dong/qua_han)
     *    - Nếu thanh toán đủ (so_tien_con_lai = 0):
     *      + Cập nhật tất cả ChiTietHocPhiMon -> trang_thai = 'da_thanh_toan'
     *      + Tự động thêm sinh viên vào danh sách chờ xếp lớp (HocPhiService)
     *    - Commit transaction
     *    - Redirect đến trang chi tiết học phí với thông báo thành công
     * 5. Nếu thất bại:
     *    - Xóa bản ghi LichSuDongHocPhi pending
     *    - Redirect với thông báo lỗi (từ VNPay response)
     * 6. Xóa orderId và hocPhiId khỏi session sau khi xử lý xong
     *
     * @param Request $request Chứa toàn bộ query params từ VNPay callback
     * @return \Illuminate\Http\RedirectResponse Redirect với thông báo thành công/thất bại
     * @throws \Exception Khi có lỗi cập nhật database
=======
     * Handle ZaloPay payment callback (return URL)
>>>>>>> b05ccc9876a8b428a1cb263d9332ed1a628483f1
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
<<<<<<< HEAD
     * X\u1eed l\u00fd VNPay IPN (Instant Payment Notification) - Server-to-Server webhook
     *
     * \u0110\u00e2y l\u00e0 endpoint cho VNPay g\u1ecdi th\u00f4ng b\u00e1o thanh to\u00e1n t\u1ef1 \u0111\u1ed9ng (kh\u00f4ng qua browser).
     * S\u1eed d\u1ee5ng \u0111\u1ec3 \u0111\u1ed3ng b\u1ed9 k\u1ebft qu\u1ea3 thanh to\u00e1n v\u1edbi h\u1ec7 th\u1ed1ng VNPay.\n     *\n     * Quy tr\u00ecnh:\n     * 1. Nh\u1eadn POST request t\u1eeb VNPay server (kh\u00f4ng ph\u1ea3i t\u1eeb browser)\n     * 2. X\u00e1c th\u1ef1c Secure Hash \u0111\u1ec3 \u0111\u1ea3m b\u1ea3o request t\u1eeb VNPay ch\u00ednh th\u1ee9c\n     * 3. Ki\u1ec3m tra m\u00e3 response:\n     *    - '00' = Giao d\u1ecbch th\u00e0nh c\u00f4ng\n     *    - Kh\u00e1c '00' = Giao d\u1ecbch th\u1ea5t b\u1ea1i\n     * 4. N\u1ebfu th\u00e0nh c\u00f4ng v\u00e0 ch\u01b0a c\u1eadp nh\u1eadt:\n     *    - C\u1eadp nh\u1eadt LichSuDongHocPhi (t\u01b0\u01a1ng t\u1ef1 vnpayCallback)\n     *    - C\u1eadp nh\u1eadt HocPhiHocKy\n     *    - C\u1eadp nh\u1eadt ChiTietHocPhiMon n\u1ebfu thanh to\u00e1n \u0111\u1ee7\n     *    - Th\u00eam v\u00e0o danh s\u00e1ch ch\u1edd x\u1ebfp l\u1edbp n\u1ebfu thanh to\u00e1n \u0111\u1ee7\n     * 5. Tr\u1ea3 v\u1ec1 JSON response cho VNPay:\n     *    - RspCode: '00' = Success, '97' = Invalid signature, '99' = Error\n     *    - Message: Chi ti\u1ebft k\u1ebft qu\u1ea3\n     *\n     * L\u01b0u \u00fd:\n     * - IPN c\u00f3 th\u1ec3 \u0111\u1ebfn tr\u01b0\u1edbc/sau vnpayCallback, c\u1ea7n ki\u1ec3m tra tr\u1ea1ng th\u00e1i tr\u01b0\u1edbc khi c\u1eadp nh\u1eadt\n     * - Idempotent: Kh\u00f4ng c\u1eadp nh\u1eadt 2 l\u1ea7n cho c\u00f9ng 1 giao d\u1ecbch\n     * - VNPay c\u00f3 th\u1ec3 g\u1eedi IPN nhi\u1ec1u l\u1ea7n n\u1ebfu kh\u00f4ng nh\u1eadn \u0111\u01b0\u1ee3c RspCode '00'\n     *\n     * @param Request $request POST data t\u1eeb VNPay server\n     * @return \\Illuminate\\Http\\JsonResponse JSON {RspCode, Message}\n     * @throws \\Exception Khi c\u00f3 l\u1ed7i c\u1eadp nh\u1eadt database\n     */\n    public function vnpayIpn(Request $request)
=======
     * Handle ZaloPay IPN (Callback from ZaloPay server)
     */
    public function zaloPayIpn(Request $request)
>>>>>>> b05ccc9876a8b428a1cb263d9332ed1a628483f1
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
