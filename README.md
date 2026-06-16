# Tour Projetos — Sistema de Gerenciamento de Viagens de Turismo

Sistema web administrativo (monolítico) para o gerenciamento de **viagens de turismo**, **veículos**, **motoristas** e **usuários administradores**, com autenticação, troca de senha obrigatória no primeiro acesso e uma **API REST** para consulta das viagens.

> Projeto desenvolvido como Teste Prático de Desenvolvimento de Software — **COINPEL / Prefeitura Municipal de Pelotas**.

---

## Índice

- [Tecnologias](#-tecnologias)
- [Arquitetura e organização de pastas](#-arquitetura-e-organização-de-pastas)
- [Requisitos do projeto e como foram resolvidos](#-requisitos-do-projeto-e-como-foram-resolvidos)
- [Funcionalidades implementadas](#-funcionalidades-implementadas)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação e execução](#-instalação-e-execução)
- [Banco de dados com Docker](#-banco-de-dados-com-docker)
- [Variáveis de ambiente](#-variáveis-de-ambiente)
- [Credenciais de acesso inicial](#-credenciais-de-acesso-inicial)
- [API REST](#-api-rest)
- [Boas práticas adotadas](#-boas-práticas-adotadas)
- [Modelo de dados](#-modelo-de-dados)
- [Testes](#-testes)
- [Padrão de commits](#-padrão-de-commits)
- [Autor](#-autor)

---

## Tecnologias

| Categoria | Tecnologia | Versão | Função no projeto |
|-----------|-----------|--------|-------------------|
| Linguagem | **PHP** | 8.2+ | Linguagem base da aplicação |
| Framework | **Laravel** | 12.x | Estrutura MVC monolítica do sistema |
| Banco de dados | **PostgreSQL** | 16 | Persistência dos dados |
| Estilização | **Tailwind CSS** | 4.x | UI utilitária e responsiva |
| Bundler | **Vite** | 5.x | Compilação de assets (CSS/JS) |
| Autenticação | **Laravel (Auth/Sanctum)** | — | Sessão web + token na API |
| Infraestrutura | **Docker / Docker Compose** | — | Containerização do banco de dados |
| Qualidade | **Laravel Pint / Larastan** | — | Padronização e análise estática |
| Versionamento | **Git** | — | Controle de versão |

> Substitua as versões pelas que você efetivamente utilizou (`php -v`, `composer show laravel/framework`).

---

## Arquitetura e organização de pastas

O projeto segue o padrão **MVC** recomendado pelo Laravel, com **Controllers magros**: a validação fica em **Form Requests**, a autorização em **Policies** e a saída da API em **Resources**. (Para CRUDs simples como estes, uma camada de *Service* adicionaria indireção sem ganho real — optou-se por não usá-la.)

```
app/
├── Http/
│   ├── Controllers/        # Recebem a requisição e orquestram a resposta
│   ├── Requests/           # Form Requests: validação isolada das regras de negócio
│   ├── Resources/          # API Resources: padronização do JSON de saída
│   └── Middleware/         # Ex.: ForcePasswordChange (primeiro acesso)
├── Models/                 # Eloquent Models (Trip, Vehicle, Driver, User,
│                           #   Package, Client, Contract, ActivityLog)
├── Enums/                  # Enums de status (TripStatus, ContractStatus)
└── Policies/               # Autorização por entidade

database/
├── migrations/             # Estrutura das tabelas
├── factories/              # Geração de dados para testes/seeds
└── seeders/                # Usuário admin inicial e dados de exemplo

resources/
├── views/                  # Blade + componentes (seguindo o design do Figma)
│   ├── components/         # Componentes reutilizáveis (inputs, botões, layout)
│   ├── auth/               # Login e troca de senha
│   ├── trips/  vehicles/  drivers/  users/
│   ├── packages/  clients/  contracts/  statistics/
│   └── layouts/
└── css/  js/               # Entradas do Vite/Tailwind

routes/
├── web.php                 # Rotas do ambiente administrativo
└── api.php                 # Rotas da API REST

docker-compose.yml          # Serviço do PostgreSQL
```

**Por que esta organização:** separar `Requests`, `Resources` e `Policies` mantém o Controller limpo, facilita testes e reutilização, e deixa cada responsabilidade em um único lugar (princípio da responsabilidade única).

---

## Requisitos do projeto e como foram resolvidos

### Requisitos Funcionais

| Código | Requisito | Como foi resolvido |
|--------|-----------|--------------------|
| **RF01** | Gerenciar Viagens (origem, destino, horários de partida/chegada e demais campos) | CRUD completo de `Trip`, com relacionamento para veículo e motorista, **validação de datas** (chegada > partida), **status** da viagem (agendada/em andamento/concluída/cancelada) e **validação de conflito de agendamento** (mesmo veículo ou motorista em horários sobrepostos). |
| **RF02** | Gerenciar Veículos | CRUD completo de `Vehicle` (placa, modelo, marca, capacidade, ano, status ativo/inativo). |
| **RF03** | Gerenciar Motoristas | CRUD completo de `Driver` (nome, CPF, CNH, validade, etc.), com **foto de perfil opcional** e **listagem em cards**. |
| **RF04** | Gerenciar Usuários administradores | CRUD de `User`, com criação de novos administradores. |
| **RF05** | Autenticação dos administradores | Login via sessão do Laravel (`Auth`), com proteção CSRF e *rate limiting* na rota de login. |
| **RF06** | Troca de senha obrigatória no primeiro acesso | Flag `must_change_password` no usuário + middleware `ForcePasswordChange` que redireciona para a tela de nova senha (mínimo 8 caracteres, conforme o design). |
| **RF07** | Visualização de todos os módulos pelo administrador | Layout administrativo com menu para todos os módulos + **dashboard** com contadores e próximas viagens, protegido por autenticação. |

### Requisitos Não Funcionais

| Código | Requisito | Como foi resolvido |
|--------|-----------|--------------------|
| **RNF01** | Senhas criptografadas no banco | Hash automático via `Hash::make` / cast `hashed` do Eloquent (Bcrypt). Senha nunca é armazenada ou retornada em texto puro. |
| **RNF02** | Laravel 12 monolítico | Aplicação única em Laravel 12, com web e API no mesmo projeto. |
| **RNF03** | API REST com endpoint que lista todas as viagens em JSON | Endpoint `GET /api/trips` retornando JSON padronizado via API Resource. |
| **RNF05** | Banco de dados PostgreSQL | Driver `pgsql`, banco rodando em container Docker. |
| **RNF06** | Tailwind CSS | Toda a UI construída com Tailwind. |
| **RNF07** | Responsividade | Layout responsivo (mobile-first) com utilitários de breakpoint do Tailwind. |

> **Observação:** a numeração do documento original pula de **RNF03** para **RNF05** (não existe RNF04). Mantive a numeração original para rastreabilidade.

---

## Funcionalidades implementadas

Além dos requisitos obrigatórios, o sistema entrega um conjunto de recursos que
aproximam a aplicação de um produto real:

**Núcleo (requisitos)**
- **4 CRUDs completos** — Viagens, Veículos, Motoristas e Usuários, com busca,
  paginação e *soft delete*.
- **Autenticação** por sessão (login/logout) com *rate limiting* e CSRF.
- **Troca de senha obrigatória no 1º acesso** (apresentada como modal, não dispensável).
- **API REST** `GET /api/trips` (JSON padronizado via API Resource).

**Extras**
- **Validação de conflito de agendamento:** ao criar/editar uma viagem, o sistema
  bloqueia o mesmo **veículo** ou **motorista** em horários que se sobrepõem.
- **Status da viagem:** `agendada` (padrão), `em_andamento`, `concluida`, `cancelada` —
  com filtro e exibição na listagem.
- **API paginada e com filtros:** `GET /api/trips` é paginado e aceita
  `origin`, `destination` e `date` (ver seção da API), exigindo `auth:sanctum`.
- **Dashboard:** contadores (viagens, veículos, motoristas, usuários) e lista de
  **próximas viagens**.
- **Log de atividades (auditoria):** criação/edição/exclusão de qualquer registro é
  gravada em `activity_logs` (quem fez, ação e registro afetado), visível em
  **/activity-logs** (somente leitura).
- **Foto de perfil do motorista:** upload **opcional** e validado (imagem,
  `jpg/jpeg/png/webp`, máx. 2 MB), armazenada no disco `public` com nome **gerado**
  pelo Laravel; a listagem de Motoristas é apresentada em **cards** com avatar
  (placeholder com a inicial quando não há foto).

**Módulos adicionais (extensões além do escopo do teste)**

> O enunciado do teste permitia **propor funcionalidades**. Os módulos abaixo **não**
> fazem parte dos requisitos (RF/RNF) — foram acrescentados para aproximar o sistema
> de um produto real de uma agência de turismo. Seguem exatamente o mesmo padrão dos
> CRUDs do núcleo (Form Requests, Policy, busca, paginação, *soft delete*, drawer,
> auditoria).

- **Pacotes:** CRUD do produto turístico (`name`, `destination`, `duration_days`,
  `price`, `description`, `active`).
- **Clientes:** CRUD do cadastro de clientes (`name`, `email` único, `phone`,
  `document` [CPF/CNPJ] único, `active`) — gerenciado pelo admin, **sem login de
  cliente**.
- **Contratos:** vínculo entre um **cliente (obrigatório)** e um **pacote**, com
  `title`, `start_date`, `end_date` (término ≥ início), `value` e `status`
  (`rascunho`/`ativo`/`concluido`/`cancelado`). O contrato é a **ponte**: o cliente se
  relaciona com pacotes **através** dos contratos. Listagem com *eager loading* de
  cliente e pacote (sem N+1).
- **Estatísticas:** painel de **números agregados** (contadores e somas — viagens por
  status, ativos por cadastro, contratos por status, valor dos contratos ativos),
  **sem gráficos e sem dependências novas**, em **/statistics**.

---

## Pré-requisitos

- PHP **8.2+** com extensões: `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer 2.x
- Node.js **20+** e npm
- Docker e Docker Compose
- Git

---

## Instalação e execução

```bash
# 1. Clonar o repositório
git clone https://github.com/GuilhermeLambrecht/TesteCoinPel.git
cd TesteCoinPel

# 2. Instalar dependências PHP
composer install

# 3. Criar o arquivo de ambiente
cp .env.example .env
php artisan key:generate

# 4. Subir o banco de dados (PostgreSQL via Docker)
docker compose up -d

# 5. Rodar migrations e popular dados iniciais (usuário admin + exemplos)
php artisan migrate --seed

# 6. Criar o symlink de storage (necessário para exibir as fotos dos motoristas)
php artisan storage:link

# 7. Instalar dependências de front-end e compilar assets
npm install
npm run build      # produção
# npm run dev      # desenvolvimento (com hot reload)

# 8. Iniciar o servidor
php artisan serve
```

Acesse: **http://localhost:8000**

> **API REST (Sanctum):** já vem configurada e versionada no projeto (`routes/api.php`,
> `config/sanctum.php` e a migration de tokens). **Não** é necessário rodar
> `php artisan install:api` em um clone — o passo 5 (`migrate`) já cria a tabela
> `personal_access_tokens`.

---

## Banco de dados com Docker

O PostgreSQL roda isolado em um container, evitando instalar o banco diretamente na máquina e garantindo o mesmo ambiente para qualquer avaliador.

`docker-compose.yml`:

```yaml
services:
  database:
    image: postgres:16-alpine
    container_name: tour_postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: tour_db
      POSTGRES_USER: tour_user
      POSTGRES_PASSWORD: secret
    # A porta do host é parametrizada via DB_PORT (padrão 5433, para não conflitar
    # com um PostgreSQL nativo na 5432). Ajuste DB_PORT no .env se precisar de outra.
    ports:
      - "${DB_PORT:-5433}:5432"
    volumes:
      - tour_pgdata:/var/lib/postgresql/data

volumes:
  tour_pgdata:
```

Comandos úteis:

```bash
docker compose up -d        # iniciar
docker compose down         # parar
docker compose logs -f      # acompanhar logs
docker compose down -v      # parar e apagar os dados (reset total)
```

---

## Variáveis de ambiente

Principais valores no `.env` (ajustados para o Docker acima):

```env
APP_NAME="Tour Projetos"
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=tour_db
DB_USERNAME=tour_user
DB_PASSWORD=secret
```

> **Porta do banco (`DB_PORT`):** o `docker-compose.yml` mapeia a porta do host via
> `${DB_PORT:-5433}`, então o padrão é **5433** — escolhido para não conflitar com um
> PostgreSQL nativo na 5432. O container sempre escuta a **5432 internamente**; muda
> apenas a porta exposta no host. Se quiser outra, defina `DB_PORT` no `.env`.

> O `.env` real **nunca** é versionado (está no `.gitignore`). Apenas o `.env.example` vai para o repositório.

---

## Credenciais de acesso inicial

O seeder cria um administrador com senha provisória. No primeiro login, o sistema **obriga** a troca de senha (RF06).

```
E-mail: admin@coinpel.local
Senha:  password
```

> Estas são exatamente as credenciais criadas pelo `database/seeders/DatabaseSeeder.php`.
> Como `must_change_password` nasce `true`, o primeiro login exige a troca da senha provisória.

---

## API REST

Endpoint que lista todas as viagens em JSON (RNF03):

```http
GET /api/trips
Accept: application/json
Authorization: Bearer <token>
```

**Autenticação obrigatória.** Por ser uma API administrativa (sem consumo externo previsto),
o endpoint é **protegido por `auth:sanctum`** — uma requisição sem token válido retorna
**401 Unauthorized**. O payload é **minimizado**: os relacionamentos trazem apenas o necessário
para identificar veículo e motorista, **sem dados pessoais** (CPF, CNH, telefone).

A listagem é **paginada** (15 por página); navegue com `?page=N`. A resposta inclui os
metadados de paginação do Laravel (`links` e `meta`).

**Filtros opcionais e combináveis** (query string):

| Parâmetro | Efeito |
|---|---|
| `?origin=` | viagens cuja **origem** contém o texto (case-insensitive) |
| `?destination=` | viagens cujo **destino** contém o texto (case-insensitive) |
| `?date=` | viagens com **partida a partir** da data (`YYYY-MM-DD`) |

Ex.: `GET /api/trips?destination=Gramado&date=2025-06-01&page=2`. Sem parâmetros, retorna
todas as viagens paginadas.

Gerando um token para teste (via `php artisan tinker`):

```php
$user = App\Models\User::first();
$user->createToken('demo')->plainTextToken; // copie o token retornado
```

Chamada de exemplo:

```bash
curl -H "Accept: application/json" \
     -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     http://localhost:8000/api/trips
```

Exemplo de resposta (shape enxuto, paginado):

```json
{
  "data": [
    {
      "id": 1,
      "origin": "Pelotas",
      "destination": "Gramado",
      "departure_at": "2025-06-20T08:00:00.000000Z",
      "arrival_at": "2025-06-20T14:30:00.000000Z",
      "status": "agendada",
      "vehicle": { "id": 3, "plate": "ABC1D23", "model": "Marcopolo Paradiso" },
      "driver":  { "id": 5, "name": "João Silva" }
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/trips?page=1",
    "last":  "http://localhost:8000/api/trips?page=2",
    "prev":  null,
    "next":  "http://localhost:8000/api/trips?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 23
  }
}
```

> O campo `status` da viagem assume um destes valores: `agendada` (default), `em_andamento`,
> `concluida` ou `cancelada`.

---

## Boas práticas adotadas

### Segurança
- Senhas com **hash Bcrypt** (RNF01); nunca trafegam nem são salvas em texto puro.
- Proteção **CSRF** em todos os formulários (padrão do Laravel).
- Validação centralizada em **Form Requests**, evitando dados inválidos no banco.
- **Rate limiting** na rota de login para mitigar ataques de força bruta.
- **Mass assignment** controlado via `$fillable` nos models.
- **Policies / middleware de autenticação** protegendo todas as rotas administrativas.
- Segredos fora do versionamento (`.env` no `.gitignore`).
- Troca obrigatória da senha provisória no primeiro acesso.
- **Log de atividades (auditoria):** criação/edição/exclusão de registros de qualquer
  módulo (viagens, veículos, motoristas, usuários, pacotes, clientes e contratos) ficam
  registradas em `activity_logs` com quem fez, a ação e o registro afetado. Disponível em
  **/activity-logs** (somente leitura).

### Performance
- **Eager loading** (`with()`) nos relacionamentos para evitar consultas N+1.
- **Paginação** nas listagens para não carregar tabelas inteiras.
- **Índices** em chaves estrangeiras e colunas mais consultadas.
- **API Resources** entregando apenas os campos necessários no JSON.
- Assets minificados em produção via `npm run build`.

### Infraestrutura
- Banco de dados containerizado com **Docker**, garantindo paridade de ambiente.
- Volume persistente para os dados do PostgreSQL.
- `.env.example` documentado para subida rápida do projeto.

### Código limpo e organização
- Código (variáveis, métodos, classes) em **inglês**; comentários em português.
- Controllers magros; validação em **Form Requests** e autorização em **Policies**.
- Componentes Blade reutilizáveis (inputs, botões, layout), seguindo a hierarquia do design.
- Padronização automática com **Laravel Pint** (`./vendor/bin/pint`).
- Análise estática com **Larastan/PHPStan** para evitar erros antes da execução.
- Estrutura de pastas previsível e por responsabilidade.

---

## Modelo de dados

Esquema resumido das principais entidades:

```
users
 ├─ id, name, email, password
 └─ must_change_password (bool)   → controla o primeiro acesso (RF06)

vehicles
 └─ id, plate (unique), model, brand, capacity, year, active (bool)

drivers
 └─ id, name, cpf (unique), cnh, cnh_category, cnh_expiration, phone,
    active (bool), photo_path (nullable)   → foto de perfil opcional

trips
 ├─ id, origin, destination, departure_at, arrival_at
 ├─ status (enum: agendada | em_andamento | concluida | cancelada)
 ├─ vehicle_id  → FK vehicles (indexada)
 └─ driver_id   → FK drivers  (indexada)

packages
 └─ id, name, destination, duration_days, price, description (nullable), active (bool)

clients
 └─ id, name, email (unique), phone, document (unique), active (bool)

contracts
 ├─ id, title, start_date, end_date, value
 ├─ status (enum: rascunho | ativo | concluido | cancelado)
 ├─ client_id   → FK clients  (obrigatória, indexada)
 └─ package_id  → FK packages (obrigatória, indexada)

activity_logs                     → auditoria (criar/editar/excluir)
 ├─ id, action, description, subject_type, subject_id
 └─ user_id → FK users

personal_access_tokens            → tokens da API (Sanctum)
```

**Relacionamentos principais:**

- `Trip` **belongsTo** `Vehicle` e `Driver`; `Vehicle`/`Driver` **hasMany** `Trip`.
- `Contract` **belongsTo** `Client` e `Package` (ambos obrigatórios).
- `Client` **hasMany** `Contract`; `Package` **hasMany** `Contract`.
- O **Contrato é a ponte**: um `Client` se relaciona com `Package` **através** dos
  contratos (não há vínculo direto cliente↔pacote).

Todas as tabelas de negócio usam `timestamps` e **soft deletes** para preservar
histórico. As fotos dos motoristas ficam no disco `public` (servidas via
`php artisan storage:link`).

---

## Testes

```bash
php artisan test                 # suíte completa (PHPUnit)
./vendor/bin/pint --test         # padrão de código (Laravel Pint)
./vendor/bin/phpstan analyse     # análise estática (Larastan, nível 5)
```

São **148 testes / 602 asserções** (todos verdes), cobrindo os caminhos principais:
autenticação e *rate limiting*, fluxo de troca de senha no 1º acesso, todos os CRUDs
(núcleo + pacotes, clientes e contratos), validações e *soft delete*, unicidade de
e-mail/documento do cliente, conflito de agendamento de viagens, validação de datas e
vínculo cliente/pacote do contrato (incluindo *eager loading* sem N+1), upload/validação
da foto do motorista, estatísticas, log de atividades e o endpoint da API (autenticação
obrigatória e *shape* sem dados pessoais). Os testes rodam em **SQLite em memória**, sem
depender do PostgreSQL.

---

##  Autor

**Guilherme Silva Lambrecht** — [GitHub](https://github.com/GuilhermeLambrecht) · [LinkedIn](https://www.linkedin.com/in/guilherme-lambrecht-3160253a1/)

Teste prático desenvolvido para a **COINPEL — Empresa Municipal de Informática de Pelotas**.