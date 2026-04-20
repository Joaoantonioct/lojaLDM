@extends('layouts.main')

@section('title', 'Cadastrar Produto')

@section('conteudo')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Cadastrar Novo Produto</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('produtos.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem do Produto</label>
                            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                            <small class="form-text text-muted">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                            <div class="mt-2" id="preview-container" style="display: none;">
                                <img id="image-preview" src="#" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco" class="form-label">Preço</label>
                                    <input type="decimal" class="form-control" id="preco" name="preco" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estoque" class="form-label">Estoque</label>
                                    <input type="number" class="form-control" id="estoque" name="estoque" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoria</label>
                            <select class="form-control" id="categoria_id" name="categoria_id">
                                <option value="">Selecione uma categoria</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cadastrar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('imagem').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('image-preview');
    const container = document.getElementById('preview-container');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        container.style.display = 'none';
        preview.src = '#';
    }
});
</script>
@endpush