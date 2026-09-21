# Arquivos e comunicação entre as partes do LDM

## 1. Visão geral da arquitetura

O projeto segue o fluxo tradicional do Laravel:

```text
Navegador
   |
   v
routes/web.php
   |
   v
app/Http/Controllers/LDMcontroller.php
| `GET` | `/` | `LDMcontroller@index` | Home pública |
| `GET` | `/login` | `AuthController@showLogin` | Formulário de login |
| `POST` | `/login` | `AuthController@login` | Autentica cliente |
| `GET` | `/register` | `AuthController@showRegister` | Formulário de cadastro |
| `POST` | `/register` | `AuthController@register` | Cria cliente |
| `POST` | `/logout` | `AuthController@logout` | Encerra sessão |
| `GET` | `/cliente` | `LDMcontroller@client` | Área privada do cliente |
   +--> app/Models/*.php --> Banco de dados
| `GET` | `/pesquisa` | closure com `Product` | Busca pública de produtos |
   |
   v
 envia `$dados` e `$pesquisa` para `public/home.blade.php`.

#### `client()`

- consulta produtos ativos;
- envia os dados para `customer/dashboard.blade.php`;
- é acessado somente com o middleware `auth`.

### `app/Http/Controllers/AuthController.php`

Controla cadastro, login e logout.

- `showRegister()` exibe o cadastro;
- `register()` valida e cria o usuário;
- `showLogin()` exibe o login;
- `login()` autentica por e-mail e senha;
- `logout()` encerra a sessão e retorna à área pública.
   |
### `resources/views/layouts/public.blade.php`

Layout das páginas públicas. Inclui:
   +--> resources/js/*.js
Página pública principal do catálogo. Recebe `$dados` e `$pesquisa`, inclui o carrossel e o componente de produtos.

### `resources/views/customer/dashboard.blade.php`

Dashboard privado do cliente. Exibe atalhos da conta, carrossel e catálogo reutilizado.

### `resources/views/auth/login.blade.php` e `register.blade.php`

Formulários de entrada e criação de conta. Ambos enviam requisições com CSRF e mostram erros de validação.

O Vite compila os arquivos frontend e disponibiliza o resultado em `public/build`.

## 2. Rotas e entradas da aplicação


O componente reutiliza subcomponentes em `resources/views/components/catalog` para filtros, barra de resultados e estado vazio.
### `routes/web.php`

Registra as rotas web e conecta URLs às ações correspondentes.

| Método | URL | Destino | Função |
|---|---|---|---|
| `GET` | `/` | `LDMcontroller@index` | Lista produtos |
| `GET` | `/pagina/add` | `LDMcontroller@add` | Exibe cadastro |
| `POST` | `/pagina/add` | `LDMcontroller@store` | Valida e grava produto |
| `GET` | `/product/{product}` | `LDMcontroller@detalls` | Abre detalhes do produto |
| `GET` | `/pagina/detallsProducts` | `LDMcontroller@detalls` | Compatibilidade da tela antiga |
| `GET` | `/pesquisa` | closure com `Product` | Busca produtos |
| `GET` | `/contato` | closure | Exibe contato |
| `GET` | `/search` | closure | Tela de busca legada |

A rota `/product/{product}` utiliza model binding: o valor da URL é convertido em uma instância de `Product` antes de chegar ao controller.

## 3. Controller

### `app/Http/Controllers/LDMcontroller.php`

É o ponto central dos fluxos de catálogo, cadastro e detalhes.

#### `index()`

- consulta `Product`;
- carrega `category`, `brand` e `images`;
- envia `$dados` e `$pesquisa` para `public/home.blade.php`.

#### `add()`

- consulta `Brand::all()`;
- consulta `Category::all()`;
- envia `$marcas` e `$categorias` para `pagina/add.blade.php`.

#### `store(Request $request)`

- recebe o formulário;
- valida dados e arquivos;
- gera o slug;
- cria o produto;
- armazena imagens no disco `public`;
- cria registros em `product_images`;
- executa a gravação dentro de uma transação;
- redireciona para a página inicial.

#### `detalls(?Product $product = null)`

- recebe o produto pelo model binding;
- quando não recebe produto, usa o primeiro produto ativo;
- carrega categoria, marca e imagens;
- busca produtos relacionados da mesma categoria;
- envia `$product` e `$relatedProducts` para `pagina/detallsProducts.blade.php`.

## 4. Models e relacionamentos

### `app/Models/Product.php`

Representa a tabela `product`. Define os campos de atribuição em massa, casts de preço e booleanos, além das relações:

```text
Product belongsTo Category
Product belongsTo Brand
Product hasMany ProductImage
Product hasMany ProductVariant
```

### `app/Models/Category.php`

Representa `categories` e possui muitos produtos.

### `app/Models/Brand.php`

Representa `brands` e possui muitos produtos.

### `app/Models/ProductImage.php`

Representa `product_images`. Guarda:

- `product_id`;
- caminho do arquivo em `image`;
- indicador `is_main`;
- ordem em `position`.

A imagem se comunica com o produto pela relação `belongsTo`.

### `app/Models/ProductVariant.php`

Representa `product_variants` e pertence a um produto. O campo `attributes` é convertido para array.

## 5. Views e comunicação com o backend

### `resources/views/layouts/public.blade.php`

Layout das páginas públicas. Inclui:

- cabeçalho;
- conteúdo da página com `@yield('content')`;
- rodapé;
- entradas do Vite.

### `resources/views/layouts/customer.blade.php`

Layout da área privada do cliente. Reutiliza header e footer, mas organiza os assets da área autenticada.

### `resources/views/public/home.blade.php`

Página principal pública do catálogo. Recebe `$dados` e `$pesquisa`, inclui o carrossel e o componente de produtos.

### `resources/views/customer/dashboard.blade.php`

Dashboard privado do cliente. Exibe atalhos da conta, carrossel e catálogo reutilizado.

### `resources/views/auth/login.blade.php` e `register.blade.php`

Formulários de login e cadastro. Usam CSRF e exibem erros de validação.

### `resources/views/components/cart-itens.blade.php`

Percorre `$dados`. Para cada produto:

1. procura a imagem com `is_main = true`;
2. usa a primeira imagem se não houver principal;
3. usa `public/img/toddy.jpg` como fallback;
4. mostra categoria, marca, nome, descrição e estoque;
5. cria o link `url('/product/'.$dado->id)`.

### `resources/views/pagina/add.blade.php`

Envia o cadastro para `/pagina/add` com `POST`. Usa `multipart/form-data` para enviar arquivos em `images[]`.

Recebe do controller:

- `$marcas`;
- `$categorias`.

### `resources/views/pagina/detallsProducts.blade.php`

Recebe:

- `$product`;
- `$relatedProducts`.

Renderiza os dados do produto e cria a galeria de imagens. Cada miniatura possui `data-image`, que é consumido pelo JavaScript da página.

### `resources/views/product.blade.php`

Também exibe cards de produtos e utiliza a imagem principal relacionada ao produto. Funciona como outra apresentação do catálogo.

## 6. CSS e JavaScript

### CSS

- `resources/css/style.css`: estilos gerais;
- `resources/css/header.css`: cabeçalho;
- `resources/css/footer.css`: rodapé;
- `resources/css/welcome.css`: página inicial;
- `resources/css/cart-itens.css`: cards e resultados;
- `resources/css/add.css`: formulário de cadastro;
- `resources/css/detalssProducts.css`: página de detalhes;
- `resources/css/catalog-carousel.css`: carrossel do catálogo;
- `resources/css/contact.css`: página de contato;
- `resources/css/app.css`: estilos gerais compilados pelo frontend.

### JavaScript

- `resources/js/app.js`: entrada geral do frontend;
- `resources/js/catalog-carousel.js`: comportamento do carrossel;
- `resources/js/cart-itens.js`: comportamento dos cards/listagem;
- `resources/js/detallsProducts.js`: troca a imagem principal ao clicar nas miniaturas.

O script da página de detalhes espera encontrar:

- elementos `.product-thumbnail`;
- o elemento `#product-main-image`;
- o atributo `data-image` nas miniaturas.

