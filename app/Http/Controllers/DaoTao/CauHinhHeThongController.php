<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\CauHinhHeThong;
use Illuminate\Http\Request;

class CauHinhHeThongController extends Controller
{
    /**
     * Hiển thị trang cấu hình điểm danh
     */
    public function diemDanh()
    {
        $choPhepTuongLai = CauHinhHeThong::choPhepDiemDanhTuongLai();

        return view('daotao.cau-hinh.diem-danh', compact('choPhepTuongLai'));
    }

    /**
     * Cập nhật cấu hình cho phép điểm danh tương lai
     */
    public function updateDiemDanh(Request $request)
    {
        // Checkbox không được gửi khi unchecked, nên cần xử lý đặc biệt
        $choPhepTuongLai = $request->has('cho_phep_tuong_lai') && $request->cho_phep_tuong_lai == '1';

        CauHinhHeThong::setGiaTri(
            'cho_phep_diem_danh_tuong_lai',
            $choPhepTuongLai,
            'Cho phép điểm danh tương lai',
            'Khi bật: Cho phép giảng viên điểm danh cho các buổi học trong tương lai. Khi tắt: Chỉ cho phép điểm danh trong ngày học.'
        );

        $message = $choPhepTuongLai 
            ? 'Đã bật tính năng cho phép điểm danh tương lai.' 
            : 'Đã tắt tính năng cho phép điểm danh tương lai.';

        return redirect()->route('dao-tao.cau-hinh.diem-danh')
            ->with('success', $message);
    }
}
