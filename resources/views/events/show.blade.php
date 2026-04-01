@extends('layouts.app')
@section('title', $event->title . ' — Bora Ali')

@section('content')
    <div class="max-w-3xl mx-auto">

        {{-- Status e ações do organizador --}}
        @auth
            @if (Auth::id() === $event->user_id)
                <div
                    class="flex items-center justify-between mb-4 p-4 bg-yellow-50
                        border border-yellow-200 rounded-xl">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-yellow-800">
                            Status:
                            <span class="capitalize">{{ $event->status }}</span>
                        </span>
                    </div>
                    <div class="flex gap-2">
                        @if ($event->status === 'draft')
                            <form method="POST" action="{{ route('events.publish', $event->slug) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700
                                           text-white text-sm rounded-lg transition">
                                    Publicar evento
                                </button>
                            </form>
                        @endif

                        @if (in_array($event->status, ['draft', 'published']))
                            <form method="POST" action="{{ route('events.cancel', $event->slug) }}"
                                onsubmit="return confirm('Cancelar o evento e notificar todos os compradores?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 border border-red-300 text-red-600
                                               hover:bg-red-50 text-sm rounded-lg transition">
                                    Cancelar evento
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        @endauth

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700
                    rounded-xl text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($event->isPublished())
            <a href="{{ route('checkin.index', $event->slug) }}"
                class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white
              text-sm font-medium rounded-lg transition">
                📷 Check-in
            </a>
        @endif

        {{-- Capa --}}
        <div
            class="aspect-video bg-gradient-to-br from-indigo-100 to-purple-100
                rounded-2xl overflow-hidden mb-6 flex items-center justify-center">
            @if ($event->cover_image)
                <img src="{{ $event->coverUrl() }}" alt="{{ $event->title }}"
                    class="w-full h-full object-cover">
            @else
                <span class="text-6xl">🎉</span>
            @endif
        </div>

        {{-- Cabeçalho --}}
        <div class="mb-6">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                @if ($event->is_free)
                    <span class="text-xs font-medium px-3 py-1 bg-green-100 text-green-700 rounded-full">
                        Gratuito
                    </span>
                @endif
                @if ($event->isFinished())
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
                @if ($event->ends_at->isSameDay($event->starts_at))
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
                    <div class="flex items-center gap-3">
                        @if ($event->organizer->avatar)
                            <img src="{{ str_starts_with($event->organizer->avatar, 'http')
                                ? $event->organizer->avatar
                                : Storage::url($event->organizer->avatar) }}"
                                class="w-9 h-9 rounded-full object-cover">
                        @else
                            <div
                                class="w-9 h-9 rounded-full bg-indigo-100 flex items-center
                        justify-center text-indigo-600 font-bold text-sm">
                                {{ strtoupper(substr($event->organizer->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $event->organizer->name }}</p>
                            @if ($event->organizer->username)
                                <a href="{{ route('organizer.public', $event->organizer->username) }}"
                                    class="text-xs text-indigo-600 hover:underline">
                                    Ver perfil →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CTA Ingresso --}}
                @if (!$event->isFinished() && $event->isPublished())
                    @if ($event->ticketTypes->isNotEmpty())
                        <div class="bg-white rounded-2xl border border-gray-100 p-4">
                            <h3 class="font-semibold text-gray-800 text-sm mb-3">🎫 Ingressos</h3>
                            <form method="POST" action="{{ route('orders.store', $event->slug) }}">
                                @csrf
                                @foreach ($event->ticketTypes as $type)
                                    @php $batch = $type->activeBatch(); @endphp
                                    @if ($batch)
                                        <div
                                            class="flex items-center justify-between py-2
                                    border-b border-gray-50 last:border-0">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">{{ $type->name }}</p>
                                                <p class="text-xs text-indigo-600 font-medium">
                                                    {{ $batch->formattedPrice() }}
                                                    <span class="text-gray-400">({{ $batch->name }})</span>
                                                </p>
                                            </div>
                                            <select name="items[{{ $loop->index }}][quantity]"
                                                class="text-sm border border-gray-200 rounded-lg px-2 py-1">
                                                @for ($i = 0; $i <= min(5, $batch->remainingQuantity()); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <input type="hidden" name="items[{{ $loop->index }}][batch_id]"
                                                value="{{ $batch->id }}">
                                        </div>
                                    @endif
                                @endforeach
                                <button type="submit"
                                    class="w-full mt-3 py-2.5 bg-indigo-600 hover:bg-indigo-700
                               text-white font-semibold rounded-xl text-sm transition">
                                    Comprar ingresso
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Painel do organizador: adicionar ingressos --}}
        @auth
            @if (Auth::id() === $event->user_id)
                <div class="mt-8 space-y-4">

                    {{-- Status do Mercado Pago — só mostra se o evento usar split --}}
                    @if ($event->usesSplit())
                        @if (Auth::user()->hasMpConnected())
                            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl">
                                <span class="text-green-600 text-lg">✅</span>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-green-800">Mercado Pago conectado</p>
                                    <p class="text-xs text-green-600">Seus repasses estão configurados.</p>
                                </div>
                                <form method="POST" action="{{ route('mp.disconnect') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-500 hover:underline">
                                        Desconectar
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                                <span class="text-yellow-600 text-lg">⚠️</span>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-yellow-800">Conecte seu Mercado Pago</p>
                                    <p class="text-xs text-yellow-700">
                                        Necessário para receber os pagamentos direto na sua conta.
                                    </p>
                                </div>
                                <a href="{{ route('mp.connect') }}"
                                    class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white
                      text-xs font-medium rounded-lg transition shrink-0">
                                    Conectar Mercado Pago
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                            <span class="text-blue-600 text-lg">💰</span>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Pagamento direto na plataforma</p>
                                <p class="text-xs text-blue-600">
                                    A Bora Ali recebe e repassa para você após o evento.
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Gerenciar ingressos --}}
                    <h2 class="font-semibold text-gray-800">Gerenciar ingressos</h2>

                    {{-- Tipos existentes --}}
                    @foreach ($event->ticketTypes as $type)
                        <div class="bg-white rounded-2xl border border-gray-100 p-4">
                            <p class="font-medium text-gray-700 text-sm mb-2">{{ $type->name }}</p>
                            @foreach ($type->batches as $batch)
                                <div
                                    class="text-xs text-gray-500 flex justify-between py-1
                            border-b border-gray-50 last:border-0">
                                    <span>{{ $batch->name }} — {{ $batch->formattedPrice() }}</span>
                                    <span>{{ $batch->quantity_sold }}/{{ $batch->quantity }} vendidos</span>
                                </div>
                            @endforeach

                            {{-- Adicionar lote --}}
                            <details class="mt-3">
                                <summary class="text-xs text-indigo-600 cursor-pointer hover:underline">
                                    + Adicionar lote
                                </summary>
                                <form method="POST" action="{{ route('tickets.batches.store', [$event->slug, $type->id]) }}"
                                    class="mt-3 space-y-2">
                                    @csrf
                                    <input type="text" name="name" placeholder="Nome do lote (ex: 1º Lote)"
                                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg">
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" name="quantity" placeholder="Quantidade" min="1"
                                            class="px-3 py-2 text-sm border border-gray-200 rounded-lg">
                                        <input type="text" name="price" placeholder="Preço (ex: 50,00)"
                                            class="px-3 py-2 text-sm border border-gray-200 rounded-lg">
                                    </div>
                                    <button type="submit" class="w-full py-2 bg-gray-800 text-white text-sm rounded-lg">
                                        Salvar lote
                                    </button>
                                </form>
                            </details>
                        </div>
                    @endforeach

                    {{-- Adicionar tipo --}}
                    <details class="bg-white rounded-2xl border border-dashed border-gray-200 p-4">
                        <summary class="text-sm text-indigo-600 cursor-pointer hover:underline font-medium">
                            + Novo tipo de ingresso
                        </summary>
                        <form method="POST" action="{{ route('tickets.types.store', $event->slug) }}"
                            class="mt-4 space-y-3">
                            @csrf
                            <input type="text" name="name" placeholder="Nome (ex: Inteira, VIP, Meia)"
                                class="w-full px-4 py-2 text-sm border border-gray-200 rounded-xl">
                            <input type="text" name="description" placeholder="Descrição (opcional)"
                                class="w-full px-4 py-2 text-sm border border-gray-200 rounded-xl">
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="is_half_price" value="1">
                                É meia-entrada
                            </label>
                            <button type="submit" class="w-full py-2 bg-indigo-600 text-white text-sm rounded-xl">
                                Adicionar tipo
                            </button>
                        </form>
                    </details>
                </div>
            @endif
        @endauth
    </div>
@endsection
