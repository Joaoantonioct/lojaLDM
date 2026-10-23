@extends('layouts.public')

@section('title', 'Carrinho | LDM')

@push('styles')
    @vite('resources/css/cart.css')
@endpush

@section('content')
<main class="cart-page" data-cart-page data-client-id="{{ auth()->id() ?? 'guest' }}">
    <nav class="cart-breadcrumb" aria-label="Navegação estrutural">
        <a href="{{ route('home') }}">Início</a>
        <span aria-hidden="true">/</span>
        <strong>Carrinho</strong>
    </nav>

    <header class="cart-heading">
        <div>
            <p class="cart-eyebrow">Sua seleção</p>
            <h1>Carrinho de compras</h1>
            <p>Revise os produtos antes de continuar.</p>
        </div>
        <span class="cart-heading-count" data-cart-item-count>0 itens</span>
    </header>

    <div class="cart-layout">
        <section class="cart-products" aria-label="Produtos no carrinho">
            <div class="cart-product-list" data-cart-items></div>
            <div class="cart-empty" data-cart-empty hidden>
                <span class="cart-empty-icon" aria-hidden="true">&#128722;</span>
                <h2>Seu carrinho está vazio</h2>
                <p>Explore os produtos da LDM e adicione os que deseja comprar.</p>
                <a class="cart-primary-link" href="{{ route('home') }}">Explorar produtos</a>
            </div>
            <div class="cart-list-footer" data-cart-footer hidden>
                <button type="button" data-cart-clear>Limpar carrinho</button>
                <a href="{{ route('home') }}">Continuar comprando</a>
            </div>
        </section>

        <aside class="cart-summary" aria-labelledby="cart-summary-title">
            <h2 id="cart-summary-title">Resumo do pedido</h2>
            <div class="cart-summary-line">
                <span>Produtos (<span data-cart-summary-count>0</span>)</span>
                <strong data-cart-subtotal>R$ 0,00</strong>
            </div>
            <div class="cart-summary-line">
                <span>Frete</span>
                <span class="cart-shipping-note">Calculado na finalização</span>
            </div>
            <div class="cart-summary-total">
                <span>Subtotal</span>
                <strong data-cart-total>R$ 0,00</strong>
            </div>
            <button class="cart-checkout-button" type="button" disabled>
                Finalização indisponível
            </button>
            <p class="cart-checkout-note">A finalização de pedidos ainda não está disponível nesta loja.</p>
        </aside>
    </div>
</main>
@endsection

@push('scripts')
    @vite('resources/js/cart.js')
@endpush