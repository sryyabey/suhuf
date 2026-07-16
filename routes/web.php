<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Share\NoteShareController;
use App\Http\Controllers\UserInviteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login',  [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->hasRole('user')) {
            return redirect()->route('user.dashboard');
        }
        return redirect('/salih');
    }

    // Dil bazlı private cache: tekrar ziyarette anında yüklenir (5 dk).
    return response()->view('welcome')
        ->header('Cache-Control', 'private, max-age=300')
        ->header('Vary', 'Accept-Language, Cookie');
})->name('home');

Route::middleware(['auth', 'role:user|super_admin'])->group(function (): void {
    Route::view('/dashboard', 'dashboard.user')->name('user.dashboard');
    Route::post('/dashboard/invites', [UserInviteController::class, 'store'])->name('user.invites.store');
    Route::view('/quran-reading', 'quran.read')->name('user.quran-read');
    Route::view('/quran-text', 'quran.text')->name('user.quran-text');
    Route::view('/quran-notes', 'quran.notes-range')->name('user.quran-notes-range');
    Route::view('/my-shares', 'shares.index')->name('user.shares');
    Route::view('/statistics', 'statistics.index')->name('user.statistics');
    Route::view('/settings', 'settings.index')->name('user.settings');
});

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])
    ->name('locale.switch')
    ->where('locale', 'tr|en');

Route::get('/share/notes/{token}', [NoteShareController::class, 'show'])->name('notes.share.show');

// Backward compatibility redirects for old Turkish slugs.
Route::redirect('/giris', '/login', 301);
Route::redirect('/kuran-okuma', '/quran-reading', 301);
Route::redirect('/kuran-metin', '/quran-text', 301);
Route::redirect('/kuran-notlar', '/quran-notes', 301);
Route::redirect('/paylasimlarim', '/my-shares', 301);
Route::redirect('/istatistikler', '/statistics', 301);
Route::redirect('/ayarlar', '/settings', 301);
Route::redirect('/paylas/notlar', '/share/notes', 301);
Route::get('/paylas/notlar/{token}', function (string $token) {
    return redirect()->route('notes.share.show', ['token' => $token], 301);
});
