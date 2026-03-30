<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketBatch;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TicketTypeController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        Gate::authorize('update', $event);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:255'],
            'is_half_price' => ['sometimes', 'boolean'],
        ]);

        $event->ticketTypes()->create([
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'is_half_price' => $request->boolean('is_half_price'),
            'sort_order'    => $event->ticketTypes()->count(),
        ]);

        return redirect()->route('events.show', $event->slug)
            ->with('status', 'Tipo de ingresso adicionado.');
    }

    public function storeBatch(Request $request, string $slug, TicketType $type)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        Gate::authorize('update', $event);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'quantity'  => ['required', 'integer', 'min:1'],
            'price'     => ['required', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at'   => ['nullable', 'date', 'after:starts_at'],
        ]);

        // Converte "50,00" ou "50.00" para centavos
        $priceCents = (int) round(
            (float) str_replace(',', '.', preg_replace('/[^\d,.]/', '', $validated['price'])) * 100
        );

        $type->batches()->create([
            'name'      => $validated['name'],
            'quantity'  => $validated['quantity'],
            'price'     => $priceCents,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at'   => $validated['ends_at'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('events.show', $event->slug)
            ->with('status', 'Lote adicionado.');
    }
}