<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\GiangVien;
use App\Models\LichHocChiTiet;
use App\Models\DiemDanh;
use App\Models\LopHocPhan;
use App\Models\KetQuaHocTap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BaoCaoController extends Controller
{
    /**
     * Trang chủ báo cáo giảng dạy
     */
    public function index()
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách lớp giảng dạy
        $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy'])
            ->get();

        // Thống kê tổng quan
        $tongLop = $lopHocPhans->count();
        
        // Thống kê buổi học
        $tongBuoiHoc = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->count();
        
        $buoiDaDay = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->where('trang_thai', 'da_day')
            ->count();

        // Thống kê điểm danh
        $tongDiemDanh = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
            ->whereIn('lich_hoc_chi_tiet.lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->count();

        $diemDanhCoMat = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
            ->whereIn('lich_hoc_chi_tiet.lop_hoc_phan_id', $lopHocPhans->pluck('id'))
            ->where('diem_danh.trang_thai', 'co_mat')
            ->count();

        $tyLeCoMat = $tongDiemDanh > 0 ? round(($diemDanhCoMat / $tongDiemDanh) * 100, 2) : 0;

        return view('giangvien.bao-cao.index', compact(
            'giangVien',
            'lopHocPhans',
            'tongLop',
            'tongBuoiHoc',
            'buoiDaDay',
            'tongDiemDanh',
            'tyLeCoMat'
        ));
    }

    /**
     * Báo cáo tiến độ giảng dạy
     */
    public function tienDoGiangDay(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Filters
        $hocKyId = $request->input('hoc_ky_id');
        $lopHocPhanId = $request->input('lop_hoc_phan_id');

        // Lấy danh sách lớp
        $lopHocPhansQuery = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy']);

        if ($hocKyId) {
            $lopHocPhansQuery->where('lop_hoc_phan.hoc_ky_id', $hocKyId);
        }

        if ($lopHocPhanId) {
            $lopHocPhansQuery->where('lop_hoc_phan.id', $lopHocPhanId);
        }

        $lopHocPhans = $lopHocPhansQuery->get();

        // Thống kê chi tiết cho từng lớp
        $thongKe = [];
        foreach ($lopHocPhans as $lop) {
            $tongBuoi = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
            $daDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                ->where('trang_thai', 'da_day')
                ->count();
            $chuaDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                ->where('trang_thai', 'chua_day')
                ->count();

            $tiLe = $tongBuoi > 0 ? round(($daDayCount / $tongBuoi) * 100, 2) : 0;

            $thongKe[] = [
                'lop' => $lop,
                'tong_buoi' => $tongBuoi,
                'da_day' => $daDayCount,
                'chua_day' => $chuaDayCount,
                'ti_le' => $tiLe,
            ];
        }

        // Lấy danh sách học kỳ để filter
        $hocKys = DB::table('hoc_ky')->orderBy('nam_hoc', 'desc')->get();

        // Lấy danh sách lớp để filter
        $allLopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with('monHoc')
            ->get();

        return view('giangvien.bao-cao.tien-do', compact(
            'giangVien',
            'thongKe',
            'hocKys',
            'allLopHocPhans',
            'hocKyId',
            'lopHocPhanId'
        ));
    }

    /**
     * Báo cáo điểm danh
     */
    public function diemDanh(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Filters
        $hocKyId = $request->input('hoc_ky_id');
        $lopHocPhanId = $request->input('lop_hoc_phan_id');

        // Lấy danh sách lớp
        $lopHocPhansQuery = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy']);

        if ($hocKyId) {
            $lopHocPhansQuery->where('lop_hoc_phan.hoc_ky_id', $hocKyId);
        }

        if ($lopHocPhanId) {
            $lopHocPhansQuery->where('lop_hoc_phan.id', $lopHocPhanId);
        }

        $lopHocPhans = $lopHocPhansQuery->get();

        // Thống kê điểm danh cho từng lớp
        $thongKe = [];
        foreach ($lopHocPhans as $lop) {
            $tongDiemDanh = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->count();

            $coMat = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'co_mat')
                ->count();

            $vang = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'vang')
                ->count();

            $diTre = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'di_tre')
                ->count();

            $nghiPhep = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                ->where('diem_danh.trang_thai', 'nghi_phep')
                ->count();

            $tyLeCoMat = $tongDiemDanh > 0 ? round(($coMat / $tongDiemDanh) * 100, 2) : 0;

            $thongKe[] = [
                'lop' => $lop,
                'tong' => $tongDiemDanh,
                'co_mat' => $coMat,
                'vang' => $vang,
                'di_tre' => $diTre,
                'nghi_phep' => $nghiPhep,
                'ty_le_co_mat' => $tyLeCoMat,
            ];
        }

        // Lấy danh sách học kỳ và lớp để filter
        $hocKys = DB::table('hoc_ky')->orderBy('nam_hoc', 'desc')->get();
        $allLopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with('monHoc')
            ->get();

        return view('giangvien.bao-cao.diem-danh', compact(
            'giangVien',
            'thongKe',
            'hocKys',
            'allLopHocPhans',
            'hocKyId',
            'lopHocPhanId'
        ));
    }

    /**
     * Báo cáo phân tích điểm
     */
    public function phanTichDiem(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Filters
        $hocKyId = $request->input('hoc_ky_id');
        $lopHocPhanId = $request->input('lop_hoc_phan_id');

        // Lấy danh sách lớp
        $lopHocPhansQuery = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc', 'hocKy']);

        if ($hocKyId) {
            $lopHocPhansQuery->where('lop_hoc_phan.hoc_ky_id', $hocKyId);
        }

        if ($lopHocPhanId) {
            $lopHocPhansQuery->where('lop_hoc_phan.id', $lopHocPhanId);
        }

        $lopHocPhans = $lopHocPhansQuery->get();

        // Thống kê điểm cho từng lớp
        $thongKe = [];
        foreach ($lopHocPhans as $lop) {
            // Lấy kết quả học tập
            $ketQuas = KetQuaHocTap::join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
                ->where('lop_hoc_phan_sinh_vien.lop_hoc_phan_id', $lop->id)
                ->select('ket_qua_hoc_tap.*')
                ->get();

            $tongSinhVien = $ketQuas->count();
            $quaMon = $ketQuas->where('qua_mon', true)->count();
            $khongQuaMon = $ketQuas->where('qua_mon', false)->count();

            // Điểm trung bình
            $diemTrungBinh = $ketQuas->avg('diem_he_10');

            // Phân bố theo điểm chữ
            $phanBoDiem = $ketQuas->groupBy('diem_chu')->map->count();

            $tyLeQuaMon = $tongSinhVien > 0 ? round(($quaMon / $tongSinhVien) * 100, 2) : 0;

            $thongKe[] = [
                'lop' => $lop,
                'tong_sv' => $tongSinhVien,
                'qua_mon' => $quaMon,
                'khong_qua_mon' => $khongQuaMon,
                'ty_le_qua_mon' => $tyLeQuaMon,
                'diem_trung_binh' => $diemTrungBinh ? round($diemTrungBinh, 2) : 0,
                'phan_bo_diem' => $phanBoDiem,
            ];
        }

        // Lấy danh sách học kỳ và lớp để filter
        $hocKys = DB::table('hoc_ky')->orderBy('nam_hoc', 'desc')->get();
        $allLopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with('monHoc')
            ->get();

        return view('giangvien.bao-cao.phan-tich-diem', compact(
            'giangVien',
            'thongKe',
            'hocKys',
            'allLopHocPhans',
            'hocKyId',
            'lopHocPhanId'
        ));
    }

    /**
     * Xuất báo cáo Excel
     */
    public function exportExcel(Request $request)
    {
        $loaiBaoCao = $request->input('loai', 'tien-do');
        $user = Auth::user();
        $giangVien = $user->giangVien;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'BÁO CÁO GIẢNG DẠY CÁ NHÂN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Giảng viên: ' . $giangVien->ho_ten);
        $sheet->mergeCells('A2:F2');
        
        $sheet->setCellValue('A3', 'Ngày xuất: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A3:F3');

        $row = 5;

        if ($loaiBaoCao === 'tien-do') {
            // Báo cáo tiến độ
            $sheet->setCellValue('A' . $row, 'STT');
            $sheet->setCellValue('B' . $row, 'Mã lớp');
            $sheet->setCellValue('C' . $row, 'Môn học');
            $sheet->setCellValue('D' . $row, 'Tổng buổi');
            $sheet->setCellValue('E' . $row, 'Đã dạy');
            $sheet->setCellValue('F' . $row, 'Tiến độ (%)');
            
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $stt = 1;

            $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
                ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
                ->with(['monHoc'])
                ->get();

            foreach ($lopHocPhans as $lop) {
                $tongBuoi = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
                $daDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                    ->where('trang_thai', 'da_day')
                    ->count();
                $tiLe = $tongBuoi > 0 ? round(($daDayCount / $tongBuoi) * 100, 2) : 0;

                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $lop->ma_lop_hp);
                $sheet->setCellValue('C' . $row, $lop->monHoc->ten_mon ?? '');
                $sheet->setCellValue('D' . $row, $tongBuoi);
                $sheet->setCellValue('E' . $row, $daDayCount);
                $sheet->setCellValue('F' . $row, $tiLe);
                $row++;
            }
        } elseif ($loaiBaoCao === 'diem-danh') {
            // Báo cáo điểm danh
            $sheet->setCellValue('A' . $row, 'STT');
            $sheet->setCellValue('B' . $row, 'Mã lớp');
            $sheet->setCellValue('C' . $row, 'Môn học');
            $sheet->setCellValue('D' . $row, 'Có mặt');
            $sheet->setCellValue('E' . $row, 'Vắng');
            $sheet->setCellValue('F' . $row, 'Tỷ lệ có mặt (%)');
            
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $stt = 1;

            $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
                ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
                ->with(['monHoc'])
                ->get();

            foreach ($lopHocPhans as $lop) {
                $tongDiemDanh = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                    ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                    ->count();

                $coMat = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                    ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                    ->where('diem_danh.trang_thai', 'co_mat')
                    ->count();

                $vang = DiemDanh::join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                    ->where('lich_hoc_chi_tiet.lop_hoc_phan_id', $lop->id)
                    ->where('diem_danh.trang_thai', 'vang')
                    ->count();

                $tyLeCoMat = $tongDiemDanh > 0 ? round(($coMat / $tongDiemDanh) * 100, 2) : 0;

                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $lop->ma_lop_hp);
                $sheet->setCellValue('C' . $row, $lop->monHoc->ten_mon ?? '');
                $sheet->setCellValue('D' . $row, $coMat);
                $sheet->setCellValue('E' . $row, $vang);
                $sheet->setCellValue('F' . $row, $tyLeCoMat);
                $row++;
            }
        } elseif ($loaiBaoCao === 'diem') {
            // Báo cáo điểm
            $sheet->setCellValue('A' . $row, 'STT');
            $sheet->setCellValue('B' . $row, 'Mã lớp');
            $sheet->setCellValue('C' . $row, 'Môn học');
            $sheet->setCellValue('D' . $row, 'Tổng SV');
            $sheet->setCellValue('E' . $row, 'Qua môn');
            $sheet->setCellValue('F' . $row, 'Tỷ lệ qua (%)');
            
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
            $stt = 1;

            $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
                ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
                ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
                ->with(['monHoc'])
                ->get();

            foreach ($lopHocPhans as $lop) {
                $ketQuas = KetQuaHocTap::join('lop_hoc_phan_sinh_vien', 'ket_qua_hoc_tap.lop_hoc_phan_sinh_vien_id', '=', 'lop_hoc_phan_sinh_vien.id')
                    ->where('lop_hoc_phan_sinh_vien.lop_hoc_phan_id', $lop->id)
                    ->select('ket_qua_hoc_tap.*')
                    ->get();

                $tongSinhVien = $ketQuas->count();
                $quaMon = $ketQuas->where('qua_mon', true)->count();
                $tyLeQuaMon = $tongSinhVien > 0 ? round(($quaMon / $tongSinhVien) * 100, 2) : 0;

                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $lop->ma_lop_hp);
                $sheet->setCellValue('C' . $row, $lop->monHoc->ten_mon ?? '');
                $sheet->setCellValue('D' . $row, $tongSinhVien);
                $sheet->setCellValue('E' . $row, $quaMon);
                $sheet->setCellValue('F' . $row, $tyLeQuaMon);
                $row++;
            }
        }

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Borders
        $sheet->getStyle('A5:F' . ($row - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Export
        $writer = new Xlsx($spreadsheet);
        $fileName = 'bao_cao_giang_day_' . $giangVien->ma_giang_vien . '_' . now()->format('YmdHis') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Xuất báo cáo PDF
     */
    public function exportPdf(Request $request)
    {
        $loaiBaoCao = $request->input('loai', 'tien-do');
        $user = Auth::user();
        $giangVien = $user->giangVien;

        $data = [
            'giangVien' => $giangVien,
            'loaiBaoCao' => $loaiBaoCao,
            'ngayXuat' => now()->format('d/m/Y H:i'),
        ];

        $lopHocPhans = LopHocPhan::select('lop_hoc_phan.*')
            ->join('lop_hoc_phan_giang_vien', 'lop_hoc_phan.id', '=', 'lop_hoc_phan_giang_vien.lop_hoc_phan_id')
            ->where('lop_hoc_phan_giang_vien.giang_vien_id', $giangVien->id)
            ->with(['monHoc'])
            ->get();

        if ($loaiBaoCao === 'tien-do') {
            $thongKe = [];
            foreach ($lopHocPhans as $lop) {
                $tongBuoi = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)->count();
                $daDayCount = LichHocChiTiet::where('lop_hoc_phan_id', $lop->id)
                    ->where('trang_thai', 'da_day')
                    ->count();
                $tiLe = $tongBuoi > 0 ? round(($daDayCount / $tongBuoi) * 100, 2) : 0;

                $thongKe[] = [
                    'lop' => $lop,
                    'tong_buoi' => $tongBuoi,
                    'da_day' => $daDayCount,
                    'ti_le' => $tiLe,
                ];
            }
            $data['thongKe'] = $thongKe;
        }

        $pdf = Pdf::loadView('giangvien.bao-cao.pdf', $data);
        $fileName = 'bao_cao_giang_day_' . $giangVien->ma_giang_vien . '_' . now()->format('YmdHis') . '.pdf';
        
        return $pdf->download($fileName);
    }
}
