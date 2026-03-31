<?php

namespace App\Providers;

use App\Payment\PaymentManager;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Carbon::setLocale('pt_BR');
    }

    public function register(): void
    {
        $this->app->singleton(PaymentManager::class);
    }
}