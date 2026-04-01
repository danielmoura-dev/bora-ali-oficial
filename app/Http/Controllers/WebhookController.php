<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmOrderJob;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{

    public function mercadopago(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('x-signature', '');
        $requestId = $request->header('x-request-id', '');
        $dataId    = $request->input('data.id', '');

        $provider = new \App\Payment\Providers\MercadoPagoProvider();

        if (!$provider->validateWebhookSignature($payload, $signature, $requestId, $dataId)) {
            Log::warning('Webhook MP: assinatura inválida');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $action = $request->input('action');
        $dataId = $request->input('data.id');

        Log::info("Webhook MP: {$action}", ['payment_id' => $dataId]);

        if ($action === 'payment.updated' && $dataId) {
            $order = Order::where('payment_id', $dataId)->first();

            if ($order && !$order->isPaid()) {
                ConfirmOrderJob::dispatch($order);
            }
        }

        return response()->json(['received' => true]);
    }
}