@extends('layouts.main')

@section('title', 'Pedidos')

@section('conteudo')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Meus Pedidos</h1>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Pedido
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#001</td>
                            <td>20/04/2026</td>
                            <td>João Silva</td>
                            <td class="text-primary fw-bold">R$ 299,70</td>
                            <td><span class="badge bg-success">Entregue</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('orders.show', 1) }}" class="btn btn-outline-primary">Ver</a>
                                    <a href="{{ route('orders.edit', 1) }}" class="btn btn-outline-warning">Editar</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#002</td>
                            <td>19/04/2026</td>
                            <td>Maria Santos</td>
                            <td class="text-primary fw-bold">R$ 149,90</td>
                            <td><span class="badge bg-warning">Em transporte</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('orders.show', 2) }}" class="btn btn-outline-primary">Ver</a>
                                    <a href="{{ route('orders.edit', 2) }}" class="btn btn-outline-warning">Editar</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#003</td>
                            <td>18/04/2026</td>
                            <td>Pedro Costa</td>
                            <td class="text-primary fw-bold">R$ 89,90</td>
                            <td><span class="badge bg-info">Em preparação</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('orders.show', 3) }}" class="btn btn-outline-primary">Ver</a>
                                    <a href="{{ route('orders.edit', 3) }}" class="btn btn-outline-warning">Editar</a>
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
