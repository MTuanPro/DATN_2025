<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\GiangVien;
use Illuminate\Http\Request;

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
}
