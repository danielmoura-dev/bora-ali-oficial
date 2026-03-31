@extends('layouts.app')
@section('title', 'Checkout — Bora Ali')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Finalizar pedido</h1>

    @if($errors->has('payment'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            {{ $errors->first('payment') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-5 gap-6">

        {{-- Resumo --}}
        <div class="sm:col-span-3">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h2 class="font-semibold text-gray-700 mb-4">Resumo do pedido</h2>

                <p class="text-sm font-medium text-gray-800 mb-3">
                    🎉 {{ $order->event->title }}
                </p>

                @foreach($order->items as $item)
                    <div class="flex justify-between text-sm py-2 border-t border-gray-50">
                        <span class="text-gray-600">
                            {{ $item->quantity }}× {{ $item->ticketType->name }}
                            <span class="text-gray-400 text-xs">({{ $item->batch->name }})</span>
                        </span>
                        <span class="font-medium">
                            R$ {{ number_format($item->subtotal / 100, 2, ',', '.') }}
                        </span>
                    </div>
                @endforeach

                <div class="border-t border-gray-100 mt-3 pt-3 space-y-1">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>R$ {{ number_format($order->subtotal / 100, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Taxa da plataforma</span>
                        <span>R$ {{ number_format($order->platform_fee / 100, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 text-base pt-2
                                border-t border-gray-100">
                        <span>Total</span>
                        <span>{{ $order->formattedTotal() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pagamento --}}
        <div class="sm:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h2 class="font-semibold text-gray-700 mb-4">Pagamento</h2>

                <form method="POST" action="{{ route('orders.pay', $order->reference) }}"
                      class="space-y-4">
                    @csrf
                    <input type="hidden" name="payment_method" value="pix">

                    {{-- Pix info --}}
                    <div class="p-4 bg-green-50 border border-green-100 rounded-xl">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">⚡</span>
                            <span class="font-semibold text-green-800 text-sm">Pagar com Pix</span>
                        </div>
                        <p class="text-xs text-green-700">
                            QR Code gerado na próxima tela. Pagamento confirmado em segundos.
                        </p>
                    </div>

                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $order->formattedTotal() }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            inclui R$ {{ number_format($order->platform_fee / 100, 2, ',', '.') }}
                            de taxa da plataforma
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                                   font-semibold rounded-xl transition text-sm">
                        Gerar QR Code Pix
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection