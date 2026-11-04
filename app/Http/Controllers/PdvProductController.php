<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\PdvIdentifierAllocator;
use App\Support\ProductSizeCatalog;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class PdvProductController extends Controller
{
    public function identifiers(PdvIdentifierAllocator $allocator): JsonResponse
    {
        return response()->json($allocator->allocate());
    }

    public function store(Request $request, PdvIdentifierAllocator $allocator): JsonResponse
    {
        $request->merge([
            'name' => is_string($request->input('name'))
                ? Str::title(Str::squish($request->input('name')))
                : $request->input('name'),
            'category' => is_string($request->input('category')) && trim($request->input('category')) !== ''
                ? Str::title(Str::squish($request->input('category')))
                : 'Geral',
            'brand' => is_string($request->input('brand'))
                ? Str::title(Str::squish($request->input('brand')))
                : $request->input('brand'),
        ]);

        $data = $request->validate([
            'eventId' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['required', 'string', 'max:100'],
            'sizeType' => ['sometimes', 'string', 'in:none,clothing,trousers,footwear'],
            'brand' => ['nullable', 'string', 'max:100'],
            'sku' => ['required', 'string', 'regex:/^PDV-[0-9]{6,}$/', 'max:100'],
            'barcode' => ['required', 'string', 'regex:/^2[0-9][0-9]{11}$/'],
            'priceCents' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'costCents' => ['nullable', 'integer', 'min:0', 'max:2147483647'],
            'condition' => ['nullable', 'string', 'in:novo,seminovo,brecho'],
            'quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'colors' => ['sometimes', 'array', 'max:10'],
            'colors.*.name' => ['required_with:colors', 'string', 'max:50'],
            'colors.*.imageData' => ['required_with:colors', 'string', 'max:1400000'],
            'variants' => ['sometimes', 'array', 'max:140'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.size' => ['nullable', 'string', 'max:20'],
            'variants.*.sku' => ['required_with:variants', 'string', 'regex:/^PDV-[0-9]{6,}$/', 'max:100'],
            'variants.*.barcode' => ['required_with:variants', 'string', 'regex:/^2[0-9]{12}$/'],
            'variants.*.quantity' => ['required_with:variants', 'integer', 'min:0', 'max:2147483647'],
            'images' => ['sometimes', 'array', 'max:10'],
            'images.*' => ['required', 'string', 'max:1400000'],
        ]);
        $images = $this->decodeImages($data['images'] ?? []);
        $colorImages = $this->decodeImages(array_column($data['colors'] ?? [], 'imageData'));

        if (! $allocator->isValidBarcode($data['barcode'])) {
            throw ValidationException::withMessages(['barcode' => 'O código de barras informado é inválido.']);
        }
        foreach ($data['variants'] ?? [] as $index => $variant) {
            if (! $allocator->isValidBarcode($variant['barcode'])) {
                throw ValidationException::withMessages(["variants.{$index}.barcode" => 'O código de barras da variação é inválido.']);
            }
        }

        $payloadHash = hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        try {
            $storedImagePaths = [];
            $result = DB::transaction(function () use ($data, $payloadHash, $allocator, $images, $colorImages, &$storedImagePaths): array {
                $receipt = DB::table('pdv_product_receipts')
                    ->where('event_id', $data['eventId'])
                    ->lockForUpdate()
                    ->first();

                if ($receipt) {
                    if (! hash_equals($receipt->payload_hash, $payloadHash)) {
                        abort(409, 'O eventId já foi usado com dados diferentes.');
                    }

                    return ['created' => true, 'duplicate' => true, 'productId' => $receipt->product_id];
                }

                $category = $this->categoryFor($data['category']);
                $category->loadMissing('parent');
                $sizeType = $data['sizeType'] ?? $category->apparelSizeType() ?? 'none';
                $hasSizeVariants = $sizeType !== 'none';
                $variants = $data['variants'] ?? [];
                $colors = $data['colors'] ?? [];
                if ($hasSizeVariants && $variants === []) {
                    throw ValidationException::withMessages([
                        'variants' => 'Cadastre o estoque das combinações de cor e tamanho para este produto.',
                    ]);
                }
                $knownColors = [];
                foreach ($colors as $color) {
                    $key = mb_strtolower(trim($color['name']));
                    if (isset($knownColors[$key])) {
                        throw ValidationException::withMessages(['colors' => 'Não repita o nome de uma cor.']);
                    }
                    $knownColors[$key] = true;
                }
                $seenCombinations = [];
                if ($variants !== []) {
                    $allowedSizes = ProductSizeCatalog::forType($sizeType);
                    foreach ($variants as $index => $variant) {
                        $color = $variant['color'] ?? '';
                        $size = $variant['size'] ?? '';
                        if ($colors !== [] && ! isset($knownColors[mb_strtolower($color)])) {
                            throw ValidationException::withMessages(["variants.{$index}.color" => 'Selecione uma cor cadastrada.']);
                        }
                        if ($hasSizeVariants && ! in_array($size, $allowedSizes, true)) {
                            throw ValidationException::withMessages(["variants.{$index}.size" => 'Tamanho incompatível com a categoria.']);
                        }
                        if (! $hasSizeVariants && $size !== '') {
                            throw ValidationException::withMessages(["variants.{$index}.size" => 'Este produto não aceita tamanhos.']);
                        }
                        $combination = mb_strtolower($color).'|'.$size;
                        if (isset($seenCombinations[$combination])) {
                            throw ValidationException::withMessages(['variants' => 'Não repita a combinação de cor e tamanho.']);
                        }
                        $seenCombinations[$combination] = true;
                    }
                    if (array_sum(array_column($variants, 'quantity')) !== $data['quantity']) {
                        throw ValidationException::withMessages(['quantity' => 'O estoque total deve corresponder à soma dos tamanhos.']);
                    }
                }
                if ($colors !== [] && count(array_unique(array_map(
                    fn ($variant) => mb_strtolower($variant['color'] ?? ''),
                    $variants,
                ))) !== count($colors)) {
                    throw ValidationException::withMessages(['variants' => 'Informe o estoque de cada cor cadastrada.']);
                }
                if (! $hasSizeVariants && $colors === [] && $variants !== []) {
                    throw ValidationException::withMessages(['variants' => 'Este produto não aceita variações sem cor.']);
                }
                $brand = $this->brandFor($data['brand'] ?? null);
                $product = Product::query()->create([
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(6)),
                    'category_id' => $category->id,
                    'brand_id' => $brand->id ?? null,
                    'qty' => $data['quantity'],
                    'description' => $data['description'] ?? null,
                    'price' => $this->decimalFromCents($data['priceCents']),
                    'cost' => isset($data['costCents']) ? $this->decimalFromCents($data['costCents']) : 0,
                    'condition' => $data['condition'] ?? null,
                    'size_type' => $sizeType,
                    'private' => false,
                    'active' => true,
                ]);

                $allocator->attachReserved($data['sku'], $data['barcode'], $product);

                $productColors = [];
                foreach ($colors as $index => $color) {
                    $imagePath = 'products/'.$product->id.'/colors/'.Str::uuid().'.jpg';
                    if (! Storage::disk('public')->put($imagePath, $colorImages[$index])) {
                        throw new \RuntimeException('Não foi possível armazenar a imagem da cor.');
                    }
                    $storedImagePaths[] = $imagePath;
                    $productColors[mb_strtolower($color['name'])] = $product->colors()->create([
                        'name' => $color['name'],
                        'image' => $imagePath,
                    ]);
                }

                foreach ($variants as $variant) {
                    $color = $productColors[mb_strtolower($variant['color'] ?? '')] ?? null;
                    $size = $variant['size'] ?? '';
                    $product->variants()->create([
                        'color_id' => $color?->id,
                        'name' => trim(($color?->name ?? '').' '.$size) ?: null,
                        'sku' => $variant['sku'],
                        'barcode' => $variant['barcode'],
                        'qty' => $variant['quantity'],
                        'attributes' => array_filter([
                            'color' => $color?->name,
                            'size' => $size ?: null,
                        ]),
                        'active' => true,
                    ]);
                }

                foreach ($images as $position => $imageBytes) {
                    $imagePath = 'products/'.$product->id.'/'.Str::uuid().'.jpg';
                    if (! Storage::disk('public')->put($imagePath, $imageBytes)) {
                        throw new \RuntimeException('Não foi possível armazenar uma imagem do produto.');
                    }
                    $storedImagePaths[] = $imagePath;
                    ProductImage::query()->create([
                        'product_id' => $product->id,
                        'image' => $imagePath,
                        'is_main' => $position === 0,
                        'position' => $position,
                    ]);
                }

                DB::table('pdv_product_receipts')->insert([
                    'event_id' => $data['eventId'],
                    'product_id' => $product->id,
                    'payload_hash' => $payloadHash,
                    'created_at' => now(),
                ]);

                return ['created' => true, 'duplicate' => false, 'productId' => $product->id];
            });
        } catch (QueryException $exception) {
            foreach ($storedImagePaths ?? [] as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $receipt = DB::table('pdv_product_receipts')->where('event_id', $data['eventId'])->first();
            if (! $receipt) {
                throw $exception;
            }
            if (! hash_equals($receipt->payload_hash, $payloadHash)) {
                abort(409, 'O eventId já foi usado com dados diferentes.');
            }

            $result = ['created' => true, 'duplicate' => true, 'productId' => $receipt->product_id];
        } catch (Throwable $exception) {
            foreach ($storedImagePaths ?? [] as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }

        return response()->json($result, $result['duplicate'] ? 200 : 201);
    }

    public function pending(): JsonResponse
    {
        $records = DB::table('pdv_product_outbox')
            ->join('product', 'product.id', '=', 'pdv_product_outbox.product_id')
            ->join('categories', 'categories.id', '=', 'product.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'product.brand_id')
            ->whereNull('pdv_product_outbox.acknowledged_at')
            ->orderBy('pdv_product_outbox.created_at')
            ->get([
                'pdv_product_outbox.event_id as eventId',
                'product.id as product_id',
                'product.name',
                'product.description',
                'categories.name as category',
                'brands.name as brand',
                'product.sku',
                'product.barcode',
                'product.price',
                'product.cost',
                'product.condition',
                'product.size_type',
                'product.qty',
            ])
            ->map(function ($record): array {
                $product = Product::query()->with(['variants.color', 'colors', 'category.parent'])->findOrFail((int) $record->product_id);

                return [
                    'eventId' => $record->eventId,
                    'name' => $record->name,
                    'description' => $record->description,
                    'category' => $record->category,
                    'brand' => $record->brand,
                    'sku' => $record->sku,
                    'barcode' => $record->barcode,
                    'priceCents' => (int) round((float) $record->price * 100),
                    'costCents' => (int) round((float) ($record->cost ?? 0) * 100),
                    'condition' => $record->condition,
                    'sizeType' => $record->size_type ?? $product->category?->apparelSizeType() ?? 'none',
                    'quantity' => (int) $record->qty,
                    'variants' => $product->variants->where('active', true)->map(fn ($variant): array => [
                        'color' => $variant->color?->name,
                        'size' => $variant->attributes['size'] ?? null,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'quantity' => (int) $variant->qty,
                    ])->values(),
                    'colors' => $product->colors->map(fn ($color): array => [
                        'name' => $color->name,
                        'imageData' => $this->colorImageData($color->image),
                    ])->values(),
                    'imageData' => $this->primaryImageData((int) $record->product_id),
                ];
            });

        return response()->json(['data' => $records]);
    }

    public function acknowledge(Request $request): JsonResponse
    {
        $data = $request->validate(['eventId' => ['required', 'uuid']]);
        $outbox = DB::table('pdv_product_outbox')->where('event_id', $data['eventId'])->first();

        if (! $outbox) {
            abort(404, 'Evento não encontrado.');
        }

        $duplicate = $outbox->acknowledged_at !== null;
        if (! $duplicate) {
            DB::table('pdv_product_outbox')
                ->where('event_id', $data['eventId'])
                ->whereNull('acknowledged_at')
                ->update(['acknowledged_at' => now(), 'updated_at' => now()]);
        }

        return response()->json(['acknowledged' => true, 'duplicate' => $duplicate]);
    }

    public function pendingSiteOrders(): JsonResponse
    {
        $orders = DB::table('pdv_site_order_outbox')
            ->join('site_test_orders', 'site_test_orders.id', '=', 'pdv_site_order_outbox.order_id')
            ->whereNull('pdv_site_order_outbox.acknowledged_at')
            ->orderBy('pdv_site_order_outbox.created_at')
            ->get([
                'pdv_site_order_outbox.event_id as eventId',
                'site_test_orders.public_id as orderId',
                'site_test_orders.id as internalOrderId',
                'site_test_orders.total_cents as totalCents',
                'site_test_orders.created_at as createdAt',
            ])
            ->map(fn ($order): array => $this->siteOrderRecord($order));

        return response()->json(['data' => $orders]);
    }

    public function siteOrderHistory(Request $request): JsonResponse
    {
        $data = $request->validate(['afterId' => ['sometimes', 'integer', 'min:0']]);
        $cursor = (int) ($data['afterId'] ?? 0);
        $orders = DB::table('site_test_orders')
            ->join('pdv_site_order_outbox', 'pdv_site_order_outbox.order_id', '=', 'site_test_orders.id')
            ->where('site_test_orders.id', '>', $cursor)
            ->orderBy('site_test_orders.id')
            ->limit(100)
            ->get([
                'pdv_site_order_outbox.event_id as eventId',
                'site_test_orders.public_id as orderId',
                'site_test_orders.id as internalOrderId',
                'site_test_orders.total_cents as totalCents',
                'site_test_orders.created_at as createdAt',
            ]);

        return response()->json([
            'data' => $orders->map(fn ($order): array => $this->siteOrderRecord($order)),
            'nextCursor' => $orders->isNotEmpty() ? (int) $orders->last()->internalOrderId : $cursor,
        ]);
    }

    public function acknowledgeSiteOrder(Request $request): JsonResponse
    {
        $data = $request->validate(['eventId' => ['required', 'uuid']]);
        $outbox = DB::table('pdv_site_order_outbox')->where('event_id', $data['eventId'])->first();

        if (! $outbox) {
            abort(404, 'Evento de pedido não encontrado.');
        }

        $duplicate = $outbox->acknowledged_at !== null;
        if (! $duplicate) {
            DB::table('pdv_site_order_outbox')
                ->where('event_id', $data['eventId'])
                ->whereNull('acknowledged_at')
                ->update(['acknowledged_at' => now(), 'updated_at' => now()]);
        }

        return response()->json(['acknowledged' => true, 'duplicate' => $duplicate]);
    }

    private function siteOrderRecord(object $order): array
    {
        $items = DB::table('site_test_order_items')
            ->where('order_id', $order->internalOrderId)
            ->orderBy('id')
            ->get(['sku', 'quantity', 'unit_price_cents', 'total_cents'])
            ->map(fn ($item): array => [
                'sku' => $item->sku,
                'quantity' => (int) $item->quantity,
                'unitPriceCents' => (int) $item->unit_price_cents,
                'totalCents' => (int) $item->total_cents,
            ]);

        return [
            'eventId' => $order->eventId,
            'orderId' => $order->orderId,
            'createdAt' => Carbon::parse($order->createdAt)->toIso8601String(),
            'totalCents' => (int) $order->totalCents,
            'items' => $items,
        ];
    }

    public function review(): JsonResponse
    {
        $products = Product::query()
            ->with('category:id,name')
            ->orderBy('id')
            ->paginate(50);
        $linkedIds = DB::table('pdv_identifiers')
            ->whereIn('product_id', $products->getCollection()->pluck('id'))
            ->pluck('product_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        return response()->json([
            'data' => $products->getCollection()->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category?->name,
                'priceCents' => (int) round((float) $product->price * 100),
                'quantity' => (int) $product->qty,
                'linked' => in_array($product->id, $linkedIds, true),
            ]),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }

    public function link(Request $request, PdvIdentifierAllocator $allocator): JsonResponse
    {
        $data = $request->validate([
            'productId' => ['required', 'integer', 'exists:product,id'],
            'sku' => ['required', 'string', 'regex:/^PDV-[0-9]{6,}$/', 'max:100'],
            'barcode' => ['required', 'string', 'regex:/^2[0-9][0-9]{11}$/'],
        ]);

        if (! $allocator->isValidBarcode($data['barcode'])) {
            throw ValidationException::withMessages(['barcode' => 'O código de barras informado é inválido.']);
        }

        DB::transaction(function () use ($data, $allocator): void {
            $product = Product::query()->lockForUpdate()->findOrFail($data['productId']);
            $allocator->claim($data['sku'], $data['barcode'], $product);
        });

        return response()->json(['linked' => true, 'productId' => (int) $data['productId']]);
    }

    private function categoryFor(string $name): Category
    {
        $name = Str::title(Str::squish($name));
        $category = Category::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($category) {
            return $category;
        }

        $baseSlug = Str::slug($name) ?: 'categoria';

        return Category::query()->create([
            'name' => $name,
            'slug' => $baseSlug.'-'.Str::lower(Str::random(6)),
            'active' => true,
        ]);
    }

    private function brandFor(?string $name): ?Brand
    {
        if (! $name) {
            return null;
        }

        $name = Str::title(Str::squish($name));
        $brand = Brand::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($brand) {
            return $brand;
        }

        $baseSlug = Str::slug($name) ?: 'marca';

        return Brand::query()->create([
            'name' => $name,
            'slug' => $baseSlug.'-'.Str::lower(Str::random(6)),
            'active' => true,
        ]);
    }

    private function decodeImages(array $images): array
    {
        $totalLength = 0;
        $decoded = [];

        foreach ($images as $image) {
            $totalLength += strlen($image);
            if ($totalLength > 1_300_000) {
                throw ValidationException::withMessages([
                    'images' => 'O conjunto das imagens excede o limite de sincronização.',
                ]);
            }

            if (! preg_match('/^data:image\/jpeg;base64,([A-Za-z0-9+\/]+={0,2})$/', $image, $matches)) {
                throw ValidationException::withMessages([
                    'images' => 'As imagens recebidas do PDV devem estar no formato JPEG.',
                ]);
            }

            $bytes = base64_decode($matches[1], true);
            if ($bytes === false || strlen($bytes) > 750 * 1024
                || ! str_starts_with($bytes, "\xFF\xD8\xFF")) {
                throw ValidationException::withMessages([
                    'images' => 'Uma imagem JPEG é inválida ou excede o tamanho permitido.',
                ]);
            }

            $decoded[] = $bytes;
        }

        return $decoded;
    }

    private function primaryImageData(int $productId): ?string
    {
        $image = ProductImage::query()
            ->where('product_id', $productId)
            ->orderByDesc('is_main')
            ->orderBy('position')
            ->first();

        if (! $image) {
            return null;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($image->image)) {
            abort(500, 'O arquivo da imagem principal do produto não foi encontrado.');
        }

        $mimeType = $disk->mimeType($image->image);
        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            abort(500, 'O formato da imagem principal não pode ser enviado ao PDV.');
        }

        return 'data:'.$mimeType.';base64,'.base64_encode($disk->get($image->image));
    }

    private function colorImageData(string $imagePath): string
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($imagePath)) {
            abort(500, 'O arquivo da imagem de uma cor não foi encontrado.');
        }

        $mimeType = $disk->mimeType($imagePath);
        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            abort(500, 'O formato da imagem de uma cor não pode ser enviado ao PDV.');
        }

        return 'data:'.$mimeType.';base64,'.base64_encode($disk->get($imagePath));
    }

    private function decimalFromCents(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
