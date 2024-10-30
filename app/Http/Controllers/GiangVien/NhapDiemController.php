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

                // Kiểm tra lớp đã kết thúc chưa
                $daKetThuc = $lhp->daKetThuc();
                $dangDienRa = $lhp->dangDienRa();
                
                // Xác định trạng thái hiển thị
                $trangThaiHienThi = $lhp->trang_thai_lop;
                if ($daKetThuc) {
                    $trangThaiHienThi = 'ket_thuc';
                }

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
                    'trang_thai' => $trangThaiHienThi,
                    'trang_thai_lop' => $lhp->trang_thai_lop, // Giữ nguyên để kiểm tra logic khác
                    'da_khoa_diem' => $lhp->trang_thai_lop === 'da_khoa_diem',
                    'da_ket_thuc' => $daKetThuc,
                    'dang_dien_ra' => $dangDienRa,
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
        // Chỉ chặn sửa khi đang chờ duyệt (da_khoa_diem), không chặn khi đã duyệt (cho phép sửa lại)
        $daKhoaDiem = $lopHocPhan->trang_thai_lop === 'da_khoa_diem';
        $daDuyetDiem = $lopHocPhan->trang_thai_lop === 'da_duyet_diem';
        $laGiangVienChinh = $duocPhanCong->vai_tro === 'giang_vien_chinh';
        
        // Kiểm tra lớp đã kết thúc chưa (dựa vào ngày kết thúc)
        $daKetThuc = $lopHocPhan->daKetThuc();
        $dangDienRa = $lopHocPhan->dangDienRa();

        return view('giangvien.nhap-diem.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'sinhViens',
            'nhapDiems',
            'daKhoaDiem',
            'daDuyetDiem',
            'laGiangVienChinh',
            'daKetThuc',
            'dangDienRa'
        ));
    }

    /**
     * Nhập điểm thành phần (AJAX)
     */
    public function nhapDiem(Request $request)
    {
        // Validate - cho phép diem_so null để xóa điểm
        $validated = $request->validate([
            'lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
            'cau_hinh_id' => 'required|exists:cau_hinh_dau_diem,id',
            'cot_diem' => 'required|integer|min:1',
            'diem_so' => 'nullable|numeric|min:0|max:10', // Cho phép null để xóa điểm
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

        // Kiểm tra lớp đã kết thúc chưa - không cho phép sửa điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể sửa điểm sau khi lớp kết thúc.'
            ], 403);
        }

        // Cho phép sửa điểm trong mọi trường hợp (kể cả khi đã duyệt) nếu lớp chưa kết thúc
        // Giảng viên có thể sửa và gửi lại cho đào tạo phê duyệt lại

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

            // Nếu diem_so là null hoặc rỗng, xóa điểm
            if ($validated['diem_so'] === null || $validated['diem_so'] === '') {
                $deleted = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $validated['lop_hoc_phan_sinh_vien_id'])
                    ->where('cau_hinh_id', $validated['cau_hinh_id'])
                    ->where('cot_diem', $validated['cot_diem'])
                    ->delete();

                // Tự động tính lại điểm tổng
                $this->diemService->tinhDiemTong($validated['lop_hoc_phan_sinh_vien_id']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa điểm thành công',
                    'deleted' => $deleted > 0
                ]);
            }

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
     * Lấy điểm TK của sinh viên (AJAX) - bao gồm điểm tạm thời
     */
    public function getDiemTK(Request $request)
    {
        $validated = $request->validate([
            'lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
        ]);

        // Reload lại để lấy dữ liệu mới nhất
        $lhpsv = LopHocPhanSinhVien::with('ketQuaHocTap')->find($validated['lop_hoc_phan_sinh_vien_id']);
        $ketQua = $lhpsv->ketQuaHocTap;

        // Nếu có điểm chính thức thì trả về
        if ($ketQua && $ketQua->diem_he_10 !== null) {
            return response()->json([
                'success' => true,
                'diem_tk' => number_format($ketQua->diem_he_10, 2),
                'is_tam_thoi' => false
            ]);
        }

        // Nếu chưa có điểm chính thức, tính điểm tạm thời
        $diemTamThoi = $this->diemService->tinhDiemTKTamThoi($validated['lop_hoc_phan_sinh_vien_id']);

        return response()->json([
            'success' => true,
            'diem_tk' => $diemTamThoi !== null ? number_format($diemTamThoi, 2) : '-',
            'is_tam_thoi' => $diemTamThoi !== null
        ]);
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

        // Kiểm tra lớp đã kết thúc chưa - không cho phép khóa điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể khóa điểm sau khi lớp kết thúc.'
            ], 403);
        }

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

        // Kiểm tra lớp đã kết thúc chưa - không cho phép gửi điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể gửi điểm sau khi lớp kết thúc.'
            ], 403);
        }

        // Cho phép gửi lại điểm ngay cả khi đã duyệt (để giảng viên có thể sửa và gửi lại)
        // Không cần kiểm tra trạng thái, luôn cho phép gửi (nếu lớp chưa kết thúc)

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

            // Lưu trạng thái cũ để biết có phải gửi lại không
            $trangThaiCu = $lopHocPhan->trang_thai_lop;

            // Đặt trạng thái là đã khóa điểm (chờ duyệt)
            // Nếu đã duyệt trước đó, reset về chờ duyệt lại và xóa lý do trả về nếu có
            $lopHocPhan->update([
                'trang_thai_lop' => 'da_khoa_diem',
                'ly_do_tra_ve' => null, // Xóa lý do trả về nếu có
            ]);

            // Gửi thông báo cho đào tạo
            $this->guiThongBaoGuiDiemChoDaoTao($lopHocPhan, $giangVien, $trangThaiCu === 'da_duyet_diem');

            DB::commit();

            $message = $trangThaiCu === 'da_duyet_diem' 
                ? 'Đã gửi lại điểm cho đào tạo thành công. Chờ đào tạo duyệt lại.'
                : 'Đã gửi điểm cho đào tạo thành công. Chờ đào tạo duyệt.';

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
     * Gửi thông báo cho đào tạo khi giảng viên gửi điểm
     */
    private function guiThongBaoGuiDiemChoDaoTao($lopHocPhan, $giangVien, $laGuiLai = false)
    {
        // Lấy tất cả tài khoản đào tạo
        $daoTaos = \App\Models\DaoTao::with('user')->get();

        if ($daoTaos->isEmpty()) {
            return;
        }

        // Tạo thông báo
        $tieuDe = $laGuiLai 
            ? 'Giảng viên gửi lại điểm lớp ' . $lopHocPhan->ma_lop_hp
            : 'Giảng viên gửi điểm lớp ' . $lopHocPhan->ma_lop_hp;
        
        $noiDung = $laGuiLai
            ? "Giảng viên {$giangVien->ho_ten} đã chỉnh sửa và gửi lại điểm lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->monHoc->ten_mon} ({$lopHocPhan->hocKy->ten_hoc_ky}) để duyệt lại. Vui lòng truy cập phần 'Duyệt điểm' để xem và duyệt."
            : "Giảng viên {$giangVien->ho_ten} đã gửi điểm lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->monHoc->ten_mon} ({$lopHocPhan->hocKy->ten_hoc_ky}) để duyệt. Vui lòng truy cập phần 'Duyệt điểm' để xem và duyệt.";

        $thongBao = ThongBao::create([
            'tieu_de' => $tieuDe,
            'noi_dung' => $noiDung,
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

    /**
     * Download template Excel để nhập điểm
     */
    public function downloadTemplate($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->exists();

        if (!$duocPhanCong) {
            return redirect()->route('giangvien.nhap-diem.index')
                ->with('error', 'Bạn không có quyền truy cập lớp này');
        }

        $lopHocPhan = LopHocPhan::with(['monHoc'])->find($lopHocPhanId);
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien'])
            ->orderBy('sinh_vien_id')
            ->get();

        // Tạo file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nhập điểm');

        // Header row
        $headers = ['STT', 'MSSV', 'Họ tên'];
        $colIndex = 4; // Bắt đầu từ cột D

        foreach ($cauHinhs as $cauHinh) {
            if ($cauHinh->so_cot > 1) {
                for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                    $headers[] = $cauHinh->ten_dau_diem . ' - Cột ' . $cot;
                }
            } else {
                $headers[] = $cauHinh->ten_dau_diem;
            }
        }

        // Ghi header
        $sheet->fromArray([$headers], null, 'A1');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1')->applyFromArray($headerStyle);

        // Ghi dữ liệu sinh viên
        $row = 2;
        foreach ($sinhViens as $index => $lhpsv) {
            $rowData = [
                $index + 1,
                $lhpsv->sinhVien->ma_sinh_vien,
                $lhpsv->sinhVien->ho_ten,
            ];

            // Lấy điểm đã nhập
            $nhapDiems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)->get();
            $diemMap = [];
            foreach ($nhapDiems as $diem) {
                $key = $diem->cau_hinh_id . '_' . $diem->cot_diem;
                $diemMap[$key] = $diem->diem_so;
            }

            // Thêm điểm vào row
            foreach ($cauHinhs as $cauHinh) {
                for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                    $key = $cauHinh->id . '_' . $cot;
                    $rowData[] = $diemMap[$key] ?? '';
                }
            }

            $sheet->fromArray([$rowData], null, 'A' . $row);
            $row++;
        }

        // Auto size columns
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tạo file và download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Template_Nhap_Diem_' . $lopHocPhan->ma_lop_hp . '_' . date('YmdHis') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Import điểm từ Excel
     */
    public function importExcel(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->exists();

        if (!$duocPhanCong) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền nhập điểm lớp này'
            ], 403);
        }

        // Validate file
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel',
            'file.mimes' => 'File phải có định dạng Excel (.xlsx, .xls)',
            'file.max' => 'File không được vượt quá 5MB',
        ]);

        $lopHocPhan = LopHocPhan::find($lopHocPhanId);

        // Kiểm tra lớp đã kết thúc chưa - không cho phép import điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể import điểm sau khi lớp kết thúc.'
            ], 403);
        }

        // Cho phép import điểm trong mọi trường hợp (kể cả khi đã duyệt) nếu lớp chưa kết thúc
        // Giảng viên có thể sửa và gửi lại cho đào tạo phê duyệt lại

        try {
            DB::beginTransaction();

            // Đọc file Excel
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            // Bỏ qua header (dòng đầu tiên)
            array_shift($data);

            // Lấy cấu hình đầu điểm
            $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
                ->orderBy('id')
                ->get();

            // Lấy danh sách sinh viên
            $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->with(['sinhVien'])
                ->get()
                ->keyBy(function ($item) {
                    return $item->sinhVien->ma_sinh_vien;
                });

            $imported = 0;
            $errors = [];
            $colIndex = 3; // Bắt đầu từ cột D (index 3)

            foreach ($data as $rowNum => $row) {
                $rowNum += 2; // +2 vì bỏ header và index bắt đầu từ 0

                // Bỏ qua dòng trống
                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }

                $maSV = trim($row[1]);
                $sinhVien = $sinhViens->get($maSV);

                if (!$sinhVien) {
                    $errors[] = "Dòng {$rowNum}: Không tìm thấy sinh viên có MSSV: {$maSV}";
                    continue;
                }

                $colIndex = 3; // Reset về cột D
                foreach ($cauHinhs as $cauHinh) {
                    for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                        $diemValue = $row[$colIndex] ?? null;
                        $colIndex++;

                        // Bỏ qua nếu không có giá trị
                        if ($diemValue === null || $diemValue === '') {
                            continue;
                        }

                        // Validate điểm
                        $diemSo = is_numeric($diemValue) ? (float)$diemValue : null;
                        if ($diemSo === null || $diemSo < 0 || $diemSo > 10) {
                            $errors[] = "Dòng {$rowNum} - {$cauHinh->ten_dau_diem} (Cột {$cot}): Điểm không hợp lệ ({$diemValue})";
                            continue;
                        }

                        // Lưu điểm
                        try {
                            NhapDiem::updateOrCreate(
                                [
                                    'lop_hoc_phan_sinh_vien_id' => $sinhVien->id,
                                    'cau_hinh_id' => $cauHinh->id,
                                    'cot_diem' => $cot,
                                ],
                                [
                                    'diem_so' => $diemSo,
                                ]
                            );

                            // Tự động tính điểm tổng
                            $this->diemService->tinhDiemTong($sinhVien->id);
                            $imported++;
                        } catch (\Exception $e) {
                            $errors[] = "Dòng {$rowNum} - {$cauHinh->ten_dau_diem} (Cột {$cot}): " . $e->getMessage();
                        }
                    }
                }
            }

            DB::commit();

            $message = "Import thành công {$imported} điểm.";
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => array_slice($errors, 0, 10), // Chỉ hiển thị 10 lỗi đầu
                'total_errors' => count($errors),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi import: ' . $e->getMessage()
            ], 500);
        }
    }
}