@extends('layouts.guest.guest')

@section('content')
    <div class="container">
        @include('partials.carousel')
        
        <div class="row">
            @include('partials.sidebar')
            
            <div class="col-md-9">
                <!-- Conteúdo específico da página -->
            </div>
        </div>
    </div>
@endsection