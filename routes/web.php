<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Onboarding\OnboardingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth — visitantes
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisterController::class, 'show'])->name('auth.register');
    Route::post('/register', [RegisterController::class, 'store'])->name('auth.register.store');

    Route::get('/login',  [LoginController::class, 'show'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'store'])->name('auth.login.store');

    Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// Auth — autenticados com middleware de onboarding
Route::middleware(['auth', 'onboarding'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('auth.logout');

    // Verificação de e-mail
    Route::get('/verify',         [VerificationController::class, 'notice'])->name('auth.verify.notice');
    Route::post('/verify',        [VerificationController::class, 'submit'])->name('auth.verify.submit');
    Route::post('/verify/resend', [VerificationController::class, 'resend'])->name('auth.verify.resend');

    // Onboarding Step 2
    Route::get('/onboarding/step2',  [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store');

    // Onboarding Step 3
    Route::get('/onboarding/step3',          [OnboardingController::class, 'step3'])->name('onboarding.step3');
    Route::post('/onboarding/step3/send',    [OnboardingController::class, 'step3Send'])->name('onboarding.step3.send');
    Route::get('/onboarding/step3/verify',   [OnboardingController::class, 'step3Verify'])->name('onboarding.step3.verify');
    Route::post('/onboarding/step3/confirm', [OnboardingController::class, 'step3Confirm'])->name('onboarding.step3.confirm');
});