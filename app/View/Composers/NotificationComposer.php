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
            // Lấy thông báo chưa đọc của user hiện tại
            $thongBaoChuaDoc = NguoiNhanThongBao::with(['thongBao' => function ($query) {
                $query->congKhai()
                    ->orderBy('ngay_gui', 'desc');
            }])
                ->where('nguoi_nhan_id', Auth::id())
                ->chuaDoc()
                ->take(10)
                ->get();

            // Đếm số thông báo chưa đọc
            $soThongBaoChuaDoc = NguoiNhanThongBao::where('nguoi_nhan_id', Auth::id())
                ->chuaDoc()
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
