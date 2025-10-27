<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\HocPhiHocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Observers\HocPhiHocKyObserver;
use App\Observers\KetQuaHocTapObserver;
use App\Observers\LopHocPhanSinhVienObserver;

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
    }
}
