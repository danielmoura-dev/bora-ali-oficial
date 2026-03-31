<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MercadoPagoController extends Controller
{
    public function __construct(private MercadoPagoOAuthService $oauthService) {}

    public function connect(Request $request)
    {
        $state = csrf_token();
        $url   = $this->oauthService->getAuthorizationUrl($state);

        return redirect($url);
    }

    public function callback(Request $request)
    {
        $code = $request->input('code');

        if (!$code) {
            return redirect()->route('profile.edit')
                ->withErrors(['mp' => 'Autorização negada pelo Mercado Pago.']);
        }

        $tokens = $this->oauthService->exchangeCodeForTokens($code);

        if (!$tokens) {
            return redirect()->route('profile.edit')
                ->withErrors(['mp' => 'Erro ao conectar com o Mercado Pago.']);
        }

        $this->oauthService->saveTokens(Auth::user(), $tokens);

        return redirect()->route('mp.connected');
    }

    public function connected()
    {
        return view('mp.connected');
    }

    public function disconnect(Request $request)
    {
        $this->oauthService->disconnect(Auth::user());

        return redirect()->route('profile.edit')
            ->with('status', 'Mercado Pago desconectado.');
    }
}