<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    // Rotas que nunca devem ser interceptadas
    private array $bypassRoutes = [
        'auth.logout',
        'auth.verify.notice',
        'auth.verify.submit',
        'auth.verify.resend',
    ];

    private array $step3Routes = [
        'onboarding.step3',
        'onboarding.step3.send',
        'onboarding.step3.verify',
        'onboarding.step3.confirm',
    ];

    // Rotas de onboarding — acessíveis só com e-mail verificado
    private array $onboardingRoutes = [
        'onboarding.step2',
        'onboarding.step2.store',
        'onboarding.step3',
        'onboarding.step3.store',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $routeName = $request->route()?->getName();

        if (!$user) {
            return $next($request);
        }

        // Logout e verificação sempre passam
        if (in_array($routeName, $this->bypassRoutes)) {
            return $next($request);
        }

        // Sem e-mail verificado → verificação
        if (!$user->isEmailVerified()) {
            return redirect()->route('auth.verify.notice');
        }

        // Step 2 → só rotas do step2 passam
        if ($user->onboarding_step === 2) {
            if (in_array($routeName, ['onboarding.step2', 'onboarding.step2.store'])) {
                return $next($request);
            }
            return redirect()->route('onboarding.step2');
        }

        // Step 3 → só rotas do step3 passam
        if ($user->onboarding_step === 3) {
            if (in_array($routeName, $this->step3Routes)) {
                return $next($request);
            }
            return redirect()->route('onboarding.step3');
        }

        return $next($request);
    }
}