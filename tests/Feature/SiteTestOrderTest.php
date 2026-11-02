<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteTestOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['pdv.sync_token' => 'test-sync-token-that-is-at-least-32-chars']);
    }

    public function test_test_order_decrements_stock_and_queues_the_same_sku_for_the_pdv(): void
    {
        $category = Category::create(['name' => 'Teste', 'slug' => 'teste']);
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
                'items' => [['productId' => $product->id, 'quantity' => 2]],
            ])
            ->assertCreated()
            ->assertJsonPath('message', 'Pedido de teste registrado. Nenhum pagamento foi processado.');

        $this->assertSame(1, $product->fresh()->qty);
        $this->assertDatabaseHas('site_test_orders', [
            'user_id' => $user->id,
            'total_cents' => 2500,
        ]);
        $this->assertDatabaseHas('site_test_order_items', [
            'product_id' => $product->id,
            'sku' => 'PDV-000123',
            'quantity' => 2,
            'unit_price_cents' => 1250,
        ]);

        $order = DB::table('site_test_orders')->first();
        $event = DB::table('pdv_site_order_outbox')
            ->where('order_id', $order->id)
            ->first();

        $this->assertNotNull($event);
        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->getJson(route('pdv.site-orders.pending'))
            ->assertOk()
            ->assertJsonPath('data.0.eventId', $event->event_id)
            ->assertJsonPath('data.0.orderId', $order->public_id)
            ->assertJsonStructure(['data' => [[
                'createdAt',
                'totalCents',
                'items' => [['unitPriceCents', 'totalCents']],
            ]]])
            ->assertJsonPath('data.0.totalCents', 2500)
            ->assertJsonPath('data.0.items.0.sku', 'PDV-000123')
            ->assertJsonPath('data.0.items.0.quantity', 2)
            ->assertJsonPath('data.0.items.0.unitPriceCents', 1250)
            ->assertJsonPath('data.0.items.0.totalCents', 2500);

        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->postJson(route('pdv.site-orders.ack'), ['eventId' => $event->event_id])
            ->assertOk()
            ->assertJson(['acknowledged' => true, 'duplicate' => false]);
        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->getJson(route('pdv.site-orders.pending'))
            ->assertOk()
            ->assertJsonPath('data', []);
        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->getJson(route('pdv.site-orders.history', ['afterId' => 0]))
            ->assertOk()
            ->assertJsonPath('nextCursor', (int) $order->id)
            ->assertJsonPath('data.0.orderId', $order->public_id)
            ->assertJsonPath('data.0.items.0.totalCents', 2500);
        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->getJson(route('pdv.site-orders.history', ['afterId' => $order->id]))
            ->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('nextCursor', (int) $order->id);

        $this->get(route('cart.test.complete', $order->public_id))
            ->assertOk()
            ->assertSee('não processou pagamento')
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
