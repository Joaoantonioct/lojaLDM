<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdvPublicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['pdv.sync_token' => 'test-sync-token-that-is-at-least-32-chars']);
        Storage::fake('public');
    }

    public function test_piece_allows_three_active_publications_and_rejects_the_fourth_regardless_of_stock(): void
    {
        $category = Category::create(['name' => 'Roupas', 'slug' => 'roupas']);
        $product = Product::create([
            'name' => 'Blusa',
            'slug' => 'blusa-publicacao',
            'category_id' => $category->id,
            'qty' => 1,
            'price' => '30.00',
            'sku' => 'PDV-000701',
            'private' => false,
            'active' => true,
        ]);

        $headers = ['Authorization' => 'Bearer test-sync-token-that-is-at-least-32-chars'];

        $request = fn () => [
            'eventId' => (string) Str::uuid(),
            'title' => 'Look de verão',
            'description' => 'Combinação de peças',
            'productSkus' => [$product->sku],
            'durationHours' => 24,
            'imageData' => 'data:image/jpeg;base64,/9j/AA==',
        ];

        for ($i = 1; $i <= 3; $i++) {
            $this->postJson('/api/pdv/publications', $request(), $headers)
                ->assertCreated();
        }

        $this->assertDatabaseCount('publications', 3);
        $this->assertDatabaseCount('publication_items', 3);

        $this->postJson('/api/pdv/publications', $request(), $headers)
            ->assertStatus(409)
            ->assertJsonPath('message', 'A peça com o código PDV-000701 já atingiu o limite de 3 publicações ativas.');

        $this->assertDatabaseCount('publications', 3);

        $product->update(['qty' => 2]);

        $this->postJson('/api/pdv/publications', $request(), $headers)
            ->assertStatus(409);

        $this->assertDatabaseCount('publications', 3);
    }


    public function test_stock_out_removes_every_publication_using_piece_and_preserves_unrelated_looks(): void
    {
        $category = Category::create(['name' => 'Roupas', 'slug' => 'roupas']);

        $createProduct = function (string $name, string $slug, string $sku) use ($category): Product {
            return Product::create([
                'name' => $name,
                'slug' => $slug,
                'category_id' => $category->id,
                'qty' => 2,
                'price' => '30.00',
                'sku' => $sku,
                'private' => false,
                'active' => true,
            ]);
        };

        $pieceA = $createProduct('Blusa A', 'blusa-a', 'PDV-000710');
        $pieceB = $createProduct('Calça B', 'calca-b', 'PDV-000711');
        $pieceC = $createProduct('Saia C', 'saia-c', 'PDV-000712');
        $pieceD = $createProduct('Vestido D', 'vestido-d', 'PDV-000713');

        $pieceA->update(['qty' => 1]);

        $look1 = $this->createPublication([$pieceA->sku, $pieceB->sku]);
        $look2 = $this->createPublication([$pieceA->sku, $pieceC->sku]);
        $look3 = $this->createPublication([$pieceB->sku, $pieceC->sku]);
        $look4 = $this->createPublication([$pieceD->sku]);

        $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->postJson('/api/pdv/sync', [
                'eventId' => (string) Str::uuid(),
                'type' => 'inventory.delta',
                'payload' => [
                    'sku' => $pieceA->sku,
                    'delta' => -1,
                    'reason' => 'Venda no caixa',
                ],
            ])
            ->assertOk();

        $this->assertDatabaseHas('product', [
            'id' => $pieceA->id,
            'qty' => 0,
        ]);

        $this->assertDatabaseMissing('publications', ['id' => $look1]);
        $this->assertDatabaseMissing('publications', ['id' => $look2]);

        $this->assertDatabaseMissing('publication_items', ['publication_id' => $look1]);
        $this->assertDatabaseMissing('publication_items', ['publication_id' => $look2]);

        $this->assertDatabaseHas('publications', ['id' => $look3]);
        $this->assertDatabaseHas('publications', ['id' => $look4]);
    }

    public function test_buying_a_publication_creates_one_order_line_per_exact_piece(): void
    {
        config([
            'services.asaas.api_key' => 'test-asaas-api-key',
            'services.asaas.base_url' => 'https://api-sandbox.asaas.com/v3',
        ]);
        Http::fake([
            '*api-sandbox.asaas.com/v3/checkouts' => Http::response([
                'id' => 'checkout_publication_test',
                'link' => 'https://asaas.com/checkout/publication-test',
            ], 200),
        ]);

        $category = Category::create(['name' => 'Look', 'slug' => 'look']);
        $blouse = Product::create([
            'name' => 'Blusa',
            'slug' => 'blusa-look',
            'category_id' => $category->id,
            'qty' => 2,
            'price' => '35.50',
            'sku' => 'PDV-000703',
            'private' => false,
            'active' => true,
        ]);
        $skirt = Product::create([
            'name' => 'Saia',
            'slug' => 'saia-look',
            'category_id' => $category->id,
            'qty' => 2,
            'price' => '42.25',
            'sku' => 'PDV-000704',
            'private' => false,
            'active' => true,
        ]);
        $publicationId = $this->createPublication([$blouse->sku, $skirt->sku]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('cart.test.store'), ['publicationId' => $publicationId])
            ->assertCreated()
            ->assertJsonPath('redirectUrl', 'https://asaas.com/checkout/publication-test');

        $order = DB::table('site_test_orders')->first();
        $this->assertSame(7775, (int) $order->total_cents);
        $this->assertDatabaseCount('site_test_order_items', 2);
        $this->assertDatabaseHas('site_test_order_items', [
            'order_id' => $order->id,
            'product_id' => $blouse->id,
            'sku' => $blouse->sku,
            'quantity' => 1,
            'total_cents' => 3550,
        ]);
        $this->assertDatabaseHas('site_test_order_items', [
            'order_id' => $order->id,
            'product_id' => $skirt->id,
            'sku' => $skirt->sku,
            'quantity' => 1,
            'total_cents' => 4225,
        ]);
    }

    public function test_expired_publication_cannot_be_purchased(): void
    {
        config([
            "services.asaas.api_key" => "test-asaas-api-key",
            "services.asaas.base_url" => "https://api-sandbox.asaas.com/v3",
        ]);
        Http::fake();

        $category = Category::create(["name" => "Vencimento", "slug" => "vencimento-compra"]);
        $product = Product::create([
            "name" => "Blusa vencida",
            "slug" => "blusa-vencida",
            "category_id" => $category->id,
            "qty" => 5,
            "price" => "49.90",
            "sku" => "PDV-000706",
            "private" => false,
            "active" => true,
        ]);

        $publicationId = $this->createPublication([$product->sku]);
        $this->travel(25)->hours();

        $this->actingAs(User::factory()->create())
            ->postJson(route("cart.test.store"), ["publicationId" => $publicationId])
            ->assertUnprocessable();

        $this->assertDatabaseCount("site_test_orders", 0);
        $this->assertDatabaseHas("product", ["id" => $product->id, "qty" => 5]);
        Http::assertNothingSent();
    }

    public function test_expired_publication_is_removed_with_its_image(): void
    {
        $category = Category::create(["name" => "Expiracao", "slug" => "expiracao-teste"]);
        $product = Product::create(["name" => "Blusa temporaria", "slug" => "blusa-temporaria", "category_id" => $category->id, "qty" => 5, "price" => "49.90", "sku" => "PDV-000705", "private" => false, "active" => true]);
        $id = $this->createPublication([$product->sku]);
        $publication = \App\Models\Publication::findOrFail($id);
        $image = $publication->image_path;
        Storage::disk("public")->assertExists($image);
        $this->travel(25)->hours();
        $this->assertFalse($publication->fresh()->isAvailable());
        $removed = app(\App\Services\PublicationInventoryService::class)->removeUnavailable();
        $this->assertSame(1, $removed);
        $this->assertDatabaseMissing("publications", ["id" => $id]);
        $this->assertDatabaseMissing("publication_items", ["publication_id" => $id]);
        Storage::disk("public")->assertMissing($image);
        $this->assertDatabaseHas("product", ["id" => $product->id, "qty" => 5]);
    }

    private function createPublication(array $skus): int
    {
        return (int) $this->withToken('test-sync-token-that-is-at-least-32-chars')
            ->postJson('/api/pdv/publications', [
                'eventId' => (string) Str::uuid(),
                'title' => 'Combinação de teste',
                'description' => '',
                'productSkus' => $skus,
                'durationHours' => 24,
            'imageData' => 'data:image/jpeg;base64,/9j/AA==',
            ])
            ->assertCreated()
            ->json('publicationId');
    }
}
