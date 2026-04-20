@extends('layouts.main')

@section('title', 'Novo Pedido')

@section('conteudo')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Novo Pedido</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('orders.store') }}">
                        @csrf
                        
                        <!-- Dados do Cliente -->
                        <div class="mb-4">
                            <h5>Dados do Cliente</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_nome" class="form-label">Nome do Cliente</label>
                                        <input type="text" class="form-control" id="cliente_nome" name="cliente_nome" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="cliente_email" name="cliente_email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="cliente_telefone" name="cliente_telefone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_cpf" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="cliente_cpf" name="cliente_cpf">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Endereço de Entrega -->
                        <div class="mb-4">
                            <h5>Endereço de Entrega</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="endereco_cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control" id="endereco_cep" name="endereco_cep">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="endereco_cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="endereco_cidade" name="endereco_cidade">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="endereco_rua" class="form-label">Rua</label>
                                        <input type="text" class="form-control" id="endereco_rua" name="endereco_rua">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="endereco_numero" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="endereco_numero" name="endereco_numero">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="endereco_complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="endereco_complemento" name="endereco_complemento">
                            </div>
                        </div>
                        
                        <!-- Produtos do Pedido -->
                        <div class="mb-4">
                            <h5>Produtos do Pedido</h5>
                            <div class="table-responsive">
                                <table class="table" id="produtos_table">
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
                                                    <option value="">Selecione um produto</option>
                                                    <option value="1">Vestido Rosa - R$ 99,90</option>
                                                    <option value="2">Bolsa Preta - R$ 149,90</option>
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control" name="quantidade[]" value="1" min="1"></td>
                                            <td>R$ 0,00</td>
                                            <td>R$ 0,00</td>
                                            <td><button type="button" class="btn btn-sm btn-danger">Remover</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addProductRow()">
                                <i class="bi bi-plus"></i> Adicionar Produto
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Criar Pedido
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
                        <strong id="subtotal">R$ 0,00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <strong id="frete">R$ 15,00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto:</span>
                        <strong id="desconto">R$ 0,00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5">Total:</span>
                        <strong class="h5 text-primary" id="total">R$ 15,00</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                        <select class="form-control" id="forma_pagamento" name="forma_pagamento">
                            <option value="cartao">Cartão de Crédito</option>
                            <option value="debito">Cartão de Débito</option>
                            <option value="pix">PIX</option>
                            <option value="boleto">Boleto</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addProductRow() {
    // Lógica para adicionar nova linha de produto
    alert('Funcionalidade a ser implementada');
}
</script>
@endsection
