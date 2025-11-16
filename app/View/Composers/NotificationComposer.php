<?php

namespace App\View\Composers;

use App\Models\NguoiNhanThongBao;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $now = now();

            // Lấy thông báo chưa đọc của user hiện tại với điều kiện đầy đủ
            $thongBaoChuaDoc = NguoiNhanThongBao::with(['thongBao.nguoiGui'])
                ->where('nguoi_nhan_id', $userId)
                ->chuaDoc()
                ->whereHas('thongBao', function ($query) use ($now) {
                    $query->where('trang_thai', 'cong_khai')
                        ->where(function ($q) use ($now) {
                            $q->whereNull('hien_thi_tu_ngay')
                                ->orWhere('hien_thi_tu_ngay', '<=', $now);
                        })
                        ->where(function ($q) use ($now) {
                            $q->whereNull('ngay_het_han')
                                ->orWhere('ngay_het_han', '>=', $now);
                        })
                        ->orderBy('ngay_gui', 'desc');
                })
                ->take(10)
                ->get();

            // Đếm số thông báo chưa đọc với điều kiện đầy đủ
            $soThongBaoChuaDoc = NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
                ->chuaDoc()
                ->whereHas('thongBao', function ($query) use ($now) {
                    $query->where('trang_thai', 'cong_khai')
                        ->where(function ($q) use ($now) {
                            $q->whereNull('hien_thi_tu_ngay')
                                ->orWhere('hien_thi_tu_ngay', '<=', $now);
                        })
                        ->where(function ($q) use ($now) {
                            $q->whereNull('ngay_het_han')
                                ->orWhere('ngay_het_han', '>=', $now);
                        });
                })
                ->count();

            $view->with([
                'thongBaoChuaDoc' => $thongBaoChuaDoc,
                'soThongBaoChuaDoc' => $soThongBaoChuaDoc
            ]);
        } else {
            $view->with([
                'thongBaoChuaDoc' => collect(),
                'soThongBaoChuaDoc' => 0
            ]);
        }
    }
}
