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
            <a class="mobile-menu-subitem" href="{{ route('pesquisa', ['search' => 'Roupas', 'conditions' => ['novo']]) }}">Novos</a>
            <a class="mobile-menu-subitem" href="{{ route('pesquisa', ['search' => 'Roupas', 'conditions' => ['seminovo']]) }}">Seminovos</a>
            <a class="mobile-menu-subitem" href="{{ route('pesquisa', ['search' => 'Roupas', 'conditions' => ['brecho']]) }}">Brechó</a>
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

    <details class="mobile-account-dropdown account-dropdown">
        <summary
            class="action-item mobile-account"
            aria-label="{{ auth()->check() ? 'Abrir menu da conta' : 'Abrir opções de acesso' }}"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="8" r="4"></circle>
                <path d="M4.5 21a7.5 7.5 0 0 1 15 0"></path>
            </svg>
            <span>{{ auth()->check() ? 'Conta' : 'Entrar' }}</span>
        </summary>
        <div class="account-menu mobile-account-menu">
            @auth
                @include('partials.account-menu-content')
            @else
                <div class="account-menu-guest">
                    <strong>Acesse sua conta</strong>
                    <span>Entre ou crie uma conta para salvar seus favoritos.</span>
                </div>
                <a href="{{ route('login') }}" class="account-menu-link">Entrar</a>
                <a href="{{ route('register') }}" class="account-menu-link">Criar conta</a>
            @endauth
        </div>
    </details>

    <a href="{{ route('cart.index') }}" class="action-item action-cart mobile-cart" aria-label="Carrinho">
        <img src="{{ asset('img/carrinho.png') }}" alt="Carrinho">
        <span class="badge" data-cart-count>0</span>
    </a>
</div>