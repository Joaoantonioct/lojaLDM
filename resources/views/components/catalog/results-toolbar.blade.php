<div class="results-toolbar">
    <div>
        <strong>{{ $dados->count() }}</strong>
        @if($dados->count() == 1)
            produto encontrado
        @else
            produtos encontrados
        @endif
    </div>

    <div class="sort-box">
        <label for="ordenacao">Ordenar:</label>
        <select id="ordenacao">
            <option value="relevancia">Mais relevantes</option>
            <option value="menor-preco">Menor preço</option>
            <option value="maior-preco">Maior preço</option>
        </select>
    </div>
</div>
