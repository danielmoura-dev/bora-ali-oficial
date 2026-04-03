@extends('layouts.auth')
@section('title', 'Esqueci minha senha — Bora Ali')

@section('content')
    <h1 class="text-xl font-semibold text-gray-800 mb-2">Esqueci minha senha</h1>
    <p class="text-sm text-gray-500 mb-6">Informe seu e-mail e enviaremos um link para redefinir sua senha.</p>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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

        <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                   rounded-xl transition text-sm">
            Enviar link de redefinição
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Lembrou a senha?
        <a href="{{ route('auth.login') }}" class="text-indigo-600 font-medium hover:underline">Voltar ao login</a>
    </p>
@endsection
