<div id="cookie-banner" style="display:none"
     class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-gray-900 bg-opacity-95">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center
                justify-between gap-4">
        <p class="text-sm text-gray-300 text-center sm:text-left">
            Usamos cookies essenciais e de análise para melhorar sua
            experiência. Ao continuar navegando, você concorda com nossa
            <a href="{{ route('legal.privacy') }}"
               class="text-orange-400 hover:underline">
                Política de Privacidade
            </a>.
        </p>
        <div class="flex gap-3 shrink-0">
            <button onclick="rejectCookies()"
                    class="px-4 py-2 border border-gray-600 text-gray-300
                           hover:bg-gray-800 text-sm rounded-xl transition">
                Só essenciais
            </button>
            <button onclick="acceptCookies()"
                    class="px-6 py-2 bg-orange-500 hover:bg-orange-600
                           text-white text-sm font-medium rounded-xl transition">
                Aceitar todos
            </button>
        </div>
    </div>
</div>

<script>
function acceptCookies() {
    setCookie('cookies_accepted', '1');
    document.getElementById('cookie-banner').remove();
}

function rejectCookies() {
    setCookie('cookies_accepted', 'essential');
    // Desativa o Analytics
    window['ga-disable-{{ config('services.google.analytics_id') }}'] = true;
    document.getElementById('cookie-banner').remove();
}

function setCookie(name, value) {
    const expires = new Date();
    expires.setFullYear(expires.getFullYear() + 1);
    document.cookie = name + '=' + value + '; expires='
        + expires.toUTCString() + '; path=/';
}

function getCookie(name) {
    return document.cookie.split('; ').find(r => r.startsWith(name + '='))?.split('=')[1];
}

if (!getCookie('cookies_accepted')) {
    document.getElementById('cookie-banner').style.display = '';
}
</script>