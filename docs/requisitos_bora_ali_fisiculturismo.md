# Documento de Requisitos – Sistema de Inscrição para Eventos de Fisiculturismo

## 1. Objetivo (tabelas: visão geral, sem tabela dedicada)

Definir os requisitos necessários para que o sistema Bora Ali suporte inscrições e operação de eventos de fisiculturismo, contemplando múltiplas categorias por atleta, regras configuráveis, cobrança estruturada e operação de credenciamento no local do evento.

## 2. Cadastro do Atleta (tabelas: users)

Dados obrigatórios:

- Nome completo
- CPF
- RG
- Data de nascimento
- Sexo
- Cidade
- UF
- Telefone
- Treinador

Dados opcionais:

- Instagram

Regras:

- O sistema deve validar o formato do CPF
- A idade do atleta deve ser calculada automaticamente com base na data de nascimento
- O sistema deve permitir que, no futuro, campos sejam configuráveis por evento

## 3. Inscrição no Evento (nova tabela: registrations ou orders)

Requisitos:

- Um atleta pode possuir múltiplas inscrições em diferentes eventos
- Cada inscrição possui um status (pendente, confirmada, credenciada)
- O número do atleta pode ser gerado automaticamente ou definido durante o credenciamento

## 4. Categorias (novas tabelas: event_categories, registration_categories)

Estrutura:

- As categorias devem ser mantidas em formato plano (sem hierarquia)
- Cada categoria pertence a um evento

Configurações:
Cada categoria deve possuir um conjunto de regras configuráveis, incluindo:

- Sexo permitido
- Idade mínima e máxima
- Peso mínimo e máximo
- Altura mínima e máxima
- Indicador de iniciante (estreante)
  Essas regras devem ser armazenadas como configuração flexível e interpretadas pelo sistema.

Regras funcionais:

- O atleta pode selecionar múltiplas categorias sem limite
- O sistema deve validar automaticamente se o atleta atende aos critérios da categoria
- O sistema deve permitir que staff ou administradores ignorem validações quando necessário

## 5. Kits (Estrutura de Cobrança) (novas tabelas: event_kits, registration_kits)

Conceito:

- Categorias definem elegibilidade
- Kits definem valores e formas de cobrança

Requisitos:

- Um evento pode possuir múltiplos kits
- Um atleta pode selecionar um ou mais kits durante a inscrição
- Kits podem representar inscrição por quantidade de categorias
- Kits podem representar pacotes promocionais
- Kits podem representar combinações com serviços inclusos

## 6. Itens Adicionais (Add-ons) (novas tabelas: event_addons, registration_addons)

Exemplos:

- Backstage
- Fotos
- Vídeos
- Pintura corporal

Requisitos:

- Cada item adicional possui preço próprio
- Pode ser selecionado individualmente
- Pode permitir quantidade, quando aplicável
- Pode possuir limite de seleção

## 7. Pagamentos (tabelas: orders | nova: registration_payments)

Formas de pagamento:

- PIX
- Cartão
- Dinheiro (registro manual)

Requisitos:

- Uma inscrição pode possuir múltiplos pagamentos
- O sistema deve controlar o status do pagamento: pendente, pago ou confirmado manualmente
- Deve permitir integração futura com gateways de pagamento
- Deve permitir registro manual por operadores no evento

## 8. Medidas do Atleta (novas tabelas: athlete_measurements)

Dados:

- Altura
- Peso

Regras:

- O atleta pode informar medidas no momento da inscrição
- Medidas oficiais devem ser registradas durante o credenciamento
- O sistema deve manter histórico de medições
- Deve ser possível identificar quais medições são oficiais

## 9. Staff do Evento (tabelas: users | nova: event_staff)

Tipos de staff:

- Administrador
- Check-in (credenciamento)
- Caixa (pagamentos)
- Juiz (visualização de categorias)

Requisitos:

- Cada membro do staff deve estar vinculado a um evento
- O sistema deve controlar permissões de acesso com base no papel
- O acesso deve ser restrito às funcionalidades necessárias para cada função

## 10. Credenciamento (Check-in) (nova tabela: registration_checkins)

Fluxo:

- Localização do atleta (nome, CPF ou outro identificador)
- Conferência dos dados cadastrais
- Registro das medidas oficiais
- Ajuste de categorias, se necessário
- Verificação ou registro de pagamento
- Geração ou confirmação do número do atleta
- Registro de credenciamento

Requisitos:

- O sistema deve registrar quem realizou o credenciamento
- Deve registrar data e hora do processo
- Deve permitir ajustes em tempo real na inscrição

## 11. Validações de Negócio (tabelas: event_categories, registration_categories)

Validações automáticas:

- Compatibilidade entre sexo do atleta e categoria
- Compatibilidade de idade com a categoria
- Compatibilidade de peso e altura, quando aplicável

Regras adicionais:

- Permitir override manual por staff autorizado
- Registrar alterações feitas durante o evento

## 12. Fluxo Operacional Completo (tabelas envolvidas: users, events, registrations, event_categories, event_kits, event_addons, registration_payments, registration_checkins)

Inscrição online:

- Cadastro ou identificação do atleta
- Preenchimento dos dados
- Seleção de categorias
- Seleção de kits
- Seleção de itens adicionais
- Pagamento
- Confirmação da inscrição

Operação no evento:

- Acesso ao painel por staff
- Busca do atleta
- Credenciamento
- Registro de medidas oficiais
- Ajustes necessários
- Confirmação de pagamento
- Liberação do atleta para competição

## 13. Considerações Gerais

- O sistema deve ser flexível para suportar diferentes formatos de eventos
- Deve permitir configurações específicas por evento
- Deve suportar operação offline parcial com registro manual e posterior sincronização
- Deve priorizar usabilidade no dia do evento, com interfaces rápidas e simples para o staff

## 14. Conclusão

Com os requisitos definidos, o sistema passa a suportar não apenas inscrições, mas também a operação completa de eventos de fisiculturismo. A separação entre regras de categoria, cobrança via kits e operação de credenciamento garante flexibilidade, escalabilidade e aderência a diferentes formatos de competição.
