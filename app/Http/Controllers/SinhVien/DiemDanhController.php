<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\LichHocChiTiet;
use App\Models\DiemDanh;
use App\Models\YeuCauDiemDanhBu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DiemDanhController extends Controller
{
    /**
     * Hiển thị danh sách lịch học hôm nay để điểm danh
     */
    public function index()
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        $today = Carbon::today();
        $now = Carbon::now();

        // Lấy các lịch học hôm nay của sinh viên
        $lichHocHomNay = LichHocChiTiet::whereDate('ngay_hoc', $today)
            ->whereHas('lopHocPhan.sinhViens', function ($query) use ($sinhVien) {
                $query->where('sinh_vien_id', $sinhVien->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc']);
            })
            ->with([
                'lopHocPhan.monHoc',
                'phongHoc',
                'giangVien',
                'caHoc'
            ])
            ->orderBy('gio_bat_dau')
            ->get();

        // Kiểm tra trạng thái điểm danh và thời gian cho từng lịch học
        foreach ($lichHocHomNay as $lich) {
            // Lấy thông tin điểm danh của sinh viên
            $diemDanh = DiemDanh::where('lich_hoc_chi_tiet_id', $lich->id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();

            $lich->diemDanh = $diemDanh;

            // Tính thời gian bắt đầu lớp học
            $gioBatDau = $lich->gio_bat_dau ?? ($lich->caHoc ? $lich->caHoc->gio_bat_dau : null);
            
            if ($gioBatDau) {
                $thoiGianBatDau = Carbon::parse($lich->ngay_hoc . ' ' . $gioBatDau);
                $thoiGianKetThucDiemDanh = $thoiGianBatDau->copy()->addMinutes(40);
                
                $lich->thoi_gian_bat_dau = $thoiGianBatDau;
                $lich->thoi_gian_ket_thuc_diem_danh = $thoiGianKetThucDiemDanh;
                
                // Kiểm tra có thể điểm danh không
                $lich->co_the_diem_danh = $now->between($thoiGianBatDau, $thoiGianKetThucDiemDanh) && !$diemDanh;
                $lich->qua_gio_diem_danh = $now->greaterThan($thoiGianKetThucDiemDanh);
                $lich->chua_den_gio = $now->lessThan($thoiGianBatDau);
                
                // Tính số phút còn lại
                if ($lich->co_the_diem_danh) {
                    $lich->phut_con_lai = max(0, $now->diffInMinutes($thoiGianKetThucDiemDanh, false));
                }
            } else {
                $lich->co_the_diem_danh = false;
                $lich->qua_gio_diem_danh = false;
                $lich->chua_den_gio = true;
            }

            // Kiểm tra yêu cầu điểm danh bù
            $lich->yeuCauDiemDanhBu = YeuCauDiemDanhBu::where('lich_hoc_chi_tiet_id', $lich->id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();
        }

        return view('sinhvien.diem-danh.diem-danh-hom-nay', compact('lichHocHomNay', 'sinhVien', 'now'));
    }

    /**
     * Thực hiện điểm danh
     */
    public function store(Request $request, $lichHocChiTietId)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return back()->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        $lichHoc = LichHocChiTiet::findOrFail($lichHocChiTietId);

        // Kiểm tra sinh viên có trong lớp học phần không
        $coTrongLop = DB::table('lop_hoc_phan_sinh_vien')
            ->where('lop_hoc_phan_id', $lichHoc->lop_hoc_phan_id)
            ->where('sinh_vien_id', $sinhVien->id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->exists();

        if (!$coTrongLop) {
            return back()->with('error', 'Bạn không có trong lớp học phần này!');
        }

        // Kiểm tra đã điểm danh chưa
        $daDiemDanh = DiemDanh::where('lich_hoc_chi_tiet_id', $lichHoc->id)
            ->where('sinh_vien_id', $sinhVien->id)
            ->exists();

        if ($daDiemDanh) {
            return back()->with('error', 'Bạn đã điểm danh cho buổi học này rồi!');
        }

        // Kiểm tra thời gian điểm danh (40 phút từ khi bắt đầu)
        $now = Carbon::now();
        $gioBatDau = $lichHoc->gio_bat_dau ?? ($lichHoc->caHoc ? $lichHoc->caHoc->gio_bat_dau : null);
        
        if (!$gioBatDau) {
            return back()->with('error', 'Không xác định được giờ bắt đầu của lớp học!');
        }

        $thoiGianBatDau = Carbon::parse($lichHoc->ngay_hoc . ' ' . $gioBatDau);
        $thoiGianKetThucDiemDanh = $thoiGianBatDau->copy()->addMinutes(40);

        // Kiểm tra có trong khoảng thời gian cho phép điểm danh không
        if ($now->lessThan($thoiGianBatDau)) {
            return back()->with('error', 'Chưa đến giờ điểm danh!');
        }

        if ($now->greaterThan($thoiGianKetThucDiemDanh)) {
            return back()->with('error', 'Đã quá thời gian điểm danh (40 phút từ khi bắt đầu lớp). Vui lòng yêu cầu điểm danh bù nếu cần.');
        }

        // Xác định trạng thái điểm danh
        $phutTre = max(0, $thoiGianBatDau->diffInMinutes($now, false));
        $trangThai = $phutTre > 15 ? 'muon' : 'co_mat';

        // Tạo bản ghi điểm danh
        DiemDanh::create([
            'lich_hoc_chi_tiet_id' => $lichHoc->id,
            'sinh_vien_id' => $sinhVien->id,
            'trang_thai' => $trangThai,
            'thoi_gian_diem_danh' => $now,
            'ghi_chu' => $trangThai === 'muon' ? "Đến muộn $phutTre phút" : 'Điểm danh đúng giờ',
        ]);

        $message = $trangThai === 'muon' 
            ? "Điểm danh thành công! Bạn đã đến muộn $phutTre phút." 
            : 'Điểm danh thành công!';

        return back()->with('success', $message);
    }
}
