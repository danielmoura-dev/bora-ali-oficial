<?php

namespace App\Services;

use App\Models\Checkin;
use App\Models\Event;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class CheckinService
{
    public function scan(Event $event, string $ticketCode): array
    {
        $ticketCode = strtoupper(trim($ticketCode));

        // Busca o item pelo código
        $item = OrderItem::where('ticket_code', $ticketCode)
            ->with(['order.user', 'ticketType'])
            ->first();

        if (!$item) {
            return [
                'status'  => 'not_found',
                'message' => 'Ingresso não encontrado.',
            ];
        }

        // Verifica se é do evento correto
        if ($item->order->event_id !== $event->id) {
            return [
                'status'  => 'wrong_event',
                'message' => 'Este ingresso pertence a outro evento.',
            ];
        }

        // Verifica se já fez check-in
        if ($item->isCheckedIn()) {
            $checkin = $item->checkin;
            return [
                'status'      => 'already_checked_in',
                'message'     => 'Ingresso já utilizado.',
                'checked_in_at' => $checkin->checked_in_at->translatedFormat('d/m/Y \à\s H:i'),
            ];
        }

        // Realiza o check-in
        Checkin::create([
            'order_item_id' => $item->id,
            'event_id'      => $event->id,
            'checked_in_by' => Auth::id(),
            'ticket_code'   => $ticketCode,
            'checked_in_at' => now(),
        ]);

        return [
            'status'      => 'success',
            'message'     => 'Check-in realizado com sucesso!',
            'ticket_type' => $item->ticketType->name,
            'buyer'       => [
                'name'  => $item->order->user->name,
                'email' => $item->order->user->email,
            ],
        ];
    }

    public function stats(Event $event): array
    {
        // Total de ingressos vendidos e pagos para esse evento
        $total = OrderItem::whereHas('order', fn ($q) =>
            $q->where('event_id', $event->id)->where('status', 'paid')
        )->sum('quantity');

        $checkedIn = Checkin::where('event_id', $event->id)->count();
        $remaining = max(0, $total - $checkedIn);
        $percentage = $total > 0 ? round(($checkedIn / $total) * 100) : 0;

        return compact('total', 'checkedIn', 'remaining', 'percentage');
    }
}