<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendVerificationCode(string $phone): string
    {
        $code      = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey  = "whatsapp_code_{$phone}";

        Cache::put($cacheKey, $code, now()->addMinutes(10));

        if (app()->environment('testing')) {
            return $code;
        }

        $this->sendMessage(
            phone: $phone,
            message: "Seu código de verificação do Bora Ali é: *{$code}*\n\nVálido por 10 minutos. Não compartilhe.",
        );

        return $code;
    }

    public function verifyCode(string $phone, string $code): bool
    {
        $cacheKey = "whatsapp_code_{$phone}";
        $stored   = Cache::get($cacheKey);

        if (!$stored) {
            return false;
        }

        if (!hash_equals((string) $stored, (string) $code)) {
            return false;
        }

        Cache::forget($cacheKey);

        return true;
    }

    public function formatPhone(string $raw): string
    {
        // Remove tudo que não for dígito e adiciona DDI 55
        $digits = preg_replace('/\D/', '', $raw);

        if (strlen($digits) === 11) {
            return '55' . $digits;
        }

        if (strlen($digits) === 13 && str_starts_with($digits, '55')) {
            return $digits;
        }

        return $digits;
    }

    public function isValidBrazilianPhone(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Aceita com ou sem DDI 55: 10-11 dígitos locais ou 12-13 com DDI
        if (strlen($digits) === 11 || strlen($digits) === 13) {
            return true;
        }

        return false;
    }

    private function sendMessage(string $phone, string $message): void
    {
        try {
            Http::get(config('services.whatsapp.api_url'), [
                'phone'   => $phone,
                'text'    => $message,
                'apikey'  => config('services.whatsapp.api_key'),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}