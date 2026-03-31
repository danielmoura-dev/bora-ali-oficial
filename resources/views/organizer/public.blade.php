@extends('layouts.app')
@section('title', ($organizer->username ?? $organizer->name) . ' — Bora Ali')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header do perfil --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">

            {{-- Avatar --}}
            <div class="shrink-0">
                @if($organizer->avatar)
                    <img src="{{ str_starts_with($organizer->avatar, 'http')
                                    ? $organizer->avatar
                                    : Storage::url($organizer->avatar) }}"
                         class="w-20 h-20 rounded-full object-cover border-2 border-gray-100">
                @else
                    <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center
                                justify-center text-indigo-600 font-bold text-3xl">
                        {{ strtoupper(substr($organizer->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h1 class="text-xl font-bold text-gray-800">{{ $organizer->name }}</h1>
                    @if($organizer->username)
                        <span class="text-sm text-gray-400">@{{ $organizer->username }}</span>
                    @endif
                </div>

                @if($organizer->bio)
                    <p class="text-sm text-gray-600 mb-3">{{ $organizer->bio }}</p>
                @endif

                {{-- Links --}}
                <div class="flex flex-wrap items-center gap-3">
                    @if($organizer->website)
                        <a href="{{ $organizer->website }}" target="_blank" rel="noopener"
                           class="flex items-center gap-1 text-xs text-indigo-600 hover:underline">
                            🌐 {{ parse_url($organizer->website, PHP_URL_HOST) }}
                        </a>
                    @endif

                    @if($organizer->instagram)
                        <a href="https://instagram.com/{{ $organizer->instagram }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-1 text-xs text-pink-600 hover:underline">
                            📸 @{{ $organizer->instagram }}
                        </a>
                    @endif

                    @if($organizer->whatsapp)
                        <a href="https://wa.me/55{{ preg_replace('/\D/', '', $organizer->whatsapp) }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-1 text-xs text-green-600 hover:underline">
                            💬 WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="shrink-0 text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $totalEvents }}</p>
                <p class="text-xs text-gray-400">evento(s) realizado(s)</p>
            </div>
        </div>
    </div>

    {{-- Eventos ativos --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Próximos eventos</h2>

        @if($currentEvents->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
                <p class="text-gray-400 text-sm">Nenhum evento ativo no momento.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($currentEvents as $event)
                    @include('partials.event-card', ['event' => $event])
                @endforeach
            </div>
        @endif
    </section>

    {{-- Eventos encerrados --}}
    @if($finishedEvents->isNotEmpty())
        <section>
            <h2 class="text-lg font-semibold text-gray-500 mb-4">Eventos anteriores</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 opacity-70">
                @foreach($finishedEvents as $event)
                    @include('partials.event-card', ['event' => $event, 'finished' => true])
                @endforeach
            </div>
        </section>
    @endif

</div>

@use('Illuminate\Support\Facades\Storage')
@endsection