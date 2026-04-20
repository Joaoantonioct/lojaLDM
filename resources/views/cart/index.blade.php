@extends('layouts.main')

@section('title', 'Carrinho de Compras')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-cart3"></i> Meu Carrinho</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(count($cart) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preço</th>
                                        <th>Quantidade</th>
                                        <th>Subtotal</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $id => $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item['image'] }}" width="50" class="rounded me-2" alt="{{ $item['name'] }}">
                                                    <div>
                                                        <strong>{{ $item['name'] }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-primary fw-bold">R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.update', $id) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm" style="width: 80px;" onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td class="text-primary fw-bold">R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.remove', $id) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover" onclick="return confirm('Tem certeza que deseja remover este item?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Seu carrinho está vazio</h4>
                            <p class="text-muted">Adicione produtos para continuar comprando.</p>
                            <a href="{{ route('produtos.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Continuar Comprando
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <strong>R$ 15,00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto:</span>
                        <strong class="text-success">-R$ 0,00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5">Total:</span>
                        <strong class="h5 text-primary">R$ {{ number_format($total + 15, 2, ',', '.') }}</strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" @if(count($cart) == 0) disabled @endif>
                            <i class="bi bi-credit-card"></i> Finalizar Compra
                        </button>
                        <button class="btn btn-outline-danger" onclick="if(confirm('Tem certeza que deseja limpar o carrinho?')) window.location.href='{{ route('cart.clear') }}'">
                            <i class="bi bi-trash"></i> Limpar Carrinho
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
