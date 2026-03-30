<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Onboarding\OnboardingController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', fn () => view('home'))->name('home');

// Auth — visitantes
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisterController::class, 'show'])->name('auth.register');
    Route::post('/register', [RegisterController::class, 'store'])->name('auth.register.store');

    Route::get('/login',  [LoginController::class, 'show'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'store'])->name('auth.login.store');

    Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// Auth — autenticados COM middleware de onboarding
Route::middleware(['auth', 'onboarding'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('auth.logout');

    Route::get('/verify',         [VerificationController::class, 'notice'])->name('auth.verify.notice');
    Route::post('/verify',        [VerificationController::class, 'submit'])->name('auth.verify.submit');
    Route::post('/verify/resend', [VerificationController::class, 'resend'])->name('auth.verify.resend');

    Route::get('/onboarding/step2',  [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/step2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store');

    Route::get('/onboarding/step3',  [OnboardingController::class, 'step3'])->name('onboarding.step3');
});