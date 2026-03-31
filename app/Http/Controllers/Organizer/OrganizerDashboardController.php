<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrganizerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // IDs dos eventos do organizador
        $eventIds = Event::where('user_id', $userId)->pluck('id');

        // Métricas gerais
        $totalRevenue = Order::whereIn('event_id', $eventIds)
            ->where('status', 'paid')
            ->sum('total');

        $totalPlatformFees = Order::whereIn('event_id', $eventIds)
            ->where('status', 'paid')
            ->sum('platform_fee');

        $totalTicketsSold = Order::whereIn('event_id', $eventIds)
            ->where('status', 'paid')
            ->withCount('items')
            ->get()
            ->sum('items_count');

        $pendingOrdersCount = Order::whereIn('event_id', $eventIds)
            ->where('status', 'pending')
            ->count();

        // Eventos com resumo de vendas
        $events = Event::where('user_id', $userId)
            ->withCount([
                'orders as paid_orders_count' => fn ($q) => $q->where('status', 'paid'),
                'orders as pending_orders_count' => fn ($q) => $q->where('status', 'pending'),
            ])
            ->withSum(
                ['orders as revenue' => fn ($q) => $q->where('status', 'paid')],
                'total'
            )
            ->orderByDesc('created_at')
            ->get();

        // Últimas vendas (10 mais recentes)
        $recentOrders = Order::whereIn('event_id', $eventIds)
            ->where('status', 'paid')
            ->with(['user', 'event', 'items'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('organizer.dashboard', compact(
            'totalRevenue',
            'totalPlatformFees',
            'totalTicketsSold',
            'pendingOrdersCount',
            'events',
            'recentOrders',
        ));
    }

    public function eventSales(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        Gate::authorize('update', $event);

        $orders = Order::where('event_id', $event->id)
            ->with(['user', 'items.ticketType', 'items.batch'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'total_revenue'  => Order::where('event_id', $event->id)
                ->where('status', 'paid')->sum('total'),
            'total_sold'     => Order::where('event_id', $event->id)
                ->where('status', 'paid')->count(),
            'total_pending'  => Order::where('event_id', $event->id)
                ->where('status', 'pending')->count(),
            'platform_fees'  => Order::where('event_id', $event->id)
                ->where('status', 'paid')->sum('platform_fee'),
        ];

        // Vendas por tipo de ingresso
        $salesByType = $event->ticketTypes()
            ->with(['batches'])
            ->withSum(
                ['batches as total_sold' => fn ($q) => $q],
                'quantity_sold'
            )
            ->get();

        return view('organizer.event-sales', compact('event', 'orders', 'stats', 'salesByType'));
    }
}