<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function notice()
    {
        if (Auth::user()->isEmailVerified()) {
            return redirect()->route('onboarding.step2');
        }

        return view('auth.verify');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $verified = $this->authService->verifyCode(
            Auth::user(),
            $request->code
        );

        if (!$verified) {
            return back()->withErrors([
                'code' => 'Código inválido ou expirado.',
            ]);
        }

        return redirect()->route('onboarding.step2');
    }

    public function resend()
    {
        $user = Auth::user();

        if ($user->isEmailVerified()) {
            return redirect()->route('onboarding.step2');
        }

        $this->authService->sendVerificationCode($user);

        return back()->with('status', 'Novo código enviado para o seu e-mail.');
    }
}