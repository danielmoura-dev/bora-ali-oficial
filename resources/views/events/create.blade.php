@extends('layouts.app')
@section('title', 'Criar Evento — Bora Ali')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Cabeçalho --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Criar novo evento</h1>
        </div>
        <p class="text-sm text-gray-500 ml-13 pl-[52px]">
            Preencha os dados do seu evento. Você poderá revisá-lo antes de publicar.
        </p>
    </div>

    <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- ── 1. Informações básicas ──────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="font-semibold text-gray-800 text-sm">Informações básicas</h2>
            </div>

            <div class="p-6 space-y-5">

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nome do evento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="Ex: Festival de Verão 2025"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                  transition @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Imagem de capa --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Imagem de divulgação <span class="text-red-500">*</span>
                    </label>
                    <div class="relative border-2 border-dashed border-gray-200 rounded-xl
                                hover:border-orange-400 transition cursor-pointer group"
                         onclick="document.getElementById('cover_image').click()">

                        {{-- Preview --}}
                        <div id="preview-wrapper" class="hidden">
                            <img id="cover-preview" class="w-full max-h-56 object-cover rounded-xl">
                            <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100
                                        transition flex items-center justify-center">
                                <span class="text-white text-sm font-medium">Trocar imagem</span>
                            </div>
                        </div>

                        {{-- Placeholder --}}
                        <div id="upload-placeholder" class="py-10 flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 group-hover:bg-orange-50
                                        flex items-center justify-center transition">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-400 transition"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500">Clique para escolher uma imagem</p>
                            <p class="text-xs text-gray-400">JPG, PNG ou WebP — máx. 3MB</p>
                        </div>
                    </div>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*" class="hidden">
                    @error('cover_image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categoria --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Categoria <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <select name="category_id" id="category_id"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                       appearance-none transition @error('category_id') border-red-400 @enderror">
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                        data-slug="{{ $category->slug }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ── 2. Data e horário ───────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h2 class="font-semibold text-gray-800 text-sm">Data e horário</h2>
            </div>

            <div class="p-6 space-y-5">

                {{-- Início --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Início <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="date" id="starts_date"
                                   value="{{ old('starts_at') ? substr(old('starts_at'), 0, 10) : '' }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                          transition @error('starts_at') border-red-400 @enderror">
                        </div>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <input type="time" id="starts_time"
                                   value="{{ old('starts_at') ? substr(old('starts_at'), 11, 5) : '' }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                          transition @error('starts_at') border-red-400 @enderror">
                        </div>
                    </div>
                    <input type="hidden" name="starts_at" id="starts_at" value="{{ old('starts_at') }}">
                    @error('starts_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Término --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Término <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="date" id="ends_date"
                                   value="{{ old('ends_at') ? substr(old('ends_at'), 0, 10) : '' }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                          transition @error('ends_at') border-red-400 @enderror">
                        </div>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <input type="time" id="ends_time"
                                   value="{{ old('ends_at') ? substr(old('ends_at'), 11, 5) : '' }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                          transition @error('ends_at') border-red-400 @enderror">
                        </div>
                    </div>
                    <input type="hidden" name="ends_at" id="ends_at" value="{{ old('ends_at') }}">
                    @error('ends_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Duração calculada --}}
                <div id="duration-display"
                     class="hidden flex items-center gap-2 text-sm text-orange-600 font-medium
                            py-2.5 px-4 bg-orange-50 rounded-xl border border-orange-100">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="duration-text"></span>
                </div>

            </div>
        </div>

        {{-- ── 3. Campos dinâmicos da categoria ───────────────── --}}
        <div id="category-fields-container" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h2 class="font-semibold text-gray-800 text-sm">Detalhes da categoria</h2>
            </div>
            <div class="p-6">
                <div id="category-fields-inner" class="space-y-5"></div>
            </div>
        </div>

        {{-- ── 4. Descrição ────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h10"/>
                </svg>
                <h2 class="font-semibold text-gray-800 text-sm">Descrição do evento</h2>
            </div>
            <div class="p-6">
                <textarea name="description" rows="5"
                          placeholder="Conte todos os detalhes: programação, atrações, regras e informações importantes..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm resize-none
                                 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                 transition @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── 5. Local ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h2 class="font-semibold text-gray-800 text-sm">Local do evento</h2>
            </div>
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nome do local <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="venue_name" value="{{ old('venue_name') }}"
                           placeholder="Ex: Arena Castelão"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                  transition @error('venue_name') border-red-400 @enderror">
                    @error('venue_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Endereço completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="venue_address" value="{{ old('venue_address') }}"
                           placeholder="Ex: Av. Alberto Craveiro, 2901"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                  transition @error('venue_address') border-red-400 @enderror">
                    @error('venue_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Cidade <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Fortaleza"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                      transition @error('city') border-red-400 @enderror">
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="state"
                                    class="w-full px-3 py-3 rounded-xl border border-gray-200 text-sm
                                           focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                                           appearance-none transition @error('state') border-red-400 @enderror">
                                <option value="">UF</option>
                                @foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                    <option value="{{ $uf }}" {{ old('state') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        @error('state')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── 6. Ingressos ─────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <h2 class="font-semibold text-gray-800 text-sm">Ingressos</h2>
            </div>
            <div class="p-6 space-y-5">

                {{-- Toggle Pago / Gratuito --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de ingresso
                    </label>
                    <div class="flex gap-3">
                        {{-- Pago --}}
                        <button type="button" id="btn-paid" onclick="setIsFree(false)"
                                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl
                                       border-2 text-sm font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pago
                        </button>
                        {{-- Gratuito --}}
                        <button type="button" id="btn-free" onclick="setIsFree(true)"
                                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl
                                       border-2 text-sm font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            Gratuito
                        </button>
                    </div>
                    <input type="hidden" name="is_free" id="is_free" value="{{ old('is_free', '0') }}">
                </div>

                {{-- Opções visíveis só quando pago --}}
                <div id="paid-options" class="space-y-5">

                    {{-- Taxa de serviço --}}
                    <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl
                                  cursor-pointer hover:border-orange-300 hover:bg-orange-50
                                  has-[:checked]:border-orange-400 has-[:checked]:bg-orange-50 transition">
                        <input type="checkbox" name="absorb_service_fee" id="absorb_service_fee"
                               value="1" {{ old('absorb_service_fee') ? 'checked' : '' }}
                               class="mt-0.5 w-4 h-4 text-orange-500 rounded border-gray-300 shrink-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Absorver taxa de serviço</p>
                            <p id="fee-description" class="text-xs text-gray-500 mt-0.5">
                                A taxa de R$ 1,00 será exibida separadamente ao comprador no momento da compra.
                            </p>
                        </div>
                    </label>

                    {{-- Nomenclatura --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Como chamar o ingresso?
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 p-3.5 border-2 rounded-xl cursor-pointer
                                          hover:border-orange-300 transition
                                          has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="ticket_nomenclature" value="ingresso"
                                       {{ old('ticket_nomenclature', 'ingresso') === 'ingresso' ? 'checked' : '' }}
                                       class="text-orange-500">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                          d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">Ingresso</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3.5 border-2 rounded-xl cursor-pointer
                                          hover:border-orange-300 transition
                                          has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="ticket_nomenclature" value="inscricao"
                                       {{ old('ticket_nomenclature') === 'inscricao' ? 'checked' : '' }}
                                       class="text-orange-500">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">Inscrição</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Afeta botões, e-mails e textos do evento.</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── 7. Configuração de pagamento (some se gratuito) ─── --}}
        <div id="payment-section" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <h2 class="font-semibold text-gray-800 text-sm">Configuração de pagamento</h2>
            </div>
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Como os pagamentos serão recebidos?
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-4 border-2 border-gray-100 rounded-xl
                                      cursor-pointer hover:border-orange-300 transition
                                      has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                            <input type="radio" name="payment_mode" value="direct"
                                   class="mt-0.5 text-orange-500 shrink-0" checked>
                            <div>
                                <p class="font-medium text-sm text-gray-800">Direto na plataforma</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Não precisa de conta no Mercado Pago. A Bora Ali recebe e repassa para você.
                                </p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 border-2 border-gray-100 rounded-xl
                                      cursor-pointer hover:border-orange-300 transition
                                      has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                            <input type="radio" name="payment_mode" value="split"
                                   class="mt-0.5 text-orange-500 shrink-0">
                            <div>
                                <p class="font-medium text-sm text-gray-800">Split automático</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Você recebe direto na sua conta do Mercado Pago após cada venda. Requer conta conectada.
                                </p>
                            </div>
                        </label>
                    </div>
                    @error('payment_mode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" name="payment_provider" value="mercadopago">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Métodos aceitos</label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="payment_methods[]" value="pix" checked
                               class="text-orange-500 rounded">
                        <span class="text-sm text-gray-700">Pix</span>
                        <span class="text-xs text-gray-400">(único disponível no momento)</span>
                    </label>
                </div>

                <div id="split-warning"
                     class="hidden flex items-start gap-2.5 p-3.5 bg-amber-50 border border-amber-200
                            rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>
                        Para usar o split automático, conecte sua conta do Mercado Pago em
                        <a href="{{ route('profile.show') }}" class="underline font-medium">Meu Perfil</a>.
                    </span>
                </div>

            </div>
        </div>

        {{-- ── Ações ────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between gap-3 pb-6">
            <a href="{{ route('home') }}"
               class="flex items-center gap-1.5 px-5 py-3 text-sm text-gray-500
                      hover:text-gray-700 transition rounded-xl hover:bg-gray-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Cancelar
            </a>
            <button type="submit"
                    class="flex items-center gap-2 px-8 py-3 bg-orange-500 hover:bg-orange-600
                           text-white font-medium rounded-xl transition text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Salvar rascunho
            </button>
        </div>

    </form>
</div>

<script>
    // ── Preview de imagem ──────────────────────────────────────
    document.getElementById('cover_image').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('cover-preview').src = e.target.result;
            document.getElementById('preview-wrapper').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });

    // ── Data/hora: combina date + time nos campos hidden ───────
    function combineDateTime(dateId, timeId, hiddenId) {
        const d = document.getElementById(dateId).value;
        const t = document.getElementById(timeId).value || '00:00';
        document.getElementById(hiddenId).value = d && t ? `${d}T${t}` : '';
    }

    ['starts_date', 'starts_time'].forEach(id =>
        document.getElementById(id).addEventListener('change', () => {
            combineDateTime('starts_date', 'starts_time', 'starts_at');
            calcDuration();
        })
    );
    ['ends_date', 'ends_time'].forEach(id =>
        document.getElementById(id).addEventListener('change', () => {
            combineDateTime('ends_date', 'ends_time', 'ends_at');
            calcDuration();
        })
    );

    // Inicializa combinados se vier do old()
    combineDateTime('starts_date', 'starts_time', 'starts_at');
    combineDateTime('ends_date', 'ends_time', 'ends_at');

    // ── Duração calculada ──────────────────────────────────────
    function calcDuration() {
        const startVal = document.getElementById('starts_at').value;
        const endVal   = document.getElementById('ends_at').value;
        const display  = document.getElementById('duration-display');
        const text     = document.getElementById('duration-text');

        if (!startVal || !endVal) { display.classList.add('hidden'); return; }

        const diffMs = new Date(endVal) - new Date(startVal);
        if (diffMs <= 0) { display.classList.add('hidden'); return; }

        const days  = Math.floor(diffMs / 86400000);
        const hours = Math.floor((diffMs % 86400000) / 3600000);
        const mins  = Math.floor((diffMs % 3600000) / 60000);

        let parts = [];
        if (days)  parts.push(`${days} dia${days > 1 ? 's' : ''}`);
        if (hours) parts.push(`${hours} hora${hours > 1 ? 's' : ''}`);
        if (mins && !days) parts.push(`${mins} min`);

        text.textContent = 'Duração: ' + parts.join(' e ');
        display.classList.remove('hidden');
    }

    // ── Toggle pago / gratuito ─────────────────────────────────
    function setIsFree(free) {
        document.getElementById('is_free').value = free ? '1' : '0';

        const btnPaid    = document.getElementById('btn-paid');
        const btnFree    = document.getElementById('btn-free');
        const paidOpts   = document.getElementById('paid-options');
        const paySection = document.getElementById('payment-section');

        if (free) {
            // Gratuito ativo
            btnFree.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
            btnFree.classList.remove('border-gray-200', 'text-gray-500');
            btnPaid.classList.remove('border-orange-500', 'bg-orange-50', 'text-orange-600');
            btnPaid.classList.add('border-gray-200', 'text-gray-500');
            paidOpts.classList.add('hidden');
            paySection.classList.add('hidden');
        } else {
            // Pago ativo
            btnPaid.classList.add('border-orange-500', 'bg-orange-50', 'text-orange-600');
            btnPaid.classList.remove('border-gray-200', 'text-gray-500');
            btnFree.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
            btnFree.classList.add('border-gray-200', 'text-gray-500');
            paidOpts.classList.remove('hidden');
            paySection.classList.remove('hidden');
        }
    }

    // Inicializa com pago por padrão
    setIsFree(document.getElementById('is_free').value === '1');

    // ── Texto dinâmico da taxa ─────────────────────────────────
    document.getElementById('absorb_service_fee').addEventListener('change', function () {
        document.getElementById('fee-description').textContent = this.checked
            ? 'A taxa de R$ 1,00 por ingresso será incluída no preço final. O comprador não verá a taxa separada.'
            : 'A taxa de R$ 1,00 será exibida separadamente ao comprador no momento da compra.';
    });

    // ── Aviso split ────────────────────────────────────────────
    document.querySelectorAll('input[name="payment_mode"]').forEach(radio =>
        radio.addEventListener('change', function () {
            document.getElementById('split-warning').classList.toggle('hidden', this.value !== 'split');
        })
    );

    // ── Campos dinâmicos da categoria ─────────────────────────
    const categorySelect = document.getElementById('category_id');

    async function loadCategoryFields(slug) {
        const container = document.getElementById('category-fields-container');
        const inner     = document.getElementById('category-fields-inner');

        if (!slug) { container.classList.add('hidden'); inner.innerHTML = ''; return; }

        try {
            const res  = await fetch(`/api/categories/${slug}/fields`);
            const data = await res.json();

            if (!data.fields || data.fields.length === 0) {
                container.classList.add('hidden'); inner.innerHTML = ''; return;
            }

            inner.innerHTML = data.fields.map(renderField).join('');
            container.classList.remove('hidden');
        } catch (e) {
            container.classList.add('hidden'); inner.innerHTML = '';
        }
    }

    function renderField(field) {
        const required   = field.required ? 'required' : '';
        const reqMark    = field.required ? '<span class="text-red-500">*</span>' : '';
        const ph         = field.placeholder ? `placeholder="${field.placeholder}"` : '';
        const inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-sm transition';

        let input = '';
        if (field.type === 'textarea') {
            input = `<textarea name="fields[${field.name}]" rows="3" ${ph} ${required} class="${inputClass} resize-none"></textarea>`;
        } else if (field.type === 'select' && field.options) {
            const opts = field.options.map(o => `<option value="${o}">${o}</option>`).join('');
            input = `<select name="fields[${field.name}]" ${required} class="${inputClass}"><option value="">Selecione...</option>${opts}</select>`;
        } else if (field.type === 'checkbox') {
            return `<div><label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="fields[${field.name}]" value="1" ${required} class="w-4 h-4 text-orange-500 rounded border-gray-300">
                <span class="text-sm text-gray-700">${field.label}</span>
            </label></div>`;
        } else {
            const type = field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : 'text');
            input = `<input type="${type}" name="fields[${field.name}]" ${ph} ${required} class="${inputClass}">`;
        }

        return `<div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">${field.label} ${reqMark}</label>
            ${input}
        </div>`;
    }

    categorySelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        loadCategoryFields(selected?.dataset.slug || null);
    });

    (function () {
        const selected = categorySelect.options[categorySelect.selectedIndex];
        if (selected?.dataset.slug) loadCategoryFields(selected.dataset.slug);
    })();
</script>
@endsection
