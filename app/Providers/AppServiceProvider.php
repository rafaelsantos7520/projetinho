<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\View::composer(['storefront.*', 'components.layouts.app'], function ($view) {
            if (app()->bound(\App\Models\Tenant::class)) {
                try {
                    $view->with('storeSettings', \App\Models\StoreSettings::current());
                } catch (\Exception $e) {
                    // Fallback silencioso
                }
            }
        });
    }
}
