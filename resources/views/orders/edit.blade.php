@extends('layouts.main')

@section('title', 'Editar Pedido')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Pedido #{{ $id }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('orders.update', $id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Dados do Cliente -->
                        <div class="mb-4">
                            <h5>Dados do Cliente</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_nome" class="form-label">Nome do Cliente</label>
                                        <input type="text" class="form-control" id="cliente_nome" name="cliente_nome" value="João Silva" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="cliente_email" name="cliente_email" value="joao.silva@email.com">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="cliente_telefone" name="cliente_telefone" value="(11) 99999-9999">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_cpf" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="cliente_cpf" name="cliente_cpf" value="123.456.789-00">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status do Pedido -->
                        <div class="mb-4">
                            <h5>Status do Pedido</h5>
                            <select class="form-control" id="status" name="status">
                                <option value="pendente">Pendente</option>
                                <option value="confirmado">Confirmado</option>
                                <option value="preparacao">Em Preparação</option>
                                <option value="transporte">Em Transporte</option>
                                <option value="entregue" selected>Entregue</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        
                        <!-- Endereço de Entrega -->
                        <div class="mb-4">
                            <h5>Endereço de Entrega</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="endereco_cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control" id="endereco_cep" name="endereco_cep" value="01234-567">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="endereco_cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="endereco_cidade" name="endereco_cidade" value="São Paulo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="endereco_rua" class="form-label">Rua</label>
                                        <input type="text" class="form-control" id="endereco_rua" name="endereco_rua" value="das Flores">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="endereco_numero" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="endereco_numero" name="endereco_numero" value="123">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="endereco_complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="endereco_complemento" name="endereco_complemento" value="Apto 45">
                            </div>
                        </div>
                        
                        <!-- Produtos do Pedido -->
                        <div class="mb-4">
                            <h5>Produtos do Pedido</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Quantidade</th>
                                            <th>Preço Unit.</th>
                                            <th>Subtotal</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="form-select" name="produto_id[]">
                                                    <option value="1" selected>Vestido Rosa - R$ 99,90</option>
                                                    <option value="2">Bolsa Preta - R$ 149,90</option>
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control" name="quantidade[]" value="1" min="1"></td>
                                            <td>R$ 99,90</td>
                                            <td>R$ 99,90</td>
                                            <td><button type="button" class="btn btn-sm btn-danger">Remover</button></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <select class="form-select" name="produto_id[]">
                                                    <option value="1">Vestido Rosa - R$ 99,90</option>
                                                    <option value="2" selected>Bolsa Preta - R$ 149,90</option>
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control" name="quantidade[]" value="2" min="1"></td>
                                            <td>R$ 99,90</td>
                                            <td>R$ 199,80</td>
                                            <td><button type="button" class="btn btn-sm btn-danger">Remover</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus"></i> Adicionar Produto
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $id) }}" class="btn btn-secondary">
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
                    <h5>Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>R$ 299,70</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <strong>R$ 15,00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto:</span>
                        <strong class="text-success">-R$ 15,00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5">Total:</span>
                        <strong class="h5 text-primary">R$ 299,70</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                        <select class="form-control" id="forma_pagamento" name="forma_pagamento">
                            <option value="cartao" selected>Cartão de Crédito</option>
                            <option value="debito">Cartão de Débito</option>
                            <option value="pix">PIX</option>
                            <option value="boleto">Boleto</option>
                        </select>
                    </div>
                    
                    <!-- Observações -->
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3">Cliente solicitou entrega rápida.</textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Confirmar Pedido
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Cancelar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
