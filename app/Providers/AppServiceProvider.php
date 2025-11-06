<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
<<<<<<< HEAD

use Illuminate\Support\Facades\View;


=======
use Illuminate\Support\Facades\View;
>>>>>>> origin/main
use App\Models\HocPhiHocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Observers\HocPhiHocKyObserver;
use App\Observers\KetQuaHocTapObserver;
use App\Observers\LopHocPhanSinhVienObserver;
use App\View\Composers\NotificationComposer;

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
<<<<<<< HEAD
<<<<<<< HEAD

        // Register View Composer for notifications in header
        View::composer('layouts.blocks.header', NotificationComposer::class);
=======
>>>>>>> 3ce5bf463aba81437bc908d45799f550b6b5f94d
=======

        // Register View Composer for notifications in header
        View::composer('layouts.blocks.header', NotificationComposer::class);
>>>>>>> origin/main
    }
}
