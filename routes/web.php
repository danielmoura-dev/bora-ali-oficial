<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

// Página inicial (placeholder por enquanto)
Route::get('/', fn () => view('home'))->name('home');

// Auth — somente para visitantes
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisterController::class, 'show'])->name('auth.register');
    Route::post('/register', [RegisterController::class, 'store'])->name('auth.register.store');

    Route::get('/login',  [LoginController::class, 'show'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'store'])->name('auth.login.store');
});

// Auth — somente para usuários autenticados
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('auth.logout');

    Route::get('/verify',        [VerificationController::class, 'notice'])->name('auth.verify.notice');
    Route::post('/verify',       [VerificationController::class, 'submit'])->name('auth.verify.submit');
    Route::post('/verify/resend',[VerificationController::class, 'resend'])->name('auth.verify.resend');

    // Placeholders de onboarding (próximo módulo)
    Route::get('/onboarding/step2', fn () => 'Step 2')->name('onboarding.step2');
    Route::get('/onboarding/step3', fn () => 'Step 3')->name('onboarding.step3');
});