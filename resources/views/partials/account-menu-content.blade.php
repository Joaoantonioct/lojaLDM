<div class="account-profile">
    <span class="account-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
    <span class="account-identity">
        <strong>{{ auth()->user()->name }}</strong>
        <small>{{ auth()->user()->email }}</small>
    </span>
</div>

<nav class="account-menu-links" aria-label="Opções da conta">
    <a href="{{ route('cliente') }}" class="account-menu-link">
        <span>Minha conta</span>
        <small>Seu perfil e catálogo</small>
    </a>
    <a href="{{ route('cliente') }}#favoritos" class="account-menu-link">
        <span>Meus favoritos</span>
        <small>Produtos que você salvou</small>
    </a>
    <a href="{{ route('cart.index') }}" class="account-menu-link">
        <span>Meu carrinho</span>
        <small>Revise os produtos selecionados</small>
    </a>
    <a href="{{ url('/contato') }}" class="account-menu-link">
        <span>Ajuda e atendimento</span>
        <small>Fale com a equipe LDM</small>
    </a>
</nav>

<div class="account-menu-notice">
    <strong>Compras e configurações</strong>
    <span>O histórico de pedidos e as configurações da conta ainda não estão disponíveis.</span>
</div>

<form method="POST" action="{{ route('logout') }}" class="account-logout-form">
    @csrf
    <button type="submit" class="account-menu-link">Sair da conta</button>
</form>
