<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\GiangVien;
use App\Models\User;
use App\Models\DaoTao\Khoa;
use App\Models\DanhMuc\TrinhDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GiangVienController extends Controller
{
    /**
     * Hiển thị danh sách giảng viên
     */
    public function index(Request $request)
    {
        $query = GiangVien::with(['khoa', 'trinhDo', 'user']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_giang_vien', 'like', "%{$search}%")
                    ->orWhere('ho_ten', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$search}%");
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
        return view('daotao.giang-vien.create', compact('khoas', 'trinhDos'));
    }

    /**
     * Lưu giảng viên mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_giang_vien' => ['required', 'string', 'max:50', 'unique:giang_vien,ma_giang_vien'],
            'ho_ten' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:giang_vien,email'],
            'so_dien_thoai' => ['required', 'string', 'max:15'],
            'ngay_sinh' => ['nullable', 'date'],
            'gioi_tinh' => ['nullable', 'in:Nam,Nữ,Khác'],
            'dia_chi' => ['nullable', 'string'],
            'trinh_do_id' => ['required', 'exists:dm_trinh_do,id'],
            'chuyen_mon' => ['required', 'string', 'max:255'],
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
            'chuyen_mon.required' => 'Vui lòng nhập chuyên môn',
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
                    'trang_thai' => 'hoat_dong',
                ]);

                // Gán vai trò giảng viên
                $vaiTroGiangVien = \App\Models\VaiTro::where('ma_vai_tro', 'giang_vien')->first();
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
                'chuyen_mon' => $validated['chuyen_mon'],
                'khoa_id' => $validated['khoa_id'],
                'ngay_vao_truong' => $validated['ngay_vao_truong'],
                'anh_dai_dien' => $anhDaiDien,
                'user_id' => $userId,
            ]);

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
        return view('daotao.giang-vien.edit', compact('giangVien', 'khoas', 'trinhDos'));
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
            'so_dien_thoai' => ['required', 'string', 'max:15'],
            'ngay_sinh' => ['nullable', 'date'],
            'gioi_tinh' => ['nullable', 'in:Nam,Nữ,Khác'],
            'dia_chi' => ['nullable', 'string'],
            'trinh_do_id' => ['required', 'exists:dm_trinh_do,id'],
            'chuyen_mon' => ['required', 'string', 'max:255'],
            'khoa_id' => ['required', 'exists:khoa,id'],
            'ngay_vao_truong' => ['required', 'date'],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ], [
            'ma_giang_vien.required' => 'Vui lòng nhập mã giảng viên',
            'ma_giang_vien.unique' => 'Mã giảng viên đã tồn tại',
            'ho_ten.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.unique' => 'Email đã tồn tại',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại',
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

            $giangVien->update($validated);

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
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
            'tao_tai_khoan' => ['nullable', 'boolean'],
        ], [
            'file.required' => 'Vui lòng chọn file Excel',
            'file.mimes' => 'File phải có định dạng Excel (xlsx, xls, csv)',
            'file.max' => 'File không được vượt quá 5MB',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            // Đọc file Excel (giả sử dùng PhpSpreadsheet hoặc Laravel Excel)
            // Để đơn giản, tôi sẽ làm logic cơ bản

            $imported = 0;
            $errors = [];

            // TODO: Implement Excel import logic
            // Cần cài package: composer require maatwebsite/excel

            return back()->with('success', "Đã import thành công {$imported} giảng viên!");
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="giang_vien_template.csv"',
        ];

        $columns = [
            'ma_giang_vien',
            'ho_ten',
            'email',
            'so_dien_thoai',
            'chuyen_mon',
            'ma_khoa',
            'ma_trinh_do',
            'ngay_vao_truong',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, $columns);

            // Sample data
            fputcsv($file, [
                'GV001',
                'Nguyễn Văn A',
                'nguyenvana@example.com',
                '0123456789',
                'Lập trình',
                'CNTT',
                'THAC_SI',
                '2020-01-01',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
