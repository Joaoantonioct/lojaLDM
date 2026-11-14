<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use Tests\TestCase;

class SiteTestOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['pdv.sync_token' => 'test-sync-token-that-is-at-least-32-chars']);
    }

    
public function 
test_test_order_reserves_stock_and_creates_asaas_checkout(): void
{
    config([
        'services.asaas.api_key' => 'test-asaas-api-key',
        'services.asaas.base_url' => 
'https://api-sandbox.asaas.com/v3',
    ]);

    Http::fake([
        '*api-sandbox.asaas.com/v3/checkouts' => Http::response([
            'id' => 'checkout_test_123',
        ], 200),
    ]);

    $category = Category::create([
        'name' => 'Teste',
        'slug' => 'teste',
    ]);

    $product = Product::create([
        'name' => 'Produto de teste',
        'slug' => 'produto-de-teste',
        'category_id' => $category->id,
        'qty' => 3,
        'price' => '12.50',
        'sku' => 'PDV-000123',
        'private' => false,
        'active' => true,
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('cart.test.store'), [
            'items' => [
                ['productId' => $product->id, 'quantity' => 2],
            ],
        ])
        ->assertCreated()
        ->assertJsonPath('message', 'Checkout criado. Escolha Pix ou cartão para concluir o pagamento.')
        ->assertJsonPath(
            'redirectUrl',
            
'https://asaas.com/checkoutSession/show?id=checkout_test_123'
        );

    $this->assertSame(1, $product->fresh()->qty);

    $this->assertDatabaseHas('site_test_orders', [
        'user_id' => $user->id,
        'total_cents' => 2500,
        'order_status' => 'pending_payment',
        'payment_status' => 'pending',
        'asaas_checkout_id' => 'checkout_test_123',
    ]);

    $this->assertDatabaseHas('site_test_order_items', [
        'product_id' => $product->id,
        'sku' => 'PDV-000123',
        'quantity' => 2,
        'unit_price_cents' => 1250,
    ]);

        // O pedido só será enviado ao PDV após a confirmação do pagamento pelo webhook.
    
	$this->assertDatabaseCount('pdv_site_order_outbox', 0);

    $this->withToken('test-sync-token-that-is-at-least-32-chars')
        ->getJson(route('pdv.site-orders.pending'))
        ->assertOk()
        ->assertJsonPath('data', []);

    $order = DB::table('site_test_orders')->first();

    $this->get(route('cart.test.complete', $order->public_id))
        ->assertOk()
        ->assertSee('Aguardando confirmação do pagamento')
        ->assertSee('Produto de teste');
}


    public function test_insufficient_stock_does_not_create_an_order_or_change_stock(): void
    {
        $category = Category::create(['name' => 'Teste', 'slug' => 'teste']);
        $product = Product::create([
            'name' => 'Última unidade',
            'slug' => 'ultima-unidade',
            'category_id' => $category->id,
            'qty' => 0,
            'price' => '10.00',
            'sku' => 'PDV-000124',
            'private' => false,
            'active' => true,
        ]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('cart.test.store'), [
                'items' => [['productId' => $product->id, 'quantity' => 1]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items');

        $this->assertSame(0, $product->fresh()->qty);
        $this->assertDatabaseCount('site_test_orders', 0);
        $this->assertDatabaseCount('pdv_site_order_outbox', 0);
    }

    public function test_catalog_displays_an_explicit_out_of_stock_message(): void
    {
        $category = Category::create(['name' => 'Teste', 'slug' => 'teste']);
        $product = Product::create([
            'name' => 'Produto esgotado',
            'slug' => 'produto-esgotado',
            'category_id' => $category->id,
            'qty' => 0,
            'price' => '10.00',
            'sku' => 'PDV-000125',
            'private' => false,
            'active' => true,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Sem estoque');
    }

    public function test_checkout_requires_an_authenticated_customer(): void
    {
        $this->postJson(route('cart.test.store'), ['items' => []])
            ->assertUnauthorized();
    }
}
