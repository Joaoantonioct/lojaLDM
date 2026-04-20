@extends('layouts.main')

@section('title', 'Detalhes da Categoria')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Detalhes da Categoria #{{ $id }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <i class="bi bi-tag display-1 text-primary"></i>
                        </div>
                        <div class="col-md-9">
                            <h3>Nome da Categoria</h3>
                            <p class="text-muted">Descrição detalhada da categoria aparecerá aqui.</p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">12 produtos</span>
                                <span class="badge bg-success">Ativa</span>
                            </div>
                            
                            <hr>
                            
                            <h5>Produtos nesta Categoria:</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <img src="/img/placeholder.png" class="card-img-top" alt="Produto">
                                        <div class="card-body">
                                            <h6 class="card-title">Produto 1</h6>
                                            <p class="card-text text-primary">R$ 99,90</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <img src="/img/placeholder.png" class="card-img-top" alt="Produto">
                                        <div class="card-body">
                                            <h6 class="card-title">Produto 2</h6>
                                            <p class="card-text text-primary">R$ 149,90</p>
                                        </div>
                                    </div>
                                </div>
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
                        <a href="{{ route('categories.edit', $id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar Categoria
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $id) }}" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Excluir Categoria
                            </button>
                        </form>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
