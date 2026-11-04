# LDM

Catálogo de produtos desenvolvido com Laravel, Blade, Eloquent, Vite, CSS e JavaScript.

## Funcionalidades atuais

- catálogo público de produtos;
- pesquisa por nome, descrição, marca e categoria, com sinônimos para roupas e calçados e busca por subcategorias (códigos internos não são exibidos nem pesquisáveis no site);
- filtros por categoria, marca, estado de vestuário (novo/seminovo/brechó), preço e estoque;
- ordenação dos cards por preço;
- página de detalhes com galeria e produtos relacionados;
- cadastro de produtos e imagens;
- grade de tamanhos separada por tipo de peça: blusas/roupas (PP a XGG), calças/leggings (34 a 60) e calçados (33 a 46), com estoque por tamanho;
- promoções por percentual, com período agendado e preço promocional no catálogo;
- integração autenticada com o PDV para criação e sincronização de produtos, estoque, preço e promoções;
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
- `product_variants`;
- `product_promotions` e `pdv_sync_receipts`, usadas pela sincronização de promoções do PDV.

Depois de enviar uma nova migration para a Hostinger, execute-a no terminal da hospedagem, dentro da pasta do Laravel e com o `.env` apontando para o banco de produção:

```bash
php artisan migrate:status
php artisan migrate --force
php artisan migrate:status
```

Confirme que `2026_11_01_000002_create_product_promotions_table` aparece como **Ran** no status. Essa migration cria `product_promotions` e `pdv_sync_receipts`. Se aparecer como **Pending**, o deploy ainda não aplicou a migration; não crie a tabela manualmente nem use `migrate:fresh`, pois isso pode apagar dados.

As imagens são armazenadas em `storage/app/public` e exibidas pelo link `public/storage`.

## Integração com o PDV

Configure `PDV_SYNC_TOKEN` no `.env` com um segredo aleatório de pelo menos 32 caracteres; use o mesmo valor na variável `PDV_SITE_SYNC_TOKEN` do servidor PDV e mantenha ambos fora do controle de versão. Depois, aplique a migration:

```bash
php artisan migrate --force
```

A API exige um cabeçalho `Authorization: Bearer` com o segredo configurado em `PDV_SYNC_TOKEN` em todas as rotas `/api/pdv/*`. O site gera SKUs `PDV-000001` e códigos de barras internos EAN-13 com prefixo `20` a `29`; os códigos são mantidos fora das telas e do catálogo.

- `POST /api/pdv/identifiers`: reserva e retorna `{sku, barcode}` para um novo produto originado no PDV.
- `POST /api/pdv/products`: cria idempotentemente no site o produto PDV, usando identificadores reservados.
- `GET /api/pdv/products/pending` retorna os produtos em `{data: [...]}`; `POST /api/pdv/products/ack` confirma cada importação concluída pelo PDV.
- `GET /api/pdv/site-orders/pending` entrega pedidos de teste do site com data e valores dos itens; `POST /api/pdv/site-orders/ack` confirma a importação no PDV.
- `GET /api/pdv/site-orders/history?afterId=0` fornece o histórico paginado para conciliar pedidos já confirmados anteriormente.
- `GET /api/pdv/products/review`: lista os produtos existentes no site sem seus códigos, para revisão explícita.
- `POST /api/pdv/products/link`: recebe `{productId, sku, barcode}` e vincula exatamente os códigos escolhidos, recusando conflitos. Não há correspondência automática por nome.
- `POST /api/pdv/sync`: recebe eventos idempotentes de estoque, preço e promoção localizando o produto por SKU.

O cadastro do site gera os identificadores e coloca o produto na fila de saída do PDV. Produtos preexistentes só são vinculados após revisão explícita; o site preserva seus dados e não tenta encontrá-los automaticamente pelo nome.

O carrinho autenticado permite registrar **pedidos temporários de teste**, sem processar pagamentos. Ao confirmar, o site valida e baixa o estoque em uma transação e coloca o pedido na fila idempotente para o PDV aplicar a baixa na unidade configurada. O PDV processa essa fila e reconcilia o histórico periodicamente; os pedidos ficam identificados separadamente no relatório e não são registrados como pagamentos nem alteram o caixa. Produtos sem estoque são recusados e exibidos como “Sem estoque”. Remova esse fluxo de teste quando integrar um sistema de pagamento real.

Na importação para o PDV, um SKU já existente só pode ser atualizado quando o código de barras também corresponde. Em caso de conflito, o item antigo é preservado e o evento permanece pendente para correção.

O PDV mantém uma fila local e sincroniza as movimentações da unidade configurada. O primeiro alinhamento de estoque e preço é iniciado pelo administrador na página **Promoções** do PDV; essa ação substitui os valores atuais do site pelos valores da unidade principal do PDV. Use HTTPS para qualquer comunicação fora de `localhost`.

## Validação rápida

```bash
php artisan route:list
php artisan view:cache
php artisan migrate:status
npm run build
php artisan test
```
