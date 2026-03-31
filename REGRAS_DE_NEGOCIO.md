# Bora Ali — Regras de Negócio

Documento de referência para desenvolvedores. Descreve como o sistema funciona, as decisões de design e as regras que governam cada módulo.

---

## 1. Usuários e Onboarding

### Registro
- Usuário pode se registrar via **e-mail/senha** ou **Google OAuth**.
- Ao registrar por e-mail, um código de 6 dígitos é enviado via Resend. O código expira em **15 minutos**.
- Ao registrar via Google, o e-mail é considerado automaticamente verificado.
- A senha deve ter no mínimo 8 caracteres, com letras maiúsculas e números (`Password::min(8)->mixedCase()->numbers()`).

### Onboarding em 3 passos
Todo usuário novo passa por 3 etapas obrigatórias antes de acessar a plataforma completa. O campo `onboarding_step` controla a etapa atual.

| Step | O que é feito | Valor após concluir |
|---|---|---|
| 1 | Verificação de e-mail | `onboarding_step = 2` |
| 2 | Seleção de perfil (CPF ou CNPJ) | `onboarding_step = 3` |
| 3 | Cadastro e verificação de celular | `onboarding_step = 4` |

O middleware `EnsureOnboardingComplete` intercepta todas as rotas autenticadas e redireciona o usuário para o passo correto se o onboarding não estiver completo.

### Perfil CPF vs CNPJ
- **CPF**: requer nome completo, CPF válido (algoritmo de dígitos verificadores) e data de nascimento.
- **CNPJ**: requer razão social e CNPJ válido (algoritmo de dígitos verificadores).
- O documento é salvo **apenas com dígitos** (sem pontuação) para consistência nas buscas.

### Celular
- O número é normalizado para o formato internacional: `55` + DDD + número (ex: `5585999991234`).
- A verificação é feita via WhatsApp: um código de 6 dígitos é gerado e armazenado no **cache** (não no banco) com TTL de 10 minutos.
- Um número só pode estar associado a uma conta verificada por vez.

---

## 2. Eventos

### Ciclo de vida
```
draft → published → finished (automático pela data) / cancelled
```

- Todo evento nasce como `draft`.
- O organizador deve publicar manualmente (`status = published`).
- Eventos com `ends_at < now()` são considerados `finished` (verificado no model, não há coluna separada).
- O organizador pode cancelar um evento publicado, o que notifica todos os compradores.

### Slugs
- Gerados automaticamente a partir do título via `Str::slug()`.
- Em caso de colisão, um sufixo numérico é adicionado: `show-de-rock`, `show-de-rock-1`, etc.

### Home page
- **Eventos atuais**: `starts_at >= now()` OU (`starts_at <= now()` E `ends_at >= now()`).
- **Eventos encerrados**: `ends_at < now()`.
- Apenas eventos `published` aparecem publicamente.
- A busca usa `LIKE` nas colunas `title`, `description`, `city` e `venue_name`.

---

## 3. Ingressos

### Estrutura hierárquica
```
Event
  └── TicketType (ex: Inteira, Meia, VIP)
        └── TicketBatch (ex: 1º Lote, 2º Lote)
```

- Um evento pode ter múltiplos tipos de ingresso.
- Cada tipo pode ter múltiplos lotes com preços e quantidades diferentes.
- O **lote ativo** é o primeiro lote com `is_active = true`, `quantity_sold < quantity`, e dentro do período de venda (`starts_at <= now <= ends_at`).

### Preços
- Todos os preços são armazenados em **centavos** (integer) para evitar problemas de ponto flutuante.
- Exemplo: R$ 50,00 → `5000` no banco.
- O Mercado Pago recebe o valor em **reais** (`price / 100`).

---

## 4. Pedidos e Pagamentos

### Taxa da plataforma
- **R$ 1,00 por ingresso** (100 centavos), independente do valor do ingresso.
- Calculado como `PLATFORM_FEE_CENTS × quantidade_total_de_itens`.
- Exemplo: 3 ingressos de R$ 50 → subtotal R$ 150 + taxa R$ 3 = **total R$ 153**.

### Fluxo de compra
```
1. Usuário seleciona ingressos na página do evento
2. POST /eventos/{slug}/pedido → cria Order com status pending
3. Redireciona para /pedidos/{reference}/checkout
4. Usuário escolhe Pix → POST /pedidos/{reference}/pagar
5. Mercado Pago gera QR Code → Order salva payment_id
6. Tela de pending com polling a cada 5s
7. Webhook do MP confirma → OrderService.confirmPayment()
8. Order status = paid, ticket_codes gerados, estoque decrementado
9. E-mail de confirmação enviado ao comprador
```