## 7. Banco, migrations e arquivos

As migrations ficam em `database/migrations` e são executadas em ordem pelo Laravel:

- `0001_01_01_000000_create_users_table.php`: usuários;
- `0001_01_01_000001_create_cache_table.php`: cache;
- `0001_01_01_000002_create_jobs_table.php`: jobs;
- `2026_09_05_204124_create_categories_table.php`: categorias;
- `2026_09_05_204151_create_brands_table.php`: marcas;
- `2026_09_06_212720_create_product_table.php`: produtos;
- `2026_09_12_204242_create_product_images_table.php`: imagens dos produtos;
- `2026_09_12_204323_create_product_variants_table.php`: variantes.

O fluxo de imagens é:

```text
add.blade.php
   |
   | POST images[]
   v
LDMcontroller@store
   |
   +--> Storage disk public
   |       storage/app/public/products/{id}/arquivo
   |
   +--> ProductImage::create()
           |
           v
       product_images
           |
           v
cart-itens.blade.php / detallsProducts.blade.php
```

O link `public/storage` conecta os arquivos de `storage/app/public` ao navegador.

## 8. Vite e assets compilados

### `vite.config.js`

Define as entradas CSS e JavaScript do projeto, incluindo:

- `resources/css/detalssProducts.css`;
- `resources/js/detallsProducts.js`.

O comando abaixo atualiza o manifest e os arquivos em `public/build`:

```bash
npm run build
```

O layout usa `@vite(...)` para carregar essas entradas durante a renderização das páginas.

## 9. Testes

### `tests/TestCase.php`

Classe base dos testes. Usa `RefreshDatabase` para recriar o banco de testes.

### `tests/Feature/ExampleTest.php`

Testa:

- resposta HTTP da página inicial;
- cadastro de produto com imagens;
- persistência de duas imagens;
- definição da imagem principal e da posição;
- existência dos arquivos no disco público fake.

Comando de validação:

```bash
php artisan test
```

## 10. Resumo de comunicação

```text
Usuário acessa uma URL
        |
        v
routes/web.php identifica o destino
        |
        v
LDMcontroller consulta ou grava via Models
        |
        v
Banco retorna Product, Brand, Category e ProductImage
        |
        v
Controller envia variáveis para uma View Blade
        |
        v
Blade renderiza HTML e referencia CSS/JS via Vite
        |
        v
Navegador exibe o catálogo, busca, cadastro ou detalhes
```
