@extends('layouts.main')

@section('title', 'Admin - Produtos')

@section('conteudo')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-box"></i> Gerenciar Produtos</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Produto
        </a>
    </div>
    
    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h4>24</h4>
                    <p>Total de Produtos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h4>18</h4>
                    <p>Ativos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h4>4</h4>
                    <p>Em Estoque Baixo</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h4>2</h4>
                    <p>Sem Estoque</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabela de Produtos -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Lista de Produtos</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Buscar produto..." style="width: 200px;">
                    <select class="form-select form-select-sm" style="width: 150px;">
                        <option value="">Todos</option>
                        <option value="ativo">Ativos</option>
                        <option value="inativo">Inativos</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="/img/placeholder.png" width="50" class="rounded" alt="Produto">
                            </td>
                            <td>
                                <strong>Vestido Rosa Elegante</strong><br>
                                <small class="text-muted">SKU: VRS-001</small>
                            </td>
                            <td><span class="badge bg-info">Vestidos</span></td>
                            <td class="text-primary fw-bold">R$ 299,90</td>
                            <td>
                                <span class="badge bg-success">15 unidades</span>
                            </td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.products.show', 1) }}" class="btn btn-outline-primary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', 1) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', 1) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="/img/placeholder.png" width="50" class="rounded" alt="Produto">
                            </td>
                            <td>
                                <strong>Bolsa Preta Premium</strong><br>
                                <small class="text-muted">SKU: BPP-002</small>
                            </td>
                            <td><span class="badge bg-success">Bolsas</span></td>
                            <td class="text-primary fw-bold">R$ 199,90</td>
                            <td>
                                <span class="badge bg-warning">3 unidades</span>
                            </td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.products.show', 2) }}" class="btn btn-outline-primary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', 2) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', 2) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
