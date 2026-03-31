# Bora Ali 🎉

Plataforma SaaS de gestão de eventos regionais. Permite que organizadores criem eventos, vendam ingressos e façam check-in, com taxa fixa de **R$ 1,00 por transação** para a plataforma.

---

## Stack Técnica

| Camada | Tecnologia |
|---|---|
| Framework | Laravel 11 (PHP 8.2+) |
| Banco de dados | MySQL |
| Frontend | Tailwind CSS + JavaScript via Vite |
| Autenticação | Customizada (sem Breeze/Jetstream) + Google OAuth via Socialite |
| E-mail | Resend API |
| Pagamentos | Mercado Pago (Pix) — preparado para migrar para Pagar.me |
| Testes | PHPUnit / Pest |
| Ambiente local | Laragon + ngrok |

---

## Pré-requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL
- Laragon (Windows) ou equivalente

---

## Instalação local

### 1. Clone o repositório

```bash
git clone https://github.com/SEU_USUARIO/bora-ali.git
cd bora-ali
```

### 2. Instale as dependências

```bash
composer install
npm install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Edite o `.env` com suas credenciais:

```env
APP_NAME="Bora Ali"
APP_URL=http://bora-ali.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bora_ali
DB_USERNAME=root
DB_PASSWORD=

# Resend (e-mail)
RESEND_API_KEY=re_sua_chave

# Google OAuth
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Mercado Pago
MP_ACCESS_TOKEN=TEST-seu_token
MP_PUBLIC_KEY=TEST-sua_public_key
MP_CLIENT_ID=seu_client_id
MP_CLIENT_SECRET=seu_client_secret
MP_WEBHOOK_SECRET=seu_webhook_secret
MP_SANDBOX=true
```

### 4. Configure o banco de dados

```bash
mysql -u root -e "CREATE DATABASE bora_ali CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE bora_ali_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

### 5. Configure o banco de testes

No `phpunit.xml`, confirme:

```xml
<env name="DB_DATABASE" value="bora_ali_testing"/>
<env name="DB_USERNAME" value="root"/>
<env name="DB_PASSWORD" value=""/>
```

### 6. Compile os assets

```bash
# Desenvolvimento (com HMR)
npm run dev

# Produção
npm run build
```

### 7. Suba o servidor

```bash
php artisan serve
```

Acesse: `http://bora-ali.test` ou `http://localhost:8000`

---

## Rodando os testes

```bash
# Suite completa
php artisan test

# Módulo específico
php artisan test tests/Feature/AuthRegisterTest.php
```

---

## Configuração do ngrok (webhooks)

Para receber webhooks do Mercado Pago em desenvolvimento:

```bash
ngrok http 80
```

Registre a URL gerada no dashboard do Mercado Pago:
`https://SEU_HASH.ngrok.io/webhooks/mercadopago`

---

## Estrutura de diretórios relevante

```
app/
├── Console/Commands/       # SendEventReminders
├── Http/Controllers/
│   ├── Auth/               # Register, Login, Google, Verification
│   ├── Onboarding/         # OnboardingController (steps 2 e 3)
│   ├── Organizer/          # OrganizerDashboardController
│   └── ...                 # Event, Order, Checkin, Profile, etc.
├── Mail/                   # OrderConfirmedMail, EventReminderMail, OrderCancelledMail
├── Models/                 # User, Event, Order, OrderItem, TicketType, TicketBatch, Checkin
├── Policies/               # EventPolicy, OrderPolicy
├── Rules/                  # ValidCpf, ValidCnpj
└── Services/               # AuthService, WhatsAppService, OrderService,
                            # PaymentService, CancellationService,
                            # MercadoPagoOAuthService, CheckinService

database/
├── factories/              # UserFactory, EventFactory, OrderFactory, etc.
└── migrations/             # Todas as migrations em ordem cronológica

resources/views/
├── auth/                   # login, register, verify
├── checkin/                # index (scanner QR)
├── emails/                 # order-confirmed, event-reminder, order-cancelled
├── events/                 # create, show, my
├── layouts/                # app.blade.php, auth.blade.php
├── onboarding/             # step2, step3, step3-verify
├── orders/                 # checkout, pending, success, my-tickets
├── organizer/              # dashboard, event-sales, public
├── partials/               # event-card
└── profile/                # show

routes/
└── web.php                 # Todas as rotas agrupadas por contexto
```

---

## Variáveis de ambiente — referência completa

| Variável | Descrição |
|---|---|
| `APP_KEY` | Chave de criptografia do Laravel |
| `APP_URL` | URL base da aplicação |
| `DB_*` | Configurações do banco MySQL |
| `RESEND_API_KEY` | Chave da API do Resend para envio de e-mails |
| `GOOGLE_CLIENT_ID` | Client ID do Google OAuth |
| `GOOGLE_CLIENT_SECRET` | Secret do Google OAuth |
| `MP_ACCESS_TOKEN` | Access token do Mercado Pago (plataforma) |
| `MP_PUBLIC_KEY` | Public key do Mercado Pago (frontend) |
| `MP_CLIENT_ID` | Client ID para OAuth do organizador |
| `MP_CLIENT_SECRET` | Client Secret para OAuth do organizador |
| `MP_WEBHOOK_SECRET` | Secret para validar webhooks do MP |
| `MP_SANDBOX` | `true` em dev, `false` em produção |
| `WHATSAPP_API_URL` | URL da API de WhatsApp (CallMeBot ou similar) |
| `WHATSAPP_API_KEY` | Chave da API de WhatsApp |

---

## Cron job (produção)

Adicione ao crontab do servidor:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

O comando `events:send-reminders` roda diariamente às 09h e envia lembretes para compradores de eventos que começam no dia seguinte.

---

## Licença

Projeto privado — todos os direitos reservados.
