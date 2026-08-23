<div class="container_prod">
  <div style="width: 100%;">  
    <h2 class="section-title">Novidade</h2>
    <div class="carousel-wrapper">
      
      @if(isset($produtosNovidade) && $produtosNovidade->count() > 0)
        @foreach($produtosNovidade as $produto)
          <div class="card product-card">
            <div class="product-image">
              <img src="{{ $produto->imagem ?? '/img/placeholder-produto.png' }}" alt="{{ $produto->nome }}">
            </div>
            <div class="product-info">
              <a href="{{ route('produto.detalhe', $produto->slug) }}" class="product-name">{{ $produto->nome }}</a>
              <div class="price-container">
                @if($produto->preco_antigo && $produto->preco_antigo > $produto->preco_atual)
                  <span class="price-old">{{ $produto->preco_antigo_formatado }}</span>
                @endif
                <div class="price-current">
                  <span class="price-value">{{ $produto->preco_atual_formatado }}</span>
                  @if($produto->desconto || ($produto->preco_antigo && $produto->preco_antigo > $produto->preco_atual))
                    <span class="price-discount">{{ $produto->desconto_formatado }}</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <!-- Produtos de exemplo quando não há dados no banco -->
        <div class="card product-card">
          <div class="product-image">
            <img src="/img/vestido vermelho.png" alt="Produto">
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Kit 2 Blusas Feminina Básica</a>
            <div class="price-container">
              <span class="price-old">R$ 74,90</span>
              <div class="price-current">
                <span class="price-value">R$ 47,21</span>
                <span class="price-discount">36% OFF</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/camisa social.png" alt="Produto">
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Kit 2 Blusas Feminina Básica</a>
            <div class="price-container">
              <span class="price-old">R$ 74,90</span>
              <div class="price-current">
                <span class="price-value">R$ 47,21</span>
                <span class="price-discount">36% OFF</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/heatset.png" alt="Produto">
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Kit 2 Blusas Feminina Básica</a>
            <div class="price-container">
              <span class="price-old">R$ 74,90</span>
              <div class="price-current">
                <span class="price-value">R$ 47,21</span>
                <span class="price-discount">36% OFF</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/camisa primavera.png" alt="Produto">
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Kit 2 Blusas Feminina Básica</a>
            <div class="price-container">
              <span class="price-old">R$ 74,90</span>
              <div class="price-current">
                <span class="price-value">R$ 47,21</span>
                <span class="price-discount">36% OFF</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/capinha celular.png" alt="Produto">
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Kit 2 Blusas Feminina Básica</a>
            <div class="price-container">
              <span class="price-old">R$ 74,90</span>
              <div class="price-current">
                <span class="price-value">R$ 47,21</span>
                <span class="price-discount">36% OFF</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/kit celular.png" alt="Produto">
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Kit 2 Blusas Feminina Básica</a>
            <div class="price-container">
              <span class="price-old">R$ 74,90</span>
              <div class="price-current">
                <span class="price-value">R$ 47,21</span>
                <span class="price-discount">36% OFF</span>
              </div>
            </div>
          </div>
        </div>
      @endif

    </div>
  </div>
</div>
