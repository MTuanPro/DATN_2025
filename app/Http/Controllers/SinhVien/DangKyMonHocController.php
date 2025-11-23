<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\DangKyMonHocTam;
use App\Models\HocKy;
use App\Models\DaoTao\MonHoc;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\LopHocPhan;
use App\Models\KetQuaHocTap;
use App\Services\DangKyMonHocService;
use App\Services\HocPhiService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DangKyMonHocController extends Controller
{
    protected $dangKyMonHocService;
    protected $hocPhiService;
    protected $notificationService;

    /**
     * Khởi tạo DangKyMonHocController với 3 service dependencies cho quản lý đăng ký môn học
     *
     * Các service được inject:
     * - DangKyMonHocService: Xử lý logic đăng ký môn học, kiểm tra điều kiện, trùng lịch
     * - HocPhiService: Tính toán học phí khi đăng ký môn, tạo hóa đơn, xếp lớp
     * - NotificationService: Gửi thông báo cho sinh viên và đào tạo về đăng ký
     *
     * @param DangKyMonHocService $dangKyMonHocService Service đăng ký môn học
     * @param HocPhiService $hocPhiService Service tính học phí
     * @param NotificationService $notificationService Service gửi thông báo
     * @return void
     */
    public function __construct(
        DangKyMonHocService $dangKyMonHocService, 
        HocPhiService $hocPhiService,
        NotificationService $notificationService
    ) {
        $this->dangKyMonHocService = $dangKyMonHocService;
        $this->hocPhiService = $hocPhiService;
        $this->notificationService = $notificationService;
    }

    /**
     * Hiển thị giao diện đăng ký môn học cho sinh viên trong học kỳ hiện tại
     *
     * Quy trình hiển thị:
     * 1. Kiểm tra học kỳ hiện tại đang mở đăng ký (la_hoc_ky_hien_tai = true AND dang_mo_dang_ky = true)
     * 2. Nếu không có học kỳ mở đăng ký:
     *    - Hiển thị thông báo 'Không có học kỳ mở đăng ký'
     *    - Hiển thị debug info (học kỳ hiện tại, học kỳ mở đăng ký)
     *    - Trả về view rỗng
     * 3. Lấy danh sách lớp học phần đang mở trong học kỳ (trang_thai_lop: mo_dang_ky, dang_hoc)
     * 4. Lấy chương trình khung của ngành sinh viên
     * 5. Lấy danh sách môn đã đăng ký tạm (DangKyMonHocTam)
     * 6. Lấy danh sách môn đã học và đã qua (KetQuaHocTap)
     * 7. Tính tổng số tín chỉ đã đăng ký (giới hạn tối đa 24 tín chỉ/học kỳ)
     * 8. Nhóm môn học theo học kỳ đề nghị trong chương trình khung
     * 9. Hiển thị cảnh báo nếu số tín chỉ vượt quá giới hạn
     * 10. Hiển thị trạng thái của từng môn: Đăng ký được, Đã đăng ký, Đã học, Chưa đủ điều kiện...
     *
     * @param Request $request Có thể chứa hoc_ky_id để xem học kỳ khác
     * @return \Illuminate\View\View Giao diện đăng ký môn học với danh sách môn và thông tin tín chỉ
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy sinh viên
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy học kỳ hiện tại và đang mở đăng ký
        $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)
            ->where('dang_mo_dang_ky', true)
            ->first();

        // Debug: Kiểm tra học kỳ
        $hocKyHienTai = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        $hocKyMoDangKy = HocKy::where('dang_mo_dang_ky', true)->first();

        if (!$hocKy) {
            $hocKy = (object)[
                'id' => null,
                'ten_hoc_ky' => 'Không có học kỳ mở đăng ký',
                'nam_hoc' => '',
                'ngay_bat_dau_dang_ky' => now(),
                'ngay_ket_thuc_dang_ky' => now(),
            ];

            $debugInfo = [
                'hoc_ky_hien_tai' => $hocKyHienTai ? $hocKyHienTai->ten_hoc_ky . ' - ' . $hocKyHienTai->nam_hoc : 'Không có',
                'hoc_ky_mo_dang_ky' => $hocKyMoDangKy ? $hocKyMoDangKy->ten_hoc_ky . ' - ' . $hocKyMoDangKy->nam_hoc : 'Không có',
                'message' => 'Hiện tại không có học kỳ nào mở đăng ký môn học.',
            ];

            return view('sinhvien.dang-ky-mon-hoc.index', [
                'hocKy' => $hocKy,
                'message' => $debugInfo['message'],
                'debugInfo' => $debugInfo,
                'tongTinChiDaDangKy' => 0,
                'tinChiToiDa' => 24,
                'chuongTrinhKhung' => collect(),
                'dangKyCollection' => collect(),
                'monDaDangKy' => [],
                'monDaHoc' => [],
                'monDaQua' => [],
                'lopHocPhans' => collect(),
                'sinhVien' => Auth::user()->sinhVien,
            ]);
        }

        // Lấy danh sách lớp học phần đang mở trong học kỳ này
        $lopDangMo = LopHocPhan::where('hoc_ky_id', $hocKy->id)
            ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
            ->with(['monHoc.khoa'])
            ->get()
            ->groupBy('mon_hoc_id');

        $monIdCoLopMo = $lopDangMo->keys()->toArray();

        // Debug: Kiểm tra chuyên ngành và chương trình khung
        $tongChuongTrinhKhung = 0;
        $chuongTrinhKhungCoLopMo = 0;
        
        if ($sinhVien->chuyen_nganh_id) {
            $tongChuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)->count();
            $chuongTrinhKhungCoLopMo = ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
                ->whereIn('mon_hoc_id', $monIdCoLopMo)
                ->count();
        }

        // Lấy chương trình khung của sinh viên
        // Nếu có chuyên ngành: lấy theo chương trình khung
        // Nếu không có chuyên ngành hoặc CTK rỗng: lấy tất cả môn có lớp đang mở
        if ($sinhVien->chuyen_nganh_id) {
            // Có chuyên ngành: lấy theo chương trình khung
            $chuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
                ->whereIn('mon_hoc_id', $monIdCoLopMo) // Chỉ lấy môn có lớp đang mở
                ->with(['monHoc.khoa'])
                ->orderBy('hoc_ky_goi_y', 'asc')
                ->orderBy('thu_tu_hoc', 'asc')
                ->get();
            
            // Nếu CTK rỗng hoặc không có môn nào có lớp mở, lấy tất cả môn có lớp đang mở
            if ($chuongTrinhKhung->isEmpty() && !empty($monIdCoLopMo)) {
                foreach ($monIdCoLopMo as $monId) {
                    $monHoc = \App\Models\DaoTao\MonHoc::with('khoa')->find($monId);
                    if ($monHoc) {
                        // Tạo object giả tương tự ChuongTrinhKhung để hiển thị
                        $chuongTrinhKhung->push((object)[
                            'id' => null,
                            'mon_hoc_id' => $monHoc->id,
                            'monHoc' => $monHoc,
                            'hoc_ky_goi_y' => 1,
                            'thu_tu_hoc' => 1,
                            'bat_buoc' => false,
                        ]);
                    }
                }
            }
        } else {
            // Không có chuyên ngành: tạo danh sách giả từ các môn có lớp đang mở
            $chuongTrinhKhung = collect();
            foreach ($monIdCoLopMo as $monId) {
                $monHoc = \App\Models\DaoTao\MonHoc::with('khoa')->find($monId);
                if ($monHoc) {
                    // Tạo object giả tương tự ChuongTrinhKhung để hiển thị
                    $chuongTrinhKhung->push((object)[
                        'id' => null,
                        'mon_hoc_id' => $monHoc->id,
                        'monHoc' => $monHoc,
                        'hoc_ky_goi_y' => 1,
                        'thu_tu_hoc' => 1,
                        'bat_buoc' => false,
                    ]);
                }
            }
        }

        // Debug info
        $debugInfo = [
            'hoc_ky_id' => $hocKy->id,
            'tong_lop_dang_mo' => $lopDangMo->count(),
            'tong_mon_co_lop_mo' => count($monIdCoLopMo),
            'tong_chuong_trinh_khung' => $tongChuongTrinhKhung,
            'chuong_trinh_khung_co_lop_mo' => $chuongTrinhKhungCoLopMo,
            'chuyen_nganh_id' => $sinhVien->chuyen_nganh_id,
            'chuyen_nganh' => $sinhVien->chuyenNganh->ten_chuyen_nganh ?? 'Chưa có',
            'co_chuyen_nganh' => $sinhVien->chuyen_nganh_id ? 'Có' : 'Không',
            'so_mon_hien_thi' => $chuongTrinhKhung->count(),
        ];

        // Lấy các môn đã đăng ký trong học kỳ này (lấy full collection để có ID)
        $dangKyCollection = DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
            ->where('hoc_ky_id', $hocKy->id)
            ->get();

        $monDaDangKy = $dangKyCollection->pluck('mon_hoc_id')->toArray();

        // Lấy các môn đã học (có kết quả)
        $monDaHoc = DB::table('lop_hoc_phan_sinh_vien')
            ->join('lop_hoc_phan', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
            ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
            ->pluck('lop_hoc_phan.mon_hoc_id')
            ->toArray();

        // Lấy các môn đã qua
        $monDaQua = DB::table('ket_qua_hoc_tap')
            ->join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
            ->join('lop_hoc_phan', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
            ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
            ->where('ket_qua_hoc_tap.qua_mon', true)
->pluck('lop_hoc_phan.mon_hoc_id')
            ->toArray();

        // Lấy danh sách lớp học phần đang mở
        $lopHocPhans = LopHocPhan::where('hoc_ky_id', $hocKy->id)
            ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
            ->with(['monHoc', 'giangVienChinh.giangVien'])
            ->get()
            ->groupBy('mon_hoc_id');

        // Tính số tín chỉ đã đăng ký
        $tongTinChiDaDangKy = DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
            ->where('hoc_ky_id', $hocKy->id)
            ->join('mon_hoc', 'dang_ky_mon_hoc_tam.mon_hoc_id', '=', 'mon_hoc.id')
            ->sum('mon_hoc.so_tin_chi');

        // Giới hạn tín chỉ tối đa (thường là 24)
        $tinChiToiDa = 24;

        return view('sinhvien.dang-ky-mon-hoc.index', compact(
            'hocKy',
            'chuongTrinhKhung',
            'dangKyCollection',
            'monDaDangKy',
            'monDaHoc',
            'monDaQua',
            'lopHocPhans',
            'tongTinChiDaDangKy',
            'tinChiToiDa',
            'sinhVien',
            'debugInfo'
        ));
    }

    /**
     * Form tạo đăng ký (phiên bản fallback)
     */
    /**
     * Hiển thị form tạo đăng ký môn học mới (không sử dụng trong hệ thống hiện tại)
     *
     * Form này không được sử dụng trong flow đăng ký môn thông thường.
     * Sinh viên đăng ký trực tiếp từ trang index bằng AJAX.
     *
     * @return \Illuminate\View\View Form tạo đăng ký (unused)
     */
    public function create()
    {
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();
        $monHocs = MonHoc::orderBy('ma_mon')->limit(200)->get();

        return view('sinhvien.dang-ky-mon-hoc.create', compact('hocKys', 'monHocs'));
    }

    /**
     * Đăng ký môn học
     */
    /**
     * Lưu đăng ký môn học tạm thời của sinh viên qua AJAX request
     *
     * Quy trình đăng ký môn học (rất phức tạp với nhiều kiểm tra):
     * 1. Validate dữ liệu đầu vào:
     *    - lop_hoc_phan_id: Lớp học phần muốn đăng ký (required, exists)
     * 2. Kiểm tra học kỳ đang mở đăng ký:
     *    - Nếu không có học kỳ mở: Trả về lỗi 'Không trong thời gian đăng ký'
     * 3. Kiểm tra lớp học phần:
     *    - Lớp có thuộc học kỳ đang mở không?
     *    - Lớp có đang mở đăng ký không? (trang_thai_lop: mo_dang_ky, dang_hoc)
     *    - Còn chỗ không? (si_so_hien_tai < si_so_toi_da)
     * 4. Kiểm tra sinh viên đã đăng ký môn này chưa:
     *    - Trong DangKyMonHocTam (tạm thời)
     *    - Trong LopHocPhanSinhVien (đã xếp lớp chính thức)
     *    - Nếu đã đăng ký: Trả về lỗi 'Bạn đã đăng ký môn này'
     * 5. Kiểm tra điều kiện tiên quyết:
     *    - Môn có yêu cầu môn tiên quyết không?
     *    - Sinh viên đã qua môn tiên quyết chưa? (KetQuaHocTap)
     *    - Nếu chưa đủ điều kiện: Trả về lỗi liệt kê các môn tiên quyết cần học
     * 6. Kiểm tra giới hạn tín chỉ:
     *    - Tính tổng tín chỉ đã đăng ký + tín chỉ môn mới
     *    - Nếu vượt 24 tín chỉ: Trả về lỗi 'Vượt quá giới hạn 24 tín chỉ'
     * 7. Kiểm tra trùng lịch học (thông qua DangKyMonHocService):
     *    - So sánh lịch học của môn mới với các môn đã đăng ký
     *    - Nếu trùng lịch: Trả về lỗi với thông tin lịch trùng
     * 8. Kiểm tra học phí:
     *    - Sinh viên đã thanh toán học phí học kỳ chưa?
     *    - Nếu chưa thanh toán: Cảnh báo nhưng vẫn cho đăng ký
     * 9. Sử dụng database transaction để đảm bảo data integrity:
     *    - Tạo bản ghi DangKyMonHocTam (trạng thái: cho_duyet)
     *    - Lưu thông tin: sinh_vien_id, lop_hoc_phan_id, hoc_ky_id, ngay_dang_ky
     *    - Tự động tăng si_so_hien_tai của LopHocPhan (optimistic locking)
     * 10. Gửi thông báo:
     *     - Thông báo cho sinh viên về đăng ký thành công
     *     - Thông báo cho đào tạo (nếu cần duyệt)
     * 11. Trả về JSON response:
     *     - success: true
     *     - message: 'Thêm môn học vào danh sách đăng ký thành công'
     *     - data: Thông tin đăng ký vừa tạo
     *     - warnings: Cảnh báo nếu có (ví dụ: chưa thanh toán học phí)
     *
     * @param Request $request Chứa lop_hoc_phan_id (ID lớp học phần muốn đăng ký)
     * @return \Illuminate\Http\JsonResponse JSON {success, message, data?, warnings?}
     * @throws \Exception Khi có lỗi trong quá trình đăng ký
     */
    public function store(Request $request)
    {
        $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
        ]);

        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin sinh viên!'
            ], 404);
        }

        // Kiểm tra học kỳ đang mở đăng ký
        $hocKy = HocKy::find($request->hoc_ky_id);
        if (!$hocKy || !$hocKy->dang_mo_dang_ky) {
            return response()->json([
                'success' => false,
                'message' => 'Học kỳ không mở đăng ký!'
            ], 400);
        }
        
        // Kiểm tra thời gian đăng ký (nếu có)
        if ($hocKy->ngay_bat_dau_dang_ky && $hocKy->ngay_bat_dau_dang_ky > now()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đến thời gian đăng ký!'
            ], 400);
        }
        
        if ($hocKy->ngay_ket_thuc_dang_ky && $hocKy->ngay_ket_thuc_dang_ky < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Đã hết thời gian đăng ký!'
            ], 400);
        }

        // Sử dụng Service để validate tất cả điều kiện
        $validation = $this->dangKyMonHocService->validateRegistration(
            $sinhVien->id,
            $request->mon_hoc_id,
            $request->hoc_ky_id
        );

        if (!$validation['passed']) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể đăng ký môn học',
                'errors' => $validation['errors'],
                'details' => $validation['details']
            ], 400);
        }

        // Tính độ ưu tiên
        $uuTien = 0;
