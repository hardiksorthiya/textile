<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

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
        // Use Bootstrap 5 pagination
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        $settings = Schema::hasTable('settings') ? Setting::first() : null;
        View::share('appSettings', $settings);

        // Explicit route model binding for damage details
        \Illuminate\Support\Facades\Route::bind('damageDetail', function ($value) {
            return \App\Models\DamageDetail::findOrFail($value);
        });
        
        // Explicit route model binding for damage images
        \Illuminate\Support\Facades\Route::bind('damageImage', function ($value) {
            return \App\Models\DamageImage::findOrFail($value);
        });
    }
}
