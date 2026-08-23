@extends('layouts.guest.guest')

@section('title', 'Criar Conta - LDM')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('content')
<div class="register-container">
    <!-- Card Principal -->
    <div class="register-card">
        <!-- Título Principal -->
        <div class="register-header">
            <h2 class="register-title">Crie sua conta e aproveite nossas ofertas</h2>
        </div>

        <!-- Seção de Login Social Google -->
        <div class="social-login-section">
            <div class="google-account-item" onclick="window.location.href='{{ route('auth.google') }}'">
                <div class="google-account-info">
                    <div class="google-avatar">
                        <svg viewBox="0 0 24 24" class="google-icon">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <div class="google-text">
                        <div class="google-label">Continuar como João</div>
                        <div class="google-email">contratoativo.br@gamil.com</div>
                    </div>
                </div>
                <div class="google-dropdown">
                    <svg viewBox="0 0 24 24" class="dropdown-icon">
                        <path fill="#5f6368" d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Divisor -->
        <div class="divider">
            <span>Ou digite seu e-mail</span>
        </div>

        <!-- Formulário de Email -->
        <form class="register-form" action="#" method="POST">
            @csrf
            
            <!-- Campo de Email -->
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="exemplo@email.com" required>
            </div>

            <!-- Botão de Continuar -->
            <button type="submit" class="continue-btn">
                Continuar
            </button>
        </form>
    </div>

    <!-- Link Corporativo (Fora do Card) -->
    <div class="corporate-link">
        <p>Você precisa de uma conta para sua empresa? <a href="#">Crie uma conta corporativa</a></p>
    </div>
</div>
@endsection
