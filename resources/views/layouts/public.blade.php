<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/ldm.png') }}?v=2">
    <title>@yield('title')</title>

    @vite([
        'resources/css/style.css',
        'resources/css/welcome.css',
        'resources/css/cart-itens.css',
        'resources/css/app.css',
        'resources/css/header.css',
        'resources/css/footer.css',
        'resources/css/catalog-carousel.css',
        'resources/js/app.js',
        'resources/js/cart-itens.js',
    ])
    @stack('styles')
</head>
<body data-cart-owner="{{ auth()->id() ?? 'guest' }}">
    @include('partials.header')

    @yield('content')

    @include('partials.footer')
    @include('partials.mobile-nav')

    @stack('scripts')
</body>
</html>
