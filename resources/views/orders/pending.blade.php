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

    {{-- Status e pedido --}}
    <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 mb-6">
        <p class="text-sm text-yellow-800 font-medium">⏳ Aguardando pagamento</p>
        <p class="text-xs text-yellow-700 mt-1">
            Assim que o pagamento for confirmado, seu ingresso estará disponível em
            <strong>Meus Ingressos</strong>.
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
</script>
@endsection