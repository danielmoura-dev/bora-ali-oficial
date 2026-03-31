@extends('layouts.app')
@section('title', 'Aguardando pagamento — Bora Ali')

@section('content')
<div class="max-w-md mx-auto text-center">
    <div class="text-5xl mb-4">⚡</div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Pague com Pix</h1>
    <p class="text-gray-500 text-sm mb-6">
        Escaneie o QR Code ou copie o código. Seu ingresso será confirmado automaticamente.
    </p>

    {{-- QR Code --}}
    @if(!empty($result['pix_qrcode']))
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4 inline-block">
            <img src="data:image/png;base64,{{ $result['pix_qrcode'] }}"
                 alt="QR Code Pix"
                 class="w-52 h-52 mx-auto">
        </div>
    @endif

    {{-- Copia e cola --}}
    @if(!empty($result['pix_copy_paste']))
        <div class="bg-gray-50 rounded-2xl border border-gray-100 p-4 mb-6 text-left">
            <p class="text-xs text-gray-500 mb-2 font-medium">Pix copia e cola:</p>
            <p class="font-mono text-xs text-gray-700 break-all leading-relaxed"
               id="pix-code">{{ $result['pix_copy_paste'] }}</p>
            <button onclick="copyPix()"
                    id="copy-btn"
                    class="mt-3 w-full py-2 bg-gray-800 hover:bg-gray-900 text-white
                           text-sm font-medium rounded-xl transition">
                Copiar código
            </button>
        </div>
    @endif

    {{-- Status dinâmico --}}
    <div id="status-box"
         class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-center gap-2">
            <svg id="spinner" class="animate-spin w-4 h-4 text-yellow-600"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p class="text-sm text-yellow-800 font-medium" id="status-text">
                Aguardando pagamento...
            </p>
        </div>
        <p class="text-xs text-yellow-700 mt-1">
            Verificando automaticamente a cada 5 segundos.
        </p>
    </div>

    <p class="text-xs text-gray-400 mb-4">Pedido: {{ $order->reference }}</p>

    <div class="flex flex-col gap-2">
        <a href="{{ route('tickets.my') }}"
           class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                  font-medium rounded-xl transition text-sm">
            Verificar meus ingressos
        </a>
        <a href="{{ route('home') }}"
           class="px-6 py-3 text-gray-500 hover:text-gray-700 text-sm transition">
            Voltar ao início
        </a>
    </div>
</div>

<script>
function copyPix() {
    const code = document.getElementById('pix-code')?.textContent?.trim();
    if (!code) return;
    navigator.clipboard.writeText(code).then(() => {
        const btn = document.getElementById('copy-btn');
        btn.textContent = '✓ Copiado!';
        btn.classList.replace('bg-gray-800', 'bg-green-600');
        setTimeout(() => {
            btn.textContent = 'Copiar código';
            btn.classList.replace('bg-green-600', 'bg-gray-800');
        }, 2000);
    });
}

// Polling — verifica status a cada 5 segundos
const statusUrl  = '{{ route('orders.status', $order->reference) }}';
const successUrl = '{{ route('orders.success', $order->reference) }}';
let attempts     = 0;
const maxAttempts = 36; // 3 minutos

const interval = setInterval(async () => {
    attempts++;

    if (attempts >= maxAttempts) {
        clearInterval(interval);
        document.getElementById('status-text').textContent =
            'Tempo esgotado. Verifique seus ingressos.';
        document.getElementById('spinner').classList.add('hidden');
        return;
    }

    try {
        const res  = await fetch(statusUrl, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.paid) {
            clearInterval(interval);
            document.getElementById('status-box').className =
                'bg-green-50 border border-green-200 rounded-xl p-4 mb-6';
            document.getElementById('status-text').textContent =
                '✅ Pagamento confirmado! Redirecionando...';
            document.getElementById('spinner').classList.add('hidden');

            setTimeout(() => {
                window.location.href = successUrl;
            }, 1500);
        }
    } catch (e) {
        console.error('Erro ao verificar status:', e);
    }
}, 5000);
</script>
@endsection