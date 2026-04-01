@extends('layouts.app')
@section('title', 'Meus Eventos — Bora Ali')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Meus Eventos</h1>
        <a href="{{ route('events.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                  text-sm font-medium rounded-xl transition">
            + Criar evento
        </a>
    </div>

    @if($events->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <div class="text-5xl mb-3">📅</div>
            <p class="font-medium">Você ainda não criou nenhum evento</p>
            <a href="{{ route('events.create') }}"
               class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                Criar meu primeiro evento
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($events as $event)
                <div class="bg-white rounded-2xl border border-gray-100 p-4
                            flex items-center gap-4 hover:shadow-sm transition">

                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-indigo-100
                                to-purple-100 flex items-center justify-center shrink-0 overflow-hidden">
                        @if($event->cover_image)
                            <img src="{{ $event->coverUrl() }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl">🎉</span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span @class([
                                'text-xs font-medium px-2 py-0.5 rounded-full',
                                'bg-yellow-100 text-yellow-700' => $event->status === 'draft',
                                'bg-green-100 text-green-700'  => $event->status === 'published',
                                'bg-gray-100 text-gray-500'    => $event->status === 'finished',
                                'bg-red-100 text-red-600'      => $event->status === 'cancelled',
                            ])>
                                {{ match($event->status) {
                                    'draft'     => 'Rascunho',
                                    'published' => 'Publicado',
                                    'finished'  => 'Encerrado',
                                    'cancelled' => 'Cancelado',
                                } }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-800 text-sm truncate">
                            {{ $event->title }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $event->starts_at->translatedFormat('d \d\e M \d\e Y · H:i') }}
                            · {{ $event->city }}/{{ $event->state }}
                        </p>
                    </div>

                    <a href="{{ route('events.show', $event->slug) }}"
                       class="px-3 py-2 text-sm text-indigo-600 hover:bg-indigo-50
                              rounded-lg transition shrink-0">
                        Ver →
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection