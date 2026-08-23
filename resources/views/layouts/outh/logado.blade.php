<li class="dropdown">
    <a href="#" class="dropdown-toggle">Minha Conta</a>
    <ul class="dropdown-menu">
        <li><a href="{{ route('compras') }}">Compras</a></li>
        <li><a href="{{ route('historico') }}">Histórico</a></li>
        <li><a href="{{ route('perguntas') }}">Perguntas</a></li>
        <li><a href="{{ route('opinioes') }}">Opiniões</a></li>
        <li><a href="{{ route('logout') }}">Sair</a></li>
    </ul>
</li>
<li><a href="{{ route('compras') }}">Compras</a></li>
<li><a href="{{ route('favoritos') }}">Favoritos</a></li>
<li><a href="{{ route('notificacoes') }}">
    <img src="/img/notificacao.png" width="17" alt="Notificações">
</a></li>

<style>
    li {
        list-style: none;
    }
</style>
