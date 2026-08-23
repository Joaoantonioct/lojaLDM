# LDM - E-commerce de Moda Feminina

<p align="center">
  <img src="/img/ldm.png" alt="LDM Logo" width="200">
</p>

<p align="center">
  <strong>Loja Virtual de Moda Feminina com Laravel 13</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13+-FF2D20" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4" alt="PHP">
  <img src="https://img.shields.io/badge/Bootstrap-5.3+-7952B3" alt="Bootstrap">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1" alt="MySQL">
</p>

## Sobre o Projeto

LDM é uma plataforma de e-commerce completa para moda feminina, desenvolvida com Laravel 13 e PHP 8.3+. O projeto oferece uma experiência de compra moderna e intuitiva, com painel administrativo completo e funcionalidades avançadas de gerenciamento.

### Características Principais

- **Catálogo de Produtos** com categorias organizadas
- **Sistema de Carrinho** persistente via sessão
- **Cadastro de Clientes** com validação completa
- **Painel Administrativo** protegido por middleware
- **Marketplace na Página Inicial** estilo Mercado Livre
- **Design Responsivo** com Bootstrap 5
- **SEO Amigável** com URLs amigáveis

## Estrutura do Projeto

```
lojaLDM/
|-- app/
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Admin/           # Controllers administrativos
|   |   |   |-- Auth/            # Controllers de autenticação
|   |   |   |-- Api/             # Controllers de API
|   |   |   |-- CartController.php
|   |   |   |-- ProductController.php
|   |   |   |-- CategoryController.php
|   |   |   |-- OrderController.php
|   |   |   |-- UserController.php
|   |   |   |-- HomeController.php
|   |   |-- Middleware/
|   |   |   |-- AdminMiddleware.php
|-- resources/
|   |-- views/
|   |   |-- layouts/
|   |   |   |-- main.blade.php
|   |   |-- components/
|   |   |   |-- navbar.blade.php
|   |   |   |-- footer.blade.php
|   |   |   |-- admin-navbar.blade.php
|   |   |-- admin/               # Views administrativas
|   |   |-- auth/               # Views de autenticação
|   |   |-- cart/               # Views do carrinho
|   |   |-- products/           # Views de produtos
|   |   |-- categories/         # Views de categorias
|   |   |-- orders/             # Views de pedidos
|   |   |-- users/              # Views de usuários
|   |   |-- welcome.blade.php   # Página inicial marketplace
|-- public/
|   |-- css/style.css
|   |-- js/script.js
|   |-- img/                    # Imagens do projeto
|-- routes/
|   |-- web.php                 # Rotas principais
|   |-- admin.php               # Rotas administrativas
```

## Funcionalidades Implementadas

### 1. Sistema de Produtos
- **Listagem de Produtos** com cards visuais
- **Cadastro de Produtos** com upload de imagem
- **Edição de Produtos** com preview de imagem
- **Detalhes do Produto** com informações completas
- **Categorias** com ícones e cores personalizadas

### 2. Carrinho de Compras
- **Adicionar ao Carrinho** via formulários POST
- **Gestão de Sessão** para persistência
- **Atualizar Quantidades** em tempo real
- **Remover Itens** com confirmação
- **Limpar Carrinho** com um clique
- **Contador Dinâmico** no navbar

### 3. Sistema de Usuários
- **Registro Completo** com validação
- **Dados Pessoais**: nome, CPF, data nascimento
- **Endereço Completo**: CEP, rua, número, complemento
- **Preferências**: newsletter e termos
- **Login** com redirecionamento inteligente

### 4. Painel Administrativo
- **Proteção por Middleware** AdminMiddleware
- **Dashboard** com estatísticas
- **Gestão de Produtos** completa
- **Gestão de Categorias**
- **Gestão de Pedidos**
- **Gestão de Usuários**
- **Navbar Administrativo** exclusivo

### 5. Marketplace na Página Inicial
- **Banner Principal** com call-to-action
- **Categorias em Destaque** com ícones
- **Produtos em Destaque** com avaliação
- **Seção de Promoções** com countdown
- **Newsletter** com captura de leads
- **CSS Organizado** com comentários

## Tecnologias Utilizadas

### Backend
- **Laravel 13** - Framework PHP
- **PHP 8.3+** - Linguagem principal
- **MySQL 8.0+** - Banco de dados
- **Eloquent ORM** - Mapeamento objeto-relacional
- **Blade Templates** - Sistema de templates

### Frontend
- **Bootstrap 5.3** - Framework CSS
- **Bootstrap Icons** - Ícones vetoriais
- **Font Awesome 6.5.1** - Ícones adicionais para navbar
- **JavaScript Vanilla** - Interatividade
- **CSS Personalizado** - Estilos customizados
- **Design Responsivo** - Mobile-first

