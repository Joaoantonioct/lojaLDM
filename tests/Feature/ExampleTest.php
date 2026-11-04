<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_cart_page_is_available_to_guests(): void
    {
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Carrinho de compras');
    }

    public function test_customer_favorites_have_horizontal_carousel_controls(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('cliente'))
            ->assertOk()
            ->assertSee('data-active-product-ids=""', false)
            ->assertSee('class="client-favorites-carousel"', false)
            ->assertSee('class="client-favorites-grid"', false)
            ->assertSee('aria-label="Ver favoritos anteriores"', false)
            ->assertSee('aria-label="Ver próximos favoritos"', false);
    }

    public function test_customer_dashboard_exposes_only_active_catalog_product_ids(): void
    {
        $category = Category::create([
            'name' => 'Categoria de estado do catálogo',
            'slug' => 'categoria-de-estado-do-catalogo',
        ]);
        $activeProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Produto ativo do catálogo',
            'slug' => 'produto-ativo-do-catalogo',
            'price' => '19.90',
            'qty' => 1,
            'active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Produto inativo do catálogo',
            'slug' => 'produto-inativo-do-catalogo',
            'price' => '19.90',
            'qty' => 1,
            'active' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cliente'))
            ->assertOk()
            ->assertSee('data-active-product-ids="'.$activeProduct->id.'"', false)
            ->assertDontSee('Produto inativo do catálogo');
    }

    public function test_customer_search_shortcut_targets_the_header_search_field(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('cliente'))
            ->assertOk()
            ->assertSee('id="site-header"', false)
            ->assertSee('href="'.route('cliente').'#site-header"', false);
    }

    public function test_clothing_category_menu_links_to_new_and_seminovo_products(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Novos')
            ->assertSee('Seminovos')
            ->assertSee(route('pesquisa', [
                'search' => 'Roupas',
                'conditions' => ['novo'],
            ]))
            ->assertSee(route('pesquisa', [
                'search' => 'Roupas',
                'conditions' => ['seminovo'],
            ]))
            ->assertSee(route('pesquisa', [
                'search' => 'Roupas',
                'conditions' => ['brecho'],
            ]));
    }

    public function test_footer_displays_both_brand_logos(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(asset('img/logo1.png'))
            ->assertSee(asset('img/michele1.png'))
            ->assertSee('data-social-icon="facebook"', false)
            ->assertSee('data-social-icon="instagram"', false)
            ->assertSee('data-social-icon="whatsapp"', false)
            ->assertSee('href="https://www.facebook.com/"', false)
            ->assertSee('href="https://www.instagram.com/"', false)
            ->assertSee('href="https://www.whatsapp.com/"', false)
            ->assertSee('© '.date('Y').' LDM. Todos os direitos reservados.');
    }

    public function test_product_creation_page_keeps_the_product_form_and_image_uploader(): void
    {
        Category::create([
            'name' => 'Calçados',
            'slug' => 'calcados-formulario-teste',
        ]);

        $this->get('/pagina/add')
            ->assertOk()
            ->assertSee('Adicionar produto')
            ->assertSee('data-product-create-form', false)
            ->assertSee('name="images[]"', false)
            ->assertSee('data-image-dropzone', false)
            ->assertSee('data-inline-panel="category"', false)
            ->assertSee('data-inline-panel="subcategory"', false)
            ->assertSeeInOrder([
                'id="category_id"',
                'id="subcategory_id"',
                'data-condition-field',
            ], false)
            ->assertSee('data-apparel="true"', false)
            ->assertSee('value="brecho"', false)
            ->assertSee('data-brand-panel', false);
    }

    public function test_cellphone_filter_finds_products_in_the_singular_cellphone_category(): void
    {
        $category = Category::create([
            'name' => 'celular',
            'slug' => 'celular-filtro-teste',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Smartphone celular teste',
            'slug' => 'smartphone-celular-teste',
            'price' => '999.90',
            'qty' => 5,
        ]);

        $this->get(route('pesquisa', ['search' => 'celular']))
            ->assertOk()
            ->assertSee('Smartphone celular teste');
    }

    public function test_general_clothing_search_finds_apparel_by_its_category(): void
    {
        $clothing = Category::create([
            'name' => 'Vestuário teste',
            'slug' => 'vestuario-pesquisa-teste',
        ]);
        $dress = Category::create([
            'name' => 'Vestidos',
            'slug' => 'vestidos-pesquisa-teste',
            'parent_id' => $clothing->id,
        ]);
        $shoes = Category::create([
            'name' => 'Calçados',
            'slug' => 'calcados-pesquisa-teste',
        ]);
        Product::create([
            'category_id' => $dress->id,
            'name' => 'Modelo estampado pesquisa',
            'slug' => 'modelo-estampado-pesquisa',
            'price' => '79.90',
            'qty' => 2,
        ]);
        Product::create([
            'category_id' => $shoes->id,
            'name' => 'Sapato fora da busca',
            'slug' => 'sapato-fora-da-busca',
            'price' => '99.90',
            'qty' => 1,
        ]);

        $this->get(route('pesquisa', ['search' => 'roupas']))
            ->assertOk()
            ->assertSee('Modelo estampado pesquisa')
            ->assertSee('Sapato fora da busca');
    }

    public function test_footwear_search_expands_synonyms_and_includes_subcategories(): void
    {
        $shoes = Category::create([
            'name' => 'Calçados',
            'slug' => 'calcados-sinonimos-teste',
        ]);
        $sneakers = Category::create([
            'name' => 'Tênis',
            'slug' => 'tenis-sinonimos-teste',
            'parent_id' => $shoes->id,
        ]);
        Product::create([
            'category_id' => $sneakers->id,
            'name' => 'Modelo urbano sem termo de busca',
            'slug' => 'modelo-urbano-sem-termo',
            'price' => '129.90',
            'qty' => 1,
        ]);

        $this->get(route('pesquisa', ['search' => 'sapatos']))
            ->assertOk()
            ->assertSee('Modelo urbano sem termo de busca');
    }

    public function test_customer_search_uses_the_same_apparel_expansion_as_catalog_search(): void
    {
        $category = Category::create([
            'name' => 'Moda feminina',
            'slug' => 'moda-feminina-pesquisa-cliente',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Peça exclusiva pesquisa cliente',
            'slug' => 'peca-exclusiva-pesquisa-cliente',
            'price' => '59.90',
            'qty' => 1,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cliente', ['search' => 'roupas']))
            ->assertOk()
            ->assertSee('Peça exclusiva pesquisa cliente');
    }

    public function test_customer_interest_shortcut_shows_saved_products_and_similar_items(): void
    {
        $category = Category::create([
            'name' => 'Calçados',
            'slug' => 'calcados-interesses-teste',
        ]);
        $sneakers = Category::create([
            'name' => 'Tênis',
            'slug' => 'tenis-interesses-teste',
            'parent_id' => $category->id,
        ]);
        $boots = Category::create([
            'name' => 'Botas',
            'slug' => 'botas-interesses-teste',
            'parent_id' => $category->id,
        ]);
        $brand = Brand::create([
            'name' => 'Marca interesses teste',
            'slug' => 'marca-interesses-teste',
        ]);
        $interested = Product::create([
            'category_id' => $sneakers->id,
            'brand_id' => $brand->id,
            'name' => 'Tênis salvo no interesse',
            'slug' => 'tenis-salvo-no-interesse',
            'price' => '100.00',
            'qty' => 1,
        ]);
        Product::create([
            'category_id' => $boots->id,
            'name' => 'Bota da mesma categoria',
            'slug' => 'bota-da-mesma-categoria',
            'price' => '120.00',
            'qty' => 1,
        ]);
        Product::create([
            'category_id' => Category::create([
                'name' => 'Acessórios',
                'slug' => 'acessorios-interesses-teste',
            ])->id,
            'brand_id' => $brand->id,
            'name' => 'Produto da mesma marca',
            'slug' => 'produto-da-mesma-marca-interesse',
            'price' => '80.00',
            'qty' => 1,
        ]);
        Product::create([
            'category_id' => Category::create([
                'name' => 'Papelaria',
                'slug' => 'papelaria-interesses-teste',
            ])->id,
            'name' => 'Produto sem relação',
            'slug' => 'produto-sem-relacao-interesse',
            'price' => '20.00',
            'qty' => 1,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cliente', ['interest_products' => [$interested->id]]))
            ->assertOk()
            ->assertSee('Produtos que podem interessar')
            ->assertSee('Tênis salvo no interesse')
            ->assertSee('Bota da mesma categoria')
            ->assertSee('Produto da mesma marca')
            ->assertDontSee('Produto sem relação');
    }

    public function test_customer_interest_shortcut_falls_back_to_available_products_when_no_search_match_exists(): void
    {
        $category = Category::create([
            'name' => 'Papelaria fallback teste',
            'slug' => 'papelaria-fallback-interesses-teste',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Produto disponível de recomendação',
            'slug' => 'produto-disponivel-recomendacao',
            'price' => '10.00',
            'qty' => 1,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('cliente', ['interest_search' => 'termo sem correspondência']))
            ->assertOk()
            ->assertSee('Produtos que podem interessar')
            ->assertSee('Produto disponível de recomendação')
            ->assertDontSee('Nenhum produto encontrado');
    }

    public function test_search_can_filter_products_by_new_or_seminovo_condition(): void
    {
        $category = Category::create([
            'name' => 'Roupas',
            'slug' => 'roupas-condicao-teste',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Camiseta nova teste',
            'slug' => 'camiseta-nova-teste',
            'price' => '49.90',
            'qty' => 3,
            'condition' => 'novo',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Camiseta seminova teste',
            'slug' => 'camiseta-seminova-teste',
            'price' => '29.90',
            'qty' => 1,
            'condition' => 'seminovo',
        ]);

        $this->get(route('pesquisa', [
            'search' => 'Roupas',
            'conditions' => ['seminovo'],
        ]))
            ->assertOk()
            ->assertSee('Camiseta seminova teste')
            ->assertDontSee('Camiseta nova teste')
            ->assertSee('name="conditions[]" value="seminovo" checked', false);
    }

    public function test_search_can_filter_products_from_brecho(): void
    {
        $category = Category::create([
            'name' => 'Roupas brechó',
            'slug' => 'roupas-brecho-teste',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Jaqueta de brechó',
            'slug' => 'jaqueta-de-brecho',
            'price' => '35.00',
            'qty' => 1,
            'condition' => 'brecho',
        ]);

        $this->get(route('pesquisa', [
            'search' => 'Roupas brechó',
            'conditions' => ['brecho'],
        ]))
            ->assertOk()
            ->assertSee('Jaqueta de brechó')
            ->assertSee('Estado: Brechó')
            ->assertSee('name="conditions[]" value="brecho" checked', false);
    }

    public function test_search_page_uses_collapsed_filters_without_the_home_carousel(): void
    {
        $category = Category::create([
            'name' => 'Categoria busca responsiva',
            'slug' => 'categoria-busca-responsiva',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Produto busca responsiva',
            'slug' => 'produto-busca-responsiva',
            'price' => '19.90',
            'qty' => 2,
        ]);

        $this->get(route('pesquisa', ['search' => 'Produto busca responsiva']))
            ->assertOk()
            ->assertSee('Produto busca responsiva')
            ->assertSee('<div class="search-layout has-filters">', false)
            ->assertSee('<details class="search-filter-disclosure">', false)
            ->assertDontSee('class="catalog-carousel"', false);
    }

    public function test_product_links_open_at_the_product_content_below_the_site_header(): void
    {
        $category = Category::create([
            'name' => 'Categoria âncora produto',
            'slug' => 'categoria-ancora-produto',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produto âncora',
            'slug' => 'produto-ancora',
            'price' => '19.90',
            'qty' => 1,
        ]);

        $this->get(route('pesquisa', ['search' => 'Produto âncora']))
            ->assertOk()
            ->assertSee('href="'.route('product.show', $product).'#product-content"', false);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('id="product-content"', false);
    }

    public function test_product_page_shows_all_sizes_and_disables_unavailable_options(): void
    {
        $category = Category::create([
            'name' => 'Calças legging',
            'slug' => 'calcas-legging',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Calça Legging Estoque',
            'slug' => 'calca-legging-estoque',
            'price' => '50.00',
            'qty' => 2,
            'size_type' => 'trousers',
        ]);
        $product->variants()->create([
            'name' => '36',
            'sku' => 'SIZE-36-TEST',
            'qty' => 2,
            'attributes' => ['size' => '36'],
            'active' => true,
        ]);
        $product->variants()->create([
            'name' => '38',
            'sku' => 'SIZE-38-TEST',
            'qty' => 0,
            'attributes' => ['size' => '38'],
            'active' => true,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Estoque: 2 unidades disponíveis')
            ->assertSee('Tamanho: <span data-size-selection>Escolha</span>', false)
            ->assertDontSee('data-selected-size-stock', false)
            ->assertSee('data-variant-id=', false)
            ->assertSee('>36</span>', false)
            ->assertSee('>38</span>', false)
            ->assertSee('class="product-size-option is-unavailable"', false)
            ->assertSee('data-size="38" data-color-id="" data-color-name="" data-stock="0" disabled required', false)
            ->assertDontSee('<select id="product-size"', false);
    }

    public function test_product_page_renders_color_thumbnails_and_filters_clothing_sizes(): void
    {
        Storage::fake('public');
        $category = Category::create([
            'name' => 'Roupas',
            'slug' => 'roupas',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Blusa de malha',
            'slug' => 'blusa-de-malha',
            'price' => '39.90',
            'qty' => 3,
            'size_type' => 'clothing',
        ]);
        $black = ProductColor::create([
            'product_id' => $product->id,
            'name' => 'Preto',
            'image' => 'products/black.jpg',
        ]);
        $blue = ProductColor::create([
            'product_id' => $product->id,
            'name' => 'Azul',
            'image' => 'products/blue.jpg',
        ]);
        $product->variants()->create([
            'color_id' => $black->id,
            'name' => 'P',
            'sku' => 'COLOR-BLACK-P',
            'qty' => 2,
            'attributes' => ['color' => 'Preto', 'size' => 'P'],
            'active' => true,
        ]);
        $product->variants()->create([
            'color_id' => $blue->id,
            'name' => 'M',
            'sku' => 'COLOR-BLUE-M',
            'qty' => 1,
            'attributes' => ['color' => 'Azul', 'size' => 'M'],
            'active' => true,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Cor: <span data-color-selection>Escolha</span>', false)
            ->assertSee('Tamanho: <span data-size-selection>Escolha</span>', false)
            ->assertSee('aria-label="Cor Preto"', false)
            ->assertSee('aria-label="Cor Azul"', false)
            ->assertSee('data-size-color="'.$black->id.'"', false)
            ->assertSee('data-size-color="'.$blue->id.'"', false)
            ->assertDontSee('<span>Preto</span>', false)
            ->assertDontSee('<span>Azul</span>', false);
    }

    public function test_customer_search_keeps_other_sections_available_above_results(): void
    {
        $category = Category::create([
            'name' => 'Categoria pesquisa cliente',
            'slug' => 'categoria-pesquisa-cliente',
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Produto resultado cliente',
            'slug' => 'produto-resultado-cliente',
            'price' => '29.90',
            'qty' => 3,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('pesquisa', ['search' => 'Produto resultado cliente']))
            ->assertOk()
            ->assertSee('Produto resultado cliente')
            ->assertSee('id="favoritos"', false)
            ->assertSee('Produtos favoritos')
            ->assertSee('Produtos disponíveis')
            ->assertSee('<div class="search-layout has-filters">', false)
            ->assertSee('data-search="true"', false);
    }

    public function test_brand_can_be_created_from_the_product_creation_page(): void
    {
        $this->postJson(route('brands.store'), ['name' => 'Marca criada no formulário'])
            ->assertCreated()
            ->assertJsonPath('brand.name', 'Marca Criada No Formulário')
            ->assertJsonPath('existing', false);

        $this->assertDatabaseHas('brands', [
            'name' => 'Marca Criada No Formulário',
            'slug' => 'marca-criada-no-formulario',
            'active' => true,
        ]);
    }

    public function test_existing_brand_is_returned_instead_of_duplicated(): void
    {
        $brand = Brand::create([
            'name' => 'Marca existente',
            'slug' => 'marca-existente',
        ]);

        $this->postJson(route('brands.store'), ['name' => 'marca existente'])
            ->assertOk()
            ->assertJsonPath('brand.id', $brand->id)
            ->assertJsonPath('existing', true);

        $this->assertDatabaseCount('brands', 1);
    }

    public function test_categories_and_subcategories_can_be_created_from_product_form(): void
    {
        $categoryResponse = $this->postJson(route('categories.store'), [
            'name' => 'Categoria inline',
        ])->assertCreated()
            ->assertJsonPath('category.name', 'Categoria Inline')
            ->assertJsonPath('category.parent_id', null);

        $categoryId = $categoryResponse->json('category.id');

        $this->postJson(route('categories.store'), [
            'name' => 'Subcategoria inline',
            'parent_id' => $categoryId,
        ])->assertCreated()
            ->assertJsonPath('category.name', 'Subcategoria Inline')
            ->assertJsonPath('category.parent_id', $categoryId);

        $this->postJson(route('categories.store'), [
            'name' => 'subcategoria inline',
            'parent_id' => $categoryId,
        ])->assertOk()
            ->assertJsonPath('message', 'Essa subcategoria já está cadastrada e foi selecionada.')
            ->assertJsonPath('existing', true);

        $this->assertDatabaseHas('categories', [
            'name' => 'Subcategoria Inline',
            'parent_id' => $categoryId,
        ]);
        $this->assertDatabaseCount('categories', 2);
    }

    public function test_inline_category_creation_reuses_existing_category(): void
    {
        $category = Category::create([
            'name' => 'Categoria existente',
            'slug' => 'categoria-existente',
        ]);

        $this->postJson(route('categories.store'), [
            'name' => 'categoria existente',
        ])->assertOk()
            ->assertJsonPath('category.id', $category->id)
            ->assertJsonPath('existing', true);

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_product_brand_category_and_subcategory_names_use_consistent_title_case(): void
    {
        $brand = $this->postJson(route('brands.store'), [
            'name' => '  mARCA   de bRINQUEDOS ',
        ])->assertCreated()
            ->assertJsonPath('brand.name', 'Marca De Brinquedos')
            ->json('brand.id');

        $category = $this->postJson(route('categories.store'), [
            'name' => '  BRINQUEDOS ',
        ])->assertCreated()
            ->assertJsonPath('category.name', 'Brinquedos')
            ->json('category.id');

        $subcategory = $this->postJson(route('categories.store'), [
            'name' => '  BONECAS   E JOGOS ',
            'parent_id' => $category,
        ])->assertCreated()
            ->assertJsonPath('category.name', 'Bonecas E Jogos')
            ->json('category.id');

        Storage::fake('public');
        $this->post('/pagina/add', [
            'name' => '  BONECA   de PANO ',
            'brand_id' => $brand,
            'qty' => 1,
            'description' => 'Produto de teste.',
            'category_id' => $category,
            'subcategory_id' => $subcategory,
            'size_type' => 'none',
            'price' => '25.00',
            'cost' => '10.00',
            'private' => 0,
            'images' => [UploadedFile::fake()->create('produto.jpg', 100, 'image/jpeg')],
        ])->assertRedirect('/');

        $this->assertDatabaseHas('product', [
            'name' => 'Boneca De Pano',
            'brand_id' => $brand,
            'category_id' => $subcategory,
        ]);
    }

    public function test_guest_must_log_in_before_adding_a_product_to_cart(): void
    {
        $category = Category::create([
            'name' => 'Categoria teste',
            'slug' => 'categoria-carrinho-teste',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produto teste',
            'slug' => 'produto-carrinho-teste',
            'price' => '29.90',
            'qty' => 3,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('href="'.route('login', ['redirect' => '/product/'.$product->id]).'"', false)
            ->assertDontSee('data-cart-add')
            ->assertDontSee('id="cart-added-drawer"', false);
    }

    public function test_authenticated_product_page_includes_the_added_to_cart_drawer(): void
    {
        $category = Category::create([
            'name' => 'Categoria autenticada',
            'slug' => 'categoria-drawer-autenticada',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produto com drawer',
            'slug' => 'produto-com-drawer',
            'price' => '49.90',
            'qty' => 2,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('id="cart-added-drawer"', false)
            ->assertSee('Adicionado ao carrinho');
    }

    public function test_product_images_are_stored_when_a_product_is_created(): void
    {
        Storage::fake('public');

        $brand = Brand::create([
            'name' => 'Marca teste',
            'slug' => 'marca-teste',
        ]);

        $category = Category::create([
            'name' => 'Categoria teste',
            'slug' => 'categoria-teste',
        ]);

        $response = $this->post('/pagina/add', [
            'name' => 'Produto teste',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'size_type' => 'none',
            'price' => '29.90',
            'cost' => '12.00',
            'qty' => 3,
            'description' => 'Descrição do produto teste',
            'private' => 0,
            'images' => [
                UploadedFile::fake()->create('principal.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('secundaria.png', 100, 'image/png'),
            ],
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseCount('product_images', 2);
        $this->assertDatabaseHas('product_images', [
            'is_main' => 1,
            'position' => 0,
        ]);
        $this->assertDatabaseHas('product_images', [
            'is_main' => 0,
            'position' => 1,
        ]);

        ProductImage::query()->each(function (ProductImage $image): void {
            Storage::disk('public')->assertExists($image->image);
        });
    }

    public function test_product_cannot_be_created_without_cost_and_at_least_one_image(): void
    {
        $brand = Brand::create([
            'name' => 'Marca campos obrigatórios',
            'slug' => 'marca-campos-obrigatorios',
        ]);
        $category = Category::create([
            'name' => 'Categoria campos obrigatórios',
            'slug' => 'categoria-campos-obrigatorios',
        ]);

        $this->from('/pagina/add')
            ->post('/pagina/add', [
                'name' => 'Produto incompleto',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'size_type' => 'none',
                'price' => '29.90',
                'qty' => 3,
                'description' => 'Descrição do produto incompleto.',
                'private' => 0,
            ])
            ->assertRedirect('/pagina/add')
            ->assertSessionHasErrors(['cost', 'images']);

        $this->assertDatabaseMissing('product', ['name' => 'Produto Incompleto']);
    }

    public function test_product_price_accepts_brazilian_decimal_comma(): void
    {
        $brand = Brand::create([
            'name' => 'Marca preço local',
            'slug' => 'marca-preco-local',
        ]);
        $category = Category::create([
            'name' => 'Categoria preço local',
            'slug' => 'categoria-preco-local',
        ]);

        $this->post('/pagina/add', [
            'name' => 'Produto com preço local',
            'brand_id' => $brand->id,
            'qty' => 2,
            'description' => 'Descrição de teste.',
            'category_id' => $category->id,
            'size_type' => 'none',
            'price' => '29,90',
            'cost' => '12.00',
            'private' => 0,
            'images' => [UploadedFile::fake()->create('produto.jpg', 100, 'image/jpeg')],
        ])->assertRedirect('/');

        $this->assertDatabaseHas('product', [
            'name' => 'Produto Com Preço Local',
            'price' => '29.90',
            'condition' => null,
        ]);
    }

    public function test_product_can_be_created_as_seminovo(): void
    {
        $brand = Brand::create([
            'name' => 'Marca seminovo',
            'slug' => 'marca-seminovo',
        ]);
        $category = Category::create([
            'name' => 'Roupas seminovo',
            'slug' => 'roupas-seminovo',
        ]);

        Storage::fake('public');
        $this->post('/pagina/add', [
            'name' => 'Produto seminovo cadastrado',
            'brand_id' => $brand->id,
            'qty' => 1,
            'description' => 'Produto em bom estado.',
            'category_id' => $category->id,
            'size_type' => 'clothing',
            'price' => '25.00',
            'cost' => '10.00',
            'private' => 0,
            'images' => [UploadedFile::fake()->create('produto.jpg', 100, 'image/jpeg')],
            'condition' => 'seminovo',
            'variants' => [
                ['size' => 'M', 'qty' => 1],
            ],
        ])->assertRedirect('/');

        $this->assertDatabaseHas('product', [
            'name' => 'Produto Seminovo Cadastrado',
            'condition' => 'seminovo',
            'size_type' => 'clothing',
        ]);
    }

    public function test_apparel_product_requires_a_condition(): void
    {
        $brand = Brand::create([
            'name' => 'Marca roupa sem estado',
            'slug' => 'marca-roupa-sem-estado',
        ]);
        $category = Category::create([
            'name' => 'Calçados',
            'slug' => 'calcados-sem-estado',
        ]);

        $this->from('/pagina/add')
            ->post('/pagina/add', [
                'name' => 'Tênis sem estado',
                'brand_id' => $brand->id,
                'qty' => 1,
                'description' => 'Calçado de teste.',
                'category_id' => $category->id,
                'size_type' => 'footwear',
                'variants' => [
                    ['size' => '40', 'qty' => 1],
                ],
                'price' => '100.00',
                'private' => 0,
            ])
            ->assertRedirect('/pagina/add')
            ->assertSessionHasErrors('condition');
    }

    public function test_non_apparel_product_does_not_keep_a_condition(): void
    {
        $brand = Brand::create([
            'name' => 'Marca papelaria',
            'slug' => 'marca-papelaria-condicao',
        ]);
        $category = Category::create([
            'name' => 'Papelaria sem estado',
            'slug' => 'papelaria-sem-estado',
        ]);

        Storage::fake('public');
        $this->post('/pagina/add', [
            'name' => 'Caderno sem estado de roupa',
            'brand_id' => $brand->id,
            'qty' => 1,
            'description' => 'Produto de papelaria.',
            'category_id' => $category->id,
            'size_type' => 'none',
            'price' => '10.00',
            'cost' => '4.00',
            'private' => 0,
            'images' => [UploadedFile::fake()->create('produto.jpg', 100, 'image/jpeg')],
            'condition' => 'brecho',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('product', [
            'name' => 'Caderno Sem Estado De Roupa',
            'condition' => null,
        ]);
    }

    public function test_login_returns_to_the_requested_product(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'redirect' => '/product/5',
        ])->assertRedirect('/product/5');
    }

    public function test_registration_returns_to_the_requested_product(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Pessoa Teste',
            'email' => 'pessoa@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'redirect' => '/product/5',
        ])->assertRedirect('/product/5');
    }

    public function test_login_returns_to_any_valid_internal_route(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'redirect' => '/pesquisa?search=celular',
        ])->assertRedirect('/pesquisa?search=celular');
    }

    public function test_login_ignores_external_redirect_destination(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'redirect' => 'https://example.com',
        ])->assertRedirect(route('cliente'));
    }
}
