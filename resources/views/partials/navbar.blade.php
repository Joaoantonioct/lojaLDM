<div class="nav-container">
  <!-- Linha Superior: Logo e Busca -->
  <div class="top-bar">
    <div class="logo">
        <a href="#">
            <img src="img/ldm2026.png" class="logo-img" alt="LDM">
        </a>
    </div>

    <div class="search-bar">
      <input type="text" placeholder="Procure por produtos, marcas e muito mais...">
      <button type="submit">
        <img src="img/lupa1.png" width="20" alt="Buscar">
      </button>
    </div>
  </div>

  <!-- Linha Inferior: Categorias + Ações (Agora na mesma linha) -->
  <nav class="categories-nav">
    <ul class="nav-links">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle">Categorias</a>
            <ul class="dropdown-menu">
                <li><a href="#">Moda & íntima</a></li>
                <li><a href="#">Beleza & perfumaria</a></li>
                <li><a href="#">Papelaria & presentes</a></li>
                <li><a href="#">Tecnologia</a></li>
                <li><a href="#">Serviços</a></li>
            </ul>
        </li>
        <li><a href="#">Produtos</a></li>
        <li><a href="#">Ofertas</a></li>
        <li><a href="#">Novidades</a></li>
        <li><a href="#">Mais vendidos</a></li>
        <li><a href="#">Moda</a></li>
    </ul>

    <div class="user-actions-bar">
      <div class="account-links">
        @guest
            @include('layouts.guest.publico')
        @else
            @include('layouts.outh.logado')
        @endguest
      </div>
      <a href="/carrinho" class="cart-link" title="Ir para o carrinho">
          <!-- Substituindo o emoji pela imagem -->
          <img src="img/carrinho-de-compras.png" width="22" alt="carrinho" class="cart-icon">
          
          <!-- Badge do contador -->
          <span class="cart-badge">0</span>
        </a>
    </div>
  </nav>
</div>