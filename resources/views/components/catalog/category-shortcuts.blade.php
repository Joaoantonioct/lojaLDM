<nav class="category-shortcuts" id="categories" aria-label="Explorar categorias">
    <a class="category-shortcut" href="{{ route('pesquisa', ['search' => 'Papelaria']) }}">
        <span class="category-shortcut-icon" aria-hidden="true">📚</span>
        <span class="category-shortcut-label">Papelaria</span>
    </a>
    <a class="category-shortcut" href="{{ route('pesquisa', ['search' => 'Roupas']) }}">
        <span class="category-shortcut-icon" aria-hidden="true">👕</span>
        <span class="category-shortcut-label">Roupas</span>
    </a>
    <a class="category-shortcut" href="{{ route('pesquisa', ['search' => 'Acessórios']) }}">
        <span class="category-shortcut-icon" aria-hidden="true">🎧</span>
        <span class="category-shortcut-label">Acessórios</span>
    </a>
    <a class="category-shortcut" href="{{ route('pesquisa', ['search' => 'celular']) }}">
        <span class="category-shortcut-icon" aria-hidden="true">📱</span>
        <span class="category-shortcut-label">Celulares</span>
    </a>
    <a class="category-shortcut" href="{{ route('pesquisa') }}">
        <span class="category-shortcut-icon" aria-hidden="true">＋</span>
        <span class="category-shortcut-label">Ver mais</span>
    </a>
</nav>
