<nav class="mobile-bottom-nav" aria-label="Navegação principal">
    <a class="mobile-bottom-link" href="{{ auth()->check() ? route('cliente') : route('home') }}">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"></path>
        </svg>
        <span>Início</span>
    </a>
    <a class="mobile-bottom-link" href="{{ (auth()->check() ? route('cliente') : route('home')) . '#categories' }}">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
            <rect x="14" y="14" width="7" height="7" rx="1"></rect>
        </svg>
        <span>Categorias</span>
    </a>
    <a class="mobile-bottom-link" href="{{ route('pesquisa') }}">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="10.8" cy="10.8" r="6.8"></circle>
            <path d="m16 16 5 5"></path>
        </svg>
        <span>Pesquisar</span>
    </a>
    <a
        class="mobile-bottom-link"
        href="{{ auth()->check() ? route('cliente') . '#favoritos' : route('login') }}"
    >
        @if(auth()->check())
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20.8 8.8c0 5.1-8.8 11-8.8 11s-8.8-5.9-8.8-11a4.7 4.7 0 0 1 8.8-2.3 4.7 4.7 0 0 1 8.8 2.3z"></path>
            </svg>
        @else
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="8" r="4"></circle>
                <path d="M4.5 21a7.5 7.5 0 0 1 15 0"></path>
            </svg>
        @endif
        <span>{{ auth()->check() ? 'Favoritos' : 'Conta' }}</span>
    </a>
    <a class="mobile-bottom-link" href="{{ route('cart.index') }}">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h8.5a2 2 0 0 0 1.9-1.4L22 8H6"></path>
            <circle cx="10" cy="21" r="1"></circle>
            <circle cx="18" cy="21" r="1"></circle>
        </svg>
        <span class="mobile-bottom-cart-count badge" data-cart-count>0</span>
        <span>Carrinho</span>
    </a>
</nav>
