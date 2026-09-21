@extends('layouts.public')

@section('title', 'LDM')

@section('content')
<main class="catalog-container">
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @include('components.catalog-carousel')
    @include('components.cart-itens')
</main>
@endsection
