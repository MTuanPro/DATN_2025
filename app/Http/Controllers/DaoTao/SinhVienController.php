<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DanhMuc\TrangThaiHocTap;
use App\Models\GiangVien;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SinhVienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SinhVien::with([
            'lopHanhChinh',
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

        // Lọc theo lớp
        if ($request->filled('lop_hanh_chinh_id')) {
            $query->where('lop_hanh_chinh_id', $request->lop_hanh_chinh_id);
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
        $lopHanhChinhs = LopHanhChinh::orderBy('ma_lop')->get();
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $trangThais = TrangThaiHocTap::orderBy('ten_trang_thai')->get();

        return view('daotao.sinh-vien.index', compact('sinhViens', 'lopHanhChinhs', 'khoaHocs', 'nganhs', 'trangThais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lopHanhChinhs = LopHanhChinh::with(['khoaHoc', 'nganh'])->orderBy('ma_lop')->get();
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $chuyenNganhs = ChuyenNganh::with('nganh')->orderBy('ten_chuyen_nganh')->get();
        $trangThais = TrangThaiHocTap::orderBy('ten_trang_thai')->get();

        return view('daotao.sinh-vien.create', compact('lopHanhChinhs', 'khoaHocs', 'nganhs', 'chuyenNganhs', 'trangThais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_sinh_vien' => 'required|string|max:255|unique:sinh_vien,ma_sinh_vien',
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:sinh_vien,email',
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
            'lop_hanh_chinh_id' => 'required|exists:lop_hanh_chinh,id',
            'nganh_id' => 'required|exists:nganh,id',
            'chuyen_nganh_id' => 'nullable|exists:chuyen_nganh,id',
            'ky_hien_tai' => 'required|integer|min:1|max:8',
            'trang_thai_hoc_tap_id' => 'required|exists:trang_thai_hoc_tap,id',
        ], [
            'ma_sinh_vien.required' => 'Mã sinh viên là bắt buộc',
            'ma_sinh_vien.unique' => 'Mã sinh viên đã tồn tại',
            'email.unique' => 'Email đã tồn tại',
            'can_cuoc_cong_dan.unique' => 'Số CCCD đã tồn tại',
        ]);

        DB::beginTransaction();
        try {
            // Xử lý upload ảnh
            if ($request->hasFile('anh_dai_dien')) {
                $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('sinh-vien', 'public');
            }

            // Tạo tài khoản User
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['ma_sinh_vien']), // Mật khẩu mặc định là MSSV
                'trang_thai' => 'hoat_dong',
                'email_verified_at' => now(),
            ]);

            // Gán vai trò sinh_vien
            $vaiTroSinhVien = VaiTro::where('ma_vai_tro', 'sinh_vien')->first();
            if ($vaiTroSinhVien) {
                $user->vaiTro()->attach($vaiTroSinhVien->id, [
                    'nguoi_gan_id' => auth()->id(),
                    'ngay_gan' => now(),
                ]);
            }

            // Tạo sinh viên với user_id
            $validated['user_id'] = $user->id;

            // Lấy GVCN từ lớp hành chính
            $lopHanhChinh = LopHanhChinh::find($validated['lop_hanh_chinh_id']);
            if ($lopHanhChinh && $lopHanhChinh->giang_vien_chu_nhiem_id) {
                $validated['giang_vien_chu_nhiem_id'] = $lopHanhChinh->giang_vien_chu_nhiem_id;
            }

            $sinhVien = SinhVien::create($validated);

            // Cập nhật sĩ số lớp
            $lopHanhChinh->increment('si_so');

            DB::commit();

            return redirect()->route('dao-tao.sinh-vien.index')
                ->with('success', 'Thêm sinh viên thành công! Mật khẩu mặc định là MSSV.');
        } catch (\Exception $e) {
            DB::rollBack();
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
            'lopHanhChinh.nganh.khoa',
            'nganh.khoa',
            'chuyenNganh',
            'trangThaiHocTap',
            'giangVienChuNhiem'
        ])->findOrFail($id);

        return view('daotao.sinh-vien.show', compact('sinhVien'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sinhVien = SinhVien::findOrFail($id);
        $lopHanhChinhs = LopHanhChinh::with(['khoaHoc', 'nganh'])->orderBy('ma_lop')->get();
        $khoaHocs = KhoaHoc::orderBy('ten_khoa_hoc')->get();
        $nganhs = Nganh::with('khoa')->orderBy('ten_nganh')->get();
        $chuyenNganhs = ChuyenNganh::with('nganh')->orderBy('ten_chuyen_nganh')->get();
        $trangThais = TrangThaiHocTap::orderBy('ten_trang_thai')->get();

        return view('daotao.sinh-vien.edit', compact('sinhVien', 'lopHanhChinhs', 'khoaHocs', 'nganhs', 'chuyenNganhs', 'trangThais'));
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
            'lop_hanh_chinh_id' => 'required|exists:lop_hanh_chinh,id',
            'nganh_id' => 'required|exists:nganh,id',
            'chuyen_nganh_id' => 'nullable|exists:chuyen_nganh,id',
            'ky_hien_tai' => 'required|integer|min:1|max:8',
            'trang_thai_hoc_tap_id' => 'required|exists:trang_thai_hoc_tap,id',
        ]);

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

            // Kiểm tra nếu đổi lớp
            $lopCu = $sinhVien->lop_hanh_chinh_id;
            $lopMoi = $validated['lop_hanh_chinh_id'];

            if ($lopCu != $lopMoi) {
                // Giảm sĩ số lớp cũ
                LopHanhChinh::find($lopCu)->decrement('si_so');
                // Tăng sĩ số lớp mới
                LopHanhChinh::find($lopMoi)->increment('si_so');

                // Cập nhật GVCN từ lớp mới
                $lopHanhChinh = LopHanhChinh::find($lopMoi);
                if ($lopHanhChinh && $lopHanhChinh->giang_vien_chu_nhiem_id) {
                    $validated['giang_vien_chu_nhiem_id'] = $lopHanhChinh->giang_vien_chu_nhiem_id;
                }
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

            // Giảm sĩ số lớp
            if ($sinhVien->lopHanhChinh) {
                $sinhVien->lopHanhChinh->decrement('si_so');
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
            'ma_lop',
            'ma_khoa_hoc',
            'ma_nganh',
            'ky_hien_tai',
            'ma_trang_thai',
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
                'CNTT-K15-01',
                'K15',
                'CNTT',
                '1',
                'DANG_HOC',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import sinh viên từ CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file CSV',
            'file.mimes' => 'File phải là CSV (.csv, .txt)',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');

            if ($handle === false) {
                throw new \Exception('Không thể đọc file');
            }

            $imported = 0;
            $errors = [];
            $rowNum = 0;

            // Bỏ qua dòng header
            fgetcsv($handle);

            $vaiTroSV = VaiTro::where('ma_vai_tro', 'sinh_vien')->first();

            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;

                // Kiểm tra dòng trống
                if (empty($row[0])) {
                    continue;
                }

                try {
                    // Parse data
                    $maSV = $row[0];
                    $hoTen = $row[1];
                    $email = $row[2];
                    $ngaySinh = $row[3];
                    $gioiTinh = $row[4];
                    $sdt = $row[5];
                    $cccd = $row[6];
                    $maLop = $row[7];
                    $maKhoaHoc = $row[8];
                    $maNganh = $row[9];
                    $kyHienTai = $row[10] ?? 1;
                    $maTrangThai = $row[11] ?? '';

                    // Tìm ID từ mã
                    $lopHanhChinh = LopHanhChinh::where('ma_lop', $maLop)->first();
                    $khoaHoc = KhoaHoc::where('ma_khoa_hoc', $maKhoaHoc)->first();
                    $nganh = Nganh::where('ma_nganh', $maNganh)->first();
                    $trangThai = TrangThaiHocTap::first(); // Lấy trạng thái đầu tiên

                    if (!$lopHanhChinh || !$khoaHoc || !$nganh || !$trangThai) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy lớp/khóa/ngành/trạng thái";
                        continue;
                    }

                    // Kiểm tra trùng
                    if (SinhVien::where('ma_sinh_vien', $maSV)->exists()) {
                        $errors[] = "Dòng {$rowNum}: MSSV {$maSV} đã tồn tại";
                        continue;
                    }

                    if (SinhVien::where('email', $email)->exists()) {
                        $errors[] = "Dòng {$rowNum}: Email {$email} đã tồn tại";
                        continue;
                    }

                    // Tạo User
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

                    // Tạo sinh viên
                    SinhVien::create([
                        'ma_sinh_vien' => $maSV,
                        'ho_ten' => $hoTen,
                        'email' => $email,
                        'ngay_sinh' => $ngaySinh,
                        'gioi_tinh' => $gioiTinh,
                        'so_dien_thoai' => $sdt,
                        'can_cuoc_cong_dan' => $cccd,
                        'khoa_hoc_id' => $khoaHoc->id,
                        'lop_hanh_chinh_id' => $lopHanhChinh->id,
                        'nganh_id' => $nganh->id,
                        'ky_hien_tai' => $kyHienTai,
                        'trang_thai_hoc_tap_id' => $trangThai->id,
                        'giang_vien_chu_nhiem_id' => $lopHanhChinh->giang_vien_chu_nhiem_id,
                        'user_id' => $user->id,
                    ]);

                    // Tăng sĩ số lớp
                    $lopHanhChinh->increment('si_so');

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng {$rowNum}: " . $e->getMessage();
                }
            }

            fclose($handle);

            DB::commit();

            $message = "Import thành công {$imported} sinh viên.";
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 5));
            }

            return redirect()->route('dao-tao.sinh-vien.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            return back()->with('error', 'Có lỗi xảy ra khi import: ' . $e->getMessage());
        }
    }
}