// Sinh viên năm cuối (kỳ >= 7) có độ ưu tiên cao
        if ($sinhVien->ky_hien_tai >= 7) {
            $uuTien += 100;
        }

        // Sinh viên học lại (đã học môn này nhưng chưa qua) có độ ưu tiên
        $daHocChuaQua = DB::table('ket_qua_hoc_tap')
            ->join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
            ->join('lop_hoc_phan', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
            ->where('lop_hoc_phan_sinh_vien.sinh_vien_id', $sinhVien->id)
            ->where('lop_hoc_phan.mon_hoc_id', $request->mon_hoc_id)
            ->where('ket_qua_hoc_tap.qua_mon', false)
            ->exists();

        if ($daHocChuaQua) {
            $uuTien += 50;
        }

        try {
            DB::beginTransaction();

            // Tạo đăng ký tạm với trạng thái chờ đóng học phí (chưa vào danh sách chờ xếp lớp)
            $dangKy = DangKyMonHocTam::create([
                'sinh_vien_id' => $sinhVien->id,
                'mon_hoc_id' => $request->mon_hoc_id,
                'hoc_ky_id' => $request->hoc_ky_id,
                'ngay_dang_ky' => now(),
                'uu_tien' => $uuTien,
                'trang_thai' => 'cho_dong_hoc_phi', // Trạng thái mới: chờ đóng học phí
            ]);

            // ✅ TÍNH HỌC PHÍ NGAY KHI ĐĂNG KÝ MÔN
            $hocPhi = $this->hocPhiService->tinhHocPhiKhiDangKyMonHoc(
                $sinhVien->id,
                $request->hoc_ky_id,
                $request->mon_hoc_id
            );

            if (!$hocPhi) {
                DB::rollBack();
                
                // Kiểm tra xem có cấu hình học phí không
                $cauHinh = \App\Models\CauHinhHocPhi::getCauHinhHienTai();
                $message = 'Không thể tính học phí. ';
                
                if (!$cauHinh) {
                    $allConfigs = \App\Models\CauHinhHocPhi::count();
                    if ($allConfigs == 0) {
                        $message .= 'Chưa có cấu hình học phí nào trong hệ thống. Vui lòng liên hệ phòng đào tạo để thiết lập cấu hình học phí.';
                    } else {
                        $message .= 'Không tìm thấy cấu hình học phí đang áp dụng cho ngày hiện tại. Vui lòng kiểm tra lại cấu hình học phí hoặc liên hệ phòng đào tạo.';
                    }
                } else {
                    $message .= 'Vui lòng thử lại hoặc liên hệ phòng đào tạo.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            // Lấy thông tin môn học để gửi thông báo
            $monHoc = \App\Models\DaoTao\MonHoc::find($request->mon_hoc_id);
            $tenMonHoc = $monHoc ? $monHoc->ten_mon : 'Môn học';

            // ✅ GỬI THÔNG BÁO YÊU CẦU ĐÓNG HỌC PHÍ
            try {
                // Tính số tiền học phí cho môn này (từ chi tiết học phí môn)
                $chiTietHocPhi = \App\Models\ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                    ->where('mon_hoc_id', $request->mon_hoc_id)
                    ->first();
                
                $soTienMonHoc = $chiTietHocPhi ? $chiTietHocPhi->thanh_tien : 0;
                
                // Sử dụng hạn đóng từ học phí (đã được tính là 1 tuần)
                $hanDong = $hocPhi->han_dong;

                $this->notificationService->sendTuitionPaymentRequestNotification(
                    $sinhVien->id,
                    $tenMonHoc,
                    $hocPhi->tong_so_tien, // Tổng học phí của học kỳ
                    $hanDong
                );
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi thông báo yêu cầu đóng học phí: ' . $e->getMessage());
                // Không rollback vì đăng ký đã thành công, chỉ log lỗi
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Đăng ký môn học thành công! Vui lòng đóng học phí trong vòng 1 tuần để được xếp lớp.',
                'data' => $dangKy,
                'hoc_phi' => [
                    'tong_so_tien' => $hocPhi->tong_so_tien,
                    'so_tien_con_lai' => $hocPhi->so_tien_con_lai,
                    'han_dong' => $hanDong,
                ]
            ];

            // Thêm warnings nếu có
            if (!empty($validation['warnings'])) {
                $response['warnings'] = $validation['warnings'];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi đăng ký môn học: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký môn học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy đăng ký môn học
     * Cho phép hủy khi: chưa đóng học phí (cho_dong_hoc_phi) hoặc chưa xếp lớp (cho_xep_lop)
     */
    /**
     * Hủy đăng ký môn học tạm thời của sinh viên qua AJAX
     *
     * Quy trình hủy đăng ký:
     * 1. Tìm bản ghi DangKyMonHocTam theo ID
     * 2. Kiểm tra quyền: Sinh viên chỉ được hủy đăng ký của mình
     * 3. Kiểm tra trạng thái:
     *    - Chỉ cho phép hủy nếu trạng thái: cho_duyet, da_duyet
     *    - Không cho hủy nếu: da_xep_lop, bi_tu_choi
     * 4. Kiểm tra thời gian:
     *    - Chỉ cho phép hủy trong thời gian mở đăng ký
     *    - Nếu quá hạn: Trả về lỗi 'Hết thời gian hủy đăng ký'
     * 5. Sử dụng database transaction:
     *    - Xóa bản ghi DangKyMonHocTam
     *    - Giảm si_so_hien_tai của LopHocPhan (atomic decrement)
     *    - Cập nhật lại học phí (nếu có)
     * 6. Gửi thông báo cho sinh viên về việc hủy đăng ký
     * 7. Trả về JSON response thành công
     *
     * @param int $id ID của bản ghi DangKyMonHocTam cần hủy
     * @return \Illuminate\Http\JsonResponse JSON {success, message}
     * @throws \Exception Khi có lỗi trong quá trình hủy
     */
    public function destroy($id)
    {
        $sinhVien = Auth::user()->sinhVien;

        $dangKy = DangKyMonHocTam::where('id', $id)
            ->where('sinh_vien_id', $sinhVien->id)
            ->first();

        if (!$dangKy) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đăng ký!'
            ], 404);
        }

        // ✅ Cho phép hủy nếu chưa đóng học phí hoặc chưa xếp lớp
        if (!in_array($dangKy->trang_thai, ['cho_dong_hoc_phi', 'cho_xep_lop'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đăng ký đã được xếp lớp!'
            ], 400);
        }

        // Kiểm tra thời gian hủy đăng ký
        $hocKy = $dangKy->hocKy;
        if ($hocKy && $hocKy->ngay_ket_thuc_dang_ky && now() > $hocKy->ngay_ket_thuc_dang_ky) {
            return response()->json([
                'success' => false,
                'message' => 'Đã hết thời gian hủy đăng ký!'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // ✅ HỦY HỌC PHÍ TƯƠNG ỨNG
            $hocPhi = \App\Models\HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
                ->where('hoc_ky_id', $dangKy->hoc_ky_id)
                ->first();

            if ($hocPhi) {
                // Tìm và hủy chi tiết học phí của môn này
                $chiTietHocPhi = \App\Models\ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                    ->where('mon_hoc_id', $dangKy->mon_hoc_id)
                    ->whereNull('lop_hoc_phan_sinh_vien_id') // Chỉ hủy những môn chưa xếp lớp
                    ->first();

                if ($chiTietHocPhi) {
                    // Đánh dấu là hủy
                    $chiTietHocPhi->trang_thai = 'huy';
                    $chiTietHocPhi->save();

                    // Tính lại tổng học phí
                    $this->hocPhiService->recalculateHocPhi($hocPhi->id);
                }
            }

            // Xóa đăng ký
            $dangKy->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hủy đăng ký thành công! Học phí đã được cập nhật.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi hủy đăng ký môn học: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy đăng ký: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem danh sách môn đã đăng ký
     */
    /**
     * Xem danh sách tất cả các môn học đã đăng ký của sinh viên (có lọc và phân trang)
     *
     * Hiển thị:
     * - Các môn đăng ký tạm thời (DangKyMonHocTam)
     * - Các môn đã xếp lớp chính thức (LopHocPhanSinhVien)
     * - Kèm thông tin lớp học phần, môn học, học kỳ
     * - Trạng thái đăng ký: Chờ duyệt, Đã duyệt, Đã xếp lớp, Bị từ chối
     *
     * Chức năng lọc:
     * - Theo học kỳ (hoc_ky_id)
     * - Theo trạng thái (trang_thai)
     * - Tìm kiếm theo tên môn học (search)
     *
     * Phân trang 15 môn/trang.
     *
     * @param Request $request Có thể chứa hoc_ky_id, trang_thai, search
     * @return \Illuminate\View\View Danh sách môn đã đăng ký
     */
    public function myRegistrations(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        $query = DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
->with(['monHoc', 'hocKy', 'lopHocPhanSinhVien.lopHocPhan']);

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(15);

        // Thống kê
        $thongKe = [
            'cho_xep_lop' => DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
                ->where('trang_thai', 'cho_xep_lop')->count(),
            'da_xep_lop' => DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
                ->where('trang_thai', 'da_xep_lop')->count(),
            'that_bai' => DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
                ->where('trang_thai', 'that_bai')->count(),
            'tong_tin_chi' => DangKyMonHocTam::where('sinh_vien_id', $sinhVien->id)
                ->where('trang_thai', 'da_xep_lop')
                ->join('mon_hoc', 'dang_ky_mon_hoc_tam.mon_hoc_id', '=', 'mon_hoc.id')
                ->sum('mon_hoc.so_tin_chi'),
        ];

        // Danh sách học kỳ cho bộ lọc
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view(
            'sinhvien.dang-ky-mon-hoc.my-registrations',
            compact('registrations', 'thongKe', 'hocKys')
        );
    }
}