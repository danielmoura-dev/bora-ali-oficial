@php
    $lowestPrice = null;
    if (!$event->is_free) {
        foreach ($event->ticketTypes as $type) {
            $batch = $type->activeBatch();
            if ($batch) {
                $lowestPrice = $lowestPrice === null
                    ? $batch->price
                    : min($lowestPrice, $batch->price);
            }
        }
    }
    $isFinished = $finished ?? false;
    $isToday = !$isFinished && $event->starts_at->isToday();
    $isThisWeek = !$isFinished && !$isToday && $event->starts_at->isCurrentWeek();
@endphp

<a href="{{ route('events.show', $event->slug) }}"
   class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100
          hover:border-orange-100 hover:shadow-lg hover:-translate-y-0.5
          transition-all duration-200 {{ $isFinished ? 'opacity-60' : '' }}">

    {{-- Capa --}}
    <div class="relative aspect-video overflow-hidden bg-gradient-to-br from-orange-100 to-orange-100">
        @if($event->cover_image)
            <img src="{{ $event->coverUrl() }}"
                 alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center
                        bg-gradient-to-br from-orange-100 to-orange-100">
                <span class="text-5xl">🎉</span>
            </div>
        @endif

        {{-- Badge topo esquerdo --}}
        <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
            @if($isToday)
                <span class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1
                             bg-rose-500 text-white rounded-full shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse inline-block"></span>
                    Hoje
                </span>
            @elseif($isThisWeek)
                <span class="text-xs font-semibold px-2.5 py-1
                             bg-amber-400 text-white rounded-full shadow-sm">
                    Esta semana
                </span>
            @elseif($isFinished)
                <span class="text-xs font-medium px-2.5 py-1
                             bg-gray-800/60 text-white backdrop-blur-sm rounded-full">
                    Encerrado
                </span>
            @endif
        </div>

        {{-- Preço topo direito --}}
        <div class="absolute top-2.5 right-2.5">
            @if($event->is_free)
                <span class="text-xs font-semibold px-2.5 py-1
                             bg-emerald-500 text-white rounded-full shadow-sm">
                    Gratuito
                </span>
            @elseif($lowestPrice !== null)
                <span class="text-xs font-semibold px-2.5 py-1
                             bg-white/90 backdrop-blur-sm text-gray-800 rounded-full shadow-sm">
                    A partir de R$ {{ number_format($lowestPrice / 100, 2, ',', '.') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Conteúdo --}}
    <div class="flex flex-col flex-1 p-4 gap-2">

        {{-- Título --}}
        <h3 class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2
                   group-hover:text-orange-500 transition-colors">
            {{ $event->title }}
        </h3>

        {{-- Data --}}
        <div class="flex items-center gap-1.5 text-xs text-orange-500 font-medium">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ $event->starts_at->translatedFormat('D, d \d\e M · H:i') }}
        </div>

        {{-- Separador --}}
        <div class="border-t border-gray-100 mt-auto pt-2 flex items-center justify-between">
            {{-- Local --}}
            <p class="text-xs text-gray-500 flex items-center gap-1 truncate">
                <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="truncate">{{ $event->city }}, {{ $event->state }}</span>
            </p>

            {{-- Seta --}}
            <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-orange-400 shrink-0 transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </div>
</a>
