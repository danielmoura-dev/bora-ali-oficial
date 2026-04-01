<?php

namespace App\Services;

use App\Mail\OrderCancelledMail;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CancellationService
{
    const CANCELLATION_DEADLINE_HOURS = 48;

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->forceFill(['status' => 'cancelled'])->save();

            // Devolve estoque
            foreach ($order->items as $item) {
                $item->batch()->decrement('quantity_sold', $item->quantity);
            }

            Mail::to($order->user->email)
                ->queue(new OrderCancelledMail(
                    $order->load(['event', 'user', 'items.ticketType'])
                ));
        });
    }

    public function refundOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Em produção: chamar API do Mercado Pago/Pagar.me para reembolso
            if (!app()->environment('testing') && $order->payment_id) {
                $this->processGatewayRefund($order);
            }

            $order->forceFill(['status' => 'refunded'])->save();

            foreach ($order->items as $item) {
                $item->batch()->decrement('quantity_sold', $item->quantity);
            }

            Mail::to($order->user->email)
                ->queue(new OrderCancelledMail(
                    $order->load(['event', 'user', 'items.ticketType'])
                ));
        });
    }

    public function cancelEvent(Event $event): void
    {
        DB::transaction(function () use ($event) {
            $event->forceFill(['status' => 'cancelled'])->save();

            // Cancela todos os pedidos pagos do evento
            $orders = Order::where('event_id', $event->id)
                ->whereIn('status', ['paid', 'pending'])
                ->with(['user', 'items.batch', 'items.ticketType'])
                ->get();

            foreach ($orders as $order) {
                $order->forceFill(['status' => 'cancelled'])->save();

                foreach ($order->items as $item) {
                    $item->batch()->decrement('quantity_sold', $item->quantity);
                }

                Mail::to($order->user->email)
                    ->queue(new OrderCancelledMail($order));
            }
        });
    }

    public function canBuyerCancel(Order $order): bool
    {
        if ($order->status !== 'paid') {
            return false;
        }

        // Não cancela se o evento já aconteceu
        if ($order->event->isFinished()) {
            return false;
        }

        return true;
    }

    private function processGatewayRefund(Order $order): void
    {
        try {
            // Mercado Pago refund
            $client  = new \MercadoPago\Client\Payment\PaymentClient();
            $client->refund((int) $order->payment_id, [
                'amount' => $order->total / 100,
            ]);
        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'order'   => $order->reference,
                'message' => $e->getMessage(),
            ]);
        }
    }
}