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
            'resources/css/contact.css', 
            'resources/css/client.css', 
            'resources/css/login.css', 
            'resources/js/app.js',
            'resources/css/add.css', 
            'resources/css/catalog-carousel.css', 
            'resources/js/cart-itens.js',
            'resources/js/detallsProducts.js',
            'resources/css/detalssProducts.css'
        ])

</head>
<body>
    
        @include('partials.header')

    @yield('content')

    
    
    @include('partials.footer')
</body>
</html>
