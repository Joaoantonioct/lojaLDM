<header class="main-header" id="site-header">
    <div class="header-container">

        @include('partials.logo')

        @include('partials.search')

        @include('partials.header-actions')

    </div>

    <div class="mobile-location" aria-hidden="true">
        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
        <span>Enviar para <strong>Corumbá 79333090</strong></span>
        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
    </div>

    @include('partials.navbar')
</header>