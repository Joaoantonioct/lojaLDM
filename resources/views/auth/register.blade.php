@extends('layouts.main')

@section('title', 'Criar conta | LDM')

@section('content')
<main class="login-page">
    <section class="login-panel" aria-labelledby="register-title">
        <div class="login-heading">
            <span class="login-kicker">Área do cliente</span>
            <h1 id="register-title">Crie sua conta</h1>
            <p>Cadastre seus dados para acompanhar compras e guardar suas pesquisas.</p>
        </div>

        @if($errors->any())
            <div class="login-error" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="login-form" method="POST" action="{{ route('register.store') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ old('redirect', request('redirect')) }}">

            <label for="name">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>

            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>

            <label for="password">Senha</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>

            <label for="password_confirmation">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

            <button type="submit">Criar conta</button>
        </form>

        <p class="login-secondary-link">
            Já possui uma conta? <a href="{{ route('login', request()->filled('redirect') ? ['redirect' => request('redirect')] : []) }}">Entrar</a>
        </p>
    </section>
</main>
@endsection
