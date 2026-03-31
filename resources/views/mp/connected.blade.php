@extends('layouts.app')
@section('title', 'Mercado Pago conectado — Bora Ali')

@section('content')
<div class="max-w-md mx-auto text-center py-12">
    <div class="text-6xl mb-4">✅</div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Mercado Pago conectado!</h1>
    <p class="text-gray-500 mb-8">
        Agora você pode receber pagamentos diretamente na sua conta.
        A plataforma retém R$ 1,00 por ingresso vendido automaticamente.
    </p>

    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 text-left mb-8">
        <h2 class="font-semibold text-indigo-800 text-sm mb-3">Como funciona o repasse:</h2>
        <div class="space-y-2 text-sm text-indigo-700">
            <div class="flex items-start gap-2">
                <span>💰</span>
                <span>O comprador paga o valor total do ingresso</span>
            </div>
            <div class="flex items-start gap-2">
                <span>🏦</span>
                <span>O Mercado Pago retém a taxa deles automaticamente</span>
            </div>
            <div class="flex items-start gap-2">
                <span>⚡</span>
                <span>A Bora Ali retém R$ 1,00 por ingresso</span>
            </div>
            <div class="flex items-start gap-2">
                <span>✅</span>
                <span>O restante vai direto para a sua conta</span>
            </div>
        </div>
    </div>

    <a href="{{ route('events.create') }}"
       class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700
              text-white font-medium rounded-xl transition text-sm">
        Criar meu primeiro evento
    </a>
</div>
@endsection