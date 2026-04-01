<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Payment\PaymentManager;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentManager $paymentManager,
        private OrderService   $orderService,
    ) {}

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
            $this->handlePaymentUpdate((string) $dataId);
        }

        return response()->json(['received' => true]);
    }

    private function handlePaymentUpdate(string $paymentId): void
    {
        $order = Order::where('payment_id', $paymentId)
            ->with('event')
            ->first();

        if (!$order || $order->isPaid()) {
            return;
        }

        $provider = $this->paymentManager->for($order->event);
        $status   = $provider->getPaymentStatus($paymentId);

        if ($status === 'paid') {
            $this->orderService->confirmPayment($order, $order->payment_method ?? 'pix');
        }

        if ($status === 'cancelled') {
            $order->forceFill(['status' => 'cancelled'])->save();
        }
    }
}