@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('title', 'Bora Ali — Eventos Regionais')

@section('content')

{{-- ===== CARROSSEL (só aparece sem busca ativa e com eventos) ===== --}}
@if(!$query && $currentEvents->isNotEmpty())
    @php $carouselEvents = $currentEvents->take(6); @endphp

    <div class="mb-10 -mx-4 sm:mx-0" x-data="carousel({{ $carouselEvents->count() }})">

        {{-- Imagem principal --}}
        <div class="relative overflow-hidden sm:rounded-2xl bg-gray-900"
             style="aspect-ratio: 16/7">

            {{-- Slides --}}
            @foreach($carouselEvents as $i => $event)
                <a href="{{ route('events.show', $event->slug) }}"
                   class="absolute inset-0 transition-opacity duration-500"
                   :class="current === {{ $i }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                    @if($event->cover_image)
                        <img src="{{ $event->coverUrl() }}"
                             alt="{{ $event->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600
                                    flex items-center justify-center">
                            <span class="text-7xl">🎉</span>
                        </div>
                    @endif
                    {{-- Gradiente inferior --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                </a>
            @endforeach

            {{-- Seta esquerda --}}
            <button @click.prevent="prev()"
                    class="absolute left-3 top-1/2 -translate-y-1/2 z-20
                           w-9 h-9 flex items-center justify-center
                           bg-white/20 hover:bg-white/40 backdrop-blur-sm
                           rounded-full text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Seta direita --}}
            <button @click.prevent="next()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 z-20
                           w-9 h-9 flex items-center justify-center
                           bg-white/20 hover:bg-white/40 backdrop-blur-sm
                           rounded-full text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Dots --}}
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5">
                @foreach($carouselEvents as $i => $event)
                    <button @click.prevent="current = {{ $i }}"
                            class="rounded-full transition-all duration-300"
                            :class="current === {{ $i }}
                                ? 'w-5 h-1.5 bg-white'
                                : 'w-1.5 h-1.5 bg-white/50 hover:bg-white/80'">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Info do evento ativo --}}
        <div class="mt-3 px-4 sm:px-0">
            @foreach($carouselEvents as $i => $event)
                <a href="{{ route('events.show', $event->slug) }}"
                   x-show="current === {{ $i }}"
                   x-cloak
                   class="flex items-start gap-3 group">
                    <div class="min-w-0 flex-1">
                        <h2 class="font-semibold text-gray-900 text-base leading-snug
                                   group-hover:text-indigo-600 transition truncate">
                            {{ $event->title }}
                        </h2>
                        <div class="flex items-center gap-3 mt-0.5 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $event->city }}/{{ $event->state }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $event->starts_at->translatedFormat('d \d\e M \d\e Y') }}
                            </span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 shrink-0 mt-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endforeach
        </div>
    </div>
@endif

{{-- ===== RESULTADO DE BUSCA ===== --}}
@if($query)
    <div class="mb-6">
        <p class="text-gray-500 text-sm">
            Resultados para <strong class="text-gray-800">"{{ $query }}"</strong>
        </p>
    </div>
@endif

{{-- ===== EVENTOS EM DESTAQUE / ENCONTRADOS ===== --}}
<section class="mb-12">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        @if($query) Eventos encontrados @else Eventos em destaque @endif
    </h2>

    @if($currentEvents->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <div class="text-5xl mb-3">🔍</div>
            <p class="font-medium">Nenhum evento encontrado</p>
            @if($query)
                <p class="text-sm mt-1">Tente buscar por outro termo ou cidade.</p>
                <a href="{{ route('home') }}"
                   class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                    Ver todos os eventos
                </a>
            @else
                <p class="text-sm mt-1">Em breve novos eventos por aqui.</p>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($currentEvents as $event)
                @include('partials.event-card', ['event' => $event])
            @endforeach
        </div>
    @endif
</section>

{{-- ===== EVENTOS ENCERRADOS ===== --}}
@if(!$query && $finishedEvents->isNotEmpty())
    <section>
        <h2 class="text-lg font-semibold text-gray-500 mb-4">Eventos encerrados</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 opacity-70">
            @foreach($finishedEvents as $event)
                @include('partials.event-card', ['event' => $event, 'finished' => true])
            @endforeach
        </div>
    </section>
@endif

@endsection

@push('scripts')
<script>
    function carousel(total) {
        return {
            current: 0,
            total: total,
            timer: null,
            init() {
                this.timer = setInterval(() => this.next(), 5000);
            },
            next() {
                this.current = (this.current + 1) % this.total;
                this.resetTimer();
            },
            prev() {
                this.current = (this.current - 1 + this.total) % this.total;
                this.resetTimer();
            },
            resetTimer() {
                clearInterval(this.timer);
                this.timer = setInterval(() => this.next(), 5000);
            }
        }
    }
</script>
@endpush
