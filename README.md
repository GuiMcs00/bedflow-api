# BedFlow API (Hospital Bed Management)

REST API to manage hospital bed occupancy:

- Assign a patient to a bed
- Release a bed
- Transfer a patient to another bed
- Find a patient’s bed by CPF
- Get bed occupancy status
- List beds with occupancy status

## Tech Stack

- PHP 8.2+ (recommended 8.3)
- Laravel
- PostgreSQL (Docker)

## Requirements

- PHP 8.2+ (recommended 8.3)
- Composer
- Docker + Docker Compose

## Getting Started

### 1) Setup environment

Install dependencies:

```bash
composer install
```

Create environment file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

### 2) Start database (PostgreSQL via Docker)

Start Postgres:

```bash
docker compose up -d
```

### 3) Configure database connection

Make sure your `.env` contains:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bedflow
DB_USERNAME=bedflow
DB_PASSWORD=bedflow

SESSION_DRIVER=file
```

### 4) Run migrations

```bash
php artisan migrate
```

### 5) Run the API locally

```bash
php artisan serve
```

API will be available at:

- [http://127.0.0.1:8000](http://127.0.0.1:8000)

## Planned Endpoints (to be implemented)

> The project focuses on bed occupancy management (no full CRUD required).

- `POST /api/beds/{bed}/occupy`
  Assigns a patient to a bed (CPF required; name optional)

- `POST /api/beds/{bed}/release`
  Releases an occupied bed

- `POST /api/beds/transfer`
  Transfers a patient from one bed to another

- `GET /api/patients/{cpf}/bed`
  Finds the current bed for a patient by CPF

- `GET /api/beds/{bed}`
  Returns bed status (and patient info if occupied)

- `GET /api/beds`
  Lists beds with their occupancy status

## Notes

- Patient CPF is stored as digits only (11 chars). Inputs may be provided with or without formatting.
- Business rules are enforced at database level using PostgreSQL partial unique indexes:
    - one active occupancy per bed
    - one active occupancy per patient
