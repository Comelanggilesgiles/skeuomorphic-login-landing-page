<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes — Nexa AI (login only)
|--------------------------------------------------------------------------
| Guests can view and submit the login form. Successful sign-in
| redirects to the dashboard; logout requires an active session.
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // throttle:5,1 = max 5 attempts per minute per IP (brute-force guard)
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Redirect target after a successful sign-in
Route::view('/dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Optional — Social sign-in (Laravel Socialite)
|--------------------------------------------------------------------------
| The GitHub / Google buttons on the login page point at these URLs.
| Install Socialite and uncomment to enable:
|
| Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
|     ->whereIn('provider', ['github', 'google'])
|     ->name('auth.social');
| Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
|     ->whereIn('provider', ['github', 'google']);
*/
