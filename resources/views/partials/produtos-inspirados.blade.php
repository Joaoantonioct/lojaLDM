<!-- Seção de Produtos Inspirado no Último Visto -->
<section class="produtos-inspirados-section">
  <h2 class="section-title">Ultimos adcionados</h2>
  
  <div class="container_prod">
    
    
    <div class="carousel-wrapper" id="produtos-carousel">
      @if(isset($produtosInspirados) && count($produtosInspirados) > 0)
        @foreach($produtosInspirados as $produto)
          <div class="card product-card">
            <div class="product-image">
              <img src="{{ $produto->imagem ?? '/img/placeholder.png' }}" alt="{{ $produto->nome }}">
              @if($produto->desconto > 0)
                <span class="discount-badge">{{ $produto->desconto }}% OFF</span>
              @endif
            </div>
            <div class="product-info">
              <a href="{{ route('produto.show', $produto->id) }}" class="product-name">
                {{ Str::limit($produto->nome, 50) }}
              </a>
              <div class="price-container">
                @if($produto->preco_original > $produto->preco_atual)
                  <span class="price-old">R$ {{ number_format($produto->preco_original, 2, ',', '.') }}</span>
                @endif
                <div class="price-current">
                  <span class="price-value">R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}</span>
                  @if($produto->desconto > 0)
                    <span class="price-discount">{{ $produto->desconto }}% OFF</span>
                  @endif
                </div>
              </div>
              <button class="add-to-cart-btn" onclick="addToCart({{ $produto->id }})">
                <i class="bi bi-cart-plus"></i> Adicionar
              </button>
            </div>
          </div>
        @endforeach
      @else
        <!-- Produtos de exemplo quando não há dados do banco -->
        <div class="card product-card">
          <div class="product-image">
            <img src="/img/vestido vermelho.png" alt="Produto">
            <span class="discount-badge">36% OFF</span>
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Vestido Vermelho Elegante</a>
            <div class="price-container">
              <span class="price-old">R$ 189,90</span>
              <div class="price-current">
                <span class="price-value">R$ 121,54</span>
                <span class="price-discount">36% OFF</span>
              </div>
            </div>
            <button class="add-to-cart-btn" onclick="addToCart(1)">
              <i class="bi bi-cart-plus"></i> Adicionar
            </button>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/camisa social.png" alt="Produto">
            <span class="discount-badge">25% OFF</span>
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Camisa Social Masculina</a>
            <div class="price-container">
              <span class="price-old">R$ 149,90</span>
              <div class="price-current">
                <span class="price-value">R$ 112,43</span>
                <span class="price-discount">25% OFF</span>
              </div>
            </div>
            <button class="add-to-cart-btn" onclick="addToCart(2)">
              <i class="bi bi-cart-plus"></i> Adicionar
            </button>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/heatset.png" alt="Produto">
            <span class="discount-badge">30% OFF</span>
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Headset Bluetooth Premium</a>
            <div class="price-container">
              <span class="price-old">R$ 299,90</span>
              <div class="price-current">
                <span class="price-value">R$ 209,93</span>
                <span class="price-discount">30% OFF</span>
              </div>
            </div>
            <button class="add-to-cart-btn" onclick="addToCart(3)">
              <i class="bi bi-cart-plus"></i> Adicionar
            </button>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/camisa primavera.png" alt="Produto">
            <span class="discount-badge">20% OFF</span>
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Camisa Primavera Feminina</a>
            <div class="price-container">
              <span class="price-old">R$ 89,90</span>
              <div class="price-current">
                <span class="price-value">R$ 71,92</span>
                <span class="price-discount">20% OFF</span>
              </div>
            </div>
            <button class="add-to-cart-btn" onclick="addToCart(4)">
              <i class="bi bi-cart-plus"></i> Adicionar
            </button>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/capinha celular.png" alt="Produto">
            <span class="discount-badge">15% OFF</span>
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Capinha Premium Smartphone</a>
            <div class="price-container">
              <span class="price-old">R$ 49,90</span>
              <div class="price-current">
                <span class="price-value">R$ 42,42</span>
                <span class="price-discount">15% OFF</span>
              </div>
            </div>
            <button class="add-to-cart-btn" onclick="addToCart(5)">
              <i class="bi bi-cart-plus"></i> Adicionar
            </button>
          </div>
        </div>

        <div class="card product-card">
          <div class="product-image">
            <img src="/img/kit celular.png" alt="Produto">
            <span class="discount-badge">40% OFF</span>
          </div>
          <div class="product-info">
            <a href="#" class="product-name">Kit Acessórios Celular</a>
            <div class="price-container">
              <span class="price-old">R$ 199,90</span>
              <div class="price-current">
                <span class="price-value">R$ 119,94</span>
                <span class="price-discount">40% OFF</span>
              </div>
            </div>
            <button class="add-to-cart-btn" onclick="addToCart(6)">
              <i class="bi bi-cart-plus"></i> Adicionar
            </button>
          </div>
        </div>
      @endif
    </div>
  </div>
</section>

<script>
// Função para rolar o carrossel de produtos
function scrollProdutos(direction) {
  const carousel = document.getElementById('produtos-carousel');
  const scrollAmount = 220; // Largura do card + gap
  
  if (direction === 'left') {
    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  } else {
    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  }
}

// Função para adicionar ao carrinho
function addToCart(produtoId) {
  // Simulação de adição ao carrinho
  const button = event.target;
  const originalText = button.innerHTML;
  
  button.innerHTML = '<i class="bi bi-check-circle"></i> Adicionado!';
  button.style.background = '#10b981';
  button.disabled = true;
  
  // Log para debugging
  console.log('Produto adicionado ao carrinho:', produtoId);
  
  // Simular requisição AJAX para adicionar ao carrinho
  // fetch(`/cart/add/${produtoId}`, { method: 'POST' })
  //   .then(response => response.json())
  //   .then(data => {
  //     console.log('Produto adicionado com sucesso:', data);
  //   })
  //   .catch(error => {
  //     console.error('Erro ao adicionar produto:', error);
  //   });
  
  // Restaurar botão após 2 segundos
  setTimeout(() => {
    button.innerHTML = originalText;
    button.style.background = '';
    button.disabled = false;
  }, 2000);
}

// Auto-scroll opcional (comentado por enquanto)
// let autoScrollInterval = setInterval(() => {
//   scrollProdutos('right');
// }, 5000);

// Parar auto-scroll quando o mouse estiver sobre o carrossel
// document.getElementById('produtos-carousel').addEventListener('mouseenter', () => {
//   clearInterval(autoScrollInterval);
// });

// Retomar auto-scroll quando o mouse sair
// document.getElementById('produtos-carousel').addEventListener('mouseleave', () => {
//   autoScrollInterval = setInterval(() => {
//     scrollProdutos('right');
//   }, 5000);
// });
</script>
