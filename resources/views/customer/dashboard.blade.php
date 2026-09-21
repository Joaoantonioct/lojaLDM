@extends('layouts.customer')

@section('title', 'Área do cliente | LDM')

@section('content')
<main class="client-page">
    <section class="client-hero" aria-label="Área do cliente">
        @include('components.catalog-carousel')

        <div class="client-shortcuts" aria-label="Atalhos do cliente">
            <a class="client-shortcut" href="#favoritos">
                <span class="client-shortcut-title">Favoritos</span>
                <span class="client-shortcut-icon">&#9825;</span>
                <span class="client-shortcut-value">{{ $favoritosCount ?? 0 }}</span>
            </a>

            <a class="client-shortcut" href="#interessados">
                <span class="client-shortcut-title">Também interessa</span>
                <span class="client-shortcut-icon">&#9734;</span>
                <span class="client-shortcut-value">{{ $dados->take(1)->count() }}</span>
            </a>

            <a class="client-shortcut" href="#carrinho">
                <span class="client-shortcut-title">Carrinho</span>
                <span class="client-shortcut-icon">&#128722;</span>
                <span class="client-shortcut-value">{{ $cartCount ?? 0 }} itens</span>
            </a>

            <a class="client-shortcut" href="#recentes">
                <span class="client-shortcut-title">Visto recentemente</span>
                <span class="client-shortcut-icon">&#8634;</span>
                <span class="client-shortcut-value">{{ $recentCount ?? 0 }}</span>
            </a>

            <a class="client-shortcut" href="{{ route('pesquisa') }}">
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
    </section>

    <section class="client-products" id="produtos">
        <div class="client-products-heading">
            <div>
                <span class="client-section-kicker">Catálogo</span>
                <h2>Produtos disponíveis</h2>
            </div>
            <a href="{{ route('pesquisa') }}#produtos">Ver todos</a>
        </div>

        @include('components.cart-itens', [
            'dados' => $dados,
            'pesquisa' => null,
            'categorias' => collect(),
            'marcas' => collect(),
        ])
    </section>
</main>
@endsection
