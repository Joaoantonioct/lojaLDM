<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    /**
     * Exibe os detalhes de um produto específico
     */
    public function detalhe($slug)
    {
        // Busca produto pelo slug ou retorna 404 se não encontrado
        $produto = Produto::where('slug', $slug)
            ->where('ativo', true)
            ->firstOrFail();

        // Busca produtos relacionados (mesma categoria, excluindo o atual)
        $produtosRelacionados = Produto::where('categoria_id', $produto->categoria_id)
            ->where('id', '!=', $produto->id)
            ->where('ativo', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('produto.detalhe', compact('produto', 'produtosRelacionados'));
    }
}
