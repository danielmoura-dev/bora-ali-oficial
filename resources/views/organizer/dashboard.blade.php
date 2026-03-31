@extends('layouts.app')
@section('title', 'Painel do Organizador — Bora Ali')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Painel do Organizador</h1>
            <p class="text-sm text-gray-500 mt-0.5">Olá, {{ Auth::user()->name }}</p>
        </div>
        <a href="{{ route('events.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                  text-sm font-medium rounded-xl transition">
            + Criar evento
        </a>
    </div>

    {{-- Cards de métricas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Receita total</p>
            <p class="text-2xl font-bold text-gray-800">
                R$ {{ number_format($totalRevenue / 100, 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">pedidos pagos</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Ingressos vendidos</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalTicketsSold }}</p>
            <p class="text-xs text-gray-400 mt-1">total de itens</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Aguardando pag.</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingOrdersCount }}</p>
            <p class="text-xs text-gray-400 mt-1">pedidos pendentes</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Taxa plataforma</p>
            <p class="text-2xl font-bold text-gray-500">
                R$ {{ number_format($totalPlatformFees / 100, 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">R$ 1,00 por ingresso</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Meus Eventos --}}
        <div class="lg:col-span-2">
            <h2 class="font-semibold text-gray-700 mb-3">Meus eventos</h2>

            @if($events->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
                    <p class="text-gray-400 text-sm">Nenhum evento criado ainda.</p>
                    <a href="{{ route('events.create') }}"
                       class="mt-3 inline-block text-indigo-600 text-sm hover:underline">
                        Criar primeiro evento
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($events as $event)
                        <div class="bg-white rounded-2xl border border-gray-100 p-4
                                    hover:shadow-sm transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span @class([
                                            'text-xs font-medium px-2 py-0.5 rounded-full',
                                            'bg-yellow-100 text-yellow-700' => $event->status === 'draft',
                                            'bg-green-100 text-green-700'  => $event->status === 'published',
                                            'bg-gray-100 text-gray-500'    => $event->status === 'finished',
                                        ])>
                                            {{ match($event->status) {
                                                'draft'     => 'Rascunho',
                                                'published' => 'Publicado',
                                                'finished'  => 'Encerrado',
                                                default     => $event->status,
                                            } }}
                                        </span>
                                    </div>
                                    <h3 class="font-semibold text-gray-800 text-sm truncate">
                                        {{ $event->title }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $event->starts_at->translatedFormat('d \d\e M \d\e Y') }}
                                        · {{ $event->city }}/{{ $event->state }}
                                    </p>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-gray-800">
                                        R$ {{ number_format(($event->revenue ?? 0) / 100, 2, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $event->paid_orders_count }} pago(s)
                                        @if($event->pending_orders_count > 0)
                                            · <span class="text-yellow-600">
                                                {{ $event->pending_orders_count }} pendente(s)
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-50">
                                <a href="{{ route('events.show', $event->slug) }}"
                                   class="text-xs text-gray-500 hover:text-indigo-600 transition">
                                    Ver evento →
                                </a>
                                <span class="text-gray-200">|</span>
                                <a href="{{ route('organizer.event.sales', $event->slug) }}"
                                   class="text-xs text-gray-500 hover:text-indigo-600 transition">
                                    Ver vendas →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Últimas vendas --}}
        <div>
            <h2 class="font-semibold text-gray-700 mb-3">Últimas vendas</h2>

            @if($recentOrders->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center">
                    <p class="text-gray-400 text-sm">Nenhuma venda ainda.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($recentOrders as $order)
                        <div class="bg-white rounded-2xl border border-gray-100 p-3">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-gray-700 truncate flex-1">
                                    {{ $order->user->name }}
                                </p>
                                <span class="text-xs font-bold text-green-600 shrink-0 ml-2">
                                    {{ $order->formattedTotal() }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 truncate">
                                {{ $order->event->title }}
                            </p>
                            <p class="text-xs text-gray-300 mt-0.5">
                                {{ $order->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection