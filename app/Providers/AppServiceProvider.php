<?php

namespace App\Providers;
use Illuminate\Support\Facades\Blade;
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
        
        Blade::if('superadminadmin', function () {
            return auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin');
        });

        Blade::if('superadmin', function () {
            return auth()->check() && auth()->user()->role === 'superadmin';
        });

        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->role === 'admin';
        });

        Blade::if('manager', function () {
            return auth()->check() && auth()->user()->role === 'manager';
        });
    }
}
