@extends('layouts.main')

@section('title', 'Detalhes do Usuário')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Detalhes do Usuário #{{ $id }}</h4>
                        <span class="badge bg-success">Ativo</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Dados Pessoais -->
                    <div class="mb-4">
                        <h5>Dados Pessoais</h5>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <img src="/img/placeholder.png" width="150" class="rounded-circle mb-3" alt="Avatar">
                                <div class="mb-3">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-camera"></i> Alterar Foto
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nome:</strong> João Silva</p>
                                        <p><strong>CPF:</strong> 123.456.789-00</p>
                                        <p><strong>Data Nasc:</strong> 15/03/1990</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Email:</strong> joao.silva@email.com</p>
                                        <p><strong>Telefone:</strong> (11) 99999-9999</p>
                                        <p><strong>Tipo:</strong> <span class="badge bg-primary">Cliente</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Endereço -->
                    <div class="mb-4">
                        <h5>Endereço Principal</h5>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">
                                    <strong>Rua:</strong> das Flores, 123 - Apto 45<br>
                                    <strong>Bairro:</strong> Jardim São Paulo<br>
                                    <strong>CEP:</strong> 01234-567<br>
                                    <strong>Cidade:</strong> São Paulo - SP
                                </p>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary">Editar Endereço</button>
                                    <button class="btn btn-sm btn-outline-success">Adicionar Endereço</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Histórico de Pedidos -->
                    <div class="mb-4">
                        <h5>Histórico de Pedidos</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nº Pedido</th>
                                        <th>Data</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#001</td>
                                        <td>20/04/2026</td>
                                        <td class="text-primary fw-bold">R$ 299,70</td>
                                        <td><span class="badge bg-success">Entregue</span></td>
                                        <td>
                                            <a href="{{ route('orders.show', 1) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#002</td>
                                        <td>15/04/2026</td>
                                        <td class="text-primary fw-bold">R$ 149,90</td>
                                        <td><span class="badge bg-warning">Em transporte</span></td>
                                        <td>
                                            <a href="{{ route('orders.show', 2) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Preferências -->
                    <div class="mb-4">
                        <h5>Preferências</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter" checked disabled>
                                    <label class="form-check-label" for="newsletter">
                                        Receber newsletter
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="promocoes" checked disabled>
                                    <label class="form-check-label" for="promocoes">
                                        Receber promoções
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Categorias Favoritas:</strong></p>
                                <div>
                                    <span class="badge bg-primary me-1">Vestidos</span>
                                    <span class="badge bg-success me-1">Bolsas</span>
                                    <span class="badge bg-info">Acessórios</span>
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
                        <a href="{{ route('users.edit', $id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar Usuário
                        </a>
                        <a href="#" class="btn btn-info">
                            <i class="bi bi-lock"></i> Redefinir Senha
                        </a>
                        <a href="#" class="btn btn-success">
                            <i class="bi bi-envelope"></i> Enviar Email
                        </a>
                        <form method="POST" action="{{ route('users.destroy', $id) }}" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Excluir Usuário
                            </button>
                        </form>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Estatísticas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Estatísticas</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">12</h4>
                            <small class="text-muted">Pedidos</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">R$ 2.450</h4>
                            <small class="text-muted">Total Gasto</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">3</h4>
                            <small class="text-muted">Endereços</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">5</h4>
                            <small class="text-muted">Cupons Usados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
