<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/img/ldm.png">
    <title>@yield('title', 'Loja LDM')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/atalhocards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/card-produto.css') }}">
    <link rel="stylesheet" href="{{ asset('css/produtos-inspirados.css') }}">
    
    
    
    @stack('styles')
</head>
<body>
    <div class="site-wrapper">
        @include('partials.header')
        <main class="@yield('main-class', 'min-height-content')">
            @yield('content')
        </main>
        @include('partials.footer')
    </div>
    
    @stack('scripts')
</body>
</html>