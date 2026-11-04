@extends('layouts.main')

@section('title', 'Adicionar produto | LDM')

@section('content')
<main class="product-create-page">
    <header class="product-create-heading">
        <div>
            <p class="product-create-eyebrow"><i class="fa-solid fa-cube" aria-hidden="true"></i> Área do vendedor</p>
            <h1>Adicionar produto</h1>
            <p>Preencha as informações do produto para exibi-lo no catálogo da loja.</p>
        </div>
        <span class="product-create-heading-icon" aria-hidden="true"><i class="fa-solid fa-box-open"></i></span>
    </header>

    <form class="product-create-form" data-product-create-form action="{{ url('/pagina/add') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="private" value="0">

        <div class="product-create-fields">
            <div class="product-create-field product-create-field-full">
                <label for="name"><i class="fa-solid fa-cube" aria-hidden="true"></i> Nome do produto <span>*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ex.: Camiseta básica" required>
                @error('name')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <div class="product-create-field product-create-field-full">
                <label for="brand_id"><i class="fa-solid fa-tag" aria-hidden="true"></i> Marca <span>*</span></label>
                <select id="brand_id" name="brand_id" required>
                    <option value="">Selecione uma marca</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ old('brand_id') == $marca->id ? 'selected' : '' }}>{{ $marca->name }}</option>
                    @endforeach
                </select>
                <button class="product-create-add-brand" type="button" data-brand-toggle aria-expanded="false" aria-controls="new-brand-panel">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Cadastrar marca
                </button>
                <div class="product-create-new-brand" id="new-brand-panel" data-brand-panel data-brand-endpoint="{{ route('brands.store') }}" hidden>
                    <label for="new-brand-name">Nome da nova marca</label>
                    <div class="product-create-new-brand-controls">
                        <input id="new-brand-name" type="text" maxlength="100" autocomplete="off" data-new-brand-name placeholder="Ex.: Minha marca">
                        <button type="button" data-brand-save>Salvar</button>
                    </div>
                </div>
                <small class="product-create-inline-feedback" data-brand-feedback role="status" aria-live="polite" hidden></small>
                @error('brand_id')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <div class="product-create-field-row">
                <div class="product-create-field">
                    <label for="price"><i class="fa-solid fa-circle-dollar-to-slot" aria-hidden="true"></i> Preço de venda <span>*</span></label>
                    <div class="product-create-input-prefix"><span>R$</span><input type="text" id="price" name="price" value="{{ old('price') }}" inputmode="decimal" autocomplete="off" placeholder="0,00" required></div>
                    @error('price')<small class="product-create-error">{{ $message }}</small>@enderror
                </div>
                <div class="product-create-field">
                    <label for="cost"><i class="fa-solid fa-tag" aria-hidden="true"></i> Custo <span>*</span></label>
                    <div class="product-create-input-prefix"><span>R$</span><input type="text" id="cost" name="cost" value="{{ old('cost') }}" inputmode="decimal" autocomplete="off" placeholder="0,00" required></div>
                    @error('cost')<small class="product-create-error">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="product-create-field product-create-field-full">
                <label for="size_type"><i class="fa-solid fa-ruler" aria-hidden="true"></i> Tipo de peça <span>*</span></label>
                <select id="size_type" name="size_type" required>
                    <option value="">Selecione o tipo de peça</option>
                    <option value="clothing" {{ old('size_type') === 'clothing' ? 'selected' : '' }}>Roupas em letras (PP a XGG: blusas, camisetas, vestidos, saias e jaquetas)</option>
                    <option value="trousers" {{ old('size_type') === 'trousers' ? 'selected' : '' }}>Calça, legging, bermuda e shorts (numeração 34 a 60)</option>
                    <option value="footwear" {{ old('size_type') === 'footwear' ? 'selected' : '' }}>Calçado (numeração 33 a 46)</option>
                    <option value="none" {{ old('size_type') === 'none' ? 'selected' : '' }}>Outro produto, sem grade de tamanho</option>
                </select>
                @error('size_type')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <section class="product-create-field product-create-field-full product-create-colors" data-color-field>
                <div class="product-create-color-heading">
                    <div>
                        <strong>Cores do produto</strong>
                        <p class="product-create-subcategory-hint">Adicione uma foto para cada cor. O estoque será separado por cor e tamanho.</p>
                    </div>
                    <button class="product-create-add-brand" type="button" data-add-color>
                        <i class="fa-solid fa-plus" aria-hidden="true"></i> Adicionar cor
                    </button>
                </div>
                <div class="product-create-color-list" data-color-options></div>
                @error('colors')<small class="product-create-error">{{ $message }}</small>@enderror
                @error('colors.*.name')<small class="product-create-error">{{ $message }}</small>@enderror
                @error('colors.*.image')<small class="product-create-error">{{ $message }}</small>@enderror
                @error('variants')<small class="product-create-error">{{ $message }}</small>@enderror
                @error('variants.*.size')<small class="product-create-error">{{ $message }}</small>@enderror
                @error('variants.*.qty')<small class="product-create-error">{{ $message }}</small>@enderror
            </section>

            <div class="product-create-field-row">
                <div class="product-create-field">
                    <label for="qty"><i class="fa-solid fa-cube" aria-hidden="true"></i> Quantidade em estoque <span>*</span></label>
                    <input type="number" id="qty" name="qty" value="{{ old('qty') }}" min="0" placeholder="Ex.: 10" required data-total-qty>
                    <small class="product-create-subcategory-hint" data-total-qty-hint hidden>O total será calculado pela soma das combinações de cor e tamanho.</small>
                    @error('qty')<small class="product-create-error">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="product-create-field product-create-field-full">
                <label for="category_id"><i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i> Categoria <span>*</span></label>
                <select id="category_id" name="category_id" required>
                    <option value="">Selecione uma categoria</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" data-apparel="{{ $categoria->isApparelCategory() ? 'true' : 'false' }}" data-size-type="{{ $categoria->apparelSizeType() }}" {{ old('category_id', old('parent_category_id')) == $categoria->id ? 'selected' : '' }}>{{ $categoria->name }}</option>
                    @endforeach
                </select>
                <button class="product-create-add-brand" type="button" data-inline-toggle="category" aria-expanded="false" aria-controls="new-category-panel">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Cadastrar categoria
                </button>
                <div class="product-create-new-brand" id="new-category-panel" data-inline-panel="category" data-category-endpoint="{{ route('categories.store') }}" hidden>
                    <label for="new-category-name">Nome da categoria</label>
                    <div class="product-create-new-brand-controls">
                        <input id="new-category-name" type="text" maxlength="100" autocomplete="off" data-inline-name="category" placeholder="Ex.: Casa e decoração">
                        <button type="button" data-inline-save="category">Salvar</button>
                    </div>
                </div>
                <small class="product-create-inline-feedback" data-inline-feedback="category" role="status" aria-live="polite" hidden></small>
                @error('category_id')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <div class="product-create-field product-create-field-full">
                <label for="subcategory_id"><i class="fa-solid fa-grip" aria-hidden="true"></i> Subcategoria <span data-subcategory-required hidden>*</span></label>
                <select id="subcategory_id" name="subcategory_id" disabled>
                    <option value="">Selecione uma subcategoria (opcional)</option>
                    @foreach($categorias as $categoria)
                        @foreach($categoria->children->sortBy('name') as $subcategoria)
                            <option value="{{ $subcategoria->id }}" data-parent="{{ $categoria->id }}" data-apparel="{{ $subcategoria->isApparelCategory() ? 'true' : 'false' }}" data-size-type="{{ $subcategoria->apparelSizeType() }}" {{ old('subcategory_id') == $subcategoria->id ? 'selected' : '' }}>{{ $subcategoria->name }}</option>
                        @endforeach
                    @endforeach
                </select>
                <small class="product-create-subcategory-hint" data-subcategory-hint>Escolha uma categoria para ver as opções disponíveis.</small>
                <button class="product-create-add-brand" type="button" data-inline-toggle="subcategory" aria-expanded="false" aria-controls="new-subcategory-panel" disabled>
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Cadastrar subcategoria
                </button>
                <div class="product-create-new-brand" id="new-subcategory-panel" data-inline-panel="subcategory" data-category-endpoint="{{ route('categories.store') }}" hidden>
                    <label for="new-subcategory-name">Nome da subcategoria</label>
                    <div class="product-create-new-brand-controls">
                        <input id="new-subcategory-name" type="text" maxlength="100" autocomplete="off" data-inline-name="subcategory" placeholder="Ex.: Acessórios">
                        <button type="button" data-inline-save="subcategory">Salvar</button>
                    </div>
                </div>
                <small class="product-create-inline-feedback" data-inline-feedback="subcategory" role="status" aria-live="polite" hidden></small>
                <a class="product-create-category-link" href="{{ url('/pagina/categorias') }}">Não encontrou? Cadastre uma subcategoria.</a>
                @error('subcategory_id')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <div class="product-create-field product-create-field-full" data-condition-field hidden>
                <label for="condition"><i class="fa-solid fa-tag" aria-hidden="true"></i> Estado do produto <span>*</span></label>
                <select id="condition" name="condition" disabled>
                    <option value="novo" {{ old('condition', 'novo') === 'novo' ? 'selected' : '' }}>Novo</option>
                    <option value="seminovo" {{ old('condition') === 'seminovo' ? 'selected' : '' }}>Seminovo</option>
                    <option value="brecho" {{ old('condition') === 'brecho' ? 'selected' : '' }}>Brechó</option>
                </select>
                @error('condition')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <div class="product-create-field product-create-field-full" data-size-field hidden>
                <label><i class="fa-solid fa-ruler" aria-hidden="true"></i> Tamanhos e estoque por tamanho <span>*</span></label>
                <p class="product-create-subcategory-hint">Selecione os tamanhos disponíveis e informe quantas unidades há de cada um.</p>
                <div class="product-create-size-grid" data-size-options data-selected-variants="{{ json_encode(old('variants', [])) }}"></div>
                @error('variants')<small class="product-create-error">{{ $message }}</small>@enderror
                @error('variants.*.size')<small class="product-create-error">{{ $message }}</small>@enderror
                @error('variants.*.qty')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <div class="product-create-field product-create-field-full">
                <label for="description"><i class="fa-regular fa-file-lines" aria-hidden="true"></i> Descrição <span>*</span></label>
                <textarea id="description" name="description" rows="5" maxlength="1000" placeholder="Descreva as principais características do produto..." required>{{ old('description') }}</textarea>
                <small class="product-create-character-count"><span data-description-count>0</span>/1000</small>
                @error('description')<small class="product-create-error">{{ $message }}</small>@enderror
            </div>

            <div class="product-create-field product-create-field-full">
                <label><i class="fa-regular fa-image" aria-hidden="true"></i> Imagem da vitrine</label>
                <div class="product-create-cover-preview" data-cover-preview>
                    <span class="product-create-cover-placeholder"><i class="fa-regular fa-image" aria-hidden="true"></i> A primeira imagem será a imagem principal</span>
                </div>
            </div>
        </div>

        <aside class="product-create-image-panel" aria-labelledby="product-images-title">
            <div class="product-create-image-heading">
                <h2 id="product-images-title">Imagens adicionadas</h2>
                <span data-image-count>0 de 10</span>
            </div>
            <div class="product-create-image-grid" data-image-preview aria-live="polite"></div>
            <label class="product-create-dropzone" data-image-dropzone for="images">
                <i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i>
                <strong>Arraste e solte ao menos uma imagem aqui <span>*</span></strong>
                <span>ou clique para selecionar</span>
                <small>JPG, PNG ou WEBP. Até 5 MB por imagem.</small>
            </label>
            <input class="product-create-file-input" type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required>
            <small class="product-create-upload-note">Envie até 10 imagens. A primeira será usada na vitrine do produto.</small>
            @error('images')<small class="product-create-error">{{ $message }}</small>@enderror
            @error('images.*')<small class="product-create-error">{{ $message }}</small>@enderror
        </aside>

        @if($errors->any())
            <div class="product-create-errors" role="alert">
                <strong>Revise os dados do produto:</strong>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <footer class="product-create-actions">
            <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Cancelar</a>
            <button type="submit"><i class="fa-regular fa-floppy-disk" aria-hidden="true"></i> Revisar e publicar produto</button>
        </footer>
    </form>
</main>
@endsection

@push('scripts')
    @vite('resources/js/product-create.js')
@endpush
