@extends('layouts.customer')

@section('title', 'Área do cliente | LDM')

@section('content')
<main class="client-page" data-client-id="{{ auth()->id() }}" data-active-product-ids="{{ collect($activeProductIds ?? [])->implode(',') }}">
    <section class="client-hero" aria-label="Área do cliente">
            @include('components.catalog-carousel')
            @include('components.catalog.category-shortcuts')

        <div class="client-shortcuts-viewport">
            <button class="client-shortcuts-button client-shortcuts-prev" type="button" aria-label="Atalhos anteriores">
                &#10094;
            </button>

            <div class="client-shortcuts" aria-label="Atalhos do cliente">
                <a class="client-shortcut" href="#favoritos" data-shortcut="favorites">
                <span class="client-shortcut-title">Favoritos</span>
                <span class="client-shortcut-icon" data-shortcut-preview="favorites" aria-hidden="true">&#9825;</span>
                <span class="client-shortcut-note" data-shortcut-note="favorites">Nenhum favorito salvo</span>
                <span class="client-shortcut-value" data-shortcut-value="favorites">0 produtos</span>
                </a>

                <a class="client-shortcut" href="{{ route('cliente') }}" data-recommendations-url="{{ route('cliente') }}" data-shortcut="searches">
                <span class="client-shortcut-title">Também interessa</span>
                <span class="client-shortcut-icon" data-shortcut-preview="searches" aria-hidden="true">&#9734;</span>
                <span class="client-shortcut-note" data-shortcut-note="searches"></span>
                <span class="client-shortcut-value" data-shortcut-value="searches">0 buscas</span>
                </a>

                <a class="client-shortcut" href="#carrinho" data-shortcut="cart">
                <span class="client-shortcut-title">Carrinho</span>
                <span class="client-shortcut-icon" data-shortcut-preview="cart" aria-hidden="true">&#128722;</span>
                <span class="client-shortcut-note" data-shortcut-note="cart"></span>
                <span class="client-shortcut-value" data-shortcut-value="cart">0 itens</span>
                </a>

                <a class="client-shortcut" href="#recentes" data-shortcut="recent">
                <span class="client-shortcut-title">Visto recentemente</span>
                <span class="client-shortcut-icon" data-shortcut-preview="recent" aria-hidden="true">&#8634;</span>
                <span class="client-shortcut-note" data-shortcut-note="recent"></span>
                <span class="client-shortcut-value" data-shortcut-value="recent">0 produtos</span>
                </a>

                <a class="client-shortcut" href="{{ route('cliente') }}#site-header">
                <span class="client-shortcut-title">Sua busca</span>
                <span class="client-shortcut-icon">&#128269;</span>
                <span class="client-shortcut-value">Pesquisar</span>
                </a>

                <a class="client-shortcut" href="#compras">
                <span class="client-shortcut-title">Compras</span>
                <span class="client-shortcut-icon">&#128230;</span>
                <span class="client-shortcut-value">{{ $ordersCount ?? 0 }} pedidos</span>
                </a>
            </div>

            <button class="client-shortcuts-button client-shortcuts-next" type="button" aria-label="Próximos atalhos">
                &#10095;
            </button>
        </div>
    </section>

    <section class="client-favorites" id="favoritos" data-favorites-section hidden aria-labelledby="favorites-title">
        <div class="client-products-heading">
            <div>
                <span class="client-section-kicker">Sua lista</span>
                <h2 id="favorites-title">Produtos favoritos</h2>
            </div>
        </div>
        <div class="client-favorites-carousel">
            <button
                class="client-favorites-button client-favorites-prev"
                type="button"
                aria-label="Ver favoritos anteriores"
                aria-controls="client-favorites-list"
                hidden
            >&#10094;</button>
            <div
                class="client-favorites-grid"
                id="client-favorites-list"
                data-favorites-list
                tabindex="0"
                aria-label="Produtos favoritos"
            ></div>
            <button
                class="client-favorites-button client-favorites-next"
                type="button"
                aria-label="Ver próximos favoritos"
                aria-controls="client-favorites-list"
                hidden
            >&#10095;</button>
        </div>
    </section>

    <section class="client-products">
        <div class="client-products-heading">
            <div>
                <span class="client-section-kicker">Catálogo</span>
                <h2>{{ !empty($recomendacoes) ? 'Produtos que podem interessar' : 'Produtos disponíveis' }}</h2>
            </div>
            <a href="{{ route('cliente') }}#produtos">Ver todos</a>
        </div>

        @include('components.cart-itens', [
            'dados' => $dados,
            'pesquisa' => $pesquisa ?? null,
            'categorias' => $categorias ?? collect(),
            'marcas' => $marcas ?? collect(),
        ])
    </section>
</main>
@endsection
