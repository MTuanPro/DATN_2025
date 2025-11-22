<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\HocPhiHocKy;
use App\Models\LichSuDongHocPhi;
use App\Models\ChiTietHocPhiMon;
use App\Models\DangKyMonHocTam;
use App\Models\DaoTao\SinhVien;
use App\Models\HocKy;
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
            'bien_lai_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ghi_chu' => 'nullable|string',
        ], [
            'so_tien_dong.required' => 'Số tiền đóng là bắt buộc',
            'so_tien_dong.numeric' => 'Số tiền phải là số',
            'so_tien_dong.min' => 'Số tiền phải lớn hơn 0',
            'ngay_dong.required' => 'Ngày đóng là bắt buộc',
            'phuong_thuc_thanh_toan.required' => 'Phương thức thanh toán là bắt buộc',
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

            DB::commit();
            
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
}

