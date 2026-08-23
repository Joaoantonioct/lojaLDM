<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdutoInspiradoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ContaController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::get('/login', [HomeController::class, 'login'])->name('login');

// Rotas para produtos
Route::get('/produto/{slug}', [ProdutoController::class, 'detalhe'])->name('produto.detalhe');

// Rotas para produtos inspirados
Route::get('/produtos-inspirados', [ProdutoInspiradoController::class, 'index'])->name('produtos.inspirados');

// Rota de teste para página de login
//Route::get('/login-teste', function () {
  //  return view('auth.login-teste');
//})->name('login.teste');

// Rotas de autenticação Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Rotas privadas (requer autenticação)
Route::middleware(['auth'])->group(function () {
    Route::get('/compras', [ContaController::class, 'compras'])->name('compras');
    Route::get('/historico', [ContaController::class, 'historico'])->name('historico');
    Route::get('/perguntas', [ContaController::class, 'perguntas'])->name('perguntas');
    Route::get('/opinioes', [ContaController::class, 'opinioes'])->name('opinioes');
    Route::get('/favoritos', [ContaController::class, 'favoritos'])->name('favoritos');
    Route::get('/notificacoes', [ContaController::class, 'notificacoes'])->name('notificacoes');
    Route::post('/logout', [ContaController::class, 'logout'])->name('logout');
    
    // Rotas para carrinho, checkout, pedidos e perfil
    Route::get('/carrinho', function () { return view('carrinho'); })->name('carrinho');
    Route::get('/checkout', function () { return view('checkout'); })->name('checkout');
    Route::get('/pedidos', function () { return view('pedidos'); })->name('pedidos');
    Route::get('/perfil', function () { return view('perfil'); })->name('perfil');
});
