<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
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
use App\Models\DiemDanh;
use App\Observers\DiemDanhObserver;
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

        // Register Observer for auto-recalculate results when attendance changes
        DiemDanh::observe(DiemDanhObserver::class);

        // Register View Composer for notifications in header
        View::composer('layouts.blocks.header', NotificationComposer::class);

        // ============ Blade Directives để kiểm tra quyền trong View ============

        /**
         * @can_permission('ma_quyen') - Kiểm tra user có quyền
         * Sử dụng: @can_permission('khoa.them') ... @endcan_permission
         */
        Blade::directive('can_permission', function ($permission) {
            return "<?php if(Auth::check() && Auth::user()->hasPermission({$permission})): ?>";
        });
        Blade::directive('endcan_permission', function () {
            return "<?php endif; ?>";
        });

        /**
         * @cannot_permission('ma_quyen') - Kiểm tra user KHÔNG có quyền
         */
        Blade::directive('cannot_permission', function ($permission) {
            return "<?php if(!Auth::check() || !Auth::user()->hasPermission({$permission})): ?>";
        });
        Blade::directive('endcannot_permission', function () {
            return "<?php endif; ?>";
        });

        /**
         * @can_any_permission(['quyen1', 'quyen2']) - Kiểm tra user có 1 trong các quyền
         */
        Blade::directive('can_any_permission', function ($permissions) {
            return "<?php if(Auth::check() && Auth::user()->hasAnyPermission({$permissions})): ?>";
        });
        Blade::directive('endcan_any_permission', function () {
            return "<?php endif; ?>";
        });
    }
}
