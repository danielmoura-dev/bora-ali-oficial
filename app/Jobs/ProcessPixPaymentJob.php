<?php

namespace App\Jobs;

use App\Models\Order;
use App\Payment\PaymentManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPixPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;
    public int $backoff = 10;

    public function __construct(public Order $order) {}

    public function handle(PaymentManager $paymentManager): void
    {
        $order = $this->order->fresh(['user', 'event.organizer', 'items']);

        $provider = $paymentManager->for($order->event);
        $result   = $provider->processPixPayment($order);

        if ($result['status'] === 'pending_pix') {
            $order->forceFill([
                'payment_id'       => $result['payment_id'],
                'payment_metadata' => $result,
            ])->save();

            return;
        }

        Log::error('ProcessPixPaymentJob: falha ao gerar Pix', [
            'order'   => $order->reference,
            'message' => $result['message'] ?? 'unknown',
        ]);
    }
}
