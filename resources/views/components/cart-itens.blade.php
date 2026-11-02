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
                        data-product-id="{{ $dado->id }}"
                        data-price="{{ $dado->effective_price }}"
                    >

                        @php
                            $imagemPrincipal = $dado->images->firstWhere('is_main', true) ?? $dado->images->first();
                        @endphp

                        <a href="{{ route('product.show', $dado) }}" class="search-product-link">
                            <div class="search-product-image">
                                <img
                                    src="{{ $imagemPrincipal ? asset('storage/'.$imagemPrincipal->image) : asset('img/toddy.jpg') }}"
                                    alt="{{ $dado->name }}"
                                    loading="lazy"
                                >
                            </div>
                            <div class="search-product-info">
                                <h2 class="search-product-name">{{ $dado->name }}</h2>
                                @if($dado->currentPromotion)
                                    <small class="line-through text-gray-500">R$ {{ number_format((float) $dado->price, 2, ',', '.') }}</small>
                                @endif
                                <span class="search-product-price">R$ {{ number_format((float) $dado->effective_price, 2, ',', '.') }}</span>
                                @if($dado->currentPromotion)
                                    <small class="font-semibold text-red-700">{{ number_format($dado->currentPromotion->discount_basis_points / 100, 2, ',', '.') }}% OFF</small>
                                @endif
                                @if($dado->conditionLabel())
                                    <span class="search-product-condition">Estado: {{ $dado->conditionLabel() }}</span>
                                @endif
                                <span class="search-product-stock {{ $dado->qty > 0 ? 'is-available' : 'is-unavailable' }}">
                                    {{ $dado->qty > 0 ? 'Em estoque' : 'Sem estoque' }}
                                </span>
                            </div>
                        </a>

                    </article>

                @endforeach

            </div>

        @endif

    </main>

</div>