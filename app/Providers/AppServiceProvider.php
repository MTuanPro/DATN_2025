<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\HocPhiHocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Models\DangKyMonHoc;
use App\Models\LichThi;
use App\Models\LichSuDongHocPhi;
use App\Observers\HocPhiHocKyObserver;
use App\Observers\KetQuaHocTapObserver;
use App\Observers\LopHocPhanSinhVienObserver;
use App\Observers\DangKyMonHocObserver;
use App\Observers\LichThiObserver;
use App\Observers\LichHocCoDinhObserver;
use App\Observers\CaHocObserver;
use App\Observers\LichSuDongHocPhiObserver;
use App\Models\LichHocCoDinh;
use App\Models\CaHoc;
use App\View\Composers\NotificationComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Register Observers for auto-calculation
        HocPhiHocKy::observe(HocPhiHocKyObserver::class);
        KetQuaHocTap::observe(KetQuaHocTapObserver::class);
        LopHocPhanSinhVien::observe(LopHocPhanSinhVienObserver::class);

        // Register Observers for auto-notifications
        DangKyMonHoc::observe(DangKyMonHocObserver::class);
        LichThi::observe(LichThiObserver::class);
        
        // Register Observer for auto-update registration status when tuition paid
        LichSuDongHocPhi::observe(LichSuDongHocPhiObserver::class);
        
        // Register Observer for auto-sync LichHocChiTiet
        LichHocCoDinh::observe(LichHocCoDinhObserver::class);
        
        // Register Observer for auto-sync LichHocCoDinh when CaHoc changes
        CaHoc::observe(CaHocObserver::class);

        // Register View Composer for notifications in header
        View::composer('layouts.blocks.header', NotificationComposer::class);
    }
}
