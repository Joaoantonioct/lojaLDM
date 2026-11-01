<form action="{{ route('pesquisa') }}" method="GET">
    <div class="search-box">

        <input
            type="text"
            name="search"
            id="catalog-search"
            value="{{ request('search') }}"
            placeholder="Buscar produtos, marcas e mais..."
        >

        <button type="submit" aria-label="Buscar produtos">
            <img src="{{ asset('img/lupa.png') }}" alt="">
        </button>

    </div>
</form>