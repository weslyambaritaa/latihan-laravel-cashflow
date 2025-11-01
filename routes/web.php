<?php

use Illuminate\Support\Facades\Route; // <-- INI PERBAIKANNYA
use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth; // <-- Pastikan ini juga pakai backslash

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', fn () => view('pages.auth.login'))->name('login');
    Route::get('/register', fn () => view('pages.auth.register'))->name('register');
});

// Grup untuk semua yang sudah login
Route::middleware('auth')->group(function () {

    // Grup Rute Aplikasi (URL: /app/...)
    Route::prefix('app')->name('app.')->group(function () {
        Route::get('/home', fn () => view('pages.app.home'))->name('home');

        Route::get('/cashflow/{id}', function ($id) {
            $cashflow = Cashflow::where('id', $id)
                                ->where('user_id', auth()->id()) 
                                ->firstOrFail(); 
            return view('pages.app.cashflow.detail', compact('cashflow'));
        })->name('cashflow.detail');
    });

    // Rute Logout (URL: /logout)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login'); // Arahkan ke halaman login
    })->name('logout');

});