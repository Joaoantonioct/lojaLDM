@extends('layouts.main')

@section('title', 'Detalhes do Pedido')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Pedido #{{ $id }}</h4>
                        <span class="badge bg-success">Entregue</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Dados do Cliente -->
                    <div class="mb-4">
                        <h5>Dados do Cliente</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nome:</strong> João Silva</p>
                                <p><strong>Email:</strong> joao.silva@email.com</p>
                                <p><strong>Telefone:</strong> (11) 99999-9999</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>CPF:</strong> 123.456.789-00</p>
                                <p><strong>Data do Pedido:</strong> 20/04/2026</p>
                                <p><strong>Forma Pagamento:</strong> Cartão de Crédito</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Endereço de Entrega -->
                    <div class="mb-4">
                        <h5>Endereço de Entrega</h5>
                        <p>
                            <strong>Rua:</strong> das Flores, 123<br>
                            <strong>Complemento:</strong> Apto 45<br>
                            <strong>CEP:</strong> 01234-567<br>
                            <strong>Cidade:</strong> São Paulo - SP
                        </p>
                    </div>
                    
                    <!-- Produtos do Pedido -->
                    <div class="mb-4">
                        <h5>Produtos do Pedido</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/img/placeholder.png" width="50" class="me-2 rounded" alt="Produto">
                                                <div>
                                                    <strong>Vestido Rosa</strong><br>
                                                    <small class="text-muted">Tamanho: M | Cor: Rosa</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>1</td>
                                        <td>R$ 99,90</td>
                                        <td class="fw-bold">R$ 99,90</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/img/placeholder.png" width="50" class="me-2 rounded" alt="Produto">
                                                <div>
                                                    <strong>Bolsa Preta</strong><br>
                                                    <small class="text-muted">Material: Couro</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>2</td>
                                        <td>R$ 99,90</td>
                                        <td class="fw-bold">R$ 199,80</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Status do Pedido -->
                    <div class="mb-4">
                        <h5>Status do Pedido</h5>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success" style="width: 100%">Entregue</div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small>Pedido Confirmado</small>
                            <small>Em Preparação</small>
                            <small>Em Transporte</small>
                            <small>Entregue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Resumo Financeiro</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>R$ 299,70</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <strong>R$ 15,00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto:</span>
                        <strong class="text-success">-R$ 15,00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5">Total:</span>
                        <strong class="h5 text-primary">R$ 299,70</strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.edit', $id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar Pedido
                        </a>
                        <a href="#" class="btn btn-info">
                            <i class="bi bi-printer"></i> Imprimir
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Histórico -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Histórico</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <small class="text-muted">20/04/2026 14:30</small>
                            <p class="mb-1">Pedido entregue com sucesso</p>
                        </div>
                        <div class="timeline-item">
                            <small class="text-muted">19/04/2026 10:15</small>
                            <p class="mb-1">Pedido em transporte</p>
                        </div>
                        <div class="timeline-item">
                            <small class="text-muted">18/04/2026 16:45</small>
                            <p class="mb-1">Pedido em preparação</p>
                        </div>
                        <div class="timeline-item">
                            <small class="text-muted">18/04/2026 14:20</small>
                            <p class="mb-1">Pedido confirmado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
