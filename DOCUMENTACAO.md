# Documentação de Desenvolvimento - Loja Virtual LDM

## Início do Projeto: 19/04/2026

## Regra Importante
**Sempre que realizar qualquer alteração ou criação de arquivo/migration/view/controller, deve atualizar imediatamente esta documentação.** Isso garante que todo o trabalho fique documentado e facilite manutenções futuras.

---

## Histórico de Desenvolvimento

### 19/04/2026 - Inicialização do Projeto
- **Status**: Estrutura inicial criada
- **Responsável**: Sistema
- **Descrição**: 
  - Verificação de pastas existentes
  - Criação de estrutura completa para loja virtual
  - Configuração inicial do ambiente Laravel

---

## Atualizações de Desenvolvimento

### 27/04/2026 - Organização das Pastas do Projeto
- **Arquivos Afetados**: 
  - Pastas removidas: `app\Repositories`, `app\Services`, `resources\css`, `resources\js`, `ESTRUTURA_PASTAS.md`, `GUIA_MODELS.md`
  - Pastas criadas: `app\Http\Middleware`, `app\Http\Requests`, `resources\lang`, `public\css`, `public\js`, `public\images`
- **Descrição da Mudança**: Organização completa das pastas do projeto para seguir a estrutura padrão do Laravel, removendo pastas desnecessárias e criando as pastas que faltavam.
- **Impacto no Sistema**: Estrutura do projeto agora segue o padrão Laravel oficial, facilitando desenvolvimento e manutenção
- **Responsável**: Sistema

### 27/04/2026 - Limpeza Completa dos Arquivos do Projeto
- **Arquivos Afetados**: 
  - `public/css/style.css` (limpo)
  - `public/css/welcome.css` (limpo)
  - `public/css/products.css` (limpo)
  - `public/css/cart.css` (limpo)
  - `public/css/auth.css` (limpo)
  - `public/css/admin.css` (limpo)
  - `resources/views/welcome.blade.php` (limpo)
  - `resources/views/layouts/main.blade.php` (limpo)
  - `resources/views/components/navbar.blade.php` (limpo)
  - `resources/views/components/footer.blade.php` (limpo)
  - `resources/views/auth/login.blade.php` (limpo)
  - `resources/views/auth/register.blade.php` (limpo)
  - `resources/views/products/index.blade.php` (limpo)
  - `resources/views/cart/index.blade.php` (limpo)
  - `resources/views/users/index.blade.php` (limpo)
  - `resources/views/categories/index.blade.php` (limpo)
  - `resources/views/orders/index.blade.php` (limpo)
  - `resources/views/checkout/index.blade.php` (limpo)
  - `resources/views/busca.blade.php` (limpo)
  - `resources/views/contato.blade.php` (limpo)
  - `resources/views/produto.blade.php` (limpo)
  - `resources/views/categoria.blade.php` (limpo)
  - `resources/views/sobre.blade.php` (limpo)
  - `app/Http/Controllers/Controller.php` (limpo)
  - `app/Http/Controllers/HomeController.php` (limpo)
  - `routes/web.php` (recriado com conteúdo básico)
  - `routes/api.php` (recriado com conteúdo básico)
  - `routes/console.php` (recriado com conteúdo básico)
  - `app/Models/User.php` (recriado com conteúdo básico)
  - Pastas removidas: `app\View`, `resources\views\orders`, `resources\views\components`
- **Descrição da Mudança**: Limpeza completa de todo o conteúdo de arquivos CSS, views, controllers, rotas e models do projeto, mantendo apenas estrutura básica. Remoção de pastas duplicadas e desnecessárias.
- **Impacto no Sistema**: Projeto limpo e pronto para novo desenvolvimento do zero, com estrutura básica mantida
- **Responsável**: Sistema

---

### [Data] - [Título da Alteração]
- **Arquivos Afetados**: 
- **Descrição da Mudança**: 
- **Impacto no Sistema**: 
- **Responsável**: 

---

## Estrutura da Documentação

### Para cada alteração, incluir:
1. **Data da alteração**
2. **Arquivo(s) afetado(s)**
3. **Descrição detalhada da mudança**
4. **Impacto no sistema**
5. **Responsável pela alteração**

### Exemplo de preenchimento:

### 19/04/2026 - Criação de Estrutura de Pastas
- **Arquivos Afetados**: 
  - `resources/views/products/` (nova)
  - `resources/views/users/` (nova)
  - `resources/views/orders/` (nova)
  - `app/Http/Middleware/` (nova)
  - `app/Http/Requests/` (nova)
  - `app/Services/` (nova)
  - `app/Repositories/` (nova)
  - `public/images/` (nova)
  - `storage/app/public/products/` (nova)
- **Descrição da Mudança**: Criação de estrutura completa de pastas para loja virtual seguindo padrões Laravel
- **Impacto no Sistema**: Estrutura base pronta para desenvolvimento de funcionalidades da loja
- **Responsável**: Sistema

---

## Checklist de Desenvolvimento

### Funcionalidades a Implementar:
- [ ] Autenticação de usuários
- [ ] Cadastro de produtos
- [ ] Gestão de categorias
- [ ] Carrinho de compras
- [ ] Processamento de pedidos
- [ ] Sistema de pagamento
- [ ] Painel administrativo
- [ ] Sistema de avaliações
- [ ] Busca e filtros
- [ ] Gestão de estoque

### Models a Criar:
- [ ] User
- [ ] Product
- [ ] Category  
- [ ] Cart
- [ ] Order
- [ ] OrderItem
- [ ] Review
- [ ] Address

### Controllers a Criar:
- [ ] ProductController
- [ ] CategoryController
- [ ] CartController
- [ ] OrderController
- [ ] UserController
- [ ] AdminController

### Migrations a Criar:
- [ ] create_users_table
- [ ] create_products_table
- [ ] create_categories_table
- [ ] create_carts_table
- [ ] create_orders_table
- [ ] create_order_items_table
- [ ] create_reviews_table
- [ ] create_addresses_table

---

## Padrões e Convenções

### Nomenclatura:
- **Models**: Singular e PascalCase (Product, Category)
- **Controllers**: PascalCase + sufixo Controller (ProductController)
- **Migrations**: snake_case + sufixo _table (create_products_table)
- **Views**: snake_case (product_list, product_detail)
- **Routes**: plural e snake_case (/products, /categories)

### Padrões de Código:
- Seguir PSR-12
- Usar Type Hints
- Documentar com PHPDoc
- Validação via Form Requests
- Repositories para acesso a dados
- Services para lógica de negócio

---

## Ambiente de Desenvolvimento

### Requisitos:
- PHP 8.3+
- Composer
- Node.js + NPM
- Banco de dados (MySQL/PostgreSQL/SQLite)

### Comandos Úteis:
```bash
# Instalar dependências
composer install
npm install

# Rodar migrações
php artisan migrate

# Iniciar servidor
php artisan serve

# Compilar assets
npm run dev

# Criar link storage
php artisan storage:link

# Gerar key
php artisan key:generate
```

---

## Notas Importantes

1. **Versionamento**: Todo código deve ser versionado com Git
2. **Testes**: Criar testes unitários para funcionalidades críticas
3. **Segurança**: Validar todos os inputs e sanitizar dados
4. **Performance**: Otimizar queries e usar cache quando necessário
5. **Documentação**: Manter esta documentação sempre atualizada

---

## Contato e Suporte

- **Desenvolvedor**: [Nome]
- **Email**: [Email]
- **Projeto**: Loja Virtual LDM
- **Versão**: 1.0.0
