<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;

class HomeController extends Controller
{
    public function index()
    {
        // Busca produtos recentes para a seção "Novidade"
        $produtosNovidade = Produto::getProdutosRecentes(6);
        
        // Busca produtos em destaque para outras seções
        $produtosDestaque = Produto::getProdutosEmDestaque(6);
        
        return view('welcome', compact('produtosNovidade', 'produtosDestaque'));
    }

    public function register()
    {
        return view('auth.register');
    }

    public function login()
    {
        return view('auth.login-teste');
    }
}
