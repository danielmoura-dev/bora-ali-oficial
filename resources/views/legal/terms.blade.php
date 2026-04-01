@extends('layouts.app')
@section('title', 'Termos de Uso — Bora Ali')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 p-8">

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Termos de Uso</h1>
        <p class="text-sm text-gray-400 mb-8">Última atualização: {{ date('d/m/Y') }}</p>

        <div class="prose prose-gray max-w-none space-y-6 text-sm text-gray-600 leading-relaxed">

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">1. Aceitação dos Termos</h2>
                <p>
                    Ao acessar ou usar a plataforma Bora Ali, você concorda em cumprir e estar
                    vinculado a estes Termos de Uso. Se você não concordar com qualquer parte
                    destes termos, não poderá acessar ou usar nossos serviços.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">2. Descrição do Serviço</h2>
                <p>
                    A Bora Ali é uma plataforma de gestão de eventos que permite que organizadores
                    criem eventos, vendam ingressos e gerenciem participantes. Compradores podem
                    adquirir ingressos para eventos publicados na plataforma.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">3. Cadastro e Conta</h2>
                <p>Para usar a plataforma, você deve:</p>
                <ul class="list-disc pl-5 space-y-1 mt-2">
                    <li>Ter pelo menos 18 anos de idade</li>
                    <li>Fornecer informações verdadeiras, precisas e completas</li>
                    <li>Manter suas informações de cadastro atualizadas</li>
                    <li>Manter a segurança da sua senha e não compartilhá-la</li>
                    <li>Ser responsável por todas as atividades realizadas em sua conta</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">4. Responsabilidades do Organizador</h2>
                <p>Ao criar um evento na plataforma, o organizador declara que:</p>
                <ul class="list-disc pl-5 space-y-1 mt-2">
                    <li>Tem autorização legal para realizar o evento</li>
                    <li>As informações do evento são verdadeiras e precisas</li>
                    <li>É responsável pela realização do evento e pelo atendimento aos participantes</li>
                    <li>Cumprirá todas as leis e regulamentações aplicáveis</li>
                    <li>Em caso de cancelamento, notificará os compradores e providenciará reembolsos</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">5. Taxa da Plataforma</h2>
                <p>
                    A Bora Ali cobra uma taxa de <strong class="text-gray-800">R$ 1,00 (um real) por ingresso vendido</strong>.
                    Esta taxa é retida automaticamente no momento do pagamento e é não reembolsável,
                    exceto em casos de cancelamento do evento pelo organizador.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">6. Pagamentos e Reembolsos</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Os pagamentos são processados por meio de gateways seguros de terceiros</li>
                    <li>Reembolsos por cancelamento do comprador estão sujeitos à política do evento</li>
                    <li>Em caso de cancelamento pelo organizador, todos os compradores serão reembolsados</li>
                    <li>O prazo de reembolso pode variar conforme o método de pagamento utilizado</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">7. Conteúdo Proibido</h2>
                <p>É proibido criar eventos ou publicar conteúdo que:</p>
                <ul class="list-disc pl-5 space-y-1 mt-2">
                    <li>Seja ilegal ou promova atividades ilegais</li>
                    <li>Seja discriminatório, ofensivo ou prejudicial</li>
                    <li>Viole direitos de propriedade intelectual de terceiros</li>
                    <li>Contenha informações falsas ou enganosas</li>
                    <li>Seja de natureza fraudulenta</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">8. Limitação de Responsabilidade</h2>
                <p>
                    A Bora Ali atua como intermediária entre organizadores e compradores.
                    Não nos responsabilizamos pela qualidade, segurança ou realização dos eventos,
                    nem por danos diretos ou indiretos decorrentes do uso da plataforma ou da
                    participação em eventos.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">9. Cancelamento de Conta</h2>
                <p>
                    Você pode cancelar sua conta a qualquer momento através das configurações
                    do perfil. A Bora Ali reserva-se o direito de suspender ou encerrar contas
                    que violem estes Termos de Uso, sem aviso prévio.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">10. Alterações nos Termos</h2>
                <p>
                    Podemos atualizar estes termos periodicamente. Notificaremos sobre mudanças
                    significativas por e-mail ou mediante aviso na plataforma. O uso continuado
                    após as alterações constitui aceitação dos novos termos.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-gray-800 mb-2">11. Contato</h2>
                <p>
                    Dúvidas sobre estes Termos de Uso? Entre em contato conosco pelo e-mail:
                    <a href="mailto:contato@boraali.com.br"
                       class="text-indigo-600 hover:underline">
                        contato@boraali.com.br
                    </a>
                </p>
            </section>

        </div>
    </div>
</div>
@endsection