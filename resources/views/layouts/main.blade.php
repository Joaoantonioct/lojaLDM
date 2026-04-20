<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <title>@yield('title')</title>

    <!--FAVICON-->
    <link rel="icon" type="image/png" href="/img/ldm.png">

    <!--FONTE GOOGLE-->

    <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">

    <!--CSS BOOTSTRAP--->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!--CSS PERSONALIZADO-->

    <link rel="stylesheet" href="/css/style.css">
    
    <!--JS PERSONALIZADO-->
    <script src="/js/script.js"></script>
</head>
<body>
    <x-navbar />
    
    @yield('conteudo')
    
    <x-footer />
</body>
</html>