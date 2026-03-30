<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $onboardingRoutes = [
            'auth.verify.notice',
            'auth.verify.submit',
            'auth.verify.resend',
            'auth.logout',
            'onboarding.step2',
            'onboarding.step2.store',
            'onboarding.step3',
            'onboarding.step3.store',
        ];

        if (in_array($request->route()?->getName(), $onboardingRoutes)) {
            return $next($request);
        }

        if (!$user->isEmailVerified()) {
            return redirect()->route('auth.verify.notice');
        }

        if ($user->onboarding_step === 2) {
            return redirect()->route('onboarding.step2');
        }

        if ($user->onboarding_step === 3) {
            return redirect()->route('onboarding.step3');
        }

        return $next($request);
    }
}