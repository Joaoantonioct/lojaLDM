@extends('layouts.main')

@section('title', 'Produtos')

@section('conteudo')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista de Produtos</h1>
        <a href="{{ route('produtos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Produto
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="/img/placeholder.png" class="card-img-top" alt="Vestido Rosa">
                <div class="card-body">
                    <h5 class="card-title">Vestido Rosa Elegante</h5>
                    <p class="card-text">Vestido feminino elegante, perfeito para ocasiões especiais.</p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="h5 text-primary">R$ 299,90</span>
                        <span class="badge bg-success">Em Estoque</span>
                    </div>
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="1">
                            <input type="hidden" name="product_name" value="Vestido Rosa Elegante">
                            <input type="hidden" name="product_price" value="299.90">
                            <input type="hidden" name="product_image" value="/img/placeholder.png">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                        </form>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="/img/placeholder.png" class="card-img-top" alt="Bolsa Preta">
                <div class="card-body">
                    <h5 class="card-title">Bolsa Preta Premium</h5>
                    <p class="card-text">Bolsa de couro genuíno com acabamento premium.</p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="h5 text-primary">R$ 199,90</span>
                        <span class="badge bg-success">Em Estoque</span>
                    </div>
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="2">
                            <input type="hidden" name="product_name" value="Bolsa Preta Premium">
                            <input type="hidden" name="product_price" value="199.90">
                            <input type="hidden" name="product_image" value="/img/placeholder.png">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                        </form>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="/img/placeholder.png" class="card-img-top" alt="Vestido Azul">
                <div class="card-body">
                    <h5 class="card-title">Vestido Azul Clássico</h5>
                    <p class="card-text">Vestido azul marinho, ideal para eventos formais.</p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="h5 text-primary">R$ 249,90</span>
                        <span class="badge bg-warning">Últimas 3 unidades</span>
                    </div>
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="3">
                            <input type="hidden" name="product_name" value="Vestido Azul Clássico">
                            <input type="hidden" name="product_price" value="249.90">
                            <input type="hidden" name="product_image" value="/img/placeholder.png">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                        </form>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection