@extends('layouts.app')
@section('title', 'Meus Ingressos — Bora Ali')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Meus Ingressos e Inscrições</h1>

    @if($orders->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <div class="text-5xl mb-3">🎫</div>
            <p class="font-medium">Você ainda não comprou ingressos</p>
            <a href="{{ route('home') }}"
               class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                Ver eventos disponíveis
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">
                                {{ $order->event->title }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $order->event->starts_at->translatedFormat('d \d\e M \d\e Y · H:i') }}
                                · {{ $order->reference }}
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="text-xs font-medium px-2 py-1 bg-green-100 text-green-700 rounded-full">
                                Pago
                            </span>

                            {{-- Cancelar pedido --}}
                            @if($order->isPaid() && !$order->event->isFinished())
                                <form method="POST"
                                      action="{{ route('orders.cancel', $order->reference) }}"
                                      onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-500 hover:underline mt-1">
                                        Cancelar pedido
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @foreach($order->items as $item)
                        <div class="px-4 py-3 flex items-center justify-between
                                    border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm text-gray-700">{{ $item->ticketType->name }}</p>
                                <p class="text-xs text-gray-400">
                        {{ $item->quantity }} {{ $order->event->ticketLabel($item->quantity > 1) }}
                    </p>
                            </div>
                            <p class="font-mono font-bold text-indigo-600 text-sm tracking-widest">
                                {{ $item->ticket_code }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection