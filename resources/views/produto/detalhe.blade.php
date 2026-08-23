@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $produto->categoria->nome ?? 'Produtos' }}</a></li>
            <li class="breadcrumb-item active">{{ $produto->nome }}</li>
        </ol>
    </nav>

    <!-- Detalhes do Produto -->
    <div class="row">
        <!-- Imagem do Produto -->
        <div class="col-md-6 mb-4">
            <div class="product-image-container">
                <img src="{{ $produto->imagem ?? '/img/placeholder-produto.png' }}" 
                     alt="{{ $produto->nome }}" 
                     class="img-fluid rounded shadow-sm">
            </div>
        </div>

        <!-- Informações do Produto -->
        <div class="col-md-6">
            <h1 class="product-title">{{ $produto->nome }}</h1>
            
            <!-- Preços -->
            <div class="product-price mb-3">
                @if($produto->preco_antigo && $produto->preco_antigo > $produto->preco_atual)
                    <span class="price-old text-muted text-decoration-line-through">
                        {{ $produto->preco_antigo_formatado }}
                    </span>
                @endif
                <div class="price-current d-flex align-items-center">
                    <span class="price-value h3 text-primary mb-0">
                        {{ $produto->preco_atual_formatado }}
                    </span>
                    @if($produto->desconto || ($produto->preco_antigo && $produto->preco_antigo > $produto->preco_atual))
                        <span class="price-discount badge bg-danger ms-2">
                            {{ $produto->desconto_formatado }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Descrição -->
            <div class="product-description mb-4">
                <p class="text-muted">{{ $produto->descricao ?? 'Produto de alta qualidade com garantia do fabricante.' }}</p>
            </div>

            <!-- Botões de Ação -->
            <div class="product-actions mb-4">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-lg flex-grow-1" onclick="addToCart({{ $produto->id }})">
                        <i class="bi bi-cart-plus me-2"></i> Adicionar ao Carrinho
                    </button>
                    <button class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="product-info mt-4">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="bi bi-truck text-primary me-2"></i>
                        <span>Frete grátis para compras acima de R$ 100</span>
                    </div>
                    <div class="col-12 mb-2">
                        <i class="bi bi-shield-check text-primary me-2"></i>
                        <span>Garantia do fabricante</span>
                    </div>
                    <div class="col-12 mb-2">
                        <i class="bi bi-arrow-repeat text-primary me-2"></i>
                        <span>Devolução grátis em até 7 dias</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos Relacionados -->
    @if($produtosRelacionados->count() > 0)
    <section class="related-products mt-5">
        <h2 class="section-title mb-4">Produtos Relacionados</h2>
        <div class="row">
            @foreach($produtosRelacionados as $relacionado)
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card product-card h-100">
                    <div class="product-image">
                        <img src="{{ $relacionado->imagem ?? '/img/placeholder-produto.png' }}" 
                             alt="{{ $relacionado->nome }}" 
                             class="card-img-top">
                        @if($relacionado->desconto || ($relacionado->preco_antigo && $relacionado->preco_antigo > $relacionado->preco_atual))
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                {{ $relacionado->desconto_formatado }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('produto.detalhe', $relacionado->slug) }}" 
                               class="text-decoration-none text-dark">
                                {{ $relacionado->nome }}
                            </a>
                        </h5>
                        <div class="price-container">
                            @if($relacionado->preco_antigo && $relacionado->preco_antigo > $relacionado->preco_atual)
                                <small class="text-muted text-decoration-line-through">
                                    {{ $relacionado->preco_antigo_formatado }}
                                </small>
                            @endif
                            <div class="price-current">
                                <span class="text-primary fw-bold">{{ $relacionado->preco_atual_formatado }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

<style>
.product-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.product-image-container img {
    transition: transform 0.3s ease;
}

.product-image-container:hover img {
    transform: scale(1.05);
}

.product-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #333;
}

.price-old {
    font-size: 1.2rem;
}

.price-value {
    font-weight: 700;
}

.price-discount {
    font-size: 0.8rem;
}

.product-actions .btn {
    border-radius: 8px;
    font-weight: 600;
}

.product-info i {
    font-size: 1.1rem;
}

.related-products .product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e0e0e0;
}

.related-products .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.related-products .product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.related-products .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #6c757d;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #1e3a8a;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}
</style>

<script>
function addToCart(produtoId) {
    // Simulação de adição ao carrinho
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Adicionado!';
    button.classList.remove('btn-primary');
    button.classList.add('btn-success');
    button.disabled = true;
    
    console.log('Produto adicionado ao carrinho:', produtoId);
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-primary');
        button.disabled = false;
    }, 2000);
}
</script>
@endsection
