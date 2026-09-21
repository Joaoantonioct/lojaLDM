<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $dados = Product::with(['category', 'brand', 'images'])
            ->where('active', true)
            ->get();

        return view('customer.dashboard', [
            'dados' => $dados,
            'pesquisa' => null,
            'favoritosCount' => 0,
            'cartCount' => 0,
            'recentCount' => 0,
            'ordersCount' => 0,
        ]);
    }

    public function add()
    {
        $marcas = Brand::all();
        $categorias = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('pagina.add', compact('marcas', 'categorias'));
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
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $dados['slug'] = Str::slug($dados['name']).'-'.Str::lower(Str::random(6));
        $dados['active'] = true;

        Category::create($dados);

        return redirect('/pagina/categorias')->with(
            'success',
            $request->filled('parent_id')
                ? 'Subcategoria criada com sucesso.'
                : 'Categoria criada com sucesso.'
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'qty' => ['required', 'integer', 'min:0'],
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

        return view('pagina.detallsProducts', compact('product', 'relatedProducts'));
    }
}