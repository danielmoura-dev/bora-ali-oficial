<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bora Ali — Eventos Regionais')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Mobile: linha 1 --}}
            <div class="flex items-center justify-between h-14">
                <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600 shrink-0">
                    Bora Ali 🎉
                </a>

                {{-- Desktop: busca central --}}
                <form method="GET" action="{{ route('home') }}" class="hidden md:flex flex-1 max-w-md mx-6">
                    <div class="relative w-full">
                        <input type="search" name="q" value="{{ request('q') }}"
                            placeholder="Buscar eventos, cidades..."
                            class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      text-sm bg-gray-50">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                {{-- Desktop: nav direita --}}
                <nav class="hidden md:flex items-center gap-1">
                    @auth
                        <a href="{{ route('organizer.dashboard') }}"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-indigo-600
          hover:bg-indigo-50 rounded-lg transition">
                            Painel
                        </a>
                        <a href="{{ route('events.create') }}"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-indigo-600
                                           hover:bg-indigo-50 rounded-lg transition">
                            Criar Evento
                        </a>
                        <a href="{{ route('events.my') }}"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-indigo-600
                                           hover:bg-indigo-50 rounded-lg transition">
                            Meus Eventos
                        </a>
                        <a href="#"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-indigo-600
                                           hover:bg-indigo-50 rounded-lg transition">
                            Meus Ingressos
                        </a>

                        {{-- Avatar / Perfil --}}
                        <div class="relative ml-2" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg
                                           hover:bg-gray-50 transition text-sm">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" class="w-7 h-7 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-7 h-7 rounded-full bg-indigo-100 flex items-center
                                                justify-center text-indigo-600 font-medium text-xs">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            </button>

                            <div x-show="open" @click.outside="open = false"
                                class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg
                                        border border-gray-100 py-1 text-sm">
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    Perfil
                                </a>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-red-600
                                                   hover:bg-red-50">
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('auth.login') }}"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-indigo-600
                                  rounded-lg transition">
                            Entrar
                        </a>
                        <a href="{{ route('auth.register') }}"
                            class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700
                                  text-white rounded-lg transition">
                            Criar conta
                        </a>
                    @endauth
                </nav>

                {{-- Mobile: ícone menu --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            {{-- Mobile: busca --}}
            <div class="md:hidden pb-3">
                <form method="GET" action="{{ route('home') }}">
                    <div class="relative">
                        <input type="search" name="q" value="{{ request('q') }}"
                            placeholder="Buscar eventos, cidades..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      text-sm bg-gray-50">
                        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            {{-- Mobile: menu expandido --}}
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-100 py-3 space-y-1">
                @auth
                    <a href="{{ route('organizer.dashboard') }}"
                        class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        Painel
                    </a>
                    <a href="{{ route('events.create') }}"
                        class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        Criar Evento
                    </a>
                    <a href="{{ route('events.my') }}"
                        class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        Meus Eventos
                    </a>
                    <a href="#" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        Meus Ingressos
                    </a>
                    <a href="#" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        Perfil
                    </a>
                    <form method="POST" action="{{ route('auth.logout') }}" class="px-3">
                        @csrf
                        <button type="submit" class="w-full text-left py-2 text-sm text-red-600">
                            Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.login') }}"
                        class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        Entrar
                    </a>
                    <a href="{{ route('auth.register') }}"
                        class="block px-3 py-2 text-sm text-indigo-600 font-medium hover:bg-indigo-50 rounded-lg">
                        Criar conta
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Conteúdo --}}
    <main class="max-w-6xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>

</html>
