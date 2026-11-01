@extends('layouts.public')

@section('title', 'LDM')

@section('content')
<main class="catalog-container">
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(blank($pesquisa ?? null))
        @include('components.catalog-carousel')
        @include('components.catalog.category-shortcuts')
    @endif
    <section class="home-featured" aria-labelledby="home-featured-title">
        <div class="catalog-section-heading">
            <h2 id="home-featured-title">Destaques</h2>
            <a href="{{ route('pesquisa') }}#produtos">Ver todos <span aria-hidden="true">→</span></a>
        </div>
    @include('components.cart-itens')
    </section>
</main>
@endsection
