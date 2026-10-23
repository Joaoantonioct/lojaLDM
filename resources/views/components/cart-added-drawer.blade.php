@php
    $drawerImage = $product->images->firstWhere('is_main', true) ?? $product->images->first();
@endphp

<div class="cart-added-drawer" id="cart-added-drawer" role="dialog" aria-modal="true" aria-labelledby="cart-drawer-title" hidden>
    <button class="cart-drawer-backdrop" type="button" data-cart-drawer-close aria-label="Fechar confirmação"></button>

    <section class="cart-drawer-panel">
        <header class="cart-drawer-header">
            <div class="cart-drawer-confirmation">
                <span class="cart-drawer-check" aria-hidden="true">&#10003;</span>
                <div>
                    <h2 id="cart-drawer-title">Adicionado ao carrinho</h2>
                    <p class="cart-drawer-product-name">{{ $product->name }}</p>
                    <p class="cart-drawer-product-meta"><span data-drawer-quantity>1</span> unidade · <strong data-drawer-total>R$ {{ number_format((float) $product->price, 2, ',', '.') }}</strong></p>
                </div>
            </div>
            <button class="cart-drawer-close" type="button" data-cart-drawer-close aria-label="Fechar">&times;</button>
        </header>

        <div class="cart-drawer-content">
            <p class="cart-drawer-recommendation-title">Veja também estes produtos</p>
            @if($suggestedProducts->isNotEmpty())
                <div class="cart-drawer-products">
                    @foreach($suggestedProducts as $suggestion)
                        @php
                            $suggestionImage = $suggestion->images->firstWhere('is_main', true) ?? $suggestion->images->first();
                        @endphp
                        <a class="cart-drawer-product-card" href="{{ route('product.show', $suggestion) }}">
                            <span class="cart-drawer-product-image">
                                <img src="{{ $suggestionImage ? asset('storage/'.$suggestionImage->image) : asset('img/toddy.jpg') }}" alt="{{ $suggestion->name }}" loading="lazy">
                            </span>
                            <span class="cart-drawer-suggestion-name">{{ $suggestion->name }}</span>
                            <strong>R$ {{ number_format((float) $suggestion->price, 2, ',', '.') }}</strong>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="cart-drawer-empty-recommendations">Explore o catálogo para encontrar outros produtos.</p>
            @endif
        </div>

        <footer class="cart-drawer-footer">
            <a class="cart-drawer-continue" href="{{ route('home') }}">Continuar comprando</a>
            <a class="cart-drawer-view-cart" href="{{ route('cart.index') }}">Ir para o carrinho</a>
        </footer>
    </section>
</div>