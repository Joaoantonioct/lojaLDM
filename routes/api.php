<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoInspiradoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas API para produtos inspirados
Route::get('/produtos-inspirados/carregar-mais', [ProdutoInspiradoController::class, 'carregarMais'])->name('api.produtos-inspirados.carregar-mais');
Route::post('/produtos-inspirados/atualizar-por-visualizacao', [ProdutoInspiradoController::class, 'atualizarPorVisualizacao'])->name('api.produtos-inspirados.atualizar-visualizacao');

// Rotas API para carrinho (requer autenticação)
Route::middleware('auth:sanctum')->post('/carrinho/adicionar', [ProdutoInspiradoController::class, 'adicionarAoCarrinho'])->name('api.carrinho.adicionar');
