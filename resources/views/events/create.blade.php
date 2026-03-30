@extends('layouts.app')
@section('title', 'Criar Evento — Bora Ali')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Criar novo evento</h1>
        <p class="text-sm text-gray-500 mt-1">
            Preencha os dados do seu evento. Você poderá revisá-lo antes de publicar.
        </p>
    </div>

    <form method="POST" action="{{ route('events.store') }}"
          enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Informações básicas --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                Informações básicas
            </h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Título do evento <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                       placeholder="Ex: Festival de Verão 2025"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                              @error('title') border-red-400 @enderror">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Descrição <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="5"
                          placeholder="Descreva seu evento com detalhes: atrações, programação, informações importantes..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200
                                 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm resize-none
                                 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Imagem de capa
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center
                            hover:border-indigo-400 transition cursor-pointer"
                     onclick="document.getElementById('cover_image').click()">
                    <div id="preview-wrapper" class="hidden mb-3">
                        <img id="cover-preview" class="max-h-40 mx-auto rounded-lg object-cover">
                    </div>
                    <div id="upload-placeholder">
                        <div class="text-3xl mb-2">🖼️</div>
                        <p class="text-sm text-gray-500">Clique para escolher uma imagem</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WebP — máx. 3MB</p>
                    </div>
                </div>
                <input type="file" name="cover_image" id="cover_image"
                       accept="image/*" class="hidden">
                @error('cover_image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_free" id="is_free" value="1"
                       {{ old('is_free') ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                <label for="is_free" class="text-sm text-gray-700">
                    Este evento é gratuito
                </label>
            </div>
        </div>

        {{-- Local --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                Local do evento
            </h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nome do local <span class="text-red-500">*</span>
                </label>
                <input type="text" name="venue_name" value="{{ old('venue_name') }}"
                       placeholder="Ex: Arena Castelão"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                              @error('venue_name') border-red-400 @enderror">
                @error('venue_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Endereço <span class="text-red-500">*</span>
                </label>
                <input type="text" name="venue_address" value="{{ old('venue_address') }}"
                       placeholder="Ex: Av. Alberto Craveiro, 2901"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                              @error('venue_address') border-red-400 @enderror">
                @error('venue_address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Cidade <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           placeholder="Fortaleza"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                                  @error('city') border-red-400 @enderror">
                    @error('city')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="state"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                                   @error('state') border-red-400 @enderror">
                        <option value="">UF</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" {{ old('state') === $uf ? 'selected' : '' }}>
                                {{ $uf }}
                            </option>
                        @endforeach
                    </select>
                    @error('state')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Data e hora --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                Data e horário
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Início <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="starts_at"
                           value="{{ old('starts_at') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                                  @error('starts_at') border-red-400 @enderror">
                    @error('starts_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Término <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="ends_at"
                           value="{{ old('ends_at') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                                  @error('ends_at') border-red-400 @enderror">
                    @error('ends_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('home') }}"
               class="px-6 py-3 text-sm text-gray-600 hover:text-gray-800 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                           font-medium rounded-xl transition text-sm">
                Salvar rascunho
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('cover_image').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('cover-preview').src = e.target.result;
        document.getElementById('preview-wrapper').classList.remove('hidden');
        document.getElementById('upload-placeholder').classList.add('hidden');
    };
    reader.readAsDataURL(file);
});
</script>
@endsection