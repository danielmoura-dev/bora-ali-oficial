@extends('layouts.app')
@section('title', 'Política de Privacidade — Bora Ali')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 p-8">

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Política de Privacidade</h1>
        <p class="text-sm text-gray-400 mb-8">Última atualização: {{ date('d/m/Y') }}</p>

        <div class="space-y-6 text-sm text-gray-600 leading-relaxed">

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">1. Informações que Coletamos</h2>
                <p class="mb-2">Coletamos as seguintes informações quando você usa a plataforma:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong class="text-gray-700">Dados de cadastro:</strong> nome, e-mail, CPF ou CNPJ, data de nascimento e número de celular</li>
                    <li><strong class="text-gray-700">Dados de pagamento:</strong> processados diretamente pelo gateway de pagamento — não armazenamos dados de cartão</li>
                    <li><strong class="text-gray-700">Dados de uso:</strong> eventos criados, ingressos comprados e histórico de transações</li>
                    <li><strong class="text-gray-700">Dados técnicos:</strong> endereço IP, tipo de navegador e dispositivo</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">2. Como Usamos suas Informações</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Criar e gerenciar sua conta na plataforma</li>
                    <li>Processar pagamentos e emitir ingressos</li>
                    <li>Enviar confirmações de compra e lembretes de eventos</li>
                    <li>Verificar sua identidade e prevenir fraudes</li>
                    <li>Melhorar nossos serviços e experiência do usuário</li>
                    <li>Cumprir obrigações legais e regulatórias</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">3. Compartilhamento de Dados</h2>
                <p class="mb-2">Compartilhamos seus dados apenas nas seguintes situações:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong class="text-gray-700">Organizadores:</strong> nome e e-mail do comprador são compartilhados com o organizador do evento para fins de check-in</li>
                    <li><strong class="text-gray-700">Gateways de pagamento:</strong> Mercado Pago e/ou Pagar.me para processar transações</li>
                    <li><strong class="text-gray-700">Obrigação legal:</strong> quando exigido por lei ou ordem judicial</li>
                </ul>
                <p class="mt-2">Não vendemos seus dados pessoais a terceiros.</p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">4. Segurança dos Dados</h2>
                <p>
                    Adotamos medidas técnicas e organizacionais para proteger suas informações,
                    incluindo criptografia de dados sensíveis, acesso restrito às informações
                    e monitoramento de segurança. Nenhum sistema é 100% seguro, mas fazemos
                    o máximo para proteger seus dados.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">5. Cookies</h2>
                <p>
                    Utilizamos cookies essenciais para o funcionamento da plataforma,
                    como manutenção da sessão de login. Não utilizamos cookies de
                    rastreamento ou publicidade de terceiros.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">6. Seus Direitos (LGPD)</h2>
                <p class="mb-2">
                    De acordo com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018),
                    você tem direito a:
                </p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Acessar os dados que temos sobre você</li>
                    <li>Corrigir dados incorretos ou desatualizados</li>
                    <li>Solicitar a exclusão dos seus dados</li>
                    <li>Revogar consentimentos dados anteriormente</li>
                    <li>Solicitar a portabilidade dos seus dados</li>
                </ul>
                <p class="mt-2">
                    Para exercer esses direitos, entre em contato pelo e-mail:
                    <a href="mailto:privacidade@boraali.com.br"
                       class="text-indigo-600 hover:underline">
                        privacidade@boraali.com.br
                    </a>
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">7. Retenção de Dados</h2>
                <p>
                    Mantemos seus dados pelo tempo necessário para prestação dos serviços
                    e cumprimento de obrigações legais. Dados de transações financeiras
                    são mantidos por até 5 anos conforme exigência fiscal. Ao encerrar
                    sua conta, seus dados pessoais são removidos em até 30 dias,
                    exceto onde a lei exigir retenção maior.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">8. Menores de Idade</h2>
                <p>
                    Nossa plataforma não é destinada a menores de 18 anos. Não coletamos
                    intencionalmente dados de menores. Se identificarmos que coletamos
                    dados de um menor, removeremos as informações imediatamente.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">9. Alterações nesta Política</h2>
                <p>
                    Podemos atualizar esta Política periodicamente. Notificaremos sobre
                    mudanças significativas por e-mail ou mediante aviso na plataforma.
                    Recomendamos que você revise esta página regularmente.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">10. Contato</h2>
                <p>
                    Dúvidas sobre esta Política de Privacidade? Entre em contato:
                    <a href="mailto:privacidade@boraali.com.br"
                       class="text-indigo-600 hover:underline">
                        privacidade@boraali.com.br
                    </a>
                </p>
            </section>

        </div>
    </div>
</div>
@endsection