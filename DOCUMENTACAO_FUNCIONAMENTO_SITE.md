# Funcionamento do site LDM

## 1. Visão geral

O LDM possui três áreas principais:

- **pública:** catálogo, pesquisa e detalhes dos produtos;
- **autenticação:** cadastro, login e logout;
- **privada:** área do cliente em `/cliente`.

Os componentes visuais, como header, footer, carrossel e cards, são compartilhados quando não dependem de autenticação.

## 2. Fluxo público

### Página inicial

`GET /`

1. `LDMcontroller@index` consulta os produtos.
2. A view `resources/views/public/home.blade.php` monta a página.
3. `components.catalog-carousel` exibe os banners.
4. `components.cart-itens` exibe a listagem.
5. Cada card abre `/product/{id}`.

### Pesquisa

`GET /pesquisa?search=termo`

A busca consulta:

- nome;
- descrição;
- marca;
- categoria e categoria-pai.

SKU e código de barras são identificadores internos e não aparecem nem são pesquisáveis no site.

Também aceita filtros por:

- categorias;
- marcas;
- preço mínimo e máximo;
- disponibilidade em estoque.

A pesquisa reutiliza a view pública e os componentes de catálogo.

### Detalhes do produto

`GET /product/{product}`

A página mostra dados do produto, imagens, miniaturas, estoque, descrição e produtos relacionados. O modal de compartilhamento fica separado em:

```text
resources/views/components/product/share-modal.blade.php
```

## 3. Fluxo de autenticação

### Cadastro

`GET /register` mostra o formulário.

`POST /register` valida:

- nome obrigatório;
- e-mail válido e único;
- senha com pelo menos 8 caracteres;
- confirmação da senha.

Depois de criar o usuário, o sistema:

1. grava os dados em `users`;
2. aplica hash na senha pelo model `User`;
3. inicia a sessão;
4. regenera o ID da sessão;
5. redireciona para `/cliente`.

### Login

`GET /login` mostra o formulário.

`POST /login` usa `Auth::attempt`. Se as credenciais estiverem corretas, o sistema regenera a sessão e redireciona para `/cliente`.

### Logout

`POST /logout` encerra a sessão, invalida os dados de sessão, regenera o token CSRF e redireciona para `/`.

## 4. Área privada do cliente

`GET /cliente`

Essa rota usa o middleware `auth`. Usuários não autenticados são direcionados ao login.

A página usa:

```text
resources/views/layouts/customer.blade.php
resources/views/customer/dashboard.blade.php
resources/css/client.css
```

## 5. Integração de produtos com o PDV

As rotas autenticadas `/api/pdv/*` usam o segredo `PDV_SYNC_TOKEN` no cabeçalho Bearer. O site gera os identificadores centrais: `POST /api/pdv/identifiers` reserva um SKU no padrão `PDV-000001` e um código de barras EAN-13 interno com prefixo de `20` a `29`.

O cadastro de produto no site persiste os códigos sem mostrá-los na interface e cria uma mensagem de saída consultável em `GET /api/pdv/products/pending`, cuja resposta é `{data: [...]}`; o PDV confirma cada importação concluída com `POST /api/pdv/products/ack`. No sentido inverso, o PDV reserva identificadores e envia `POST /api/pdv/products`, idempotente por `eventId`; categorias são associadas pelo nome existente ou criadas quando não existem. Se um SKU já existir no PDV com outro código de barras, o cadastro antigo é preservado e o evento permanece pendente.

Produtos que já existiam no site não são associados automaticamente por nome. O PDV pode consultar `GET /api/pdv/products/review` e vincular uma seleção explícita por `POST /api/pdv/products/link`, informando o ID do produto e os códigos exatos do PDV. A API recusa códigos já vinculados ou reservados para outra associação. SKU e código de barras não são expostos na interface nem no catálogo do site.

O carrinho autenticado também oferece um fluxo temporário de pedido de teste, sem pagamento. `POST /carrinho/finalizar` valida o estoque sob bloqueio transacional, calcula os preços vigentes, baixa o saldo do site e cria um evento idempotente. O PDV consulta `GET /api/pdv/site-orders/pending`, registra o pedido e seus itens separadamente das vendas do caixa, aplica uma movimentação de saída na unidade configurada e confirma com `POST /api/pdv/site-orders/ack`. Também consulta `GET /api/pdv/site-orders/history` de forma paginada para conciliar pedidos anteriores já confirmados. O relatório mensal identifica os pedidos do site e seus valores informativos, sem incluí-los nos pagamentos, faturamento recebido ou conferência de caixa. Estoque insuficiente ou SKU sem vínculo bloqueia a confirmação. Remova o fluxo de teste quando o pagamento real for integrado.

O dashboard reutiliza o carrossel e a listagem de produtos, mas seu layout pertence à área privada.

## 5. Cadastro de produtos

### Formulário

`GET /pagina/add`

### Gravação

`POST /pagina/add`

O controller valida produto, marca, categoria, preço, estoque, descrição e imagens. A gravação usa transação:

```text
Product
ProductImage
```

As imagens são guardadas em:

```text
storage/app/public/products/{id}
```

Para torná-las acessíveis pelo navegador:

```bash
php artisan storage:link
```

## 6. Assets frontend

O Vite compila os arquivos CSS e JavaScript usados pelas views.

Durante o desenvolvimento:

```bash
npm run dev
```

Para produção:

```bash
npm run build
```

O layout público e o layout do cliente carregam apenas os assets relacionados às suas áreas, enquanto componentes compartilhados continuam disponíveis nos dois fluxos.
