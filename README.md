# LDM

Catálogo de produtos desenvolvido com Laravel, Blade, Eloquent, Vite, CSS e JavaScript.

## Funcionalidades atuais

- catálogo público de produtos;
- pesquisa por nome, descrição, SKU, marca e categoria;
- filtros por categoria, marca, estado de vestuário (novo/seminovo/brechó), preço e estoque;
- ordenação dos cards por preço;
- página de detalhes com galeria e produtos relacionados;
- cadastro de produtos e imagens;
- cadastro e login de clientes;
- área privada do cliente em `/cliente`;
- logout com retorno para a área pública;
- carrossel de banners;
- modal de compartilhamento do produto.

## Organização principal

```text
app/
├── Http/Controllers/
│   ├── AuthController.php
│   └── LDMcontroller.php
└── Models/

resources/
├── views/
│   ├── public/                 # catálogo público
│   ├── customer/               # área autenticada do cliente
│   ├── auth/                   # login e cadastro
│   ├── components/             # partes reutilizáveis
│   ├── layouts/                # layouts público e privado
│   └── partials/               # header, navbar e footer
├── css/
└── js/

routes/web.php                 # rotas web atuais
database/migrations/           # estrutura do banco
```

## Executar localmente

Requisitos:

- PHP 8.3 ou superior;
- Composer;
- Node.js e npm;
- banco configurado no `.env`.

```bash
composer install
npm install
php artisan migrate
php artisan storage:link
npm run dev
php artisan serve
```

Abra `http://127.0.0.1:8000`.

Para gerar os assets de produção:

```bash
npm run build
```

## Autenticação

Rotas principais:

| Método | URL | Ação |
|---|---|---|
| GET | `/register` | formulário de cadastro |
| POST | `/register` | cria o usuário |
| GET | `/login` | formulário de login |
| POST | `/login` | autentica o usuário |
| POST | `/logout` | encerra a sessão |
| GET | `/cliente` | área privada do cliente |

Após criar a conta ou fazer login, o cliente é enviado para `/cliente`. A senha é armazenada com hash pelo cast do model `User`.

## Fluxo público e privado

- `resources/views/public`: catálogo e pesquisa acessíveis sem login;
- `resources/views/customer`: páginas que exigem autenticação;
- `resources/views/auth`: login e cadastro;
- `resources/views/components`: componentes compartilhados entre áreas.

A rota `/cliente` usa o middleware `auth`. As rotas de login e cadastro usam `guest`.

## Banco de dados

As migrations principais criam:

- `users`, `password_reset_tokens` e `sessions`;
- `categories` e `brands`;
- `product`;
- `product_images`;
- `product_variants`.

As imagens são armazenadas em `storage/app/public` e exibidas pelo link `public/storage`.

## Validação rápida

```bash
php artisan route:list
php artisan view:cache
php artisan migrate:status
npm run build
php artisan test
```
