@extends('layouts.public')

@section('title', 'Acompanhamento do pedido | LDM')

@push('styles')
    @vite('resources/css/cart.css')
@endpush

@section('content')
<main class="cart-page">
    <nav class="cart-breadcrumb" aria-label="Navegação estrutural">
        <a href="{{ route('home') }}">Início</a>
        <span aria-hidden="true">/</span>
        <strong>Pedido</strong>
    </nav>

    @php
        $paymentStatus = $order->payment_status ?? 'pending';

        $messages = [
            'pending' => [
                'title' => 'Aguardando confirmação do pagamento',
                'description' => 'Recebemos seu pedido. Assim que o Asaas confirmar o pagamento, atualizaremos esta situação.',
            ],
            'confirmed' => [
                'title' => 'Pagamento confirmado!',
                'description' => 'Seu pagamento foi confirmado. Seu pedido está liberado para sincronização com a loja.',
            ],
            'paid_review' => [
                'title' => 'Pagamento em análise',
                'description' => 'Recebemos a informação de pagamento, mas precisamos conferir alguns dados antes de liberar seu pedido.',
            ],
            'failed' => [
                'title' => 'Não foi possível iniciar o pagamento',
                'description' => 'O pagamento não foi iniciado. Entre em contato com a loja se precisar de ajuda.',
            ],
            'canceled' => [
                'title' => 'Pagamento cancelado',
                'description' => 'O checkout foi cancelado e a reserva de estoque foi liberada.',
            ],
            'expired' => [
                'title' => 'Checkout expirado',
                'description' => 'O prazo para pagamento terminou e a reserva de estoque foi liberada.',
            ],
            'checkout_unknown' => [
                'title' => 'Pagamento em verificação',
                'description' => 'Não foi possível confirmar a criação do checkout. Estamos verificando a situação. Evite realizar outro pagamento para este pedido até a conferência.',
            ],
        ];

        $message = $messages[$paymentStatus] ?? [
            'title' => 'Pedido em processamento',
            'description' => 'A situação do pedido está sendo verificada.',
        ];
    @endphp

    <section class="cart-empty">
        <h1>{{ $message['title'] }}</h1>
        <p>{{ $message['description'] }}</p>

        <p>Pedido: <strong>{{ $order->public_id }}</strong></p>

        <ul>
            @foreach($items as $item)
                <li>
                    {{ $item->product_name }} — {{ $item->quantity }} unidade(s)
                </li>
            @endforeach
        </ul>

        <p>
            Total:
            <strong>R$ {{ number_format($order->total_cents / 100, 2, ',', '.') }}</strong>
        </p>

        <a class="cart-primary-link" href="{{ route('home') }}">
            Voltar ao catálogo
        </a>
    </section>
</main>
@endsection
