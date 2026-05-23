<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::redirect('/login', '/salih/login')->name('login');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect('/salih/login');
    }

    if (Auth::user()->hasRole('user')) {
        return redirect()->route('user.dashboard');
    }

    return redirect('/salih');
});

Route::middleware(['auth', 'role:user|super_admin'])->group(function (): void {
    Route::view('/dashboard', 'dashboard.user')->name('user.dashboard');
    Route::view('/kuran-okuma', 'quran.read')->name('user.quran-read');
    Route::view('/kuran-metin', 'quran.text')->name('user.quran-text');
    Route::view('/kuran-notlar', 'quran.notes-range')->name('user.quran-notes-range');
    Route::view('/ayarlar', 'settings.index')->name('user.settings');
});
