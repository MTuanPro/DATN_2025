<?php

namespace App\Services;

use App\Models\CauHinhDauDiem;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Models\NhapDiem;
use App\Models\BangDiem;
use App\Models\LichHocChiTiet;
use App\Models\DiemDanh;
use App\Models\CanhBaoHocVu;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiemService
{
    /**
     * Tính điểm tổng kết cho sinh viên trong lớp học phần
     */
    public function tinhDiemTong($lopHocPhanSinhVienId)
    {
        $lhpsv = LopHocPhanSinhVien::with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy', 'sinhVien'])->find($lopHocPhanSinhVienId);

        if (!$lhpsv) {
            return false;
        }

        // 1. Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
            ->orderBy('id')
            ->get();

        if ($cauHinhs->isEmpty()) {
            return false;
        }

        $tongDiem = 0;
        $daCoTatCaDiem = true;
        $diemCuoiKy = null; // Lưu điểm cuối kỳ để kiểm tra

        // 2. Tính điểm từng đầu
        foreach ($cauHinhs as $cauHinh) {
            // Lấy điểm đã nhập
            $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
                ->where('cau_hinh_id', $cauHinh->id)
                ->orderBy('cot_diem')
                ->get();

            // Kiểm tra đủ cột chưa
            if ($diems->count() < $cauHinh->so_cot) {
                $daCoTatCaDiem = false;
                continue;
            }

            // Tính trung bình các cột
            $diemTrungBinh = $diems->avg('diem_so');

            // Lưu điểm cuối kỳ nếu là đầu điểm cuối kỳ
            // Kiểm tra tên đầu điểm có chứa "cuối kỳ" hoặc "cuoi ky" (không phân biệt hoa thường)
            $tenDauDiem = mb_strtolower($cauHinh->ten_dau_diem);
            if (str_contains($tenDauDiem, 'cuối kỳ') || str_contains($tenDauDiem, 'cuoi ky') || 
                str_contains($tenDauDiem, 'cuối kì') || str_contains($tenDauDiem, 'cuoi ki')) {
                $diemCuoiKy = $diemTrungBinh;
            }

            // Nhân với tỷ lệ %
            $tongDiem += $diemTrungBinh * ($cauHinh->ty_le / 100);
        }

        // 3. Nếu chưa đủ điểm thì không tính
        if (!$daCoTatCaDiem) {
            return false;
        }

        // 4. Làm tròn 2 chữ số
        $diemHe10 = round($tongDiem, 2);

        // 5. Tính điểm hệ 4 và điểm chữ
        $diemHe4 = $this->chuyenDoiHe4($diemHe10);
        $diemChu = $this->chuyenDoiDiemChu($diemHe10);

        // 6. Kiểm tra điều kiện qua môn
        // Điều kiện ban đầu: Điểm tổng kết >= 4.0 VÀ điểm cuối kỳ >= 5.0 (nếu có)
        $quaMon = $diemHe10 >= 4.0;
        
        // Nếu có điểm cuối kỳ và điểm cuối kỳ < 5 thì không đạt
        if ($diemCuoiKy !== null && $diemCuoiKy < 5.0) {
            $quaMon = false;
        }
        
        // Kiểm tra điểm chữ = F → trượt
        if ($diemChu === 'F') {
            $quaMon = false;
        }
        
        // Kiểm tra tỷ lệ vắng > 20% → trượt
        // Lấy tổng số buổi học từ lịch học chi tiết
        $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
            ->where('trang_thai', '!=', 'huy')
            ->count();
        
        if ($tongBuoiHoc > 0) {
            // Lấy số buổi vắng từ điểm danh
            $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
                ->selectRaw('
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang
                ')
                ->first();
            
            $soBuoiVang = $diemDanhStats ? ($diemDanhStats->vang ?? 0) : 0;
            
            // Tính tỷ lệ vắng
            $tyLeVang = ($soBuoiVang / $tongBuoiHoc) * 100;
            
            // Nếu vắng > 20% → trượt (kể cả khi chưa điểm danh hết tất cả số buổi)
            if ($tyLeVang > 20) {
                $quaMon = false;
            }
        }

        // 7. Update hoặc tạo mới kết quả học tập
        // Tìm hoặc tạo bản ghi
        $ketQua = KetQuaHocTap::firstOrNew(
            ['lop_hoc_phan_sinh_vien_id' => $lopHocPhanSinhVienId]
        );
        
        // Lưu giá trị qua_mon cũ để kiểm tra thay đổi
        $quaMonCu = $ketQua->qua_mon ?? null;
        
        // Cập nhật tất cả các trường (luôn cập nhật qua_mon để đảm bảo logic mới được áp dụng)
        $ketQua->diem_he_10 = $diemHe10;
        $ketQua->diem_he_4 = $diemHe4;
        $ketQua->diem_chu = $diemChu;
        $ketQua->qua_mon = $quaMon; // Luôn cập nhật qua_mon với logic mới
        
        // Lưu mà không trigger events để tránh Observer ghi đè
        $ketQua->saveQuietly();

        // Nếu kết quả không đạt (qua_mon = false) và trước đó chưa có hoặc đã đạt
        // thì tạo cảnh báo học vụ và gửi thông báo
        if (!$quaMon && ($quaMonCu === null || $quaMonCu === true)) {
            $this->taoCanhBaoVaThongBao($lhpsv, $diemHe10, $diemChu, $tyLeVang ?? null);
        }

        return true;
    }

    /**
     * Tính điểm TK tạm thời dựa trên điểm đã nhập (không cần đủ tất cả)
     */
    public function tinhDiemTKTamThoi($lopHocPhanSinhVienId)
    {
        $lhpsv = LopHocPhanSinhVien::with('lopHocPhan')->find($lopHocPhanSinhVienId);

        if (!$lhpsv) {
            return null;
        }

        // 1. Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
            ->orderBy('id')
            ->get();

        if ($cauHinhs->isEmpty()) {
            return null;
        }

        $tongDiem = 0;
        $tongTyLe = 0; // Tổng tỷ lệ của các đầu điểm đã có điểm

        // 2. Tính điểm từng đầu (chỉ tính những đầu đã có đủ điểm)
        foreach ($cauHinhs as $cauHinh) {
            // Lấy điểm đã nhập
            $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienId)
                ->where('cau_hinh_id', $cauHinh->id)
                ->orderBy('cot_diem')
                ->get();

            // Chỉ tính nếu đủ cột
            if ($diems->count() >= $cauHinh->so_cot) {
                // Tính trung bình các cột
                $diemTrungBinh = $diems->avg('diem_so');

                // Nhân với tỷ lệ %
                $tongDiem += $diemTrungBinh * ($cauHinh->ty_le / 100);
                $tongTyLe += $cauHinh->ty_le;
            }
        }

        // 3. Nếu không có điểm nào thì trả về null
        if ($tongTyLe == 0) {
            return null;
        }

        // 4. Trả về điểm thực tế đã có (không scale lại)
        // Ví dụ: nếu chỉ có 40% điểm (Chuyên cần 10% + Giữa kỳ 30% = 40%)
        // - Chuyên cần: 10 điểm * 10% = 1.0
        // - Giữa kỳ: 10 điểm * 30% = 3.0
        // - Tổng điểm thực tế: 1.0 + 3.0 = 4.0
        // - Hiển thị: 4.0 (điểm thực tế dựa trên phần trăm đã có)
        // Làm tròn 2 chữ số
        return round($tongDiem, 2);
    }

    /**
     * Tính điểm trung bình học kỳ (GPA)
     */
    public function tinhGPAHocKy($sinhVienId, $hocKyId)
    {
        $ketQuas = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId, $hocKyId) {
            $q->where('sinh_vien_id', $sinhVienId)
                ->whereHas('lopHocPhan', function ($q2) use ($hocKyId) {
                    $q2->where('hoc_ky_id', $hocKyId)
                        ->where('trang_thai_lop', 'da_duyet_diem');
                });
        })
            ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
            ->get();

        if ($ketQuas->isEmpty()) {
return 0;
        }

        $tongDiem = 0;
        $tongTinChi = 0;

        foreach ($ketQuas as $kq) {
            $tinChi = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
            $tongDiem += $kq->diem_he_4 * $tinChi;
            $tongTinChi += $tinChi;
        }

        return $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;
    }

    /**
     * Tính điểm trung bình tích lũy (GPA tích lũy)
     */
    public function tinhGPATichLuy($sinhVienId)
    {
        $ketQuas = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId) {
            $q->where('sinh_vien_id', $sinhVienId)
                ->whereHas('lopHocPhan', function ($q2) {
                    $q2->where('trang_thai_lop', 'da_duyet_diem');
                });
        })
            ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
            ->get();

        if ($ketQuas->isEmpty()) {
            return 0;
        }

        $tongDiem = 0;
        $tongTinChi = 0;

        foreach ($ketQuas as $kq) {
            $tinChi = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
            $tongDiem += $kq->diem_he_4 * $tinChi;
            $tongTinChi += $tinChi;
        }

        return $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;
    }

    /**
     * Tính tổng tín chỉ đạt trong học kỳ
     */
    public function tinhTongTinChiDat($sinhVienId, $hocKyId = null)
    {
        $query = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId, $hocKyId) {
            $q->where('sinh_vien_id', $sinhVienId)
                ->whereHas('lopHocPhan', function ($q2) use ($hocKyId) {
                    $q2->where('trang_thai_lop', 'da_duyet_diem');
                    if ($hocKyId) {
                        $q2->where('hoc_ky_id', $hocKyId);
                    }
                });
        })
            ->where('qua_mon', true)
            ->with('lopHocPhanSinhVien.lopHocPhan.monHoc');

        return $query->get()
            ->sum(function ($kq) {
                return $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
            });
    }

    /**
     * Tính tổng tín chỉ đăng ký trong học kỳ
     */
    public function tinhTongTinChiDangKy($sinhVienId, $hocKyId)
    {
        return LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
            ->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            })
            ->with('lopHocPhan.monHoc')
            ->get()
            ->sum(function ($lhpsv) {
                return $lhpsv->lopHocPhan->monHoc->so_tin_chi;
            });
    }

    /**
     * Tạo hoặc cập nhật bảng điểm học kỳ
     */
    public function capNhatBangDiem($sinhVienId, $hocKyId)
    {
        $tinChiDangKy = $this->tinhTongTinChiDangKy($sinhVienId, $hocKyId);
        $tinChiDat = $this->tinhTongTinChiDat($sinhVienId, $hocKyId);
        $gpaHocKy = $this->tinhGPAHocKy($sinhVienId, $hocKyId);
        
        // Tính điểm TB hệ 10 từ GPA hệ 4
        $diemTBHe10 = $this->chuyenDoiHe4SangHe10($gpaHocKy);
        
        // Xếp loại
        $xepLoai = BangDiem::tinhXepLoai($gpaHocKy, $tinChiDat, $tinChiDangKy);

        BangDiem::updateOrCreate(
            [
                'sinh_vien_id' => $sinhVienId,
                'hoc_ky_id' => $hocKyId,
            ],
            [
                'tong_tin_chi_dang_ky' => $tinChiDangKy,
                'tong_tin_chi_dat' => $tinChiDat,
                'diem_trung_binh_he_10' => $diemTBHe10,
                'diem_trung_binh_he_4' => $gpaHocKy,
                'xep_loai_hoc_tap' => $xepLoai,
            ]
        );

        return true;
    }

    /**
     * Chuyển đổi điểm hệ 10 sang hệ 4
     */
    public function chuyenDoiHe4($diemHe10)
    {
        if ($diemHe10 >= 9.0) return 4.0;
        if ($diemHe10 >= 8.5) return 3.7;
        if ($diemHe10 >= 8.0) return 3.5;
        if ($diemHe10 >= 7.0) return 3.0;
        if ($diemHe10 >= 6.5) return 2.5;
        if ($diemHe10 >= 5.5) return 2.0;
        if ($diemHe10 >= 5.0) return 1.5;
        if ($diemHe10 >= 4.0) return 1.0;
        return 0.0;
    }

    /**
     * Chuyển đổi điểm hệ 4 sang hệ 10 (ước lượng)
     */
    public function chuyenDoiHe4SangHe10($diemHe4)
    {
        if ($diemHe4 >= 3.7) return 9.0;
        if ($diemHe4 >= 3.5) return 8.5;
        if ($diemHe4 >= 3.0) return 8.0;
        if ($diemHe4 >= 2.5) return 7.0;
        if ($diemHe4 >= 2.0) return 6.5;
        if ($diemHe4 >= 1.5) return 5.5;
        if ($diemHe4 >= 1.0) return 5.0;
        return 4.0;
    }

    /**
     * Chuyển đổi điểm sang điểm chữ
     */
    public function chuyenDoiDiemChu($diemHe10)
    {
        if ($diemHe10 >= 9.0) return 'A+';
        if ($diemHe10 >= 8.5) return 'A';
        if ($diemHe10 >= 8.0) return 'B+';
        if ($diemHe10 >= 7.0) return 'B';
        if ($diemHe10 >= 6.5) return 'C+';
        if ($diemHe10 >= 5.5) return 'C';
        if ($diemHe10 >= 5.0) return 'D+';
        if ($diemHe10 >= 4.0) return 'D';
        return 'F';
    }

    /**
     * Xếp loại học tập
     */
    public function xepLoai($diemTB)
    {
        if ($diemTB >= 3.6) return 'xuat_sac';
        if ($diemTB >= 3.2) return 'gioi';
        if ($diemTB >= 2.5) return 'kha';
        if ($diemTB >= 2.0) return 'trung_binh';
        if ($diemTB >= 1.0) return 'yeu';
        return 'kem';
    }

    /**
     * Kiểm tra sinh viên đạt hay không
     */
    public function kiemTraDat($diemHe10)
    {
        return $diemHe10 >= 4.0;
    }

    /**
     * Tạo cảnh báo học vụ và gửi thông báo khi kết quả không đạt
     */
    protected function taoCanhBaoVaThongBao($lopHocPhanSinhVien, $diemHe10, $diemChu, $tyLeVang = null)
    {
        try {
            $sinhVien = $lopHocPhanSinhVien->sinhVien;
            $lopHocPhan = $lopHocPhanSinhVien->lopHocPhan;
            
            if (!$sinhVien || !$lopHocPhan) {
                return;
            }

            $hocKy = $lopHocPhan->hocKy;
            if (!$hocKy) {
                return;
            }

            // Xác định loại cảnh báo và lý do
            $lyDo = [];
            $loaiCanhBao = 'diem_thap';
            
            if ($diemChu === 'F') {
                $lyDo[] = "Điểm chữ: F (Điểm hệ 10: {$diemHe10})";
                $loaiCanhBao = 'diem_thap';
            }
            
            if ($tyLeVang !== null && $tyLeVang > 20) {
                $lyDo[] = "Vắng quá 20% số buổi học (Tỷ lệ vắng: " . number_format($tyLeVang, 1) . "%)";
                if ($loaiCanhBao === 'diem_thap') {
                    $loaiCanhBao = 'diem_thap'; // Giữ nguyên nếu cả điểm thấp và vắng nhiều
                } else {
                    $loaiCanhBao = 'vang_nhieu';
                }
            }

            if (empty($lyDo)) {
                $lyDo[] = "Kết quả học tập không đạt yêu cầu";
            }

            $lyDoString = implode('; ', $lyDo);

            // Xác định mức độ cảnh báo
            $mucDo = 'canh_cao';
            if ($diemHe10 < 2.0 || ($tyLeVang !== null && $tyLeVang > 40)) {
                $mucDo = 'dinh_chi';
            }

            // Kiểm tra xem đã có cảnh báo chưa xử lý cho môn này chưa
            $canhBaoTonTai = CanhBaoHocVu::where('sinh_vien_id', $sinhVien->id)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', $loaiCanhBao)
                ->where('trang_thai', 'chua_xu_ly')
                ->where('ghi_chu', 'like', "%{$lopHocPhan->ma_lop_hp}%")
                ->first();

            if ($canhBaoTonTai) {
                // Cập nhật cảnh báo hiện có
                $canhBaoTonTai->update([
                    'ly_do' => $lyDoString,
                    'muc_do' => $mucDo,
                    'ngay_canh_bao' => now(),
                ]);
                $canhBao = $canhBaoTonTai;
            } else {
                // Tạo cảnh báo mới
                $canhBao = CanhBaoHocVu::create([
                    'sinh_vien_id' => $sinhVien->id,
                    'hoc_ky_id' => $hocKy->id,
                    'loai_canh_bao' => $loaiCanhBao,
                    'muc_do' => $mucDo,
                    'ly_do' => $lyDoString,
                    'ngay_canh_bao' => now(),
                    'trang_thai' => 'chua_xu_ly',
                    'ghi_chu' => "Tự động tạo từ hệ thống. Môn: {$lopHocPhan->monHoc->ten_mon} - Lớp: {$lopHocPhan->ma_lop_hp}",
                ]);
            }

            // Gửi thông báo tự động cho sinh viên
            if ($sinhVien->user_id) {
                $notificationService = new NotificationService();
                
                $noiDung = "⚠️ Cảnh báo: Kết quả học tập không đạt!\n\n";
                $noiDung .= "📚 Môn học: {$lopHocPhan->monHoc->ten_mon}\n";
                $noiDung .= "📋 Lớp học phần: {$lopHocPhan->ma_lop_hp}\n";
                $noiDung .= "📊 Điểm hệ 10: {$diemHe10}\n";
                $noiDung .= "📝 Điểm chữ: {$diemChu}\n\n";
                $noiDung .= "❌ Lý do không đạt:\n";
                $noiDung .= "• " . implode("\n• ", $lyDo) . "\n\n";
                $noiDung .= "💡 Hãy liên hệ với giảng viên hoặc phòng đào tạo để được hỗ trợ.";

                $notificationService->createAutoNotification(
                    loaiThongBao: 'canh_bao_hoc_vu',
                    tieuDe: '⚠️ Cảnh báo: Kết quả học tập không đạt - ' . $lopHocPhan->monHoc->ten_mon,
                    noiDung: $noiDung,
                    nguoiNhanIds: [$sinhVien->user_id],
                    options: [
                        'muc_do_quan_trong' => 'quan_trong',
                        'gui_web_notification' => true,
                        'gui_email' => true,
                    ]
                );

                Log::info('Đã tạo cảnh báo học vụ và gửi thông báo', [
                    'sinh_vien_id' => $sinhVien->id,
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'canh_bao_id' => $canhBao->id,
                    'loai_canh_bao' => $loaiCanhBao,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo cảnh báo học vụ và gửi thông báo: ' . $e->getMessage(), [
                'lop_hoc_phan_sinh_vien_id' => $lopHocPhanSinhVien->id ?? null,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
}