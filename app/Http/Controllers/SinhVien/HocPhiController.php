<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\HocPhiHocKy;
use App\Models\ChiTietHocPhiMon;
use App\Models\LichSuDongHocPhi;
use App\Services\MoMoService;
use App\Services\HocPhiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
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
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
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
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
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
            abort(403, 'Không tìm thấy thông tin sinh viên!');
        }

        $hocPhi = HocPhiHocKy::with([
            'sinhVien.user',
            'sinhVien.lopHanhChinh.chuyenNganh.nganh.khoa',
            'hocKy',
            'chiTietHocPhiMon.monHoc',
            'lichSuDongHocPhi'
        ])
            ->where('sinh_vien_id', $sinhVien->id)
            ->findOrFail($id);

        $pdf = Pdf::loadView('sinhvien.hoc-phi.pdf', compact('hocPhi'));

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
     * Show form to pay tuition via MoMo
     */
    public function showMoMoPayment($id)
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
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

        return view('sinhvien.hoc-phi.momo-payment', compact('hocPhi'));
    }

    /**
     * Initiate MoMo payment
     */
    public function initiateMoMoPayment(Request $request, $id)
    {
        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        $hocPhi = HocPhiHocKy::with(['hocKy'])
            ->where('sinh_vien_id', $sinhVien->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'so_tien_dong' => 'required|numeric|min:1000|max:' . $hocPhi->so_tien_con_lai,
        ], [
            'so_tien_dong.required' => 'Vui lòng nhập số tiền thanh toán',
            'so_tien_dong.numeric' => 'Số tiền phải là số',
            'so_tien_dong.min' => 'Số tiền tối thiểu là 1,000 đ',
            'so_tien_dong.max' => 'Số tiền không được vượt quá số tiền còn lại',
        ]);

        $amount = (int) $validated['so_tien_dong'];

        // Create order info
        $orderInfo = "Thanh toan hoc phi - {$hocPhi->hocKy->ten_hoc_ky} - {$sinhVien->ma_sinh_vien}";
        
        // Create order ID
        $orderId = 'HP_' . $hocPhi->id . '_' . $sinhVien->id . '_' . time();

        // Extra data (JSON)
        $extraData = json_encode([
            'hoc_phi_hoc_ky_id' => $hocPhi->id,
            'sinh_vien_id' => $sinhVien->id,
            'so_tien_dong' => $amount,
        ]);

        // Create temporary payment record (pending)
        try {
            DB::beginTransaction();

            $lichSu = LichSuDongHocPhi::create([
                'hoc_phi_hoc_ky_id' => $hocPhi->id,
                'so_tien_dong' => $amount,
                'ngay_dong' => now(),
                'phuong_thuc_thanh_toan' => 'MoMo',
                'ma_giao_dich' => $orderId,
                'ghi_chu' => 'Đang chờ xác nhận từ MoMo',
            ]);

            DB::commit();

            // Initiate MoMo payment
            $momoService = new MoMoService();
            $result = $momoService->createPayment($orderId, $amount, $orderInfo, $extraData);

            if ($result['success']) {
                // Store orderId in session for verification
                session(['momo_order_id' => $orderId]);
                session(['momo_hoc_phi_id' => $hocPhi->id]);

                // Redirect to MoMo payment page
                return redirect($result['payUrl']);
            } else {
                // Delete the pending record
                $lichSu->delete();

                return redirect()
                    ->route('sinh-vien.hoc-phi.momo-payment', $id)
                    ->with('error', $result['message'] ?? 'Không thể tạo yêu cầu thanh toán. Vui lòng thử lại.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('sinh-vien.hoc-phi.momo-payment', $id)
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Handle MoMo payment callback (return URL)
     */
    public function momoCallback(Request $request)
    {
        $momoService = new MoMoService();
        $data = $request->all();

        $verifyResult = $momoService->verifyCallback($data);

        if (!$verifyResult['valid']) {
            return redirect()
                ->route('sinh-vien.hoc-phi.index')
                ->with('error', 'Giao dịch không hợp lệ. Vui lòng liên hệ bộ phận tài vụ.');
        }

        $orderId = $verifyResult['orderId'] ?? '';
        $hocPhiId = session('momo_hoc_phi_id');

        // Clear session
        session()->forget(['momo_order_id', 'momo_hoc_phi_id']);

        if ($verifyResult['success']) {
            // Payment successful
            try {
                DB::beginTransaction();

                // Find the payment record
                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $orderId)->first();

                if ($lichSu) {
                    $hocPhi = $lichSu->hocPhiHocKy;

                    // Update payment record
                    $lichSu->update([
                        'ngay_dong' => now(),
                        'ghi_chu' => 'Thanh toán thành công qua MoMo. Mã giao dịch: ' . ($verifyResult['transId'] ?? ''),
                    ]);

                    // Update HocPhiHocKy
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

                        // ✅ KHI ĐÓNG ĐỦ HỌC PHÍ: Tự động thêm vào danh sách chờ xếp lớp
                        $hocPhiService = new HocPhiService();
                        $hocPhiService->themVaoDanhSachChoXepLop($hocPhi->sinh_vien_id, $hocPhi->hoc_ky_id);
                    }

                    DB::commit();

                    return redirect()
                        ->route('sinh-vien.hoc-phi.show', $hocPhi->id)
                        ->with('success', 'Thanh toán thành công! Mã giao dịch: ' . ($verifyResult['transId'] ?? $orderId));
                } else {
                    DB::rollBack();
                    return redirect()
                        ->route('sinh-vien.hoc-phi.index')
                        ->with('error', 'Không tìm thấy giao dịch. Vui lòng liên hệ bộ phận tài vụ.');
                }

            } catch (\Exception $e) {
                DB::rollBack();
                
                return redirect()
                    ->route('sinh-vien.hoc-phi.index')
                    ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
            }
        } else {
            // Payment failed or cancelled
            $resultCode = $verifyResult['resultCode'] ?? -1;
            $message = $verifyResult['message'] ?? 'Giao dịch thất bại';

            // Delete pending payment record
            LichSuDongHocPhi::where('ma_giao_dich', $orderId)->delete();

            $errorMessage = match($resultCode) {
                1003 => 'Bạn đã hủy giao dịch.',
                1002 => 'Giao dịch thất bại. Vui lòng thử lại.',
                default => $message,
            };

            return redirect()
                ->route('sinh-vien.hoc-phi.show', $hocPhiId ?? 0)
                ->with('error', $errorMessage);
        }
    }

    /**
     * Handle MoMo IPN (Instant Payment Notification) - Server to server
     */
    public function momoIpn(Request $request)
    {
        $momoService = new MoMoService();
        $data = $request->all();

        $verifyResult = $momoService->verifyCallback($data);

        if (!$verifyResult['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature'
            ], 400);
        }

        $orderId = $verifyResult['orderId'] ?? '';

        if ($verifyResult['success']) {
            // Payment successful - update database
            try {
                DB::beginTransaction();

                $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $orderId)->first();

                if ($lichSu) {
                    $hocPhi = $lichSu->hocPhiHocKy;

                    // Update payment record if not already updated
                    if ($lichSu->ghi_chu && str_contains($lichSu->ghi_chu, 'Đang chờ')) {
                        $lichSu->update([
                            'ngay_dong' => now(),
                            'ghi_chu' => 'Thanh toán thành công qua MoMo. Mã giao dịch: ' . ($verifyResult['transId'] ?? ''),
                        ]);

                        // Update HocPhiHocKy
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

                            // ✅ KHI ĐÓNG ĐỦ HỌC PHÍ: Tự động thêm vào danh sách chờ xếp lớp
                            $hocPhiService = new HocPhiService();
                            $hocPhiService->themVaoDanhSachChoXepLop($hocPhi->sinh_vien_id, $hocPhi->hoc_ky_id);
                        }
                    }

                    DB::commit();
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment processed successfully'
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            // Payment failed - delete pending record
            LichSuDongHocPhi::where('ma_giao_dich', $orderId)->delete();

            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed'
            ], 200);
        }
    }
}
