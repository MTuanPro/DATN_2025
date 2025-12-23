<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\YeuCauDiemDanhBu;
use App\Models\DiemDanh;
use App\Models\PhanCongGiangDay;
use App\Models\CanhBaoHocVu;
use App\Models\LichHocChiTiet;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class YeuCauDiemDanhBuController extends Controller
{
    /**
     * Danh sách yêu cầu điểm danh bù
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên.');
        }

        // Lấy các lớp được phân công
        $lopHocPhanIds = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->pluck('lop_hoc_phan_id')
            ->toArray();

        // Query yêu cầu điểm danh bù
        $query = YeuCauDiemDanhBu::with([
            'lopHocPhanSinhVien.sinhVien',
            'lichHocChiTiet.lopHocPhan.monHoc',
            'nguoiDuyet'
        ])
        ->whereHas('lichHocChiTiet', function ($q) use ($lopHocPhanIds) {
            $q->whereIn('lop_hoc_phan_id', $lopHocPhanIds);
        });

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        } else {
            // Mặc định chỉ hiển thị chờ duyệt
            $query->where('trang_thai', 'cho_duyet');
        }

        $yeuCaus = $query->orderBy('ngay_gui', 'desc')->paginate(20);

        return view('giangvien.yeu-cau-diem-danh-bu.index', compact('yeuCaus'));
    }

    /**
     * Duyệt yêu cầu điểm danh bù
     */
    public function duyet(Request $request, $id)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ giảng viên'
            ], 403);
        }

        $yeuCau = YeuCauDiemDanhBu::with(['lichHocChiTiet', 'lopHocPhanSinhVien'])->findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $yeuCau->lichHocChiTiet->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền duyệt yêu cầu này'
            ], 403);
        }

        // Kiểm tra trạng thái
        if ($yeuCau->trang_thai !== 'cho_duyet') {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu này đã được xử lý rồi'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Cập nhật trạng thái yêu cầu
            $yeuCau->update([
                'trang_thai' => 'da_duyet',
                'ngay_duyet' => Carbon::now('Asia/Ho_Chi_Minh'),
                'nguoi_duyet_id' => $user->id,
            ]);

            // Tạo hoặc cập nhật điểm danh thành "có mặt"
            DiemDanh::updateOrCreate(
                [
                    'lop_hoc_phan_sinh_vien_id' => $yeuCau->lop_hoc_phan_sinh_vien_id,
                    'lich_hoc_chi_tiet_id' => $yeuCau->lich_hoc_chi_tiet_id,
                ],
                [
                    'trang_thai' => 'co_mat',
                    'thoi_gian_diem_danh' => Carbon::now('Asia/Ho_Chi_Minh'),
                    'ghi_chu' => 'Điểm danh bù - Lý do: ' . $yeuCau->ly_do,
                ]
            );

            // Cập nhật lại cảnh báo học vụ dựa trên tỷ lệ vắng mới
            $this->capNhatCanhBaoHocVu($yeuCau);

            // Gửi thông báo cho sinh viên
            $this->guiThongBaoChoSinhVien($yeuCau, 'da_duyet');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã duyệt yêu cầu điểm danh bù thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Từ chối yêu cầu điểm danh bù
     */
    public function tuChoi(Request $request, $id)
    {
        $validated = $request->validate([
            'ly_do_tu_choi' => 'required|string|max:500',
        ], [
            'ly_do_tu_choi.required' => 'Vui lòng nhập lý do từ chối',
            'ly_do_tu_choi.max' => 'Lý do từ chối không được quá 500 ký tự',
        ]);

        $user = Auth::user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ giảng viên'
            ], 403);
        }

        $yeuCau = YeuCauDiemDanhBu::with(['lichHocChiTiet'])->findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $yeuCau->lichHocChiTiet->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền từ chối yêu cầu này'
            ], 403);
        }

        // Kiểm tra trạng thái
        if ($yeuCau->trang_thai !== 'cho_duyet') {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu này đã được xử lý rồi'
            ], 400);
        }

        // Cập nhật trạng thái yêu cầu
        $yeuCau->update([
            'trang_thai' => 'tu_choi',
            'ly_do_tu_choi' => $validated['ly_do_tu_choi'],
            'ngay_duyet' => Carbon::now(),
            'nguoi_duyet_id' => $user->id,
        ]);

        // Gửi thông báo cho sinh viên
        $this->guiThongBaoChoSinhVien($yeuCau, 'tu_choi', $validated['ly_do_tu_choi']);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối yêu cầu điểm danh bù'
        ]);
    }

    /**
     * Gửi thông báo cho sinh viên
     */
    private function guiThongBaoChoSinhVien($yeuCau, $trangThai, $lyDoTuChoi = null)
    {
        try {
            $sinhVien = $yeuCau->sinhVien;
            $buoiHoc = $yeuCau->lichHocChiTiet;
            $lopHocPhan = $buoiHoc->lopHocPhan;

            if (!$sinhVien || !$sinhVien->user) {
                return;
            }

            if ($trangThai === 'da_duyet') {
                $tieuDe = 'Yêu cầu điểm danh bù đã được duyệt';
                $noiDung = "Yêu cầu điểm danh bù của bạn cho buổi học:\n\n"
                    . "• Môn học: {$lopHocPhan->monHoc->ten_mon}\n"
                    . "• Lớp: {$lopHocPhan->ma_lop_hp}\n"
                    . "• Ngày học: " . Carbon::parse($buoiHoc->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y') . "\n\n"
                    . "Đã được giảng viên duyệt. Điểm danh của bạn đã được cập nhật thành 'Có mặt'.";
            } else {
                $tieuDe = 'Yêu cầu điểm danh bù bị từ chối';
                $noiDung = "Yêu cầu điểm danh bù của bạn cho buổi học:\n\n"
                    . "• Môn học: {$lopHocPhan->monHoc->ten_mon}\n"
                    . "• Lớp: {$lopHocPhan->ma_lop_hp}\n"
                    . "• Ngày học: " . Carbon::parse($buoiHoc->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y') . "\n\n"
                    . "Đã bị giảng viên từ chối.\n"
                    . "Lý do: {$lyDoTuChoi}";
            }

            $thongBao = \App\Models\ThongBao::create([
                'tieu_de' => $tieuDe,
                'noi_dung' => $noiDung,
                'loai_nguon' => 'thu_cong',
                'loai_thong_bao' => 'diem_danh',
                'muc_do_quan_trong' => 'quan_trong',
                'doi_tuong' => 'sinh_vien',
                'doi_tuong_cu_the_id' => $sinhVien->id,
                'nguoi_gui_id' => Auth::id(),
                'ngay_gui' => now(),
                'lien_ket_loai' => 'yeu_cau_diem_danh_bu',
                'lien_ket_id' => $yeuCau->id,
            ]);

            \App\Models\NguoiNhanThongBao::create([
                'thong_bao_id' => $thongBao->id,
                'nguoi_nhan_id' => $sinhVien->user_id,
                'da_doc' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo yêu cầu điểm danh bù cho sinh viên: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật cảnh báo học vụ sau khi duyệt điểm danh bù
     */
    private function capNhatCanhBaoHocVu($yeuCau)
    {
        try {
            $lopHocPhanSinhVien = $yeuCau->lopHocPhanSinhVien;
            $sinhVien = $lopHocPhanSinhVien->sinhVien;
            $lopHocPhan = $yeuCau->lichHocChiTiet->lopHocPhan;
            $hocKy = $lopHocPhan->hocKy;

            if (!$sinhVien || !$lopHocPhan || !$hocKy) {
                return;
            }

            // Tính lại tổng số buổi học và thống kê điểm danh
            $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('trang_thai', '!=', 'huy')
                ->count();

            if ($tongBuoiHoc == 0) {
                return;
            }

            // Lấy thống kê điểm danh mới nhất
            $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
                ->selectRaw('
                    COUNT(*) as tong_buoi_diem_danh,
                    SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                    SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                    SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                ')
                ->first();

            $coMat = $diemDanhStats ? ($diemDanhStats->co_mat ?? 0) : 0;
            $vang = $diemDanhStats ? ($diemDanhStats->vang ?? 0) : 0;
            
            // Tính tỷ lệ vắng mới
            $tyLeVang = ($vang / $tongBuoiHoc) * 100;
            $tyLeCoMat = ($coMat / $tongBuoiHoc) * 100;

            // Tìm cảnh báo học vụ hiện có
            $canhBao = CanhBaoHocVu::where('sinh_vien_id', $sinhVien->id)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'vang_nhieu')
                ->where('ghi_chu', 'like', "%{$lopHocPhan->ma_lop_hp}%")
                ->where('trang_thai', 'chua_xu_ly')
                ->first();

            if ($tyLeVang <= 20) {
                // Nếu tỷ lệ vắng đã giảm xuống dưới 20%
                if ($canhBao) {
                    // Cập nhật trạng thái cảnh báo thành đã xử lý với thông tin chi tiết
                    $lyDoMoi = "Đã khắc phục. Tỷ lệ vắng ban đầu vượt 20%, sau điểm danh bù đã giảm xuống " . number_format($tyLeVang, 1) . "%. ";
                    $lyDoMoi .= "Tổng số buổi học: {$tongBuoiHoc}, Có mặt: {$coMat}, Vắng: {$vang}, ";
                    $lyDoMoi .= "Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%";
                    
                    $canhBao->update([
                        'trang_thai' => 'da_xu_ly',
                        'ngay_xu_ly' => now(),
                        'ly_do' => $lyDoMoi,
                        'ket_qua_xu_ly' => 'Sinh viên đã điểm danh bù. Tỷ lệ vắng hiện tại: ' . number_format($tyLeVang, 1) . '% (đã đạt yêu cầu)',
                    ]);

                    // Gửi thông báo cập nhật cho sinh viên
                    if ($sinhVien->user_id) {
                        $notificationService = new NotificationService();
                        
                        $noiDung = "✅ Cập nhật cảnh báo học vụ\n\n";
                        $noiDung .= "📚 Môn học: {$lopHocPhan->monHoc->ten_mon}\n";
                        $noiDung .= "📋 Lớp học phần: {$lopHocPhan->ma_lop_hp}\n\n";
                        $noiDung .= "🎉 Sau khi điểm danh bù được duyệt, tỷ lệ vắng của bạn đã giảm xuống " . number_format($tyLeVang, 1) . "%.\n";
                        $noiDung .= "📊 Thống kê điểm danh hiện tại:\n";
                        $noiDung .= "• Tổng số buổi học: {$tongBuoiHoc}\n";
                        $noiDung .= "• Có mặt: {$coMat} buổi\n";
                        $noiDung .= "• Vắng: {$vang} buổi\n";
                        $noiDung .= "• Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%\n\n";
                        $noiDung .= "✅ Cảnh báo học vụ đã được gỡ bỏ. Hãy tiếp tục duy trì chuyên cần tốt!";

                        $notificationService->createAutoNotification(
                            loaiThongBao: 'canh_bao_hoc_vu',
                            tieuDe: '✅ Đã gỡ cảnh báo học vụ - ' . $lopHocPhan->monHoc->ten_mon,
                            noiDung: $noiDung,
                            nguoiNhanIds: [$sinhVien->user_id],
                            options: [
                                'muc_do_quan_trong' => 'quan_trong',
                                'gui_web_notification' => true,
                            ]
                        );
                    }

                    Log::info('✅ Đã gỡ cảnh báo học vụ sau điểm danh bù', [
                        'sinh_vien_id' => $sinhVien->id,
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'ty_le_vang_cu' => $tyLeVang,
                        'ty_le_vang_moi' => $tyLeVang,
                    ]);
                }
            } else {
                // Nếu tỷ lệ vắng vẫn > 20% nhưng đã giảm
                if ($canhBao) {
                    // Xác định mức độ cảnh báo mới
                    $mucDo = 'canh_cao';
                    if ($tyLeCoMat < 60) {
                        $mucDo = 'dinh_chi';
                    } elseif ($tyLeCoMat < 70) {
                        $mucDo = 'canh_cao';
                    }

                    $lyDoMoi = "Vắng quá 20% số buổi học. ";
                    $lyDoMoi .= "Tổng số buổi học: {$tongBuoiHoc}, ";
                    $lyDoMoi .= "Có mặt: {$coMat}, ";
                    $lyDoMoi .= "Vắng: {$vang}, ";
                    $lyDoMoi .= "Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%";

                    // Cập nhật cảnh báo với thông tin mới
                    $canhBao->update([
                        'ly_do' => $lyDoMoi,
                        'muc_do' => $mucDo,
                        'ngay_canh_bao' => now(),
                    ]);

                    // Gửi thông báo cập nhật cho sinh viên
                    if ($sinhVien->user_id) {
                        $notificationService = new NotificationService();
                        
                        $noiDung = "⚠️ Cập nhật cảnh báo học vụ\n\n";
                        $noiDung .= "📚 Môn học: {$lopHocPhan->monHoc->ten_mon}\n";
                        $noiDung .= "📋 Lớp học phần: {$lopHocPhan->ma_lop_hp}\n\n";
                        $noiDung .= "Sau khi điểm danh bù được duyệt, tỷ lệ vắng của bạn hiện tại là " . number_format($tyLeVang, 1) . "%.\n";
                        $noiDung .= "📊 Thống kê điểm danh:\n";
                        $noiDung .= "• Tổng số buổi học: {$tongBuoiHoc}\n";
                        $noiDung .= "• Có mặt: {$coMat} buổi\n";
                        $noiDung .= "• Vắng: {$vang} buổi\n";
                        $noiDung .= "• Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%\n\n";
                        $noiDung .= "⚠️ Tỷ lệ vắng vẫn vượt quá 20%. Hãy cố gắng tham gia đầy đủ các buổi học tiếp theo!";

                        $notificationService->createAutoNotification(
                            loaiThongBao: 'canh_bao_hoc_vu',
                            tieuDe: '⚠️ Cập nhật cảnh báo học vụ - ' . $lopHocPhan->monHoc->ten_mon,
                            noiDung: $noiDung,
                            nguoiNhanIds: [$sinhVien->user_id],
                            options: [
                                'muc_do_quan_trong' => 'quan_trong',
                                'gui_web_notification' => true,
                            ]
                        );
                    }

                    Log::info('✅ Đã cập nhật cảnh báo học vụ sau điểm danh bù', [
                        'sinh_vien_id' => $sinhVien->id,
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'ty_le_vang_moi' => $tyLeVang,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật cảnh báo học vụ sau điểm danh bù: ' . $e->getMessage(), [
                'yeu_cau_id' => $yeuCau->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
}
