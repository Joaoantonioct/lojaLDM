<nav class="nav-bar">

    <div class="nav-container">

        <ul class="nav-links">

            <li class="nav-category">
                <a href="{{ route('pesquisa') }}" class="highlight nav-category-toggle" aria-haspopup="true">
                    Categorias
                    <span class="nav-category-arrow" aria-hidden="true"></span>
                </a>

                <ul class="category-menu">
                    <li><a href="{{ route('pesquisa', ['search' => 'Roupas']) }}">Roupas</a></li>
                    <li><a href="{{ route('pesquisa', ['search' => 'Acessórios de celular']) }}">Acessórios de celular</a></li>
                    <li><a href="{{ route('pesquisa', ['search' => 'Papelaria']) }}">Papelaria</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ route('pesquisa') }}#produtos">
                    Produto
                </a>
            </li>

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
                <a href="{{ route('cliente') }}" class="action-item">
                    <span>Minha conta</span>
                </a>
            @endguest

            <a href="#" class="action-item">
                <span>Compras</span>
            </a>

            <a href="" class="action-item action-cart">
                <img src="{{ asset('img/carrinho.png') }}" alt="Carrinho">
                <span class="badge">3</span>
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="action-item action-logout">Sair</button>
                </form>
            @endauth
        </div>

    </div>

</nav>