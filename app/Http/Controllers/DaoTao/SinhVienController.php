<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DanhMuc\TrangThaiHocTap;
use App\Models\GiangVien;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ImportHelper;

class SinhVienController extends Controller
{
    use ImportHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SinhVien::with([
            'khoaHoc',
            'nganh',
            'chuyenNganh',
            'trangThaiHocTap'
        ]);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_sinh_vien', 'like', "%{$search}%")
                    ->orWhere('ho_ten', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$search}%");
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

        // Lọc theo trạng thái
        if ($request->filled('trang_thai_hoc_tap_id')) {
            $query->where('trang_thai_hoc_tap_id', $request->trang_thai_hoc_tap_id);
        }

        $sinhViens = $query->orderBy('created_at', 'desc')->paginate(15);

        // Dữ liệu cho bộ lọc
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $trangThais = TrangThaiHocTap::orderBy('ten_trang_thai')->get();

        return view('daotao.sinh-vien.index', compact('sinhViens', 'khoaHocs', 'nganhs', 'trangThais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $chuyenNganhs = ChuyenNganh::with('nganh')->orderBy('ten_chuyen_nganh')->get();
        $trangThais = TrangThaiHocTap::orderBy('ten_trang_thai')->get();

        return view('daotao.sinh-vien.create', compact('khoaHocs', 'nganhs', 'chuyenNganhs', 'trangThais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_sinh_vien' => 'required|string|max:255|unique:sinh_vien,ma_sinh_vien',
            'ho_ten' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:sinh_vien,email',
                function ($attribute, $value, $fail) {
                    // Kiểm tra email đã tồn tại trong bảng users
                    if (User::where('email', $value)->exists()) {
                        $fail('Email này đã được sử dụng cho tài khoản khác trong hệ thống.');
                    }
                },
            ],
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|in:nam,nu,khac',
            'so_dien_thoai' => 'required|string|max:15',
            'so_nha_duong' => 'nullable|string|max:255',
            'phuong_xa' => 'nullable|string|max:255',
            'quan_huyen' => 'nullable|string|max:255',
            'tinh_thanh' => 'nullable|string|max:255',
            'can_cuoc_cong_dan' => 'required|string|max:20|unique:sinh_vien,can_cuoc_cong_dan',
            'ngay_cap_cccd' => 'nullable|date',
            'noi_cap_cccd' => 'nullable|string|max:255',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'khoa_hoc_id' => 'required|exists:khoa_hoc,id',
            'nganh_id' => 'required|exists:nganh,id',
            'chuyen_nganh_id' => 'nullable|exists:chuyen_nganh,id',
            'ky_hien_tai' => 'required|integer|min:1|max:8',
            'trang_thai_hoc_tap_id' => 'required|exists:trang_thai_hoc_tap,id',
        ], [
            'ma_sinh_vien.required' => 'Mã sinh viên là bắt buộc',
            'ma_sinh_vien.unique' => 'Mã sinh viên đã tồn tại',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại trong danh sách sinh viên',
            'can_cuoc_cong_dan.unique' => 'Số CCCD đã tồn tại',
        ]);

        // Validation: Chuyên ngành phải thuộc ngành đã chọn
        if ($validated['chuyen_nganh_id']) {
            $chuyenNganh = ChuyenNganh::find($validated['chuyen_nganh_id']);
            if (!$chuyenNganh || $chuyenNganh->nganh_id != $validated['nganh_id']) {
                return back()->withInput()->with('error', 'Chuyên ngành không thuộc ngành đã chọn!');
            }
        }

        DB::beginTransaction();
        try {
            // Xử lý upload ảnh
            if ($request->hasFile('anh_dai_dien')) {
                $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('sinh-vien', 'public');
            }

            // Tạo tài khoản User
            $user = User::create([
                'name' => $validated['ho_ten'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['ma_sinh_vien']), // Mật khẩu mặc định là MSSV
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
            ]);

            Log::info('Đã tạo User ID: ' . $user->id . ' cho sinh viên: ' . $validated['ma_sinh_vien']);

            // Gán vai trò sinh_vien
            $vaiTroSinhVien = VaiTro::where('ma_vai_tro', 'sinh_vien')->first();
            if (!$vaiTroSinhVien) {
                throw new \Exception('Không tìm thấy vai trò sinh_vien trong hệ thống!');
            }

            $user->vaiTro()->attach($vaiTroSinhVien->id, [
                'nguoi_gan_id' => Auth::check() ? Auth::id() : null,
                'ngay_gan' => now(),
            ]);

            Log::info('Đã gán vai trò sinh_vien cho User ID: ' . $user->id);

            // Tạo sinh viên với user_id
            $validated['user_id'] = $user->id;

            $sinhVien = SinhVien::create($validated);

            Log::info('Đã tạo Sinh viên ID: ' . $sinhVien->id);

            DB::commit();

            return redirect()->route('dao-tao.sinh-vien.index')
                ->with('success', "Thêm sinh viên thành công! Email: {$validated['email']} - Mật khẩu: {$validated['ma_sinh_vien']}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo sinh viên: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sinhVien = SinhVien::with([
            'user',
            'khoaHoc',
            'nganh.khoa',
            'chuyenNganh',
            'trangThaiHocTap'
        ])->findOrFail($id);

        return view('daotao.sinh-vien.show', compact('sinhVien'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sinhVien = SinhVien::findOrFail($id);
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $chuyenNganhs = ChuyenNganh::with('nganh')->orderBy('ten_chuyen_nganh')->get();
        $trangThais = TrangThaiHocTap::orderBy('ten_trang_thai')->get();

        return view('daotao.sinh-vien.edit', compact('sinhVien', 'khoaHocs', 'nganhs', 'chuyenNganhs', 'trangThais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sinhVien = SinhVien::findOrFail($id);

        $validated = $request->validate([
            'ma_sinh_vien' => 'required|string|max:255|unique:sinh_vien,ma_sinh_vien,' . $id,
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:sinh_vien,email,' . $id,
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|in:nam,nu,khac',
            'so_dien_thoai' => 'required|string|max:15',
            'so_nha_duong' => 'nullable|string|max:255',
            'phuong_xa' => 'nullable|string|max:255',
            'quan_huyen' => 'nullable|string|max:255',
            'tinh_thanh' => 'nullable|string|max:255',
            'can_cuoc_cong_dan' => 'required|string|max:20|unique:sinh_vien,can_cuoc_cong_dan,' . $id,
            'ngay_cap_cccd' => 'nullable|date',
            'noi_cap_cccd' => 'nullable|string|max:255',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'khoa_hoc_id' => 'required|exists:khoa_hoc,id',
            'nganh_id' => 'required|exists:nganh,id',
            'chuyen_nganh_id' => 'nullable|exists:chuyen_nganh,id',
            'ky_hien_tai' => 'required|integer|min:1|max:8',
            'trang_thai_hoc_tap_id' => 'required|exists:trang_thai_hoc_tap,id',
        ]);

        // Validation: Chuyên ngành phải thuộc ngành đã chọn
        if ($validated['chuyen_nganh_id']) {
            $chuyenNganh = ChuyenNganh::find($validated['chuyen_nganh_id']);
            if (!$chuyenNganh || $chuyenNganh->nganh_id != $validated['nganh_id']) {
                return back()->withInput()->with('error', 'Chuyên ngành không thuộc ngành đã chọn!');
            }
        }

        DB::beginTransaction();
        try {
            // Xử lý upload ảnh mới
            if ($request->hasFile('anh_dai_dien')) {
                // Xóa ảnh cũ
                if ($sinhVien->anh_dai_dien) {
                    Storage::disk('public')->delete($sinhVien->anh_dai_dien);
                }
                $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('sinh-vien', 'public');
            }

            $sinhVien->update($validated);

            // Cập nhật email User nếu thay đổi
            if ($sinhVien->user && $sinhVien->user->email != $validated['email']) {
                $sinhVien->user->update(['email' => $validated['email']]);
            }

            DB::commit();

            return redirect()->route('dao-tao.sinh-vien.index')
                ->with('success', 'Cập nhật sinh viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sinhVien = SinhVien::findOrFail($id);

        DB::beginTransaction();
        try {
            // Xóa ảnh đại diện
            if ($sinhVien->anh_dai_dien) {
                Storage::disk('public')->delete($sinhVien->anh_dai_dien);
            }

            // Xóa User (sẽ cascade xóa sinh viên)
            if ($sinhVien->user) {
                $sinhVien->user->delete();
            }

            $sinhVien->delete();

            DB::commit();

            return redirect()->route('dao-tao.sinh-vien.index')
                ->with('success', 'Xóa sinh viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove multiple resources from storage.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));

        if (empty($ids)) {
            return back()->with('error', 'Không có sinh viên nào được chọn!');
        }

        DB::beginTransaction();
        try {
            $deleted = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $sinhVien = SinhVien::find($id);
                    if (!$sinhVien) {
                        $errors[] = "Sinh viên ID {$id} không tồn tại";
                        continue;
                    }

                    // Xóa ảnh đại diện
                    if ($sinhVien->anh_dai_dien) {
                        Storage::disk('public')->delete($sinhVien->anh_dai_dien);
                    }

                    // Xóa User (sẽ cascade xóa sinh viên)
                    if ($sinhVien->user) {
                        $sinhVien->user->delete();
                    }

                    $sinhVien->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Lỗi khi xóa sinh viên ID {$id}: " . $e->getMessage();
                    Log::error("Lỗi xóa sinh viên ID {$id}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Đã xóa thành công {$deleted} sinh viên.";
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()->route('dao-tao.sinh-vien.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xóa nhiều sinh viên: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('daotao.sinh-vien.import');
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="sinh_vien_template.csv"',
        ];

        $columns = [
            'ma_sinh_vien',
            'ho_ten',
            'email',
            'ngay_sinh',
            'gioi_tinh',
            'so_dien_thoai',
            'can_cuoc_cong_dan',
            'khoa_hoc',
            'nganh',
            'chuyen_nganh',
            'ky_hien_tai',
            'trang_thai',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header
            fputcsv($file, $columns);

            // Sample data
            fputcsv($file, [
                '2021600001',
                'Nguyễn Văn A',
                'nva@student.edu.vn',
                '2003-01-15',
                'nam',
                '0901234567',
                '001203012345',
                'K15', // Tên khóa học
                'Công nghệ thông tin', // Tên ngành
                'CNTT01', // Mã chuyên ngành
                '1',
                'Đang học', // Tên trạng thái
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import sinh viên từ Excel hoặc CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel hoặc CSV',
            'file.mimes' => 'File phải là Excel (.xlsx, .xls) hoặc CSV (.csv, .txt)',
            'file.max' => 'File không được vượt quá 5MB',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            $data = [];
            
            // Đọc file Excel
            if (in_array($extension, ['xlsx', 'xls'])) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                
                // Bỏ qua header (dòng đầu tiên)
                array_shift($data);
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
            $vaiTroSV = VaiTro::where('ma_vai_tro', 'sinh_vien')->first();

            foreach ($data as $rowNum => $row) {
                $rowNum += 2; // +2 vì bỏ header và index bắt đầu từ 0

                // Kiểm tra dòng trống
                if (empty($row[0])) {
                    continue;
                }

                try {
                    // Parse data - xử lý cả array từ Excel và CSV
                    $maSV = trim($row[0] ?? '');
                    $hoTen = trim($row[1] ?? '');
                    $email = trim($row[2] ?? '');
                    $ngaySinh = trim($row[3] ?? '');
                    $gioiTinh = trim($row[4] ?? '');
                    $sdt = trim($row[5] ?? '');
                    $cccd = trim($row[6] ?? '');
                    $tenKhoaHoc = trim($row[7] ?? ''); // Tên khóa học
                    $tenNganh = trim($row[8] ?? ''); // Tên ngành
                    $maChuyenNganh = trim($row[9] ?? ''); // Mã chuyên ngành
                    $kyHienTai = !empty($row[10]) ? (int)$row[10] : 1;
                    $tenTrangThai = trim($row[11] ?? ''); // Tên trạng thái 
                    
                    // Tìm khóa học theo tên
                    $khoaHoc = KhoaHoc::where('ten_khoa_hoc', $tenKhoaHoc)->first();
                    
                    // Tìm ngành theo tên hoặc mã
                    $nganh = Nganh::where('ten_nganh', $tenNganh)
                        ->orWhere('ma_nganh', $tenNganh)
                        ->first();
                    
                    // Tìm trạng thái theo tên
                    $trangThai = null;
                    if (!empty($tenTrangThai)) {
                        $trangThai = TrangThaiHocTap::where('ten_trang_thai', $tenTrangThai)->first();
                    }
                    
                    // Nếu không tìm thấy trạng thái, lấy mặc định "Đang học"
                    if (!$trangThai) {
                        $trangThai = TrangThaiHocTap::where('ten_trang_thai', 'Đang học')->first() 
                            ?? TrangThaiHocTap::first();
                    }

                    // Kiểm tra và báo lỗi chi tiết
                    if (!$khoaHoc) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy khóa học với tên: {$tenKhoaHoc}";
                        continue;
                    }
                    
                    if (!$nganh) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy ngành với tên/mã: {$tenNganh}";
                        continue;
                    }
                    
                    // Tìm chuyên ngành theo mã (nếu có) - sau khi đã kiểm tra ngành
                    $chuyenNganh = null;
                    if (!empty($maChuyenNganh)) {
                        $chuyenNganh = ChuyenNganh::where('ma_chuyen_nganh', $maChuyenNganh)->first();
                        if (!$chuyenNganh) {
                            $errors[] = "Dòng {$rowNum}: Không tìm thấy chuyên ngành với mã: {$maChuyenNganh}";
                            continue;
                        }
                        // Kiểm tra chuyên ngành có thuộc ngành đã chọn không
                        if ($chuyenNganh->nganh_id != $nganh->id) {
                            $errors[] = "Dòng {$rowNum}: Chuyên ngành {$maChuyenNganh} không thuộc ngành đã chọn";
                            continue;
                        }
                    }
                    
                    if (!$trangThai) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy trạng thái với tên/mã: {$tenTrangThai}";
                        continue;
                    }

                    // Parse ngày sinh
                    $ngaySinhParsed = null;
                    if (!empty($ngaySinh)) {
                        try {
                            $ngaySinhParsed = $this->parseDate($ngaySinh);
                        } catch (\Exception $e) {
                            $errors[] = "Dòng {$rowNum}: {$e->getMessage()}";
                            continue;
                        }
                    }

                    // Kiểm tra email trùng với sinh viên khác (nếu đang update)
                    $existingSinhVien = SinhVien::where('ma_sinh_vien', $maSV)->first();
                    if ($existingSinhVien && $existingSinhVien->email !== $email) {
                        if (SinhVien::where('email', $email)->where('id', '!=', $existingSinhVien->id)->exists()) {
                            $errors[] = "Dòng {$rowNum}: Email {$email} đã được sử dụng bởi sinh viên khác";
                            continue;
                        }
                    } elseif (!$existingSinhVien && SinhVien::where('email', $email)->exists()) {
                        $errors[] = "Dòng {$rowNum}: Email {$email} đã tồn tại";
                        continue;
                    }

                    // Tìm hoặc tạo User
                    $user = User::where('email', $email)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $hoTen,
                            'email' => $email,
                            'password' => Hash::make($maSV),
                            'trang_thai' => 'hoat_dong',
                            'email_verified_at' => now(),
                        ]);

                        // Gán vai trò
                        if ($vaiTroSV) {
                            $user->vaiTro()->attach($vaiTroSV->id, [
                                'nguoi_gan_id' => 1,
                                'ngay_gan' => now(),
                            ]);
                        }
                    } else {
                        // Update user nếu đã tồn tại
                        $user->update([
                            'name' => $hoTen,
                        ]);
                    }

                    // Update hoặc tạo sinh viên (dựa vào ma_sinh_vien)
                    $isNew = !SinhVien::where('ma_sinh_vien', $maSV)->exists();
                    $sinhVienData = [
                        'ho_ten' => $hoTen,
                        'email' => $email,
                        'ngay_sinh' => $ngaySinhParsed,
                        'gioi_tinh' => $gioiTinh,
                        'so_dien_thoai' => $sdt,
                        'can_cuoc_cong_dan' => $cccd,
                        'khoa_hoc_id' => $khoaHoc->id,
                        'nganh_id' => $nganh->id,
                        'ky_hien_tai' => $kyHienTai,
                        'trang_thai_hoc_tap_id' => $trangThai->id,
                        'user_id' => $user->id,
                    ];
                    
                    // Thêm chuyên ngành nếu có
                    if ($chuyenNganh) {
                        $sinhVienData['chuyen_nganh_id'] = $chuyenNganh->id;
                    }
                    
                    $sinhVien = SinhVien::updateOrCreate(
                        ['ma_sinh_vien' => $maSV],
                        $sinhVienData
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng {$rowNum}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Import thành công {$imported} sinh viên (đã tạo mới hoặc cập nhật).";
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 5));
            }

            return redirect()->route('dao-tao.sinh-vien.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi import: ' . $e->getMessage());
        }
    }
}
