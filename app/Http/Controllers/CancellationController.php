<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Services\CancellationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CancellationController extends Controller
{
    public function __construct(private CancellationService $cancellationService) {}

    // Comprador cancela seu próprio pedido
    public function cancelOrder(string $reference)
    {
        $order = Order::where('reference', $reference)
            ->with(['event', 'user', 'items.batch', 'items.ticketType'])
            ->firstOrFail();

        // Só o dono do pedido pode cancelar
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$this->cancellationService->canBuyerCancel($order)) {
            return back()->withErrors([
                'order' => 'Este pedido não pode ser cancelado.',
            ]);
        }

        $this->cancellationService->cancelOrder($order);

        return redirect()->route('tickets.my')
            ->with('status', 'Pedido cancelado com sucesso.');
    }

    // Organizador reembolsa pedido específico
    public function refundOrder(string $reference)
    {
        $order = Order::where('reference', $reference)
            ->with(['event', 'user', 'items.batch', 'items.ticketType'])
            ->firstOrFail();

        Gate::authorize('update', $order->event);

        if (!in_array($order->status, ['paid'])) {
            return back()->withErrors([
                'order' => 'Este pedido não pode ser reembolsado.',
            ]);
        }

        $this->cancellationService->refundOrder($order);

        return redirect()->route('organizer.event.sales', $order->event->slug)
            ->with('status', "Pedido {$order->reference} reembolsado.");
    }

    // Organizador cancela o evento inteiro
    public function cancelEvent(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        Gate::authorize('delete', $event);

        $this->cancellationService->cancelEvent($event);

        return redirect()->route('events.show', $event->slug)
            ->with('status', 'Evento cancelado. Todos os compradores foram notificados.');
    }
}