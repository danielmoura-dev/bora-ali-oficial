@extends('layouts.app')
@section('title', 'Checkout — Bora Ali')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Finalizar pedido</h1>

    <div class="grid grid-cols-1 sm:grid-cols-5 gap-6">

        {{-- Resumo --}}
        <div class="sm:col-span-3 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h2 class="font-semibold text-gray-700 mb-4">Resumo do pedido</h2>

                <div class="text-sm font-medium text-gray-800 mb-3">
                    🎉 {{ $order->event->title }}
                </div>

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
                <h2 class="font-semibold text-gray-700 mb-4">Forma de pagamento</h2>

                <form method="POST" action="{{ route('orders.pay', $order->reference) }}"
                      class="space-y-3">
                    @csrf

                    @foreach(['credit_card' => '💳 Cartão de crédito', 'pix' => '⚡ Pix', 'boleto' => '🧾 Boleto'] as $value => $label)
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-100
                                      rounded-xl cursor-pointer hover:border-indigo-300
                                      has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50
                                      transition">
                            <input type="radio" name="payment_method" value="{{ $value }}"
                                   class="text-indigo-600"
                                   {{ $value === 'credit_card' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach

                    @error('payment_method')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror

                    <div class="pt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <p class="text-xs text-yellow-700 text-center">
                            🚧 Ambiente de testes — nenhum pagamento real será processado
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                                   font-semibold rounded-xl transition text-sm mt-2">
                        Confirmar pagamento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection