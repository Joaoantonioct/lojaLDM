<section class="catalog-carousel">

    <div class="carousel-track">

        <!-- BANNER 1 -->
        <article class="carousel-slide active">

            <div class="carousel-content">

                <span class="eyebrow">Catálogo LDM</span>

                <h1>
                    Encontre o que você procura
                </h1>

                <p>
                    Produtos selecionados para facilitar o seu dia a dia.
                </p>

                <a href="{{ route('pesquisa', ['search' => 'Papelaria']) }}" class="primary-button">
                    Ver papelaria
                </a>

            </div>

            <div class="carousel-image">
                <img src="/img/bannerpapelaria.png" alt="Ofertas da semana">
            </div>

        </article>


        <!-- BANNER 2 -->
        <article class="carousel-slide">

            <div class="carousel-content">

                <span class="eyebrow">Estilo para você</span>

                <h1>
                    Novidades em roupas
                </h1>

                <p>
                    Encontre peças para completar o seu estilo.
                </p>

                <a href="{{ route('pesquisa', ['search' => 'Roupas']) }}" class="primary-button">
                    Ver roupas
                </a>

            </div>

            <div class="carousel-image">
                <img src="/img/roupascarrossel.png" alt="Produtos em promoção">
            </div>

        </article>


        <!-- BANNER 3 -->
        <article class="carousel-slide">

            <div class="carousel-content">

                <span class="eyebrow">Acessórios LDM</span>

                <h1>
                    Acessórios para o seu dia
                </h1>

                <p>
                    Descubra opções práticas para acompanhar você.
                </p>

                <a href="{{ route('pesquisa', ['search' => 'Acessórios']) }}" class="primary-button">
                    Ver acessórios
                </a>

            </div>

            <div class="carousel-image">
                <img src="/img/acessorioscarrossel.png" alt="Novidades do catálogo">
            </div>

        </article>

    </div>


    <!-- SETA ANTERIOR -->
    <button class="carousel-button carousel-prev" type="button">
        &#10094;
    </button>

    <!-- SETA PRÓXIMA -->
    <button class="carousel-button carousel-next" type="button">
        &#10095;
    </button>


    <!-- INDICADORES -->
    <div class="carousel-dots">

        <button class="carousel-dot active" type="button"></button>

        <button class="carousel-dot" type="button"></button>

        <button class="carousel-dot" type="button"></button>

    </div>

</section>
