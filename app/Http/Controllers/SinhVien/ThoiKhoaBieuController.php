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
                'message' => 'Không tìm thấy học kỳ hiện tại.',
                'lopHocPhanSinhViens' => collect(), // Truyền collection rỗng
                'thoiKhoaBieu' => [],
                'trungLich' => [],
                'sinhVien' => $sinhVien,
                'coTheXemTKB' => false,
                'viewMode' => $request->get('view_mode', 'co_dinh'),
                'thoiGianFilter' => $request->get('thoi_gian', null),
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
                'message' => $checkResult['ly_do'],
                'lopHocPhanSinhViens' => collect(), // Truyền collection rỗng
                'thoiKhoaBieu' => [],
                'trungLich' => [],
                'sinhVien' => $sinhVien,
                'viewMode' => $request->get('view_mode', 'co_dinh'),
                'thoiGianFilter' => $request->get('thoi_gian', null),
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
                'lopHocPhan.lichHocCoDinhs' => function ($query) {
                    $query->orderBy('thu_trong_tuan')->orderBy('tiet_bat_dau');
                },
                'lopHocPhan.lichHocCoDinhs.phongHoc',
                'lopHocPhan.lichHocCoDinhs.giangVien',
                'lopHocPhan.lichHocCoDinhs.caHoc',
            ])
            ->get();

        // Debug: Kiểm tra đăng ký tạm (chưa xếp lớp)
        $dangKyTam = \App\Models\DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
            ->where('hoc_ky_id', $hocKy->id)
            ->where('trang_thai', 'cho_xep_lop')
            ->count();

        // Debug: Kiểm tra tổng số lớp học phần sinh viên đã đăng ký (tất cả trạng thái)
        $tongLopDangKy = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->count();

        // Debug: Lấy tất cả các lớp học phần (tất cả trạng thái) để debug
        $tatCaLopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.lichHocCoDinhs',
            ])
            ->get();

        // Debug: Thống kê chi tiết
        $debugChiTiet = [];
        foreach ($tatCaLopHocPhanSinhViens as $lopSV) {
            $soLichCoDinh = $lopSV->lopHocPhan->lichHocCoDinhs->count();
            $debugChiTiet[] = [
                'ma_lop_hp' => $lopSV->lopHocPhan->ma_lop_hp ?? 'N/A',
                'ten_mon' => $lopSV->lopHocPhan->monHoc->ten_mon ?? 'N/A',
                'trang_thai' => $lopSV->trang_thai,
                'so_lich_co_dinh' => $soLichCoDinh,
                'co_lich' => $soLichCoDinh > 0,
            ];
        }

        // Kiểm tra chế độ xem: 'co_dinh' (lịch cố định), 'full' (toàn bộ học kỳ), hoặc filter thời gian
        $viewMode = $request->get('view_mode', 'co_dinh');
        $thoiGianFilter = $request->get('thoi_gian', null);
        
        // Lấy danh sách ca học đang hoạt động
        $caHocs = \App\Models\CaHoc::where('trang_thai', true)
            ->orderBy('thu_tu')
            ->get();

        // Tạo ma trận thời khóa biểu theo ca học (7 ngày x số ca học)
        $thoiKhoaBieu = [];
        for ($thu = 2; $thu <= 8; $thu++) {
            foreach ($caHocs as $caHoc) {
                $thoiKhoaBieu[$thu][$caHoc->id] = null;
            }
        }

        // Tính toán khoảng thời gian nếu có filter thời gian
        $startDate = null;
        $endDate = null;
        if ($thoiGianFilter) {
            $now = Carbon::now();
            switch ($thoiGianFilter) {
                case '7_ngay_toi':
                    $startDate = $now->copy();
                    $endDate = $now->copy()->addDays(7);
                    break;
                case '14_ngay_toi':
                    $startDate = $now->copy();
                    $endDate = $now->copy()->addDays(14);
                    break;
                case '30_ngay_toi':
                    $startDate = $now->copy();
                    $endDate = $now->copy()->addDays(30);
                    break;
                case '60_ngay_toi':
                    $startDate = $now->copy();
                    $endDate = $now->copy()->addDays(60);
                    break;
                case '90_ngay_toi':
                    $startDate = $now->copy();
                    $endDate = $now->copy()->addDays(90);
                    break;
                case '7_ngay_truoc':
                    $startDate = $now->copy()->subDays(7);
                    $endDate = $now->copy();
                    break;
                case '14_ngay_truoc':
                    $startDate = $now->copy()->subDays(14);
                    $endDate = $now->copy();
                    break;
                case '30_ngay_truoc':
                    $startDate = $now->copy()->subDays(30);
                    $endDate = $now->copy();
                    break;
                case '60_ngay_truoc':
                    $startDate = $now->copy()->subDays(60);
                    $endDate = $now->copy();
                    break;
                case '90_ngay_truoc':
                    $startDate = $now->copy()->subDays(90);
                    $endDate = $now->copy();
                    break;
            }
        }

        // Lấy lịch học chi tiết
        $lichHocChiTietFull = collect();
        $lopHocPhanIds = $lopHocPhanSinhViens->pluck('lop_hoc_phan_id');
        $thoiKhoaBieuTheoNgay = []; // Ma trận theo ngày và ca học
        
        if ($viewMode === 'full' || $thoiGianFilter) {
            $query = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
                ->where('trang_thai', '!=', 'huy');
            
            if ($thoiGianFilter && $startDate && $endDate) {
                // Nếu có filter thời gian, lấy trong khoảng đó
                $query->whereBetween('ngay_hoc', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            } elseif ($viewMode === 'full') {
                // Nếu không có filter, lấy toàn bộ học kỳ
                $query->whereBetween('ngay_hoc', [$hocKy->ngay_bat_dau, $hocKy->ngay_ket_thuc]);
            }
            
            $lichHocChiTietFull = $query->with([
                    'lopHocPhan.monHoc',
                    'giangVien',
                    'phongHoc',
                    'caHoc',
                ])
                ->orderBy('ngay_hoc')
                ->orderBy('gio_bat_dau')
                ->get();
            
            // Tạo ma trận theo ngày và ca học (cho chế độ hiển thị theo ngày)
            foreach ($lichHocChiTietFull as $lich) {
                $ngayHoc = Carbon::parse($lich->ngay_hoc)->format('Y-m-d');
                $caHocId = $lich->ca_hoc_id;
                if (!$caHocId && $lich->caHoc) {
                    $caHocId = $lich->caHoc->id;
                }
                
                if (!$caHocId) {
                    continue;
                }
                
                if (!isset($thoiKhoaBieuTheoNgay[$ngayHoc])) {
                    $thoiKhoaBieuTheoNgay[$ngayHoc] = [];
                }
                
                if (!isset($thoiKhoaBieuTheoNgay[$ngayHoc][$caHocId])) {
                    $thoiKhoaBieuTheoNgay[$ngayHoc][$caHocId] = [];
                }
                
                $thoiKhoaBieuTheoNgay[$ngayHoc][$caHocId][] = $lich;
            }
        }

        // Điền lịch vào ma trận
        if ($viewMode === 'co_dinh' && !$thoiGianFilter) {
            // Lịch cố định (lặp lại theo tuần) - chỉ khi không có filter thời gian
            foreach ($lopHocPhanSinhViens as $lopSV) {
                $lopHocPhan = $lopSV->lopHocPhan;

                // Kiểm tra xem lớp có lịch học cố định chưa
                if ($lopHocPhan->lichHocCoDinhs->isEmpty()) {
                    continue;
                }

                foreach ($lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                    $thuTrongTuan = $lichCoDinh->thu_trong_tuan;
                    
                    // Lấy ca học từ lịch cố định
                    if (!$lichCoDinh->ca_hoc_id) {
                        continue;
                    }
                    
                    $caHocId = $lichCoDinh->ca_hoc_id;
                    $caHoc = $lichCoDinh->caHoc;
                    
                    if (!$caHoc) {
                        continue;
                    }

                    // Kiểm tra xem môn học có tồn tại không (có thể đã bị xóa)
                    if (!$lopHocPhan->monHoc) {
                        continue;
                    }

                    $thoiKhoaBieu[$thuTrongTuan][$caHocId] = [
                        'mon_hoc' => $lopHocPhan->monHoc->ten_mon ?? 'N/A',
                        'ma_mon' => $lopHocPhan->monHoc->ma_mon ?? 'N/A',
                        'phong' => $lichCoDinh->phongHoc->ten_phong ?? 'TBA',
                        'giang_vien' => $lichCoDinh->giangVien->ho_ten ?? 'TBA',
                        'so_tiet' => $lichCoDinh->tiet_ket_thuc - $lichCoDinh->tiet_bat_dau + 1,
                        'gio_bat_dau' => $lichCoDinh->gio_bat_dau,
                        'gio_ket_thuc' => $lichCoDinh->gio_ket_thuc,
                        'loai_lop' => $lopHocPhan->loai_lop ?? null,
                        'ca_hoc' => $caHoc,
                        'ca_hoc_id' => $caHocId,
                    ];
                }
            }
        } else {
            // Lịch đầy đủ hoặc có filter thời gian: Nhóm theo thứ trong tuần và ca học
            foreach ($lichHocChiTietFull as $lich) {
                $ngayHoc = Carbon::parse($lich->ngay_hoc);
                $thuTrongTuan = $ngayHoc->dayOfWeek; // 0 = CN, 1 = T2, ..., 6 = T7
                $thuTrongTuan = $thuTrongTuan == 0 ? 8 : $thuTrongTuan + 1; // Chuyển sang hệ 2-8
                
                $caHocId = $lich->ca_hoc_id;
                if (!$caHocId && $lich->caHoc) {
                    $caHocId = $lich->caHoc->id;
                }
                
                if (!$caHocId) {
                    continue;
                }

                // Nhóm các lịch học cùng thứ và ca lại với nhau
                $key = $thuTrongTuan . '_' . $caHocId;
                if (!isset($thoiKhoaBieu[$thuTrongTuan][$caHocId])) {
                    $lopHocPhan = $lich->lopHocPhan;
                    
                    // Kiểm tra xem môn học có tồn tại không (có thể đã bị xóa)
                    if (!$lopHocPhan || !$lopHocPhan->monHoc) {
                        continue;
                    }
                    
                    $thoiKhoaBieu[$thuTrongTuan][$caHocId] = [
                        'mon_hoc' => $lopHocPhan->monHoc->ten_mon ?? 'N/A',
                        'ma_mon' => $lopHocPhan->monHoc->ma_mon ?? 'N/A',
                        'phong' => $lich->phongHoc->ten_phong ?? 'TBA',
                        'giang_vien' => $lich->giangVien->ho_ten ?? 'TBA',
                        'so_tiet' => $lich->so_tiet ?? 1,
                        'gio_bat_dau' => $lich->gio_bat_dau ?? $lich->caHoc->gio_bat_dau ?? null,
                        'gio_ket_thuc' => $lich->gio_ket_thuc ?? $lich->caHoc->gio_ket_thuc ?? null,
                        'loai_lop' => $lopHocPhan->loai_lop ?? null,
                        'ca_hoc' => $lich->caHoc,
                        'ca_hoc_id' => $caHocId,
                        'lich_list' => [], // Danh sách các lịch học trong thứ và ca này
                        'is_full' => true, // Đánh dấu là lịch đầy đủ
                    ];
                }
                // Thêm lịch vào danh sách
                $thoiKhoaBieu[$thuTrongTuan][$caHocId]['lich_list'][] = $lich;
            }
        }

        // Kiểm tra trùng lịch
        $trungLich = $this->kiemTraTrungLich($lopHocPhanSinhViens);

        // Kiểm tra lớp nào chưa có lịch học cố định
        $lopChuaCoLich = [];
        foreach ($lopHocPhanSinhViens as $lopSV) {
            if ($lopSV->lopHocPhan->lichHocCoDinhs->isEmpty()) {
                $tenMon = $lopSV->lopHocPhan->monHoc->ten_mon ?? 'Môn học đã bị xóa';
                $lopChuaCoLich[] = $lopSV->lopHocPhan->ma_lop_hp . ' - ' . $tenMon;
            }
        }

        // Kiểm tra lớp có lịch nhưng trạng thái không đúng (không được hiển thị)
        $lopCoLichNhungTrangThaiSai = [];
        foreach ($tatCaLopHocPhanSinhViens as $lopSV) {
            $soLichCoDinh = $lopSV->lopHocPhan->lichHocCoDinhs->count();
            if ($soLichCoDinh > 0 && !in_array($lopSV->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
                $lopCoLichNhungTrangThaiSai[] = [
                    'ma_lop_hp' => $lopSV->lopHocPhan->ma_lop_hp ?? 'N/A',
                    'ten_mon' => $lopSV->lopHocPhan->monHoc->ten_mon ?? 'N/A',
                    'trang_thai' => $lopSV->trang_thai,
                    'so_lich_co_dinh' => $soLichCoDinh,
                ];
            }
        }

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Debug info
        $debugInfo = [
            'tong_lop_da_xep' => $lopHocPhanSinhViens->count(),
            'tong_lop_dang_ky' => $tongLopDangKy,
            'dang_ky_tam_cho_xep' => $dangKyTam,
            'lop_co_lich' => $lopHocPhanSinhViens->count() - count($lopChuaCoLich),
            'chi_tiet' => $debugChiTiet,
            'hoc_phi_trang_thai' => $hocPhi->trang_thai ?? 'chua_co',
        ];

        return view('sinhvien.thoi-khoa-bieu.index', compact(
            'hocKy',
            'hocKys',
            'lopHocPhanSinhViens',
            'thoiKhoaBieu',
            'trungLich',
            'sinhVien',
            'hocPhi',
            'lopChuaCoLich',
            'lopCoLichNhungTrangThaiSai',
            'debugInfo',
            'dangKyTam',
            'caHocs',
            'viewMode',
            'lichHocChiTietFull',
            'thoiGianFilter',
            'startDate',
            'endDate',
            'thoiKhoaBieuTheoNgay'
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

        // Tính hạn đóng học phí (2 tuần sau ngày xếp lớp)
        $hanDongHocPhi = Carbon::parse($ngayXepLop)->addWeeks(2);

        // ✅ QUY TẮC CHÍNH: 
        // 1. Nếu đã đóng đủ học phí → XEM ĐƯỢC TKB NGAY (không cần chờ)
        // 2. Nếu chưa đóng học phí → Có 2 tuần để đóng, sau đó mới được xem

        // Trường hợp 1: ĐÃ ĐÓNG ĐỦ HỌC PHÍ → XEM ĐƯỢC TKB NGAY
        if ($hocPhi->trang_thai == 'da_nop_du') {
            // ✅ Đã đóng đủ học phí → XEM ĐƯỢC TKB NGAY
                return [
                    'co_the_xem' => true,
                    'ly_do' => '',
                    'han_xem_tkb' => $hanDongHocPhi,
                    'ngay_xep_lop' => Carbon::parse($ngayXepLop),
                    'da_dong_hoc_phi' => true
                ];
        }

        // Trường hợp 2: CHƯA ĐÓNG HỌC PHÍ (hoặc chỉ đóng một phần)
        if (now()->lt($hanDongHocPhi)) {
            // Trong thời gian đóng học phí (2 tuần)
            $soNgayConLai = now()->diffInDays($hanDongHocPhi, false);
            $trangThaiText = $hocPhi->trang_thai == 'da_nop_mot_phan' ? 'đã đóng một phần' : 'chưa đóng';
            return [
                'co_the_xem' => false,
                'ly_do' => "Bạn {$trangThaiText} học phí. Bạn có {$soNgayConLai} ngày để đóng đủ học phí. Thời khóa biểu sẽ xuất hiện ngay sau khi đóng đủ học phí. Hạn đóng: " . $hanDongHocPhi->format('d/m/Y'),
                'han_xem_tkb' => $hanDongHocPhi,
                'ngay_xep_lop' => Carbon::parse($ngayXepLop),
                'trong_thoi_gian_dong' => true
            ];
        }

        // Trường hợp 3: Đã qua hạn đóng học phí (2 tuần) NHƯNG chưa đóng đủ → KHÔNG XEM ĐƯỢC
        return [
            'co_the_xem' => false,
            'ly_do' => "Bạn đã quá hạn đóng học phí. Vui lòng đóng đủ học phí để xem thời khóa biểu. Hạn đóng: " . $hanDongHocPhi->format('d/m/Y'),
            'han_xem_tkb' => $hanDongHocPhi,
            'ngay_xep_lop' => Carbon::parse($ngayXepLop),
            'qua_han' => true
        ];
    }

    /**
     * Kiểm tra trùng lịch (theo ca học)
     */
    private function kiemTraTrungLich($lopHocPhanSinhViens)
    {
        $lichHoc = [];
        $trungLich = [];

        foreach ($lopHocPhanSinhViens as $lopSV) {
            foreach ($lopSV->lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                // Kiểm tra trùng lịch theo ca học thay vì tiết
                if (!$lichCoDinh->ca_hoc_id) {
                    continue; // Bỏ qua nếu không có ca học
                }
                
                $key = $lichCoDinh->thu_trong_tuan . '_' . $lichCoDinh->ca_hoc_id;

                if (isset($lichHoc[$key])) {
                    $caHocTen = $lichCoDinh->caHoc ? $lichCoDinh->caHoc->ten_ca : 'Ca ' . $lichCoDinh->ca_hoc_id;
                    $tenMon = $lopSV->lopHocPhan->monHoc->ten_mon ?? 'Môn học đã bị xóa';
                    $trungLich[] = [
                        'thu' => $lichCoDinh->getTenThuAttribute(),
                        'ca_hoc' => $caHocTen,
                        'ca_hoc_id' => $lichCoDinh->ca_hoc_id,
                        'mon_1' => $lichHoc[$key],
                        'mon_2' => $tenMon,
                    ];
                } else {
                    $tenMon = $lopSV->lopHocPhan->monHoc->ten_mon ?? 'Môn học đã bị xóa';
                    $lichHoc[$key] = $tenMon;
                }
            }
        }

        return $trungLich;
    }

    /**
     * Xem lịch học dạng bảng với filter thời gian
     */
    public function lichHoc(Request $request)
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
            return view('sinhvien.thoi-khoa-bieu.lich-hoc', [
                'hocKy' => null,
                'hocKys' => $hocKys,
                'lichHocList' => collect(),
                'thoiGianFilter' => $request->get('thoi_gian', '7_ngay_toi'),
            ]);
        }

        // Lấy filter thời gian
        $thoiGianFilter = $request->get('thoi_gian', '7_ngay_toi');
        
        // Tính toán khoảng thời gian dựa trên filter
        $now = Carbon::now();
        $startDate = null;
        $endDate = null;

        switch ($thoiGianFilter) {
            case '7_ngay_toi':
                $startDate = $now->copy();
                $endDate = $now->copy()->addDays(7);
                break;
            case '14_ngay_toi':
                $startDate = $now->copy();
                $endDate = $now->copy()->addDays(14);
                break;
            case '30_ngay_toi':
                $startDate = $now->copy();
                $endDate = $now->copy()->addDays(30);
                break;
            case '60_ngay_toi':
                $startDate = $now->copy();
                $endDate = $now->copy()->addDays(60);
                break;
            case '90_ngay_toi':
                $startDate = $now->copy();
                $endDate = $now->copy()->addDays(90);
                break;
            case '7_ngay_truoc':
                $startDate = $now->copy()->subDays(7);
                $endDate = $now->copy();
                break;
            case '14_ngay_truoc':
                $startDate = $now->copy()->subDays(14);
                $endDate = $now->copy();
                break;
            case '30_ngay_truoc':
                $startDate = $now->copy()->subDays(30);
                $endDate = $now->copy();
                break;
            case '60_ngay_truoc':
                $startDate = $now->copy()->subDays(60);
                $endDate = $now->copy();
                break;
            case '90_ngay_truoc':
                $startDate = $now->copy()->subDays(90);
                $endDate = $now->copy();
                break;
            default:
                $startDate = $now->copy();
                $endDate = $now->copy()->addDays(7);
        }

        // Lấy các lớp học phần của sinh viên trong học kỳ
        $lopHocPhanIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->pluck('lop_hoc_phan_id');

        // Lấy lịch học chi tiết trong khoảng thời gian
        $lichHocList = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereBetween('ngay_hoc', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('trang_thai', '!=', 'huy') // Chỉ lấy lịch chưa bị hủy
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.lopHocPhanSinhViens' => function($q) use ($sinhVien) {
                    $q->where('sinh_vien_id', $sinhVien->id);
                },
                'giangVien',
                'phongHoc',
                'caHoc',
            ])
            ->orderBy('ngay_hoc')
            ->orderBy('gio_bat_dau')
            ->paginate(10);

        // Danh sách học kỳ cho bộ lọc
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('sinhvien.thoi-khoa-bieu.lich-hoc', compact(
            'lichHocList',
            'hocKy',
            'hocKys',
            'thoiGianFilter',
            'startDate',
            'endDate'
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

                // Tạo object giả để view PDF có thể truy cập các quan hệ
                $lichHocObject = (object) [
                    'lopHocPhan' => $lopSV->lopHocPhan,
                    'lichHocCoDinh' => $lichCoDinh,
                ];

                $thoiKhoaBieu[$thuTrongTuan][$tietBatDau] = [
                    'lich' => $lichHocObject,
                    'rowspan' => $soTiet,
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
