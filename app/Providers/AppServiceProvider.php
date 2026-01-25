<?php

namespace App\Providers;

use App\Models\Landlord\Tenant;
use Illuminate\Support\ServiceProvider;
use App\Observers\Landlord\TenantObserver;

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
        Tenant::observe(TenantObserver::class);
    }
}
