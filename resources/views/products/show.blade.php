@extends('layouts.main')

@section('title', 'Detalhes do Produto')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Detalhes do Produto #{{ $id }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <img src="/img/placeholder.png" class="img-fluid rounded" alt="Produto">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h3>Nome do Produto</h3>
                            <p class="text-muted">Descrição detalhada do produto aparecerá aqui.</p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="text-primary">R$ 99,90</h4>
                                <span class="badge bg-success">Em estoque</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary">
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                                <button class="btn btn-outline-secondary">
                                    <i class="bi bi-heart"></i> Favoritar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('produtos.edit', $id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form method="POST" action="{{ route('produtos.destroy', $id) }}" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection