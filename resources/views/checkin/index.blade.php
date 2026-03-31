@extends('layouts.app')
@section('title', 'Check-in — ' . $event->title)

@section('content')
<div class="max-w-lg mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('organizer.dashboard') }}"
           class="text-sm text-indigo-600 hover:underline">
            ← Painel
        </a>
        <h1 class="text-xl font-bold text-gray-800 mt-2">Check-in</h1>
        <p class="text-sm text-gray-500 truncate">{{ $event->title }}</p>
    </div>

    {{-- Stats --}}
    <div id="stats-bar"
         class="bg-white rounded-2xl border border-gray-100 p-4 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Check-ins realizados</span>
            <span class="text-sm font-bold text-gray-800">
                <span id="stat-checked">0</span> / <span id="stat-total">0</span>
            </span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div id="stat-bar"
                 class="bg-indigo-500 h-2 rounded-full transition-all duration-500"
                 style="width: 0%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1">
            <span id="stat-remaining">0</span> restantes
        </p>
    </div>

    {{-- Scanner de câmera --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
        <h2 class="font-semibold text-gray-700 text-sm mb-3">📷 Escanear QR Code</h2>

        <div id="camera-container"
             class="relative bg-gray-900 rounded-xl overflow-hidden aspect-square mb-3">
            <video id="camera-feed"
                   class="w-full h-full object-cover"
                   playsinline></video>

            {{-- Mira --}}
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-48 h-48 border-2 border-white rounded-xl opacity-60"></div>
            </div>

            {{-- Overlay de resultado --}}
            <div id="scan-overlay"
                 class="hidden absolute inset-0 flex items-center justify-center
                        bg-black bg-opacity-70">
                <div id="scan-result-icon" class="text-6xl"></div>
            </div>
        </div>

        <button id="camera-btn" onclick="toggleCamera()"
                class="w-full py-2.5 bg-gray-800 hover:bg-gray-900 text-white
                       text-sm font-medium rounded-xl transition">
            Ativar câmera
        </button>
    </div>

    {{-- Input manual --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
        <h2 class="font-semibold text-gray-700 text-sm mb-3">⌨️ Digitar código</h2>

        <div class="flex gap-2">
            <input type="text" id="manual-code"
                   placeholder="XXXX-XXXX"
                   maxlength="9"
                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200
                          focus:outline-none focus:ring-2 focus:ring-indigo-500
                          text-sm font-mono uppercase tracking-widest">
            <button onclick="submitManual()"
                    class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                           font-medium rounded-xl transition text-sm">
                OK
            </button>
        </div>
    </div>

    {{-- Resultado do último scan --}}
    <div id="result-card" class="hidden rounded-2xl p-5 mb-4 transition-all"></div>

    {{-- Histórico de check-ins --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm mb-3">Últimos check-ins</h2>
        <div id="checkin-log" class="space-y-2 max-h-64 overflow-y-auto">
            <p class="text-xs text-gray-400 text-center py-4">
                Nenhum check-in ainda.
            </p>
        </div>
    </div>
</div>

{{-- QR Scanner library --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
const scanUrl  = '{{ route('checkin.scan', $event->slug) }}';
const statsUrl = '{{ route('checkin.stats', $event->slug) }}';
const csrf     = '{{ csrf_token() }}';

let scanner    = null;
let cameraOn   = false;
let processing = false;
const log      = [];

// ── Stats ─────────────────────────────────────────────────────

async function loadStats() {
    try {
        const res  = await fetch(statsUrl);
        const data = await res.json();
        document.getElementById('stat-checked').textContent   = data.checked_in;
        document.getElementById('stat-total').textContent     = data.total;
        document.getElementById('stat-remaining').textContent = data.remaining;
        document.getElementById('stat-bar').style.width       = data.percentage + '%';
    } catch (e) {}
}

loadStats();
setInterval(loadStats, 15000);

// ── Scan ──────────────────────────────────────────────────────

async function processCode(code) {
    if (processing) return;
    processing = true;

    code = code.toUpperCase().trim();

    try {
        const res  = await fetch(scanUrl, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  csrf,
            },
            body: JSON.stringify({ ticket_code: code }),
        });

        const data = await res.json();
        showResult(data, code);
        loadStats();

    } catch (e) {
        showResult({ status: 'error', message: 'Erro de conexão.' }, code);
    } finally {
        setTimeout(() => { processing = false; }, 2000);
    }
}

function showResult(data, code) {
    const card    = document.getElementById('result-card');
    const overlay = document.getElementById('scan-overlay');
    const icon    = document.getElementById('scan-result-icon');

    const config = {
        success:            { bg: 'bg-green-50 border border-green-200', icon: '✅', textColor: 'text-green-800' },
        already_checked_in: { bg: 'bg-yellow-50 border border-yellow-200', icon: '⚠️', textColor: 'text-yellow-800' },
        not_found:          { bg: 'bg-red-50 border border-red-200', icon: '❌', textColor: 'text-red-800' },
        wrong_event:        { bg: 'bg-red-50 border border-red-200', icon: '🚫', textColor: 'text-red-800' },
        error:              { bg: 'bg-gray-50 border border-gray-200', icon: '⚡', textColor: 'text-gray-800' },
    };

    const c = config[data.status] ?? config.error;

    // Card de resultado
    card.className = `rounded-2xl p-5 mb-4 ${c.bg}`;
    card.classList.remove('hidden');
    card.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="text-3xl">${c.icon}</span>
            <div>
                <p class="font-semibold ${c.textColor} text-sm">${data.message}</p>
                ${data.buyer ? `<p class="text-xs text-gray-600 mt-0.5">${data.buyer.name} · ${data.ticket_type}</p>` : ''}
                ${data.checked_in_at ? `<p class="text-xs text-gray-500 mt-0.5">Entrou em: ${data.checked_in_at}</p>` : ''}
                <p class="text-xs text-gray-400 font-mono mt-0.5">${code}</p>
            </div>
        </div>
    `;

    // Overlay na câmera
    if (cameraOn) {
        icon.textContent = c.icon;
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('hidden'), 1500);
    }

    // Log
    if (data.status === 'success') {
        addToLog(code, data.buyer?.name, data.ticket_type);
    }

    // Auto-scroll para o card
    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function addToLog(code, name, type) {
    log.unshift({ code, name, type, time: new Date() });

    const container = document.getElementById('checkin-log');
    container.innerHTML = log.slice(0, 20).map(entry => `
        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-700">${entry.name ?? '—'}</p>
                <p class="text-xs text-gray-400">${entry.type ?? ''} · ${entry.code}</p>
            </div>
            <p class="text-xs text-gray-300">${entry.time.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}</p>
        </div>
    `).join('');
}

// ── Câmera ────────────────────────────────────────────────────

async function toggleCamera() {
    const btn = document.getElementById('camera-btn');

    if (cameraOn) {
        if (scanner) await scanner.stop();
        scanner = null;
        cameraOn = false;
        btn.textContent = 'Ativar câmera';
        document.getElementById('camera-feed').srcObject = null;
        return;
    }

    try {
        scanner = new Html5Qrcode('camera-feed');
        await scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 200, height: 200 } },
            async (decodedText) => {
                await processCode(decodedText);
            },
            () => {}
        );
        cameraOn = true;
        btn.textContent = 'Desativar câmera';
    } catch (e) {
        alert('Não foi possível acessar a câmera. Use o input manual.');
    }
}

// ── Input manual ──────────────────────────────────────────────

function submitManual() {
    const input = document.getElementById('manual-code');
    const code  = input.value.trim();

    if (code.length < 4) return;

    processCode(code);
    input.value = '';
    input.focus();
}

document.getElementById('manual-code').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') submitManual();
});

// Máscara do input: XXXX-XXXX
document.getElementById('manual-code').addEventListener('input', function () {
    let v = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 8);
    if (v.length > 4) v = v.slice(0, 4) + '-' + v.slice(4);
    this.value = v;
});
</script>
@endsection