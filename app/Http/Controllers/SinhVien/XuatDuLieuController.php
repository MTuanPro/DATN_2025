<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Models\LichHocChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class XuatDuLieuController extends Controller
{
    /**
     * Trang danh sách xuất dữ liệu
     */
    public function index()
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        return view('sinhvien.xuat-du-lieu.index', compact('sinhVien', 'hocKys'));
    }

    /**
     * Xuất bảng điểm Excel
     */
    public function xuatBangDiemExcel(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;
        $hocKyId = $request->hoc_ky_id;

        // Lấy danh sách lớp học phần của sinh viên
        $lopHocPhanSinhVienIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->when($hocKyId, function ($q) use ($hocKyId) {
                $q->whereHas('lopHocPhan', function ($query) use ($hocKyId) {
                    $query->where('hoc_ky_id', $hocKyId);
                });
            })
            ->pluck('id');

        // Lấy kết quả học tập (chỉ lấy kết quả mới nhất cho mỗi môn)
        $ketQuas = KetQuaHocTap::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienIds)
            ->with(['lopHocPhanSinhVien.lopHocPhan.monHoc', 'lopHocPhanSinhVien.lopHocPhan.hocKy'])
            ->get()
            ->unique(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id;
            });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'BẢNG ĐIỂM SINH VIÊN');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Thông tin sinh viên
        $row = 3;
        $sheet->setCellValue('A' . $row, 'Họ tên: ' . $sinhVien->ho_ten);
        $sheet->setCellValue('F' . $row, 'MSSV: ' . $sinhVien->ma_sinh_vien);
        $row++;
        $sheet->setCellValue('A' . $row, 'Lớp: ' . ($sinhVien->lop->ten_lop ?? 'N/A'));
        $sheet->setCellValue('F' . $row, 'Khóa: ' . ($sinhVien->khoa_hoc ?? 'N/A'));
        $row += 2;

        // Table headers
        $headers = ['STT', 'Mã môn', 'Tên môn học', 'Số TC', 'Điểm QT', 'Điểm GK', 'Điểm CK', 'Điểm TK (10)', 'Điểm chữ'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $col++;
        }
        $row++;

        // Data
        $stt = 1;
        $tongTinChi = 0;
        $tongDiemTichLuy = 0;

        foreach ($ketQuas as $kq) {
            $monHoc = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc;
            $tinChi = $monHoc->so_tin_chi;

            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $monHoc->ma_mon);
            $sheet->setCellValue('C' . $row, $monHoc->ten_mon);
            $sheet->setCellValue('D' . $row, $tinChi);
            $sheet->setCellValue('E' . $row, $kq->diem_qua_trinh ?? '-');
            $sheet->setCellValue('F' . $row, $kq->diem_giua_ky ?? '-');
            $sheet->setCellValue('G' . $row, $kq->diem_cuoi_ky ?? '-');
            $sheet->setCellValue('H' . $row, $kq->diem_he_10 ? number_format($kq->diem_he_10, 2) : '-');
            $sheet->setCellValue('I' . $row, $kq->diem_chu ?? '-');

            if ($kq->qua_mon) {
                $tongTinChi += $tinChi;
                $tongDiemTichLuy += $kq->diem_he_4 * $tinChi;
            }

            $row++;
        }

        // GPA
        $row++;
        $gpa = $tongTinChi > 0 ? round($tongDiemTichLuy / $tongTinChi, 2) : 0;
        $sheet->setCellValue('A' . $row, 'GPA: ' . $gpa);
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('D' . $row, 'Tổng TC đạt: ' . $tongTinChi);
        $sheet->mergeCells('D' . $row . ':E' . $row);
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A6:I' . ($row - 1))->applyFromArray($styleArray);

        // Download
        $writer = new Xlsx($spreadsheet);
        $fileName = 'BangDiem_' . $sinhVien->ma_sinh_vien . '_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Xuất bảng điểm PDF
     */
    public function xuatBangDiemPdf(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;
        $hocKyId = $request->hoc_ky_id;

        // Lấy danh sách lớp học phần của sinh viên
        $lopHocPhanSinhVienIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->when($hocKyId, function ($q) use ($hocKyId) {
                $q->whereHas('lopHocPhan', function ($query) use ($hocKyId) {
                    $query->where('hoc_ky_id', $hocKyId);
                });
            })
            ->pluck('id');

        // Lấy kết quả học tập (chỉ lấy kết quả mới nhất cho mỗi môn)
        $ketQuas = KetQuaHocTap::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienIds)
            ->with(['lopHocPhanSinhVien.lopHocPhan.monHoc', 'lopHocPhanSinhVien.lopHocPhan.hocKy'])
            ->get()
            ->unique(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id;
            });

        // Tính GPA
        $tongTinChi = 0;
        $tongDiemTichLuy = 0;

        foreach ($ketQuas as $kq) {
            if ($kq->qua_mon) {
                $tinChi = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
                $tongTinChi += $tinChi;
                $tongDiemTichLuy += $kq->diem_he_4 * $tinChi;
            }
        }

        $gpa = $tongTinChi > 0 ? round($tongDiemTichLuy / $tongTinChi, 2) : 0;

        $hocKy = $hocKyId ? HocKy::find($hocKyId) : null;

        $pdf = Pdf::loadView('sinhvien.xuat-du-lieu.bang-diem-pdf', compact(
            'sinhVien',
            'ketQuas',
            'gpa',
            'tongTinChi',
            'hocKy'
        ));

        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('BangDiem_' . $sinhVien->ma_sinh_vien . '.pdf');
    }

    /**
     * Xuất thời khóa biểu PDF
     */
    public function xuatTKBPdf(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;
        $hocKyId = $request->hoc_ky_id;

        $hocKy = HocKy::find($hocKyId);

        if (!$hocKy) {
            return redirect()->back()->with('error', 'Vui lòng chọn học kỳ!');
        }

        // Lấy lịch học
        $lopHocPhanIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            })
            ->pluck('lop_hoc_phan_id');

        $lichHocs = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->with(['lopHocPhan.monHoc', 'giangVien', 'phongHoc'])
            ->orderBy('ngay_hoc')
            ->orderBy('tiet_bat_dau')
            ->get();

        // Sắp xếp theo thứ và tiết
        $tkb = [];
        foreach ($lichHocs as $lich) {
            $thu = \Carbon\Carbon::parse($lich->ngay_hoc)->dayOfWeek;
            if ($thu == 0) $thu = 8; // Chủ nhật = 8
            
            $tiet = $lich->tiet_bat_dau;
            
            if (!isset($tkb[$thu])) {
                $tkb[$thu] = [];
            }
            if (!isset($tkb[$thu][$tiet])) {
                $tkb[$thu][$tiet] = [];
            }
            
            $tkb[$thu][$tiet][] = $lich;
        }

        ksort($tkb);

        $pdf = Pdf::loadView('sinhvien.xuat-du-lieu.tkb-pdf', compact(
            'sinhVien',
            'hocKy',
            'tkb'
        ));

        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('TKB_' . $sinhVien->ma_sinh_vien . '_' . $hocKy->ten_hoc_ky . '.pdf');
    }

    /**
     * Giấy xác nhận sinh viên PDF
     */
    public function giayXacNhanPdf()
    {
        $sinhVien = Auth::user()->sinhVien;

        // Lấy thông tin học tập
        $hocKyHienTai = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        
        // Lấy tổng tín chỉ đạt
        $lopHocPhanSinhVienIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)->pluck('id');
        
        $tongTinChiDat = KetQuaHocTap::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienIds)
            ->where('qua_mon', true)
            ->with('lopHocPhanSinhVien.lopHocPhan.monHoc')
            ->get()
            ->unique(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id;
            })
            ->sum(function ($item) {
                return $item->lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi;
            });

        $pdf = Pdf::loadView('sinhvien.xuat-du-lieu.giay-xac-nhan-pdf', compact(
            'sinhVien',
            'hocKyHienTai',
            'tongTinChiDat'
        ));

        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('GiayXacNhan_' . $sinhVien->ma_sinh_vien . '.pdf');
    }
}
