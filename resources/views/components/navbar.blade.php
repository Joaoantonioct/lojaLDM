<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="/" style="letter-spacing: 2px;">
    <img src="/img/ldm.png" alt="LDM Logo" width="40" height="40" class="me-2">
    LDM
</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="/produto">Coleções</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="/sobre">Sobre</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('login') }}" class="text-dark text-decoration-none">
                    <small>Entrar</small>
                </a>
                <span class="text-muted">|</span>
                <a href="{{ route('register') }}" class="text-dark text-decoration-none">
                    <small>Criar Conta</small>
                </a>
                <a href="{{ route('cart.index') }}" class="btn btn-dark btn-sm rounded-pill px-3">
                    Carrinho ({{ count(session()->get('cart', [])) }})
                </a>
            </div>
        </div>
    </div>
</nav>
</div>