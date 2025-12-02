<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            $versionFile = base_path('.version');
            $version = file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'dev';
            $view->with('appVersion', $version);
        });
    }
}
