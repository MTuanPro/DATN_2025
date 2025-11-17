<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LopHocPhanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LopHocPhan::with(['monHoc', 'hocKy', 'giangVienChinh.giangVien']);

        // Lọc theo học kỳ
        if ($request->has('hoc_ky_id') && $request->hoc_ky_id != '') {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo môn học
        if ($request->has('mon_hoc_id') && $request->mon_hoc_id != '') {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai != '') {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop_hp', 'like', "%{$search}%")
                    ->orWhere('ten_lop_hp', 'like', "%{$search}%");
            });
        }

        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Tính số lượng thực tế từ bảng lop_hoc_phan_sinh_vien cho mỗi lớp
        // Sử dụng cùng logic với getLopHocPhanByMonHoc để đảm bảo đồng bộ
        $lopIds = $lopHocPhans->pluck('id')->toArray();
        $soLuongThucTe = LopHocPhanSinhVien::whereIn('lop_hoc_phan_id', $lopIds)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->selectRaw('lop_hoc_phan_id, COUNT(*) as so_luong')
            ->groupBy('lop_hoc_phan_id')
            ->pluck('so_luong', 'lop_hoc_phan_id')
            ->toArray();
        
        foreach ($lopHocPhans as $lop) {
            $lop->so_luong_thuc_te = $soLuongThucTe[$lop->id] ?? 0;
        }
        
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
        $monHocs = MonHoc::orderBy('ten_mon')->get();

        return view('daotao.lop-hoc-phan.index', compact('lopHocPhans', 'hocKys', 'monHocs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $monHocs = MonHoc::orderBy('ten_mon')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.lop-hoc-phan.create', compact('monHocs', 'hocKys'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_lop_hp' => 'required|string|max:20|unique:lop_hoc_phan,ma_lop_hp',
            'ten_lop_hp' => 'required|string|max:255',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'nhom_lop' => 'nullable|integer|min:1',
            'suc_chua' => 'required|integer|min:10|max:200',
            'so_luong_toi_thieu' => 'required|integer|min:5|lte:suc_chua',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url|required_if:hinh_thuc,online,hybrid',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'trang_thai_lop' => 'required|in:mo_dang_ky,dang_hoc,ket_thuc,huy',
            'ghi_chu' => 'nullable|string',
        ], [
            'ma_lop_hp.required' => 'Mã lớp học phần là bắt buộc',
            'ma_lop_hp.unique' => 'Mã lớp học phần đã tồn tại',
            'ten_lop_hp.required' => 'Tên lớp học phần là bắt buộc',
            'mon_hoc_id.required' => 'Môn học là bắt buộc',
            'mon_hoc_id.exists' => 'Môn học không tồn tại',
            'hoc_ky_id.required' => 'Học kỳ là bắt buộc',
            'hoc_ky_id.exists' => 'Học kỳ không tồn tại',
            'nhom_lop.min' => 'Nhóm lớp phải lớn hơn 0',
            'suc_chua.required' => 'Sức chứa là bắt buộc',
            'suc_chua.min' => 'Sức chứa phải từ 10 sinh viên trở lên',
            'suc_chua.max' => 'Sức chứa không được vượt quá 200',
            'so_luong_toi_thieu.required' => 'Số lượng tối thiểu là bắt buộc',
            'so_luong_toi_thieu.min' => 'Số lượng tối thiểu phải từ 5 sinh viên',
            'so_luong_toi_thieu.lte' => 'Số lượng tối thiểu phải nhỏ hơn hoặc bằng sức chứa',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
            'link_online.url' => 'Link online phải là URL hợp lệ',
            'link_online.required_if' => 'Link online là bắt buộc khi chọn hình thức Online hoặc Hybrid',
            'ngay_bat_dau.date' => 'Ngày bắt đầu phải là ngày hợp lệ',
            'ngay_ket_thuc.date' => 'Ngày kết thúc phải là ngày hợp lệ',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'trang_thai_lop.required' => 'Trạng thái là bắt buộc',
        ]);

        // Kiểm tra unique constraint: mon_hoc_id + hoc_ky_id + nhom_lop
        $nhomLop = $validated['nhom_lop'] ?? 1;
        $exists = LopHocPhan::where('mon_hoc_id', $validated['mon_hoc_id'])
            ->where('hoc_ky_id', $validated['hoc_ky_id'])
            ->where('nhom_lop', $nhomLop)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'nhom_lop' => 'Lớp học phần này đã tồn tại (cùng môn học, học kỳ và nhóm lớp). Vui lòng chọn nhóm lớp khác.'
            ]);
        }

        $validated['nhom_lop'] = $nhomLop;
        $validated['so_luong_dang_ky'] = 0; // Đặt giá trị mặc định
        
        // Kiểm tra nếu trạng thái là "Mở đăng ký" thì học kỳ phải đang mở đăng ký
        if ($validated['trang_thai_lop'] === 'mo_dang_ky') {
            $hocKy = HocKy::find($validated['hoc_ky_id']);
            
            if (!$hocKy->dang_mo_dang_ky) {
                return back()->withInput()->withErrors([
                    'trang_thai_lop' => 'Không thể mở đăng ký lớp học phần này vì học kỳ "' . $hocKy->ten_hoc_ky . '" chưa mở đăng ký. Vui lòng mở đăng ký học kỳ trước!'
                ]);
            }
        }
        
        LopHocPhan::create($validated);

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Tạo lớp học phần thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(LopHocPhan $lopHocPhan)
    {
        $lopHocPhan->load(['monHoc', 'hocKy', 'lopHocPhanGiangVien.giangVien', 'cauHinhDauDiem']);

        return view('daotao.lop-hoc-phan.show', compact('lopHocPhan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LopHocPhan $lopHocPhan)
    {
        $monHocs = MonHoc::orderBy('ten_mon')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.lop-hoc-phan.edit', compact('lopHocPhan', 'monHocs', 'hocKys'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LopHocPhan $lopHocPhan)
    {
        $validated = $request->validate([
            'ma_lop_hp' => 'required|string|max:20|unique:lop_hoc_phan,ma_lop_hp,' . $lopHocPhan->id,
            'ten_lop_hp' => 'required|string|max:255',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'nhom_lop' => 'nullable|integer|min:1',
            'suc_chua' => 'required|integer|min:10|max:200',
            'so_luong_toi_thieu' => 'required|integer|min:5|lte:suc_chua',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url|required_if:hinh_thuc,online,hybrid',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'trang_thai_lop' => 'required|in:mo_dang_ky,dang_hoc,ket_thuc,huy',
            'ghi_chu' => 'nullable|string',
        ], [
            'ma_lop_hp.required' => 'Mã lớp học phần là bắt buộc',
            'ma_lop_hp.unique' => 'Mã lớp học phần đã tồn tại',
            'ten_lop_hp.required' => 'Tên lớp học phần là bắt buộc',
            'mon_hoc_id.required' => 'Môn học là bắt buộc',
            'hoc_ky_id.required' => 'Học kỳ là bắt buộc',
            'nhom_lop.min' => 'Nhóm lớp phải lớn hơn 0',
            'suc_chua.required' => 'Sức chứa là bắt buộc',
            'so_luong_toi_thieu.required' => 'Số lượng tối thiểu là bắt buộc',
            'so_luong_toi_thieu.lte' => 'Số lượng tối thiểu phải nhỏ hơn hoặc bằng sức chứa',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
            'trang_thai_lop.required' => 'Trạng thái là bắt buộc',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
        ]);

        // Kiểm tra unique constraint: mon_hoc_id + hoc_ky_id + nhom_lop (trừ record hiện tại)
        $nhomLop = $validated['nhom_lop'] ?? 1;
        $exists = LopHocPhan::where('mon_hoc_id', $validated['mon_hoc_id'])
            ->where('hoc_ky_id', $validated['hoc_ky_id'])
            ->where('nhom_lop', $nhomLop)
            ->where('id', '!=', $lopHocPhan->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'nhom_lop' => 'Lớp học phần này đã tồn tại (cùng môn học, học kỳ và nhóm lớp). Vui lòng chọn nhóm lớp khác.'
            ]);
        }

        $validated['nhom_lop'] = $nhomLop;
        
        // Kiểm tra nếu trạng thái là "Mở đăng ký" thì học kỳ phải đang mở đăng ký
        if ($validated['trang_thai_lop'] === 'mo_dang_ky') {
            $hocKy = HocKy::find($validated['hoc_ky_id']);
            
            if (!$hocKy->dang_mo_dang_ky) {
                return back()->withInput()->withErrors([
                    'trang_thai_lop' => 'Không thể mở đăng ký lớp học phần này vì học kỳ "' . $hocKy->ten_hoc_ky . '" chưa mở đăng ký. Vui lòng mở đăng ký học kỳ trước!'
                ]);
            }
        }
        
        $lopHocPhan->update($validated);

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Cập nhật lớp học phần thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LopHocPhan $lopHocPhan)
    {
        // Kiểm tra xem lớp đã có sinh viên đăng ký chưa
        if ($lopHocPhan->so_luong_dang_ky > 0) {
            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('error', 'Không thể xóa lớp học phần đã có sinh viên đăng ký!');
        }

        $lopHocPhan->delete();

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Xóa lớp học phần thành công!');
    }

    /**
     * Đồng bộ lại số lượng đăng ký từ bảng lop_hoc_phan_sinh_vien
     */
    public function syncSoLuongDangKy()
    {
        try {
            DB::beginTransaction();

            // Lấy tất cả lớp học phần
            $lopHocPhans = LopHocPhan::all();
            $updated = 0;

            foreach ($lopHocPhans as $lop) {
                // Đếm số lượng sinh viên thực tế có trạng thái da_xep_lop hoặc dang_hoc
                $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                    ->count();

                // Cập nhật số lượng
                if ($lop->so_luong_dang_ky != $soLuongThucTe) {
                    $lop->so_luong_dang_ky = $soLuongThucTe;
                    $lop->save();
                    $updated++;
                }
            }

            DB::commit();

            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('success', "Đã đồng bộ lại số lượng đăng ký cho {$updated} lớp học phần!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('error', 'Lỗi khi đồng bộ: ' . $e->getMessage());
        }
    }
}
