<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Services\CatalogProductSearch;
use App\Services\PdvIdentifierAllocator;
use App\Support\ProductSizeCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LDMcontroller extends Controller
{
    public function index()
    {
        $dados = Product::with(['category', 'brand', 'images', 'currentPromotion'])->get();

        return view('public.home', [
            'dados' => $dados,
            'pesquisa' => null,
        ]);
    }

    public function client(CatalogProductSearch $productSearch)
    {
        $pesquisa = trim((string) request('search', ''));
        $interestSearch = trim((string) request('interest_search', ''));
        $interestProducts = request('interest_products', []);
        $interestProductIds = collect(is_array($interestProducts) ? $interestProducts : [])
            ->filter(fn ($id) => is_scalar($id) && ctype_digit((string) $id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->take(8)
            ->values()
            ->all();

        $consulta = Product::with(['category', 'brand', 'images', 'currentPromotion'])
            ->where('active', true);

        $recomendacoes = false;
        if ($interestProductIds !== []) {
            $interesse = Product::query()
                ->with('category.parent')
                ->whereIn('id', $interestProductIds)
                ->get();

            $recomendacoes = $interesse->isNotEmpty();
        }

        if ($recomendacoes) {
            $categoryIds = $interesse->pluck('category_id')->filter()->unique()->all();
            $parentCategoryIds = $interesse
                ->map(fn (Product $product) => $product->category?->parent_id)
                ->filter()
                ->unique()
                ->all();
            $relatedCategoryIds = array_values(array_unique([
                ...$categoryIds,
                ...$parentCategoryIds,
                ...Category::query()
                    ->whereIn('parent_id', [...$categoryIds, ...$parentCategoryIds])
                    ->pluck('id')
                    ->all(),
            ]));
            $brandIds = $interesse->pluck('brand_id')->filter()->unique()->all();

            $consulta->where(function ($query) use (
                $interestProductIds,
                $relatedCategoryIds,
                $brandIds,
            ) {
                $query->whereIn('product.id', $interestProductIds);

                if ($relatedCategoryIds !== []) {
                    $query->orWhereIn('product.category_id', $relatedCategoryIds);
                }

                if ($brandIds !== []) {
                    $query->orWhereIn('product.brand_id', $brandIds);
                }
            });

            $consulta->orderByRaw(
                'CASE WHEN product.id IN ('.implode(',', $interestProductIds).') THEN 0 ELSE 1 END'
            );
        } else {
            $productSearch->apply($consulta, $pesquisa ?: $interestSearch);
            $recomendacoes = $pesquisa === '' && $interestSearch !== '';
        }

        $dados = $consulta->get();

        if ($recomendacoes && $dados->isEmpty()) {
            $dados = Product::with(['category', 'brand', 'images', 'currentPromotion'])
                ->where('active', true)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->limit(12)
                ->get();
        }

        return view('customer.dashboard', [
            'dados' => $dados,
            'activeProductIds' => Product::query()->where('active', true)->pluck('id'),
            'pesquisa' => $pesquisa ?: null,
            'recomendacoes' => $recomendacoes,
            'favoritosCount' => 0,
            'cartCount' => 0,
            'recentCount' => 0,
            'ordersCount' => 0,
        ]);
    }

    public function add()
    {
        $marcas = Brand::orderBy('name')->get();
        $categorias = Category::with('children.parent')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('pagina.add', compact('marcas', 'categorias'));
    }

    public function storeBrand(Request $request)
    {
        $request->merge(['name' => Str::title(Str::squish((string) $request->input('name')))]);
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $marcaExistente = Brand::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($dados['name'])])
            ->first();

        if ($marcaExistente) {
            return response()->json([
                'message' => 'Essa marca já está cadastrada e foi selecionada.',
                'brand' => ['id' => $marcaExistente->id, 'name' => $marcaExistente->name],
                'existing' => true,
            ]);
        }

        $baseSlug = Str::slug($dados['name']) ?: 'marca';
        $slug = $baseSlug;
        $suffix = 2;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        $marca = Brand::create([
            'name' => $dados['name'],
            'slug' => $slug,
            'active' => true,
        ]);

        return response()->json([
            'message' => 'Marca cadastrada e selecionada.',
            'brand' => ['id' => $marca->id, 'name' => $marca->name],
            'existing' => false,
        ], 201);
    }

    public function categories()
    {
        $categorias = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('pagina.categories', compact('categorias'));
    }

    public function storeCategory(Request $request)
    {
        $request->merge(['name' => Str::title(Str::squish((string) $request->input('name')))]);
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        if ($request->expectsJson()) {
            $existingCategoryQuery = Category::query()
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($dados['name'])]);

            if (empty($dados['parent_id'])) {
                $existingCategoryQuery->whereNull('parent_id');
            } else {
                $existingCategoryQuery->where('parent_id', $dados['parent_id']);
            }

            $existingCategory = $existingCategoryQuery->first();

            if ($existingCategory) {
                return response()->json([
                    'message' => empty($dados['parent_id'])
                        ? 'Essa categoria já está cadastrada e foi selecionada.'
                        : 'Essa subcategoria já está cadastrada e foi selecionada.',
                    'category' => [
                        'id' => $existingCategory->id,
                        'name' => $existingCategory->name,
                        'parent_id' => $existingCategory->parent_id,
                        'apparel' => $existingCategory->isApparelCategory(),
                        'sizeType' => $existingCategory->apparelSizeType(),
                    ],
                    'existing' => true,
                ]);
            }
        }

        $dados['slug'] = Str::slug($dados['name']).'-'.Str::lower(Str::random(6));
        $dados['active'] = true;

        $category = Category::create($dados);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => empty($dados['parent_id'])
                    ? 'Categoria cadastrada e selecionada.'
                    : 'Subcategoria cadastrada e selecionada.',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'parent_id' => $category->parent_id,
                    'apparel' => $category->isApparelCategory(),
                    'sizeType' => $category->apparelSizeType(),
                ],
                'existing' => false,
            ], 201);
        }

        return redirect('/pagina/categorias')->with(
            'success',
            $request->filled('parent_id')
                ? 'Subcategoria criada com sucesso.'
                : 'Categoria criada com sucesso.'
        );
    }

    public function store(Request $request)
    {
        $price = trim((string) $request->input('price'));

        if (str_contains($price, ',')) {
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);
        }

        $cost = trim((string) $request->input('cost'));

        if (str_contains($cost, ',')) {
            $cost = str_replace('.', '', $cost);
            $cost = str_replace(',', '.', $cost);
        }

        $categoryId = $request->input('subcategory_id') ?: $request->input('category_id');
        $sizeType = $request->input('size_type');
        $isSizedProduct = in_array($sizeType, ['clothing', 'trousers', 'footwear'], true);
        $hasColors = $request->has('colors');

        $request->merge([
            'name' => Str::title(Str::squish((string) $request->input('name'))),
            'price' => $price,
            'cost' => $cost,
            'condition' => $isSizedProduct ? $request->input('condition') : null,
        ]);

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'qty' => [Rule::requiredIf(! $isSizedProduct && ! $hasColors), 'nullable', 'integer', 'min:0'],
            'size_type' => ['required', Rule::in(['none', 'clothing', 'trousers', 'footwear'])],
            'colors' => ['sometimes', 'array', 'max:10'],
            'colors.*.name' => ['required_with:colors', 'string', 'max:50'],
            'colors.*.image' => ['required_with:colors', 'image', 'mimes:jpeg,jpg,png,webp', 'max:1024'],
            'variants' => [Rule::requiredIf($isSizedProduct || $hasColors), 'nullable', 'array', 'min:1', 'max:140'],
            'variants.*.color_index' => [Rule::requiredIf($hasColors), 'nullable', 'integer', 'min:0', 'max:9'],
            'variants.*.size' => [$isSizedProduct ? 'required' : 'nullable', 'string', Rule::in(ProductSizeCatalog::forType($sizeType))],
            'variants.*.qty' => ['required_with:variants', 'integer', 'min:0', 'max:2147483647'],
            'condition' => [
                Rule::requiredIf($isSizedProduct),
                'nullable',
                Rule::in(['novo', 'seminovo', 'brecho']),
            ],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'private' => ['required', 'boolean'],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        if (! empty($dados['subcategory_id'])) {
            $subcategoriaValida = Category::whereKey($dados['subcategory_id'])
                ->where('parent_id', $dados['category_id'])
                ->exists();

            if (! $subcategoriaValida) {
                return back()
                    ->withErrors(['subcategory_id' => 'A subcategoria não pertence à categoria selecionada.'])
                    ->withInput();
            }

            $dados['category_id'] = $dados['subcategory_id'];
        } elseif (Category::where('parent_id', $dados['category_id'])->exists()) {
            return back()
                ->withErrors(['subcategory_id' => 'Selecione uma subcategoria para a categoria escolhida.'])
                ->withInput();
        }

        $colorDefinitions = $dados['colors'] ?? [];
        $variants = $dados['variants'] ?? [];

        if ($colorDefinitions !== []) {
            $colorNames = [];
            $combinations = [];
            $colorsUsed = [];
            foreach ($colorDefinitions as $index => $color) {
                $normalizedName = mb_strtolower(Str::squish($color['name']));
                if (isset($colorNames[$normalizedName])) {
                    return back()->withErrors(['colors' => 'Não repita o nome de uma cor.'])->withInput();
                }
                $colorNames[$normalizedName] = true;
            }
            foreach ($variants as $variant) {
                $colorIndex = $variant['color_index'] ?? null;
                if (! is_numeric($colorIndex) || ! array_key_exists((int) $colorIndex, $colorDefinitions)) {
                    return back()->withErrors(['variants' => 'Cada estoque deve pertencer a uma cor cadastrada.'])->withInput();
                }
                $colorIndex = (int) $colorIndex;
                $size = $variant['size'] ?? '';
                if ($isSizedProduct && ! in_array($size, ProductSizeCatalog::forType($sizeType), true)) {
                    return back()->withErrors(['variants' => 'Selecione um tamanho válido para cada cor.'])->withInput();
                }
                $combination = $colorIndex.'|'.$size;
                if (isset($combinations[$combination])) {
                    return back()->withErrors(['variants' => 'Não repita a combinação de cor e tamanho.'])->withInput();
                }
                $combinations[$combination] = true;
                $colorsUsed[$colorIndex] = true;
            }
            if (count($colorsUsed) !== count($colorDefinitions)) {
                return back()->withErrors(['variants' => 'Informe o estoque de cada cor cadastrada.'])->withInput();
            }
        } elseif ($isSizedProduct) {
            $sizes = array_column($variants, 'size');
            if (count($sizes) !== count(array_unique($sizes))) {
                return back()->withErrors(['variants' => 'Não repita o mesmo tamanho no produto.'])->withInput();
            }
        }

        if ($isSizedProduct || $colorDefinitions !== []) {
            $dados['qty'] = array_sum(array_column($dados['variants'], 'qty'));
        }
        unset($dados['variants'], $dados['colors'], $dados['subcategory_id']);

        $dados['slug'] = Str::slug($dados['name']).'-'.Str::lower(Str::random(6));

        $images = $request->file('images', []);

        DB::transaction(function () use ($dados, $images, $variants, $colorDefinitions): void {
            $product = Product::create($dados);
            $allocator = app(PdvIdentifierAllocator::class);
            $identifiers = $allocator->allocate($product->id);
            $product->sku = $identifiers['sku'];
            $product->barcode = $identifiers['barcode'];
            $product->save();

            $productColors = [];
            foreach ($colorDefinitions as $index => $color) {
                $productColors[$index] = ProductColor::create([
                    'product_id' => $product->id,
                    'name' => Str::squish($color['name']),
                    'image' => $color['image']->store('products/'.$product->id.'/colors', 'public'),
                ]);
            }

            foreach ($variants as $variant) {
                $colorIndex = $variant['color_index'] ?? null;
                $color = $colorIndex === null ? null : $productColors[$colorIndex];
                $size = $variant['size'] ?? null;
                $variantIdentifiers = $allocator->allocate();
                $product->variants()->create([
                    'color_id' => $color?->id,
                    'name' => trim(($color?->name ?? '').' '.($size ?? '')) ?: null,
                    'sku' => $variantIdentifiers['sku'],
                    'barcode' => $variantIdentifiers['barcode'],
                    'qty' => $variant['qty'],
                    'attributes' => array_filter([
                        'color' => $color?->name,
                        'size' => $size,
                    ], fn ($value) => $value !== null && $value !== ''),
                    'active' => true,
                ]);
            }

            DB::table('pdv_product_outbox')->insert([
                'event_id' => (string) Str::uuid(),
                'product_id' => $product->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($images as $position => $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $image->store('products/'.$product->id, 'public'),
                    'is_main' => $position === 0,
                    'position' => $position,
                ]);
            }
        });

        return redirect('/')->with(
            'success',
            'Produto adicionado com sucesso.'
        );
    }

    public function detalls(?Product $product = null)
    {
        $product ??= Product::query()->where('active', true)->firstOrFail();

        $product->load([
            'category',
            'brand',
            'currentPromotion',
            'colors',
            'variants' => fn ($query) => $query->with('color')->where('active', true)->orderBy('id'),
            'images' => fn ($query) => $query
                ->orderByDesc('is_main')
                ->orderBy('position'),
        ]);

        $relatedProducts = Product::with(['category', 'brand', 'images', 'currentPromotion'])
            ->where('active', true)
            ->whereKeyNot($product->getKey())
            ->when($product->category_id, function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })
            ->latest()
            ->take(3)
            ->get();

        $brandProducts = $product->brand_id
            ? Product::with(['images', 'currentPromotion'])
                ->where('active', true)
                ->where('brand_id', $product->brand_id)
                ->whereKeyNot($product->getKey())
                ->latest()
                ->take(3)
                ->get()
            : collect();

        return view('pagina.detallsProducts', compact('product', 'relatedProducts', 'brandProducts'));
    }
}
