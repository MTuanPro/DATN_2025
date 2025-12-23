<?php

namespace App\Observers;

use App\Models\DiemDanh;
use App\Models\LichHocChiTiet;
use App\Models\CanhBaoHocVu;
use App\Models\LopHocPhanSinhVien;
use App\Services\DiemService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class DiemDanhObserver
{
    protected $diemService;

    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    /**
     * Handle the DiemDanh "created" event.
     * Tự động tính lại kết quả học tập khi có điểm danh mới
     */
    public function created(DiemDanh $diemDanh): void
    {
        $this->tinhLaiKetQuaHocTap($diemDanh);
    }

    /**
     * Handle the DiemDanh "updated" event.
     * Tự động tính lại kết quả học tập khi điểm danh được cập nhật
     */
    public function updated(DiemDanh $diemDanh): void
    {
        // Chỉ tính lại nếu trạng thái điểm danh thay đổi
        if ($diemDanh->wasChanged('trang_thai')) {
            $this->tinhLaiKetQuaHocTap($diemDanh);
        }
    }

    /**
     * Handle the DiemDanh "deleted" event.
     * Tự động tính lại kết quả học tập khi điểm danh bị xóa
     */
    public function deleted(DiemDanh $diemDanh): void
    {
        $this->tinhLaiKetQuaHocTap($diemDanh);
    }

    /**
     * Tính lại kết quả học tập dựa trên điểm danh
     */
    protected function tinhLaiKetQuaHocTap(DiemDanh $diemDanh): void
    {
        try {
            $lopHocPhanSinhVienId = $diemDanh->lop_hoc_phan_sinh_vien_id;
            
            if ($lopHocPhanSinhVienId) {
                // Tính lại điểm tổng kết (sẽ kiểm tra cả điểm F và tỷ lệ vắng > 20%)
                $this->diemService->tinhDiemTong($lopHocPhanSinhVienId);
                
                // Kiểm tra và tạo cảnh báo vắng nhiều
                $this->kiemTraVaTaoCanhBaoVangNhieu($diemDanh);
                
                Log::info("✅ [AUTO] Đã tính lại kết quả học tập sau khi thay đổi điểm danh", [
                    'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSinhVienId,
                    'diem_danh_id' => $diemDanh->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error("❌ [AUTO] Lỗi tính lại kết quả học tập sau khi thay đổi điểm danh: {$e->getMessage()}", [
                'diem_danh_id' => $diemDanh->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Kiểm tra và tạo cảnh báo vắng nhiều khi điểm danh thay đổi
     */
    protected function kiemTraVaTaoCanhBaoVangNhieu(DiemDanh $diemDanh): void
    {
        try {
            $lopHocPhanSinhVien = LopHocPhanSinhVien::with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy', 'sinhVien'])
                ->find($diemDanh->lop_hoc_phan_sinh_vien_id);
            
            if (!$lopHocPhanSinhVien || !$lopHocPhanSinhVien->sinhVien || !$lopHocPhanSinhVien->lopHocPhan) {
                return;
            }

            $sinhVien = $lopHocPhanSinhVien->sinhVien;
            $lopHocPhan = $lopHocPhanSinhVien->lopHocPhan;
            $hocKy = $lopHocPhan->hocKy;
            
            if (!$hocKy) {
                return;
            }

            // Lấy tổng số buổi học từ lịch học chi tiết
            $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('trang_thai', '!=', 'huy')
                ->count();

            if ($tongBuoiHoc == 0) {
                return;
            }

            // Tính thống kê điểm danh
            $diemDanhStats = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
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
            
            // Tính tỷ lệ vắng
            $tyLeVang = ($vang / $tongBuoiHoc) * 100;
            $tyLeCoMat = ($coMat / $tongBuoiHoc) * 100;

            // Kiểm tra xem đã có cảnh báo vắng nhiều cho môn này chưa
            $canhBaoTonTai = CanhBaoHocVu::where('sinh_vien_id', $sinhVien->id)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'vang_nhieu')
                ->where('trang_thai', 'chua_xu_ly')
                ->where('ghi_chu', 'like', "%{$lopHocPhan->ma_lop_hp}%")
                ->first();

            // Nếu vắng > 20% → tạo/cập nhật cảnh báo
            if ($tyLeVang > 20) {
                $thongKe = [
                    'tong_buoi' => $tongBuoiHoc,
                    'co_mat' => $coMat,
                    'vang' => $vang,
                    'di_tre' => $diemDanhStats ? ($diemDanhStats->di_tre ?? 0) : 0,
                    'nghi_phep' => $diemDanhStats ? ($diemDanhStats->nghi_phep ?? 0) : 0,
                    'ty_le' => $tyLeCoMat,
                ];

                // Xác định mức độ cảnh báo
                $mucDo = 'canh_cao';
                if ($tyLeCoMat < 60) {
                    $mucDo = 'dinh_chi';
                } elseif ($tyLeCoMat < 70) {
                    $mucDo = 'canh_cao';
                }

                if ($canhBaoTonTai) {
                    // Cập nhật cảnh báo hiện có
                    $canhBaoTonTai->update([
                        'ly_do' => $this->taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat),
                        'muc_do' => $mucDo,
                        'ngay_canh_bao' => now(),
                    ]);
                    $canhBao = $canhBaoTonTai;
                } else {
                    // Tạo cảnh báo mới
                    $canhBao = CanhBaoHocVu::create([
                        'sinh_vien_id' => $sinhVien->id,
                        'hoc_ky_id' => $hocKy->id,
                        'loai_canh_bao' => 'vang_nhieu',
                        'muc_do' => $mucDo,
                        'ly_do' => $this->taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat),
                        'ngay_canh_bao' => now(),
                        'trang_thai' => 'chua_xu_ly',
                        'ghi_chu' => "Tự động tạo từ hệ thống điểm danh. Môn: {$lopHocPhan->monHoc->ten_mon} - Lớp: {$lopHocPhan->ma_lop_hp}",
                    ]);
                }

                // Gửi thông báo cho sinh viên
                if ($sinhVien->user_id) {
                    $notificationService = new NotificationService();
                    
                    $noiDung = "⚠️ Cảnh báo: Vắng quá 20% số buổi học!\n\n";
                    $noiDung .= "📚 Môn học: {$lopHocPhan->monHoc->ten_mon}\n";
                    $noiDung .= "📋 Lớp học phần: {$lopHocPhan->ma_lop_hp}\n";
                    $noiDung .= "📊 Thống kê điểm danh:\n";
                    $noiDung .= "• Tổng số buổi học: {$tongBuoiHoc}\n";
                    $noiDung .= "• Có mặt: {$coMat} buổi\n";
                    $noiDung .= "• Vắng: {$vang} buổi\n";
                    $noiDung .= "• Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%\n";
                    $noiDung .= "• Tỷ lệ vắng: " . number_format($tyLeVang, 1) . "%\n\n";
                    $noiDung .= "⚠️ Hậu quả: Nếu tiếp tục vắng, bạn có thể bị cấm thi và không đạt môn học này.\n\n";
                    $noiDung .= "💡 Hãy liên hệ với giảng viên hoặc phòng đào tạo để được hỗ trợ.";

                    $notificationService->createAutoNotification(
                        loaiThongBao: 'canh_bao_hoc_vu',
                        tieuDe: '⚠️ Cảnh báo: Vắng quá 20% - ' . $lopHocPhan->monHoc->ten_mon,
                        noiDung: $noiDung,
                        nguoiNhanIds: [$sinhVien->user_id],
                        options: [
                            'muc_do_quan_trong' => 'quan_trong',
                            'gui_web_notification' => true,
                            'gui_email' => true,
                        ]
                    );
                }

                Log::info('✅ [AUTO] Đã tạo/cập nhật cảnh báo vắng nhiều', [
                    'sinh_vien_id' => $sinhVien->id,
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'canh_bao_id' => $canhBao->id,
                    'ty_le_vang' => $tyLeVang,
                ]);
            } else {
                // Nếu tỷ lệ vắng ≤ 20% và có cảnh báo đang tồn tại → gỡ bỏ cảnh báo
                if ($canhBaoTonTai) {
                    // Cập nhật trạng thái cảnh báo thành đã xử lý với thông tin chi tiết
                    $lyDoMoi = "Đã khắc phục. Tỷ lệ vắng ban đầu vượt 20%, sau khôi phục điểm danh đã giảm xuống " . number_format($tyLeVang, 1) . "%. ";
                    $lyDoMoi .= "Tổng số buổi học: {$tongBuoiHoc}, Có mặt: {$coMat}, Vắng: {$vang}, ";
                    $lyDoMoi .= "Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%";
                    
                    $canhBaoTonTai->update([
                        'trang_thai' => 'da_xu_ly',
                        'ngay_xu_ly' => now(),
                        'ly_do' => $lyDoMoi,
                        'ket_qua_xu_ly' => 'Sinh viên đã khôi phục điểm danh. Tỷ lệ vắng hiện tại: ' . number_format($tyLeVang, 1) . '% (đã đạt yêu cầu)',
                    ]);

                    // Gửi thông báo cập nhật cho sinh viên
                    if ($sinhVien->user_id) {
                        $notificationService = new NotificationService();
                        
                        $noiDung = "✅ Cập nhật cảnh báo học vụ\n\n";
                        $noiDung .= "📚 Môn học: {$lopHocPhan->monHoc->ten_mon}\n";
                        $noiDung .= "📋 Lớp học phần: {$lopHocPhan->ma_lop_hp}\n\n";
                        $noiDung .= "🎉 Sau khi điểm danh được khôi phục, tỷ lệ vắng của bạn đã giảm xuống " . number_format($tyLeVang, 1) . "%.\n";
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

                    Log::info('✅ [AUTO] Đã gỡ cảnh báo vắng nhiều sau khi khôi phục điểm danh', [
                        'sinh_vien_id' => $sinhVien->id,
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'canh_bao_id' => $canhBaoTonTai->id,
                        'ty_le_vang_moi' => $tyLeVang,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ [AUTO] Lỗi kiểm tra và tạo cảnh báo vắng nhiều: {$e->getMessage()}", [
                'diem_danh_id' => $diemDanh->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Tạo lý do cảnh báo
     */
    protected function taoLyDoCanhBao($lopHocPhan, $thongKe, $tyLeCoMat): string
    {
        $lyDo = "Vắng quá 20% số buổi học. ";
        $lyDo .= "Tổng số buổi học: {$thongKe['tong_buoi']}, ";
        $lyDo .= "Có mặt: {$thongKe['co_mat']}, ";
        $lyDo .= "Vắng: {$thongKe['vang']}, ";
        $lyDo .= "Tỷ lệ có mặt: " . number_format($tyLeCoMat, 1) . "%";
        
        return $lyDo;
    }
}

