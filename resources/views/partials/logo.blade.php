<div class="logo">
    <a href="{{ auth()->check() ? route('cliente') : route('home') }}">
        <img src="{{ asset('img/logo1.png') }}" class="logo-img" alt="LDM - Loja de variedades">
    </a>
</div>