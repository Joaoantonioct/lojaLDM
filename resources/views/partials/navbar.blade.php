<nav class="nav-bar">

    <div class="nav-container">

        <ul class="nav-links">

            <li class="nav-category">
                <a href="{{ route('pesquisa') }}" class="highlight nav-category-toggle" aria-haspopup="true">
                    Categorias
                    <span class="nav-category-arrow" aria-hidden="true">▾</span>
                </a>

                <ul class="category-menu">
                    <li><a href="{{ route('pesquisa', ['search' => 'Roupas']) }}">Roupas</a></li>
                    <li><a href="{{ route('pesquisa', ['search' => 'Papelaria']) }}">Papelaria</a></li>
                    <li><a href="{{ route('pesquisa', ['search' => 'Acessórios']) }}">Acessórios</a></li>
                    <li><a href="{{ route('pesquisa', ['search' => 'celular']) }}">Celulares</a></li>
                </ul>
            </li>

            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ (auth()->check() ? route('cliente') : route('pesquisa')) . '#produtos' }}">Todos os Produtos</a></li>
            <li><a href="{{ route('pesquisa', ['search' => 'Papelaria']) }}">Papelaria</a></li>
            <li><a href="{{ route('pesquisa', ['search' => 'Roupas']) }}">Roupas</a></li>
            <li><a href="{{ route('pesquisa', ['search' => 'Acessórios']) }}">Acessórios</a></li>
            <li><a href="{{ route('pesquisa', ['search' => 'celular']) }}">Celulares</a></li>

            {{-- 
            <li>
                <a href="/pagina/detallsProducts">
                    Detalhes do produto
                </a>
            </li>
            --}}

        </ul>

        <div class="action-links nav-actions">
            @guest
                <a href="{{ route('register') }}" class="action-item">
                    <span>Crie a sua conta</span>
                </a>

                <a href="{{ route('login') }}" class="action-item">
                    <span>Entre</span>
                </a>
            @else
                <details class="account-dropdown">
                    <summary class="action-item account-toggle">
                        <span>Minha conta</span>
                        <span class="account-chevron" aria-hidden="true">▾</span>
                    </summary>
                    <div class="account-menu">
                        <div class="account-profile">
                            <span class="account-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="account-identity">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ auth()->user()->email }}</small>
                            </span>
                        </div>
                        <a href="{{ route('cliente') }}" class="account-menu-link">Minha conta</a>
                        <a href="{{ route('cliente') }}#produtos" class="account-menu-link">Todos os produtos</a>
                        <form method="POST" action="{{ route('logout') }}" class="account-logout-form">
                            @csrf
                            <button type="submit" class="account-menu-link">Sair</button>
                        </form>
                    </div>
                </details>
            @endguest

            <a href="#" class="action-item">
                <span>Compras</span>
            </a>

            <a href="{{ route('cart.index') }}" class="action-item action-cart">
                <img src="{{ asset('img/carrinho.png') }}" alt="Carrinho">
                <span class="badge" data-cart-count>0</span>
            </a>

        </div>

    </div>

</nav>