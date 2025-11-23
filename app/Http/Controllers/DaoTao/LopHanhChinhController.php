<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\GiangVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LopHanhChinhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Hiển thị danh sách các lớp hành chính
     * 
     * Hỗ trợ lọc theo khoa, ngành, khóa học, trạng thái và tìm kiếm.
     * Kết quả bao gồm thông tin khoa, ngành, GVCN, sỉ số lớp.
     * 
     * @param Request $request Chứa các tham số lọc và tìm kiếm
     * @return \Illuminate\View\View Trang danh sách lớp hành chính
     * @throws \Exception Nếu có lỗi khi truy vấn
     */
    public function index(Request $request)
    {
        $query = LopHanhChinh::with(['khoaHoc', 'nganh', 'giangVienChuNhiem', 'sinhVien']);

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
    /**
     * Hiển thị form tạo lớp hành chính mới
     * 
     * Lấy danh sách khoa, ngành, khóa học, giảng viên chủ nhiệm
     * để hiển thị trên form tạo lớp
     * 
     * @return \Illuminate\View\View Form tạo lớp hành chính
     * @throws \Exception Nếu có lỗi khi tải dữ liệu
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
    /**
     * Lưu lớp hành chính mới vào database
     * 
     * Validate và tạo lớp hành chính với thông tin:
     * mã lớp, tên lớp, khoa, ngành, khóa học, GVCN, sỉ số
     * 
     * @param Request $request Chứa dữ liệu lớp hành chính
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách
     * @throws \Illuminate\Validation\ValidationException Nếu validation thất bại
     * @throws \Exception Nếu có lỗi khi lưu
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
    /**
     * Hiển thị chi tiết lớp hành chính
     * 
     * Hiển thị thông tin đầy đủ về lớp hành chính bao gồm:
     * danh sách sinh viên, GVCN, thống kê sỉ số, kết quả học tập
     * 
     * @param string $id ID lớp hành chính
     * @return \Illuminate\View\View Trang chi tiết lớp hành chính
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
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
    /**
     * Hiển thị form chỉnh sửa lớp hành chính
     * 
     * Lấy thông tin lớp hành chính hiện tại, danh sách khoa, ngành, khóa, GVCN
     * 
     * @param string $id ID lớp hành chính cần sửa
     * @return \Illuminate\View\View Form chỉnh sửa
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
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
    /**
     * Cập nhật thông tin lớp hành chính
     * 
     * Validate và cập nhật thông tin lớp hành chính trong database.
     * Tự động sync sỉ số thực tế sau khi cập nhật.
     * 
     * @param Request $request Chứa dữ liệu cập nhật
     * @param string $id ID lớp hành chính cần cập nhật
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách
     * @throws \Illuminate\Validation\ValidationException Nếu validation thất bại
     * @throws \Exception Nếu có lỗi khi cập nhật
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
    /**
     * Xóa lớp hành chính khỏi database
     * 
     * Kiểm tra điều kiện: chỉ xóa được nếu lớp chưa có sinh viên.
     * Xóa cả dữ liệu liên quan nếu có.
     * 
     * @param string $id ID lớp hành chính cần xóa
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
     * @throws \Exception Nếu có sinh viên hoặc lỗi khi xóa
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
    /**
     * Xuất danh sách lớp hành chính ra file Excel
     * 
     * Tạo file Excel chứa danh sách lớp hành chính với các thông tin:
     * Mã lớp, Tên lớp, Khoa, Ngành, Khóa, GVCN, Sỉ số
     * 
     * @param Request $request Chứa các tham số lọc
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File Excel được tải xuống
     * @throws \Exception Nếu có lỗi khi tạo file
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
    /**
     * Hiển thị trang import lớp hành chính từ file Excel
     * 
     * @return \Illuminate\View\View Trang upload file Excel
     */
    public function showImportForm()
    {
        return view('daotao.lop-hanh-chinh.import');
    }

    /**
     * Download template Excel
     */
    /**
     * Tải file Excel mẫu để import lớp hành chính
     * 
     * Tạo file Excel với các cột: Mã lớp, Tên lớp, Mã khoa, Mã ngành,
     * Mã khóa học, Mã GVCN, Sỉ số tối đa, Ghi chú
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File Excel mẫu
     * @throws \Exception Nếu có lỗi khi tạo file
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
    /**
     * Import danh sách lớp hành chính từ file Excel
     * 
     * Đọc file Excel và tạo/cập nhật hàng loạt các lớp hành chính.
     * Validate từng dòng và báo lỗi chi tiết.
     * 
     * @param Request $request Chứa file Excel upload
     * @return \Illuminate\Http\RedirectResponse Redirect về trang import với kết quả
     * @throws \Illuminate\Validation\ValidationException Nếu file không hợp lệ
     * @throws \Exception Nếu có lỗi khi import
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

    /**
     * Đồng bộ lại sĩ số cho tất cả các lớp hành chính
     */
    /**
     * Đồng bộ sỉ số thực tế cho tất cả lớp hành chính
     * 
     * Cập nhật sỉ số hiện tại bằng cách đếm số sinh viên
     * thuộc lớp hành chính từ bảng sinh_vien
     * 
     * @return \Illuminate\Http\JsonResponse Kết quả đồng bộ với số lượng lớp đã cập nhật
     * @throws \Exception Nếu có lỗi khi đồng bộ
     */
    public function syncSiSo()
    {
        DB::beginTransaction();
        try {
            $lopHanhChinhs = LopHanhChinh::withCount('sinhVien')->get();
            $updated = 0;

            foreach ($lopHanhChinhs as $lop) {
                $soLuongThucTe = $lop->sinh_vien_count;
                if ($lop->si_so != $soLuongThucTe) {
                    $lop->update(['si_so' => $soLuongThucTe]);
                    $updated++;
                }
            }

            DB::commit();

            return redirect()->route('dao-tao.lop-hanh-chinh.index')
                ->with('success', "Đã đồng bộ lại sĩ số cho {$updated} lớp hành chính!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi đồng bộ sĩ số: ' . $e->getMessage());
        }
    }

    /**
     * Remove multiple resources from storage.
     */
    /**
     * Xóa nhiều lớp hành chính cùng lúc
     * 
     * Xóa hàng loạt các lớp hành chính theo danh sách ID.
     * Chỉ xóa các lớp chưa có sinh viên.
     * 
     * @param Request $request Chứa mảng lop_hanh_chinh_ids
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách với kết quả
     * @throws \Illuminate\Validation\ValidationException Nếu danh sách ID không hợp lệ
     * @throws \Exception Nếu có lỗi khi xóa
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));

        if (empty($ids)) {
            return back()->with('error', 'Không có lớp hành chính nào được chọn!');
        }

        DB::beginTransaction();
        try {
            $deleted = 0;
            $errors = [];
            $skipped = 0;

            foreach ($ids as $id) {
                try {
                    $lopHanhChinh = LopHanhChinh::find($id);
                    if (!$lopHanhChinh) {
                        $errors[] = "Lớp hành chính ID {$id} không tồn tại";
                        continue;
                    }

                    // Kiểm tra xem lớp có sinh viên không
                    $soSinhVien = $lopHanhChinh->sinhVien()->count();
                    if ($soSinhVien > 0) {
                        $skipped++;
                        $errors[] = "Lớp hành chính {$lopHanhChinh->ma_lop} (ID: {$id}) đã có {$soSinhVien} sinh viên. Không thể xóa!";
                        continue;
                    }

                    // Xóa lớp hành chính
                    $lopHanhChinh->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Lỗi khi xóa lớp hành chính ID {$id}: " . $e->getMessage();
                    \Log::error("Lỗi xóa lớp hành chính ID {$id}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Đã xóa thành công {$deleted} lớp hành chính.";
            if ($skipped > 0) {
                $message .= " Bỏ qua {$skipped} lớp hành chính do có sinh viên.";
            }
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()->route('dao-tao.lop-hanh-chinh.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi xóa nhiều lớp hành chính: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }
}
