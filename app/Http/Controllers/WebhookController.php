<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private OrderService   $orderService,
    ) {}

    public function mercadopago(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('x-signature', '');
        $requestId = $request->header('x-request-id', '');

        if (!$this->paymentService->validateWebhookSignature(
            $payload, $signature, $requestId
        )) {
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
        $order = Order::where('payment_id', $paymentId)->first();

        if (!$order || $order->isPaid()) {
            return;
        }

        $status = $this->paymentService->getPaymentStatus($paymentId);

        if ($status === 'paid') {
            $this->orderService->confirmPayment($order, $order->payment_method ?? 'pix');
            Log::info("Pedido {$order->reference} confirmado via webhook.");
        }

        if ($status === 'cancelled') {
            $order->forceFill(['status' => 'cancelled'])->save();
        }
    }
}