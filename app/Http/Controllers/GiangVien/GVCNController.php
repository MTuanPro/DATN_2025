<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\SinhVien;
use App\Models\GiangVien;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GVCNController extends Controller
{
    /**
     * Hiển thị danh sách lớp chủ nhiệm
     */
    public function index()
    {
        // Lấy thông tin giảng viên từ user hiện tại
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giang-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy danh sách lớp chủ nhiệm
        $lopChuNhiem = LopHanhChinh::where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh', 'sinhVien'])
            ->get();

        // Tính toán thống kê cho từng lớp
        foreach ($lopChuNhiem as $lop) {
            $lop->tong_sinh_vien = $lop->sinhVien->count();
            $lop->sinh_vien_nam = $lop->sinhVien->where('gioi_tinh', 'nam')->count();
            $lop->sinh_vien_nu = $lop->sinhVien->where('gioi_tinh', 'nu')->count();
            $lop->dang_hoc = $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Đang học';
            })->count();
        }

        return view('giangvien.lop-chu-nhiem.index', compact('lopChuNhiem', 'giangVien'));
    }

    /**
     * Hiển thị chi tiết lớp chủ nhiệm
     */
    public function show($id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giang-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy thông tin lớp
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh', 'sinhVien.trangThaiHocTap', 'sinhVien.chuyenNganh'])
            ->firstOrFail();

        // Thống kê lớp
        $thongKe = [
            'tong_sinh_vien' => $lop->sinhVien->count(),
            'nam' => $lop->sinhVien->where('gioi_tinh', 'nam')->count(),
            'nu' => $lop->sinhVien->where('gioi_tinh', 'nu')->count(),
            'khac' => $lop->sinhVien->where('gioi_tinh', 'khac')->count(),
            'dang_hoc' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Đang học';
            })->count(),
            'bao_luu' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Bảo lưu';
            })->count(),
            'thoi_hoc' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Thôi học';
            })->count(),
            'tot_nghiep' => $lop->sinhVien->filter(function ($sv) {
                return $sv->trangThaiHocTap && $sv->trangThaiHocTap->ten_trang_thai == 'Tốt nghiệp';
            })->count(),
        ];

        // Phân bố theo chuyên ngành (cho sinh viên từ năm 3 trở lên)
        $phanBoChuyenNganh = $lop->sinhVien
            ->whereNotNull('chuyen_nganh_id')
            ->groupBy('chuyen_nganh_id')
            ->map(function ($group) {
                return [
                    'ten_chuyen_nganh' => $group->first()->chuyenNganh->ten_chuyen_nganh ?? 'Chưa xác định',
                    'so_luong' => $group->count(),
                ];
            });

        return view('giangvien.lop-chu-nhiem.show', compact('lop', 'thongKe', 'phanBoChuyenNganh', 'giangVien'));
    }

    /**
     * Hiển thị danh sách sinh viên trong lớp
     */
    public function danhSachSinhVien(Request $request, $id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giang-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra quyền truy cập
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Query sinh viên
        $query = SinhVien::where('lop_hanh_chinh_id', $id)
            ->with(['trangThaiHocTap', 'nganh', 'chuyenNganh', 'user']);

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_sinh_vien', 'like', '%' . $search . '%')
                    ->orWhere('ho_ten', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('so_dien_thoai', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo giới tính
        if ($request->has('gioi_tinh') && $request->gioi_tinh != '') {
            $query->where('gioi_tinh', $request->gioi_tinh);
        }

        // Lọc theo trạng thái học tập
        if ($request->has('trang_thai_id') && $request->trang_thai_id != '') {
            $query->where('trang_thai_hoc_tap_id', $request->trang_thai_id);
        }

        // Lọc theo chuyên ngành
        if ($request->has('chuyen_nganh_id') && $request->chuyen_nganh_id != '') {
            $query->where('chuyen_nganh_id', $request->chuyen_nganh_id);
        }

        // Sắp xếp
        $query->orderBy('ma_sinh_vien', 'asc');

        // Phân trang
        $sinhViens = $query->paginate(20)->appends($request->all());

        // Lấy danh sách chuyên ngành và trạng thái để filter
        $chuyenNganhs = \App\Models\DaoTao\ChuyenNganh::where('nganh_id', $lop->nganh_id)->get();
        $trangThais = \App\Models\DanhMuc\TrangThaiHocTap::all();

        return view('giangvien.lop-chu-nhiem.sinh-vien', compact(
            'lop',
            'sinhViens',
            'giangVien',
            'chuyenNganhs',
            'trangThais'
        ));
    }

    /**
     * Xuất danh sách sinh viên Excel
     */
    public function exportExcel($id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giang-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy thông tin lớp
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Lấy danh sách sinh viên
        $sinhViens = SinhVien::where('lop_hanh_chinh_id', $id)
            ->with(['trangThaiHocTap', 'nganh', 'chuyenNganh'])
            ->orderBy('ma_sinh_vien', 'asc')
            ->get();

        // Tạo spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'DANH SÁCH SINH VIÊN LỚP ' . $lop->ma_lop);
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Khóa học: ' . ($lop->khoaHoc->ten_khoa_hoc ?? 'N/A'));
        $sheet->setCellValue('A3', 'Ngành: ' . ($lop->nganh->ten_nganh ?? 'N/A'));
        $sheet->setCellValue('A4', 'GVCN: ' . $giangVien->ho_ten);
        $sheet->setCellValue('A5', 'Ngày xuất: ' . now()->format('d/m/Y H:i'));

        // Column headers
        $row = 7;
        $headers = ['STT', 'Mã SV', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Email', 'SĐT', 'Kỳ hiện tại', 'Chuyên ngành', 'Trạng thái', 'Địa chỉ'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $col++;
        }

        // Data
        $row = 8;
        $stt = 1;
        foreach ($sinhViens as $sv) {
            $diaChi = implode(', ', array_filter([
                $sv->so_nha_duong,
                $sv->phuong_xa,
                $sv->quan_huyen,
                $sv->tinh_thanh
            ]));

            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $sv->ma_sinh_vien);
            $sheet->setCellValue('C' . $row, $sv->ho_ten);
            $sheet->setCellValue('D' . $row, $sv->ngay_sinh ? $sv->ngay_sinh->format('d/m/Y') : '');
            $sheet->setCellValue('E' . $row, ucfirst($sv->gioi_tinh));
            $sheet->setCellValue('F' . $row, $sv->email);
            $sheet->setCellValue('G' . $row, $sv->so_dien_thoai);
            $sheet->setCellValue('H' . $row, $sv->ky_hien_tai);
            $sheet->setCellValue('I' . $row, $sv->chuyenNganh->ten_chuyen_nganh ?? 'Chưa chọn');
            $sheet->setCellValue('J' . $row, $sv->trangThaiHocTap->ten_trang_thai ?? 'N/A');
            $sheet->setCellValue('K' . $row, $diaChi);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border
        $sheet->getStyle('A7:K' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Download
        $fileName = 'Danh_sach_sinh_vien_' . $lop->ma_lop . '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Xuất danh sách sinh viên PDF
     */
    public function exportPDF($id)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giang-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy thông tin lớp
        $lop = LopHanhChinh::where('id', $id)
            ->where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->firstOrFail();

        // Lấy danh sách sinh viên
        $sinhViens = SinhVien::where('lop_hanh_chinh_id', $id)
            ->with(['trangThaiHocTap', 'nganh', 'chuyenNganh'])
            ->orderBy('ma_sinh_vien', 'asc')
            ->get();

        $data = [
            'lop' => $lop,
            'sinhViens' => $sinhViens,
            'giangVien' => $giangVien,
            'ngayXuat' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('giangvien.lop-chu-nhiem.pdf.danh-sach-sinh-vien', $data);
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'Danh_sach_sinh_vien_' . $lop->ma_lop . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}
