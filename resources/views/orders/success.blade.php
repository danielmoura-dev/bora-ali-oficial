@extends('layouts.app')
@section('title', 'Pedido confirmado — Bora Ali')

@section('content')
<div class="max-w-xl mx-auto text-center">
    <div class="text-6xl mb-4">🎉</div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Pedido confirmado!</h1>
    <p class="text-gray-500 mb-2">
        Seus ingressos para <strong>{{ $order->event->title }}</strong> estão garantidos.
    </p>
    <p class="text-sm text-gray-400 mb-8">Pedido: {{ $order->reference }}</p>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 text-left space-y-3 mb-6">
        @foreach($order->items as $item)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="font-medium text-sm text-gray-800">{{ $item->ticketType->name }}</p>
                    <p class="text-xs text-gray-400">{{ $item->quantity }} ingresso(s)</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-mono font-bold text-indigo-600 tracking-widest">
                        {{ $item->ticket_code }}
                    </p>
                    <p class="text-xs text-gray-400">código do ingresso</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('tickets.my') }}"
           class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                  font-medium rounded-xl transition text-sm">
            Ver meus ingressos
        </a>
        <a href="{{ route('home') }}"
           class="px-6 py-3 border border-gray-200 hover:bg-gray-50
                  text-gray-700 font-medium rounded-xl transition text-sm">
            Voltar ao início
        </a>
    </div>
</div>
@endsection