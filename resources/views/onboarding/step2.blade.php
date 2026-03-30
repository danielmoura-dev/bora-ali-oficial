@extends('layouts.auth')
@section('title', 'Seu perfil — Bora Ali')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 mb-1">
        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Passo 2 de 3</span>
    </div>
    <h1 class="text-xl font-semibold text-gray-800">Qual é o seu perfil?</h1>
    <p class="text-sm text-gray-500 mt-1">Precisamos dessas informações para emissão de notas fiscais.</p>
</div>

<form method="POST" action="{{ route('onboarding.step2.store') }}" class="space-y-5" id="profile-form">
    @csrf

    {{-- Seletor de tipo --}}
    <div class="grid grid-cols-2 gap-3">
        <label class="cursor-pointer">
            <input type="radio" name="profile_type" value="cpf" class="peer sr-only"
                   {{ old('profile_type') === 'cpf' ? 'checked' : '' }}>
            <div class="border-2 border-gray-200 rounded-xl p-4 text-center transition
                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50">
                <div class="text-2xl mb-1">👤</div>
                <div class="font-medium text-sm text-gray-700">Pessoa Física</div>
                <div class="text-xs text-gray-400">CPF</div>
            </div>
        </label>

        <label class="cursor-pointer">
            <input type="radio" name="profile_type" value="cnpj" class="peer sr-only"
                   {{ old('profile_type') === 'cnpj' ? 'checked' : '' }}>
            <div class="border-2 border-gray-200 rounded-xl p-4 text-center transition
                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50">
                <div class="text-2xl mb-1">🏢</div>
                <div class="font-medium text-sm text-gray-700">Pessoa Jurídica</div>
                <div class="text-xs text-gray-400">CNPJ</div>
            </div>
        </label>
    </div>
    @error('profile_type')
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror

    {{-- Campos CPF --}}
    <div id="cpf-fields" class="space-y-4 hidden">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
            <input type="text" name="document_number" id="cpf-input"
                   placeholder="000.000.000-00" maxlength="14"
                   value="{{ old('document_number') }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                          focus:ring-2 focus:ring-indigo-500 text-sm @error('document_number') border-red-400 @enderror">
            @error('document_number')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data de nascimento</label>
            <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                          focus:ring-2 focus:ring-indigo-500 text-sm @error('birth_date') border-red-400 @enderror">
            @error('birth_date')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Campos CNPJ --}}
    <div id="cnpj-fields" class="space-y-4 hidden">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
            <input type="text" name="document_number" id="cnpj-input"
                   placeholder="00.000.000/0000-00" maxlength="18"
                   value="{{ old('document_number') }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                          focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
            <input type="text" name="company_name" value="{{ old('company_name') }}"
                   placeholder="Nome da empresa conforme CNPJ"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                          focus:ring-2 focus:ring-indigo-500 text-sm @error('company_name') border-red-400 @enderror">
            @error('company_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                   rounded-xl transition text-sm">
        Continuar
    </button>
</form>

<script>
const radios = document.querySelectorAll('input[name="profile_type"]');
const cpfFields  = document.getElementById('cpf-fields');
const cnpjFields = document.getElementById('cnpj-fields');

function toggleFields() {
    const val = document.querySelector('input[name="profile_type"]:checked')?.value;
    cpfFields.classList.toggle('hidden', val !== 'cpf');
    cnpjFields.classList.toggle('hidden', val !== 'cnpj');
}

radios.forEach(r => r.addEventListener('change', toggleFields));
toggleFields(); // mostra campos do old() se houver

// Máscara CPF
document.getElementById('cpf-input')?.addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    this.value = v;
});

// Máscara CNPJ
document.getElementById('cnpj-input')?.addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 14);
    v = v.replace(/(\d{2})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1/$2');
    v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    this.value = v;
});
</script>
@endsection