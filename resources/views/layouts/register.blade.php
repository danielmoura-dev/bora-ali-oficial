@extends('layouts.auth')
@section('title', 'Criar conta — Bora Ali')

@section('content')
<h1 class="text-xl font-semibold text-gray-800 mb-6">Criar conta</h1>

<form method="POST" action="{{ route('auth.register.store') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                      focus:ring-2 focus:ring-indigo-500 text-sm @error('name') border-red-400 @enderror">
        @error('name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                      focus:ring-2 focus:ring-indigo-500 text-sm @error('email') border-red-400 @enderror">
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
        <input type="password" name="password" required
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                      focus:ring-2 focus:ring-indigo-500 text-sm @error('password') border-red-400 @enderror">
        <p class="text-gray-400 text-xs mt-1">Mínimo 8 caracteres, letras maiúsculas e números.</p>
        @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar senha</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                      focus:ring-2 focus:ring-indigo-500 text-sm">
    </div>

    <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                   rounded-xl transition text-sm mt-2">
        Criar conta
    </button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Já tem conta?
    <a href="{{ route('auth.login') }}" class="text-indigo-600 font-medium hover:underline">Entrar</a>
</p>
@endsection