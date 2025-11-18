<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\CauHinhDauDiem;
use App\Models\GiangVien;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\NhapDiem;
use App\Models\PhanCongGiangDay;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use App\Services\DiemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NhapDiemController extends Controller
{
    protected $diemService;

    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    /**
     * Danh sách lớp học phần của giảng viên
     */
    public function index()
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách lớp học phần đang dạy
        $lopHocPhans = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'lopHocPhan.cauHinhDauDiem'
            ])
            ->get()
            ->map(function ($phanCong) {
                $lhp = $phanCong->lopHocPhan;
                
                // Đếm số sinh viên
                $tongSV = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lhp->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                    ->count();

                // Đếm số sinh viên đã có điểm
                $svCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lhp) {
                    $q->where('lop_hoc_phan_id', $lhp->id)
                        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);
                })
                    ->whereNotNull('diem_he_10')
                    ->count();

                return [
                    'id' => $lhp->id,
                    'ma_lop_hp' => $lhp->ma_lop_hp,
                    'ten_lop_hp' => $lhp->ten_lop_hp,
                    'mon_hoc' => $lhp->monHoc->ten_mon,
                    'hoc_ky' => $lhp->hocKy->ten_hoc_ky,
                    'vai_tro' => $phanCong->vai_tro,
                    'tong_sv' => $tongSV,
                    'sv_co_diem' => $svCoDiem,
                    'ty_le' => $tongSV > 0 ? round($svCoDiem / $tongSV * 100, 1) : 0,
                    'trang_thai' => $lhp->trang_thai_lop,
                    'da_khoa_diem' => $lhp->trang_thai_lop === 'da_khoa_diem',
                ];
            });

        return view('giangvien.nhap-diem.index', compact('lopHocPhans'));
    }

    /**
     * Trang nhập điểm chi tiết cho lớp học phần
     */
    public function show($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
$duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$duocPhanCong) {
            return redirect()->route('giangvien.nhap-diem.index')
                ->with('error', 'Bạn không có quyền truy cập lớp này');
        }

        // Lấy thông tin lớp học phần
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        // Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        // Lấy danh sách sinh viên (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien', 'ketQuaHocTap'])
            ->orderBy('sinh_vien_id')
            ->get();

        // Lấy tất cả điểm đã nhập
        $nhapDiems = NhapDiem::whereIn('lop_hoc_phan_sinh_vien_id', $sinhViens->pluck('id'))
            ->get()
            ->groupBy('lop_hoc_phan_sinh_vien_id');

        // Kiểm tra trạng thái
        $daKhoaDiem = $lopHocPhan->trang_thai_lop === 'da_khoa_diem' || $lopHocPhan->trang_thai_lop === 'da_duyet_diem';
        $laGiangVienChinh = $duocPhanCong->vai_tro === 'giang_vien_chinh';

        return view('giangvien.nhap-diem.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'sinhViens',
            'nhapDiems',
            'daKhoaDiem',
            'laGiangVienChinh'
        ));
    }

    /**
     * Nhập điểm thành phần (AJAX)
     */
    public function nhapDiem(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
            'cau_hinh_id' => 'required|exists:cau_hinh_dau_diem,id',
            'cot_diem' => 'required|integer|min:1',
            'diem_so' => 'required|numeric|min:0|max:10',
            'ghi_chu' => 'nullable|string|max:500',
        ]);

        // Kiểm tra quyền
        $lhpsv = LopHocPhanSinhVien::find($validated['lop_hoc_phan_sinh_vien_id']);
        $giangVien = Auth::user()->giangVien;

        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->exists();

        if (!$duocPhanCong) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền nhập điểm lớp này'
            ], 403);
        }

        // Kiểm tra lớp đã khóa điểm chưa
        $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);

        if ($lopHocPhan->trang_thai_lop === 'da_khoa_diem' || $lopHocPhan->trang_thai_lop === 'da_duyet_diem') {
            return response()->json([
                'success' => false,
'message' => 'Lớp đã khóa điểm, không thể sửa'
            ], 400);
        }

        // Kiểm tra cột điểm hợp lệ
        $cauHinh = CauHinhDauDiem::find($validated['cau_hinh_id']);

        if ($validated['cot_diem'] > $cauHinh->so_cot) {
            return response()->json([
                'success' => false,
                'message' => "Cột điểm không hợp lệ (max: {$cauHinh->so_cot})"
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Insert hoặc Update điểm
            $nhapDiem = NhapDiem::updateOrCreate(
                [
                    'lop_hoc_phan_sinh_vien_id' => $validated['lop_hoc_phan_sinh_vien_id'],
                    'cau_hinh_id' => $validated['cau_hinh_id'],
                    'cot_diem' => $validated['cot_diem'],
                ],
                [
                    'diem_so' => $validated['diem_so'],
                    'ghi_chu' => $validated['ghi_chu'] ?? null,
                ]
            );

            // Tự động tính điểm tổng
            $this->diemService->tinhDiemTong($validated['lop_hoc_phan_sinh_vien_id']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã nhập điểm thành công',
                'data' => $nhapDiem
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
     * Khóa điểm (chỉ GV chính mới được khóa)
     */
    public function khoaDiem(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV chính)
        $gvChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$gvChinh) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ giảng viên chính mới có quyền khóa điểm'
            ], 403);
        }

        $lopHocPhan = LopHocPhan::find($lopHocPhanId);

        // Kiểm tra tất cả sinh viên đã có điểm chưa
        $tongSinhVien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('trang_thai', 'dang_hoc')
            ->count();

        $sinhVienCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId)
                ->where('trang_thai', 'dang_hoc');
        })
            ->whereNotNull('diem_he_10')
            ->count();

        if ($sinhVienCoDiem < $tongSinhVien) {
            $soThieu = $tongSinhVien - $sinhVienCoDiem;
// Cho phép khóa nếu confirm
            if (!$request->has('confirm')) {
                return response()->json([
                    'success' => false,
                    'message' => "Còn {$soThieu} sinh viên chưa có điểm. Bạn có chắc muốn khóa?",
                    'can_confirm' => true,
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Khóa điểm
            $lopHocPhan->update([
                'trang_thai_lop' => 'da_khoa_diem',
            ]);

            // TODO: Gửi thông báo cho đào tạo
            // $this->guiThongBaoKhoaDiem($lopHocPhanId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã khóa điểm thành công. Chờ Đào tạo duyệt.'
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
     * Mở khóa điểm (khi Đào tạo trả về)
     */
    public function moKhoaDiem($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV chính)
        $gvChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->exists();

        if (!$gvChinh) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ giảng viên chính mới có quyền mở khóa điểm'
            ], 403);
        }

        try {
            $lopHocPhan = LopHocPhan::find($lopHocPhanId);

            if ($lopHocPhan->trang_thai_lop !== 'da_khoa_diem') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp chưa khóa điểm'
                ], 400);
            }

            $lopHocPhan->update([
                'trang_thai_lop' => 'dang_hoc',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã mở khóa điểm thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi điểm cho đào tạo để duyệt
     */
    public function guiDiemChoDaoTao(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV chính)
        $gvChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$gvChinh) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ giảng viên chính mới có quyền gửi điểm cho đào tạo'
            ], 403);
        }

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        // Kiểm tra trạng thái
        if ($lopHocPhan->trang_thai_lop === 'da_khoa_diem' || $lopHocPhan->trang_thai_lop === 'da_duyet_diem') {
            return response()->json([
                'success' => false,
                'message' => 'Điểm đã được gửi hoặc đã được duyệt'
            ], 400);
        }

        // Kiểm tra tất cả sinh viên đã có điểm chưa
        $tongSinhVien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->count();

        $sinhVienCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);
        })
            ->whereNotNull('diem_he_10')
            ->count();

        if ($sinhVienCoDiem < $tongSinhVien) {
            $soThieu = $tongSinhVien - $sinhVienCoDiem;
            // Cho phép gửi nếu confirm
            if (!$request->has('confirm')) {
                return response()->json([
                    'success' => false,
                    'message' => "Còn {$soThieu} sinh viên chưa có điểm. Bạn có chắc muốn gửi điểm?",
                    'can_confirm' => true,
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Đặt trạng thái là đã khóa điểm (chờ duyệt)
            $lopHocPhan->update([
                'trang_thai_lop' => 'da_khoa_diem',
            ]);

            // Gửi thông báo cho đào tạo
            $this->guiThongBaoGuiDiemChoDaoTao($lopHocPhan, $giangVien);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi điểm cho đào tạo thành công. Chờ đào tạo duyệt.'
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
     * Gửi thông báo cho đào tạo khi giảng viên gửi điểm
     */
    private function guiThongBaoGuiDiemChoDaoTao($lopHocPhan, $giangVien)
    {
        // Lấy tất cả tài khoản đào tạo
        $daoTaos = \App\Models\DaoTao::with('user')->get();

        if ($daoTaos->isEmpty()) {
            return;
        }

        // Tạo thông báo
        $thongBao = ThongBao::create([
            'tieu_de' => 'Giảng viên gửi điểm lớp ' . $lopHocPhan->ma_lop_hp,
            'noi_dung' => "Giảng viên {$giangVien->ho_ten} đã gửi điểm lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->monHoc->ten_mon} ({$lopHocPhan->hocKy->ten_hoc_ky}) để duyệt. Vui lòng truy cập phần 'Duyệt điểm' để xem và duyệt.",
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'diem',
            'muc_do_quan_trong' => 'quan_trong',
            'doi_tuong' => 'dao_tao',
            'nguoi_gui_id' => $giangVien->user_id ?? Auth::id(),
            'ngay_gui' => now(),
            'lien_ket_loai' => 'diem',
            'lien_ket_id' => $lopHocPhan->id,
        ]);

        // Gửi cho tất cả đào tạo
        foreach ($daoTaos as $daoTao) {
            if ($daoTao->user) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao->id,
                    'nguoi_nhan_id' => $daoTao->user_id,
                    'da_doc' => false,
                ]);
            }
        }
    }
}