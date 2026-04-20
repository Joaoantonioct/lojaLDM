@extends('layouts.main')

@section('title', 'Usuários')

@section('conteudo')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Usuários</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Usuário
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/img/placeholder.png" width="40" class="rounded-circle me-2" alt="Avatar">
                                    <div>
                                        <strong>João Silva</strong><br>
                                        <small class="text-muted">CPF: 123.456.789-00</small>
                                    </div>
                                </div>
                            </td>
                            <td>joao.silva@email.com</td>
                            <td>(11) 99999-9999</td>
                            <td><span class="badge bg-primary">Cliente</span></td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.show', 1) }}" class="btn btn-outline-primary">Ver</a>
                                    <a href="{{ route('users.edit', 1) }}" class="btn btn-outline-warning">Editar</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#002</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/img/placeholder.png" width="40" class="rounded-circle me-2" alt="Avatar">
                                    <div>
                                        <strong>Maria Santos</strong><br>
                                        <small class="text-muted">CPF: 987.654.321-00</small>
                                    </div>
                                </div>
                            </td>
                            <td>maria.santos@email.com</td>
                            <td>(11) 88888-8888</td>
                            <td><span class="badge bg-primary">Cliente</span></td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.show', 2) }}" class="btn btn-outline-primary">Ver</a>
                                    <a href="{{ route('users.edit', 2) }}" class="btn btn-outline-warning">Editar</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#003</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/img/placeholder.png" width="40" class="rounded-circle me-2" alt="Avatar">
                                    <div>
                                        <strong>Admin LDM</strong><br>
                                        <small class="text-muted">CPF: 111.222.333-44</small>
                                    </div>
                                </div>
                            </td>
                            <td>admin@ldm.com.br</td>
                            <td>(11) 77777-7777</td>
                            <td><span class="badge bg-danger">Administrador</span></td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.show', 3) }}" class="btn btn-outline-primary">Ver</a>
                                    <a href="{{ route('users.edit', 3) }}" class="btn btn-outline-warning">Editar</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
