<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoOAuthService
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId     = config('services.mercadopago.client_id');
        $this->clientSecret = config('services.mercadopago.client_secret');
        $this->redirectUri  = route('mp.callback');
    }

    public function getAuthorizationUrl(string $state): string
    {
        $params = http_build_query([
            'client_id'    => $this->clientId,
            'response_type'=> 'code',
            'platform_id'  => 'mp',
            'redirect_uri' => $this->redirectUri,
            'state'        => $state,
        ]);

        return "https://auth.mercadopago.com.br/authorization?{$params}";
    }

    public function exchangeCodeForTokens(string $code): ?array
    {
        try {
            $response = Http::post('https://api.mercadopago.com/oauth/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $this->redirectUri,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('MP OAuth exchange failed', $response->json());
            return null;

        } catch (\Exception $e) {
            Log::error('MP OAuth exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function saveTokens(User $user, array $tokens): void
    {
        $user->forceFill([
            'mp_access_token'    => $tokens['access_token'],
            'mp_refresh_token'   => $tokens['refresh_token'] ?? null,
            'mp_user_id'         => (string) $tokens['user_id'],
            'mp_token_expires_at'=> now()->addSeconds($tokens['expires_in'] ?? 15552000),
        ])->save();
    }

    public function disconnect(User $user): void
    {
        $user->forceFill([
            'mp_access_token'    => null,
            'mp_refresh_token'   => null,
            'mp_user_id'         => null,
            'mp_token_expires_at'=> null,
        ])->save();
    }
}