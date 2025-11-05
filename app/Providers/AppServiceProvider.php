<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <-- 1. TAMBAHKAN IMPORT INI

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
        // *** 2. TAMBAHKAN BARIS INI ***
        // Mengatur agar pagination menggunakan view Bootstrap 5
        Paginator::useBootstrapFive();
    }
}