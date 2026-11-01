<nav class="nav-bar">

    <div class="nav-container">

        <ul class="nav-links">

            <li class="nav-category">
                <a href="{{ route('pesquisa') }}" class="highlight nav-category-toggle" aria-haspopup="true">
                    Categorias
                    <span class="nav-category-arrow" aria-hidden="true">▾</span>
                </a>

                <ul class="category-menu">
                    <li class="category-menu-parent">
                        <a href="{{ route('pesquisa', ['search' => 'Roupas']) }}">Roupas</a>
                        <ul class="category-submenu">
                            <li><a href="{{ route('pesquisa', ['search' => 'Roupas', 'conditions' => ['novo']]) }}">Novos</a></li>
                            <li><a href="{{ route('pesquisa', ['search' => 'Roupas', 'conditions' => ['seminovo']]) }}">Seminovos</a></li>
                            <li><a href="{{ route('pesquisa', ['search' => 'Roupas', 'conditions' => ['brecho']]) }}">Brechó</a></li>
                        </ul>
                    </li>
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
                        @include('partials.account-menu-content')
                    </div>
                </details>
            @endguest

            <a href="{{ url('/contato') }}" class="action-item">
                <span>Ajuda</span>
            </a>

            <a href="{{ route('cart.index') }}" class="action-item action-cart">
                <img src="{{ asset('img/carrinho.png') }}" alt="Carrinho">
                <span class="badge" data-cart-count>0</span>
            </a>

        </div>

    </div>

</nav>