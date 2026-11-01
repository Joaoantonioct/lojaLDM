<aside class="search-sidebar" data-search-filters>
    <details class="search-filter-disclosure">
        <summary class="search-filter-summary">
            <span><i class="fa-solid fa-sliders" aria-hidden="true"></i> Filtros</span>
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
        </summary>
        <div class="search-filter-content">
            <form action="{{ route('pesquisa') }}" method="GET">
                <input type="hidden" name="search" value="{{ $pesquisa }}">

                <div class="filter-section filter-section-heading">
                    <h3>Refinar resultados</h3>
                    <a href="{{ route('pesquisa', ['search' => $pesquisa]) }}" class="clear-filters">Limpar filtros</a>
                </div>

                <div class="filter-section">
                    <h4>Categoria</h4>
                    <div class="filter-list">
                        @foreach($categorias as $categoria)
                            <label class="filter-option">
                                <input type="checkbox" name="categories[]" value="{{ $categoria->id }}" @checked(in_array($categoria->id, (array) request('categories', [])))>
                                <span>{{ $categoria->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Marca</h4>
                    <div class="filter-list">
                        @foreach($marcas as $marca)
                            <label class="filter-option">
                                <input type="checkbox" name="brands[]" value="{{ $marca->id }}" @checked(in_array($marca->id, (array) request('brands', [])))>
                                <span>{{ $marca->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Estado</h4>
                    <div class="filter-list">
                        @foreach(['novo' => 'Novos', 'seminovo' => 'Seminovos', 'brecho' => 'Brechó'] as $condicao => $rotulo)
                            <label class="filter-option">
                                <input type="checkbox" name="conditions[]" value="{{ $condicao }}" @checked(in_array($condicao, (array) request('conditions', []), true))>
                                <span>{{ $rotulo }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Preço</h4>
                    <div class="price-filter">
                        <input type="number" name="min_price" placeholder="Mínimo" min="0" step="0.01" value="{{ request('min_price') }}">
                        <span>até</span>
                        <input type="number" name="max_price" placeholder="Máximo" min="0" step="0.01" value="{{ request('max_price') }}">
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Disponibilidade</h4>
                    <label class="filter-option">
                        <input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock'))>
                        <span>Em estoque</span>
                    </label>
                </div>

                <div class="filter-section">
                    <button type="submit" class="search-product-button">Aplicar filtros</button>
                </div>
            </form>
        </div>
    </details>
</aside>
