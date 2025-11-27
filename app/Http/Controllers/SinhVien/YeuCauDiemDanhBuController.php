<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\YeuCauDiemDanhBu;
use App\Models\LichHocChiTiet;
use App\Models\LopHocPhanSinhVien;
use App\Models\DiemDanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class YeuCauDiemDanhBuController extends Controller
{
    /**
     * Gửi yêu cầu điểm danh bù
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lich_hoc_chi_tiet_id' => 'required|exists:lich_hoc_chi_tiet,id',
            'ly_do' => 'required|string|max:1000',
        ], [
            'lich_hoc_chi_tiet_id.required' => 'Vui lòng chọn buổi học',
            'lich_hoc_chi_tiet_id.exists' => 'Buổi học không tồn tại',
            'ly_do.required' => 'Vui lòng nhập lý do',
            'ly_do.max' => 'Lý do không được quá 1000 ký tự',
        ]);

        $sinhVien = Auth::user()->sinhVien;
        if (!$sinhVien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin sinh viên'
            ], 403);
        }

        // Lấy buổi học
        $buoiHoc = LichHocChiTiet::findOrFail($validated['lich_hoc_chi_tiet_id']);

        // Kiểm tra sinh viên có thuộc lớp học phần này không
        $lhpsv = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->first();

        if (!$lhpsv) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thuộc lớp học phần này'
            ], 403);
        }

        // Kiểm tra chỉ cho phép gửi yêu cầu trong ngày (ngày hôm đó) hoặc trong vòng 24h từ khi điểm danh
        $ngayHoc = Carbon::parse($buoiHoc->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->startOfDay();
        $ngayHienTai = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
        
        // Kiểm tra xem có điểm danh không và thời gian điểm danh
        $diemDanh = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
            ->where('lich_hoc_chi_tiet_id', $validated['lich_hoc_chi_tiet_id'])
            ->first();
        
        $coTheGuiYeuCau = false;
        if ($diemDanh && $diemDanh->thoi_gian_diem_danh) {
            // Nếu đã có điểm danh, cho phép gửi trong vòng 24h từ khi điểm danh (nếu điểm danh trong ngày hiện tại) hoặc trong ngày học
            $thoiGianDiemDanh = Carbon::parse($diemDanh->thoi_gian_diem_danh)->setTimezone('Asia/Ho_Chi_Minh');
            $thoiGianHienTai = Carbon::now('Asia/Ho_Chi_Minh');
            $coTheGuiYeuCau = $ngayHoc->isSameDay($ngayHienTai) || 
                            ($thoiGianDiemDanh->isSameDay($ngayHienTai) && $thoiGianHienTai->diffInHours($thoiGianDiemDanh) <= 24);
        } else {
            // Nếu chưa có điểm danh, chỉ cho phép trong ngày học
            $coTheGuiYeuCau = $ngayHoc->isSameDay($ngayHienTai);
        }

        if (!$coTheGuiYeuCau) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể gửi yêu cầu điểm danh bù trong ngày học hoặc trong vòng 24h từ khi điểm danh (nếu điểm danh trong ngày hiện tại).'
            ], 403);
        }

        // Kiểm tra đã có yêu cầu chưa
        $yeuCauTonTai = YeuCauDiemDanhBu::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
            ->where('lich_hoc_chi_tiet_id', $validated['lich_hoc_chi_tiet_id'])
            ->whereIn('trang_thai', ['cho_duyet', 'da_duyet'])
            ->exists();

        if ($yeuCauTonTai) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã gửi yêu cầu điểm danh bù cho buổi học này rồi'
            ], 400);
        }

        // Kiểm tra đã có điểm danh chưa
        $daCoDiemDanh = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
            ->where('lich_hoc_chi_tiet_id', $validated['lich_hoc_chi_tiet_id'])
            ->where('trang_thai', 'co_mat')
            ->exists();

        if ($daCoDiemDanh) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã có điểm danh có mặt cho buổi học này rồi'
            ], 400);
        }

        try {
            // Tạo yêu cầu
            $yeuCau = YeuCauDiemDanhBu::create([
                'lop_hoc_phan_sinh_vien_id' => $lhpsv->id,
                'lich_hoc_chi_tiet_id' => $validated['lich_hoc_chi_tiet_id'],
                'ly_do' => $validated['ly_do'],
                'trang_thai' => 'cho_duyet',
                'ngay_gui' => Carbon::now('Asia/Ho_Chi_Minh'),
            ]);

            // Load relationships
            $yeuCau->load(['lichHocChiTiet.lopHocPhan.monHoc', 'lopHocPhanSinhVien.sinhVien']);

            // Gửi thông báo cho giảng viên
            $this->guiThongBaoChoGiangVien($yeuCau);

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi yêu cầu điểm danh bù thành công. Giảng viên sẽ xem xét và phản hồi.'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi yêu cầu điểm danh bù: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi yêu cầu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi thông báo cho giảng viên
     */
    private function guiThongBaoChoGiangVien($yeuCau)
    {
        try {
            $buoiHoc = $yeuCau->lichHocChiTiet;
            if (!$buoiHoc) {
                Log::warning('Không tìm thấy buổi học cho yêu cầu điểm danh bù', ['yeu_cau_id' => $yeuCau->id]);
                return;
            }

            // Lấy sinh viên từ lopHocPhanSinhVien
            $sinhVien = $yeuCau->lopHocPhanSinhVien->sinhVien ?? null;
            if (!$sinhVien) {
                Log::warning('Không tìm thấy sinh viên cho yêu cầu điểm danh bù', ['yeu_cau_id' => $yeuCau->id]);
                return;
            }

            $lopHocPhan = $buoiHoc->lopHocPhan;
            if (!$lopHocPhan || !$lopHocPhan->monHoc) {
                Log::warning('Không tìm thấy lớp học phần hoặc môn học cho yêu cầu điểm danh bù', ['yeu_cau_id' => $yeuCau->id]);
                return;
            }

            // Lấy giảng viên chính
            $phanCong = \App\Models\PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('vai_tro', 'giang_vien_chinh')
                ->with('giangVien.user')
                ->first();

            if ($phanCong && $phanCong->giangVien && $phanCong->giangVien->user) {
                $thongBao = \App\Models\ThongBao::create([
                    'tieu_de' => 'Yêu cầu điểm danh bù - ' . $sinhVien->ho_ten,
                    'noi_dung' => "Sinh viên {$sinhVien->ho_ten} ({$sinhVien->ma_sinh_vien}) đã gửi yêu cầu điểm danh bù cho buổi học:\n\n"
                        . "• Môn học: {$lopHocPhan->monHoc->ten_mon}\n"
                        . "• Lớp: {$lopHocPhan->ma_lop_hp}\n"
                        . "• Ngày học: " . Carbon::parse($buoiHoc->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y') . "\n"
                        . "• Lý do: {$yeuCau->ly_do}\n\n"
                        . "Vui lòng truy cập phần thông báo để xem xét và duyệt yêu cầu.",
                    'loai_nguon' => 'thu_cong',
                    'loai_thong_bao' => 'diem_danh',
                    'muc_do_quan_trong' => 'quan_trong',
                    'doi_tuong' => 'yeu_cau_diem_danh_bu',
                    'doi_tuong_cu_the_id' => $yeuCau->id,
                    'nguoi_gui_id' => Auth::id(),
                    'ngay_gui' => now(),
                    'lien_ket_loai' => 'yeu_cau_diem_danh_bu',
                    'lien_ket_id' => $yeuCau->id,
                ]);

                \App\Models\NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao->id,
                    'nguoi_nhan_id' => $phanCong->giangVien->user_id,
                    'da_doc' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo yêu cầu điểm danh bù: ' . $e->getMessage());
        }
    }
}
