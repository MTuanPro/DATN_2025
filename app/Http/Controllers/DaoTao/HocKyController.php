<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HocKyController extends Controller
{
    /**
     * Hiển thị danh sách học kỳ
     */
    public function index(Request $request)
    {
        $query = HocKy::query();

        // Tìm kiếm theo tên học kỳ hoặc năm học
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_hoc_ky', 'like', "%{$search}%")
                    ->orWhere('nam_hoc', 'like', "%{$search}%");
            });
        }

        // Lọc theo học kỳ hiện tại
        if ($request->filled('hien_tai')) {
            $query->where('la_hoc_ky_hien_tai', $request->hien_tai);
        }

        $hocKys = $query->orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->paginate(15);

        return view('daotao.hoc-ky.index', compact('hocKys'));
    }

    /**
     * Hiển thị form tạo học kỳ mới
     */
    public function create()
    {
        return view('daotao.hoc-ky.create');
    }

    /**
     * Lưu học kỳ mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_hoc_ky' => ['required', 'string', 'max:50'],
            'nam_hoc' => ['required', 'string', 'max:20'],
            'ngay_bat_dau' => ['required', 'date'],
            'ngay_ket_thuc' => ['required', 'date', 'after:ngay_bat_dau'],
            'ngay_bat_dau_dang_ky' => ['nullable', 'date'],
            'ngay_ket_thuc_dang_ky' => ['nullable', 'date', 'after:ngay_bat_dau_dang_ky'],
            'la_hoc_ky_hien_tai' => ['nullable', 'boolean'],
            'mo_ta' => ['nullable', 'string'],
        ], [
            'ten_hoc_ky.required' => 'Vui lòng nhập tên học kỳ',
            'nam_hoc.required' => 'Vui lòng nhập năm học',
            'ngay_bat_dau.required' => 'Vui lòng chọn ngày bắt đầu',
            'ngay_ket_thuc.required' => 'Vui lòng chọn ngày kết thúc',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'ngay_ket_thuc_dang_ky.after' => 'Ngày kết thúc đăng ký phải sau ngày bắt đầu đăng ký',
        ]);

        DB::beginTransaction();
        try {
            // Kiểm tra unique constraint
            $exists = HocKy::where('ten_hoc_ky', $validated['ten_hoc_ky'])
                ->where('nam_hoc', $validated['nam_hoc'])
                ->exists();

            if ($exists) {
                return back()->withInput()
                    ->with('error', 'Học kỳ này đã tồn tại trong năm học ' . $validated['nam_hoc']);
            }

            // Nếu đặt là học kỳ hiện tại, bỏ cờ của các học kỳ khác
            if ($request->la_hoc_ky_hien_tai) {
                HocKy::where('la_hoc_ky_hien_tai', true)->update(['la_hoc_ky_hien_tai' => false]);
            }

            HocKy::create($validated);

            DB::commit();
            return redirect()->route('daotao.hoc-ky.index')
                ->with('success', 'Thêm học kỳ thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị thông tin chi tiết học kỳ
     */
    public function show(HocKy $hocKy)
    {
        return view('daotao.hoc-ky.show', compact('hocKy'));
    }

    /**
     * Hiển thị form sửa học kỳ
     */
    public function edit(HocKy $hocKy)
    {
        return view('daotao.hoc-ky.edit', compact('hocKy'));
    }

    /**
     * Cập nhật học kỳ
     */
    public function update(Request $request, HocKy $hocKy)
    {
        $validated = $request->validate([
            'ten_hoc_ky' => ['required', 'string', 'max:50'],
            'nam_hoc' => ['required', 'string', 'max:20'],
            'ngay_bat_dau' => ['required', 'date'],
            'ngay_ket_thuc' => ['required', 'date', 'after:ngay_bat_dau'],
            'ngay_bat_dau_dang_ky' => ['nullable', 'date'],
            'ngay_ket_thuc_dang_ky' => ['nullable', 'date', 'after:ngay_bat_dau_dang_ky'],
            'la_hoc_ky_hien_tai' => ['nullable', 'boolean'],
            'mo_ta' => ['nullable', 'string'],
        ], [
            'ten_hoc_ky.required' => 'Vui lòng nhập tên học kỳ',
            'nam_hoc.required' => 'Vui lòng nhập năm học',
            'ngay_bat_dau.required' => 'Vui lòng chọn ngày bắt đầu',
            'ngay_ket_thuc.required' => 'Vui lòng chọn ngày kết thúc',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
        ]);

        DB::beginTransaction();
        try {
            // Kiểm tra unique constraint (trừ chính nó)
            $exists = HocKy::where('ten_hoc_ky', $validated['ten_hoc_ky'])
                ->where('nam_hoc', $validated['nam_hoc'])
                ->where('id', '!=', $hocKy->id)
                ->exists();

            if ($exists) {
                return back()->withInput()
                    ->with('error', 'Học kỳ này đã tồn tại trong năm học ' . $validated['nam_hoc']);
            }

            // Nếu đặt là học kỳ hiện tại, bỏ cờ của các học kỳ khác
            if ($request->la_hoc_ky_hien_tai && !$hocKy->la_hoc_ky_hien_tai) {
                HocKy::where('la_hoc_ky_hien_tai', true)->update(['la_hoc_ky_hien_tai' => false]);
            }

            $hocKy->update($validated);

            DB::commit();
            return redirect()->route('daotao.hoc-ky.index')
                ->with('success', 'Cập nhật học kỳ thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa học kỳ
     */
    public function destroy(HocKy $hocKy)
    {
        // Không cho phép xóa học kỳ hiện tại
        if ($hocKy->la_hoc_ky_hien_tai) {
            return back()->with('error', 'Không thể xóa học kỳ hiện tại!');
        }

        // Kiểm tra xem học kỳ có lớp học phần nào chưa
        if ($hocKy->lopHocPhans()->exists()) {
            return back()->with('error', 'Không thể xóa học kỳ đã có lớp học phần!');
        }

        try {
            $hocKy->delete();
            return redirect()->route('daotao.hoc-ky.index')
                ->with('success', 'Xóa học kỳ thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Thiết lập học kỳ hiện tại
     */
    public function setHienTai(HocKy $hocKy)
    {
        DB::beginTransaction();
        try {
            // Bỏ cờ của tất cả học kỳ khác
            HocKy::where('la_hoc_ky_hien_tai', true)->update(['la_hoc_ky_hien_tai' => false]);

            // Đặt học kỳ này là hiện tại
            $hocKy->update(['la_hoc_ky_hien_tai' => true]);

            DB::commit();
            return back()->with('success', 'Đã thiết lập học kỳ hiện tại!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Mở đăng ký môn học cho học kỳ
     */
    public function moDangKy(HocKy $hocKy)
    {
        // Kiểm tra xem đã có ngày đăng ký chưa
        if (!$hocKy->ngay_bat_dau_dang_ky || !$hocKy->ngay_ket_thuc_dang_ky) {
            return back()->with('error', 'Vui lòng thiết lập thời gian đăng ký trước!');
        }

        // Kiểm tra xem có phải học kỳ hiện tại không
        if (!$hocKy->la_hoc_ky_hien_tai) {
            return back()->with('error', 'Chỉ có thể mở đăng ký cho học kỳ hiện tại!');
        }

        try {
            // Logic mở đăng ký (có thể thêm flag mo_dang_ky vào bảng nếu cần)
            // Hiện tại chỉ kiểm tra thời gian
            $now = now();

            if ($now < $hocKy->ngay_bat_dau_dang_ky) {
                return back()->with('warning', 'Chưa đến thời gian mở đăng ký!');
            }

            if ($now > $hocKy->ngay_ket_thuc_dang_ky) {
                return back()->with('warning', 'Đã hết thời gian đăng ký!');
            }

            return back()->with('success', 'Học kỳ đang trong thời gian mở đăng ký môn học!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra trạng thái đăng ký
     */
    public function kiemTraDangKy(HocKy $hocKy)
    {
        $now = now();
        $status = 'chua_mo';
        $message = '';

        if (!$hocKy->ngay_bat_dau_dang_ky || !$hocKy->ngay_ket_thuc_dang_ky) {
            $status = 'chua_thiet_lap';
            $message = 'Chưa thiết lập thời gian đăng ký';
        } elseif ($now < $hocKy->ngay_bat_dau_dang_ky) {
            $status = 'chua_mo';
            $message = 'Chưa đến thời gian đăng ký';
        } elseif ($now > $hocKy->ngay_ket_thuc_dang_ky) {
            $status = 'da_dong';
            $message = 'Đã hết thời gian đăng ký';
        } else {
            $status = 'dang_mo';
            $message = 'Đang trong thời gian đăng ký';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'ngay_bat_dau' => $hocKy->ngay_bat_dau_dang_ky?->format('d/m/Y'),
            'ngay_ket_thuc' => $hocKy->ngay_ket_thuc_dang_ky?->format('d/m/Y'),
        ]);
    }
}
