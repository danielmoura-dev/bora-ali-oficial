<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    const PLATFORM_FEE_CENTS = 100; // R$ 1,00 por ingresso

    public function createOrder(int $userId, int $eventId, array $items): Order
    {
        return DB::transaction(function () use ($userId, $eventId, $items) {
            $subtotal    = 0;
            $totalItems  = 0;
            $orderItems  = [];

            foreach ($items as $item) {
                $batch    = TicketBatch::lockForUpdate()->findOrFail($item['batch_id']);
                $quantity = (int) $item['quantity'];

                if ($batch->remainingQuantity() < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Quantidade indisponível para o lote {$batch->name}.",
                    ]);
                }

                $itemSubtotal = $batch->price * $quantity;
                $subtotal    += $itemSubtotal;
                $totalItems  += $quantity;

                $orderItems[] = [
                    'batch'    => $batch,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $platformFee = self::PLATFORM_FEE_CENTS * $totalItems;

            $order = Order::create([
                'user_id'      => $userId,
                'event_id'     => $eventId,
                'reference'    => Order::generateReference(),
                'subtotal'     => $subtotal,
                'platform_fee' => $platformFee,
                'total'        => $subtotal + $platformFee,
                'status'       => 'pending',
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id'       => $order->id,
                    'ticket_batch_id'=> $item['batch']->id,
                    'ticket_type_id' => $item['batch']->ticket_type_id,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['batch']->price,
                    'subtotal'       => $item['subtotal'],
                ]);
            }

            return $order->load('items');
        });
    }

    public function confirmPayment(Order $order, string $paymentMethod): Order
    {
        return DB::transaction(function () use ($order, $paymentMethod) {
            // Mock: em produção, aqui chamará o Pagar.me
            $order->forceFill([
                'status'         => 'paid',
                'payment_method' => $paymentMethod,
                'payment_id'     => 'mock_' . uniqid(),
            ])->save();

            foreach ($order->items as $item) {
                // Gera código único para cada ingresso
                for ($i = 0; $i < $item->quantity; $i++) {
                    $item->forceFill([
                        'ticket_code' => OrderItem::generateTicketCode(),
                    ])->save();
                }

                // Decrementa estoque do lote
                $item->batch()->increment('quantity_sold', $item->quantity);
            }

            return $order->fresh(['items.batch', 'items.ticketType']);
        });
    }
}