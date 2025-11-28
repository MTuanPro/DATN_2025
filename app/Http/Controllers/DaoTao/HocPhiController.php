<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\HocPhiHocKy;
use App\Models\LichSuDongHocPhi;
use App\Models\ChiTietHocPhiMon;
use App\Models\DangKyMonHocTam;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HocPhiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = HocPhiHocKy::with(['sinhVien.user', 'hocKy']);

        // Filter by semester
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Filter by status
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Search by student name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->where('ma_sinh_vien', 'like', "%{$search}%")
                    ->orWhere('ho_ten', 'like', "%{$search}%");
            });
        }

        $hocPhis = $query->orderBy('created_at', 'desc')->paginate(20);
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.hoc-phi.index', compact('hocPhis', 'hocKys'));
    }





// public function update(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'phi_dich_vu' => 'nullable|numeric|min:0',
//             'han_dong' => 'required|date',
//             'ghi_chu' => 'nullable|string',
//         ]);

//         try {
//             $hocPhi = HocPhiHocKy::findOrFail($id);
            
//             $hocPhi->phi_dich_vu = $validated['phi_dich_vu'] ?? 0;
//             $hocPhi->han_dong = $validated['han_dong'];
//             $hocPhi->ghi_chu = $validated['ghi_chu'];
            
//             // Recalculate total
//             $hocPhi->tong_so_tien = $hocPhi->tong_hoc_phi_mon_hoc + $hocPhi->phi_dich_vu;
//             $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
            
//             $hocPhi->save();
//             $hocPhi->updateTrangThai();

