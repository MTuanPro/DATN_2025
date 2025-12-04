<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\GiangVien;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\DaoTao\Khoa;
use App\Models\DanhMuc\TrinhDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Traits\ImportHelper;

class GiangVienController extends Controller
{
    use ImportHelper;
    /**
     * Hiển thị danh sách giảng viên
     */
    public function index(Request $request)
    {
        $query = GiangVien::with(['khoa', 'trinhDo', 'user', 'monHocs']);

        // Tìm kiếm tương đối
        if ($request->filled('search')) {
            $search = trim($request->search);
            
            $query->where(function ($q) use ($search) {
                $q->where('ma_giang_vien', 'like', "%{$search}%")
                    ->orWhere('ho_ten', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$search}%")
                    ->orWhereHas('khoa', function ($query) use ($search) {
                        $query->where('ten_khoa', 'like', "%{$search}%")
                              ->orWhere('ma_khoa', 'like', "%{$search}%");
                    })
                    ->orWhereHas('trinhDo', function ($query) use ($search) {
                        $query->where('ten_trinh_do', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc theo khoa
        if ($request->filled('khoa_id')) {
            $query->where('khoa_id', $request->khoa_id);
        }

        // Lọc theo trình độ
        if ($request->filled('trinh_do_id')) {
            $query->where('trinh_do_id', $request->trinh_do_id);
        }

        $giangViens = $query->orderBy('created_at', 'desc')->paginate(15);
        $khoas = Khoa::all();
        $trinhDos = TrinhDo::all();

        return view('daotao.giang-vien.index', compact('giangViens', 'khoas', 'trinhDos'));
    }

    /**
     * Hiển thị form tạo giảng viên mới
     */
    public function create()
    {
        $khoas = Khoa::all();
        $trinhDos = TrinhDo::all();
        // Lấy tất cả môn học để hiển thị, sẽ lọc theo khoa bằng JavaScript
        $monHocs = \App\Models\DaoTao\MonHoc::with('khoa')->orderBy('ma_mon')->get();
        return view('daotao.giang-vien.create', compact('khoas', 'trinhDos', 'monHocs'));
    }

    /**
     * Lưu giảng viên mới
     */
    public function store(Request $request)
    {
        // Debug log
        \Log::info('Store giảng viên - Request data:', $request->all());

        $validated = $request->validate([
            'ma_giang_vien' => ['required', 'string', 'max:50', 'unique:giang_vien,ma_giang_vien'],
            'ho_ten' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:giang_vien,email'],
            'so_dien_thoai' => ['required', 'string', 'max:15'],
            'ngay_sinh' => ['nullable', 'date'],
            'gioi_tinh' => ['nullable', 'in:Nam,Nữ,Khác'],
            'dia_chi' => ['nullable', 'string'],
            'trinh_do_id' => ['required', 'exists:dm_trinh_do,id'],
            'mon_hoc_ids' => ['required', 'array', 'min:1'],
            'mon_hoc_ids.*' => ['exists:mon_hoc,id'],
            'khoa_id' => ['required', 'exists:khoa,id'],
            'ngay_vao_truong' => ['required', 'date'],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'tao_tai_khoan' => ['nullable', 'boolean'],
        ], [
            'ma_giang_vien.required' => 'Vui lòng nhập mã giảng viên',
            'ma_giang_vien.unique' => 'Mã giảng viên đã tồn tại',
            'ho_ten.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại',
            'trinh_do_id.required' => 'Vui lòng chọn trình độ',
            'mon_hoc_ids.required' => 'Vui lòng chọn ít nhất một môn học',
            'mon_hoc_ids.min' => 'Vui lòng chọn ít nhất một môn học',
            'khoa_id.required' => 'Vui lòng chọn khoa',
            'ngay_vao_truong.required' => 'Vui lòng chọn ngày vào trường',
        ]);

        DB::beginTransaction();
        try {
            // Kiểm tra email đã tồn tại trong bảng users chưa (nếu chọn tạo tài khoản)
            if ($request->tao_tai_khoan) {
                $existingUser = User::where('email', $validated['email'])->first();
                if ($existingUser) {
                    DB::rollBack();
                    return back()->withInput()
                        ->with('error', 'Email này đã được sử dụng cho tài khoản khác. Vui lòng sử dụng email khác hoặc bỏ chọn "Tạo tài khoản đăng nhập".');
                }
            }

            // Upload ảnh đại diện
            $anhDaiDien = null;
            if ($request->hasFile('anh_dai_dien')) {
                $anhDaiDien = $request->file('anh_dai_dien')->store('giang-vien', 'public');
            }

            // Tạo tài khoản user nếu được chọn
            $userId = null;
            if ($request->tao_tai_khoan) {
                $user = User::create([
                    'name' => $validated['ho_ten'],
                    'email' => $validated['email'],
                    'password' => Hash::make('12345678'), // Mật khẩu mặc định
                    'email_verified_at' => now(), // Xác thực email ngay
                    'trang_thai' => 'hoat_dong',
                ]);

                // Gán vai trò giảng viên
                $vaiTroGiangVien = VaiTro::where('ma_vai_tro', 'giang_vien')->first();
                if ($vaiTroGiangVien) {
                    $user->vaiTro()->attach($vaiTroGiangVien->id);
                }

                $userId = $user->id;
            }

            // Tạo giảng viên
            $giangVien = GiangVien::create([
                'ma_giang_vien' => $validated['ma_giang_vien'],
                'ho_ten' => $validated['ho_ten'],
                'email' => $validated['email'],
                'so_dien_thoai' => $validated['so_dien_thoai'],
                'ngay_sinh' => $validated['ngay_sinh'] ?? null,
                'gioi_tinh' => $validated['gioi_tinh'] ?? null,
                'dia_chi' => $validated['dia_chi'] ?? null,
                'trinh_do_id' => $validated['trinh_do_id'],
                'khoa_id' => $validated['khoa_id'],
                'ngay_vao_truong' => $validated['ngay_vao_truong'],
                'anh_dai_dien' => $anhDaiDien,
                'user_id' => $userId,
            ]);

            // Kiểm tra tất cả môn học được chọn phải thuộc khoa của giảng viên
            $monHocKhongThuocKhoa = \App\Models\DaoTao\MonHoc::whereIn('id', $validated['mon_hoc_ids'])
                ->where('khoa_id', '!=', $validated['khoa_id'])
                ->pluck('ma_mon', 'ten_mon')
                ->toArray();

            if (!empty($monHocKhongThuocKhoa)) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Các môn học sau không thuộc khoa đã chọn: ' . implode(', ', array_keys($monHocKhongThuocKhoa)));
            }

            // Gán môn học cho giảng viên
            $giangVien->monHocs()->sync($validated['mon_hoc_ids']);

            DB::commit();
            return redirect()->route('dao-tao.giang-vien.index')
                ->with('success', 'Thêm giảng viên thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            if ($anhDaiDien) {
                Storage::disk('public')->delete($anhDaiDien);
            }
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị thông tin chi tiết giảng viên
     */
    public function show(GiangVien $giangVien)
    {
        $giangVien->load(['khoa', 'trinhDo', 'user']);
        return view('daotao.giang-vien.show', compact('giangVien'));
    }

    /**
     * Hiển thị form sửa thông tin giảng viên
     */
    public function edit(GiangVien $giangVien)
    {
        $khoas = Khoa::all();
        $trinhDos = TrinhDo::all();
        // Lấy tất cả môn học để hiển thị, sẽ lọc theo khoa bằng JavaScript
        $monHocs = \App\Models\DaoTao\MonHoc::with('khoa')->orderBy('ma_mon')->get();
        return view('daotao.giang-vien.edit', compact('giangVien', 'khoas', 'trinhDos', 'monHocs'));
    }

    /**
     * Cập nhật thông tin giảng viên
     */
    public function update(Request $request, GiangVien $giangVien)
    {
        // Debug log
        \Log::info('Update giảng viên:', [
            'id' => $giangVien->id,
            'data' => $request->all()
        ]);

        $validated = $request->validate([
            'ma_giang_vien' => ['required', 'string', 'max:50', 'unique:giang_vien,ma_giang_vien,' . $giangVien->id],
            'ho_ten' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:giang_vien,email,' . $giangVien->id],
            'so_dien_thoai' => ['nullable', 'string', 'max:15'],
            'ngay_sinh' => ['nullable', 'date'],
            'gioi_tinh' => ['nullable', 'in:Nam,Nữ,Khác'],
            'dia_chi' => ['nullable', 'string'],
            'trinh_do_id' => ['required', 'exists:dm_trinh_do,id'],
            'mon_hoc_ids' => ['required', 'array', 'min:1'],
            'mon_hoc_ids.*' => ['exists:mon_hoc,id'],
            'khoa_id' => ['required', 'exists:khoa,id'],
            'ngay_vao_truong' => ['required', 'date'],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ], [
            'ma_giang_vien.required' => 'Vui lòng nhập mã giảng viên',
            'ma_giang_vien.unique' => 'Mã giảng viên đã tồn tại',
            'ho_ten.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.unique' => 'Email đã tồn tại',
            'mon_hoc_ids.required' => 'Vui lòng chọn ít nhất một môn học',
            'mon_hoc_ids.min' => 'Vui lòng chọn ít nhất một môn học',
        ]);

        DB::beginTransaction();
        try {
            // Upload ảnh mới nếu có
            if ($request->hasFile('anh_dai_dien')) {
                // Xóa ảnh cũ
                if ($giangVien->anh_dai_dien) {
                    Storage::disk('public')->delete($giangVien->anh_dai_dien);
                }
                $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('giang-vien', 'public');
            }

            // Lưu mon_hoc_ids trước khi update
            $monHocIds = $validated['mon_hoc_ids'];
            $khoaId = $validated['khoa_id'];
            unset($validated['mon_hoc_ids']);

            // Kiểm tra tất cả môn học được chọn phải thuộc khoa của giảng viên
            $monHocKhongThuocKhoa = \App\Models\DaoTao\MonHoc::whereIn('id', $monHocIds)
                ->where('khoa_id', '!=', $khoaId)
                ->pluck('ma_mon', 'ten_mon')
                ->toArray();

            if (!empty($monHocKhongThuocKhoa)) {
                return back()->withInput()
                    ->with('error', 'Các môn học sau không thuộc khoa đã chọn: ' . implode(', ', array_keys($monHocKhongThuocKhoa)));
            }

            $giangVien->update($validated);

            // Cập nhật môn học
            $giangVien->monHocs()->sync($monHocIds);

            // Cập nhật thông tin user nếu có
            if ($giangVien->user_id) {
                $giangVien->user->update([
                    'name' => $validated['ho_ten'],
                    'email' => $validated['email'],
                ]);
            }

            DB::commit();
            return redirect()->route('dao-tao.giang-vien.index')
                ->with('success', 'Cập nhật thông tin giảng viên thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa giảng viên (xóa thực sự khỏi database)
     */
    public function destroy(GiangVien $giangVien)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra ràng buộc trước khi xóa
            $canXoa = true;
            $lyDoKhongXoa = [];

            // Kiểm tra giảng viên có đang là trưởng khoa không
            $khoaTruongKhoa = \App\Models\DaoTao\Khoa::where('truong_khoa_id', $giangVien->id)->first();
            if ($khoaTruongKhoa) {
                $canXoa = false;
                $lyDoKhongXoa[] = "Giảng viên đang là trưởng khoa '{$khoaTruongKhoa->ten_khoa}'. Vui lòng thay đổi trưởng khoa trước khi xóa.";
            }

            // Kiểm tra giảng viên có đang chủ nhiệm lớp hành chính không
            $lopHanhChinh = \App\Models\DaoTao\LopHanhChinh::where('giang_vien_chu_nhiem_id', $giangVien->id)->count();
            if ($lopHanhChinh > 0) {
                $canXoa = false;
                $lyDoKhongXoa[] = "Giảng viên đang chủ nhiệm {$lopHanhChinh} lớp hành chính. Vui lòng thay đổi chủ nhiệm trước khi xóa.";
            }

            // Kiểm tra giảng viên có đang chủ nhiệm sinh viên không
            $sinhVienChuNhiem = \App\Models\DaoTao\SinhVien::where('giang_vien_chu_nhiem_id', $giangVien->id)->count();
            if ($sinhVienChuNhiem > 0) {
                $canXoa = false;
                $lyDoKhongXoa[] = "Giảng viên đang chủ nhiệm {$sinhVienChuNhiem} sinh viên. Vui lòng thay đổi chủ nhiệm trước khi xóa.";
            }

            if (!$canXoa) {
                DB::rollBack();
                return back()->with('error', implode(' ', $lyDoKhongXoa));
            }

            // Xóa các bản ghi liên quan trước
            // Xóa phân công giảng dạy (lop_hoc_phan_giang_vien) - có onDelete('cascade') nhưng để chắc chắn
            DB::table('lop_hoc_phan_giang_vien')->where('giang_vien_id', $giangVien->id)->delete();

            // Xóa lịch học chi tiết trước (vì có foreign key constraint)
            $soLichChiTiet = \App\Models\LichHocChiTiet::where('giang_vien_id', $giangVien->id)->count();
            if ($soLichChiTiet > 0) {
                \App\Models\LichHocChiTiet::where('giang_vien_id', $giangVien->id)->delete();
                \Log::info('Đã xóa lịch học chi tiết khi xóa giảng viên', [
                    'giang_vien_id' => $giangVien->id,
                    'so_lich_chi_tiet' => $soLichChiTiet
                ]);
            }

            // Xóa lịch học cố định (vì có foreign key constraint)
            $soLichCoDinh = \App\Models\LichHocCoDinh::where('giang_vien_id', $giangVien->id)->count();
            if ($soLichCoDinh > 0) {
                // Xóa lịch học chi tiết liên quan trước
                $lichCoDinhIds = \App\Models\LichHocCoDinh::where('giang_vien_id', $giangVien->id)->pluck('id');
                \App\Models\LichHocChiTiet::whereIn('lich_hoc_co_dinh_id', $lichCoDinhIds)->delete();

                // Sau đó xóa lịch học cố định
                \App\Models\LichHocCoDinh::where('giang_vien_id', $giangVien->id)->delete();
                \Log::info('Đã xóa lịch học cố định khi xóa giảng viên', [
                    'giang_vien_id' => $giangVien->id,
                    'so_lich_co_dinh' => $soLichCoDinh
                ]);
            }

            // Set null cho các trường liên quan
            DB::table('lop_hanh_chinh')->where('giang_vien_chu_nhiem_id', $giangVien->id)->update(['giang_vien_chu_nhiem_id' => null]);
            DB::table('sinh_vien')->where('giang_vien_chu_nhiem_id', $giangVien->id)->update(['giang_vien_chu_nhiem_id' => null]);
            DB::table('khoa')->where('truong_khoa_id', $giangVien->id)->update(['truong_khoa_id' => null]);

            // Xóa ảnh đại diện
            if ($giangVien->anh_dai_dien) {
                Storage::disk('public')->delete($giangVien->anh_dai_dien);
            }

            // Xóa user nếu có (cascade sẽ tự động xóa)
            if ($giangVien->user_id) {
                $giangVien->user->delete();
            }

            // Xóa thực sự khỏi database (force delete)
            $giangVien->forceDelete();

            DB::commit();
            return redirect()->route('dao-tao.giang-vien.index')
                ->with('success', 'Xóa giảng viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi xóa giảng viên', [
                'giang_vien_id' => $giangVien->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Import giảng viên từ Excel
     */
    public function showImportForm()
    {
        return view('daotao.giang-vien.import');
    }

    /**
     * Xử lý import từ Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
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

            // Lấy vai trò giảng viên
            $vaiTroGiangVien = VaiTro::where('ma_vai_tro', 'giang_vien')->first();

            foreach ($data as $rowNum => $row) {
                $rowNum += 2; // +2 vì bỏ header và index bắt đầu từ 0

                // Kiểm tra dòng trống
                if (empty($row[0])) {
                    continue;
                }

                try {
                    // Parse data - mapping theo cấu trúc: ma_giang_vien, ho_ten, email, khoa, trinh_do, so_dien_thoai, ngay_sinh, gioi_tinh, dia_chi, chuyen_mon, ngay_vao_truong
                    $maGV = trim($row[0] ?? '');
                    $hoTen = trim($row[1] ?? '');
                    $email = trim($row[2] ?? '');
                    $tenKhoa = trim($row[3] ?? '');
                    $tenTrinhDo = trim($row[4] ?? '');
                    $soDienThoai = trim($row[5] ?? '');
                    $ngaySinh = !empty($row[6]) ? trim($row[6]) : null;
                    $gioiTinh = !empty($row[7]) ? trim($row[7]) : null;
                    $diaChi = !empty($row[8]) ? trim($row[8]) : null;
                    $chuyenMon = !empty($row[9]) ? trim($row[9]) : 'Chưa xác định';
                    $ngayVaoTruong = !empty($row[10]) ? trim($row[10]) : null;

                    // Validate các trường bắt buộc
                    if (empty($maGV) || empty($hoTen) || empty($email) || empty($tenKhoa) || empty($soDienThoai)) {
                        $errors[] = "Dòng {$rowNum}: Thiếu thông tin bắt buộc (Mã GV, Họ tên, Email, Khoa, SĐT)";
                        continue;
                    }

                    // Kiểm tra email trùng với giảng viên khác (nếu đang update)
                    $existingGiangVien = GiangVien::where('ma_giang_vien', $maGV)->first();
                    if ($existingGiangVien && $existingGiangVien->email !== $email) {
                        if (GiangVien::where('email', $email)->where('id', '!=', $existingGiangVien->id)->exists()) {
                            $errors[] = "Dòng {$rowNum}: Email {$email} đã được sử dụng bởi giảng viên khác";
                            continue;
                        }
                    } elseif (!$existingGiangVien && GiangVien::where('email', $email)->exists()) {
                        $errors[] = "Dòng {$rowNum}: Email {$email} đã tồn tại";
                        continue;
                    }

                    // Tìm khoa theo tên hoặc mã khoa
                    $khoa = Khoa::where('ten_khoa', $tenKhoa)
                        ->orWhere('ma_khoa', $tenKhoa)
                        ->first();

                    if (!$khoa) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy khoa với tên/mã: {$tenKhoa}";
                        continue;
                    }
                    $khoaId = $khoa->id;

                    // Tìm trình độ theo tên (nếu có)
                    $trinhDoId = null;
                    if (!empty($tenTrinhDo)) {
                        $trinhDo = TrinhDo::where('ten_trinh_do', $tenTrinhDo)->first();
                        if (!$trinhDo) {
                            $errors[] = "Dòng {$rowNum}: Không tìm thấy trình độ với tên: {$tenTrinhDo}";
                            continue;
                        }
                        $trinhDoId = $trinhDo->id;
                    }

                    // Validate giới tính
                    if ($gioiTinh && !in_array($gioiTinh, ['Nam', 'Nữ', 'Khác'])) {
                        $errors[] = "Dòng {$rowNum}: Giới tính không hợp lệ (phải là: Nam, Nữ, Khác)";
                        continue;
                    }

                    // Parse ngày sinh
                    $ngaySinhDate = null;
                    if ($ngaySinh) {
                        try {
                            $ngaySinhDate = $this->parseDate($ngaySinh);
                        } catch (\Exception $e) {
                            $errors[] = "Dòng {$rowNum}: {$e->getMessage()}";
                            continue;
                        }
                    }

                    // Tạo tài khoản mặc định cho giảng viên (mật khẩu: 12345678)
                    $userId = null;

                    // Kiểm tra email đã tồn tại trong users chưa
                    $existingUser = User::where('email', $email)->first();
                    if ($existingUser) {
                        // Nếu user đã tồn tại, sử dụng user đó
                        $userId = $existingUser->id;
                        // Cập nhật tên nếu cần
                        $existingUser->update(['name' => $hoTen]);

                        // Kiểm tra và gán vai trò giảng viên nếu chưa có
                        if ($vaiTroGiangVien && !$existingUser->vaiTro->contains($vaiTroGiangVien->id)) {
                            $existingUser->vaiTro()->attach($vaiTroGiangVien->id, [
                                'nguoi_gan_id' => auth()->id() ?? 1,
                                'ngay_gan' => now(),
                            ]);
                        }
                    } else {
                        // Tạo user mới với mật khẩu mặc định 12345678
                        $user = User::create([
                            'name' => $hoTen,
                            'email' => $email,
                            'password' => Hash::make('12345678'), // Mật khẩu mặc định: 12345678
                            'trang_thai' => 'hoat_dong',
                            'email_verified_at' => now(),
                        ]);

                        // Gán vai trò giảng viên
                        if ($vaiTroGiangVien) {
                            $user->vaiTro()->attach($vaiTroGiangVien->id, [
                                'nguoi_gan_id' => auth()->id() ?? 1,
                                'ngay_gan' => now(),
                            ]);
                        }

                        $userId = $user->id;
                    }

                    // Parse ngày vào trường
                    $ngayVaoTruongDate = now()->format('Y-m-d');
                    if ($ngayVaoTruong) {
                        try {
                            $ngayVaoTruongDate = $this->parseDate($ngayVaoTruong);
                        } catch (\Exception $e) {
                            $errors[] = "Dòng {$rowNum}: {$e->getMessage()}";
                            continue;
                        }
                    }

                    // Update hoặc tạo giảng viên (dựa vào ma_giang_vien)
                    GiangVien::updateOrCreate(
                        ['ma_giang_vien' => $maGV],
                        [
                            'ho_ten' => $hoTen,
                            'email' => $email,
                            'so_dien_thoai' => $soDienThoai,
                            'ngay_sinh' => $ngaySinhDate,
                            'gioi_tinh' => $gioiTinh,
                            'dia_chi' => $diaChi,
                            'trinh_do_id' => $trinhDoId,
                            'chuyen_mon' => $chuyenMon,
                            'khoa_id' => $khoaId,
                            'ngay_vao_truong' => $ngayVaoTruongDate,
                            'user_id' => $userId,
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng {$rowNum}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Import thành công {$imported} giảng viên (đã tạo mới hoặc cập nhật).";
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 5));
            }

            return redirect()->route('dao-tao.giang-vien.index')
                ->with('success', $message)
                ->with('errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="giang_vien_template.csv"',
        ];

        $columns = [
            'ma_giang_vien',
            'ho_ten',
            'email',
            'khoa',
            'trinh_do',
            'so_dien_thoai',
            'ngay_sinh',
            'gioi_tinh',
            'dia_chi',
            'chuyen_mon',
            'ngay_vao_truong',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header
            fputcsv($file, $columns);

            // Sample data
            fputcsv($file, [
                'GV001',
                'Nguyễn Văn A',
                'nguyenvana@example.com',
                'Công nghệ thông tin', // Tên khoa
                'Thạc sĩ', // Tên trình độ
                '0123456789',
                '1985-05-15',
                'Nam',
                'Hà Nội',
                'Lập trình',
                '2020-01-01',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            return back()->with('error', 'Không có giảng viên nào được chọn!');
        }

        DB::beginTransaction();
        try {
            $deleted = 0;
            $errors = [];
            $skipped = 0;

            foreach ($ids as $id) {
                try {
                    $giangVien = GiangVien::find($id);
                    if (!$giangVien) {
                        $errors[] = "Giảng viên ID {$id} không tồn tại";
                        continue;
                    }

                    // Kiểm tra ràng buộc trước khi xóa
                    $canXoa = true;
                    $lyDoKhongXoa = [];

                    // Kiểm tra giảng viên có đang là trưởng khoa không
                    $khoaTruongKhoa = \App\Models\DaoTao\Khoa::where('truong_khoa_id', $giangVien->id)->first();
                    if ($khoaTruongKhoa) {
                        $canXoa = false;
                        $lyDoKhongXoa[] = "Giảng viên đang là trưởng khoa '{$khoaTruongKhoa->ten_khoa}'";
                    }

                    // Kiểm tra giảng viên có đang chủ nhiệm lớp hành chính không
                    $lopHanhChinh = \App\Models\DaoTao\LopHanhChinh::where('giang_vien_chu_nhiem_id', $giangVien->id)->count();
                    if ($lopHanhChinh > 0) {
                        $canXoa = false;
                        $lyDoKhongXoa[] = "Đang chủ nhiệm {$lopHanhChinh} lớp hành chính";
                    }

                    // Kiểm tra giảng viên có đang chủ nhiệm sinh viên không
                    $sinhVienChuNhiem = \App\Models\DaoTao\SinhVien::where('giang_vien_chu_nhiem_id', $giangVien->id)->count();
                    if ($sinhVienChuNhiem > 0) {
                        $canXoa = false;
                        $lyDoKhongXoa[] = "Đang chủ nhiệm {$sinhVienChuNhiem} sinh viên";
                    }

                    if (!$canXoa) {
                        $skipped++;
                        $errors[] = "Giảng viên {$giangVien->ho_ten} (ID: {$id}): " . implode(', ', $lyDoKhongXoa);
                        continue;
                    }

                    // Xóa các bản ghi liên quan trước
                    DB::table('lop_hoc_phan_giang_vien')->where('giang_vien_id', $giangVien->id)->delete();

                    // Xóa lịch học chi tiết
                    $soLichChiTiet = \App\Models\LichHocChiTiet::where('giang_vien_id', $giangVien->id)->count();
                    if ($soLichChiTiet > 0) {
                        \App\Models\LichHocChiTiet::where('giang_vien_id', $giangVien->id)->delete();
                    }

                    // Xóa lịch học cố định
                    $soLichCoDinh = \App\Models\LichHocCoDinh::where('giang_vien_id', $giangVien->id)->count();
                    if ($soLichCoDinh > 0) {
                        $lichCoDinhIds = \App\Models\LichHocCoDinh::where('giang_vien_id', $giangVien->id)->pluck('id');
                        \App\Models\LichHocChiTiet::whereIn('lich_hoc_co_dinh_id', $lichCoDinhIds)->delete();
                        \App\Models\LichHocCoDinh::where('giang_vien_id', $giangVien->id)->delete();
                    }

                    // Set null cho các trường liên quan
                    DB::table('lop_hanh_chinh')->where('giang_vien_chu_nhiem_id', $giangVien->id)->update(['giang_vien_chu_nhiem_id' => null]);
                    DB::table('sinh_vien')->where('giang_vien_chu_nhiem_id', $giangVien->id)->update(['giang_vien_chu_nhiem_id' => null]);
                    DB::table('khoa')->where('truong_khoa_id', $giangVien->id)->update(['truong_khoa_id' => null]);

                    // Xóa ảnh đại diện
                    if ($giangVien->anh_dai_dien) {
                        Storage::disk('public')->delete($giangVien->anh_dai_dien);
                    }

                    // Xóa user nếu có
                    if ($giangVien->user_id) {
                        $giangVien->user->delete();
                    }

                    // Xóa thực sự khỏi database
                    $giangVien->forceDelete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Lỗi khi xóa giảng viên ID {$id}: " . $e->getMessage();
                    \Log::error("Lỗi xóa giảng viên ID {$id}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Đã xóa thành công {$deleted} giảng viên.";
            if ($skipped > 0) {
                $message .= " Bỏ qua {$skipped} giảng viên do ràng buộc dữ liệu.";
            }
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()->route('dao-tao.giang-vien.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi xóa nhiều giảng viên: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }
}
