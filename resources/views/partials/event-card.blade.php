<article class="bg-white rounded-2xl border border-gray-100 overflow-hidden
                hover:shadow-md transition group">

    {{-- Capa --}}
    <div class="aspect-video bg-gradient-to-br from-indigo-100 to-purple-100
                flex items-center justify-center overflow-hidden">
        @if($event->cover_image)
            <img src="{{ $event->cover_image }}"
                 alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <span class="text-4xl">🎉</span>
        @endif
    </div>

    <div class="p-4">
        {{-- Badges --}}
        <div class="flex items-center gap-2 mb-2">
            @if($event->is_free)
                <span class="text-xs font-medium px-2 py-0.5 bg-green-100 text-green-700 rounded-full">
                    Gratuito
                </span>
            @endif
            @if($finished ?? false)
                <span class="text-xs font-medium px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full">
                    Encerrado
                </span>
            @endif
        </div>

        {{-- Título --}}
        <h3 class="font-semibold text-gray-800 text-sm leading-snug mb-1 line-clamp-2">
            {{ $event->title }}
        </h3>

        {{-- Data --}}
        <p class="text-xs text-indigo-600 font-medium mb-2">
            {{ $event->starts_at->translatedFormat('D, d \d\e M · H:i') }}
        </p>

        {{-- Local --}}
        <p class="text-xs text-gray-500 flex items-center gap-1 truncate">
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $event->venue_name }} · {{ $event->city }}/{{ $event->state }}
        </p>
    </div>
</article>