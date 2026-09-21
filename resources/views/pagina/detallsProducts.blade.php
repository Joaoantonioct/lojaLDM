@extends('layouts.main')

@section('title', 'Detalhes do Produto')

@section('content')

<main class="product-page">
    @php
        $images = $product->images;
        $mainImage = $images->first();
        $fallbackImage = asset('img/toddy.jpg');
    @endphp

    <p class="product-notice">Confira também produtos relacionados a {{ $product->category?->name ?? 'esta categoria' }}.</p>

    <nav class="product-breadcrumbs" aria-label="Navegação estrutural">
        <span><a href="{{ url('/') }}">Voltar à lista</a> &nbsp;|&nbsp; {{ $product->category?->name ?? 'Produtos' }} &nbsp;&gt;&nbsp; {{ $product->name }}</span>
        <span>
            <button class="product-share-trigger" type="button" aria-haspopup="dialog" aria-controls="share-product-modal">
                Compartilhar
            </button>
        </span>
    </nav>

    <section class="product-main">
        <div class="product-gallery">
            <div class="product-thumbnails" aria-label="Miniaturas do produto">
                @forelse($images as $image)
                    <button class="product-thumbnail {{ $loop->first ? 'active' : '' }}" type="button" data-image="{{ asset('storage/'.$image->image) }}"><img src="{{ asset('storage/'.$image->image) }}" alt="{{ $product->name }}"></button>
                @empty
                    <button class="product-thumbnail active" type="button" data-image="{{ $fallbackImage }}"><img src="{{ $fallbackImage }}" alt="{{ $product->name }}"></button>
                @endforelse
            </div>
            <div class="product-hero-image"><img id="product-main-image" src="{{ $mainImage ? asset('storage/'.$mainImage->image) : $fallbackImage }}" alt="{{ $product->name }}"></div>
        </div>

        <aside class="product-info">
            <p class="product-meta">{{ $product->active ? 'Produto disponível' : 'Produto privado' }} &nbsp;|&nbsp; {{ $product->brand?->name ?? 'Marca não informada' }}</p>
            <h1 class="product-title">{{ $product->name }}</h1>
            <div class="product-rating">Produto verificado <span>SKU: {{ $product->sku ?? 'não informado' }}</span></div>
            <div class="product-price">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</div>
            <span class="product-discount">Preço atual</span>
            <p><a class="payment-link" href="#pagamento">Ver mais detalhes de pagamento</a></p>
            <div class="product-benefit"><strong>ENTREGA GRÁTIS</strong><p>Consulte as condições de entrega para este produto.</p><a class="shipping-link" href="#envio">Mais detalhes e formas de entrega</a></div>
            <div class="product-specs"><div>Categoria: <strong>{{ $product->category?->name ?? 'Não informada' }}</strong></div><div>Estoque disponível <strong>{{ $product->qty }} unidade(s)</strong></div><div>Descrição: <strong>{{ $product->description ?: 'Sem descrição informada.' }}</strong></div></div>
            <div class="product-actions"><button class="product-button buy" type="button">Comprar agora</button><button class="product-button cart" type="button">Adicionar ao carrinho</button></div>
        </aside>
    </section>

    <section class="related-products" aria-labelledby="related-title">
        <div class="related-heading"><h2 id="related-title">Produtos relacionados</h2><a href="{{ url('/') }}">Ver mais</a></div>
        <div class="related-grid">
            @forelse($relatedProducts as $relatedProduct)
                @php
                    $relatedImage = $relatedProduct->images->firstWhere('is_main', true) ?? $relatedProduct->images->first();
                @endphp
                <article class="related-card"><a href="{{ route('product.show', $relatedProduct) }}"><img src="{{ $relatedImage ? asset('storage/'.$relatedImage->image) : $fallbackImage }}" alt="{{ $relatedProduct->name }}"></a><div class="related-copy"><h3>{{ $relatedProduct->name }}</h3><span class="related-price">R$ {{ number_format((float) $relatedProduct->price, 2, ',', '.') }}</span><div class="related-shipping">{{ $relatedProduct->qty > 0 ? 'Em estoque' : 'Indisponível' }}</div></div></article>
            @empty
                <p>Nenhum produto relacionado encontrado.</p>
            @endforelse
        </div>
    </section>
</main>

@include('components.product.share-modal', ['product' => $product])
@endsection