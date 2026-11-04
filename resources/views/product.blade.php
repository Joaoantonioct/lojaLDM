<section class="empty-state">

            <div class="empty-icon">
                📦
            </div>

            <h2>
                Nenhum produto encontrado
            </h2>

            <p>
                Não encontramos produtos para esta busca.
            </p>

        </section>

    @else

        <section
            class="product-grid"
            aria-label="Lista de produtos"
        >

            @foreach($dados as $dado)

                <article class="produto-card">

                    @php
                        $imagemPrincipal = $dado->images->firstWhere('is_main', true) ?? $dado->images->first();
                    @endphp


                    {{-- =========================
                         CAIXA DA IMAGEM
                    ========================== --}}

                    <div class="produto-image-box">

                        <div class="produto-image">

                            <img
                                src="{{ $imagemPrincipal ? asset('storage/'.$imagemPrincipal->image) : asset('img/toddy.jpg') }}"
                                alt="{{ $dado->name }}"
                            >

                        </div>

                    </div>


                    {{-- =========================
                         INFORMAÇÕES DO PRODUTO
                    ========================== --}}

                    <div class="produto-content">

                        {{-- Categoria --}}
                        <span class="produto-category">
                            {{ $dado->category?->name }}
                        </span>


                        {{-- Nome --}}
                        <h2 class="produto-name">
                            {{ $dado->name }}
                        </h2>


                        {{-- Descrição --}}
                        <p class="produto-description">
                            {{ $dado->description }}
                        </p>


                        {{-- Informações --}}
                        <div class="produto-info">

                            <div class="produto-info-item">

                                <span class="info-label">
                                    Estoque
                                </span>

                                <strong>
                                    {{ $dado->qty }}
                                </strong>

                            </div>


                            <div class="produto-info-item">

                                <span class="info-label">
                                    Marca
                                </span>

                                <strong>
                                    {{ $dado->brand?->name }}
                                </strong>

                            </div>

                        </div>


                        {{-- Botão --}}
                        <a
                            class="produto-link"
                            href="{{ url('/product/' . $dado->id) }}#product-content"
                        >
                            Ver produto
                            <span>→</span>
                        </a>

                    </div>

                </article>

            @endforeach

        </section>