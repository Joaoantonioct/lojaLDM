<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LDMcontroller;
use App\Http\Controllers\SiteTestOrderController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [LDMcontroller::class, 'index'])->name('home');
Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::middleware('auth')->post('/carrinho/finalizar', [SiteTestOrderController::class, 'store'])
    ->name('cart.test.store');
Route::get('/pedido-de-teste/{publicId}', [SiteTestOrderController::class, 'complete'])
    ->whereUuid('publicId')
    ->name('cart.test.complete');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/cliente', [LDMcontroller::class, 'client'])
    ->middleware('auth')
    ->name('cliente');
Route::get('/pagina/detallsProducts', [LDMcontroller::class, 'detalls']);
Route::get('/product/{product}', [LDMcontroller::class, 'detalls'])->name('product.show');
Route::get('/pagina/add', [LDMcontroller::class, 'add']);
Route::post('/pagina/add', [LDMcontroller::class, 'store']);
Route::post('/pagina/marcas', [LDMcontroller::class, 'storeBrand'])->name('brands.store');
Route::get('/pagina/categorias', [LDMcontroller::class, 'categories']);
Route::post('/pagina/categorias', [LDMcontroller::class, 'storeCategory'])->name('categories.store');

Route::get('/pesquisa', function () {

    $pesquisa = trim((string) request('search', ''));

    $consultaBase = Product::with(['category', 'brand', 'images', 'currentPromotion'])
        ->where('active', true);

    if ($pesquisa !== '') {
        $consultaBase->where(function ($query) use ($pesquisa) {
            $query->where('name', 'like', "%{$pesquisa}%")
                ->orWhere('description', 'like', "%{$pesquisa}%")
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

    $produtosEncontrados = (clone $consultaBase)->get();
    $categorias = $produtosEncontrados->pluck('category')
        ->filter()
        ->unique('id')
        ->sortBy('name');
    $marcas = $produtosEncontrados->pluck('brand')
        ->filter()
        ->unique('id')
        ->sortBy('name');

    $categoriasSelecionadas = array_filter((array) request('categories'));
    $marcasSelecionadas = array_filter((array) request('brands'));
    $condicoesSelecionadas = array_values(array_intersect(
        (array) request('conditions', []),
        ['novo', 'seminovo', 'brecho'],
    ));

    if ($categoriasSelecionadas) {
        $subcategoriasSelecionadas = Category::whereIn('parent_id', $categoriasSelecionadas)
            ->pluck('id')
            ->all();

        $consultaBase->whereIn('category_id', array_unique([
            ...$categoriasSelecionadas,
            ...$subcategoriasSelecionadas,
        ]));
    }

    if ($marcasSelecionadas) {
        $consultaBase->whereIn('brand_id', $marcasSelecionadas);
    }

    if ($condicoesSelecionadas) {
        $consultaBase->whereIn('condition', $condicoesSelecionadas);
    }

    $filtrarPrecoEfetivo = function (string $operador, string $valor) use ($consultaBase): void {
        $consultaBase->where(function ($query) use ($operador, $valor) {
            $query->whereHas('currentPromotion', function ($promotionQuery) use ($operador, $valor) {
                $promotionQuery->whereRaw(
                    "ROUND(product.price * (10000 - product_promotions.discount_basis_points) / 10000, 2) {$operador} ?",
                    [$valor],
                );
            })->orWhere(function ($query) use ($operador, $valor) {
                $query->whereDoesntHave('currentPromotion')
                    ->where('price', $operador, $valor);
            });
        });
    };

    if (request()->filled('min_price')) {
        $filtrarPrecoEfetivo('>=', (string) request('min_price'));
    }

    if (request()->filled('max_price')) {
        $filtrarPrecoEfetivo('<=', (string) request('max_price'));
    }

    if (request()->boolean('in_stock')) {
        $consultaBase->where('qty', '>', 0);
    }

    $dados = $consultaBase->get();

    return view(auth()->check() ? 'customer.dashboard' : 'public.home', [
        'pesquisa' => $pesquisa,
        'dados' => $dados,
        'categorias' => $categorias,
        'marcas' => $marcas,
        'favoritosCount' => 0,
        'cartCount' => 0,
        'recentCount' => 0,
        'ordersCount' => 0,
    ]);

})->name('pesquisa');

Route::get('/contato', function () {
    return view('contact');
});

Route::get('/search', function () {
    $busca = request('search');

    return view('search', ['busca' => $busca]);
});
