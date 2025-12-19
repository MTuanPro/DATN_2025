<?php

namespace App\Services;

use App\Models\DaoTao\SinhVien;
use App\Models\HocPhiHocKy;
use App\Models\BangDiem;
use App\Models\LopHocPhanSinhVien;
use App\Models\LichHocChiTiet;
use App\Models\LichHocCoDinh;
use App\Models\DiemDanh;
use App\Models\DangKyMonHoc;
use App\Models\ThongBao;
use App\Models\DaoTao\MonHoc;
use App\Models\LopHocPhan;
use App\Models\GiangVien;
use App\Models\DaoTao\HocKy;
use App\Models\CauHinhDauDiem;
use App\Models\KetQuaHocTap;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DaoTao\Nganh;
use App\Models\DaoTao\Khoa;
use App\Models\LichSuDongHocPhi;
use App\Models\CanhBaoHocVu;
// use App\Models\DaoTao\nganh; // Đã xóa lớp hành chính
use App\Models\DaoTao\KhoaHoc;
use App\Models\LichThi;
use App\Models\DaoTao\ChuongTrinhKhung;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotDatabaseService
{
    /**
     * Phát hiện intent và entity từ câu hỏi
     */
    public function detectIntent($message, $sinhVienId)
    {
        $message = mb_strtolower($message);

        // Mapping keywords to intent với độ ưu tiên cao hơn
        $intents = [
            'hoc_phi' => ['học phí', 'hocphi', 'tiền học', 'công nợ', 'nợ học phí', 'đóng tiền', 'thanh toán', 'còn nợ', 'đã đóng', 'biên lai', 'hóa đơn'],
            'diem' => ['điểm', 'điểm số', 'kết quả học tập', 'bảng điểm', 'gpa', 'điểm trung bình', 'điểm môn', 'học lực'],
            'thoi_khoa_bieu' => ['thời khóa biểu', 'tkb', 'lịch học', 'học lúc nào', 'ca học', 'phòng học', 'lịch', 'học hôm nay'],
            'diem_danh' => ['điểm danh', 'vắng', 'nghỉ', 'có mặt', 'phần trăm điểm danh', 'tỷ lệ điểm danh'],
            'dang_ky_mon' => ['đăng ký', 'đăng ký môn', 'môn học', 'đăng ký học phần', 'học phần', 'danh sách môn', 'môn đã đăng ký', 'đã đăng ký', 'môn nào đã đăng ký', 'có thể đăng ký'],
            'thong_bao' => ['thông báo', 'tin tức', 'thông tin'],
            'thong_tin_ca_nhan' => ['thông tin cá nhân', 'hồ sơ', 'mã sinh viên', 'lớp', 'khoa', 'ngành', 'chuyên ngành'],
            'lich_thi' => ['lịch thi', 'thi', 'kỳ thi', 'thi khi nào', 'phòng thi'],
            'giang_vien' => ['giảng viên', 'thầy', 'cô', 'giáo viên'],
            'mon_hoc' => ['môn học nào', 'có môn gì', 'danh sách môn'],
            'canh_bao' => ['cảnh báo', 'cảnh báo học vụ', 'vi phạm'],
            'ket_qua_hoc_tap' => ['kết quả', 'xếp loại', 'học lực', 'hạnh kiểm'],
            'chuong_trinh_dao_tao' => ['chương trình đào tạo', 'ctđt', 'ctdt', 'khung chương trình', 'môn bắt buộc', 'tốt nghiệp', 'tot nghiep', 'điều kiện tốt nghiệp', 'dieu kien tot nghiep', 'môn cần học', 'mon can hoc', 'khung đào tạo'],
            'lich_su_thanh_toan' => ['lịch sử thanh toán', 'đã thanh toán', 'đã đóng'],
        ];

        $detectedIntent = null;
        $confidence = 0;

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    $detectedIntent = $intent;
                    $confidence = 0.9;
                    break 2;
                }
            }
        }

        return [
            'intent' => $detectedIntent,
            'confidence' => $confidence,
            'entities' => $this->extractEntities($message),
        ];
    }

    /**
     * Trích xuất entities từ câu hỏi
     */
    private function extractEntities($message)
    {
        $entities = [];

        // Detect học kỳ
        if (preg_match('/(học kỳ|hk)\s*(\d+)/i', $message, $matches)) {
            $entities['hoc_ky'] = (int) $matches[2];
        }

        // Detect năm học
        if (preg_match('/năm\s*(học)?\s*(\d{4})/i', $message, $matches)) {
            $entities['nam_hoc'] = (int) $matches[2];
        }

        // Detect môn học (tên môn)
        if (preg_match('/(môn|học phần)\s+([a-zA-Z0-9À-ỹ\s]+)/iu', $message, $matches)) {
            $entities['mon_hoc'] = trim($matches[2]);
        }

        // Detect từ khóa thời gian
        $timeKeywords = [
            'hôm nay' => 'today',
            'ngày mai' => 'tomorrow',
            'tuần này' => 'this_week',
            'tháng này' => 'this_month',
        ];

        foreach ($timeKeywords as $keyword => $value) {
            if (str_contains($message, $keyword)) {
                $entities['time'] = $value;
                break;
            }
        }

        return $entities;
    }

    /**
     * Truy vấn thông tin học phí
     */
    public function getHocPhiInfo($sinhVienId, $entities = [])
    {
        try {
            $query = HocPhiHocKy::where('sinh_vien_id', $sinhVienId)
                ->with(['hocKy']);

            // Filter theo học kỳ nếu có
            if (isset($entities['hoc_ky'])) {
                $query->whereHas('hocKy', function ($q) use ($entities) {
                    $q->where('ten_hoc_ky', 'like', '%' . $entities['hoc_ky'] . '%');
                });
            }

            $hocPhis = $query->orderBy('created_at', 'desc')->get();

            if ($hocPhis->isEmpty()) {
                return "Bạn chưa có thông tin học phí nào trong hệ thống.";
            }

            // Tổng hợp thông tin
            $tongHocPhi = $hocPhis->sum('tong_so_tien');
            $daDong = $hocPhis->sum('so_tien_da_dong');
            $conLai = $hocPhis->sum('so_tien_con_lai');

            $response = "📊 **THÔNG TIN HỌC PHÍ CỦA BẠN**\n\n";
            $response .= "💰 Tổng học phí: " . number_format($tongHocPhi, 0, ',', '.') . "đ\n";
            $response .= "✅ Đã đóng: " . number_format($daDong, 0, ',', '.') . "đ\n";
            $response .= "⏳ Còn nợ: " . number_format($conLai, 0, ',', '.') . "đ\n\n";

            if ($conLai > 0) {
                $response .= "⚠️ Bạn còn nợ học phí. Vui lòng thanh toán sớm để tránh ảnh hưởng đến việc đăng ký học và thi.\n\n";
            } else {
                $response .= "✨ Bạn đã hoàn thành việc đóng học phí!\n\n";
            }

            $response .= "📋 Chi tiết theo học kỳ:\n";
            foreach ($hocPhis->take(5) as $hp) {
                $response .= "• {$hp->hocKy->ten_hoc_ky}: ";
                $response .= number_format($hp->tong_so_tien, 0, ',', '.') . "đ ";
                $response .= "({$hp->trang_thai_text})\n";
            }

            // Thêm hướng dẫn nộp học phí
            $response .= "\n💳 **HƯỚNG DẪN THANH TOÁN HỌC PHÍ:**\n\n";
            $response .= "**Cách 1: Thanh toán online qua ZaloPay**\n";
            $response .= "1. Đăng nhập hệ thống S-MIS\n";
            $response .= "2. Vào menu 'Học phí'\n";
            $response .= "3. Chọn 'Thanh toán học phí'\n";
            $response .= "4. Nhập số tiền cần thanh toán\n";
            $response .= "5. Quét mã QR bằng app ZaloPay\n";
            $response .= "6. Xác nhận thanh toán\n\n";

            $response .= "**Cách 2: Chuyển khoản ngân hàng**\n";
            $response .= "• Ngân hàng: [Tên ngân hàng]\n";
            $response .= "• Số tài khoản: [Số TK]\n";
            $response .= "• Chủ tài khoản: Trường...\n";
            $response .= "• Nội dung: [Mã SV] - Học phí - [Học kỳ]\n\n";

            $response .= "**Cách 3: Nộp trực tiếp**\n";
            $response .= "• Địa điểm: Phòng Kế toán\n";
            $response .= "• Thời gian: 8h-11h30, 13h30-17h (T2-T6)\n\n";

            $response .= "⚠️ **Lưu ý:** Sau khi thanh toán, vui lòng giữ biên lai để đối chiếu.";

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getHocPhiInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin học phí. Vui lòng thử lại sau.";
        }
    }

    /**
     * Truy vấn thông tin điểm
     */
    public function getDiemInfo($sinhVienId, $entities = [])
    {
        try {
            // Lấy lớp học phần sinh viên
            $query = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy', 'ketQuaHocTap']);

            // Filter theo học kỳ
            if (isset($entities['hoc_ky'])) {
                $query->whereHas('lopHocPhan.hocKy', function ($q) use ($entities) {
                    $q->where('ten_hoc_ky', 'like', '%' . $entities['hoc_ky'] . '%');
                });
            }

            // Filter theo môn học
            if (isset($entities['mon_hoc'])) {
                $query->whereHas('lopHocPhan.monHoc', function ($q) use ($entities) {
                    $q->where('ten_mon', 'like', '%' . $entities['mon_hoc'] . '%')
                        ->orWhere('ma_mon', 'like', '%' . $entities['mon_hoc'] . '%');
                });
            }

            $lopHocPhanSvs = $query->get();

            if ($lopHocPhanSvs->isEmpty()) {
                return "Bạn chưa có điểm nào được công bố.";
            }

            // Lọc chỉ những môn có kết quả
            $coKetQua = $lopHocPhanSvs->filter(function ($item) {
                return $item->ketQuaHocTap !== null;
            });

            if ($coKetQua->isEmpty()) {
                return "Chưa có kết quả học tập nào được công bố.";
            }

            // Tính GPA
            $tongTinChi = 0;
            $tongDiem = 0;

            foreach ($coKetQua as $item) {
                if ($item->ketQuaHocTap && $item->lopHocPhan->monHoc) {
                    $tinChi = $item->lopHocPhan->monHoc->so_tin_chi ?? 0;
                    $tongTinChi += $tinChi;
                    $tongDiem += ($item->ketQuaHocTap->diem_he_4 ?? 0) * $tinChi;
                }
            }

            $gpa = $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;

            $response = "📊 **KẾT QUẢ HỌC TẬP CỦA BẠN**\n\n";
            $response .= "🎯 GPA: " . number_format($gpa, 2) . "/4.0\n";
            $response .= "📚 Tổng số môn đã có điểm: " . $coKetQua->count() . " môn\n\n";

            $response .= "📋 Chi tiết điểm các môn:\n";
            foreach ($coKetQua->take(10) as $item) {
                $monHoc = $item->lopHocPhan->monHoc;
                $ketQua = $item->ketQuaHocTap;
                if ($monHoc && $ketQua) {
                    $response .= "• {$monHoc->ten_mon}: ";
                    $response .= "Điểm 10: " . number_format($ketQua->diem_he_10 ?? 0, 1) . " | ";
                    $response .= "Điểm 4: " . number_format($ketQua->diem_he_4 ?? 0, 1) . " | ";
                    $response .= "Điểm chữ: {$ketQua->diem_chu}\n";
                }
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getDiemInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin điểm. Vui lòng thử lại sau.";
        }
    }

    /**
     * Truy vấn thời khóa biểu
     */
    public function getThoiKhoaBieuInfo($sinhVienId, $entities = [])
    {
        try {
            $today = now()->format('Y-m-d');
            $time = $entities['time'] ?? 'today';

            // Lấy lớp học phần của sinh viên
            $lopHocPhanIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->pluck('lop_hoc_phan_id');

            if ($lopHocPhanIds->isEmpty()) {
                return "Bạn chưa đăng ký lớp học phần nào.";
            }

            // Lấy lịch học chi tiết
            $query = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
                ->with(['lopHocPhan.monHoc', 'caHoc', 'phongHoc', 'giangVien']);

            // Filter theo thời gian
            switch ($time) {
                case 'today':
                    $query->where('ngay_hoc', $today);
                    break;
                case 'tomorrow':
                    $query->where('ngay_hoc', now()->addDay()->format('Y-m-d'));
                    break;
                case 'this_week':
                    $query->whereBetween('ngay_hoc', [
                        now()->startOfWeek()->format('Y-m-d'),
                        now()->endOfWeek()->format('Y-m-d')
                    ]);
                    break;
            }

            $lichHocs = $query->where('trang_thai', '!=', 'huy')
                ->orderBy('ngay_hoc')
                ->orderBy('tiet_bat_dau')
                ->get();

            if ($lichHocs->isEmpty()) {
                return "Không có lịch học nào trong khoảng thời gian này.";
            }

            $response = "📅 **THỜI KHÓA BIỂU CỦA BẠN**\n\n";

            $groupedByDate = $lichHocs->groupBy('ngay_hoc');
            foreach ($groupedByDate as $date => $items) {
                $response .= "📆 " . date('d/m/Y', strtotime($date)) . " (" . $this->getDayName($date) . "):\n";
                foreach ($items as $lh) {
                    $caHoc = $lh->caHoc;
                    if ($caHoc) {
                        $response .= "  • {$caHoc->ten_ca} (Tiết {$lh->tiet_bat_dau}-{$lh->tiet_ket_thuc})\n";
                    } else {
                        $response .= "  • Tiết {$lh->tiet_bat_dau}-{$lh->tiet_ket_thuc}\n";
                    }
                    $response .= "    📖 {$lh->lopHocPhan->monHoc->ten_mon}\n";
                    if ($lh->phongHoc) {
                        $response .= "    🏫 Phòng: {$lh->phongHoc->ten_phong}\n";
                    }
                    if ($lh->giangVien) {
                        $response .= "    👨‍🏫 GV: {$lh->giangVien->ho_ten}\n";
                    }
                    $response .= "\n";
                }
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getThoiKhoaBieuInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin lịch học. Vui lòng thử lại sau.";
        }
    }

    /**
     * Truy vấn thông tin điểm danh
     */
    public function getDiemDanhInfo($sinhVienId, $entities = [])
    {
        try {
            // Lấy lop_hoc_phan_sinh_vien_ids của sinh viên
            $lopHocPhanSvIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->pluck('id');

            if ($lopHocPhanSvIds->isEmpty()) {
                return "Bạn chưa đăng ký lớp học phần nào.";
            }

            $query = DiemDanh::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSvIds)
                ->with(['lichHocChiTiet.lopHocPhan.monHoc', 'lopHocPhanSinhVien.lopHocPhan.monHoc']);

            // Filter theo môn học
            if (isset($entities['mon_hoc'])) {
                $query->whereHas('lopHocPhanSinhVien.lopHocPhan.monHoc', function ($q) use ($entities) {
                    $q->where('ten_mon', 'like', '%' . $entities['mon_hoc'] . '%')
                        ->orWhere('ma_mon', 'like', '%' . $entities['mon_hoc'] . '%');
                });
            }

            $diemDanhs = $query->orderBy('created_at', 'desc')->get();

            if ($diemDanhs->isEmpty()) {
                return "Bạn chưa có thông tin điểm danh nào.";
            }

            // Thống kê
            $tongBuoi = $diemDanhs->count();
            $coMat = $diemDanhs->where('trang_thai', 'co_mat')->count();
            $vang = $diemDanhs->where('trang_thai', 'vang_co_phep')->count() +
                $diemDanhs->where('trang_thai', 'vang_khong_phep')->count();
            $tiLe = $tongBuoi > 0 ? ($coMat / $tongBuoi) * 100 : 0;

            $response = "📊 **THÔNG TIN ĐIỂM DANH CỦA BẠN**\n\n";
            $response .= "✅ Tổng buổi học: {$tongBuoi}\n";
            $response .= "🟢 Có mặt: {$coMat} buổi\n";
            $response .= "🔴 Vắng: {$vang} buổi\n";
            $response .= "📈 Tỷ lệ tham gia: " . number_format($tiLe, 1) . "%\n\n";

            if ($tiLe < 80) {
                $response .= "⚠️ Cảnh báo: Tỷ lệ điểm danh của bạn dưới 80%. Vui lòng chú ý tham gia lớp học đầy đủ!\n\n";
            }

            // Chi tiết theo môn
            $byMon = $diemDanhs->groupBy('lopHocPhanSinhVien.lopHocPhan.mon_hoc_id');
            $response .= "📋 Chi tiết theo môn:\n";
            foreach ($byMon as $monId => $items) {
                $monHoc = $items->first()->lopHocPhanSinhVien->lopHocPhan->monHoc ?? null;
                if (!$monHoc) continue;

                $totalMon = $items->count();
                $presentMon = $items->where('trang_thai', 'co_mat')->count();
                $rateMon = $totalMon > 0 ? ($presentMon / $totalMon) * 100 : 0;

                $response .= "• {$monHoc->ten_mon}: {$presentMon}/{$totalMon} (" . number_format($rateMon, 1) . "%)\n";
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getDiemDanhInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin điểm danh. Vui lòng thử lại sau.";
        }
    }

    /**
     * Truy vấn thông báo
     */
    public function getThongBaoInfo($sinhVienId, $entities = [])
    {
        try {
            $thongBaos = ThongBao::where(function ($query) use ($sinhVienId) {
                $query->where('doi_tuong', 'sinh_vien')
                    ->orWhere('doi_tuong', 'all');
            })
                ->where('trang_thai', 'cong_khai')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            if ($thongBaos->isEmpty()) {
                return "Hiện tại không có thông báo mới nào.";
            }

            $response = "📢 **THÔNG BÁO MỚI NHẤT**\n\n";

            foreach ($thongBaos as $tb) {
                $response .= "📌 **{$tb->tieu_de}**\n";
                $response .= "📅 {$tb->created_at->format('d/m/Y H:i')}\n";
                $response .= "📝 " . Str::limit(strip_tags($tb->noi_dung), 100) . "\n\n";
            }

            $response .= "💡 Xem chi tiết tại trang Thông báo.";

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getThongBaoInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông báo. Vui lòng thử lại sau.";
        }
    }

    /**
     * Truy vấn thông tin cá nhân sinh viên
     */
    public function getThongTinCaNhanInfo($sinhVienId)
    {
        try {
            $sinhVien = SinhVien::with([
                'user',
                // 'nganh.khoaHoc', // Đã xóa lớp hành chính
                'chuyenNganh.nganh.khoa',
                'nganh.khoa'
            ])->findOrFail($sinhVienId);

            $response = "👤 **THÔNG TIN CÁ NHÂN**\n\n";
            $response .= "📝 Họ và tên: {$sinhVien->ho_ten}\n";
            $response .= "🆔 Mã sinh viên: {$sinhVien->ma_sinh_vien}\n";
            $response .= "📧 Email: {$sinhVien->user->email}\n";
            $response .= "📞 Số điện thoại: {$sinhVien->so_dien_thoai}\n";
            $response .= "🎂 Ngày sinh: " . date('d/m/Y', strtotime($sinhVien->ngay_sinh)) . "\n";
            $response .= "👤 Giới tính: " . ($sinhVien->gioi_tinh == 'nam' ? 'Nam' : 'Nữ') . "\n\n";

            $response .= "🎓 **THÔNG TIN HỌC TẬP**\n\n";
            // $response .= "🏫 Lớp: {$sinhVien->nganh->ten_lop}\n"; // Đã xóa lớp hành chính
            // $response .= "📚 Khóa học: {$sinhVien->nganh->khoaHoc->ten_khoa_hoc}\n"; // Đã xóa lớp hành chính

            if ($sinhVien->chuyenNganh) {
                $response .= "🎯 Chuyên ngành: {$sinhVien->chuyenNganh->ten_chuyen_nganh}\n";
                $response .= "📖 Ngành: {$sinhVien->chuyenNganh->nganh->ten_nganh}\n";
                $response .= "🏛️ Khoa: {$sinhVien->chuyenNganh->nganh->khoa->ten_khoa}\n";
            } elseif ($sinhVien->nganh) {
                $response .= "📖 Ngành: {$sinhVien->nganh->ten_nganh}\n";
                $response .= "🏛️ Khoa: {$sinhVien->nganh->khoa->ten_khoa}\n";
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getThongTinCaNhanInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin cá nhân.";
        }
    }

    /**
     * Truy vấn lịch thi
     */
    public function getLichThiInfo($sinhVienId, $entities = [])
    {
        try {
            // Lấy lớp học phần của sinh viên
            $lopHocPhanIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->pluck('lop_hoc_phan_id');

            if ($lopHocPhanIds->isEmpty()) {
                return "Bạn chưa đăng ký lớp học phần nào.";
            }

            // Query lịch thi
            $lichThis = LichThi::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
                ->with(['lopHocPhan.monHoc', 'phongThi', 'giamThi1', 'giamThi2'])
                ->orderBy('ngay_thi')
                ->orderBy('gio_bat_dau')
                ->get();

            if ($lichThis->isEmpty()) {
                return "📋 **LỊCH THI CỦA BẠN**\n\nℹ️ Hiện chưa có lịch thi nào được công bố.\n\nBạn vui lòng theo dõi thông báo từ nhà trường để biết lịch thi chính thức.\n\n💡 Xem chi tiết tại trang Lịch thi.";
            }

            $response = "📋 **LỊCH THI CỦA BẠN**\n\n";
            $response .= "📊 Tổng số môn thi: {$lichThis->count()}\n\n";

            // Group theo ngày thi
            $groupedByDate = $lichThis->groupBy(function ($item) {
                return $item->ngay_thi->format('Y-m-d');
            });

            foreach ($groupedByDate as $date => $items) {
                $response .= "📅 **" . date('d/m/Y', strtotime($date)) . " (" . $this->getDayName($date) . ")**\n";

                foreach ($items as $lichThi) {
                    $monHoc = $lichThi->lopHocPhan->monHoc ?? null;
                    if (!$monHoc) continue;

                    $response .= "  📖 **{$monHoc->ten_mon}** ({$monHoc->ma_mon})\n";
                    $response .= "  ⏰ Giờ: {$lichThi->gio_bat_dau} - {$lichThi->gio_ket_thuc}\n";

                    if ($lichThi->phongThi) {
                        $response .= "  🏫 Phòng: {$lichThi->phongThi->ten_phong}\n";
                    }

                    $response .= "  📝 Hình thức: {$lichThi->hinh_thuc}\n";

                    if ($lichThi->hinh_thuc == 'online' && $lichThi->link_online) {
                        $response .= "  🔗 Link: {$lichThi->link_online}\n";
                    }

                    if ($lichThi->giamThi1) {
                        $response .= "  👨‍🏫 Giám thị: {$lichThi->giamThi1->ho_ten}";
                        if ($lichThi->giamThi2) {
                            $response .= ", {$lichThi->giamThi2->ho_ten}";
                        }
                        $response .= "\n";
                    }

                    $response .= "\n";
                }
            }

            $response .= "⚠️ **Lưu ý:**\n";
            $response .= "• Mang theo thẻ sinh viên và giấy tờ tùy thân\n";
            $response .= "• Có mặt trước giờ thi 15 phút\n";
            $response .= "• Không mang tài liệu, thiết bị điện tử (trừ khi được phép)\n";
            $response .= "• Kiểm tra kỹ phòng thi và ca thi\n\n";
            $response .= "💡 Chúc bạn thi tốt!";

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getLichThiInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy lịch thi.";
        }
    }

    /**
     * Truy vấn thông tin giảng viên
     */
    public function getGiangVienInfo($sinhVienId, $entities = [])
    {
        try {
            // Lấy giảng viên từ các lớp học phần đang học
            $giangViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->with(['lopHocPhan.giangVien', 'lopHocPhan.monHoc'])
                ->get()
                ->pluck('lopHocPhan')
                ->unique('giang_vien_id');

            if ($giangViens->isEmpty()) {
                return "Bạn chưa có thông tin giảng viên nào.";
            }

            $response = "👨‍🏫 **DANH SÁCH GIẢNG VIÊN**\n\n";

            foreach ($giangViens as $lhp) {
                if ($lhp->giangVien) {
                    $response .= "• **{$lhp->giangVien->ho_ten}**\n";
                    $response .= "  📖 Môn: {$lhp->monHoc->ten_mon}\n";
                    $response .= "  📧 Email: {$lhp->giangVien->email}\n";
                    if ($lhp->giangVien->so_dien_thoai) {
                        $response .= "  📞 SĐT: {$lhp->giangVien->so_dien_thoai}\n";
                    }
                    $response .= "\n";
                }
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getGiangVienInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin giảng viên.";
        }
    }

    /**
     * Truy vấn danh sách môn học
     */
    public function getMonHocInfo($sinhVienId, $entities = [])
    {
        try {
            // Lấy môn học đang học
            $monHocs = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy'])
                ->get()
                ->pluck('lopHocPhan')
                ->sortByDesc('hoc_ky_id');

            if ($monHocs->isEmpty()) {
                return "Bạn chưa đăng ký môn học nào.";
            }

            $response = "📚 **DANH SÁCH MÔN HỌC ĐANG HỌC**\n\n";

            $groupedByHocKy = $monHocs->groupBy('hoc_ky_id');
            foreach ($groupedByHocKy as $hocKyId => $items) {
                $hocKy = $items->first()->hocKy;
                $response .= "📅 **{$hocKy->ten_hoc_ky}**\n";

                foreach ($items as $lhp) {
                    $response .= "  • {$lhp->monHoc->ten_mon} ({$lhp->monHoc->ma_mon})\n";
                    $response .= "    📊 Tín chỉ: {$lhp->monHoc->so_tin_chi}\n";
                    $response .= "    👨‍🏫 GV: {$lhp->giangVien->ho_ten}\n\n";
                }
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getMonHocInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy danh sách môn học.";
        }
    }

    /**
     * Truy vấn cảnh báo học vụ
     */
    public function getCanhBaoInfo($sinhVienId)
    {
        try {
            $canhBaos = CanhBaoHocVu::where('sinh_vien_id', $sinhVienId)
                ->with(['hocKy'])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($canhBaos->isEmpty()) {
                return "✅ Bạn không có cảnh báo học vụ nào. Hãy tiếp tục phấn đấu!";
            }

            $response = "⚠️ **CẢNH BÁO HỌC VỤ**\n\n";
            $response .= "Bạn có {$canhBaos->count()} cảnh báo học vụ:\n\n";

            foreach ($canhBaos as $cb) {
                $response .= "📌 **{$cb->loai_canh_bao_text}**\n";
                $response .= "  📅 Học kỳ: {$cb->hocKy->ten_hoc_ky}\n";
                $response .= "  📝 Lý do: {$cb->ly_do}\n";
                $response .= "  📊 Mức độ: {$cb->muc_do_text}\n";
                $response .= "  📆 Ngày: " . date('d/m/Y', strtotime($cb->created_at)) . "\n\n";
            }

            $response .= "💡 Vui lòng chú ý cải thiện kết quả học tập!";

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getCanhBaoInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin cảnh báo.";
        }
    }

    /**
     * Truy vấn kết quả học tập tổng hợp
     */
    public function getKetQuaHocTapInfo($sinhVienId)
    {
        try {
            // Lấy kết quả học tập từ KetQuaHocTap
            $lopHocPhanSvIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->pluck('id');

            if ($lopHocPhanSvIds->isEmpty()) {
                return "Bạn chưa đăng ký môn học nào.";
            }

            $ketQuaHocTaps = KetQuaHocTap::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSvIds)
                ->with(['lopHocPhanSinhVien.lopHocPhan.monHoc', 'lopHocPhanSinhVien.lopHocPhan.hocKy'])
                ->get();

            if ($ketQuaHocTaps->isEmpty()) {
                return "Chưa có kết quả học tập nào được công bố.";
            }

            // Tính GPA tích lũy
            $diemQuaMon = $ketQuaHocTaps->where('qua_mon', true);
            $tongTinChi = 0;
            $tongDiem = 0;

            foreach ($diemQuaMon as $ketQua) {
                $monHoc = $ketQua->lopHocPhanSinhVien->lopHocPhan->monHoc ?? null;
                if ($monHoc) {
                    $tinChi = $monHoc->so_tin_chi ?? 0;
                    $tongTinChi += $tinChi;
                    $tongDiem += ($ketQua->diem_he_4 ?? 0) * $tinChi;
                }
            }

            $gpaTichLuy = $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0;

            // Xếp loại học lực
            $xepLoai = 'Yếu';
            if ($gpaTichLuy >= 3.6) $xepLoai = 'Xuất sắc';
            elseif ($gpaTichLuy >= 3.2) $xepLoai = 'Giỏi';
            elseif ($gpaTichLuy >= 2.5) $xepLoai = 'Khá';
            elseif ($gpaTichLuy >= 2.0) $xepLoai = 'Trung bình';

            $response = "🎓 **KẾT QUẢ HỌC TẬP TỔNG HỢP**\n\n";
            $response .= "📊 **Kết quả tích lũy**\n";
            $response .= "• GPA: " . number_format($gpaTichLuy, 2) . "/4.0\n";
            $response .= "• Tín chỉ tích lũy: {$tongTinChi}\n";
            $response .= "• Xếp loại: {$xepLoai}\n";
            $response .= "• Tổng môn đã học: {$ketQuaHocTaps->count()}\n";
            $response .= "• Môn qua: {$diemQuaMon->count()}\n";
            $response .= "• Môn không qua: " . ($ketQuaHocTaps->count() - $diemQuaMon->count()) . "\n\n";

            // Thống kê theo học kỳ
            $groupedByHocKy = $ketQuaHocTaps->groupBy(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->hoc_ky_id ?? null;
            })->filter(function ($items, $key) {
                return $key !== null;
            });

            $response .= "📈 **Theo từng học kỳ**:\n";
            foreach ($groupedByHocKy->take(5) as $hocKyId => $items) {
                $hocKy = $items->first()->lopHocPhanSinhVien->lopHocPhan->hocKy ?? null;
                if (!$hocKy) continue;

                $tcHK = 0;
                $diemHK = 0;
                foreach ($items->where('qua_mon', true) as $ketQua) {
                    $monHoc = $ketQua->lopHocPhanSinhVien->lopHocPhan->monHoc ?? null;
                    if ($monHoc) {
                        $tc = $monHoc->so_tin_chi ?? 0;
                        $tcHK += $tc;
                        $diemHK += ($ketQua->diem_he_4 ?? 0) * $tc;
                    }
                }

                $gpaHK = $tcHK > 0 ? round($diemHK / $tcHK, 2) : 0;
                $response .= "• {$hocKy->ten_hoc_ky}: GPA " . number_format($gpaHK, 2) . " ({$items->count()} môn)\n";
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getKetQuaHocTapInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy kết quả học tập.";
        }
    }

    /**
     * Truy vấn lịch sử thanh toán
     */
    public function getLichSuThanhToanInfo($sinhVienId, $entities = [])
    {
        try {
            $lichSu = LichSuDongHocPhi::whereHas('hocPhiHocKy', function ($q) use ($sinhVienId) {
                $q->where('sinh_vien_id', $sinhVienId);
            })
                ->with(['hocPhiHocKy.hocKy'])
                ->orderBy('ngay_dong', 'desc')
                ->get();

            if ($lichSu->isEmpty()) {
                return "Bạn chưa có lịch sử thanh toán học phí nào.";
            }

            $tongDaDong = $lichSu->sum('so_tien_dong');

            $response = "💳 **LỊCH SỬ THANH TOÁN HỌC PHÍ**\n\n";
            $response .= "💰 Tổng đã thanh toán: " . number_format($tongDaDong, 0, ',', '.') . "đ\n";
            $response .= "📊 Số lần thanh toán: {$lichSu->count()}\n\n";

            $response .= "📋 Chi tiết các lần thanh toán:\n";
            foreach ($lichSu->take(10) as $ls) {
                $response .= "• " . date('d/m/Y H:i', strtotime($ls->ngay_dong)) . "\n";
                $response .= "  💵 Số tiền: " . number_format($ls->so_tien_dong, 0, ',', '.') . "đ\n";
                $response .= "  📚 Học kỳ: {$ls->hocPhiHocKy->hocKy->ten_hoc_ky}\n";
                $response .= "  💳 Phương thức: {$ls->phuong_thuc_thanh_toan}\n";
                $response .= "  🔖 Mã GD: {$ls->ma_giao_dich}\n\n";
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getLichSuThanhToanInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy lịch sử thanh toán.";
        }
    }

    /**
     * Truy vấn chương trình đào tạo và điều kiện tốt nghiệp
     */
    public function getChuongTrinhDaoTaoInfo($sinhVienId)
    {
        try {
            $sinhVien = SinhVien::with([
                'chuyenNganh.nganh',
                'nganh'
            ])->findOrFail($sinhVienId);

            $chuyenNganhId = $sinhVien->chuyen_nganh_id;

            if (!$chuyenNganhId) {
                return "Bạn chưa được phân bổ chuyên ngành. Vui lòng liên hệ phòng Đào tạo.";
            }

            // Lấy chương trình khung
            $chuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $chuyenNganhId)
                ->with(['monHoc'])
                ->orderBy('hoc_ky_goi_y')
                ->orderBy('thu_tu_hoc')
                ->get();

            if ($chuongTrinhKhung->isEmpty()) {
                return "Chưa có chương trình đào tạo cho chuyên ngành của bạn.";
            }

            // Tính toán thống kê
            $tongTinChi = $chuongTrinhKhung->sum(function ($item) {
                return $item->monHoc->so_tin_chi ?? 0;
            });

            $monBatBuoc = $chuongTrinhKhung->where('bat_buoc', true);
            $tinChiBatBuoc = $monBatBuoc->sum(function ($item) {
                return $item->monHoc->so_tin_chi ?? 0;
            });

            $monTuChon = $chuongTrinhKhung->where('bat_buoc', false);
            $tinChiTuChon = $monTuChon->sum(function ($item) {
                return $item->monHoc->so_tin_chi ?? 0;
            });

            // Tính GPA và tín chỉ đã học từ KetQuaHocTap
            $lopHocPhanSvIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->pluck('id');

            $ketQuaHocTaps = KetQuaHocTap::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSvIds)
                ->where('qua_mon', true)
                ->with(['lopHocPhanSinhVien.lopHocPhan.monHoc'])
                ->get();

            $tinChiDaHoc = 0;
            $tongDiem = 0;

            foreach ($ketQuaHocTaps as $ketQua) {
                $monHoc = $ketQua->lopHocPhanSinhVien->lopHocPhan->monHoc ?? null;
                if ($monHoc) {
                    $tinChi = $monHoc->so_tin_chi ?? 0;
                    $tinChiDaHoc += $tinChi;
                    $tongDiem += ($ketQua->diem_he_4 ?? 0) * $tinChi;
                }
            }

            $gpa = $tinChiDaHoc > 0 ? round($tongDiem / $tinChiDaHoc, 2) : 0;

            $response = "🎓 **CHƯƠNG TRÌNH ĐÀO TẠO**\n\n";
            $response .= "📚 **Tổng quan:**\n";
            $response .= "• Chuyên ngành: {$sinhVien->chuyenNganh->ten_chuyen_nganh}\n";
            $response .= "• Tổng số tín chỉ: {$tongTinChi}\n";
            $response .= "• Môn bắt buộc: {$monBatBuoc->count()} môn ({$tinChiBatBuoc} TC)\n";
            $response .= "• Môn tự chọn: {$monTuChon->count()} môn ({$tinChiTuChon} TC)\n\n";

            $response .= "📊 **Tiến độ học tập:**\n";
            $response .= "• Đã tích lũy: {$tinChiDaHoc}/{$tongTinChi} TC\n";
            $response .= "• GPA hiện tại: " . number_format($gpa, 2) . "/4.0\n";
            $tienDo = $tongTinChi > 0 ? round(($tinChiDaHoc / $tongTinChi) * 100, 1) : 0;
            $response .= "• Hoàn thành: {$tienDo}%\n\n";

            $response .= "✅ **Điều kiện tốt nghiệp:**\n";
            $response .= "• Hoàn thành {$tongTinChi} tín chỉ\n";
            $response .= "• GPA ≥ 2.0/4.0\n";
            $response .= "• Hoàn thành đầy đủ môn bắt buộc\n";
            $response .= "• Không nợ học phí\n";
            $response .= "• Không vi phạm kỷ luật mức đình chỉ trở lên\n\n";

            // Kiểm tra điều kiện
            $dauTotNghiep = true;
            $lyDoKhongDu = [];

            if ($tinChiDaHoc < $tongTinChi) {
                $dauTotNghiep = false;
                $conLai = $tongTinChi - $tinChiDaHoc;
                $lyDoKhongDu[] = "Còn thiếu {$conLai} tín chỉ";
            }

            if ($gpa < 2.0) {
                $dauTotNghiep = false;
                $lyDoKhongDu[] = "GPA chưa đạt 2.0";
            }

            if ($dauTotNghiep) {
                $response .= "🎉 **Chúc mừng! Bạn đã đủ điều kiện xét tốt nghiệp!**\n";
            } else {
                $response .= "⚠️ **Bạn chưa đủ điều kiện tốt nghiệp:**\n";
                foreach ($lyDoKhongDu as $lyDo) {
                    $response .= "• {$lyDo}\n";
                }
            }

            $response .= "\n📖 **Chi tiết chương trình theo học kỳ:**\n";
            $groupedByHocKy = $chuongTrinhKhung->groupBy('hoc_ky_goi_y');

            foreach ($groupedByHocKy as $hocKy => $items) {
                $response .= "\n**Học kỳ {$hocKy}:**\n";
                foreach ($items as $item) {
                    $icon = $item->bat_buoc ? '⭐' : '◽';
                    $loai = $item->bat_buoc ? 'Bắt buộc' : 'Tự chọn';
                    $response .= "{$icon} {$item->monHoc->ten_mon} ({$item->monHoc->so_tin_chi} TC) - {$loai}\n";
                }
            }

            $response .= "\n💡 Xem chi tiết tại trang Chương trình đào tạo.";

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getChuongTrinhDaoTaoInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin chương trình đào tạo.";
        }
    }

    /**
     * Truy vấn thông tin đăng ký môn học
     */
    public function getDangKyMonInfo($sinhVienId, $entities = [])
    {
        try {
            // Lấy danh sách môn đã đăng ký
            $dangKyMons = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                ->with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy', 'lopHocPhan.giangVien'])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($dangKyMons->isEmpty()) {
                return "Bạn chưa đăng ký môn học nào.\n\n📝 **Hướng dẫn đăng ký:**\n1. Đăng nhập hệ thống S-MIS\n2. Vào menu 'Đăng ký học phần'\n3. Chọn học kỳ cần đăng ký\n4. Chọn các môn học muốn đăng ký\n5. Kiểm tra lịch học và xác nhận đăng ký\n\n⚠️ Lưu ý: Chỉ đăng ký được trong thời gian mở đăng ký học phần.";
            }

            // Group theo học kỳ
            $groupedByHocKy = $dangKyMons->groupBy(function ($item) {
                return $item->lopHocPhan->hoc_ky_id ?? null;
            })->filter(function ($items, $key) {
                return $key !== null;
            });

            $response = "📚 **DANH SÁCH MÔN HỌC ĐÃ ĐĂNG KÝ**\n\n";
            $response .= "📊 Tổng số môn đã đăng ký: {$dangKyMons->count()} môn\n\n";

            foreach ($groupedByHocKy as $hocKyId => $items) {
                $hocKy = $items->first()->lopHocPhan->hocKy ?? null;
                if (!$hocKy) continue;

                $tongTinChi = 0;
                foreach ($items as $item) {
                    $tongTinChi += $item->lopHocPhan->monHoc->so_tin_chi ?? 0;
                }

                $response .= "📅 **{$hocKy->ten_hoc_ky}** ({$items->count()} môn - {$tongTinChi} TC)\n";

                foreach ($items as $item) {
                    $monHoc = $item->lopHocPhan->monHoc;
                    $giangVien = $item->lopHocPhan->giangVien;

                    if ($monHoc) {
                        $response .= "  • {$monHoc->ten_mon} ({$monHoc->ma_mon})\n";
                        $response .= "    📊 {$monHoc->so_tin_chi} tín chỉ\n";
                        if ($giangVien) {
                            $response .= "    👨‍🏫 GV: {$giangVien->ho_ten}\n";
                        }
                    }
                }
                $response .= "\n";
            }

            $response .= "💡 **Hướng dẫn đăng ký học phần:**\n";
            $response .= "1. Đăng nhập vào hệ thống S-MIS\n";
            $response .= "2. Vào menu 'Đăng ký học phần'\n";
            $response .= "3. Chọn học kỳ cần đăng ký\n";
            $response .= "4. Chọn các môn học muốn đăng ký\n";
            $response .= "5. Kiểm tra lịch học và xác nhận đăng ký\n\n";
            $response .= "⚠️ Lưu ý: Chỉ đăng ký được trong thời gian mở đăng ký học phần.";

            return $response;
        } catch (\Exception $e) {
            Log::error('ChatbotDatabaseService - getDangKyMonInfo error: ' . $e->getMessage());
            return "Xin lỗi, có lỗi xảy ra khi lấy thông tin đăng ký môn học.";
        }
    }

    /**
     * Query database dựa trên intent
     */
    public function queryDatabase($intent, $sinhVienId, $entities = [])
    {
        switch ($intent) {
            case 'hoc_phi':
                return $this->getHocPhiInfo($sinhVienId, $entities);

            case 'diem':
                return $this->getDiemInfo($sinhVienId, $entities);

            case 'thoi_khoa_bieu':
                return $this->getThoiKhoaBieuInfo($sinhVienId, $entities);

            case 'diem_danh':
                return $this->getDiemDanhInfo($sinhVienId, $entities);

            case 'thong_bao':
                return $this->getThongBaoInfo($sinhVienId, $entities);

            case 'dang_ky_mon':
                return $this->getDangKyMonInfo($sinhVienId, $entities);

            case 'thong_tin_ca_nhan':
                return $this->getThongTinCaNhanInfo($sinhVienId);

            case 'lich_thi':
                return $this->getLichThiInfo($sinhVienId, $entities);

            case 'giang_vien':
                return $this->getGiangVienInfo($sinhVienId, $entities);

            case 'mon_hoc':
                return $this->getMonHocInfo($sinhVienId, $entities);

            case 'canh_bao':
                return $this->getCanhBaoInfo($sinhVienId);

            case 'ket_qua_hoc_tap':
                return $this->getKetQuaHocTapInfo($sinhVienId);

            case 'lich_su_thanh_toan':
                return $this->getLichSuThanhToanInfo($sinhVienId, $entities);

            case 'chuong_trinh_dao_tao':
                return $this->getChuongTrinhDaoTaoInfo($sinhVienId);

            default:
                return null;
        }
    }

    /**
     * Helper: Get day name in Vietnamese
     */
    private function getDayName($date)
    {
        $days = [
            'Monday' => 'Thứ 2',
            'Tuesday' => 'Thứ 3',
            'Wednesday' => 'Thứ 4',
            'Thursday' => 'Thứ 5',
            'Friday' => 'Thứ 6',
            'Saturday' => 'Thứ 7',
            'Sunday' => 'Chủ nhật',
        ];

        return $days[date('l', strtotime($date))] ?? '';
    }
}
