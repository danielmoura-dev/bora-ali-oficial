@extends('layouts.app')
@section('title', 'Meu Perfil — Bora Ali')

@section('content')
    <div class="max-w-2xl mx-auto">

        <h1 class="text-2xl font-bold text-gray-800 mb-8">Meu Perfil</h1>

        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700
                    rounded-xl text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Avatar e info --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
            <div class="flex items-center gap-5 mb-6">
                <div class="relative">
                    @if ($user->avatar)
                        <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : Storage::url($user->avatar) }}"
                            class="w-20 h-20 rounded-full object-cover border-2 border-gray-100">
                    @else
                        <div
                            class="w-20 h-20 rounded-full bg-indigo-100 flex items-center
                                justify-center text-indigo-600 font-bold text-2xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="font-semibold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if ($user->isEmailVerified())
                            <span class="text-xs text-green-600 flex items-center gap-1">
                                ✅ E-mail verificado
                            </span>
                        @else
                            <span class="text-xs text-yellow-600">⚠️ E-mail não verificado</span>
                        @endif
                        @if ($user->isPhoneVerified())
                            <span class="text-xs text-green-600">· ✅ Celular verificado</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Trocar avatar --}}
            <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="flex items-center gap-3">
                    <label
                        class="cursor-pointer px-4 py-2 border border-gray-200 rounded-xl
                              text-sm text-gray-600 hover:bg-gray-50 transition">
                        <span>Trocar foto</span>
                        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </label>
                    @error('avatar')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        {{-- Dados pessoais --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">
                Dados pessoais
            </h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                              @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                              @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">
                        Ao trocar o e-mail, você precisará verificá-lo novamente.
                    </p>
                </div>

                {{-- Dados do perfil (somente leitura) --}}
                @if ($user->profile_type)
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-50">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">
                                {{ $user->isCpf() ? 'CPF' : 'CNPJ' }}
                            </label>
                            <p class="text-sm text-gray-600">
                                {{ $user->document_number }}
                            </p>
                        </div>
                        @if ($user->isCpf() && $user->birth_date)
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">
                                    Nascimento
                                </label>
                                <p class="text-sm text-gray-600">
                                    {{ $user->birth_date->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif
                        @if ($user->isCnpj() && $user->company_name)
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">
                                    Razão Social
                                </label>
                                <p class="text-sm text-gray-600">{{ $user->company_name }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <button type="submit"
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                           font-medium rounded-xl transition text-sm">
                    Salvar alterações
                </button>
            </form>
        </div>

        {{-- Mercado Pago --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">
                Mercado Pago
            </h2>

            @if ($user->hasMpConnected())
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">✅</span>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Conta conectada</p>
                            <p class="text-xs text-gray-400">
                                ID: {{ $user->mp_user_id }}
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('mp.disconnect') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-500 hover:underline">
                            Desconectar
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">
                            Conecte para receber pagamentos como organizador.
                        </p>
                    </div>
                    <a href="{{ route('mp.connect') }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                          text-sm font-medium rounded-xl transition">
                        Conectar
                    </a>
                </div>
            @endif
        </div>

        {{-- Alterar senha (só para usuários com senha) --}}
        @if ($user->password)
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">
                    Alterar senha
                </h2>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Senha atual
                        </label>
                        <input type="password" name="current_password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                                  @error('current_password') border-red-400 @enderror">
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nova senha
                        </label>
                        <input type="password" name="password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                                  @error('password') border-red-400 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirmar nova senha
                        </label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-gray-800 hover:bg-gray-900 text-white
                               font-medium rounded-xl transition text-sm">
                        Atualizar senha
                    </button>
                </form>
            </div>
        @endif

        {{-- Perfil público --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Perfil público
                </h2>
                @if ($user->username)
                    <a href="{{ route('organizer.public', $user->username) }}" target="_blank"
                        class="text-xs text-indigo-600 hover:underline">
                        Ver meu perfil →
                    </a>
                @endif
            </div>

            <form method="POST" action="{{ route('profile.public.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nome de usuário
                    </label>
                    <div class="flex items-center">
                        <span
                            class="px-3 py-3 bg-gray-50 border border-r-0 border-gray-200
                             rounded-l-xl text-sm text-gray-400">
                            bora-ali.test/organizadores/
                        </span>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                            placeholder="seunome"
                            class="flex-1 px-4 py-3 rounded-r-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                              @error('username') border-red-400 @enderror">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Apenas letras, números e _ (sem espaços).
                    </p>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" rows="3" maxlength="300" placeholder="Conte um pouco sobre você ou sua produtora..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                             focus:outline-none focus:ring-2 focus:ring-indigo-500
                             text-sm resize-none @error('bio') border-red-400 @enderror">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                    <input type="url" name="website" value="{{ old('website', $user->website) }}"
                        placeholder="https://seusite.com.br"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                          @error('website') border-red-400 @enderror">
                    @error('website')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Instagram
                        </label>
                        <div class="flex items-center">
                            <span
                                class="px-3 py-3 bg-gray-50 border border-r-0 border-gray-200
                                 rounded-l-xl text-sm text-gray-400">@</span>
                            <input type="text" name="instagram" value="{{ old('instagram', $user->instagram) }}"
                                placeholder="seuperfil"
                                class="flex-1 px-4 py-3 rounded-r-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                                  @error('instagram') border-red-400 @enderror">
                        </div>
                        @error('instagram')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            WhatsApp
                        </label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}"
                            placeholder="85999990000"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm
                              @error('whatsapp') border-red-400 @enderror">
                        @error('whatsapp')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                       font-medium rounded-xl transition text-sm">
                    Salvar perfil público
                </button>
            </form>
        </div>

        {{-- Zona de perigo --}}
        <div class="bg-white rounded-2xl border border-red-100 p-6">
            <h2 class="font-semibold text-red-600 text-sm uppercase tracking-wide mb-4">
                Zona de perigo
            </h2>

            <p class="text-sm text-gray-500 mb-4">
                Ao encerrar sua conta, todos os seus dados serão removidos permanentemente.
                Esta ação não pode ser desfeita.
            </p>

            <button onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50
                       text-sm font-medium rounded-xl transition">
                Encerrar minha conta
            </button>
        </div>
    </div>

    {{-- Modal de confirmação de exclusão --}}
    <div id="delete-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center
            justify-center z-50 px-4">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full">
            <h3 class="font-bold text-gray-800 mb-2">Encerrar conta</h3>
            <p class="text-sm text-gray-500 mb-4">
                Digite sua senha para confirmar. Esta ação é irreversível.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-3">
                @csrf
                @method('DELETE')

                <input type="password" name="password" placeholder="Sua senha"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200
                          focus:outline-none focus:ring-2 focus:ring-red-400 text-sm
                          @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600
                               rounded-xl text-sm hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white
                               rounded-xl text-sm font-medium transition">
                        Encerrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @use('Illuminate\Support\Facades\Storage')
@endsection
