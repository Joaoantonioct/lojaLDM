@extends('layouts.main')

@section('title', 'LDM')

@section('conteudo')
<!-- Banner Principal -->
<section class="hero-section mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-3">Coleção Primavera 2026</h1>
                    <p class="lead mb-4">Descubra as últimas tendências da moda feminina com estilo e elegância para todas as ocasiões.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('produtos.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-bag"></i> Ver Coleção
                        </a>
                        <a href="#promocoes" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-tag"></i> Promoções
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <img src="/img/banner-hero.jpg" class="img-fluid rounded" alt="Coleção Primavera">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categorias em Destaque -->
<section class="categories-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Categorias em Destaque</h2>
            <p class="text-muted">Escolha por categoria e encontre o estilo perfeito para você</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 mb-4">
                <a href="#" class="text-decoration-none">
                    <div class="card category-card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-tag display-4 text-primary mb-3"></i>
                            <h5 class="card-title">Vestidos</h5>
                            <p class="card-text text-muted">Elegantes e sofisticados</p>
                            <span class="badge bg-primary">45 produtos</span>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 mb-4">
                <a href="#" class="text-decoration-none">
                    <div class="card category-card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-bag display-4 text-success mb-3"></i>
                            <h5 class="card-title">Bolsas</h5>
                            <p class="card-text text-muted">Estilo e funcionalidade</p>
                            <span class="badge bg-success">32 produtos</span>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 mb-4">
                <a href="#" class="text-decoration-none">
                    <div class="card category-card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-gem display-4 text-danger mb-3"></i>
                            <h5 class="card-title">Acessórios</h5>
                            <p class="card-text text-muted">Detalhes que fazem a diferença</p>
                            <span class="badge bg-danger">28 produtos</span>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 mb-4">
                <a href="#" class="text-decoration-none">
                    <div class="card category-card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-hearts display-4 text-warning mb-3"></i>
                            <h5 class="card-title">Lingerie</h5>
                            <p class="card-text text-muted">Conforto e sensualidade</p>
                            <span class="badge bg-warning">18 produtos</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Produtos em Destaque -->
<section class="featured-products py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Produtos em Destaque</h2>
            <p class="text-muted">As peças mais amadas da nossa coleção</p>
        </div>
        
        <div class="row g-4">
            <!-- Produto 1 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-20%</span>
                        <img src="/img/vestido-rosa.jpg" class="card-img-top" alt="Vestido Rosa">
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Vestido Rosa Elegante</h5>
                            <div class="text-end">
                                <div class="text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                            </div>
                        </div>
                        <p class="card-text text-muted small">Vestido feminino elegante, perfeito para ocasiões especiais e eventos formais.</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 text-primary">R$ 239,92</span>
                            <span class="badge bg-success">Em Estoque</span>
                        </div>
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="1">
                                <input type="hidden" name="product_name" value="Vestido Rosa Elegante">
                                <input type="hidden" name="product_price" value="239.92">
                                <input type="hidden" name="product_image" value="/img/vestido-rosa.jpg">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                            </form>
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="bi bi-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Produto 2 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">NOVO</span>
                        <img src="/img/bolsa-preta.jpg" class="card-img-top" alt="Bolsa Preta">
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Bolsa Preta Premium</h5>
                            <div class="text-end">
                                <div class="text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="card-text text-muted small">Bolsa de couro genuíno com acabamento premium, espaçoso e elegante.</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 text-primary">R$ 189,90</span>
                            <span class="badge bg-success">Em Estoque</span>
                        </div>
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="2">
                                <input type="hidden" name="product_name" value="Bolsa Preta Premium">
                                <input type="hidden" name="product_price" value="189.90">
                                <input type="hidden" name="product_image" value="/img/bolsa-preta.jpg">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                            </form>
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="bi bi-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Produto 3 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card product-card h-100">
                    <img src="/img/vestido-azul.jpg" class="card-img-top" alt="Vestido Azul">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Vestido Azul Clássico</h5>
                            <div class="text-end">
                                <div class="text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                        </div>
                        <p class="card-text text-muted small">Vestido azul marinho, ideal para eventos formais e ocasiões especiais.</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 text-primary">R$ 199,90</span>
                            <span class="badge bg-warning">Últimas 3 unidades</span>
                        </div>
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="3">
                                <input type="hidden" name="product_name" value="Vestido Azul Clássico">
                                <input type="hidden" name="product_price" value="199.90">
                                <input type="hidden" name="product_image" value="/img/vestido-azul.jpg">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                            </form>
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="bi bi-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Produto 4 -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card product-card h-100">
                    <img src="/img/conjunto-lingerie.jpg" class="card-img-top" alt="Conjunto Lingerie">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Conjunto Lingerie</h5>
                            <div class="text-end">
                                <div class="text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                            </div>
                        </div>
                        <p class="card-text text-muted small">Conjunto de lingerie elegante e confortável, perfeito para ocasiões especiais.</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 text-primary">R$ 159,90</span>
                            <span class="badge bg-success">Em Estoque</span>
                        </div>
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="4">
                                <input type="hidden" name="product_name" value="Conjunto Lingerie">
                                <input type="hidden" name="product_price" value="159.90">
                                <input type="hidden" name="product_image" value="/img/conjunto-lingerie.jpg">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                            </form>
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="bi bi-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('produtos.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-arrow-right"></i> Ver Todos os Produtos
            </a>
        </div>
    </div>
</section>

<!-- Seção de Promoções -->
<section id="promocoes" class="promotions-section py-5 bg-dark text-white">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">🔥 Ofertas Imperdíveis</h2>
            <p class="text-muted">Aproveite descontos exclusivos por tempo limitado</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card promotion-card bg-white text-dark">
                    <div class="card-header bg-danger text-white text-center">
                        <h5 class="mb-0">PROMOÇÃO RELÂMPAGO</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="/img/promocao-vestido.jpg" class="img-fluid rounded mb-3" alt="Promoção Vestido">
                        <h4>Super Vestido Floral</h4>
                        <p class="text-decoration-line-through text-muted">De: R$ 299,90</p>
                        <p class="h3 text-danger">Por: R$ 149,90</p>
                        <p class="badge bg-danger">ECONOMIZE R$ 150,00</p>
                        <form method="POST" action="{{ route('cart.add') }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="product_id" value="5">
                            <input type="hidden" name="product_name" value="Super Vestido Floral">
                            <input type="hidden" name="product_price" value="149.90">
                            <input type="hidden" name="product_image" value="/img/promocao-vestido.jpg">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="bi bi-lightning-fill"></i> Aproveitar Oferta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="newsletter-content">
                    <h3 class="fw-bold mb-3">Receba Novidades e Promoções</h3>
                    <p class="text-muted mb-4">Cadastre-se em nossa newsletter e seja a primeira a saber sobre novas coleções e ofertas exclusivas.</p>
                    <form class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Seu melhor email">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-envelope"></i> Cadastrar
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="bi bi-truck display-1 text-primary mb-3"></i>
                    <h4>Frete Grátis para compras acima de R$ 200</h4>
                    <p class="text-muted">Em todo o território nacional. Entrega rápida e segura.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* ===================================
   ESTILOS DA PÁGINA WELCOME - LDM
   =================================== */

/* Banner Principal */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
}

.hero-content h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.hero-content p {
    font-size: 1.2rem;
    opacity: 0.9;
}

.hero-image img {
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

/* Cards de Categorias */
.category-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.category-card .display-4 {
    font-size: 3rem;
}

/* Cards de Produtos */
.product-card {
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.product-card .position-relative {
    overflow: hidden;
}

.product-card .badge {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
}

/* Cards de Promoção */
.promotion-card {
    border: 2px solid #dc3545;
    transition: all 0.3s ease;
}

.promotion-card:hover {
    transform: scale(1.02);
}

/* Seções */
.categories-section,
.featured-products {
    padding: 60px 0;
}

.promotions-section {
    padding: 80px 0;
}

.newsletter-section {
    padding: 60px 0;
}

/* Newsletter */
.newsletter-content form input {
    border-radius: 50px;
    border: none;
    padding: 12px 20px;
}

.newsletter-content form button {
    border-radius: 50px;
    padding: 12px 30px;
}

/* Responsividade */
@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2rem;
    }
    
    .hero-section {
        padding: 40px 0;
    }
    
    .categories-section,
    .featured-products,
    .promotions-section,
    .newsletter-section {
        padding: 40px 0;
    }
}
</style>
@endpush
