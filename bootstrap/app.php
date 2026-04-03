<?php

use App\Http\Middleware\EnsureOnboardingComplete;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'onboarding' => EnsureOnboardingComplete::class,
        ]);

        $middleware->redirectGuestsTo(fn() => route('auth.login'));

        // Exclui webhook do CSRF
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
