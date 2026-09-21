{{-- CONTEÚDO PRINCIPAL --}}
@php
    $emBusca = filled($pesquisa ?? null);
@endphp

<div class="search-layout {{ $emBusca ? 'has-filters' : 'without-filters' }}">


    {{-- FILTROS --}}
    @if($emBusca)
        @include('components.catalog.filters', [
            'pesquisa' => $pesquisa,
            'categorias' => $categorias,
            'marcas' => $marcas,
        ])
    @endif


    {{-- PRODUTOS --}}
    <main class="search-results" id="produtos" data-search="{{ $emBusca ? 'true' : 'false' }}">


        {{-- BARRA DE RESULTADOS --}}
        @include('components.catalog.results-toolbar', ['dados' => $dados])


        {{-- CASO NÃO ENCONTRE PRODUTOS --}}
        @if($dados->isEmpty())

            @include('components.catalog.empty-state', ['pesquisa' => $pesquisa])

        @else


            {{-- GRID --}}
            <div class="search-product-grid">

                @foreach($dados as $dado)

                    <article
                        class="search-product-card"
                        data-price="{{ $dado->price }}"
                    >

                        @php
                            $imagemPrincipal = $dado->images->firstWhere('is_main', true) ?? $dado->images->first();
                        @endphp

                        {{-- IMAGEM --}}
                        <div class="search-product-image">

                            <img
                                src="{{ $imagemPrincipal ? asset('storage/'.$imagemPrincipal->image) : asset('img/toddy.jpg') }}"
                                alt="{{ $dado->name }}"
                            >

                        </div>


                        {{-- INFORMAÇÕES --}}
                        <div class="search-product-info">


                            {{-- CATEGORIA --}}
                            @if($dado->category)

                                <span class="search-product-category">
                                    {{ $dado->category->name }}
                                </span>

                            @endif


                            {{-- NOME --}}
                            <h2 class="search-product-name">

                                {{ $dado->name }}

                            </h2>


                            {{-- MARCA --}}
                            @if($dado->brand)

                                <p class="search-product-brand">

                                    Marca:

                                    <strong>
                                        {{ $dado->brand->name }}
                                    </strong>

                                </p>

                            @endif


                            {{-- DESCRIÇÃO --}}
                            @if($dado->description)

                                <p class="search-product-description">

                                    {{ $dado->description }}

                                </p>

                            @endif


                            {{-- ESTOQUE --}}
                            <div class="search-product-stock">

                                @if($dado->qty > 0)

                                    <span class="stock-available">
                                        Em estoque
                                    </span>

                                    <span>
                                        {{ $dado->qty }} unidades
                                    </span>

                                @else

                                    <span class="stock-unavailable">
                                        Produto indisponível
                                    </span>

                                @endif

                            </div>


                            {{-- BOTÃO --}}
                            <a
                                href="{{ url('/product/' . $dado->id) }}"
                                class="search-product-button"
                            >
                                Ver produto
                                <span>→</span>
                            </a>


                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </main>

</div>