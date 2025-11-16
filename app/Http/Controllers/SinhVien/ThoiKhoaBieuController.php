<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhanSinhVien;
use App\Models\LichHocChiTiet;
use App\Models\HocKy;
use App\Models\HocPhiHocKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ThoiKhoaBieuController extends Controller
{
    /**
     * Hiển thị thời khóa biểu cá nhân
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy học kỳ hiện tại hoặc chọn
        $hocKy = null;
        if ($request->filled('hoc_ky_id')) {
            $hocKy = HocKy::find($request->hoc_ky_id);
        } else {
            $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        }

        if (!$hocKy) {
            $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
            return view('sinhvien.thoi-khoa-bieu.index', [
                'hocKy' => null,
                'hocKys' => $hocKys,
                'message' => 'Không tìm thấy học kỳ hiện tại.'
            ]);
        }

        // ✅ KIỂM TRA HỌC PHÍ - Logic mới
        $hocPhi = HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
            ->where('hoc_ky_id', $hocKy->id)
            ->first();

        $checkResult = $this->kiemTraCoTheXemTKB($sinhVien->id, $hocKy->id);

        if (!$checkResult['co_the_xem']) {
            $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
            return view('sinhvien.thoi-khoa-bieu.index', [
                'hocKy' => $hocKy,
                'hocKys' => $hocKys,
                'coTheXemTKB' => false,
                'hocPhi' => $hocPhi,
                'lyDoKhongXem' => $checkResult['ly_do'],
                'hanXemTKB' => $checkResult['han_xem_tkb'],
                'ngayXepLop' => $checkResult['ngay_xep_lop'],
                'message' => $checkResult['ly_do']
            ]);
        }

        // Lấy các lớp học phần sinh viên đã đăng ký trong học kỳ
        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.lichHocCoDinhs.phongHoc',
                'lopHocPhan.lichHocCoDinhs.giangVien',
            ])
            ->get();

        // Tạo ma trận thời khóa biểu (7 ngày x 12 tiết)
        $thoiKhoaBieu = [];
        for ($thu = 2; $thu <= 8; $thu++) {
            for ($tiet = 1; $tiet <= 12; $tiet++) {
                $thoiKhoaBieu[$thu][$tiet] = null;
            }
        }

        // Điền lịch cố định vào ma trận
        foreach ($lopHocPhanSinhViens as $lopSV) {
            $lopHocPhan = $lopSV->lopHocPhan;

            foreach ($lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                $thuTrongTuan = $lichCoDinh->thu_trong_tuan;
                $tietBatDau = $lichCoDinh->tiet_bat_dau;
                $soTiet = $lichCoDinh->tiet_ket_thuc - $lichCoDinh->tiet_bat_dau + 1;

                $thoiKhoaBieu[$thuTrongTuan][$tietBatDau] = [
                    'mon_hoc' => $lopHocPhan->monHoc->ten_mon,
                    'ma_mon' => $lopHocPhan->monHoc->ma_mon,
                    'phong' => $lichCoDinh->phongHoc->ten_phong ?? 'TBA',
                    'giang_vien' => $lichCoDinh->giangVien->ho_ten ?? 'TBA',
                    'so_tiet' => $soTiet,
                    'gio_bat_dau' => $lichCoDinh->gio_bat_dau,
                    'gio_ket_thuc' => $lichCoDinh->gio_ket_thuc,
                    'loai_lop' => $lopHocPhan->loai_lop,
                ];

                // Đánh dấu các tiết tiếp theo là đã có lịch
                for ($i = 1; $i < $soTiet; $i++) {
                    $thoiKhoaBieu[$thuTrongTuan][$tietBatDau + $i] = 'span';
                }
            }
        }

        // Kiểm tra trùng lịch
        $trungLich = $this->kiemTraTrungLich($lopHocPhanSinhViens);

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('sinhvien.thoi-khoa-bieu.index', compact(
            'hocKy',
            'hocKys',
            'lopHocPhanSinhViens',
            'thoiKhoaBieu',
            'trungLich',
            'sinhVien',
            'hocPhi'
        ))->with('coTheXemTKB', true);
    }

    /**
     * Kiểm tra sinh viên có thể xem TKB không
     * 
     * Logic mới (đúng theo yêu cầu):
     * 1. Sau khi xếp lớp → TKB BỊ ẨN (chưa thể xem ngay)
     * 2. Có 1 tuần để đóng học phí
     * 3. Sau 1 tuần:
     *    - Nếu đã đóng học phí → TKB xuất hiện ✅
     *    - Nếu chưa đóng học phí → TKB vẫn ẩn ❌
     * 4. Nếu đóng bù sau đó → TKB xuất hiện lại ✅
     * 
     * @param int $sinhVienId
     * @param int $hocKyId
     * @return array ['co_the_xem' => bool, 'ly_do' => string, 'han_xem_tkb' => Carbon|null]
     */
    private function kiemTraCoTheXemTKB($sinhVienId, $hocKyId)
    {
        // Lấy thông tin học phí
        $hocPhi = HocPhiHocKy::where('sinh_vien_id', $sinhVienId)
            ->where('hoc_ky_id', $hocKyId)
            ->first();

        // Trường hợp 1: Chưa có học phí (chưa xếp lớp hoặc chưa tính học phí)
        if (!$hocPhi) {
            return [
                'co_the_xem' => false,
                'ly_do' => 'Bạn chưa được xếp lớp hoặc chưa có thông tin học phí. Vui lòng liên hệ phòng đào tạo.',
                'han_xem_tkb' => null,
                'ngay_xep_lop' => null
            ];
        }

        // Lấy ngày xếp lớp gần nhất của sinh viên trong học kỳ
        $ngayXepLop = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
            ->whereHas('lopHocPhan', function($q) use($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->max('ngay_xep_lop');

        if (!$ngayXepLop) {
            return [
                'co_the_xem' => false,
                'ly_do' => 'Bạn chưa được xếp vào lớp học phần nào.',
                'han_xem_tkb' => null,
                'ngay_xep_lop' => null
            ];
        }

        // Tính hạn đóng học phí (1 tuần sau ngày xếp lớp)
        $hanDongHocPhi = Carbon::parse($ngayXepLop)->addWeek();

        // ✅ QUY TẮC CHÍNH: Chỉ xem được TKB khi:
        // 1. Đã qua 1 tuần kể từ ngày xếp lớp VÀ
        // 2. Đã đóng đủ học phí

        // Trường hợp 1: ĐÃ ĐÓNG ĐỦ HỌC PHÍ
        if ($hocPhi->trang_thai == 'da_nop_du') {
            // Kiểm tra đã qua 1 tuần chưa
            if (now()->gte($hanDongHocPhi)) {
                // ✅ Đã đóng học phí VÀ đã qua 1 tuần → XEM ĐƯỢC TKB
                return [
                    'co_the_xem' => true,
                    'ly_do' => '',
                    'han_xem_tkb' => $hanDongHocPhi,
                    'ngay_xep_lop' => Carbon::parse($ngayXepLop)
                ];
            } else {
                // Đã đóng học phí nhưng chưa qua 1 tuần → VẪN CHƯA XEM ĐƯỢC
                $soNgayConLai = now()->diffInDays($hanDongHocPhi, false);
                return [
                    'co_the_xem' => false,
                    'ly_do' => "Bạn đã đóng học phí. Thời khóa biểu sẽ xuất hiện sau {$soNgayConLai} ngày (ngày " . $hanDongHocPhi->format('d/m/Y') . ").",
                    'han_xem_tkb' => $hanDongHocPhi,
                    'ngay_xep_lop' => Carbon::parse($ngayXepLop),
                    'da_dong_hoc_phi' => true
                ];
            }
        }

        // Trường hợp 2: CHƯA ĐÓNG HỌC PHÍ
        if (now()->lt($hanDongHocPhi)) {
            // Chưa qua 1 tuần → Thời gian đóng học phí
            $soNgayConLai = now()->diffInDays($hanDongHocPhi, false);
            return [
                'co_the_xem' => false,
                'ly_do' => "Bạn có {$soNgayConLai} ngày để đóng học phí. Thời khóa biểu sẽ xuất hiện sau khi đóng học phí và qua hạn 1 tuần (ngày " . $hanDongHocPhi->format('d/m/Y') . ").",
                'han_xem_tkb' => $hanDongHocPhi,
                'ngay_xep_lop' => Carbon::parse($ngayXepLop),
                'trong_thoi_gian_dong' => true
            ];
        }

        // Trường hợp 3: Đã qua 1 tuần NHƯNG chưa đóng học phí → KHÔNG XEM ĐƯỢC
        return [
            'co_the_xem' => false,
            'ly_do' => "Bạn đã quá hạn đóng học phí. Vui lòng đóng học phí để xem thời khóa biểu. Hạn đóng: " . $hanDongHocPhi->format('d/m/Y'),
            'han_xem_tkb' => $hanDongHocPhi,
            'ngay_xep_lop' => Carbon::parse($ngayXepLop),
            'qua_han' => true
        ];
    }

    /**
     * Kiểm tra trùng lịch
     */
    private function kiemTraTrungLich($lopHocPhanSinhViens)
    {
        $lichHoc = [];
        $trungLich = [];

        foreach ($lopHocPhanSinhViens as $lopSV) {
            foreach ($lopSV->lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                $key = $lichCoDinh->thu_trong_tuan . '_' . $lichCoDinh->tiet_bat_dau;

                if (isset($lichHoc[$key])) {
                    $trungLich[] = [
                        'thu' => $lichCoDinh->getTenThuAttribute(),
                        'tiet' => $lichCoDinh->tiet_bat_dau,
                        'mon_1' => $lichHoc[$key],
                        'mon_2' => $lopSV->lopHocPhan->monHoc->ten_mon,
                    ];
                } else {
                    $lichHoc[$key] = $lopSV->lopHocPhan->monHoc->ten_mon;
                }
            }
        }

        return $trungLich;
    }

    /**
     * Xem lịch chi tiết theo tuần
     */
    public function chiTiet(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        // Lấy học kỳ
        $hocKyId = $request->get('hoc_ky_id');
        $selectedHocKy = $hocKyId
            ? HocKy::find($hocKyId)
            : HocKy::where('la_hoc_ky_hien_tai', true)->first();

        if (!$selectedHocKy) {
            $selectedHocKy = HocKy::orderBy('nam_hoc', 'desc')->first();
        }

        // Lấy tuần (mặc định tuần 1)
        $tuan = $request->get('tuan', 1);

        // Lấy các lớp học phần của sinh viên trong học kỳ
        $lopHocPhanIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($selectedHocKy) {
                $query->where('hoc_ky_id', $selectedHocKy->id);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->pluck('lop_hoc_phan_id');

        // Tính ngày bắt đầu và kết thúc của tuần
        $startOfWeek = now()->startOfWeek()->addWeeks($tuan - 1);
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Lấy lịch học chi tiết cho tuần này
        $lichHoc = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereBetween('ngay_hoc', [$startOfWeek, $endOfWeek])
            ->with([
                'lopHocPhan.monHoc',
                'giangVien',
                'phongHoc',
            ])
            ->get();

        // Danh sách học kỳ cho bộ lọc
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('sinhvien.thoi-khoa-bieu.chi-tiet', compact(
            'lichHoc',
            'selectedHocKy',
            'tuan',
            'hocKys'
        ));
    }

    /**
     * Xuất PDF thời khóa biểu
     */
    public function exportPDF(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        $hocKy = null;
        if ($request->filled('hoc_ky_id')) {
            $hocKy = HocKy::find($request->hoc_ky_id);
        } else {
            $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        }

        // ✅ KIỂM TRA HỌC PHÍ trước khi export PDF
        $checkResult = $this->kiemTraCoTheXemTKB($sinhVien->id, $hocKy->id);

        if (!$checkResult['co_the_xem']) {
            return redirect()->route('sinh-vien.thoi-khoa-bieu.index')
                ->with('error', $checkResult['ly_do']);
        }

        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.lichHocCoDinhs.phongHoc',
                'lopHocPhan.lichHocCoDinhs.giangVien',
            ])
            ->get();

        // Tạo ma trận thời khóa biểu
        $thoiKhoaBieu = [];
        for ($thu = 2; $thu <= 8; $thu++) {
            for ($tiet = 1; $tiet <= 12; $tiet++) {
                $thoiKhoaBieu[$thu][$tiet] = null;
            }
        }

        foreach ($lopHocPhanSinhViens as $lopSV) {
            foreach ($lopSV->lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                $thuTrongTuan = $lichCoDinh->thu_trong_tuan;
                $tietBatDau = $lichCoDinh->tiet_bat_dau;
                $soTiet = $lichCoDinh->tiet_ket_thuc - $lichCoDinh->tiet_bat_dau + 1;

                $thoiKhoaBieu[$thuTrongTuan][$tietBatDau] = [
                    'mon_hoc' => $lopSV->lopHocPhan->monHoc->ten_mon,
                    'phong' => $lichCoDinh->phongHoc->ten_phong ?? 'TBA',
                    'giang_vien' => $lichCoDinh->giangVien->ho_ten ?? 'TBA',
                    'so_tiet' => $soTiet,
                ];

                for ($i = 1; $i < $soTiet; $i++) {
                    $thoiKhoaBieu[$thuTrongTuan][$tietBatDau + $i] = 'span';
                }
            }
        }

        $pdf = Pdf::loadView('sinhvien.thoi-khoa-bieu.pdf', compact(
            'sinhVien',
            'hocKy',
            'thoiKhoaBieu'
        ));

        return $pdf->download('thoi-khoa-bieu-' . $sinhVien->ma_sinh_vien . '.pdf');
    }
}