### Ferramentas
- **Composer** - Gerenciador de dependências
- **Artisan** - CLI do Laravel
- **Middleware** - Filtros de requisição
- **Session** - Armazenamento temporário

## Instalação e Configuração

### Pré-requisitos
- PHP 8.3 ou superior
- Composer
- MySQL 8.0 ou superior
- Servidor web (Apache/Nginx)

### Passos para Instalação

1. **Clone o repositório**
```bash
git clone <repositorio-url> lojaLDM
cd lojaLDM
```

2. **Instale as dependências**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loja_ldm
DB_USERNAME=root
DB_PASSWORD=
```

5. **Execute as migrações**
```bash
php artisan migrate
```

6. **Inicie o servidor**
```bash
php artisan serve
```

7. **Acesse a aplicação**
```
http://127.0.0.1:8000
```

## Rotas Principais

### Rotas Públicas
- `/` - Página inicial (marketplace)
- `/produtos` - Listagem de produtos
- `/carrinho` - Carrinho de compras
- `/login` - Formulário de login
- `/register` - Formulário de registro

### Rotas do Carrinho
- `GET /cart/index` - Exibir carrinho
- `POST /cart/add` - Adicionar item
- `DELETE /cart/remove/{id}` - Remover item
- `PUT /cart/update/{id}` - Atualizar quantidade
- `POST /cart/clear` - Limpar carrinho

### Rotas Administrativas (protegidas)
- `/admin/dashboard` - Dashboard admin
- `/admin/products/*` - Gestão de produtos
- `/admin/categories/*` - Gestão de categorias
- `/admin/orders/*` - Gestão de pedidos
- `/admin/users/*` - Gestão de usuários

## Controllers Implementados

### Controllers Principais
- `HomeController` - Página inicial
- `ProductController` - Gestão de produtos
- `CategoryController` - Gestão de categorias
- `CartController` - Sistema de carrinho
- `OrderController` - Gestão de pedidos
- `UserController` - Gestão de usuários

### Controllers de Autenticação
- `RegisterController` - Registro de usuários
- `LoginController` - Autenticação (planejado)

### Controllers Administrativos
- `ProductAdminController` - Produtos no admin
- `CategoryAdminController` - Categorias no admin
- `OrderAdminController` - Pedidos no admin
- `UserAdminController` - Usuários no admin

## Views Implementadas

### Layout Principal
- `layouts/main.blade.php` - Layout base
- `components/navbar.blade.php` - Barra de navegação
- `components/footer.blade.php` - Rodapé

### Views Públicas
- `welcome.blade.php` - Marketplace inicial
- `products/index.blade.php` - Listagem produtos
- `cart/index.blade.php` - Carrinho
- `auth/login.blade.php` - Login
- `auth/register.blade.php` - Registro

### Componentes Implementados
- `components/navbar.blade.php` - Navbar estilo Mercado Livre (20/04/2026)
  - Logo com link para home
  - Barra de pesquisa funcional
  - Banner promocional "120% do CDI meli+"
  - Links de categorias (Categorias, Ofertas, Cupons, etc.)
  - Ações do usuário (Criar conta, Entrar, Compras, Carrinho)
  - Totalmente responsiva
  - Ícones Font Awesome integrados

### Views Administrativas
- `admin/products/index.blade.php` - Gestão produtos
- `admin/dashboard.blade.php` - Dashboard

## Middleware

### AdminMiddleware
Protege as rotas administrativas, verificando se:
- Usuário está autenticado
- Usuário possui tipo 'admin'
- Redireciona para login se não autorizado

## CSS e Estilização

### CSS Personalizado (`public/css/style.css`)
- Cards com efeitos hover
- Design moderno e limpo
- Fontes otimizadas
- Animações suaves

### CSS da Página Welcome
- Gradientes modernos
- Cards de marketplace
- Layout responsivo
- Comentários detalhados

## Funcionalidades Futuras

### Roadmap
- [ ] Sistema de pagamento integrado
- [ ] Busca avançada de produtos
- [ ] Sistema de avaliações
- [ ] Gestão de estoque automática
- [ ] API REST completa
- [ ] Sistema de favoritos
- [ ] Histórico de compras
- [ ] Recuperação de senha
- [ ] Integração com correios

## Contribuição

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Contato

- **Projeto**: LDM - Moda Feminina
- **Framework**: Laravel 13
- **Versão**: 1.0.0
- **Desenvolvedor**: [Seu Nome]

---

**LDM** - Transformando moda em experiência digital!
