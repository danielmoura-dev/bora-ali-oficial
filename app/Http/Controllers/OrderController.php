<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPixPaymentJob;
use App\Models\Event;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function store(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $request->validate([
            'items'            => ['required', 'array', 'min:1'],
            'items.*.batch_id' => ['required', 'integer', 'exists:ticket_batches,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $items = collect($request->input('items'))
            ->filter(fn ($i) => (int)($i['quantity'] ?? 0) > 0)
            ->values()
            ->toArray();

        if (empty($items)) {
            return back()->withErrors(['items' => 'Selecione ao menos um ingresso.']);
        }

        $order = $this->orderService->createOrder(
            userId:  Auth::id(),
            eventId: $event->id,
            items:   $items,
        );

        return redirect()->route('orders.checkout', $order->reference);
    }

    public function checkout(string $reference)
    {
        $order = Order::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->with(['items.batch.ticketType', 'event'])
            ->firstOrFail();

        if ($order->isPaid()) {
            return redirect()->route('orders.success', $order->reference);
        }

        $mpPublicKey = config('services.mercadopago.public_key');

        return view('orders.checkout', compact('order', 'mpPublicKey'));
    }

    public function pay(Request $request, string $reference)
    {
        $order = Order::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'payment_method' => ['required', 'in:pix'],
        ]);

        $order->forceFill(['payment_method' => 'pix'])->save();

        ProcessPixPaymentJob::dispatch($order);

        return view('orders.pending', compact('order'));
    }

    public function success(string $reference)
    {
        $order = Order::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->with(['items.ticketType', 'event'])
            ->firstOrFail();

        return view('orders.success', compact('order'));
    }

    public function myTickets()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'paid')
            ->with(['event', 'items.ticketType'])
            ->orderByDesc('created_at')
            ->get();

        return view('orders.my-tickets', compact('orders'));
    }

    public function status(string $reference)
    {
        $order = Order::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'status'           => $order->status,
            'paid'             => $order->isPaid(),
            'payment_metadata' => $order->payment_metadata,
        ]);
    }
}