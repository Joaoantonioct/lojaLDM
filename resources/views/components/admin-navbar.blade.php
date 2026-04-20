<div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}" style="letter-spacing: 2px;">
            <i class="bi bi-shield-lock"></i> LDM ADMIN
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="produtosDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-box"></i> Produtos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                            <i class="bi bi-list"></i> Listar Produtos
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.products.create') }}">
                            <i class="bi bi-plus-circle"></i> Novo Produto
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-tags"></i> Categorias
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-list"></i> Listar Categorias
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.categories.create') }}">
                            <i class="bi bi-plus-circle"></i> Nova Categoria
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="pedidosDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-cart-check"></i> Pedidos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                            <i class="bi bi-list"></i> Listar Pedidos
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.create') }}">
                            <i class="bi bi-plus-circle"></i> Novo Pedido
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="usuariosDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-people"></i> Usuários
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-list"></i> Listar Usuários
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                            <i class="bi bi-plus-circle"></i> Novo Usuário
                        </a></li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="text-white">
                    <small>Bem-vindo, Admin</small>
                </div>
                <a href="/" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-house"></i> Visitar Loja
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
</div>
