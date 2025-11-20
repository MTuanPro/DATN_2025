<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\HocPhiHocKy;
use App\Models\ChiTietHocPhiMon;
use App\Models\LichSuDongHocPhi;
use App\Services\PaymentService;
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
     * Tạo thanh toán online (DEMO - Giả lập thanh toán)
     */
    public function thanhToanOnline(Request $request, $id, PaymentService $paymentService)
    {
        $request->validate([
            'gateway' => 'required|in:vnpay,momo',
            'so_tien_dong' => 'required|numeric|min:10000',
        ], [
            'gateway.required' => 'Vui lòng chọn phương thức thanh toán',
            'gateway.in' => 'Phương thức thanh toán không hợp lệ',
            'so_tien_dong.required' => 'Vui lòng nhập số tiền cần đóng',
            'so_tien_dong.numeric' => 'Số tiền phải là số',
            'so_tien_dong.min' => 'Số tiền tối thiểu là 10,000 VNĐ',
        ]);

        $user = auth()->user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin sinh viên!'
            ], 403);
        }

        $hocPhi = HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
            ->with(['sinhVien', 'hocKy'])
            ->findOrFail($id);

        // Kiểm tra đã đóng đủ chưa
        if ($hocPhi->so_tien_con_lai <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Học phí đã được đóng đủ!'
            ], 400);
        }

        $soTien = $request->so_tien_dong;

        // Validate số tiền
        if ($soTien > $hocPhi->so_tien_con_lai) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền thanh toán không được lớn hơn số tiền còn lại!'
            ], 400);
        }

        // DEMO: Giả lập thanh toán thành công ngay lập tức
        try {
            DB::beginTransaction();
            
            $maGiaoDich = 'DEMO_' . strtoupper($request->gateway) . '_' . $hocPhi->id . '_' . time();
            $gateway = $request->gateway === 'vnpay' ? 'VNPay' : 'MoMo';
            
            // Tạo lịch sử thanh toán
            $lichSu = LichSuDongHocPhi::create([
                'hoc_phi_hoc_ky_id' => $hocPhi->id,
                'ma_giao_dich' => $maGiaoDich,
                'so_tien_dong' => $soTien,
                'ngay_dong' => now(),
                'phuong_thuc_thanh_toan' => $gateway,
                'ghi_chu' => "Thanh toán học phí qua {$gateway} (DEMO)",
                'response_data' => json_encode([
                    'demo' => true,
                    'gateway' => $request->gateway,
                    'message' => 'Giao dịch demo - Không có thanh toán thực tế'
                ])
            ]);

            // Cập nhật học phí
            $hocPhi->so_tien_da_dong += $soTien;
            $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
            $hocPhi->save();
            
            // Cập nhật trạng thái
            $hocPhi->updateTrangThai();

            DB::commit();

            return response()->json([
                'success' => true,
                'demo' => true,
                'message' => 'Thanh toán demo thành công!',
                'data' => [
                    'ma_giao_dich' => $maGiaoDich,
                    'so_tien' => $soTien,
                    'gateway' => $gateway,
                    'so_tien_con_lai' => $hocPhi->so_tien_con_lai,
                    'trang_thai' => $hocPhi->trang_thai,
                    'ngay_thanh_toan' => now()->format('d/m/Y H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment demo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xử lý callback từ payment gateway (IPN - Instant Payment Notification)
     * Đây là server-to-server callback
     */
    public function paymentCallback(Request $request, PaymentService $paymentService)
    {
        // Xác định gateway từ request
        $gateway = 'momo'; // Mặc định là MoMo (VNPay không có IPN riêng)
        
        if ($request->has('vnp_SecureHash')) {
            $gateway = 'vnpay';
        }

        $result = $paymentService->handleCallback($gateway, $request->all());

        // Trả về response cho payment gateway
        if ($gateway === 'vnpay') {
            return response()->json([
                'RspCode' => $result['success'] ? '00' : '99',
                'Message' => $result['message']
            ]);
        } else {
            // MoMo
            return response()->json([
                'resultCode' => $result['success'] ? 0 : 1,
                'message' => $result['message']
            ]);
        }
    }

    /**
     * Trang return sau khi sinh viên thanh toán
     */
    public function paymentReturn(Request $request, PaymentService $paymentService)
    {
        // Xác định gateway
        $gateway = 'momo';
        if ($request->has('vnp_SecureHash')) {
            $gateway = 'vnpay';
        }

        $result = $paymentService->handleCallback($gateway, $request->all());

        if ($result['success']) {
            return redirect()
                ->route('sinh-vien.hoc-phi.show', $result['hoc_phi_id'])
                ->with('success', 'Thanh toán thành công! Số tiền đã được cập nhật vào tài khoản học phí của bạn.');
        } else {
            return redirect()
                ->route('sinh-vien.hoc-phi.index')
                ->with('error', 'Thanh toán thất bại: ' . $result['message']);
        }
    }
}
