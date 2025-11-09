<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\CauHinhHocPhi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CauHinhHocPhiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cauHinhs = CauHinhHocPhi::orderBy('ap_dung_tu_ngay', 'desc')
            ->paginate(10);

        return view('daotao.hoc-phi.cau-hinh.index', compact('cauHinhs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daotao.hoc-phi.cau-hinh.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nam_hoc' => 'required|string|max:255',
            'don_gia_tren_tin_chi' => 'required|numeric|min:0',
            'phi_dich_vu' => 'nullable|numeric|min:0',
            'ap_dung_tu_ngay' => 'required|date',
            'ap_dung_den_ngay' => 'nullable|date|after_or_equal:ap_dung_tu_ngay',
            'ghi_chu' => 'nullable|string',
        ], [
            'nam_hoc.required' => 'Năm học là bắt buộc',
            'don_gia_tren_tin_chi.required' => 'Đơn giá trên tín chỉ là bắt buộc',
            'don_gia_tren_tin_chi.numeric' => 'Đơn giá phải là số',
            'don_gia_tren_tin_chi.min' => 'Đơn giá phải lớn hơn hoặc bằng 0',
            'phi_dich_vu.numeric' => 'Phí dịch vụ phải là số',
            'phi_dich_vu.min' => 'Phí dịch vụ phải lớn hơn hoặc bằng 0',
            'ap_dung_tu_ngay.required' => 'Ngày áp dụng là bắt buộc',
            'ap_dung_tu_ngay.date' => 'Ngày áp dụng không hợp lệ',
            'ap_dung_den_ngay.date' => 'Ngày kết thúc không hợp lệ',
            'ap_dung_den_ngay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày áp dụng',
        ]);

        try {
            CauHinhHocPhi::create($validated);

            return redirect()
                ->route('daotao.hoc-phi.cau-hinh.index')
                ->with('success', 'Thêm cấu hình học phí thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CauHinhHocPhi $cauHinh)
    {
        return view('daotao.hoc-phi.cau-hinh.show', compact('cauHinh'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CauHinhHocPhi $cauHinh)
    {
        return view('daotao.hoc-phi.cau-hinh.edit', compact('cauHinh'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CauHinhHocPhi $cauHinh)
    {
        $validated = $request->validate([
            'nam_hoc' => 'required|string|max:255',
            'don_gia_tren_tin_chi' => 'required|numeric|min:0',
            'phi_dich_vu' => 'nullable|numeric|min:0',
            'ap_dung_tu_ngay' => 'required|date',
            'ap_dung_den_ngay' => 'nullable|date|after_or_equal:ap_dung_tu_ngay',
            'ghi_chu' => 'nullable|string',
        ], [
            'nam_hoc.required' => 'Năm học là bắt buộc',
            'don_gia_tren_tin_chi.required' => 'Đơn giá trên tín chỉ là bắt buộc',
            'don_gia_tren_tin_chi.numeric' => 'Đơn giá phải là số',
            'don_gia_tren_tin_chi.min' => 'Đơn giá phải lớn hơn hoặc bằng 0',
            'phi_dich_vu.numeric' => 'Phí dịch vụ phải là số',
            'phi_dich_vu.min' => 'Phí dịch vụ phải lớn hơn hoặc bằng 0',
            'ap_dung_tu_ngay.required' => 'Ngày áp dụng là bắt buộc',
            'ap_dung_tu_ngay.date' => 'Ngày áp dụng không hợp lệ',
            'ap_dung_den_ngay.date' => 'Ngày kết thúc không hợp lệ',
            'ap_dung_den_ngay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày áp dụng',
        ]);

        try {
            $cauHinh->update($validated);

            return redirect()
                ->route('daotao.hoc-phi.cau-hinh.index')
                ->with('success', 'Cập nhật cấu hình học phí thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CauHinhHocPhi $cauHinh)
    {
        try {
            $cauHinh->delete();

            return redirect()
                ->route('daotao.hoc-phi.cau-hinh.index')
                ->with('success', 'Xóa cấu hình học phí thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Get current active config
     */
    public function getCurrent()
    {
        $cauHinh = CauHinhHocPhi::getCauHinhHienTai();

        if (!$cauHinh) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có cấu hình học phí hiện tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cauHinh
        ]);
    }
}

