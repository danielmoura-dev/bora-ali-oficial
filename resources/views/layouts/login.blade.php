@extends('layouts.auth')
@section('title', 'Entrar — Bora Ali')

@section('content')
<h1 class="text-xl font-semibold text-gray-800 mb-6">Entrar</h1>

<form method="POST" action="{{ route('auth.login.store') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
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
                      focus:ring-2 focus:ring-indigo-500 text-sm">
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600">
            Lembrar de mim
        </label>
    </div>

    <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                   rounded-xl transition text-sm">
        Entrar
    </button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Não tem conta?
    <a href="{{ route('auth.register') }}" class="text-indigo-600 font-medium hover:underline">Criar conta</a>
</p>
@endsection