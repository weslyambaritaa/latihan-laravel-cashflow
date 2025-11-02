<?php

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Grup rute untuk pengguna yang BELUM login ('guest' middleware)
Route::middleware('guest')->group(function () {
    // Redirect root URL ke halaman login
    Route::get('/', fn () => redirect()->route('login'));
    
    // Halaman Auth
    Route::get('/login', fn () => view('pages.auth.login'))->name('login');
    Route::get('/register', fn () => view('pages.auth.register'))->name('register');
});

// Grup rute untuk pengguna yang SUDAH login ('auth' middleware)
Route::middleware('auth')->group(function () {

    // Grup Rute Aplikasi (URL: /app/...)
    Route::prefix('app')->name('app.')->group(function () {
        // Rute Home
        Route::get('/home', fn () => view('pages.app.home'))->name('home');

        // Rute Detail Cashflow
        // Perbaikan dilakukan di .name('cashflows.detail')
        Route::get('/cashflow/{id}', function ($id) {
            // Ambil data cashflow, pastikan milik user yang sedang login
            $cashflow = Cashflow::where('id', $id)
                                ->where('user_id', auth()->id()) 
                                ->firstOrFail(); 
            return view('pages.app.cashflow.detail', compact('cashflow'));
        })->name('cashflows.detail'); // <-- PERBAIKAN DI SINI: Menggunakan nama rute 'cashflows.detail'
    });

    // Rute Logout (URL: /logout)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login'); // Arahkan ke halaman login setelah logout
    })->name('logout');

});