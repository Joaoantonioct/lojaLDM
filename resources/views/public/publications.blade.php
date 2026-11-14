@extends('layouts.public')

@section('title', 'Combinações | LDM')

@section('content')
<main class="catalog-container">
    <header class="catalog-section-heading">
        <div>
            <p class="catalog-category">Inspire-se</p>
            <h1>Looks publicados</h1>
        </div>
    </header>

    @if(($publications ?? collect())->isEmpty())
        <p class="local-notice">Não há combinações disponíveis no momento.</p>
    @else
        @include('components.catalog.publications', ['publications' => $publications])
    @endif
</main>
@endsection