<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StockGudang;
use App\Models\ProdukSiapJual;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PaymentDisplaySetting;
use App\Policies\StockGudangPolicy;
use App\Policies\ProdukSiapJualPolicy;
use App\Policies\PembelianPolicy;
use App\Policies\PenjualanPolicy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrap();
        
        $this->registerPolicies();

        View::composer('layouts.guest', function ($view) {
            $setting = null;

            if (Schema::hasTable('payment_display_settings')) {
                $setting = PaymentDisplaySetting::first();
            }

            $view->with('guestPaymentSetting', $setting);
        });
    }

    /**
     * Register model policies
     */
    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::policy(StockGudang::class, StockGudangPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(ProdukSiapJual::class, ProdukSiapJualPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Pembelian::class, PembelianPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Penjualan::class, PenjualanPolicy::class);
    }
}
