<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LDMcontroller extends Controller
{
    public function index()
    {
        $dados = Product::with(['category', 'brand', 'images'])->get();

        return view('public.home', [
            'dados' => $dados,
            'pesquisa' => null,
        ]);
    }

    public function client()
    {
        $pesquisa = trim((string) request('search', ''));

        $consulta = Product::with(['category', 'brand', 'images'])
            ->where('active', true);

        if ($pesquisa !== '') {
            $consulta->where(function ($query) use ($pesquisa) {
                $query->where('name', 'like', "%{$pesquisa}%")
                    ->orWhere('description', 'like', "%{$pesquisa}%")
                    ->orWhere('sku', 'like', "%{$pesquisa}%")
                    ->orWhereHas('brand', function ($query) use ($pesquisa) {
                        $query->where('name', 'like', "%{$pesquisa}%");
                    })
                    ->orWhereHas('category', function ($query) use ($pesquisa) {
                        $query->where('name', 'like', "%{$pesquisa}%")
                            ->orWhereHas('parent', function ($query) use ($pesquisa) {
                                $query->where('name', 'like', "%{$pesquisa}%");
                            });
                    });
            });
        }

        $dados = $consulta->get();

        return view('customer.dashboard', [
            'dados' => $dados,
            'pesquisa' => $pesquisa ?: null,
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
        $request->merge(['name' => trim((string) $request->input('name'))]);
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
        $request->merge(['name' => trim((string) $request->input('name'))]);
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

        $categoryId = $request->input('subcategory_id') ?: $request->input('category_id');
        $categoryForCondition = Category::with('parent')->find($categoryId);
        $isApparel = $categoryForCondition?->isApparelCategory() ?? false;

        $request->merge([
            'price' => $price,
            'condition' => $isApparel ? $request->input('condition') : null,
        ]);

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'qty' => ['required', 'integer', 'min:0'],
            'condition' => [
                Rule::requiredIf($isApparel),
                'nullable',
                Rule::in(['novo', 'seminovo', 'brecho']),
            ],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'private' => ['required', 'boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        if (!empty($dados['subcategory_id'])) {
            $subcategoriaValida = Category::whereKey($dados['subcategory_id'])
                ->where('parent_id', $dados['category_id'])
                ->exists();

            if (!$subcategoriaValida) {
                return back()
                    ->withErrors(['subcategory_id' => 'A subcategoria não pertence à categoria selecionada.'])
                    ->withInput();
            }

            $dados['category_id'] = $dados['subcategory_id'];
        }

        unset($dados['subcategory_id']);

        $dados['slug'] = Str::slug($dados['name']).'-'.Str::lower(Str::random(6));

        $images = $request->file('images', []);

        DB::transaction(function () use ($dados, $images): void {
            $product = Product::create($dados);

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
            'images' => fn ($query) => $query
                ->orderByDesc('is_main')
                ->orderBy('position'),
        ]);

        $relatedProducts = Product::with(['category', 'brand', 'images'])
            ->where('active', true)
            ->whereKeyNot($product->getKey())
            ->when($product->category_id, function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })
            ->latest()
            ->take(3)
            ->get();

        $brandProducts = $product->brand_id
            ? Product::with(['images'])
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