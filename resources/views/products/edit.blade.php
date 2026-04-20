@extends('layouts.main')

@section('title', 'Editar Produto')

@section('conteudo')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Produto #{{ $id }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('produtos.update', $id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="Produto Exemplo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3">Descrição do produto...</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem do Produto</label>
                            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                            <small class="form-text text-muted">Deixe em branco para manter a imagem atual. Formatos aceitos: JPG, PNG, GIF.</small>
                            <div class="mt-2">
                                <img src="/img/placeholder.png" alt="Imagem atual" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted small">Imagem atual</p>
                            </div>
                            <div class="mt-2" id="preview-container" style="display: none;">
                                <img id="image-preview" src="#" alt="Nova imagem" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted small">Nova imagem</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco" class="form-label">Preço</label>
                                    <input type="decimal" class="form-control" id="preco" name="preco" value="99.90" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estoque" class="form-label">Estoque</label>
                                    <input type="number" class="form-control" id="estoque" name="estoque" value="10" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoria</label>
                            <select class="form-control" id="categoria_id" name="categoria_id">
                                <option value="">Selecione uma categoria</option>
                                <option value="1" selected>Categoria Exemplo</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('produtos.show', $id) }}" class="btn btn-secondary">
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