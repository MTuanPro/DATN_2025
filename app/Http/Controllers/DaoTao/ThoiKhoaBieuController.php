<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LichHocChiTiet;
use App\Models\LichHocCoDinh;
use App\Models\Daotao\PhongHoc;
use App\Models\GiangVien;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ThoiKhoaBieuController extends Controller
{
    /**
     * Hiển thị lịch học theo phòng học
     * 
     * Cho phép lọc lịch học theo phòng, học kỳ và khoảng thời gian
     * 
     * @param Request $request Chứa các tham số lọc:
     *                         - phong_hoc_id: ID phòng học cần xem lịch
     *                         - hoc_ky_id: ID học kỳ
     *                         - tu_ngay: Ngày bắt đầu (YYYY-MM-DD)
     *                         - den_ngay: Ngày kết thúc (YYYY-MM-DD)
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function lichTheoPhong(Request $request)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Lọc theo phòng học
        $phongHocId = $request->get('phong_hoc_id');
        $hocKyId = $request->get('hoc_ky_id');
        $tuNgay = $request->get('tu_ngay');
        $denNgay = $request->get('den_ngay');

        $query = LichHocChiTiet::with([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'phongHoc',
            'giangVien',
            'lichHocCoDinh'
        ]);

        if ($phongHocId) {
            $query->where('phong_hoc_id', $phongHocId);
        }

        if ($hocKyId) {
            $query->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            });
        }

        if ($tuNgay) {
            $query->where('ngay_hoc', '>=', $tuNgay);
        }

        if ($denNgay) {
            $query->where('ngay_hoc', '<=', $denNgay);
        }

        $lichHocs = $query->orderBy('ngay_hoc', 'asc')
            ->orderBy('tiet_bat_dau', 'asc')
            ->paginate(20);

        return view('daotao.thoi-khoa-bieu.lich-theo-phong', compact('lichHocs', 'phongHocs', 'hocKys'));
    }

    /**
     * Hiển thị lịch giảng dạy theo giảng viên
     * 
     * Cho phép xem lịch giảng dạy của một giảng viên cụ thể
     * 
     * @param Request $request Chứa các tham số lọc:
     *                         - giang_vien_id: ID giảng viên cần xem lịch
     *                         - hoc_ky_id: ID học kỳ
     *                         - tu_ngay: Ngày bắt đầu (YYYY-MM-DD)
     *                         - den_ngay: Ngày kết thúc (YYYY-MM-DD)
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function lichTheoGiangVien(Request $request)
    {
        $giangViens = GiangVien::orderBy('ho_ten')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        // Lọc theo giảng viên
        $giangVienId = $request->get('giang_vien_id');
        $hocKyId = $request->get('hoc_ky_id');
        $tuNgay = $request->get('tu_ngay');
        $denNgay = $request->get('den_ngay');

        $query = LichHocChiTiet::with([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'phongHoc',
            'giangVien',
            'lichHocCoDinh'
        ]);

        if ($giangVienId) {
            $query->where('giang_vien_id', $giangVienId);
        }

        if ($hocKyId) {
            $query->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            });
        }

        if ($tuNgay) {
            $query->where('ngay_hoc', '>=', $tuNgay);
        }

        if ($denNgay) {
            $query->where('ngay_hoc', '<=', $denNgay);
        }

        $lichHocs = $query->orderBy('ngay_hoc', 'asc')
            ->orderBy('tiet_bat_dau', 'asc')
            ->paginate(20);

        return view('daotao.thoi-khoa-bieu.lich-theo-giang-vien', compact('lichHocs', 'giangViens', 'hocKys'));
    }
}

