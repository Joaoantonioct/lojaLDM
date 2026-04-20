<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController
{
    /**
     * Exibir uma listagem do recurso.
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Mostrar o formulário para criar um novo recurso.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Armazene um recurso recém-criado no armazenamento..
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Exibir o recurso especificado.
     */
    public function show(string $id)
    {
        return view('products.show', ['id' => $id]);
    }

    /**
     * Mostrar o formulário para editar o recurso especificado.
     */
    public function edit(string $id)
    {
        return view('products.edit', ['id' => $id]);
    }

    /**
     * Atualize o recurso especificado no armazenamento.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remova o recurso especificado do armazenamento.
     */
    public function destroy(string $id)
    {
        //
    }
}
