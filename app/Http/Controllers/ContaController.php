<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContaController extends Controller
{
    public function compras()
    {
        return view('compras');
    }

    public function historico()
    {
        return view('historico');
    }

    public function perguntas()
    {
        return view('perguntas');
    }

    public function opinioes()
    {
        return view('opinioes');
    }

    public function favoritos()
    {
        return view('favoritos');
    }

    public function notificacoes()
    {
        return view('notificacoes');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
