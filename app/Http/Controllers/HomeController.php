<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController
{
    public function index()
    {
        

    $dados = [
        'opcao1' =>[
            'nome' => 'Fabiana sama',
            'cpf' => '070.419.481-37',
            'idade' => 17
        ],
        'opcao2' =>[
            'nome' => 'Luciana sama',
            'cpf' => '123.456.789-00',
            'idade' => 17
        ],
        'opcao3' =>[
            'nome' => 'Sabrina sama',
            'cpf' => '987.654.321-00',
            'idade' => 17
        ],
        'opcao4' =>[
            'nome' => 'Aline sama',
            'cpf' => '111.222.333.44',
            'idade' => 17
        ]
    ];
    return view('welcome', compact('dados'));
    }
}
