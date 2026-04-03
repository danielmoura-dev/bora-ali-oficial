@extends('layouts.app')
@section('title', 'Criar Evento — Bora Ali')

@section('content')
    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Criar novo evento</h1>
            <p class="text-sm text-gray-500 mt-1">
                Preencha os dados do seu evento. Você poderá revisá-lo antes de publicar.
            </p>
        </div>

        <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Seção 1 — Informações básicas --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Informações básicas
                </h2>

                {{-- Nome do evento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nome do evento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        placeholder="Ex: Festival de Verão 2025"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                              @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Imagem de divulgação --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Imagem de divulgação <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center
                            hover:border-orange-400 transition cursor-pointer"
                        onclick="document.getElementById('cover_image').click()">
                        <div id="preview-wrapper" class="hidden mb-3">
                            <img id="cover-preview" class="max-h-48 mx-auto rounded-lg object-cover">
                        </div>
                        <div id="upload-placeholder">
                            <div class="text-3xl mb-2">🖼️</div>
                            <p class="text-sm text-gray-500">Clique para escolher uma imagem</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WebP — máx. 3MB</p>
                        </div>
                    </div>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*" class="hidden">
                    @error('cover_image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categoria --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Categoria <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                               focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                               @error('category_id') border-red-400 @enderror">
                        <option value="">Selecione uma categoria</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                data-slug="{{ $category->slug }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Seção 2 — Data e horário --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Data e horário
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data de início <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="starts_at" id="starts_at"
                            value="{{ old('starts_at') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                                  @error('starts_at') border-red-400 @enderror">
                        @error('starts_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data de término <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="ends_at" id="ends_at"
                            value="{{ old('ends_at') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                                  @error('ends_at') border-red-400 @enderror">
                        @error('ends_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Duração calculada --}}
                <div id="duration-display" class="hidden text-sm text-orange-600 font-medium py-2 px-3
                    bg-orange-50 rounded-xl border border-orange-100">
                </div>
            </div>

            {{-- Seção 3 — Campos dinâmicos da categoria --}}
            <div id="category-fields-container" class="hidden bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Detalhes da categoria
                </h2>
                <div id="category-fields-inner" class="space-y-4"></div>
            </div>

            {{-- Seção 4 — Descrição do evento --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Descrição do evento
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descrição do evento <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="6"
                        placeholder="Conte todos os detalhes do seu evento, como a programação, atrações, regras e informações importantes..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                                 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm resize-none
                                 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Seção 5 — Onde o evento vai acontecer --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Onde o evento vai acontecer
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nome do local <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="venue_name" value="{{ old('venue_name') }}"
                        placeholder="Ex: Arena Castelão"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                              @error('venue_name') border-red-400 @enderror">
                    @error('venue_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Endereço completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="venue_address" value="{{ old('venue_address') }}"
                        placeholder="Ex: Av. Alberto Craveiro, 2901"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                              @error('venue_address') border-red-400 @enderror">
                    @error('venue_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cidade <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Fortaleza"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                                  @error('city') border-red-400 @enderror">
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select name="state"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm
                                   @error('state') border-red-400 @enderror">
                            <option value="">UF</option>
                            @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                <option value="{{ $uf }}" {{ old('state') === $uf ? 'selected' : '' }}>
                                    {{ $uf }}
                                </option>
                            @endforeach
                        </select>
                        @error('state')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Seção 6 — Ingressos --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Ingressos
                </h2>

                {{-- Toggle gratuito / pago --}}
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                    <button type="button" id="btn-free"
                        onclick="setIsFree(true)"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium transition
                               bg-green-600 text-white">
                        Gratuito
                    </button>
                    <button type="button" id="btn-paid"
                        onclick="setIsFree(false)"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium transition
                               bg-gray-200 text-gray-600">
                        Pago
                    </button>
                    <input type="hidden" name="is_free" id="is_free" value="{{ old('is_free', '1') }}">
                </div>

                {{-- Opções de evento pago --}}
                <div id="paid-options" class="space-y-4 hidden">

                    {{-- Taxa de serviço --}}
                    <div class="p-4 border border-gray-200 rounded-xl space-y-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="absorb_service_fee" id="absorb_service_fee"
                                value="1" {{ old('absorb_service_fee') ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-orange-500 rounded border-gray-300">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Absorver taxa de serviço</p>
                                <p id="fee-description" class="text-xs text-gray-500 mt-0.5">
                                    A taxa de R$ 1,00 será exibida separadamente ao comprador no momento da compra.
                                </p>
                            </div>
                        </label>
                    </div>

                    {{-- Nomenclatura do ingresso --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Como chamar o ingresso?
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="ticket_nomenclature" value="ingresso"
                                    {{ old('ticket_nomenclature', 'ingresso') === 'ingresso' ? 'checked' : '' }}
                                    class="text-orange-500">
                                <span class="text-sm text-gray-700">🎫 Ingresso</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="ticket_nomenclature" value="inscricao"
                                    {{ old('ticket_nomenclature') === 'inscricao' ? 'checked' : '' }}
                                    class="text-orange-500">
                                <span class="text-sm text-gray-700">📋 Inscrição</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            Afeta botões, e-mails e textos do evento.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Seção 7 — Configuração de pagamento --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                    Configuração de pagamento
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Como os pagamentos serão recebidos?
                    </label>

                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-4 border-2 border-gray-100
                          rounded-xl cursor-pointer hover:border-orange-300
                          has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 transition">
                            <input type="radio" name="payment_mode" value="direct"
                                class="mt-0.5 text-orange-500" checked>
                            <div>
                                <p class="font-medium text-sm text-gray-800">Direto na plataforma</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Não precisa de conta no Mercado Pago.
                                    A Bora Ali recebe e repassa para você.
                                </p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 border-2 border-gray-100
                          rounded-xl cursor-pointer hover:border-orange-300
                          has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 transition">
                            <input type="radio" name="payment_mode" value="split"
                                class="mt-0.5 text-orange-500">
                            <div>
                                <p class="font-medium text-sm text-gray-800">Split automático</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Você recebe direto na sua conta do Mercado Pago
                                    após cada venda. Requer conta conectada.
                                </p>
                            </div>
                        </label>
                    </div>

                    @error('payment_mode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" name="payment_provider" value="mercadopago">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Métodos aceitos
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="payment_methods[]" value="pix" checked
                            class="text-orange-500 rounded">
                        <span class="text-sm text-gray-700">Pix</span>
                        <span class="text-xs text-gray-400">(único disponível no momento)</span>
                    </label>
                </div>

                <div id="split-warning"
                    class="hidden p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-800">
                    Para usar o split automático, você precisa conectar sua conta do
                    Mercado Pago em
                    <a href="{{ route('profile.show') }}" class="underline font-medium">Meu Perfil</a>.
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}"
                    class="px-6 py-3 text-sm text-gray-600 hover:text-gray-800 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white
                           font-medium rounded-xl transition text-sm">
                    Salvar rascunho
                </button>
            </div>
        </form>
    </div>

    <script>
        // ── Preview de imagem ────────────────────────────────────
        document.getElementById('cover_image').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('cover-preview').src = e.target.result;
                document.getElementById('preview-wrapper').classList.remove('hidden');
                document.getElementById('upload-placeholder').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });

        // ── Toggle gratuito / pago ───────────────────────────────
        function setIsFree(free) {
            document.getElementById('is_free').value = free ? '1' : '0';

            const btnFree = document.getElementById('btn-free');
            const btnPaid = document.getElementById('btn-paid');
            const paidOptions = document.getElementById('paid-options');

            if (free) {
                btnFree.classList.replace('bg-gray-200', 'bg-green-600');
                btnFree.classList.replace('text-gray-600', 'text-white');
                btnPaid.classList.replace('bg-orange-500', 'bg-gray-200');
                btnPaid.classList.replace('text-white', 'text-gray-600');
                paidOptions.classList.add('hidden');
            } else {
                btnPaid.classList.replace('bg-gray-200', 'bg-orange-500');
                btnPaid.classList.replace('text-gray-600', 'text-white');
                btnFree.classList.replace('bg-green-600', 'bg-gray-200');
                btnFree.classList.replace('text-white', 'text-gray-600');
                paidOptions.classList.remove('hidden');
            }
        }

        // Inicializa estado correto
        const initialFree = document.getElementById('is_free').value === '1';
        setIsFree(initialFree);

        // ── Taxa: texto dinâmico ─────────────────────────────────
        document.getElementById('absorb_service_fee').addEventListener('change', function () {
            const desc = document.getElementById('fee-description');
            if (this.checked) {
                desc.textContent = 'A taxa de R$ 1,00 por ingresso será incluída no preço final. O comprador não verá a taxa separada.';
            } else {
                desc.textContent = 'A taxa de R$ 1,00 será exibida separadamente ao comprador no momento da compra.';
            }
        });

        // ── Duração calculada ────────────────────────────────────
        function calcDuration() {
            const startVal = document.getElementById('starts_at').value;
            const endVal   = document.getElementById('ends_at').value;
            const display  = document.getElementById('duration-display');

            if (!startVal || !endVal) {
                display.classList.add('hidden');
                return;
            }

            const start = new Date(startVal);
            const end   = new Date(endVal);
            const diffMs = end - start;

            if (diffMs <= 0) {
                display.classList.add('hidden');
                return;
            }

            const totalHours = Math.floor(diffMs / (1000 * 60 * 60));
            const days  = Math.floor(totalHours / 24);
            const hours = totalHours % 24;

            let text = 'Seu evento vai durar: ';
            if (days > 0 && hours > 0) {
                text += `${days} dia${days > 1 ? 's' : ''} e ${hours} hora${hours > 1 ? 's' : ''}`;
            } else if (days > 0) {
                text += `${days} dia${days > 1 ? 's' : ''}`;
            } else {
                text += `${hours} hora${hours > 1 ? 's' : ''}`;
            }

            display.textContent = text;
            display.classList.remove('hidden');
        }

        document.getElementById('starts_at').addEventListener('change', calcDuration);
        document.getElementById('ends_at').addEventListener('change', calcDuration);

        // ── Campos dinâmicos da categoria ────────────────────────
        const categorySelect = document.getElementById('category_id');

        async function loadCategoryFields(slug) {
            const container = document.getElementById('category-fields-container');
            const inner     = document.getElementById('category-fields-inner');

            if (!slug) {
                container.classList.add('hidden');
                inner.innerHTML = '';
                return;
            }

            try {
                const res  = await fetch(`/api/categories/${slug}/fields`);
                const data = await res.json();

                if (!data.fields || data.fields.length === 0) {
                    container.classList.add('hidden');
                    inner.innerHTML = '';
                    return;
                }

                inner.innerHTML = data.fields.map(field => renderField(field)).join('');
                container.classList.remove('hidden');
            } catch (e) {
                container.classList.add('hidden');
                inner.innerHTML = '';
            }
        }

        function renderField(field) {
            const required = field.required ? 'required' : '';
            const reqMark  = field.required ? '<span class="text-red-500">*</span>' : '';
            const placeholder = field.placeholder ? `placeholder="${field.placeholder}"` : '';
            const inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm';

            let input = '';

            if (field.type === 'textarea') {
                input = `<textarea name="fields[${field.name}]" rows="3" ${placeholder} ${required}
                    class="${inputClass} resize-none"></textarea>`;
            } else if (field.type === 'select' && field.options) {
                const opts = field.options.map(o => `<option value="${o}">${o}</option>`).join('');
                input = `<select name="fields[${field.name}]" ${required} class="${inputClass}">
                    <option value="">Selecione...</option>${opts}</select>`;
            } else if (field.type === 'checkbox') {
                input = `<label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="fields[${field.name}]" value="1" ${required}
                        class="w-4 h-4 text-orange-500 rounded border-gray-300">
                    <span class="text-sm text-gray-700">${field.label}</span>
                </label>`;
                return `<div>${input}</div>`;
            } else {
                const type = field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : 'text');
                input = `<input type="${type}" name="fields[${field.name}]"
                    ${placeholder} ${required} class="${inputClass}">`;
            }

            return `<div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    ${field.label} ${reqMark}
                </label>
                ${input}
            </div>`;
        }

        categorySelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const slug = selected ? selected.dataset.slug : null;
            loadCategoryFields(slug);
        });

        // Carrega campos se houver categoria pré-selecionada (old() no retorno de erro)
        (function () {
            const selected = categorySelect.options[categorySelect.selectedIndex];
            if (selected && selected.dataset.slug) {
                loadCategoryFields(selected.dataset.slug);
            }
        })();

        // ── Split warning ────────────────────────────────────────
        document.querySelectorAll('input[name="payment_mode"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('split-warning').classList.toggle('hidden', this.value !== 'split');
            });
        });
    </script>
@endsection
