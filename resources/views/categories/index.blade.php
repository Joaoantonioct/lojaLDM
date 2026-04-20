@extends('layouts.main')

@section('title', 'Categorias')

@section('conteudo')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Categorias</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Categoria
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-tag display-4 text-primary"></i>
                    <h5 class="mt-3">Vestidos</h5>
                    <p class="text-muted">12 produtos</p>
                    <div class="btn-group" role="group">
                        <a href="{{ route('categories.show', 1) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                        <a href="{{ route('categories.edit', 1) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-bag display-4 text-success"></i>
                    <h5 class="mt-3">Bolsas</h5>
                    <p class="text-muted">8 produtos</p>
                    <div class="btn-group" role="group">
                        <a href="{{ route('categories.show', 2) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                        <a href="{{ route('categories.edit', 2) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-gem display-4 text-danger"></i>
                    <h5 class="mt-3">Acessórios</h5>
                    <p class="text-muted">15 produtos</p>
                    <div class="btn-group" role="group">
                        <a href="{{ route('categories.show', 3) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                        <a href="{{ route('categories.edit', 3) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
