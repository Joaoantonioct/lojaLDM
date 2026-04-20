# Estrutura de Pastas - Loja Virtual LDM

## Data de Criação: 19/04/2026

## Backend vs Frontend

### **BACKEND (Lógica do Servidor)**
Contém toda a lógica de negócio, processamento de dados e API:
- **app/** - Lógica principal da aplicação
- **database/** - Banco de dados e migrações
- **routes/** - Definição de rotas e endpoints
- **config/** - Configurações do servidor
- **storage/** - Armazenamento de arquivos do servidor
- **artisan** - CLI para comandos do servidor

### **FRONTEND (Interface do Usuário)**
Contém tudo que o usuário vê e interage:
- **resources/views/** - Templates HTML/Blade
- **resources/css/** - Estilos CSS fonte
- **resources/js/** - JavaScript fonte
- **public/** - Arquivos estáticos acessíveis pelo navegador
- **public/images/** - Imagens estáticas
- **public/css/** - CSS compilado
- **public/js/** - JavaScript compilado

## Estrutura Completa do Projeto

```
lojaLDM/
|
|--- [BACKEND] app/
|    |--- Http/
|    |    |--- Controllers/          # Controladores da aplicação
|    |    |--- Middleware/           # Middleware personalizados
|    |    |--- Requests/             # Form Requests para validação
|    |--- Models/                   # Models Eloquent
|    |--- Providers/                # Service Providers
|    |--- Services/                 # Lógica de negócio da loja
|    |--- Repositories/             # Padrão Repository para acesso a dados
|
|--- [BACKEND] bootstrap/           # Arquivos de inicialização
|
|--- [BACKEND] config/              # Arquivos de configuração
|    |--- app.php                   # Configuração principal
|    |--- database.php              # Configuração de banco
|    |--- filesystems.php           # Configuração de storage
|    |--- mail.php                  # Configuração de email
|    |--- services.php              # Configuração de serviços externos
|    |--- session.php               # Configuração de sessão
|
|--- [BACKEND] database/
|    |--- factories/                # Factories para testes
|    |--- migrations/               # Migrations do banco
|    |--- seeders/                  # Seeders para dados iniciais
|    |--- database.sqlite           # Banco SQLite
|
|--- [FRONTEND] public/             # Arquivos públicos acessíveis
|    |--- css/                      # Arquivos CSS compilados
|    |--- js/                       # Arquivos JavaScript compilados
|    |--- images/                   # Imagens estáticas
|    |--- storage/                  # Link simbólico para storage
|    |--- index.php                 # Entry point da aplicação
|
|--- [FRONTEND] resources/
|    |--- css/                      # Arquivos CSS fonte
|    |--- js/                       # Arquivos JavaScript fonte
|    |--- views/                    # Templates Blade
|         |--- cart/                # Views do carrinho
|         |--- categories/          # Views de categorias
|         |--- components/           # Componentes Blade
|         |--- layouts/             # Layouts principais
|         |--- orders/              # Views de pedidos
|         |--- products/            # Views de produtos
|         |--- users/               # Views de usuários
|
|--- [BACKEND] routes/
|    |--- web.php                   # Rotas web principais
|    |--- console.php               # Rotas de console
|
|--- [BACKEND] storage/
|    |--- app/                      # Storage da aplicação
|    |    |--- public/              # Arquivos públicos
|    |         |--- products/       # Upload de imagens de produtos
|    |--- framework/                # Cache e logs do framework
|    |--- logs/                     # Logs da aplicação
|
|--- [BACKEND] tests/               # Testes automatizados
|
|--- [BACKEND] vendor/              # Dependências Composer
|
|--- [BACKEND] .env                 # Variáveis de ambiente
|--- [BACKEND] .env.example         # Exemplo de .env
|--- [BACKEND] .gitignore           # Arquivos ignorados pelo Git
|--- [BACKEND] artisan              # CLI do Laravel
|--- [BACKEND] composer.json        # Configuração Composer
|--- [FRONTEND] package.json        # Configuração NPM
|--- [FRONTEND] vite.config.js      # Configuração Vite
```

## Descrição das Principais Pastas

### **BACKEND - app/**
Contém toda a lógica do servidor:
- **Controllers**: Manipulam requisições HTTP e retornam respostas
- **Models**: Representam entidades do banco de dados
- **Services**: Contêm regras de negócio complexas
- **Repositories**: Abstraem acesso a dados
- **Middleware**: Filtros para requisições HTTP
- **Requests**: Validação de formulários

### **FRONTEND - resources/views/**
Templates Blade (interface do usuário):
- **layouts**: Layouts base e componentes compartilhados
- **products**: Views relacionadas a produtos
- **cart**: Funcionalidades do carrinho
- **orders**: Gestão de pedidos
- **users**: Autenticação e perfil de usuário
- **categories**: Gestão de categorias

### **FRONTEND - public/**
Arquivos estáticos acessíveis pelo navegador:
- **css**: Estilos CSS compilados
- **js**: JavaScript compilado
- **images**: Imagens estáticas
- **storage**: Link simbólico para arquivos do storage

### **BACKEND - storage/app/public/**
Armazenamento de arquivos no servidor:
- **products**: Upload de imagens de produtos
- Acessível via link simbólico em `public/storage`

### **BACKEND - database/**
Estrutura do banco de dados:
- **migrations**: Schema versionado
- **seeders**: Dados iniciais
- **factories**: Dados para testes

### **BACKEND - routes/**
Definição de endpoints:
- **web.php**: Rotas web principais
- **console.php**: Rotas de console

### **FRONTEND - resources/css/ e resources/js/**
Arquivos fonte do frontend:
- **CSS**: Estilos SASS/CSS fonte
- **JavaScript**: Lógica do frontend

## Boas Práticas

### **Backend**
1. **Models** em `app/Models/`
2. **Controllers** específicos por módulo em `app/Http/Controllers/`
3. **Middleware** para autenticação e permissões
4. **Services** para lógica complexa de negócio
5. **Repositories** para abstração de dados
6. **Requests** para validação centralizada
7. **Migrations** para versionamento do banco

### **Frontend**
1. **Views** organizadas por funcionalidade em `resources/views/`
2. **Components** reutilizáveis em `resources/views/components/`
3. **Layouts** base em `resources/views/layouts/`
4. **CSS** modular em `resources/css/`
5. **JavaScript** organizado em `resources/js/`
6. **Imagens** estáticas em `public/images/`

### **Integração Backend-Frontend**
1. Controllers retornam dados para views
2. APIs REST para comunicação AJAX
3. Validação em ambos os lados
4. Componentes Blade reutilizáveis