### Concorrência
- O `TicketBatch` é buscado com `lockForUpdate()` dentro de uma `DB::transaction()` durante a criação do pedido.
- Isso garante que dois usuários simultâneos não comprem mais ingressos do que o disponível.

### Código do ingresso
- Gerado após confirmação do pagamento: `XXXX-XXXX` (8 caracteres alfanuméricos aleatórios).
- Único no sistema (verificado via loop com `unique` na tabela).

### Split de pagamento
- Quando o organizador conecta sua conta do Mercado Pago via OAuth, os campos `mp_access_token` e `mp_user_id` são salvos no `users`.
- O `application_fee` no payload do Pix corresponde à taxa da plataforma.
- Se o organizador não tiver MP conectado, o pagamento funciona normalmente mas sem split automático.

---

## 5. Check-in

### Regras
- Cada código de ingresso (`ticket_code`) só pode fazer check-in **uma vez** (garantido por `unique` na tabela `checkins`).
- O ingresso deve pertencer ao evento onde o check-in está sendo feito.
- Somente o organizador do evento (ou operadores por ele autorizados) pode fazer check-in.

### Respostas da API de scan

| Status HTTP | Código | Significado |
|---|---|---|
| 200 | `success` | Check-in realizado com sucesso |
| 409 | `already_checked_in` | Ingresso já utilizado |
| 404 | `not_found` | Código não existe |
| 422 | `wrong_event` | Ingresso de outro evento |

---

## 6. Cancelamento e Reembolso

### Cancelamento pelo comprador
- Permitido apenas se: `status = paid` E `event.ends_at > now()`.
- Não é possível cancelar após o evento terminar.
- O estoque do lote é devolvido (`quantity_sold` decrementado).
- E-mail de cancelamento enviado automaticamente.

### Reembolso pelo organizador
- O organizador pode reembolsar qualquer pedido `paid` individualmente.
- Em produção, aciona a API de reembolso do Mercado Pago.
- Status do pedido vai para `refunded`.

### Cancelamento do evento
- O organizador cancela o evento inteiro.
- Todos os pedidos `paid` e `pending` são cancelados.
- E-mail enviado para cada comprador.
- Estoque de todos os lotes é devolvido.

---

## 7. Notificações por E-mail

| Gatilho | E-mail enviado |
|---|---|
| Pagamento confirmado | `OrderConfirmedMail` com código(s) do ingresso |
| Evento começa amanhã | `EventReminderMail` com detalhes e código(s) |
| Pedido cancelado/reembolsado | `OrderCancelledMail` |

O comando `events:send-reminders` roda diariamente às 09h via scheduler e envia lembretes para todos os compradores de eventos que começam entre 23h e 25h a partir do momento de execução.

---

## 8. Perfil Público do Organizador

- Todo usuário tem uma página pública acessível em `/organizadores/{username}`.
- Se o usuário não tiver username definido, a URL usa o ID: `/organizadores/id/{id}`.
- Ao definir um username, acessar `/organizadores/id/{id}` redireciona para `/organizadores/{username}`.
- O username aceita apenas `[a-zA-Z0-9_]`, mínimo 3 e máximo 30 caracteres.
- A página exibe apenas eventos `published` do organizador.

---

## 9. Decisões técnicas importantes

### Por que senhas são nullable?
Usuários que se registram via Google nunca definem senha. A coluna é `nullable` para suportar os dois fluxos sem criar uma coluna separada.

### Por que o código de verificação fica no `$hidden`?
Para não vazar em respostas JSON ou logs de debug. O acesso é feito via `$user->getAttributes()['verification_code']` quando necessário.

### Por que o código do WhatsApp vai para o cache e não para o banco?
É um dado temporário com TTL de 10 minutos. O cache gerencia a expiração automaticamente, evitando poluir a tabela de usuários com dados transitórios.

### Por que os preços são em centavos?
Ponto flutuante (float/double) em linguagens de programação não representa valores monetários com precisão exata. Usar inteiros em centavos elimina erros de arredondamento.

### Por que o Mercado Pago e não o Pagar.me inicialmente?
O Pagar.me requer CNPJ para criar conta. O Mercado Pago permite sandbox sem CNPJ. A arquitetura isola toda a lógica de gateway no `PaymentService`, facilitando a troca futura sem alterar controllers ou testes.
