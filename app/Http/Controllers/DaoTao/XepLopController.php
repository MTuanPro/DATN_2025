<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\DangKyMonHocTam;
use App\Models\LopHocPhanSinhVien;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XepLopController extends Controller
{
    /**
     * Hiển thị danh sách đăng ký chờ xếp lớp
     */
    public function index(Request $request)
    {
        $query = DangKyMonHocTam::with(['sinhVien', 'monHoc', 'hocKy'])
            ->orderBy('uu_tien', 'desc')
            ->orderBy('ngay_dang_ky', 'asc');

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        // Mặc định hiển thị tất cả trạng thái

        // Lọc theo môn học
        if ($request->filled('mon_hoc_id')) {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        $dangKys = $query->paginate(20);

        // Thống kê
        $thongKe = [
            'cho_dong_hoc_phi' => DangKyMonHocTam::where('trang_thai', 'cho_dong_hoc_phi')->count(),
            'cho_xep_lop' => DangKyMonHocTam::choXepLop()->count(),
            'da_xep_lop' => DangKyMonHocTam::daXepLop()->count(),
            'that_bai' => DangKyMonHocTam::thatBai()->count(),
        ];

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.xep-lop.index', compact('dangKys', 'thongKe', 'hocKys'));
    }

    /**
     * Xếp lớp tự động cho tất cả đăng ký
     */
    public function autoAssign(Request $request)
    {
        $request->validate([
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
        ]);

        $hocKyId = $request->hoc_ky_id;

        DB::beginTransaction();
        try {
            // Lấy tất cả đăng ký chờ xếp lớp, sắp xếp theo độ ưu tiên
            $dangKys = DangKyMonHocTam::where('hoc_ky_id', $hocKyId)
                ->where('trang_thai', 'cho_xep_lop')
                ->orderBy('uu_tien', 'desc')
                ->orderBy('ngay_dang_ky', 'asc')
                ->get();

            $soLuongXepThanhCong = 0;
            $soLuongThatBai = 0;

            foreach ($dangKys as $dangKy) {
                $result = $this->xepLopChoSinhVien($dangKy);

                if ($result['success']) {
                    $soLuongXepThanhCong++;
                } else {
                    $soLuongThatBai++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Xếp lớp hoàn tất! Thành công: {$soLuongXepThanhCong}, Thất bại: {$soLuongThatBai}",
                'data' => [
                    'thanh_cong' => $soLuongXepThanhCong,
                    'that_bai' => $soLuongThatBai
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xếp lớp tự động: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xếp lớp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xếp lớp cho một sinh viên cụ thể
     */
    private function xepLopChoSinhVien($dangKy)
    {
        // Tìm lớp học phần phù hợp
        $lopHocPhans = LopHocPhan::where('mon_hoc_id', $dangKy->mon_hoc_id)
            ->where('hoc_ky_id', $dangKy->hoc_ky_id)
            ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
            ->get();

        foreach ($lopHocPhans as $lopHocPhan) {
            // Lấy sức chứa và số lượng thực tế từ bảng lop_hoc_phan_sinh_vien
            $sucChua = $lopHocPhan->suc_chua ?? 0;
            $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                ->count();

            // Kiểm tra còn chỗ không
            if ($soLuongThucTe < $sucChua) {
                // Đồng bộ so_luong_dang_ky với số lượng thực tế trước khi xếp lớp
                if ($lopHocPhan->so_luong_dang_ky != $soLuongThucTe) {
                    $lopHocPhan->so_luong_dang_ky = $soLuongThucTe;
                    $lopHocPhan->save();
                }

                // Kiểm tra sinh viên đã đăng ký lớp này chưa
                $exists = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('sinh_vien_id', $dangKy->sinh_vien_id)
                    ->exists();

                if ($exists) {
                    continue; // Đã đăng ký rồi, thử lớp khác
                }

                // Xếp vào lớp này
                // Observer sẽ tự động cập nhật so_luong_dang_ky, không cần cập nhật thủ công
                LopHocPhanSinhVien::create([
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'sinh_vien_id' => $dangKy->sinh_vien_id,
                    'dang_ky_tam_id' => $dangKy->id,
                    'ngay_dang_ky' => $dangKy->ngay_dang_ky,
                    'ngay_xep_lop' => now(),
                    'phuong_thuc_xep' => 'tu_dong',
                    'trang_thai' => 'da_xep_lop',
                ]);

                // Cập nhật trạng thái đăng ký tạm
                $dangKy->update([
                    'trang_thai' => 'da_xep_lop',
                ]);

                return ['success' => true, 'lop_hoc_phan_id' => $lopHocPhan->id];
            }
        }

        // Không tìm thấy lớp phù hợp
        $dangKy->update([
            'trang_thai' => 'that_bai',
            'ly_do_that_bai' => 'Không còn chỗ trong các lớp học phần'
        ]);

        return ['success' => false, 'message' => 'Không còn chỗ'];
    }

    /**
     * Xếp lớp thủ công
     */
    public function manualAssign(Request $request)
    {
        $request->validate([
            'dang_ky_tam_id' => 'required|exists:dang_ky_mon_hoc_tam,id',
            'lop_hoc_phan_id' => 'required|exists:lop_hoc_phan,id',
        ]);

        DB::beginTransaction();
        try {
            $dangKy = DangKyMonHocTam::findOrFail($request->dang_ky_tam_id);
            $lopHocPhan = LopHocPhan::findOrFail($request->lop_hoc_phan_id);

            // Kiểm tra lớp còn chỗ không - tính từ bảng thực tế
            $sucChua = $lopHocPhan->suc_chua ?? 0;
            $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                ->count();

            if ($soLuongThucTe >= $sucChua) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học phần đã đầy!'
                ], 400);
            }

            // Đồng bộ so_luong_dang_ky với số lượng thực tế trước khi xếp lớp
            if ($lopHocPhan->so_luong_dang_ky != $soLuongThucTe) {
                $lopHocPhan->so_luong_dang_ky = $soLuongThucTe;
                $lopHocPhan->save();
            }

            // Kiểm tra sinh viên đã đăng ký lớp này chưa
            $exists = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('sinh_vien_id', $dangKy->sinh_vien_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sinh viên đã đăng ký lớp này rồi!'
                ], 400);
            }

            // Xếp vào lớp
            // Observer sẽ tự động cập nhật so_luong_dang_ky, không cần cập nhật thủ công
            LopHocPhanSinhVien::create([
                'lop_hoc_phan_id' => $lopHocPhan->id,
                'sinh_vien_id' => $dangKy->sinh_vien_id,
                'dang_ky_tam_id' => $dangKy->id,
                'ngay_dang_ky' => $dangKy->ngay_dang_ky,
                'ngay_xep_lop' => now(),
                'nguoi_duyet_id' => Auth::id(),
                'ngay_duyet' => now(),
                'phuong_thuc_xep' => 'thu_cong',
                'trang_thai' => 'da_xep_lop',
            ]);

            // Cập nhật trạng thái đăng ký tạm
            $dangKy->update([
                'trang_thai' => 'da_xep_lop',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xếp lớp thủ công thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xếp lớp thủ công: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Danh sách chờ (waiting list)
     */
    public function waitingList(Request $request)
    {
        $query = DangKyMonHocTam::with(['sinhVien', 'monHoc', 'hocKy'])
            ->where('trang_thai', 'that_bai')
            ->orderBy('uu_tien', 'desc')
            ->orderBy('ngay_dang_ky', 'asc');

        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        $waitingList = $query->paginate(20);
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.xep-lop.waiting-list', compact('waitingList', 'hocKys'));
    }

    /**
     * Xem danh sách sinh viên trong lớp học phần
     */
    public function danhSachLop($lopHocPhanId)
    {
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'giangVienChinh.giangVien'])
            ->findOrFail($lopHocPhanId);

        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->with(['sinhVien.lopHanhChinh', 'dangKyTam'])
            ->orderBy('ngay_xep_lop', 'asc')
            ->get();

        return view('daotao.xep-lop.danh-sach-lop', compact('lopHocPhan', 'sinhViens'));
    }

    /**
     * Lấy danh sách lớp học phần theo môn học (cho modal xếp lớp thủ công)
     */
    public function getLopHocPhanByMonHoc($monHocId, Request $request)
    {
        $hocKyId = $request->get('hoc_ky_id');

        $lopHocPhans = LopHocPhan::where('mon_hoc_id', $monHocId)
            ->when($hocKyId, function ($query) use ($hocKyId) {
                $query->where('hoc_ky_id', $hocKyId);
            })
            ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
            ->with('monHoc')
            ->get()
            ->map(function ($lop) {
                $sucChua = $lop->suc_chua ?? 0;
                // Tính số lượng thực tế từ bảng lop_hoc_phan_sinh_vien thay vì dùng so_luong_dang_ky
                $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                    ->count();
                $conTrong = $sucChua - $soLuongThucTe;

                return [
                    'id' => $lop->id,
                    'ma_lop_hoc_phan' => $lop->ma_lop_hp,
                    'ten_lop_hoc_phan' => $lop->ten_lop_hp,
                    'so_luong_toi_da' => $sucChua,
                    'so_luong_hien_tai' => $soLuongThucTe,
                    'con_trong' => $conTrong,
                ];
            })
            ->filter(function ($lop) {
                return $lop['con_trong'] > 0;
            });

        return response()->json([
            'success' => true,
            'data' => $lopHocPhans->values()
        ]);
    }

    /**
     * Xóa sinh viên khỏi lớp học phần
     */
    public function xoaKhoiLop($lhpsvId)
    {
        try {
            DB::beginTransaction();

            $lhpsv = LopHocPhanSinhVien::findOrFail($lhpsvId);

            // Kiểm tra trạng thái
            if (!in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa sinh viên với trạng thái này!'
                ], 400);
            }

            // Observer sẽ tự động cập nhật so_luong_dang_ky khi xóa, không cần cập nhật thủ công
            $lopHocPhan = $lhpsv->lopHocPhan;

            // Cập nhật trạng thái đăng ký tạm về chờ xếp lớp
            if ($lhpsv->dang_ky_tam_id) {
                DangKyMonHocTam::where('id', $lhpsv->dang_ky_tam_id)
                    ->update([
                        'trang_thai' => 'cho_xep_lop',
                        'ly_do_that_bai' => null
                    ]);
            }

            // Xóa bản ghi xếp lớp
            $lhpsv->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sinh viên khỏi lớp thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xóa sinh viên khỏi lớp: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
