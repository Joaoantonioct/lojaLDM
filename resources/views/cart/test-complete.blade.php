@extends('layouts.public')

@section('title', 'Pedido de teste registrado | LDM')

@push('styles')
    @vite('resources/css/cart.css')
@endpush

@section('content')
<main class="cart-page">
    <nav class="cart-breadcrumb" aria-label="Navegação estrutural">
        <a href="{{ route('home') }}">Início</a>
        <span aria-hidden="true">/</span>
        <strong>Pedido de teste</strong>
    </nav>

    <section class="cart-empty">
        <h1>Pedido de teste registrado</h1>
        <p>Este pedido não processou pagamento. O estoque foi reservado no site e a baixa foi enviada ao PDV para sincronização.</p>
        <p>Pedido: <strong>{{ $order->public_id }}</strong></p>
        <ul>
            @foreach($items as $item)
                <li>{{ $item->product_name }} — {{ $item->quantity }} unidade(s)</li>
            @endforeach
        </ul>
        <p>Total de teste: <strong>R$ {{ number_format($order->total_cents / 100, 2, ',', '.') }}</strong></p>
        <a class="cart-primary-link" href="{{ route('home') }}">Voltar ao catálogo</a>
    </section>
</main>
@endsection
