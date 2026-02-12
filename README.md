# BedFlow API — Gerenciamento de Leitos

API REST para realizar o gerenciamento de ocupação de leitos de um hospital.

## Objetivo

A API permite:

- Incluir um paciente em um leito
- Desocupar um leito
- Transferir um paciente para outro leito
- Descobrir o leito de um paciente a partir do CPF
- Descobrir o status de ocupação de um leito
- Listar leitos com seu respectivo status de ocupação

### Regras

- Um mesmo paciente não pode estar em mais de um leito ao mesmo tempo
- Cada leito só pode ter um paciente por vez

> Observação: o foco é apenas o gerenciamento de ocupação, portanto não é estritamente necessário um CRUD completo de leitos e pacientes.

---

## Stack / Dependências

- PHP 8.2+ (recomendado 8.3)
- Laravel
- PostgreSQL (via Docker)
- Composer
- Docker + Docker Compose

---

## Como executar o projeto

### 1) Instalar dependências

```bash
composer install
```

### 2) Configurar ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 3) Subir o banco (PostgreSQL via Docker)

```bash
docker compose up -d
```

### 4) Configurar conexão com o banco

Garanta que seu `.env` contém:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bedflow
DB_USERNAME=bedflow
DB_PASSWORD=bedflow

SESSION_DRIVER=file
```

### 5) Rodar migrations e seeders

O projeto possui um seeder para criar leitos automaticamente (ex.: A101..A120), facilitando testes.

```bash
php artisan migrate --seed
```

Alternativa (reset completo do banco local):

```bash
php artisan migrate:fresh --seed
```

### 6) Subir a API

```bash
php artisan serve
```

A API ficará disponível em:

- [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Endpoints

- `GET /api/health-check-api`
  Health check simples.

- `GET /api/beds`
  Lista leitos com status de ocupação.

- `GET /api/beds/{bed}`
  Detalha um leito e seu status (inclui paciente quando ocupado).

- `POST /api/beds/{bed}/occupy`
  Ocupa um leito com um paciente (CPF obrigatório; nome opcional).
  Body (JSON):

    ```json
    {
        "cpf": "123.456.789-01",
        "name": "Maria"
    }
    ```

- `POST /api/beds/{bed}/release`
  Desocupa um leito (se estiver ocupado).

- `POST /api/beds/transfer`
  Transfere o paciente de um leito para outro.
  Body (JSON):

    ```json
    {
        "from_bed_id": 1,
        "to_bed_id": 2
    }
    ```

- `GET /api/patients/{cpf}/bed`
  Retorna o leito atual de um paciente pelo CPF.

---

## Seeder (necessário para testar a API)

Para facilitar os testes manuais, o projeto possui um **seeder de leitos** que cria um conjunto inicial (ex.: `A101..A120`).  
Como os endpoints usam o ID do leito (`/api/beds/{bed}`), é indicado executar o seeder antes de testar a API.

- Classe: `Database\\Seeders\\BedSeeder`

### Como executar

Se você já rodou as migrations:

```bash
php artisan db:seed
```

Ou, para criar as tabelas e popular os leitos de uma vez:

```bash
php artisan migrate --seed
```

para resetar o banco local e popular novamente:

```bash
php artisan migrate:fresh --seed
```

Depois disso, teste:

- `GET /api/beds` para ver os leitos criados
- `POST /api/beds/{id}/occupy` para ocupar um leito

## Testes

Para rodar a suíte de testes:

```bash
php artisan test
```

### Banco de testes

- O projeto utiliza `.env.testing` localmente (não versionado) e o template `.env.testing.example`.
- Recomenda-se criar um banco `bedflow_test` no Postgres do Docker.

---

## Coleção de Requests (Insomnia)

Você pode importar a coleção do Insomnia disponível no repositório:

- Pasta/arquivo: [BedFlow-API-Requests.json](BedFlow-API-Requests.json)

---

## Notas técnicas

- O CPF é armazenado como **apenas dígitos** (11 caracteres). A API aceita com ou sem máscara.
- As regras de negócio são reforçadas também no banco (PostgreSQL) via **índices únicos parciais** (ocupação ativa = `released_at IS NULL`):
    - 1 ocupação ativa por leito
    - 1 ocupação ativa por paciente