//             return redirect()
//                 ->route('dao-tao.hoc-phi.show', $id)
//                 ->with('success', 'Cập nhật học phí thành công!');
//         } catch (\Exception $e) {
//             return redirect()
//                 ->back()
//                 ->withInput()
//                 ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $hocPhi = HocPhiHocKy::with([
            'sinhVien.user',
            'sinhVien.lopHanhChinh',
            'hocKy',
            'chiTietHocPhiMon.monHoc',
            'lichSuDongHocPhi.nguoiThu.user'
        ])->findOrFail($id);

        return view('daotao.hoc-phi.show', compact('hocPhi'));
    }

    /**
     * Show edit form for tuition fee
     */
    public function edit($id)
    {
        $hocPhi = HocPhiHocKy::with([
            'sinhVien.user',
            'hocKy',
            'chiTietHocPhiMon.monHoc'
        ])->findOrFail($id);

        return view('daotao.hoc-phi.edit', compact('hocPhi'));
    }

    /**
     * Update tuition fee information
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'phi_dich_vu' => 'nullable|numeric|min:0',
            'han_dong' => 'required|date',
            'ghi_chu' => 'nullable|string',
        ]);

        try {
            $hocPhi = HocPhiHocKy::findOrFail($id);
            
            $hocPhi->phi_dich_vu = $validated['phi_dich_vu'] ?? 0;
            $hocPhi->han_dong = $validated['han_dong'];
            $hocPhi->ghi_chu = $validated['ghi_chu'];
            
            // Recalculate total
            $hocPhi->tong_so_tien = $hocPhi->tong_hoc_phi_mon_hoc + $hocPhi->phi_dich_vu;
            $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
            
            $hocPhi->save();
            $hocPhi->updateTrangThai();

            return redirect()
                ->route('dao-tao.hoc-phi.show', $id)
                ->with('success', 'Cập nhật học phí thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show form to record payment
     */
    public function payment($id)
    {
        $hocPhi = HocPhiHocKy::with(['sinhVien.user', 'hocKy'])->findOrFail($id);

        return view('daotao.hoc-phi.payment', compact('hocPhi'));
    }

    /**
     * Store payment record
     */
    public function storePayment(Request $request, $id)
    {
        $hocPhi = HocPhiHocKy::findOrFail($id);
        
        $validated = $request->validate([
            'so_tien_dong' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($hocPhi) {
                    if ($value > $hocPhi->so_tien_con_lai) {
                        $fail('Số tiền đóng không được vượt quá số tiền còn lại (' . number_format($hocPhi->so_tien_con_lai, 0, ',', '.') . ' đ)');
                    }
                },
            ],
            'ngay_dong' => 'required|date',
            'phuong_thuc_thanh_toan' => 'required|string',
            'ngan_hang' => 'nullable|string',
            'bien_lai_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ghi_chu' => 'nullable|string',
        ], [
            'so_tien_dong.required' => 'Số tiền đóng là bắt buộc',
            'so_tien_dong.numeric' => 'Số tiền phải là số',
            'so_tien_dong.min' => 'Số tiền phải lớn hơn 0',
            'ngay_dong.required' => 'Ngày đóng là bắt buộc',
            'phuong_thuc_thanh_toan.required' => 'Phương thức thanh toán là bắt buộc',
            'bien_lai_file.required' => 'Biên lai thanh toán là bắt buộc',
            'bien_lai_file.file' => 'File không hợp lệ',
            'bien_lai_file.mimes' => 'File phải là định dạng: PDF, JPG, JPEG, PNG',
            'bien_lai_file.max' => 'Kích thước file không được vượt quá 5MB',
        ]);

        try {
            DB::beginTransaction();

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('bien_lai_file')) {
                $filePath = $request->file('bien_lai_file')->store('bien-lai', 'public');
            }

            // Create payment history
            $nguoiThuId = null;
            $user = auth()->user();
            if ($user && $user->daoTao) {
                $nguoiThuId = $user->daoTao->id;
            }

            $lichSu = LichSuDongHocPhi::create([
                'hoc_phi_hoc_ky_id' => $hocPhi->id,
                'so_tien_dong' => $validated['so_tien_dong'],
                'ngay_dong' => $validated['ngay_dong'],
                'phuong_thuc_thanh_toan' => $validated['phuong_thuc_thanh_toan'],
                'ma_giao_dich' => LichSuDongHocPhi::generateMaGiaoDich(),
                'ngan_hang' => $validated['ngan_hang'] ?? null,
                'nguoi_thu_id' => $nguoiThuId,
                'bien_lai_file' => $filePath,
                'ghi_chu' => $validated['ghi_chu'],
            ]);

            // Update HocPhiHocKy
            $hocPhi->so_tien_da_dong += $validated['so_tien_dong'];
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
                $this->themVaoDanhSachChoXepLop($hocPhi->sinh_vien_id, $hocPhi->hoc_ky_id);
            }

            // Tạo biên lai PDF và lưu vào storage
            $bienLaiPdfPath = $this->generateBienLaiPdf($lichSu, $hocPhi);
            
            // Cập nhật lichSu với đường dẫn biên lai PDF
            if ($bienLaiPdfPath) {
                $lichSu->bien_lai_pdf = $bienLaiPdfPath;
                $lichSu->save();
            }

            DB::commit();
            
            // Gửi thông báo cho sinh viên
            $this->sendPaymentNotification($lichSu, $hocPhi);
            
            // Lấy thông tin sinh viên để redirect đến trang thời khóa biểu
            $sinhVien = $hocPhi->sinhVien;
            $hocKy = $hocPhi->hocKy;

            return redirect()
                ->route('dao-tao.hoc-phi.show', $id)
                ->with('success', 'Ghi nhận thanh toán thành công! Mã giao dịch: ' . $lichSu->ma_giao_dich)
                ->with('show_timetable', true)
                ->with('sinh_vien_id', $sinhVien->id)
                ->with('hoc_ky_id', $hocKy->id);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Export tuition fees to Excel
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return redirect()->back()->with('info', 'Chức năng xuất Excel đang phát triển');
    }

    /**
     * Show statistics
     */
    public function statistics(Request $request)
    {
        $hocKyId = $request->get('hoc_ky_id');
        
        $query = HocPhiHocKy::query();
        
        if ($hocKyId) {
            $query->where('hoc_ky_id', $hocKyId);
        }

        $stats = [
            'tong_sinh_vien' => $query->count(),
            'da_nop_du' => (clone $query)->where('trang_thai', 'da_nop_du')->count(),
            'da_nop_mot_phan' => (clone $query)->where('trang_thai', 'da_nop_mot_phan')->count(),
            'chua_nop' => (clone $query)->where('trang_thai', 'chua_nop')->count(),
            'qua_han' => (clone $query)->where('trang_thai', 'qua_han')->count(),
            'tong_hoc_phi' => (clone $query)->sum('tong_so_tien'),
            'da_thu' => (clone $query)->sum('so_tien_da_dong'),
            'con_lai' => (clone $query)->sum('so_tien_con_lai'),
        ];

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.hoc-phi.statistics', compact('stats', 'hocKys', 'hocKyId'));
    }

    /**
     * Show list of students with overdue tuition
     */
    public function overdue()
    {
        $hocPhis = HocPhiHocKy::with(['sinhVien.user', 'hocKy'])
            ->where('trang_thai', 'qua_han')
            ->orWhere(function ($query) {
                $query->where('han_dong', '<', now())
                    ->where('so_tien_con_lai', '>', 0);
            })
            ->orderBy('han_dong', 'asc')
            ->paginate(20);

        return view('daotao.hoc-phi.overdue', compact('hocPhis'));
    }

    /**
     * Thêm sinh viên vào danh sách chờ xếp lớp khi đóng đủ học phí
     * 
     * @param int $sinhVienId
     * @param int $hocKyId
     * @return void
     */
    private function themVaoDanhSachChoXepLop($sinhVienId, $hocKyId)
    {
        try {
            // Lấy tất cả đăng ký đang chờ đóng học phí của sinh viên trong học kỳ này
            $dangKys = DangKyMonHocTam::where('sinh_vien_id', $sinhVienId)
                ->where('hoc_ky_id', $hocKyId)
                ->where('trang_thai', 'cho_dong_hoc_phi')
                ->get();

            foreach ($dangKys as $dangKy) {
                // Kiểm tra xem sinh viên đã đóng đủ học phí cho môn này chưa
                $hocPhi = HocPhiHocKy::where('sinh_vien_id', $sinhVienId)
                    ->where('hoc_ky_id', $hocKyId)
                    ->first();

                if ($hocPhi && $hocPhi->trang_thai == 'da_nop_du') {
                    // Kiểm tra xem môn này có trong chi tiết học phí không
                    $chiTiet = ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                        ->where('mon_hoc_id', $dangKy->mon_hoc_id)
                        ->first();

                    if ($chiTiet) {
                        // Chuyển trạng thái từ 'cho_dong_hoc_phi' sang 'cho_xep_lop'
                        $dangKy->trang_thai = 'cho_xep_lop';
                        $dangKy->save();

                        Log::info("✅ Đã thêm sinh viên {$sinhVienId} - Môn {$dangKy->mon_hoc_id} vào danh sách chờ xếp lớp sau khi đóng đủ học phí");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ Lỗi khi thêm vào danh sách chờ xếp lớp: " . $e->getMessage());
        }
    }

    /**
     * Tạo biên lai thanh toán PDF
     * 
     * @param LichSuDongHocPhi $lichSu
     * @param HocPhiHocKy $hocPhi
     * @return string|null Đường dẫn file PDF
     */
    private function generateBienLaiPdf($lichSu, $hocPhi)
    {
        try {
            // Load lại relationships cần thiết nếu chưa được load
            if (!$lichSu->relationLoaded('nguoiThu')) {
                $lichSu->load('nguoiThu');
            }
            
            if (!$hocPhi->relationLoaded('sinhVien')) {
                $hocPhi->load('sinhVien');
            }
            
            if (!$hocPhi->sinhVien->relationLoaded('lopHanhChinh')) {
                $hocPhi->sinhVien->load('lopHanhChinh');
            }
            
            if (!$hocPhi->relationLoaded('hocKy')) {
                $hocPhi->load('hocKy');
            }
            
            if (!$hocPhi->relationLoaded('chiTietHocPhiMon')) {
                $hocPhi->load('chiTietHocPhiMon.monHoc');
            }

            // Tạo PDF
            $pdf = Pdf::loadView('daotao.hoc-phi.bien-lai-pdf', [
                'lichSu' => $lichSu,
                'hocPhi' => $hocPhi,
            ]);
            
            $pdf->setPaper('a4', 'portrait');
            
            // Tên file
            $fileName = 'bien_lai_' . $lichSu->ma_giao_dich . '_' . now()->format('YmdHis') . '.pdf';
            $filePath = 'bien-lai-pdf/' . $fileName;
            
            // Lưu vào storage
            Storage::disk('public')->put($filePath, $pdf->output());
            
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Lỗi tạo biên lai PDF: ' . $e->getMessage(), [
                'lich_su_id' => $lichSu->id,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Gửi thông báo thanh toán cho sinh viên
     * 
     * @param LichSuDongHocPhi $lichSu
     * @param HocPhiHocKy $hocPhi
     * @return void
     */
    private function sendPaymentNotification($lichSu, $hocPhi)
    {
        try {
            $sinhVien = $hocPhi->sinhVien;
            
            if (!$sinhVien || !$sinhVien->user_id) {
                Log::warning('Không tìm thấy user_id của sinh viên để gửi thông báo', [
                    'sinh_vien_id' => $hocPhi->sinh_vien_id,
                    'lich_su_id' => $lichSu->id
                ]);
                return;
            }

            $notificationService = app(NotificationService::class);
            
            $tieuDe = "Xác nhận thanh toán học phí - Mã giao dịch: {$lichSu->ma_giao_dich}";
            
            $noiDung = "Kính gửi sinh viên {$sinhVien->ho_ten},\n\n"
                . "Hệ thống xác nhận bạn đã thanh toán học phí thành công với thông tin sau:\n\n"
                . "📋 Mã giao dịch: {$lichSu->ma_giao_dich}\n"
                . "💰 Số tiền: " . number_format($lichSu->so_tien_dong, 0, ',', '.') . " đ\n"
                . "📅 Ngày thanh toán: " . \Carbon\Carbon::parse($lichSu->ngay_dong)->format('d/m/Y H:i') . "\n"
                . "💳 Phương thức: " . $this->getPhuongThucThanhToanText($lichSu->phuong_thuc_thanh_toan) . "\n"
                . "📚 Học kỳ: {$hocPhi->hocKy->ten_hoc_ky} - {$hocPhi->hocKy->nam_hoc}\n\n"
                . "📄 Biên lai thanh toán đã được tạo và đính kèm trong thông báo này. Vui lòng tải về để lưu trữ.\n\n"
                . "Số tiền còn lại: " . number_format($hocPhi->so_tien_con_lai, 0, ',', '.') . " đ\n\n"
                . "Trân trọng!\n"
                . "Phòng Đào tạo";

            // Lấy đường dẫn biên lai PDF (nếu có)
            $bienLaiPdfPath = $lichSu->bien_lai_pdf;
            $options = [
                'muc_do_quan_trong' => 'quan_trong',
                'gui_email' => false,
                'gui_web_notification' => true,
            ];
            
            // Chỉ thêm file đính kèm nếu biên lai PDF tồn tại
            if ($bienLaiPdfPath && Storage::disk('public')->exists($bienLaiPdfPath)) {
                $options['file_dinh_kem'] = $bienLaiPdfPath;
            } else {
                // Nếu không có biên lai PDF, cập nhật nội dung thông báo
                $noiDung = str_replace(
                    "📄 Biên lai thanh toán đã được tạo và đính kèm trong thông báo này. Vui lòng tải về để lưu trữ.\n\n",
                    "📄 Biên lai thanh toán đã được tạo. Vui lòng vào menu 'Học phí' để xem và tải biên lai.\n\n",
                    $noiDung
                );
            }

            $notificationService->createAutoNotification(
                'hoc_phi',
                $tieuDe,
                $noiDung,
                [$sinhVien->user_id],
                $options
            );

            Log::info('Đã gửi thông báo thanh toán cho sinh viên', [
                'sinh_vien_id' => $sinhVien->id,
                'lich_su_id' => $lichSu->id,
                'ma_giao_dich' => $lichSu->ma_giao_dich
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo thanh toán: ' . $e->getMessage(), [
                'lich_su_id' => $lichSu->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Lấy text phương thức thanh toán
     * 
     * @param string $phuongThuc
     * @return string
     */
    private function getPhuongThucThanhToanText($phuongThuc)
    {
        $map = [
            'tien_mat' => 'Tiền mặt',
            'chuyen_khoan' => 'Chuyển khoản',
            'VNPay' => 'VNPay',
        ];

        return $map[$phuongThuc] ?? $phuongThuc;
    }

    /**
     * Xem/tải biên lai thanh toán PDF
     * 
     * @param int $lichSuId
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function viewBienLai($lichSuId)
    {
        $lichSu = LichSuDongHocPhi::with([
            'hocPhiHocKy.sinhVien.lopHanhChinh',
            'hocPhiHocKy.hocKy',
            'hocPhiHocKy.chiTietHocPhiMon.monHoc',
            'nguoiThu'
        ])->findOrFail($lichSuId);

        $hocPhi = $lichSu->hocPhiHocKy;
        $hocPhi->load(['sinhVien.lopHanhChinh', 'hocKy', 'chiTietHocPhiMon.monHoc']);

        // Kiểm tra quyền truy cập
        $user = auth()->user();
        $hasAccess = false;

        // Admin hoặc Đào tạo có quyền xem
        if ($user && ($user->hasRole('admin') || $user->hasRole('dao_tao'))) {
            $hasAccess = true;
        }
        // Sinh viên chỉ xem được biên lai của mình
        elseif ($user && $user->sinhVien && $user->sinhVien->id == $hocPhi->sinh_vien_id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền xem biên lai này');
        }

        // Nếu đã có file PDF đã lưu, tải file đó
        if ($lichSu->bien_lai_pdf && Storage::disk('public')->exists($lichSu->bien_lai_pdf)) {
            $filePath = Storage::disk('public')->path($lichSu->bien_lai_pdf);
            return response()->download($filePath, 'bien_lai_' . $lichSu->ma_giao_dich . '.pdf');
        }

        // Nếu chưa có, tạo mới
        $pdf = Pdf::loadView('daotao.hoc-phi.bien-lai-pdf', [
            'lichSu' => $lichSu,
            'hocPhi' => $hocPhi,
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('bien_lai_' . $lichSu->ma_giao_dich . '.pdf');
    }
}

