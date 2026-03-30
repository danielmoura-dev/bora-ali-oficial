@extends('layouts.app')
@section('title', 'Bora Ali — Eventos Regionais')

@section('content')

{{-- Resultado de busca --}}
@if($query)
    <div class="mb-6">
        <p class="text-gray-500 text-sm">
            Resultados para <strong class="text-gray-800">"{{ $query }}"</strong>
        </p>
    </div>
@endif

{{-- Eventos Atuais --}}
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

{{-- Eventos Encerrados --}}
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