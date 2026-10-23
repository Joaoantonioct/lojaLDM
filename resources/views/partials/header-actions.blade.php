<div class="header-actions">
    <a
        class="header-promo"
        href="{{ auth()->check() ? route('cliente', ['search' => 'Roupas']).'#produtos' : route('pesquisa', ['search' => 'Roupas']).'#produtos' }}"
        aria-label="Ver produtos de roupas"
    >
        <img
            src="{{ asset('img/michele1.png') }}"
            alt="Ver produtos de roupas"
        >
    </a>

    <details class="mobile-menu">
        <summary aria-label="Abrir menu principal">
            <span class="mobile-menu-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </summary>
        <div class="mobile-menu-panel">
            <strong>Navegação</strong>
            <a href="{{ (auth()->check() ? route('cliente') : route('pesquisa')) . '#produtos' }}">Produtos</a>
            <strong>Categorias</strong>
            <a href="{{ route('pesquisa', ['search' => 'Roupas']) }}">Roupas</a>
            <a href="{{ route('pesquisa', ['search' => 'celular']) }}">Acessórios de celular</a>
            <a href="{{ route('pesquisa', ['search' => 'Papelaria']) }}">Papelaria</a>

            @guest
                <strong>Conta</strong>
                <a href="{{ route('register') }}">Crie a sua conta</a>
                <a href="{{ route('login') }}">Entre</a>
            @else
                <strong>Conta</strong>
                <a href="{{ route('cliente') }}">Minha conta</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sair</button>
                </form>
            @endguest
        </div>
    </details>

    <a href="{{ route('cart.index') }}" class="action-item action-cart mobile-cart" aria-label="Carrinho">
        <img src="{{ asset('img/carrinho.png') }}" alt="Carrinho">
        <span class="badge" data-cart-count>0</span>
    </a>
</div>