@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('title', $event->title . ' — Bora Ali')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Status e ações do organizador --}}
    @auth
        @if(Auth::id() === $event->user_id)
            <div class="flex items-center justify-between mb-4 p-4 bg-yellow-50
                        border border-yellow-200 rounded-xl">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-yellow-800">
                        Status:
                        <span class="capitalize">{{ $event->status }}</span>
                    </span>
                </div>
                <div class="flex gap-2">
                    @if($event->status === 'draft')
                        <form method="POST"
                              action="{{ route('events.publish', $event->slug) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700
                                           text-white text-sm rounded-lg transition">
                                Publicar evento
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    @endauth

    @if(session('status'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700
                    rounded-xl text-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- Capa --}}
    <div class="aspect-video bg-gradient-to-br from-indigo-100 to-purple-100
                rounded-2xl overflow-hidden mb-6 flex items-center justify-center">
        @if($event->cover_image)
            <img src="{{ Storage::url($event->cover_image) }}"
                 alt="{{ $event->title }}"
                 class="w-full h-full object-cover">
        @else
            <span class="text-6xl">🎉</span>
        @endif
    </div>

    {{-- Cabeçalho --}}
    <div class="mb-6">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            @if($event->is_free)
                <span class="text-xs font-medium px-3 py-1 bg-green-100 text-green-700 rounded-full">
                    Gratuito
                </span>
            @endif
            @if($event->isFinished())
                <span class="text-xs font-medium px-3 py-1 bg-gray-100 text-gray-500 rounded-full">
                    Encerrado
                </span>
            @endif
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
            {{ $event->title }}
        </h1>

        <p class="text-indigo-600 font-medium">
            {{ $event->starts_at->translatedFormat('D, d \d\e M \d\e Y · H:i') }}
            @if($event->ends_at->isSameDay($event->starts_at))
                até {{ $event->ends_at->format('H:i') }}
            @else
                até {{ $event->ends_at->translatedFormat('d \d\e M · H:i') }}
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {{-- Descrição --}}
        <div class="sm:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-semibold text-gray-800 mb-3">Sobre o evento</h2>
                <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ $event->description }}
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Local --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">📍 Local</h3>
                <p class="font-medium text-gray-700 text-sm">{{ $event->venue_name }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $event->venue_address }}</p>
                <p class="text-gray-500 text-xs">{{ $event->city }}/{{ $event->state }}</p>
            </div>

            {{-- Organizador --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">👤 Organizador</h3>
                <p class="text-gray-700 text-sm">{{ $event->organizer->name }}</p>
            </div>

            {{-- CTA Ingresso (placeholder) --}}
            @if(!$event->isFinished() && $event->isPublished())
                <div class="bg-indigo-600 rounded-2xl p-4 text-white text-center">
                    <p class="text-xs opacity-80 mb-1">Garanta seu lugar</p>
                    <button class="w-full py-2.5 bg-white text-indigo-600 font-semibold
                                   rounded-xl text-sm hover:bg-indigo-50 transition">
                        Comprar ingresso
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection