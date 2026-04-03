@extends('layouts.app')
@section('title', $event->title . ' — Bora Ali')

@section('content')
@php
    $isOrganizer = auth()->check() && auth()->id() === $event->user_id;
    $statusMap = [
        'draft'     => ['label' => 'Rascunho',   'color' => 'bg-gray-100 text-gray-600'],
        'published' => ['label' => 'Publicado',   'color' => 'bg-green-100 text-green-700'],
        'cancelled' => ['label' => 'Cancelado',   'color' => 'bg-red-100 text-red-600'],
        'finished'  => ['label' => 'Encerrado',   'color' => 'bg-gray-100 text-gray-500'],
    ];
    $statusInfo = $statusMap[$event->status] ?? ['label' => ucfirst($event->status), 'color' => 'bg-gray-100 text-gray-600'];
@endphp

<div class="max-w-4xl mx-auto space-y-5">

    {{-- ── Barra do organizador ───────────────────────────────── --}}
    @if($isOrganizer)
        <div class="flex flex-wrap items-center justify-between gap-3 p-4
                    bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusInfo['color'] }}">
                    {{ $statusInfo['label'] }}
                </span>
                <span class="text-sm text-gray-500">Você é o organizador deste evento</span>
            </div>
            <div class="flex items-center gap-2">
                @if($event->isPublished())
                    <a href="{{ route('checkin.index', $event->slug) }}"
                       class="flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-900
                              text-white text-sm font-medium rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Check-in
                    </a>
                @endif
                @if($event->status === 'draft')
                    <form method="POST" action="{{ route('events.publish', $event->slug) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600
                                       text-white text-sm font-medium rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            Publicar evento
                        </button>
                    </form>
                @endif
                @if(in_array($event->status, ['draft', 'published']))
                    <form method="POST" action="{{ route('events.cancel', $event->slug) }}"
                          onsubmit="return confirm('Cancelar o evento e notificar todos os compradores?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="flex items-center gap-1.5 px-4 py-2 border border-red-200
                                       text-red-600 hover:bg-red-50 text-sm font-medium rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar evento
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    {{-- Flash --}}
    @if(session('status'))
        <div class="flex items-center gap-2.5 p-4 bg-green-50 border border-green-200
                    text-green-700 rounded-2xl text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    {{-- ── Capa ────────────────────────────────────────────────── --}}
    <div class="aspect-video rounded-2xl overflow-hidden bg-gradient-to-br from-orange-100 to-amber-100
                shadow-sm">
        @if($event->cover_image)
            <img src="{{ $event->coverUrl() }}" alt="{{ $event->title }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-20 h-20 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="{{ $event->category?->icon ?? 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z' }}"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- ── Conteúdo principal ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Coluna esquerda --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Título e meta --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($event->category)
                        <span class="text-xs font-medium px-2.5 py-1 bg-orange-100 text-orange-600 rounded-full">
                            <svg class="w-3 h-3 inline-block mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $event->category->icon }}"/>
                            </svg>
                            {{ $event->category->name }}
                        </span>
                    @endif
                    @if($event->is_free)
                        <span class="text-xs font-medium px-2.5 py-1 bg-green-100 text-green-700 rounded-full">
                            Gratuito
                        </span>
                    @endif
                    @if($event->isFinished())
                        <span class="text-xs font-medium px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full">
                            Encerrado
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight mb-4">
                    {{ $event->title }}
                </h1>

                <div class="space-y-2.5">
                    {{-- Data --}}
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $event->starts_at->translatedFormat('D, d \d\e M \d\e Y') }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $event->starts_at->format('H:i') }}
                                @if($event->ends_at->isSameDay($event->starts_at))
                                    até {{ $event->ends_at->format('H:i') }}
                                @else
                                    até {{ $event->ends_at->translatedFormat('d \d\e M · H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    {{-- Local --}}
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $event->venue_name }}</p>
                            <p class="text-xs text-gray-500">{{ $event->venue_address }} · {{ $event->city }}/{{ $event->state }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sobre o evento --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10"/>
                    </svg>
                    <h2 class="font-semibold text-gray-800">Sobre o evento</h2>
                </div>
                <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ $event->description }}
                </div>
            </div>

            {{-- Detalhes da categoria --}}
            @if($event->fieldValues->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h2 class="font-semibold text-gray-800">
                            @if($event->category?->icon)
                                <svg class="w-4 h-4 inline-block mr-1 -mt-0.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $event->category->icon }}"/>
                                </svg>
                            @endif
                            Detalhes
                        </h2>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($event->fieldValues as $fv)
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-0.5">
                                    {{ $fv->field->label }}
                                </dt>
                                <dd class="text-sm font-medium text-gray-700">{{ $fv->value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

        </div>

        {{-- ── Sidebar ─────────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Organizador --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-800 text-sm">Organizador</h3>
                </div>
                <div class="flex items-center gap-3">
                    @if($event->organizer->avatar)
                        <img src="{{ str_starts_with($event->organizer->avatar, 'http') ? $event->organizer->avatar : Storage::url($event->organizer->avatar) }}"
                             class="w-10 h-10 rounded-full object-cover shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center
                                    text-orange-600 font-bold text-sm shrink-0">
                            {{ strtoupper(substr($event->organizer->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $event->organizer->name }}</p>
                        @if($event->organizer->username)
                            <a href="{{ route('organizer.public', $event->organizer->username) }}"
                               class="text-xs text-orange-500 hover:underline">Ver perfil →</a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── CTA ingressos ───────────────────────────────── --}}
            @if(!$event->isFinished() && $event->isPublished() && $event->ticketTypes->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <h3 class="font-semibold text-gray-800 text-sm">{{ $event->ticketLabel(true, true) }}</h3>
                    </div>

                    @php $hasAvailable = $event->ticketTypes->contains(fn($t) => $t->activeBatch() !== null); @endphp

                    @if($hasAvailable)
                        {{-- Preview de preços --}}
                        <div class="space-y-2 mb-4">
                            @foreach($event->ticketTypes as $type)
                                @php $batch = $type->activeBatch(); @endphp
                                @if($batch)
                                    <div class="flex items-center justify-between py-2.5 px-3
                                                bg-gray-50 rounded-xl">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $type->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $batch->name }}</p>
                                        </div>
                                        <p class="text-sm font-bold text-orange-500">
                                            {{ $batch->formattedPrice() }}
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <button onclick="document.getElementById('ticket-modal').classList.remove('hidden')"
                                class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white
                                       font-semibold rounded-xl text-sm transition shadow-sm
                                       flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Garantir {{ $event->ticketLabel() }}
                        </button>
                    @else
                        <div class="text-center py-4 text-sm text-gray-400">
                            Ingressos indisponíveis no momento.
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- ── Painel do organizador: gerenciar ingressos ─────────── --}}
    @if($isOrganizer)
        <div class="space-y-4 pt-2">

            <div class="flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Painel do organizador</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Status do pagamento --}}
            @if($event->usesSplit())
                @if(Auth::user()->hasMpConnected())
                    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-green-800">Mercado Pago conectado</p>
                            <p class="text-xs text-green-600">Seus repasses estão configurados.</p>
                        </div>
                        <form method="POST" action="{{ route('mp.disconnect') }}">
                            @csrf
                            <button type="submit" class="text-xs text-red-500 hover:underline">Desconectar</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-amber-800">Conecte seu Mercado Pago</p>
                            <p class="text-xs text-amber-700">Necessário para receber pagamentos direto na sua conta.</p>
                        </div>
                        <a href="{{ route('mp.connect') }}"
                           class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white
                                  text-xs font-medium rounded-lg transition shrink-0">
                            Conectar
                        </a>
                    </div>
                @endif
            @else
                <div class="flex items-center gap-3 p-4 bg-orange-50 border border-orange-100 rounded-2xl">
                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-orange-800">Pagamento direto na plataforma</p>
                        <p class="text-xs text-orange-600">A Bora Ali recebe e repassa para você após o evento.</p>
                    </div>
                </div>
            @endif

            {{-- Gerenciar tipos de ingresso --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <h2 class="font-semibold text-gray-800 text-sm">
                        Gerenciar {{ $event->ticketLabel(true) }}
                    </h2>
                </div>

                <div class="p-6 space-y-4">

                    @forelse($event->ticketTypes as $type)
                        <div class="border border-gray-100 rounded-xl overflow-hidden">
                            {{-- Header do tipo --}}
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-800 text-sm">{{ $type->name }}</span>
                                    @if($type->is_half_price)
                                        <span class="text-xs px-2 py-0.5 bg-orange-100 text-orange-600 rounded-full">Meia</span>
                                    @endif
                                </div>
                                <button onclick="toggleBatchForm('batch-form-{{ $type->id }}')"
                                        class="flex items-center gap-1 text-xs text-orange-500
                                               hover:text-orange-600 font-medium transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Novo lote
                                </button>
                            </div>

                            {{-- Lotes existentes --}}
                            @if($type->batches->isNotEmpty())
                                <div class="divide-y divide-gray-50">
                                    @foreach($type->batches as $batch)
                                        <div class="flex items-center justify-between px-4 py-3 text-sm">
                                            <div>
                                                <span class="font-medium text-gray-700">{{ $batch->name }}</span>
                                                <span class="text-orange-500 font-semibold ml-2">{{ $batch->formattedPrice() }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                                <div class="flex items-center gap-1">
                                                    <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                        @php $pct = $batch->quantity > 0 ? ($batch->quantity_sold / $batch->quantity) * 100 : 0; @endphp
                                                        <div class="h-full bg-orange-400 rounded-full"
                                                             style="width: {{ $pct }}%"></div>
                                                    </div>
                                                    <span>{{ $batch->quantity_sold }}/{{ $batch->quantity }}</span>
                                                </div>
                                                @if($batch->is_active && $batch->quantity_sold < $batch->quantity)
                                                    <span class="px-1.5 py-0.5 bg-green-100 text-green-600 rounded-full">Ativo</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-400 rounded-full">Inativo</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="px-4 py-3 text-xs text-gray-400">Nenhum lote criado ainda.</p>
                            @endif

                            {{-- Formulário novo lote (hidden) --}}
                            <div id="batch-form-{{ $type->id }}" class="hidden border-t border-orange-100 bg-orange-50 p-4">
                                <p class="text-xs font-semibold text-orange-700 mb-3 uppercase tracking-wide">Novo lote</p>
                                <form method="POST"
                                      action="{{ route('tickets.batches.store', [$event->slug, $type->id]) }}"
                                      class="space-y-3">
                                    @csrf
                                    <input type="text" name="name" placeholder="Nome do lote (ex: 1º Lote)"
                                           required
                                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                                  focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <input type="number" name="quantity" placeholder="Qtd." min="1" required
                                                   class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                                          focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                        </div>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">R$</span>
                                            <input type="text" name="price" placeholder="0,00" required
                                                   class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                                          focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button"
                                                onclick="toggleBatchForm('batch-form-{{ $type->id }}')"
                                                class="flex-1 py-2 border border-gray-200 text-gray-500
                                                       text-sm rounded-xl hover:bg-gray-50 transition">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                                class="flex-1 py-2 bg-orange-500 hover:bg-orange-600
                                                       text-white text-sm font-medium rounded-xl transition">
                                            Salvar lote
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">
                            Nenhum tipo de ingresso criado ainda.
                        </p>
                    @endforelse

                    {{-- Novo tipo de ingresso --}}
                    <div class="border-2 border-dashed border-gray-200 rounded-xl overflow-hidden">
                        <button onclick="toggleBatchForm('new-type-form')"
                                class="w-full flex items-center justify-center gap-2 py-3.5 text-sm
                                       text-orange-500 hover:bg-orange-50 font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Novo tipo de {{ $event->ticketLabel() }}
                        </button>

                        <div id="new-type-form" class="hidden border-t border-orange-100 bg-orange-50 p-4">
                            <form method="POST" action="{{ route('tickets.types.store', $event->slug) }}"
                                  class="space-y-3">
                                @csrf
                                <input type="text" name="name" placeholder="Nome (ex: Inteira, VIP, Meia)" required
                                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                              focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                <input type="text" name="description" placeholder="Descrição (opcional)"
                                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                              focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600">
                                    <input type="checkbox" name="is_half_price" value="1"
                                           class="w-4 h-4 text-orange-500 rounded border-gray-300">
                                    É meia-entrada
                                </label>
                                <div class="flex gap-2">
                                    <button type="button"
                                            onclick="toggleBatchForm('new-type-form')"
                                            class="flex-1 py-2 border border-gray-200 text-gray-500
                                                   text-sm rounded-xl hover:bg-gray-50 transition">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="flex-1 py-2 bg-orange-500 hover:bg-orange-600
                                                   text-white text-sm font-medium rounded-xl transition">
                                        Adicionar tipo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>

{{-- ════════════════════════════════════════════════════════════════
     MODAL DE COMPRA
════════════════════════════════════════════════════════════════ --}}
@if(!$event->isFinished() && $event->isPublished() && $event->ticketTypes->isNotEmpty())
<div id="ticket-modal"
     class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
     onclick="if(event.target===this) closeModal()">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    {{-- Painel --}}
    <div class="relative w-full sm:max-w-md bg-white sm:rounded-3xl rounded-t-3xl
                shadow-2xl overflow-hidden z-10 max-h-[90vh] flex flex-col">

        {{-- Handle mobile --}}
        <div class="sm:hidden flex justify-center pt-3 pb-1 shrink-0">
            <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100 shrink-0">
            <div>
                <h3 class="font-bold text-gray-900 text-base">
                    {{ $event->ticketLabel(true, true) }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $event->title }}</p>
            </div>
            <button onclick="closeModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full
                           hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition shrink-0 ml-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('orders.store', $event->slug) }}"
              class="flex flex-col flex-1 overflow-hidden" id="checkout-form">
            @csrf

            {{-- Ingressos --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3">
                @foreach($event->ticketTypes as $index => $type)
                    @php $batch = $type->activeBatch(); @endphp
                    @if($batch)
                        <div class="flex items-center justify-between p-4 border-2 border-gray-100
                                    rounded-2xl hover:border-orange-200 transition" id="ticket-row-{{ $index }}">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 text-sm">{{ $type->name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-orange-500 font-bold text-base">
                                        {{ $batch->formattedPrice() }}
                                    </span>
                                    @if(!$event->is_free && !$event->absorb_service_fee)
                                        <span class="text-xs text-gray-400">+ R$ 1,00 taxa</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400">{{ $batch->name }}
                                    · {{ $batch->remainingQuantity() }} disponíveis</p>
                            </div>

                            {{-- Controle de quantidade --}}
                            <div class="flex items-center gap-2 shrink-0 ml-3">
                                <button type="button" onclick="changeQty({{ $index }}, -1)"
                                        class="w-8 h-8 rounded-full border-2 border-gray-200 flex items-center
                                               justify-center text-gray-500 hover:border-orange-400
                                               hover:text-orange-500 transition font-bold text-lg leading-none">
                                    −
                                </button>
                                <span id="qty-display-{{ $index }}"
                                      class="w-6 text-center font-bold text-gray-900 text-sm">0</span>
                                <button type="button"
                                        onclick="changeQty({{ $index }}, 1, {{ min(5, $batch->remainingQuantity()) }})"
                                        class="w-8 h-8 rounded-full border-2 border-gray-200 flex items-center
                                               justify-center text-gray-500 hover:border-orange-400
                                               hover:text-orange-500 transition font-bold text-lg leading-none">
                                    +
                                </button>
                            </div>

                            <input type="hidden" name="items[{{ $index }}][quantity]"
                                   id="qty-input-{{ $index }}" value="0" disabled>
                            <input type="hidden" name="items[{{ $index }}][batch_id]"
                                   id="batch-input-{{ $index }}" value="{{ $batch->id }}" disabled>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Rodapé com total e botão --}}
            <div class="border-t border-gray-100 px-6 py-4 bg-white shrink-0 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total</span>
                    <span id="modal-total" class="font-bold text-gray-900 text-lg">R$ 0,00</span>
                </div>
                <button type="submit" id="modal-submit-btn" disabled
                        class="w-full py-3.5 bg-orange-500 text-white font-bold rounded-2xl
                               text-sm transition flex items-center justify-center gap-2
                               disabled:opacity-40 disabled:cursor-not-allowed
                               enabled:hover:bg-orange-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    Continuar para pagamento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preços dos lotes para calcular total
    const batchPrices = {
        @foreach($event->ticketTypes as $index => $type)
            @php $batch = $type->activeBatch(); @endphp
            @if($batch)
                {{ $index }}: {{ $batch->price }},
            @endif
        @endforeach
    };
    const serviceFee = {{ (!$event->is_free && !$event->absorb_service_fee) ? 100 : 0 }};
    const quantities = {};

    function changeQty(index, delta, max = 5) {
        quantities[index] = Math.max(0, Math.min(max, (quantities[index] || 0) + delta));

        const qtyInput   = document.getElementById('qty-input-' + index);
        const batchInput = document.getElementById('batch-input-' + index);
        const hasQty     = quantities[index] > 0;

        document.getElementById('qty-display-' + index).textContent = quantities[index];
        qtyInput.value            = quantities[index];
        qtyInput.disabled         = !hasQty;
        batchInput.disabled       = !hasQty;

        // Destaca linha selecionada
        const row = document.getElementById('ticket-row-' + index);
        row.classList.toggle('border-orange-400', hasQty);
        row.classList.toggle('bg-orange-50', hasQty);
        row.classList.toggle('border-gray-100', !hasQty);

        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        let totalQty = 0;
        for (const [idx, qty] of Object.entries(quantities)) {
            if (qty > 0 && batchPrices[idx] !== undefined) {
                total += batchPrices[idx] * qty;
                total += serviceFee * qty;
                totalQty += qty;
            }
        }
        document.getElementById('modal-total').textContent =
            'R$ ' + (total / 100).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        document.getElementById('modal-submit-btn').disabled = totalQty === 0;
    }

    function closeModal() {
        document.getElementById('ticket-modal').classList.add('hidden');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endif

<script>
    function toggleBatchForm(id) {
        document.getElementById(id).classList.toggle('hidden');
    }
</script>

@endsection
