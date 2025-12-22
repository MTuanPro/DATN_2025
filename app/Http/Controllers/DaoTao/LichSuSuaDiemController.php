<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LichSuSuaDiem;
use App\Models\DaoTao\SinhVien;
use App\Models\MonHoc;
use App\Models\HocKy;

class LichSuSuaDiemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LichSuSuaDiem::with([
            'lopHocPhanSinhVien.sinhVien',
            'lopHocPhanSinhVien.lopHocPhan.monHoc',
            'lopHocPhanSinhVien.lopHocPhan.hocKy',
            'nguoiSua'
        ]);

        // Tìm kiếm theo từ khóa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('lopHocPhanSinhVien.sinhVien', function ($q) use ($search) {
                    $q->where('ho_ten', 'like', "%{$search}%")
                      ->orWhere('ma_sinh_vien', 'like', "%{$search}%");
                })
                ->orWhereHas('lopHocPhanSinhVien.lopHocPhan.monHoc', function ($q) use ($search) {
                    $q->where('ten_mon', 'like', "%{$search}%")
                      ->orWhere('ma_mon', 'like', "%{$search}%");
                })
                ->orWhere('cot_diem', 'like', "%{$search}%");
            });
        }

        $lichSuSuaDiem = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('dao-tao.lich-su-sua-diem.index', compact('lichSuSuaDiem'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lichSu = LichSuSuaDiem::with([
            'lopHocPhanSinhVien.sinhVien',
            'lopHocPhanSinhVien.lopHocPhan.monHoc',
            'lopHocPhanSinhVien.lopHocPhan.hocKy',
            'nguoiSua'
        ])->findOrFail($id);

        return view('dao-tao.lich-su-sua-diem.show', compact('lichSu'));
    }
}
