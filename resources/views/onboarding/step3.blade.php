@extends('layouts.auth')
@section('title', 'Seu celular — Bora Ali')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 mb-1">
        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
            Passo 3 de 3
        </span>
    </div>
    <h1 class="text-xl font-semibold text-gray-800">Qual é o seu celular?</h1>
    <p class="text-sm text-gray-500 mt-1">
        Informe seu celular para contato.
    </p>
</div>

<form method="POST" action="{{ route('onboarding.step3.send') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Celular (com DDD)
        </label>
        <div class="flex items-center gap-2">
            <span class="px-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500">
                🇧🇷 +55
            </span>
            <input type="tel" name="phone" id="phone-input"
                   placeholder="(85) 99999-0000"
                   value="{{ old('phone') }}"
                   maxlength="15"
                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                          focus:ring-2 focus:ring-indigo-500 text-sm
                          @error('phone') border-red-400 @enderror">
        </div>
        @error('phone')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-start gap-3 p-3 bg-green-50 rounded-xl border border-green-100">
        <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <p class="text-xs text-green-700">
            Você receberá uma mensagem no WhatsApp com o código de verificação.
        </p>
    </div>

    <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                   rounded-xl transition text-sm">
        Enviar código
    </button>
</form>

<script>
document.getElementById('phone-input').addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length >= 7) {
        v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    } else if (v.length >= 3) {
        v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }
    this.value = v;
});
</script>
@endsection