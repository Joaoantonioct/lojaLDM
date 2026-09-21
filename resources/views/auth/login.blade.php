@extends('layouts.main')

@section('title', 'Entrar | LDM')

@section('content')
<main class="login-page">
    <section class="login-panel" aria-labelledby="login-title">
        <div class="login-heading">
            <span class="login-kicker">Área do cliente</span>
            <h1 id="login-title">Entre na sua conta</h1>
            <p>Acesse seus produtos favoritos, compras e pesquisas.</p>
        </div>

        @if($errors->any())
            <div class="login-error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form class="login-form" method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>

            <label for="password">Senha</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <label class="login-remember" for="remember">
                <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                <span>Manter minha sessão</span>
            </label>

            <button type="submit">Entrar</button>
        </form>

        <p class="login-secondary-link">
            Ainda não possui uma conta? <a href="{{ route('register') }}">Criar conta</a>
        </p>
    </section>
</main>
@endsection
