<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\GiangVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LopHanhChinhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LopHanhChinh::with(['khoaHoc', 'nganh', 'giangVienChuNhiem']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop', 'like', "%{$search}%")
                    ->orWhere('ten_lop', 'like', "%{$search}%");
            });
        }

        // Lọc theo khóa học
        if ($request->filled('khoa_hoc_id')) {
            $query->where('khoa_hoc_id', $request->khoa_hoc_id);
        }

        // Lọc theo ngành
        if ($request->filled('nganh_id')) {
            $query->where('nganh_id', $request->nganh_id);
        }

        $lopHanhChinh = $query->orderBy('created_at', 'desc')->paginate(15);

        // Dữ liệu cho bộ lọc
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();

        return view('daotao.lop-hanh-chinh.index', compact('lopHanhChinh', 'khoaHocs', 'nganhs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        return view('daotao.lop-hanh-chinh.create', compact('khoaHocs', 'nganhs', 'giangViens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_lop' => 'required|string|max:255|unique:lop_hanh_chinh,ma_lop',
            'ten_lop' => 'required|string|max:255',
            'khoa_hoc_id' => 'required|exists:khoa_hoc,id',
            'nganh_id' => 'required|exists:nganh,id',
            'giang_vien_chu_nhiem_id' => 'nullable|exists:giang_vien,id',
        ], [
            'ma_lop.required' => 'Mã lớp là bắt buộc',
            'ma_lop.unique' => 'Mã lớp đã tồn tại',
            'ten_lop.required' => 'Tên lớp là bắt buộc',
            'khoa_hoc_id.required' => 'Khóa học là bắt buộc',
            'nganh_id.required' => 'Ngành là bắt buộc',
        ]);

        LopHanhChinh::create($validated);

        return redirect()->route('dao-tao.lop-hanh-chinh.index')
            ->with('success', 'Thêm lớp hành chính thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lopHanhChinh = LopHanhChinh::with([
            'khoaHoc',
            'nganh.khoa',
            'giangVienChuNhiem',
            'sinhVien.trangThaiHocTap'
        ])->findOrFail($id);

        return view('daotao.lop-hanh-chinh.show', compact('lopHanhChinh'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lopHanhChinh = LopHanhChinh::findOrFail($id);
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        return view('daotao.lop-hanh-chinh.edit', compact('lopHanhChinh', 'khoaHocs', 'nganhs', 'giangViens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lopHanhChinh = LopHanhChinh::findOrFail($id);

        $validated = $request->validate([
            'ma_lop' => 'required|string|max:255|unique:lop_hanh_chinh,ma_lop,' . $id,
            'ten_lop' => 'required|string|max:255',
            'khoa_hoc_id' => 'required|exists:khoa_hoc,id',
            'nganh_id' => 'required|exists:nganh,id',
            'giang_vien_chu_nhiem_id' => 'nullable|exists:giang_vien,id',
        ], [
            'ma_lop.required' => 'Mã lớp là bắt buộc',
            'ma_lop.unique' => 'Mã lớp đã tồn tại',
            'ten_lop.required' => 'Tên lớp là bắt buộc',
            'khoa_hoc_id.required' => 'Khóa học là bắt buộc',
            'nganh_id.required' => 'Ngành là bắt buộc',
        ]);

        $lopHanhChinh->update($validated);

        return redirect()->route('dao-tao.lop-hanh-chinh.index')
            ->with('success', 'Cập nhật lớp hành chính thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lopHanhChinh = LopHanhChinh::findOrFail($id);

        // Kiểm tra xem lớp có sinh viên không
        if ($lopHanhChinh->sinhVien()->count() > 0) {
            return redirect()->route('dao-tao.lop-hanh-chinh.index')
                ->with('error', 'Không thể xóa lớp đã có sinh viên!');
        }

        $lopHanhChinh->delete();

        return redirect()->route('dao-tao.lop-hanh-chinh.index')
            ->with('success', 'Xóa lớp hành chính thành công!');
    }

    /**
     * Export danh sách lớp hành chính ra Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // Kiểm tra extension zip
            if (!extension_loaded('zip')) {
                return redirect()->route('dao-tao.lop-hanh-chinh.index')
                    ->with('error', 'PHP extension "zip" chưa được cài đặt. Vui lòng bật extension zip trong php.ini và khởi động lại web server (Laragon).');
            }
            $query = LopHanhChinh::with(['khoaHoc', 'nganh', 'giangVienChuNhiem']);

            // Áp dụng các filter giống như index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ma_lop', 'like', "%{$search}%")
                        ->orWhere('ten_lop', 'like', "%{$search}%");
                });
            }

            if ($request->filled('khoa_hoc_id')) {
                $query->where('khoa_hoc_id', $request->khoa_hoc_id);
            }

            if ($request->filled('nganh_id')) {
                $query->where('nganh_id', $request->nganh_id);
            }

            $lopHanhChinh = $query->orderBy('created_at', 'desc')->get();

            // Tạo file Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Danh sách Lớp hành chính');

            // Header row
            $headers = ['STT', 'Mã lớp', 'Tên lớp', 'Khóa học', 'Ngành', 'GVCN', 'Sĩ số', 'Ngày tạo'];
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

            // Ghi dữ liệu
            $row = 2;
            foreach ($lopHanhChinh as $index => $lop) {
                $rowData = [
                    $index + 1,
                    $lop->ma_lop,
                    $lop->ten_lop,
                    $lop->khoaHoc ? $lop->khoaHoc->ten_khoa_hoc : '',
                    $lop->nganh ? ($lop->nganh->ma_nganh . ' - ' . $lop->nganh->ten_nganh) : '',
                    $lop->giangVienChuNhiem ? $lop->giangVienChuNhiem->ho_ten : 'Chưa có',
                    $lop->si_so,
                    $lop->created_at ? $lop->created_at->format('d/m/Y') : '',
                ];

                $sheet->fromArray([$rowData], null, 'A' . $row);
                $row++;
            }

            // Auto size columns
            foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Tạo file và download
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $fileName = 'Danh_sach_Lop_hanh_chinh_' . date('YmdHis') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->route('dao-tao.lop-hanh-chinh.index')
                ->with('error', 'Có lỗi xảy ra khi xuất Excel: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form import
     */
    public function showImportForm()
    {
        return view('daotao.lop-hanh-chinh.import');
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="lop_hanh_chinh_template.csv"',
        ];

        $columns = [
            'ma_lop',
            'ten_lop',
            'khoa_hoc',
            'nganh',
            'giang_vien_chu_nhiem',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header
            fputcsv($file, $columns);

            // Sample data
            fputcsv($file, [
                'CNTT-K25-01',
                'Công Nghệ Thông Tin K25',
                'K25',
                'Công nghệ thông tin',
                'Nguyễn Văn A',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import lớp hành chính từ Excel hoặc CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel hoặc CSV',
            'file.mimes' => 'File phải có định dạng Excel (.xlsx, .xls) hoặc CSV (.csv, .txt)',
            'file.max' => 'File không được vượt quá 5MB',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            $data = [];
            
            // Đọc file Excel
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Kiểm tra extension zip có sẵn không
                if (!extension_loaded('zip')) {
                    DB::rollBack();
                    return back()->with('error', 'PHP extension "zip" chưa được cài đặt. Vui lòng bật extension zip trong php.ini hoặc sử dụng file CSV thay vì Excel.');
                }
                
                try {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                    $worksheet = $spreadsheet->getActiveSheet();
                    $data = $worksheet->toArray();
                    
                    // Bỏ qua header (dòng đầu tiên)
                    array_shift($data);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Không thể đọc file Excel: ' . $e->getMessage());
                }
            } 
            // Đọc file CSV
            else {
                $handle = fopen($file->getRealPath(), 'r');
                
                if ($handle === false) {
                    throw new \Exception('Không thể đọc file');
                }
                
                // Bỏ qua dòng header
                fgetcsv($handle);
                
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                
                fclose($handle);
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $rowNum => $row) {
                $rowNum += 2; // +2 vì bỏ header và index bắt đầu từ 0

                // Kiểm tra dòng trống
                if (empty($row[0])) {
                    continue;
                }

                try {
                    // Parse data - mapping: ma_lop, ten_lop, khoa_hoc, nganh, giang_vien_chu_nhiem
                    $maLop = trim($row[0] ?? '');
                    $tenLop = trim($row[1] ?? '');
                    $tenKhoaHoc = trim($row[2] ?? '');
                    $tenNganh = trim($row[3] ?? '');
                    $tenGiangVien = !empty($row[4]) ? trim($row[4]) : null;

                    // Validate các trường bắt buộc
                    if (empty($maLop) || empty($tenLop) || empty($tenKhoaHoc) || empty($tenNganh)) {
                        $errors[] = "Dòng {$rowNum}: Thiếu thông tin bắt buộc (Mã lớp, Tên lớp, Khóa học, Ngành)";
                        continue;
                    }

                    // Không cần kiểm tra trùng vì sẽ update nếu đã tồn tại

                    // Tìm khóa học theo tên
                    $khoaHoc = KhoaHoc::where('ten_khoa_hoc', $tenKhoaHoc)->first();
                    if (!$khoaHoc) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy khóa học với tên: {$tenKhoaHoc}";
                        continue;
                    }

                    // Tìm ngành theo tên hoặc mã
                    $nganh = Nganh::where('ten_nganh', $tenNganh)
                        ->orWhere('ma_nganh', $tenNganh)
                        ->first();
                    
                    if (!$nganh) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy ngành với tên/mã: {$tenNganh}";
                        continue;
                    }

                    // Tìm giảng viên chủ nhiệm (nếu có)
                    $giangVienId = null;
                    if ($tenGiangVien) {
                        $giangVien = GiangVien::where('ho_ten', $tenGiangVien)
                            ->orWhere('ma_giang_vien', $tenGiangVien)
                            ->first();
                        
                        if (!$giangVien) {
                            $errors[] = "Dòng {$rowNum}: Không tìm thấy giảng viên với tên/mã: {$tenGiangVien}";
                            continue;
                        }
                        $giangVienId = $giangVien->id;
                    }

                    // Update hoặc tạo lớp hành chính (dựa vào ma_lop)
                    LopHanhChinh::updateOrCreate(
                        ['ma_lop' => $maLop],
                        [
                            'ten_lop' => $tenLop,
                            'khoa_hoc_id' => $khoaHoc->id,
                            'nganh_id' => $nganh->id,
                            'giang_vien_chu_nhiem_id' => $giangVienId,
                            // Không update si_so khi import để tránh ghi đè số lượng thực tế
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng {$rowNum}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Import thành công {$imported} lớp hành chính (đã tạo mới hoặc cập nhật).";
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 5));
            }

            return redirect()->route('dao-tao.lop-hanh-chinh.index')
                ->with('success', $message)
                ->with('errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi import: ' . $e->getMessage());
        }
    }
}
