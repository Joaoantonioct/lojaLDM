<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController
{
    /**
     * Exibir formulário de registro.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Processar o registro do usuário.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'data_nascimento' => 'required|date|before:today',
            'telefone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'senha' => 'required|string|min:6|confirmed',
            'cep' => 'nullable|string|max:9',
            'cidade' => 'nullable|string|max:100',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'termos' => 'accepted'
        ]);

        // Lógica de criação do usuário
        // Aqui você pode salvar no banco de dados
        
        return redirect()->route('login')->with('success', 'Conta criada com sucesso! Faça login para continuar.');
    }
}
