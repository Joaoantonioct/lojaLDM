@extends('layouts.main')

@section('title', 'Nova Categoria')

@section('conteudo')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Nova Categoria</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="icone" class="form-label">Ícone (Bootstrap Icons)</label>
                            <input type="text" class="form-control" id="icone" name="icone" placeholder="Ex: bi-tag">
                            <small class="form-text text-muted">Use classes do Bootstrap Icons</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cor" class="form-label">Cor</label>
                            <select class="form-control" id="cor" name="cor">
                                <option value="primary">Azul</option>
                                <option value="success">Verde</option>
                                <option value="danger">Vermelho</option>
                                <option value="warning">Amarelo</option>
                                <option value="info">Ciano</option>
                                <option value="dark">Escuro</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cadastrar Categoria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
