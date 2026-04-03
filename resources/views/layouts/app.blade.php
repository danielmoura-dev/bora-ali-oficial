<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bora Ali — Eventos Regionais')</title>
    <style>[x-cloak] { display: none !important; }</style>
    @if (config('services.google.analytics_id'))
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ config('services.google.analytics_id') }}', {
                'anonymize_ip': true
            });
        </script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center gap-4 h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="shrink-0 flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Bora Ali" class="h-9 w-auto">
                </a>

                {{-- Busca central (desktop) --}}
                <form method="GET" action="{{ route('home') }}"
                      class="hidden md:flex flex-1 max-w-lg mx-auto">
                    <div class="relative w-full">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Buscar eventos, cidades..."
                               class="w-full pl-10 pr-10 py-2.5 rounded-2xl border border-gray-200
                                      bg-gray-50 text-sm focus:outline-none focus:ring-2
                                      focus:ring-orange-500 focus:border-transparent
                                      transition placeholder-gray-400">
                        @if(request('q'))
                            <a href="{{ route('home') }}"
                               class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Spacer mobile --}}
                <div class="flex-1 md:hidden"></div>

                {{-- Desktop: nav direita --}}
                <nav class="hidden md:flex items-center gap-0.5 shrink-0">

                    {{-- Painel (só para quem já tem eventos) --}}
                    @auth
                        @if(Auth::user()->hasEvents())
                            <a href="{{ route('organizer.dashboard') }}"
                               class="flex flex-col items-center gap-0.5 px-3 py-2 text-xs text-gray-500
                                      hover:text-orange-500 hover:bg-orange-50 rounded-xl transition group">
                                <svg class="w-5 h-5 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Painel</span>
                            </a>
                        @endif
                    @endauth

                    {{-- Criar Evento --}}
                    <a href="{{ route('events.create') }}"
                       class="flex flex-col items-center gap-0.5 px-3 py-2 text-xs text-gray-500
                              hover:text-orange-500 hover:bg-orange-50 rounded-xl transition group">
                        <svg class="w-5 h-5 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Criar Evento</span>
                    </a>

                    {{-- Meus Eventos --}}
                    <a href="{{ route('events.my') }}"
                       class="flex flex-col items-center gap-0.5 px-3 py-2 text-xs text-gray-500
                              hover:text-orange-500 hover:bg-orange-50 rounded-xl transition group">
                        <svg class="w-5 h-5 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Meus Eventos</span>
                    </a>

                    {{-- Ingressos --}}
                    <a href="{{ route('tickets.my') }}"
                       class="flex flex-col items-center gap-0.5 px-3 py-2 text-xs text-gray-500
                              hover:text-orange-500 hover:bg-orange-50 rounded-xl transition group">
                        <svg class="w-5 h-5 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <span>Ingressos</span>
                    </a>

                    {{-- Divisor --}}
                    <div class="w-px h-8 bg-gray-200 mx-1"></div>

                    {{-- Ícone de usuário --}}
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center justify-center w-9 h-9 rounded-full
                                           hover:ring-2 hover:ring-orange-300 transition overflow-hidden
                                           bg-orange-100 text-orange-500 font-semibold text-sm">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </button>

                            <div x-cloak x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.outside="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl
                                        border border-gray-100 py-1.5 text-sm z-50">
                                <div class="px-4 py-2 border-b border-gray-100 mb-1">
                                    <p class="font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.show') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Perfil
                                </a>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2
                                                   text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Não logado: ícone de pessoa com dropdown Entrar / Criar conta --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center justify-center w-9 h-9 rounded-full
                                           bg-gray-100 hover:bg-orange-50 hover:text-orange-500
                                           text-gray-500 transition hover:ring-2 hover:ring-orange-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </button>

                            <div x-cloak x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.outside="open = false"
                                 class="absolute right-0 mt-2 w-44 bg-white rounded-2xl shadow-xl
                                        border border-gray-100 py-1.5 text-sm z-50">
                                <a href="{{ route('auth.login') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Entrar
                                </a>
                                <a href="{{ route('auth.register') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-orange-500 hover:bg-orange-50 transition font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Criar conta
                                </a>
                            </div>
                        </div>
                    @endauth
                </nav>

                {{-- Mobile: lado direito --}}
                <div class="md:hidden flex items-center gap-1">
                    {{-- Ingressos (mobile, sempre visível) --}}
                    <a href="{{ route('tickets.my') }}"
                       class="flex items-center gap-1.5 px-2.5 py-2 rounded-xl
                              text-gray-500 hover:text-orange-500 hover:bg-orange-50 transition text-xs font-medium">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <span>Ingressos</span>
                    </a>

                    {{-- Hamburger --}}
                    <button id="mobile-menu-btn"
                            class="flex items-center justify-center w-9 h-9 rounded-xl
                                   text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                        <svg id="hamburger-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg id="close-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>

            {{-- Mobile: busca (linha abaixo) --}}
            <div class="md:hidden pb-3">
                <form method="GET" action="{{ route('home') }}">
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Buscar eventos, cidades..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-gray-200
                                      bg-gray-50 text-sm focus:outline-none focus:ring-2
                                      focus:ring-orange-500 focus:border-transparent
                                      transition placeholder-gray-400">
                    </div>
                </form>
            </div>

            {{-- Mobile: menu expandido --}}
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-100 py-3 space-y-0.5">
                @auth
                    <div class="flex items-center gap-3 px-3 py-2.5 mb-2">
                        <div class="w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center
                                    text-orange-500 font-semibold text-sm shrink-0 overflow-hidden">
                            @if (Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <a href="{{ route('profile.show') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Perfil
                    </a>
                    <a href="{{ route('events.create') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Criar Evento
                    </a>
                    <a href="{{ route('events.my') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Meus Eventos
                    </a>
                    <a href="{{ route('tickets.my') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        Ingressos
                    </a>
                    @if(Auth::user()->hasEvents())
                        <a href="{{ route('organizer.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Painel
                        </a>
                    @endif

                    <div class="pt-1 border-t border-gray-100 mt-1">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5
                                           text-sm text-red-600 hover:bg-red-50 rounded-xl transition">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sair
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('auth.login') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Entrar
                    </a>
                    <a href="{{ route('auth.register') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm text-orange-500 font-medium hover:bg-orange-50 rounded-xl transition">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Criar conta
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Conteúdo --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @include('partials.cookie-banner')
    @stack('scripts')

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        mobileMenuBtn?.addEventListener('click', function () {
            const isHidden = mobileMenu.classList.toggle('hidden');
            hamburgerIcon.classList.toggle('hidden', !isHidden);
            closeIcon.classList.toggle('hidden', isHidden);
        });
    </script>
</body>

</html>
