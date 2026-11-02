@extends('layouts.main')

@section('title', 'Detalhes do Produto')

@section('content')

@php
    $images = $product->images;
    $mainImage = $images->first();
    $fallbackImage = asset('img/toddy.jpg');
@endphp

<main
    class="product-page"
    data-client-id="{{ auth()->id() }}"
    data-product-id="{{ $product->id }}"
    data-product-name="{{ $product->name }}"
    data-product-image="{{ $mainImage ? asset('storage/'.$mainImage->image) : asset('img/toddy.jpg') }}"
    data-product-price="{{ $product->effective_price }}"
    data-product-stock="{{ $product->qty }}"
    data-product-condition="{{ $product->conditionLabel() }}"
>
    <nav class="product-breadcrumbs" aria-label="Navegação estrutural">
        <span><a href="{{ url('/') }}">Início</a> / {{ $product->category?->name ?? 'Produtos' }} / {{ $product->name }}</span>
        <button class="product-share-trigger" type="button" aria-haspopup="dialog" aria-controls="share-product-modal">Compartilhar</button>
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
            <div class="product-hero-image">
                <img id="product-main-image" src="{{ $mainImage ? asset('storage/'.$mainImage->image) : $fallbackImage }}" alt="{{ $product->name }}" draggable="false">
                @if($images->count() > 1)
                    <button class="product-gallery-arrow product-gallery-previous" type="button" aria-label="Imagem anterior">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                    </button>
                    <button class="product-gallery-arrow product-gallery-next" type="button" aria-label="Próxima imagem">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                    </button>
                @endif
            </div>
        </div>

        <section class="product-summary" aria-labelledby="product-title">
            <p class="product-meta">{{ $product->brand?->name ?? 'LDM' }}</p>
            <div class="product-title-row">
                <h1 class="product-title" id="product-title">{{ $product->name }}</h1>
                @auth
                    <button class="product-favorite-button" type="button" data-favorite-toggle aria-pressed="false" aria-label="Adicionar aos favoritos" title="Adicionar aos favoritos">
                        <span aria-hidden="true">&#9825;</span>
                    </button>
                @else
                    <a class="product-favorite-button" href="{{ route('login', ['redirect' => '/product/'.$product->id]) }}" aria-label="Entre para salvar nos favoritos" title="Entre para salvar nos favoritos">
                        <span aria-hidden="true">&#9825;</span>
                    </a>
                @endauth
            </div>
            <span class="favorite-feedback" role="status" aria-live="polite"></span>
            <p class="product-stock-label {{ $product->qty > 0 ? 'is-available' : 'is-unavailable' }}">
                {{ $product->qty > 0 ? 'Produto disponível' : 'Sem estoque' }}
            </p>
            @if($product->currentPromotion)
                <p class="text-sm text-gray-500 line-through">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</p>
                <p class="font-semibold text-red-700">{{ number_format($product->currentPromotion->discount_basis_points / 100, 2, ',', '.') }}% OFF</p>
            @endif
            <p class="product-price">R$ {{ number_format((float) $product->effective_price, 2, ',', '.') }}</p>
            <p class="product-payment-note">Preço do produto</p>
            <div class="product-summary-divider"></div>
            <h2 class="product-summary-heading">Sobre este produto</h2>
            <p class="product-summary-description">{{ $product->description ?: 'Consulte as características do produto abaixo.' }}</p>
            <a class="product-details-link" href="#product-description">Ver descrição completa</a>
        </section>

        <aside class="product-purchase" aria-label="Comprar produto">
            <h2>Disponibilidade</h2>
            <p class="purchase-stock {{ $product->qty > 0 ? 'is-available' : 'is-unavailable' }}">
                {{ $product->qty > 0 ? 'Em estoque' : 'Sem estoque' }}
            </p>
            @if($product->qty > 0)
                <p class="purchase-quantity">{{ $product->qty }} unidade(s) disponível(is)</p>
            @endif
            <div class="product-actions">
                @if($product->qty > 0)
                    @guest
                        <a class="product-button buy" href="{{ route('login', ['redirect' => '/product/'.$product->id]) }}">Comprar agora</a>
                        <a class="product-button cart" href="{{ route('login', ['redirect' => '/product/'.$product->id]) }}">Adicionar ao carrinho</a>
                    @else
                        <button class="product-button buy" type="button" data-cart-buy-now>Comprar agora</button>
                        <button class="product-button cart" type="button" data-cart-add>Adicionar ao carrinho</button>
                    @endguest
                @else
                    <button class="product-button buy" type="button" disabled>Comprar agora</button>
                    <button class="product-button cart" type="button" disabled>Adicionar ao carrinho</button>
                @endif
            </div>
        </aside>
    </section>

    @php
        $productSections = [
            ['id' => 'related-products', 'title' => 'Produtos relacionados', 'items' => $relatedProducts],
            ['id' => 'brand-products', 'title' => 'Mais produtos da '.($product->brand?->name ?? 'marca'), 'items' => $brandProducts],
        ];
    @endphp

    @foreach($productSections as $section)
        @if($section['items']->isNotEmpty())
            <section class="product-catalog-section" id="{{ $section['id'] }}" aria-labelledby="{{ $section['id'] }}-title">
                <div class="product-section-heading">
                    <h2 id="{{ $section['id'] }}-title">{{ $section['title'] }}</h2>
                    <a href="{{ url('/') }}">Ver catálogo</a>
                </div>
                <div class="product-catalog-grid">
                    @foreach($section['items'] as $catalogProduct)
                        @php
                            $catalogImage = $catalogProduct->images->firstWhere('is_main', true) ?? $catalogProduct->images->first();
                        @endphp
                        <a class="product-catalog-card" href="{{ route('product.show', $catalogProduct) }}">
                            <span class="product-catalog-image">
                                <img src="{{ $catalogImage ? asset('storage/'.$catalogImage->image) : $fallbackImage }}" alt="{{ $catalogProduct->name }}" loading="lazy">
                            </span>
                            <span class="product-catalog-name">{{ $catalogProduct->name }}</span>
                            <span class="product-catalog-price">R$ {{ number_format((float) $catalogProduct->effective_price, 2, ',', '.') }}</span>
                            <span class="product-catalog-stock {{ $catalogProduct->qty > 0 ? 'is-available' : 'is-unavailable' }}">{{ $catalogProduct->qty > 0 ? 'Em estoque' : 'Sem estoque' }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    <section class="product-information" id="product-characteristics" aria-labelledby="characteristics-title">
        <h2 id="characteristics-title">Características do produto</h2>
        <dl class="product-characteristics">
            <div><dt>Produto</dt><dd>{{ $product->name }}</dd></div>
            <div><dt>Marca</dt><dd>{{ $product->brand?->name ?? 'Não informada' }}</dd></div>
            <div><dt>Categoria</dt><dd>{{ $product->category?->name ?? 'Não informada' }}</dd></div>
            @if($product->conditionLabel())<div><dt>Estado</dt><dd>{{ $product->conditionLabel() }}</dd></div>@endif
            <div><dt>Disponibilidade</dt><dd>{{ $product->qty > 0 ? 'Em estoque ('.$product->qty.' unidade(s))' : 'Sem estoque' }}</dd></div>
        </dl>
    </section>

    <section class="product-information product-description" id="product-description" aria-labelledby="description-title">
        <h2 id="description-title">Descrição</h2>
        <div class="product-description-copy">{{ $product->description ?: 'Não há descrição adicional cadastrada para este produto.' }}</div>
    </section>

    <section class="product-information product-community" id="product-questions" aria-labelledby="questions-title">
        <h2 id="questions-title">Perguntas</h2>
        <p class="product-empty-state">Ainda não há perguntas sobre este produto.</p>
    </section>

    <section class="product-information product-community" id="product-reviews" aria-labelledby="reviews-title">
        <h2 id="reviews-title">Opiniões do produto</h2>
        <p class="product-empty-state">Este produto ainda não recebeu avaliações.</p>
    </section>
</main>

@auth
    @include('components.cart-added-drawer', [
        'product' => $product,
        'suggestedProducts' => $relatedProducts->merge($brandProducts)->unique('id')->take(4),
    ])
@endauth

@include('components.product.share-modal', ['product' => $product])
@endsection

@push('styles')
    @vite('resources/css/cart-drawer.css')
@endpush