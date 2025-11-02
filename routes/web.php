<?php

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ... (Komentar dihilangkan untuk fokus pada kode)

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    
    // PERBAIKAN UTAMA: Cegah user yang sudah login mengakses login/register
    Route::get('/login', function () {
        if (auth()->check()) {
            return redirect()->route('app.home'); // Jika sudah login, lempar ke Home
        }
        return view('pages.auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        if (auth()->check()) {
            return redirect()->route('app.home'); // Jika sudah login, lempar ke Home
        }
        return view('pages.auth.register');
    })->name('register');
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
        })->name('cashflows.detail');
    });

    // Rute Logout (URL: /logout)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login'); // Arahkan ke halaman login
    })->name('logout');

});