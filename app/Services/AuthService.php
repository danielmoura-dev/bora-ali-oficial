<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Resend\Laravel\Facades\Resend;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->sendVerificationCode($user);

        return $user;
    }

    public function sendVerificationCode(User $user): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = now()->addMinutes(15);

        $user->forceFill([
            'verification_code' => $code,
            'verification_code_expires_at' => $expires,
        ])->save();

        if (app()->environment('testing')) {
            return;
        }

        // Em local, o código vai para storage/logs/laravel.log
        // Em produção, trocar MAIL_MAILER=resend no .env
        \Illuminate\Support\Facades\Log::info("=== CÓDIGO DE VERIFICAÇÃO ===", [
            'usuario' => $user->email,
            'codigo' => $code,
            'expira' => $expires,
        ]);
    }

    public function verifyCode(User $user, string $code): bool
    {
        $stored = $user->getAttributes()['verification_code'];
        $expires = $user->verification_code_expires_at;

        if (!$stored || !$expires) {
            return false;
        }

        if ($expires->isPast()) {
            return false;
        }

        if (!hash_equals($stored, $code)) {
            return false;
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
            'onboarding_step' => 2,
        ])->save();

        return true;
    }

    public function redirectAfterLogin(User $user): string
    {
        if (!$user->isEmailVerified()) {
            return route('auth.verify.notice');
        }

        if ($user->onboarding_step >= 4 || $user->hasCompletedOnboarding()) {
            return route('home');
        }

        return match ($user->onboarding_step) {
            2 => route('onboarding.step2'),
            3 => route('onboarding.step3'),
            default => route('auth.verify.notice'),
        };
    }
}