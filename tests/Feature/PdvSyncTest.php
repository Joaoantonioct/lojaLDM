<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdvSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['pdv.sync_token' => 'test-sync-token-that-is-at-least-32-chars']);
    }

    public function test_sync_requires_a_token_and_applies_events_once(): void
    {
        $category = Category::create(['name' => 'Papelaria', 'slug' => 'papelaria']);
        $brand = Brand::create(['name' => 'Marca', 'slug' => 'marca']);
        $product = Product::create([
            'name' => 'Caderno',
            'slug' => 'caderno-teste',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'qty' => 10,
            'price' => '20.00',
            'sku' => 'PDV-000001',
            'private' => false,
            'active' => true,
        ]);

        $eventId = (string) Str::uuid();
        $payload = [
            'eventId' => $eventId,
            'type' => 'inventory.delta',
            'payload' => ['sku' => $product->sku, 'delta' => -2, 'reason' => 'Venda', 'saleId' => 1],
        ];

        $this->postJson('/api/pdv/sync', $payload)
            ->assertUnauthorized();

        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->postJson('/api/pdv/sync', $payload)
            ->assertOk()
            ->assertJson(['received' => true, 'duplicate' => false]);

        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->postJson('/api/pdv/sync', $payload)
            ->assertOk()
            ->assertJson(['received' => true, 'duplicate' => true]);

        $this->assertSame(8, $product->fresh()->qty);
        $this->assertDatabaseCount('pdv_sync_receipts', 1);
    }

    public function test_sync_updates_price_and_scheduled_promotion_displays_effective_price(): void
    {
        $category = Category::create(['name' => 'Papelaria', 'slug' => 'papelaria']);
        $brand = Brand::create(['name' => 'Marca', 'slug' => 'marca']);
        $product = Product::create([
            'name' => 'Caneta em promoção',
            'slug' => 'caneta-promocao',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'qty' => 5,
            'price' => '10.00',
            'sku' => 'PDV-000002',
            'private' => false,
            'active' => true,
        ]);
        $headers = ['Authorization' => 'Bearer test-sync-token-that-is-at-least-32-chars'];

        $this->postJson('/api/pdv/sync', [
            'eventId' => (string) Str::uuid(),
            'type' => 'product.price',
            'payload' => [
                'sku' => $product->sku,
                'priceCents' => 1250,
                'description' => str_repeat('x', 1000),
            ],
        ], $headers)->assertOk();
        $this->assertSame(str_repeat('x', 1000), $product->fresh()->description);

        $this->postJson('/api/pdv/sync', [
            'eventId' => (string) Str::uuid(),
            'type' => 'promotion.upsert',
            'payload' => [
                'sku' => $product->sku,
                'promotionId' => 1,
                'discountBasisPoints' => 2000,
                'startsAt' => now()->subMinute()->toIso8601String(),
                'endsAt' => now()->addDay()->toIso8601String(),
            ],
        ], $headers)->assertOk();

        $this->postJson('/api/pdv/sync', [
            'eventId' => (string) Str::uuid(),
            'type' => 'promotion.upsert',
            'payload' => [
                'sku' => $product->sku,
                'promotionId' => 2,
                'discountBasisPoints' => 1000,
                'startsAt' => now()->addDay()->toIso8601String(),
                'endsAt' => now()->addDays(2)->toIso8601String(),
            ],
        ], $headers)->assertOk();

        $this->assertSame('12.50', $product->fresh()->price);
        $this->assertSame('10.00', $product->fresh()->effective_price);
        $this->assertDatabaseCount('product_promotions', 2);
        $this->get('/')
            ->assertOk()
            ->assertSee('10,00')
            ->assertSee('20,00% OFF');
        $this->get('/pesquisa?min_price=10.50')
            ->assertOk()
            ->assertDontSee('Caneta em promoção');
    }

    public function test_unknown_sku_is_reported_without_creating_a_receipt(): void
    {
        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->postJson('/api/pdv/sync', [
                'eventId' => (string) Str::uuid(),
                'type' => 'inventory.set',
                'payload' => ['sku' => 'INEXISTENTE', 'quantity' => 4, 'reason' => 'Conciliação'],
            ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Não existe produto no site com o SKU INEXISTENTE.');

        $this->assertDatabaseCount('pdv_sync_receipts', 0);
    }
}
