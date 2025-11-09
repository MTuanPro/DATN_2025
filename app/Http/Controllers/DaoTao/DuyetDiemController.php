<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\CauHinhDauDiem;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\NhapDiem;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use App\Services\DiemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DuyetDiemController extends Controller
{
    protected $diemService;

    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    /**
     * Danh sách lớp học phần cần duyệt điểm
     */
    public function index(Request $request)
    {
        $hocKyId = $request->get('hoc_ky_id');
        $trangThai = $request->get('trang_thai', 'da_khoa_diem');

        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('ngay_bat_dau', 'desc')->limit(5)->get();

        // Query lớp học phần
        $query = LopHocPhan::with(['monHoc', 'hocKy']);

        // Filter theo học kỳ
        if ($hocKyId) {
            $query->where('hoc_ky_id', $hocKyId);
        }

        // Filter theo trạng thái
        if ($trangThai) {
            $query->where('trang_thai_lop', $trangThai);
        }

        $lopHocPhans = $query->get()->map(function ($lhp) {
            // Đếm số sinh viên
            $tongSV = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lhp->id)
                ->where('trang_thai', 'dang_hoc')
                ->count();

            // Đếm số sinh viên đã có điểm
            $svCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lhp) {
                $q->where('lop_hoc_phan_id', $lhp->id)
                    ->where('trang_thai', 'dang_hoc');
            })
                ->whereNotNull('diem_he_10')
                ->count();

            // Điểm trung bình lớp
            $diemTB = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lhp) {
                $q->where('lop_hoc_phan_id', $lhp->id)
                    ->where('trang_thai', 'dang_hoc');
            })
                ->whereNotNull('diem_he_10')
                ->avg('diem_he_10');

            return [
                'id' => $lhp->id,
                'ma_lop_hp' => $lhp->ma_lop_hp,
                'ten_lop_hp' => $lhp->ten_lop_hp,
                'mon_hoc' => $lhp->monHoc->ten_mon,
                'hoc_ky' => $lhp->hocKy->ten_hoc_ky,
                'tong_sv' => $tongSV,
                'sv_co_diem' => $svCoDiem,
                'ty_le' => $tongSV > 0 ? round($svCoDiem / $tongSV * 100, 1) : 0,
                'diem_tb' => $diemTB ? round($diemTB, 2) : null,
                'trang_thai' => $lhp->trang_thai_lop,
            ];
        });

        return view('daotao.duyet-diem.index', compact('lopHocPhans', 'hocKys', 'hocKyId', 'trangThai'));
    }
