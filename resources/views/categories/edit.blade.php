@extends('layouts.main')

@section('title', 'Editar Categoria')

@section('conteudo')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Categoria #{{ $id }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.update', $id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="Categoria Exemplo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3">Descrição da categoria...</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="icone" class="form-label">Ícone (Bootstrap Icons)</label>
                            <input type="text" class="form-control" id="icone" name="icone" value="bi-tag" placeholder="Ex: bi-tag">
                            <small class="form-text text-muted">Use classes do Bootstrap Icons</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cor" class="form-label">Cor</label>
                            <select class="form-control" id="cor" name="cor">
                                <option value="primary" selected>Azul</option>
                                <option value="success">Verde</option>
                                <option value="danger">Vermelho</option>
                                <option value="warning">Amarelo</option>
                                <option value="info">Ciano</option>
                                <option value="dark">Escuro</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ativa" name="ativa" checked>
                                <label class="form-check-label" for="ativa">
                                    Categoria Ativa
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.show', $id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
