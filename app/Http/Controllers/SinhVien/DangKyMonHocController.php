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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DangKyMonHocController extends Controller
{
    protected $dangKyMonHocService;

    public function __construct(DangKyMonHocService $dangKyMonHocService)
    {
        $this->dangKyMonHocService = $dangKyMonHocService;
    }

    /**
     * Hiển thị danh sách môn có thể đăng ký
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinhvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy học kỳ hiện tại hoặc đang mở đăng ký
        $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)
            ->where('ngay_bat_dau_dang_ky', '<=', now())
            ->where('ngay_ket_thuc_dang_ky', '>=', now())
            ->first();

      if (!$hocKy) {
    $hocKy = (object)[
        'id' => null,
        'ten_hoc_ky' => 'Không có học kỳ mở đăng ký',
        'nam_hoc' => '', // ✅ thêm dòng này
        'ngay_bat_dau_dang_ky' => now(),
        'ngay_ket_thuc_dang_ky' => now(),
    ];


    return view('sinhvien.dang-ky-mon-hoc.index', [
        'hocKy' => $hocKy,
        'message' => 'Hiện tại không có học kỳ nào mở đăng ký môn học.',
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



        // Lấy chương trình khung của sinh viên (sắp xếp theo học kỳ và thứ tự)
        $chuongTrinhKhung = ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
            ->with(['monHoc.khoa'])
            ->orderBy('hoc_ky_goi_y', 'asc')
            ->orderBy('thu_tu_hoc', 'asc')
            ->get();

        // Lấy thêm các môn có lớp học phần mở nhưng chưa có trong chương trình khung
        $monIdTrongCTK = $chuongTrinhKhung->pluck('mon_hoc_id')->toArray();
        $lopDangMo = LopHocPhan::where('hoc_ky_id', $hocKy->id)
            ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
            ->whereNotIn('mon_hoc_id', $monIdTrongCTK)
            ->with(['monHoc.khoa'])
            ->get()
            ->groupBy('mon_hoc_id');

        // Thêm các môn không có trong CTK vào danh sách (đánh dấu là không bắt buộc)
        foreach ($lopDangMo as $monHocId => $lops) {
            $monHoc = $lops->first()->monHoc;
            if ($monHoc) {
                $chuongTrinhKhung->push((object)[
                    'mon_hoc_id' => $monHocId,
                    'monHoc' => $monHoc,
                    'bat_buoc' => false,
                    'hoc_ky_goi_y' => 0, // Không có gợi ý
                    'thu_tu_hoc' => 999,
                ]);
            }
        }

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
            'sinhVien'
        ));
    }

    /**
     * Form tạo đăng ký (phiên bản fallback)
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
        if (!$hocKy || $hocKy->ngay_bat_dau_dang_ky > now() || $hocKy->ngay_ket_thuc_dang_ky < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Học kỳ không mở đăng ký!'
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

        // Tạo đăng ký tạm
        $dangKy = DangKyMonHocTam::create([
            'sinh_vien_id' => $sinhVien->id,
            'mon_hoc_id' => $request->mon_hoc_id,
            'hoc_ky_id' => $request->hoc_ky_id,
            'ngay_dang_ky' => now(),
            'uu_tien' => $uuTien,
            'trang_thai' => 'cho_xep_lop',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký môn học thành công! Hệ thống sẽ tự động xếp lớp sau.',
            'data' => $dangKy
        ]);
    }

    /**
     * Hủy đăng ký môn học
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

        // Chỉ cho phép hủy nếu chưa xếp lớp
        if ($dangKy->trang_thai !== 'cho_xep_lop') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đăng ký đã xếp lớp!'
            ], 400);
        }

        // Kiểm tra thời gian hủy đăng ký
        $hocKy = $dangKy->hocKy;
        if (now() > $hocKy->ngay_ket_thuc_dang_ky) {
            return response()->json([
                'success' => false,
                'message' => 'Đã hết thời gian hủy đăng ký!'
            ], 400);
        }

        $dangKy->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hủy đăng ký thành công!'
        ]);
    }

    /**
     * Xem danh sách môn đã đăng ký
     */
    public function myRegistrations(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinhvien.dashboard')
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