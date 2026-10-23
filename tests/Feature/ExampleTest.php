<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
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

    public function test_footer_displays_both_brand_logos(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(asset('img/logo1.png'))
            ->assertSee(asset('img/michele1.png'))
            ->assertSee('© '.date('Y').' LDM. Todos os direitos reservados.');
    }

    public function test_product_creation_page_keeps_the_product_form_and_image_uploader(): void
    {
        $this->get('/pagina/add')
            ->assertOk()
            ->assertSee('Adicionar produto')
            ->assertSee('data-product-create-form', false)
            ->assertSee('name="images[]"', false)
            ->assertSee('data-image-dropzone', false)
            ->assertSee('data-inline-panel="category"', false)
            ->assertSee('data-inline-panel="subcategory"', false)
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

    public function test_brand_can_be_created_from_the_product_creation_page(): void
    {
        $this->postJson(route('brands.store'), ['name' => 'Marca criada no formulário'])
            ->assertCreated()
            ->assertJsonPath('brand.name', 'Marca criada no formulário')
            ->assertJsonPath('existing', false);

        $this->assertDatabaseHas('brands', [
            'name' => 'Marca criada no formulário',
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
            ->assertJsonPath('category.name', 'Categoria inline')
            ->assertJsonPath('category.parent_id', null);

        $categoryId = $categoryResponse->json('category.id');

        $this->postJson(route('categories.store'), [
            'name' => 'Subcategoria inline',
            'parent_id' => $categoryId,
        ])->assertCreated()
            ->assertJsonPath('category.name', 'Subcategoria inline')
            ->assertJsonPath('category.parent_id', $categoryId);

        $this->postJson(route('categories.store'), [
            'name' => 'subcategoria inline',
            'parent_id' => $categoryId,
        ])->assertOk()
            ->assertJsonPath('message', 'Essa subcategoria já está cadastrada e foi selecionada.')
            ->assertJsonPath('existing', true);

        $this->assertDatabaseHas('categories', [
            'name' => 'Subcategoria inline',
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
            'price' => '29.90',
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
            'price' => '29,90',
            'private' => 0,
        ])->assertRedirect('/');

        $this->assertDatabaseHas('product', [
            'name' => 'Produto com preço local',
            'price' => '29.90',
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
