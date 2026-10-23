<div class="logo">
    <a href="{{ auth()->check() ? route('cliente') : route('home') }}">
        <img src="/img/logo1.png" class="logo-img" alt="LDM" width="150px" height="150px">
    </a>
</div>