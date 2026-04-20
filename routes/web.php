<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', [HomeController::class, 'index']);

// Rotas Resource para Produtos
Route::resource('produtos', ProductController::class);

// Rotas Resource para Categorias
Route::resource('categories', CategoryController::class);

// Rotas Resource para Pedidos
Route::resource('orders', OrderController::class);

// Rotas Resource para Usuários
Route::resource('users', UserController::class);

// Rotas para Carrinho
Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrinho/adicionar', [CartController::class, 'add'])->name('cart.add');
Route::delete('/carrinho/remover/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::put('/carrinho/atualizar/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrinho/limpar', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/contato', function () {
    return view('contato');
});

Route::get('/sobre', function () {
    return view('sobre');
});

// Rotas Administrativas Protegidas
Route::prefix('admin')->middleware('admin')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Produtos Admin
    Route::resource('products', ProductAdminController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy'
    ]);
    
    // Categorias Admin
    Route::resource('categories', CategoryAdminController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy'
    ]);
    
    // Pedidos Admin
    Route::resource('orders', OrderAdminController::class)->names([
        'index' => 'admin.orders.index',
        'create' => 'admin.orders.create',
        'store' => 'admin.orders.store',
        'show' => 'admin.orders.show',
        'edit' => 'admin.orders.edit',
        'update' => 'admin.orders.update',
        'destroy' => 'admin.orders.destroy'
    ]);
    
    // Usuários Admin
    Route::resource('users', UserAdminController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy'
    ]);
    
    // Login Admin (não protegido)
    Route::get('/login', function () {
        return view('admin.login');
    })->name('admin.login')->withoutMiddleware('admin');
    
    Route::post('/login', function () {
        // Lógica de login admin
        return redirect()->route('admin.dashboard');
    })->name('admin.login.post')->withoutMiddleware('admin');
    
    Route::post('/logout', function () {
        auth()->logout();
        return redirect()->route('admin.login');
    })->name('admin.logout')->withoutMiddleware('admin');
});

Route::get('/categoria', function () {
    return view('categoria');
});

Route::get('/busca', function () {
    return view('busca');
});

// Rotas de Autenticação
Route::get('/registrar', [RegisterController::class, 'create'])->name('register');
Route::post('/registrar', [RegisterController::class, 'store'])->name('register.store');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // Lógica de login
    return redirect('/')->with('success', 'Login realizado com sucesso!');
})->name('login.post');

//Route::get('/produto/{id}', function ($id = null) {
 //   return view('produto',['id' => $id]);
//});

Route::get('/produto', function () {
    $busca = request('search');
    return view('produto', ['busca' => $busca]);
});

Route::get('/checkout/carrinho', function () {
    return view('checkout.carrinho');
});

Route::get('/checkout/index', function () {
    return view('checkout.index');
});