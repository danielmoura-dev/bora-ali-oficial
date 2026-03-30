@extends('layouts.auth')
@section('title', 'Verificar e-mail — Bora Ali')

@section('content')
<div class="text-center mb-6">
    <div class="text-4xl mb-3">📬</div>
    <h1 class="text-xl font-semibold text-gray-800">Verifique seu e-mail</h1>
    <p class="text-sm text-gray-500 mt-2">
        Enviamos um código de 6 dígitos para <strong>{{ Auth::user()->email }}</strong>
    </p>
</div>

<form method="POST" action="{{ route('auth.verify.submit') }}" class="space-y-4">
    @csrf

    <div>
        <input type="text" name="code" maxlength="6" inputmode="numeric"
               placeholder="000000"
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
        Verificar código
    </button>
</form>

<form method="POST" action="{{ route('auth.verify.resend') }}" class="mt-4 text-center">
    @csrf
    <button type="submit" class="text-sm text-indigo-600 hover:underline">
        Não recebi o código — reenviar
    </button>
</form>
@endsection