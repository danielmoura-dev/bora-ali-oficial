@extends('layouts.app')
@section('title', 'Vendas — ' . $event->title)

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('organizer.dashboard') }}" class="hover:text-indigo-600">
            Painel
        </a>
        <span>/</span>
        <span class="text-gray-800 truncate">{{ $event->title }}</span>
    </div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-800">Vendas do evento</h1>
        <a href="{{ route('events.show', $event->slug) }}"
           class="text-sm text-indigo-600 hover:underline">
            Ver página do evento →
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Receita</p>
            <p class="text-xl font-bold text-gray-800">
                R$ {{ number_format($stats['total_revenue'] / 100, 2, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Pedidos pagos</p>
            <p class="text-xl font-bold text-green-600">{{ $stats['total_sold'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Pedidos pendentes</p>
            <p class="text-xl font-bold text-yellow-600">{{ $stats['total_pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Taxa plataforma</p>
            <p class="text-xl font-bold text-gray-500">
                R$ {{ number_format($stats['platform_fees'] / 100, 2, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Vendas por tipo --}}
    @if($salesByType->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
            <h2 class="font-semibold text-gray-700 mb-4 text-sm">Vendas por tipo de ingresso</h2>
            <div class="space-y-3">
                @foreach($salesByType as $type)
                    @php
                        $totalQty = $type->batches->sum('quantity');
                        $soldQty  = $type->total_sold ?? 0;
                        $pct      = $totalQty > 0 ? ($soldQty / $totalQty) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 font-medium">{{ $type->name }}</span>
                            <span class="text-gray-500">{{ $soldQty }} / {{ $totalQty }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full transition-all"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Lista de pedidos --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50">
            <h2 class="font-semibold text-gray-700 text-sm">Pedidos</h2>
        </div>

        @if($orders->isEmpty())
            <div class="p-8 text-center text-gray-400 text-sm">
                Nenhum pedido ainda.
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($orders as $order)
                    <div class="px-5 py-4 flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-full',
                                    'bg-green-100 text-green-700'   => $order->status === 'paid',
                                    'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                                    'bg-red-100 text-red-600'       => $order->status === 'cancelled',
                                ])>
                                    {{ match($order->status) {
                                        'paid'      => 'Pago',
                                        'pending'   => 'Pendente',
                                        'cancelled' => 'Cancelado',
                                        default     => $order->status,
                                    } }}
                                </span>
                                <span class="text-xs text-gray-400 font-mono">
                                    {{ $order->reference }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-800">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $order->created_at->translatedFormat('d \d\e M \d\e Y · H:i') }}
                            </p>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach($order->items as $item)
                                    <span class="text-xs bg-gray-100 text-gray-600
                                                 px-2 py-0.5 rounded-full">
                                        {{ $item->quantity }}× {{ $item->ticketType->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold text-gray-800 text-sm">
                                {{ $order->formattedTotal() }}
                            </p>
                            <p class="text-xs text-gray-400">
                                taxa: R$ {{ number_format($order->platform_fee / 100, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginação --}}
            @if($orders->hasPages())
                <div class="px-5 py-4 border-t border-gray-50">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection