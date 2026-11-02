<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\PdvIdentifierAllocator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdvProductIntegrationTest extends TestCase
{
    private const TOKEN = 'test-sync-token-that-is-at-least-32-chars';

    protected function setUp(): void
    {
        parent::setUp();
        config(['pdv.sync_token' => self::TOKEN]);
    }

    public function test_identifier_allocation_is_authenticated_unique_and_skips_existing_skus(): void
    {
        $category = Category::create(['name' => 'Legado', 'slug' => 'legado']);
        $legacyProduct = Product::create([
            'name' => 'Produto legado',
            'slug' => 'produto-legado',
            'category_id' => $category->id,
            'qty' => 1,
            'price' => '1.00',
            'sku' => 'PDV-000001',
            'private' => false,
            'active' => true,
        ]);

        $this->postJson('/api/pdv/identifiers')->assertUnauthorized();

        $first = $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/identifiers')
            ->assertOk()
            ->assertJsonStructure(['sku', 'barcode'])
            ->json();
        $second = $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/identifiers')
            ->assertOk()
            ->json();

        $this->assertSame('PDV-000002', $first['sku']);
        $this->assertSame('PDV-000003', $second['sku']);
        $this->assertSame('PDV-000001', $legacyProduct->fresh()->sku);
        $this->assertNotSame($first['barcode'], $second['barcode']);
        $this->assertMatchesRegularExpression('/^2[0-9][0-9]{11}$/', $first['barcode']);
        $this->assertTrue(app(PdvIdentifierAllocator::class)->isValidBarcode($first['barcode']));
        $this->assertDatabaseCount('pdv_identifiers', 2);
    }

    public function test_identifier_allocation_recreates_a_missing_sequence_after_existing_products(): void
    {
        $category = Category::create(['name' => 'Legado', 'slug' => 'legado']);
        Product::create([
            'name' => 'Produto legado recente',
            'slug' => 'produto-legado-recente',
            'category_id' => $category->id,
            'qty' => 1,
            'price' => '1.00',
            'sku' => 'PDV-000008',
            'barcode' => '2000000000084',
            'private' => false,
            'active' => true,
        ]);
        DB::table('pdv_identifier_sequences')->where('id', 1)->delete();

        $identifiers = $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/identifiers')
            ->assertOk()
            ->json();

        $this->assertSame('PDV-000009', $identifiers['sku']);
        $this->assertSame('2000000000091', $identifiers['barcode']);
        $this->assertDatabaseHas('pdv_identifier_sequences', [
            'id' => 1,
            'next_sku' => 10,
            'next_barcode' => 10,
        ]);
    }

    public function test_identifier_allocation_avoids_low_sequential_codes_when_sequence_and_products_are_empty(): void
    {
        DB::table('pdv_identifier_sequences')->where('id', 1)->delete();

        $identifiers = $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/identifiers')
            ->assertOk()
            ->json();

        $this->assertMatchesRegularExpression('/^PDV-[1-9][0-9]{8}$/', $identifiers['sku']);
        $this->assertMatchesRegularExpression('/^2[0-9][0-9]{11}$/', $identifiers['barcode']);
        $this->assertTrue(app(PdvIdentifierAllocator::class)->isValidBarcode($identifiers['barcode']));
    }

    public function test_pdv_product_creation_is_idempotent_and_resolves_category_by_name(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Papelaria', 'slug' => 'papelaria']);
        $identifiers = $this->withToken(self::TOKEN)->postJson('/api/pdv/identifiers')->assertOk()->json();
        $eventId = (string) Str::uuid();
        $payload = [
            'eventId' => $eventId,
            'name' => 'Caderno PDV',
            'category' => 'papelaria',
            'sku' => $identifiers['sku'],
            'barcode' => $identifiers['barcode'],
            'priceCents' => 1299,
            'quantity' => 7,
            'images' => [
                'data:image/jpeg;base64,/9j/2Q==',
                'data:image/jpeg;base64,/9j/2Q==',
            ],
        ];

        $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/products', $payload)
            ->assertCreated()
            ->assertJson(['created' => true, 'duplicate' => false]);

        $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/products', $payload)
            ->assertOk()
            ->assertJson(['created' => true, 'duplicate' => true]);

        $this->assertDatabaseCount('product', 1);
        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseCount('pdv_product_receipts', 1);
        $this->assertDatabaseCount('product_images', 2);
        $images = ProductImage::query()->orderBy('position')->get();
        $this->assertTrue($images[0]->is_main);
        $this->assertFalse($images[1]->is_main);
        $this->assertSame(0, $images[0]->position);
        $this->assertSame(1, $images[1]->position);
        Storage::disk('public')->assertExists($images[0]->image);
        Storage::disk('public')->assertExists($images[1]->image);
        $this->assertDatabaseHas('product', [
            'name' => 'Caderno PDV',
            'category_id' => $category->id,
            'brand_id' => null,
            'sku' => $identifiers['sku'],
            'barcode' => $identifiers['barcode'],
            'price' => '12.99',
            'qty' => 7,
            'description' => null,
        ]);
    }

    public function test_pdv_product_creation_adds_a_missing_category(): void
    {
        $identifiers = $this->withToken(self::TOKEN)->postJson('/api/pdv/identifiers')->assertOk()->json();

        $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/products', [
                'eventId' => (string) Str::uuid(),
                'name' => 'Brinquedo PDV',
                'category' => 'Brinquedos',
                'sku' => $identifiers['sku'],
                'barcode' => $identifiers['barcode'],
                'priceCents' => 2500,
                'quantity' => 3,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('categories', ['name' => 'Brinquedos']);
        $this->assertDatabaseHas('product', ['name' => 'Brinquedo PDV', 'brand_id' => null, 'description' => null]);
    }

    public function test_pdv_product_creation_uses_general_category_when_category_is_empty(): void
    {
        $identifiers = $this->withToken(self::TOKEN)->postJson('/api/pdv/identifiers')->assertOk()->json();

        $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/products', [
                'eventId' => (string) Str::uuid(),
                'name' => 'Caneta sem categoria',
                'category' => '',
                'sku' => $identifiers['sku'],
                'barcode' => $identifiers['barcode'],
                'priceCents' => 2500,
                'quantity' => 10,
            ])
            ->assertCreated();

        $category = Category::query()->where('name', 'Geral')->firstOrFail();
        $this->assertDatabaseHas('product', [
            'name' => 'Caneta sem categoria',
            'category_id' => $category->id,
            'sku' => $identifiers['sku'],
            'barcode' => $identifiers['barcode'],
            'qty' => 10,
        ]);
    }

    public function test_site_registration_allocates_identifiers_and_queues_product_for_pdv(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Papelaria', 'slug' => 'papelaria']);
        $brand = Brand::create(['name' => 'Marca', 'slug' => 'marca']);
        $tinyJpeg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAGf/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABBQJ//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAwEBPwF//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAgEBPwF//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQAGPwJ//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPyF//9k=', true);

        $this->post('/pagina/add', [
            'name' => 'Caneta da loja',
            'brand_id' => $brand->id,
            'qty' => 4,
            'description' => 'Caneta azul',
            'category_id' => $category->id,
            'price' => '5,50',
            'private' => 0,
            'images' => [
                UploadedFile::fake()->createWithContent('capa.jpg', $tinyJpeg),
                UploadedFile::fake()->createWithContent('detalhe.jpg', $tinyJpeg),
            ],
        ])->assertRedirect('/');

        $product = Product::query()->where('name', 'Caneta da loja')->firstOrFail();
        $this->assertSame('PDV-000001', $product->sku);
        $this->assertTrue(app(PdvIdentifierAllocator::class)->isValidBarcode($product->barcode));
        $this->assertDatabaseHas('pdv_product_outbox', [
            'product_id' => $product->id,
            'acknowledged_at' => null,
        ]);
        $this->assertDatabaseCount('product_images', 2);
        $this->assertTrue(ProductImage::query()->where('product_id', $product->id)->where('position', 0)->value('is_main'));
        $this->assertFalse(ProductImage::query()->where('product_id', $product->id)->where('position', 1)->value('is_main'));

        $pending = $this->withToken(self::TOKEN)
            ->getJson('/api/pdv/products/pending')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Caneta da loja')
            ->assertJsonPath('data.0.category', 'Papelaria')
            ->assertJsonPath('data.0.priceCents', 550)
            ->assertJsonPath('data.0.quantity', 4)
            ->assertJsonPath('data.0.sku', $product->sku)
            ->assertJsonPath('data.0.barcode', $product->barcode);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $pending->json('data.0.imageData'));
        $eventId = $pending->json('data.0.eventId');

        $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/products/ack', ['eventId' => $eventId])
            ->assertOk()
            ->assertJson(['acknowledged' => true, 'duplicate' => false]);
        $this->withToken(self::TOKEN)
            ->getJson('/api/pdv/products/pending')
            ->assertJsonCount(0, 'data');
    }

    public function test_review_link_uses_exact_pdv_codes_rejects_conflicts_and_hides_codes_from_site_ui(): void
    {
        $category = Category::create(['name' => 'Livros', 'slug' => 'livros']);
        $product = Product::create([
            'name' => 'Livro de teste',
            'slug' => 'livro-de-teste',
            'category_id' => $category->id,
            'qty' => 2,
            'price' => '25.00',
            'sku' => 'LEGACY-SKU-DO-NOT-CHANGE',
            'private' => false,
            'active' => true,
        ]);
        $identifiers = app(PdvIdentifierAllocator::class)->allocate();

        $this->withToken(self::TOKEN)
            ->getJson('/api/pdv/products/review')
            ->assertOk()
            ->assertJsonPath('data.0.id', $product->id)
            ->assertJsonPath('data.0.linked', false)
            ->assertJsonMissingPath('data.0.sku')
            ->assertJsonMissingPath('data.0.barcode');

        $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/products/link', [
                'productId' => $product->id,
                'sku' => $identifiers['sku'],
                'barcode' => $identifiers['barcode'],
            ])
            ->assertOk()
            ->assertJson(['linked' => true, 'productId' => $product->id]);
        $this->withToken(self::TOKEN)
            ->getJson('/api/pdv/products/review')
            ->assertJsonPath('data.0.linked', true);

        $this->assertSame($identifiers['sku'], $product->fresh()->sku);
        $this->assertSame($identifiers['barcode'], $product->fresh()->barcode);

        $otherProduct = Product::create([
            'name' => 'Outro livro',
            'slug' => 'outro-livro',
            'category_id' => $category->id,
            'qty' => 1,
            'price' => '10.00',
            'private' => false,
            'active' => true,
        ]);
        $this->withToken(self::TOKEN)
            ->postJson('/api/pdv/products/link', [
                'productId' => $otherProduct->id,
                'sku' => $identifiers['sku'],
                'barcode' => $identifiers['barcode'],
            ])
            ->assertStatus(409);

        $this->get('/product/'.$product->id)
            ->assertOk()
            ->assertDontSee($product->sku)
            ->assertDontSee($product->barcode);
        $this->get('/pagina/add')
            ->assertOk()
            ->assertDontSee('name="sku"')
            ->assertDontSee('name="barcode"');
    }
}
