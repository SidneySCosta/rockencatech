# Rockencatech — Catálogo de E-commerce com Autenticação

Catálogo de produtos para e-commerce com autenticação de usuários via **Laravel Sanctum**, padrões de design **Service & Repository**, respostas padronizadas via **Resource Collections** e ambiente totalmente containerizado com **Docker**.

> Backend: Laravel 11 + MySQL · Frontend: React (Vite) · Auth: Laravel Sanctum · Docker Compose

**Demo:** https://rockencatech-app.vercel.app/

---

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) 24+
- [Docker Compose](https://docs.docker.com/compose/install/) v2+ (`docker compose`)
- Git

> Nenhuma instalação local de PHP, Node ou MySQL é necessária — tudo roda dentro dos containers.

---

## Como rodar o projeto

### 1. Clone o repositório

```bash
git clone <repository-url>
cd rockencatech
```

### 2. Configure as variáveis de ambiente

```bash
cp .env.example .env
```

As credenciais padrão já estão configuradas para desenvolvimento local. Edite o `.env` apenas se necessário.

### 3. Suba os containers

```bash
docker compose up --build
```

Na primeira execução, o entrypoint do backend executa automaticamente:

1. `composer install`
2. Copia `.env.example` → `.env` (se não existir)
3. Injeta as variáveis de banco de dados do Docker no `.env`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. Inicia o PHP-FPM + Nginx

### 4. Acesse a aplicação

| Serviço   | URL                    |
|-----------|------------------------|
| Frontend  | http://localhost:5173  |
| Backend   | http://localhost:8000  |
| MySQL     | localhost:3306         |

---

## Estrutura do Projeto

```
rockencatech/
├── api/                        # Backend Laravel 11
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/    # Controllers finos — apenas delegam ao Service
│   │   │   └── Resources/      # Resource Collections (respostas padronizadas)
│   │   ├── Models/             # Eloquent Models
│   │   ├── Repositories/       # Repository Pattern — acesso ao banco de dados
│   │   └── Services/           # Service Layer — regras de negócio
│   ├── database/
│   │   ├── migrations/         # Migrations: users, categories, products, tokens
│   │   └── seeders/            # Seeders com dados de exemplo
│   └── docker/
│       ├── nginx.conf          # Configuração do Nginx
│       └── entrypoint.sh       # Script de inicialização do container
├── app/                        # Frontend React (Vite)
│   └── src/
│       ├── pages/              # Login, Register, ProductList, ProductDetail
│       ├── components/         # Componentes reutilizáveis
│       └── services/           # Axios — chamadas à API
├── docker-compose.yml
└── .env.example
```

---

## Arquitetura do Backend

### Padrão Repository

Os repositórios encapsulam toda a lógica de acesso ao banco de dados. Os controllers e services nunca fazem queries diretamente.

```
app/Repositories/
├── ProductRepository.php    # Listar, buscar por categoria, pesquisar, CRUD
├── CategoryRepository.php   # Listar, CRUD de categorias
└── UserRepository.php       # Criação e busca de usuários
```

### Camada de Serviço (Service Layer)

Os services encapsulam a lógica de negócio e utilizam os repositories via injeção de dependência.

```
app/Services/
├── ProductService.php       # Regras de negócio de produtos
├── CategoryService.php      # Regras de negócio de categorias
└── AuthService.php          # Registro, login, geração de token
```

### Resource Collections

Todas as respostas da API passam por Resource Collections — nenhuma resposta retorna arrays brutos.

```
app/Http/Resources/
├── ProductResource.php
├── ProductCollection.php
├── CategoryResource.php
└── UserResource.php
```

---

## Banco de Dados

### Estrutura das Tabelas

**users**

| Coluna       | Tipo         |
|--------------|--------------|
| id           | bigint PK    |
| name         | varchar      |
| email        | varchar (unique) |
| password     | varchar      |
| created_at   | timestamp    |
| updated_at   | timestamp    |

**categories**

| Coluna     | Tipo      |
|------------|-----------|
| id         | bigint PK |
| name       | varchar   |
| created_at | timestamp |
| updated_at | timestamp |

**products**

| Coluna      | Tipo           |
|-------------|----------------|
| id          | bigint PK      |
| name        | varchar        |
| description | text           |
| price       | decimal(10,2)  |
| category_id | bigint FK      |
| image_url   | varchar        |
| created_at  | timestamp      |
| updated_at  | timestamp      |

**personal_access_tokens** — gerenciada pelo Laravel Sanctum

---

## API Endpoints

### Autenticação (públicos)

| Método | Endpoint         | Descrição                              |
|--------|------------------|----------------------------------------|
| POST   | /api/register    | Registra novo usuário (name, email, password) |
| POST   | /api/login       | Autentica e retorna token Sanctum      |

### Produtos (públicos)

| Método | Endpoint                       | Descrição                        |
|--------|--------------------------------|----------------------------------|
| GET    | /api/products                  | Lista paginada de produtos       |
| GET    | /api/products/{id}             | Detalhes de um produto           |
| GET    | /api/products?category={id}    | Filtra produtos por categoria    |
| GET    | /api/products?search={query}   | Busca produtos por nome/descrição |

### Categorias (públicas)

| Método | Endpoint          | Descrição              |
|--------|-------------------|------------------------|
| GET    | /api/categories   | Lista todas as categorias |

### Endpoints protegidos (`auth:sanctum`)

| Método | Endpoint              | Descrição              |
|--------|-----------------------|------------------------|
| POST   | /api/products         | Cria produto           |
| PUT    | /api/products/{id}    | Atualiza produto       |
| DELETE | /api/products/{id}    | Remove produto         |
| POST   | /api/categories       | Cria categoria         |
| PUT    | /api/categories/{id}  | Atualiza categoria     |
| DELETE | /api/categories/{id}  | Remove categoria       |

> Endpoints protegidos requerem header: `Authorization: Bearer {token}`

### Postman Collection

A collection com todos os endpoints e cenários de teste está em [`api/docs/rockencatech.postman_collection.json`](api/docs/rockencatech.postman_collection.json).

**Como usar:**

1. Importe o arquivo no Postman: **File → Import → selecione o arquivo**
2. Execute cenários individuais ou rode todos via **Collection Runner**
3. O token de autenticação é capturado automaticamente após o login — nenhuma configuração manual necessária

A collection cobre 22 cenários: registro, login, logout, CRUD de produtos e categorias, validações e proteção de endpoints.

---

## Frontend (React)

### Páginas

| Rota              | Descrição                                          |
|-------------------|----------------------------------------------------|
| `/`               | Lista de produtos com paginação, filtro e busca    |
| `/products/:id`   | Detalhes do produto                                |
| `/login`          | Login de usuário                                   |
| `/register`       | Cadastro de usuário                                |

- UI construída com **Material UI**
- Token Sanctum armazenado em `localStorage` após login
- Token enviado automaticamente via Axios em todas as requisições subsequentes

---

## Comandos Úteis

**Iniciar em background**
```bash
docker compose up -d
```

**Parar containers**
```bash
docker compose down
```

**Resetar banco de dados**
```bash
docker compose down -v
docker compose up --build
```

**Rodar migrations manualmente**
```bash
docker compose exec api php artisan migrate
docker compose exec api php artisan migrate:fresh --seed
```

**Acessar shell do MySQL**
```bash
docker compose exec mysql mysql -u rockencatech_user -psecret rockencatech
```

**Ver logs**
```bash
docker compose logs -f         # todos os serviços
docker compose logs -f api     # backend
docker compose logs -f app     # frontend
```

**Reconstruir um container**
```bash
docker compose up --build api
docker compose up --build app
```
