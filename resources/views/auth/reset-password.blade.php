@extends('layouts.auth')
@section('title', 'Redefinir senha — Bora Ali')

@section('content')
    <h1 class="text-xl font-semibold text-gray-800 mb-2">Redefinir senha</h1>
    <p class="text-sm text-gray-500 mb-6">Escolha uma nova senha para sua conta.</p>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                       focus:ring-2 focus:ring-indigo-500 text-sm @error('email') border-red-400 @enderror">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
            <input type="password" name="password" required
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                       focus:ring-2 focus:ring-indigo-500 text-sm @error('password') border-red-400 @enderror">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha</label>
            <input type="password" name="password_confirmation" required
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                       focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>

        <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                   rounded-xl transition text-sm">
            Redefinir senha
        </button>
    </form>
@endsection
