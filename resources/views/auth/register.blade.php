@extends('layouts.main')

@section('title', 'Criar Conta')

@section('conteudo')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Criar Nova Conta</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf
                        
                        <!-- Dados Pessoais -->
                        <div class="mb-4">
                            <h5 class="mb-3">Dados Pessoais</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cpf" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="cpf" name="cpf" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(11) 99999-9999">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dados de Acesso -->
                        <div class="mb-4">
                            <h5 class="mb-3">Dados de Acesso</h5>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="senha" class="form-label">Senha</label>
                                        <input type="password" class="form-control" id="senha" name="senha" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Endereço -->
                        <div class="mb-4">
                            <h5 class="mb-3">Endereço de Entrega</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="rua" class="form-label">Rua</label>
                                        <input type="text" class="form-control" id="rua" name="rua">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="numero" name="numero">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="complemento" name="complemento">
                            </div>
                        </div>
                        
                        <!-- Preferências -->
                        <div class="mb-4">
                            <h5 class="mb-3">Preferências</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="receber_newsletter" name="receber_newsletter" checked>
                                <label class="form-check-label" for="receber_newsletter">
                                    Desejo receber newsletter e promoções por email
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="termos" name="termos" required>
                                <label class="form-check-label" for="termos">
                                    Li e aceito os <a href="#" class="text-decoration-none">termos de uso</a> e <a href="#" class="text-decoration-none">política de privacidade</a>
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Criar Conta
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-box-arrow-in-right"></i> Já tenho uma conta
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
