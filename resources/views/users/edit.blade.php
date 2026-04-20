@extends('layouts.main')

@section('title', 'Editar Usuário')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Usuário #{{ $id }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Dados Pessoais -->
                        <div class="mb-4">
                            <h5>Dados Pessoais</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" id="nome" name="nome" value="João Silva" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cpf" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="cpf" name="cpf" value="123.456.789-00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="1990-03-15">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone" value="(11) 99999-9999">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dados de Acesso -->
                        <div class="mb-4">
                            <h5>Dados de Acesso</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="joao.silva@email.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nova_senha" class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Deixe em branco para manter atual">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a nova senha">
                            </div>
                        </div>
                        
                        <!-- Endereço -->
                        <div class="mb-4">
                            <h5>Endereço Principal</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control" id="cep" name="cep" value="01234-567">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" value="São Paulo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="rua" class="form-label">Rua</label>
                                        <input type="text" class="form-control" id="rua" name="rua" value="das Flores">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="numero" name="numero" value="123">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="complemento" name="complemento" value="Apto 45">
                            </div>
                        </div>
                        
                        <!-- Permissões -->
                        <div class="mb-4">
                            <h5>Permissões</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                                        <select class="form-control" id="tipo_usuario" name="tipo_usuario">
                                            <option value="cliente" selected>Cliente</option>
                                            <option value="admin">Administrador</option>
                                            <option value="funcionario">Funcionário</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="ativo" selected>Ativo</option>
                                            <option value="inativo">Inativo</option>
                                            <option value="bloqueado">Bloqueado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="receber_newsletter" name="receber_newsletter" checked>
                                    <label class="form-check-label" for="receber_newsletter">
                                        Receber newsletter e promoções
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observações -->
                        <div class="mb-4">
                            <h5>Observações Internas</h5>
                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3">Cliente VIP, sempre compra produtos de alta qualidade.</textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.show', $id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-info">
                            <i class="bi bi-lock"></i> Redefinir Senha
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="bi bi-envelope"></i> Enviar Email de Boas Vindas
                        </button>
                        <button type="button" class="btn btn-warning">
                            <i class="bi bi-gift"></i> Enviar Cupom de Desconto
                        </button>
                        <button type="button" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle"></i> Bloquear Usuário
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Histórico de Atividades -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Histórico de Atividades</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <small class="text-muted">20/04/2026 14:30</small>
                            <p class="mb-1">Usuário realizou pedido #001</p>
                        </div>
                        <div class="timeline-item">
                            <small class="text-muted">19/04/2026 10:15</small>
                            <p class="mb-1">Usuário atualizou perfil</p>
                        </div>
                        <div class="timeline-item">
                            <small class="text-muted">18/04/2026 16:45</small>
                            <p class="mb-1">Usuário cadastrado no sistema</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
