@extends('layouts.auth')
@section('title', 'Verificar celular — Bora Ali')

@section('content')
<div class="text-center mb-6">
    <div class="text-4xl mb-3">💬</div>
    <h1 class="text-xl font-semibold text-gray-800">Código enviado!</h1>
    <p class="text-sm text-gray-500 mt-2">
        Digite o código de 6 dígitos que enviamos para
        <strong>{{ Auth::user()->phone }}</strong> via WhatsApp.
    </p>
</div>

<form method="POST" action="{{ route('onboarding.step3.confirm') }}" class="space-y-4">
    @csrf

    <div>
        <input type="text" name="code" maxlength="6" inputmode="numeric"
               placeholder="000000" autofocus
               class="w-full px-4 py-4 rounded-xl border border-gray-200 focus:outline-none
                      focus:ring-2 focus:ring-indigo-500 text-center text-2xl font-bold
                      tracking-widest @error('code') border-red-400 @enderror">
        @error('code')
            <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                   rounded-xl transition text-sm">
        Confirmar código
    </button>
</form>

<div class="text-center mt-4">
    <a href="{{ route('onboarding.step3') }}"
       class="text-sm text-indigo-600 hover:underline">
        Usar outro número
    </a>
</div>
@endsection 