/**
     * Xem chi tiết bảng điểm
     */
    public function show($lopHocPhanId)
    {
        // Lấy thông tin lớp học phần
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        if (!$lopHocPhan) {
            return redirect()->route('daotao.duyet-diem.index')
                ->with('error', 'Không tìm thấy lớp học phần');
        }

        // Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        // Lấy danh sách sinh viên
        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('trang_thai', 'dang_hoc')
            ->with(['sinhVien', 'ketQuaHocTap'])
            ->orderBy('sinh_vien_id')
            ->get();

        // Lấy tất cả điểm đã nhập
        $nhapDiems = NhapDiem::whereIn('lop_hoc_phan_sinh_vien_id', $sinhViens->pluck('id'))
            ->get()
            ->groupBy('lop_hoc_phan_sinh_vien_id');

        // Thống kê
        $thongKe = [
            'tong_sv' => $sinhViens->count(),
            'sv_co_diem' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && $sv->ketQuaHocTap->diem_he_10 !== null;
            })->count(),
            'sv_qua_mon' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && $sv->ketQuaHocTap->qua_mon;
            })->count(),
            'sv_khong_qua_mon' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && !$sv->ketQuaHocTap->qua_mon;
            })->count(),
            'diem_tb' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && $sv->ketQuaHocTap->diem_he_10 !== null;
            })->avg(function ($sv) {
                return $sv->ketQuaHocTap->diem_he_10;
            }),
        ];

        return view('daotao.duyet-diem.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'sinhViens',
            'nhapDiems',
            'thongKe'
        ));
    }

    /**
     * Duyệt điểm hoặc trả về
     */
    public function duyetDiem(Request $request, $lopHocPhanId)
    {
        // Validate
        $validated = $request->validate([
            'hanh_dong' => 'required|in:phe_duyet,tra_ve',
            'ly_do_tra_ve' => 'required_if:hanh_dong,tra_ve|max:500',
        ]);

        // Kiểm tra trạng thái
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        if ($lopHocPhan->trang_thai_lop !== 'da_khoa_diem') {
            return response()->json([
                'success' => false,
                'message' => 'Lớp chưa khóa điểm hoặc đã được duyệt'
            ], 400);
        }

        try {
            DB::beginTransaction();

            if ($validated['hanh_dong'] === 'phe_duyet') {
                // Phê duyệt
$lopHocPhan->update([
                    'trang_thai_lop' => 'da_duyet_diem',
                ]);

                // Cập nhật bảng điểm cho từng sinh viên
                $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
                    ->where('trang_thai', 'dang_hoc')
                    ->get();

                foreach ($sinhViens as $sv) {
                    $this->diemService->capNhatBangDiem($sv->sinh_vien_id, $lopHocPhan->hoc_ky_id);
                }

                // Gửi thông báo cho sinh viên
                $this->guiThongBaoCongBoDiem($lopHocPhan);

                $message = 'Đã phê duyệt và công bố điểm thành công';
            } else {
                // Trả về
                $lopHocPhan->update([
                    'trang_thai_lop' => 'dang_hoc',
                    'ly_do_tra_ve' => $validated['ly_do_tra_ve'],
                ]);

                // Gửi thông báo cho giảng viên
                $this->guiThongBaoTraVeDiem($lopHocPhan, $validated['ly_do_tra_ve']);

                $message = 'Đã trả về điểm cho giảng viên';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
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
     * Gửi thông báo công bố điểm cho sinh viên
     */
    private function guiThongBaoCongBoDiem($lopHocPhan)
    {
        // Lấy danh sách sinh viên
        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
            ->where('trang_thai', 'dang_hoc')
            ->with('sinhVien.user')
            ->get();

        // Tạo thông báo
        $thongBao = ThongBao::create([
            'tieu_de' => 'Công bố điểm môn ' . $lopHocPhan->monHoc->ten_mon,
            'noi_dung' => "Điểm môn {$lopHocPhan->monHoc->ten_mon} - Lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->hocKy->ten_hoc_ky} đã được công bố. Vui lòng truy cập để xem chi tiết.",
            'loai_thong_bao' => 'diem',
            'muc_do_quan_trong' => 'cao',
            'nguoi_gui_id' => Auth::id(),
        ]);

        // Gửi cho từng sinh viên
        foreach ($sinhViens as $sv) {
            if ($sv->sinhVien && $sv->sinhVien->user) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao->id,
                    'nguoi_nhan_id' => $sv->sinhVien->user_id,
                    'da_doc' => false,
                ]);
            }
        }
    }

    /**
     * Gửi thông báo trả về điểm cho giảng viên
     */
    private function guiThongBaoTraVeDiem($lopHocPhan, $lyDo)
    {
        // Lấy giảng viên chính
$phanCong = $lopHocPhan->lopHocPhanGiangVien()
            ->where('vai_tro', 'giang_vien_chinh')
            ->with('giangVien.user')
            ->first();

        if (!$phanCong || !$phanCong->giangVien || !$phanCong->giangVien->user) {
            return;
        }

        // Tạo thông báo
        $thongBao = ThongBao::create([
            'tieu_de' => 'Trả về điểm lớp ' . $lopHocPhan->ma_lop_hp,
            'noi_dung' => "Điểm lớp {$lopHocPhan->ma_lop_hp} đã được trả về để chỉnh sửa.\n\nLý do: {$lyDo}",
            'loai_thong_bao' => 'diem',
            'muc_do_quan_trong' => 'cao',
            'nguoi_gui_id' => Auth::id(),
        ]);

        // Gửi cho giảng viên
        NguoiNhanThongBao::create([
            'thong_bao_id' => $thongBao->id,
            'nguoi_nhan_id' => $phanCong->giangVien->user_id,
            'da_doc' => false,
        ]);
    }
}