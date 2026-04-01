<?php

namespace App\Jobs;

use App\Models\Order;
use App\Payment\PaymentManager;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;
    public int $backoff = 10;

    public function __construct(public Order $order) {}

    public function handle(PaymentManager $paymentManager, OrderService $orderService): void
    {
        $order = $this->order->fresh(['event']);

        if (!$order || $order->isPaid()) {
            return;
        }

        $provider = $paymentManager->for($order->event);
        $status   = $provider->getPaymentStatus($order->payment_id);

        if ($status === 'paid') {
            $orderService->confirmPayment($order, $order->payment_method ?? 'pix');
            return;
        }

        if ($status === 'cancelled') {
            $order->forceFill(['status' => 'cancelled'])->save();
            return;
        }

        Log::info('ConfirmOrderJob: pagamento ainda pendente', [
            'order'  => $order->reference,
            'status' => $status,
        ]);
    }
}
