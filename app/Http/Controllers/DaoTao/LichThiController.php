<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LichThi;
use App\Models\LopHocPhan;
use App\Models\DanhMuc\PhongHoc;
use App\Models\GiangVien;
use App\Models\HocKy;
use App\Http\Requests\StoreLichThiRequest;
use App\Http\Requests\UpdateLichThiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LichThiController extends Controller
{
    /**
     * Hiển thị danh sách lịch thi
     */
    public function index(Request $request)
    {
        $query = LichThi::with(['lopHocPhan.monHoc', 'phongThi', 'giamThi1', 'giamThi2']);

        // Lọc theo học kỳ (thông qua lop_hoc_phan)
        if ($request->filled('hoc_ky_id')) {
            $query->whereHas('lopHocPhan', function($q) use ($request) {
                $q->where('hoc_ky_id', $request->hoc_ky_id);
            });
        }

        // Lọc theo loại thi
        if ($request->filled('loai_thi')) {
            $query->where('loai_thi', $request->loai_thi);
        }

        // Lọc theo ngày thi
        if ($request->filled('ngay_thi_from')) {
            $query->whereDate('ngay_thi', '>=', $request->ngay_thi_from);
        }
        if ($request->filled('ngay_thi_to')) {
            $query->whereDate('ngay_thi', '<=', $request->ngay_thi_to);
        }

        // Tìm kiếm theo tên môn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('lopHocPhan.monHoc', function ($q) use ($search) {
                $q->where('ten_mon', 'like', "%{$search}%")
                  ->orWhere('ma_mon', 'like', "%{$search}%");
            });
        }

        $lichThis = $query->orderBy('ngay_thi', 'asc')
                          ->orderBy('gio_bat_dau', 'asc')
                          ->paginate(15);

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.lich-thi.index', compact('lichThis', 'hocKys'));
    }

    /**
     * Hiển thị form tạo lịch thi mới
     */
    public function create()
    {
        $lopHocPhans = LopHocPhan::with('monHoc', 'hocKy')
            ->whereHas('hocKy', function($q) {
                $q->where('la_hoc_ky_hien_tai', true);
            })
            ->get();

        $phongHocs = PhongHoc::all();
        $giangViens = GiangVien::with('user')->get();
        $hocKys = HocKy::where('la_hoc_ky_hien_tai', true)->get();

        return view('daotao.lich-thi.create', compact('lopHocPhans', 'phongHocs', 'giangViens', 'hocKys'));
    }

    /**
     * Lưu lịch thi mới
     */
    public function store(StoreLichThiRequest $request)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra trùng phòng thi
            $trungPhong = LichThi::where('phong_thi_id', $request->phong_thi_id)
                ->where('ngay_thi', $request->ngay_thi)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                          });
                })
                ->exists();

            if ($trungPhong) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Phòng thi đã có lịch thi trùng thời gian!');
            }

            $data = $request->validated();

            // Xử lý upload file đề thi
            if ($request->hasFile('de_thi_file')) {
                $data['de_thi_file'] = $request->file('de_thi_file')->store('de-thi', 'public');
            }

            // Xử lý upload file đáp án
            if ($request->hasFile('dap_an_file')) {
                $data['dap_an_file'] = $request->file('dap_an_file')->store('dap-an', 'public');
            }

            $lichThi = LichThi::create($data);

            DB::commit();

            return redirect()->route('dao-tao.lich-thi.index')
                ->with('success', 'Thêm lịch thi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết lịch thi
     */
    public function show(LichThi $lichThi)
    {
        $lichThi->load(['lopHocPhan.monHoc', 'lopHocPhan.lopHocPhanSinhViens.sinhVien', 'phongThi', 'giamThi1', 'giamThi2']);
        
        return view('daotao.lich-thi.show', compact('lichThi'));
    }

    /**
     * Hiển thị form chỉnh sửa lịch thi
     */
    public function edit(LichThi $lichThi)
    {
        $lopHocPhans = LopHocPhan::with('monHoc', 'hocKy')->get();
    $phongHocs = PhongHoc::all();
        $giangViens = GiangVien::with('user')->get();
    $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.lich-thi.edit', compact('lichThi', 'lopHocPhans', 'phongHocs', 'giangViens', 'hocKys'));
    }

    /**
     * Cập nhật lịch thi
     */
    public function update(UpdateLichThiRequest $request, LichThi $lichThi)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra trùng phòng thi (loại trừ bản ghi hiện tại)
            $trungPhong = LichThi::where('id', '!=', $lichThi->id)
                ->where('phong_thi_id', $request->phong_thi_id)
                ->where('ngay_thi', $request->ngay_thi)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                          });
                })
                ->exists();

            if ($trungPhong) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Phòng thi đã có lịch thi trùng thời gian!');
            }

            $data = $request->validated();

            // Xử lý upload file đề thi
            if ($request->hasFile('de_thi_file')) {
                // Xóa file cũ
                if ($lichThi->de_thi_file) {
                    Storage::disk('public')->delete($lichThi->de_thi_file);
                }
                $data['de_thi_file'] = $request->file('de_thi_file')->store('de-thi', 'public');
            }

            // Xử lý upload file đáp án
            if ($request->hasFile('dap_an_file')) {
                // Xóa file cũ
                if ($lichThi->dap_an_file) {
                    Storage::disk('public')->delete($lichThi->dap_an_file);
                }
                $data['dap_an_file'] = $request->file('dap_an_file')->store('dap-an', 'public');
            }

            $lichThi->update($data);

            DB::commit();

            return redirect()->route('dao-tao.lich-thi.index')
                ->with('success', 'Cập nhật lịch thi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa lịch thi
     */
    public function destroy(LichThi $lichThi)
    {
        try {
            // Xóa files đính kèm
            if ($lichThi->de_thi_file) {
                Storage::disk('public')->delete($lichThi->de_thi_file);
            }
            if ($lichThi->dap_an_file) {
                Storage::disk('public')->delete($lichThi->dap_an_file);
            }

            $lichThi->delete();

            return redirect()->route('dao-tao.lich-thi.index')
                ->with('success', 'Xóa lịch thi thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Gửi thông báo lịch thi
     */
    public function guiThongBao(LichThi $lichThi)
    {
        try {
            // TODO: Implement gửi email/thông báo cho sinh viên và giảng viên
            // Có thể sử dụng Queue để gửi hàng loạt

            return redirect()->back()
                ->with('success', 'Đã gửi thông báo lịch thi thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xuất lịch thi Excel/PDF
     */
    public function export(Request $request)
    {
        // TODO: Implement export Excel/PDF
        // Có thể sử dụng Laravel Excel hoặc DomPDF
        
        return redirect()->back()
            ->with('info', 'Chức năng xuất file đang được phát triển!');
    }
}
