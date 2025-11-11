<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhanCongGiangDay;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\HocKy;
use App\Models\LichHocCoDinh;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeachingClassController extends Controller
{
    /**
     * Hiển thị danh sách lớp học phần được phân công
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Lấy danh sách học kỳ để filter
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Filter theo học kỳ
        $hocKyId = $request->get('hoc_ky_id');
        
        // Lấy danh sách lớp được phân công
        $query = PhanCongGiangDay::with([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'lopHocPhan.giangVienChinh.giangVien'
        ])
        ->where('giang_vien_id', $giangVien->id)
        // Chỉ lấy lớp có môn học hợp lệ
        ->whereHas('lopHocPhan', function ($q) {
            $q->whereNotNull('mon_hoc_id')
              ->whereHas('monHoc');
        });

        if ($hocKyId) {
            $query->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            });
        }

        $phanCongs = $query->orderBy('created_at', 'desc')->paginate(15);

        // Thêm thông tin số sinh viên cho mỗi lớp
        foreach ($phanCongs as $phanCong) {
            $phanCong->lopHocPhan->so_sinh_vien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $phanCong->lop_hoc_phan_id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->count();
        }

        return view('giangvien.lop-giang-day.index', compact('phanCongs', 'hocKys', 'hocKyId'));
    }

    /**
     * Hiển thị chi tiết lớp học phần và danh sách sinh viên
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Kiểm tra quyền: giảng viên phải được phân công dạy lớp này
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $id)
            ->where('giang_vien_id', $giangVien->id)
            ->firstOrFail();

        // Load thông tin lớp học phần
        $lopHocPhan = LopHocPhan::with([
            'monHoc',
            'hocKy',
            'lopHocPhanGiangVien.giangVien',
            'cauHinhDauDiem'
        ])->findOrFail($id);

        // Lấy danh sách sinh viên trong lớp
        $sinhViens = LopHocPhanSinhVien::with([
            'sinhVien.lopHanhChinh',
            'ketQuaHocTap'
        ])
        ->where('lop_hoc_phan_id', $id)
        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
        ->orderBy('trang_thai', 'asc')
        ->get();

        // Lấy thông tin lịch học cố định
        $lichHocCoDinh = LichHocCoDinh::where('lop_hoc_phan_id', $id)
            ->with('phongHoc')
            ->get();

        return view('giangvien.lop-giang-day.show', compact(
            'lopHocPhan',
            'phanCong',
            'sinhViens',
            'lichHocCoDinh'
        ));
    }

    /**
     * Xuất danh sách sinh viên ra Excel
     */
    public function exportStudents(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $id)
            ->where('giang_vien_id', $giangVien->id)
            ->firstOrFail();

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($id);
        
        $sinhViens = LopHocPhanSinhVien::with([
            'sinhVien.lopHanhChinh',
            'ketQuaHocTap'
        ])
        ->where('lop_hoc_phan_id', $id)
        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
        ->get();

        // Tạo file CSV
        $fileName = 'danh-sach-sinh-vien-' . $lopHocPhan->ma_lop_hp . '-' . Carbon::now()->format('YmdHis') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($sinhViens, $lopHocPhan) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'STT',
                'Mã sinh viên',
                'Họ và tên',
                'Email',
                'Số điện thoại',
                'Lớp hành chính',
                'Trạng thái',
                'Ngày đăng ký',
                'Ngày xếp lớp'
            ]);

            // Data
            $stt = 1;
            foreach ($sinhViens as $lhpsv) {
                fputcsv($file, [
                    $stt++,
                    $lhpsv->sinhVien->ma_sinh_vien ?? '',
                    $lhpsv->sinhVien->ho_ten ?? '',
                    $lhpsv->sinhVien->email ?? '',
                    $lhpsv->sinhVien->so_dien_thoai ?? '',
                    $lhpsv->sinhVien->lopHanhChinh->ma_lop ?? '',
                    $this->getTrangThaiText($lhpsv->trang_thai),
                    $lhpsv->ngay_dang_ky ? $lhpsv->ngay_dang_ky->format('d/m/Y H:i') : '',
                    $lhpsv->ngay_xep_lop ? $lhpsv->ngay_xep_lop->format('d/m/Y H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xuất danh sách sinh viên ra PDF
     */
    public function exportStudentsPdf(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $id)
            ->where('giang_vien_id', $giangVien->id)
            ->firstOrFail();

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($id);
        
        $sinhViens = LopHocPhanSinhVien::with([
            'sinhVien.lopHanhChinh',
            'ketQuaHocTap'
        ])
        ->where('lop_hoc_phan_id', $id)
        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
        ->get();

        // Return view for PDF printing
        return view('giangvien.lop-giang-day.export-pdf', compact('lopHocPhan', 'sinhViens', 'giangVien'));
    }

    /**
     * Helper: Convert trạng thái to Vietnamese text
     */
    private function getTrangThaiText($trangThai)
    {
        $mapping = [
            'da_xep_lop' => 'Đã xếp lớp',
            'dang_hoc' => 'Đang học',
            'da_hoan_thanh' => 'Đã hoàn thành',
            'bo_hoc' => 'Bỏ học',
            'huy_dang_ky' => 'Hủy đăng ký'
        ];

        return $mapping[$trangThai] ?? $trangThai;
    }
}
