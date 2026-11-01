document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-product-create-form]');
    if (!form) return;

    const categorySelect = form.querySelector('#category_id');
    const subcategorySelect = form.querySelector('#subcategory_id');
    const subcategoryHint = form.querySelector('.product-create-subcategory-hint');
    const conditionField = form.querySelector('[data-condition-field]');
    const conditionSelect = form.querySelector('#condition');
    const brandSelect = form.querySelector('#brand_id');
    const brandToggle = form.querySelector('[data-brand-toggle]');
    const brandPanel = form.querySelector('[data-brand-panel]');
    const brandName = form.querySelector('[data-new-brand-name]');
    const brandSave = form.querySelector('[data-brand-save]');
    const brandFeedback = form.querySelector('[data-brand-feedback]');
    const categoryToggle = form.querySelector('[data-inline-toggle="category"]');
    const categoryPanel = form.querySelector('[data-inline-panel="category"]');
    const subcategoryToggle = form.querySelector('[data-inline-toggle="subcategory"]');
    const subcategoryPanel = form.querySelector('[data-inline-panel="subcategory"]');
    const description = form.querySelector('#description');
    const descriptionCount = form.querySelector('[data-description-count]');
    const imageInput = form.querySelector('#images');
    const dropzone = form.querySelector('[data-image-dropzone]');
    const previewGrid = form.querySelector('[data-image-preview]');
    const coverPreview = form.querySelector('[data-cover-preview]');
    const imageCount = form.querySelector('[data-image-count]');
    const imageLimit = 10;
    const imageSizeLimit = 5 * 1024 * 1024;
    const acceptedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    let selectedImages = [];

    function updateSubcategories() {
        const categoryId = categorySelect.value;
        let hasOptions = false;

        Array.from(subcategorySelect.options).forEach(function (option, index) {
            if (index === 0) {
                option.hidden = false;
                return;
            }

            const belongsToCategory = option.dataset.parent === categoryId;
            option.hidden = !belongsToCategory;
            hasOptions = hasOptions || belongsToCategory;
        });

        subcategorySelect.disabled = !categoryId || !hasOptions;
        subcategoryToggle.disabled = !categoryId;

        if (!hasOptions || subcategorySelect.selectedOptions[0]?.dataset.parent !== categoryId) {
            subcategorySelect.value = '';
        }

        subcategoryHint.textContent = hasOptions
            ? 'Selecione uma subcategoria, se aplicável.'
            : 'Esta categoria não possui subcategorias.';

        updateConditionField();
    }

    function updateConditionField() {
        const selectedSubcategory = subcategorySelect.selectedOptions[0];
        const selectedCategory = categorySelect.selectedOptions[0];
        const isApparel = selectedSubcategory?.value
            ? selectedSubcategory.dataset.apparel === 'true'
            : selectedCategory?.dataset.apparel === 'true';

        conditionField.hidden = !isApparel;
        conditionSelect.disabled = !isApparel;
        conditionSelect.required = Boolean(isApparel);

        if (!isApparel) {
            conditionSelect.value = '';
        } else if (!conditionSelect.value) {
            conditionSelect.value = 'novo';
        }
    }

    function setInlineFeedback(feedback, message, isSuccess) {
        feedback.textContent = message;
        feedback.hidden = false;
        feedback.classList.toggle('is-success', isSuccess);
    }

    function toggleInlinePanel(toggle, panel, input) {
        const isOpening = panel.hidden;
        panel.hidden = !isOpening;
        toggle.setAttribute('aria-expanded', String(isOpening));
        if (isOpening) input.focus();
    }

    function closeInlinePanel(toggle, panel) {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
    }

    async function saveInlineCategory(type) {
        const panel = form.querySelector(`[data-inline-panel="${type}"]`);
        const toggle = form.querySelector(`[data-inline-toggle="${type}"]`);
        const input = form.querySelector(`[data-inline-name="${type}"]`);
        const feedback = form.querySelector(`[data-inline-feedback="${type}"]`);
        const saveButton = form.querySelector(`[data-inline-save="${type}"]`);
        const name = input.value.trim();
        const parentId = type === 'subcategory' ? categorySelect.value : null;

        if (!name) {
            setInlineFeedback(feedback, `Informe o nome da ${type === 'category' ? 'categoria' : 'subcategoria'}.`, false);
            input.focus();
            return;
        }

        if (type === 'subcategory' && !parentId) {
            setInlineFeedback(feedback, 'Selecione uma categoria antes de cadastrar a subcategoria.', false);
            return;
        }

        saveButton.disabled = true;
        setInlineFeedback(feedback, 'Salvando...', false);

        try {
            const response = await fetch(panel.dataset.categoryEndpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                },
                body: JSON.stringify({ name, parent_id: parentId }),
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.errors?.name?.[0] || result.errors?.parent_id?.[0] || result.message || 'Não foi possível salvar.');
            }

            if (type === 'category') {
                let option = Array.from(categorySelect.options).find((item) => item.value === String(result.category.id));
                if (!option) {
                    option = new Option(result.category.name, result.category.id);
                    option.dataset.apparel = String(result.category.apparel);
                    categorySelect.add(option);
                }
                categorySelect.value = String(result.category.id);
                updateSubcategories();
            } else {
                let option = Array.from(subcategorySelect.options).find((item) => item.value === String(result.category.id));
                if (!option) {
                    option = new Option(result.category.name, result.category.id);
                    option.dataset.parent = String(result.category.parent_id);
                    option.dataset.apparel = String(result.category.apparel);
                    subcategorySelect.add(option);
                }
                updateSubcategories();
                subcategorySelect.value = String(result.category.id);
                updateConditionField();
            }

            input.value = '';
            setInlineFeedback(feedback, result.message, true);
            closeInlinePanel(toggle, panel);
        } catch (error) {
            setInlineFeedback(feedback, error.message || 'Não foi possível salvar.', false);
        } finally {
            saveButton.disabled = false;
        }
    }

    function syncInputFiles() {
        const transfer = new DataTransfer();
        selectedImages.forEach(function (entry) {
            transfer.items.add(entry.file);
        });
        imageInput.files = transfer.files;
    }

    function renderImages() {
        previewGrid.replaceChildren();
        imageCount.textContent = `${selectedImages.length} de ${imageLimit}`;
        coverPreview.replaceChildren();

        if (!selectedImages.length) {
            const placeholder = document.createElement('span');
            placeholder.className = 'product-create-cover-placeholder';
            placeholder.innerHTML = '<i class="fa-regular fa-image" aria-hidden="true"></i> A primeira imagem será a imagem principal';
            coverPreview.appendChild(placeholder);
            return;
        }

        const coverImage = document.createElement('img');
        coverImage.src = selectedImages[0].url;
        coverImage.alt = `Imagem de vitrine: ${selectedImages[0].file.name}`;
        coverPreview.appendChild(coverImage);

        selectedImages.forEach(function (entry, index) {
            const card = document.createElement('div');
            card.className = `product-create-image-card${index === 0 ? ' is-cover' : ''}`;

            const image = document.createElement('img');
            image.src = entry.url;
            image.alt = entry.file.name;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.setAttribute('aria-label', `Remover ${entry.file.name}`);
            removeButton.textContent = '\u00d7';
            removeButton.addEventListener('click', function () {
                URL.revokeObjectURL(entry.url);
                selectedImages = selectedImages.filter(function (selected) {
                    return selected !== entry;
                });
                syncInputFiles();
                renderImages();
            });

            const filename = document.createElement('span');
            filename.textContent = entry.file.name;
            card.append(image, removeButton, filename);
            previewGrid.appendChild(card);
        });
    }

    function addImages(files) {
        let feedback = form.querySelector('[data-image-feedback]');
        if (!feedback) {
            feedback = document.createElement('p');
            feedback.className = 'product-create-feedback';
            feedback.dataset.imageFeedback = '';
            feedback.setAttribute('role', 'status');
            feedback.setAttribute('aria-live', 'polite');
            dropzone.insertAdjacentElement('afterend', feedback);
        }

        const issues = [];
        Array.from(files).forEach(function (file) {
            if (!acceptedTypes.includes(file.type)) {
                issues.push(`${file.name}: formato não aceito.`);
                return;
            }

            if (file.size > imageSizeLimit) {
                issues.push(`${file.name}: ultrapassa 5 MB.`);
                return;
            }

            if (selectedImages.length >= imageLimit) {
                issues.push(`O limite é de ${imageLimit} imagens.`);
                return;
            }

            selectedImages.push({ file, url: URL.createObjectURL(file) });
        });

        feedback.textContent = issues.join(' ');
        syncInputFiles();
        renderImages();
    }

    brandToggle.addEventListener('click', function () {
        toggleInlinePanel(brandToggle, brandPanel, brandName);
    });

    brandSave.addEventListener('click', async function () {
        const name = brandName.value.trim();

        if (!name) {
            brandFeedback.textContent = 'Informe o nome da marca.';
            brandFeedback.classList.remove('is-success');
            brandName.focus();
            return;
        }

        brandSave.disabled = true;
        brandFeedback.textContent = 'Salvando marca...';
        brandFeedback.classList.remove('is-success');

        try {
            const response = await fetch(brandPanel.dataset.brandEndpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                },
                body: JSON.stringify({ name }),
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.errors?.name?.[0] || result.message || 'Não foi possível salvar a marca.');
            }

            let option = Array.from(brandSelect.options).find(function (item) {
                return item.value === String(result.brand.id);
            });

            if (!option) {
                option = new Option(result.brand.name, result.brand.id);
                brandSelect.add(option);
            }

            brandSelect.value = String(result.brand.id);
            brandName.value = '';
            brandFeedback.textContent = result.message;
            brandFeedback.hidden = false;
            brandFeedback.classList.add('is-success');
            closeInlinePanel(brandToggle, brandPanel);
        } catch (error) {
            brandFeedback.textContent = error.message || 'Não foi possível salvar a marca.';
            brandFeedback.hidden = false;
            brandFeedback.classList.remove('is-success');
        } finally {
            brandSave.disabled = false;
        }
    });

    categoryToggle.addEventListener('click', function () {
        toggleInlinePanel(categoryToggle, categoryPanel, form.querySelector('[data-inline-name="category"]'));
    });
    subcategoryToggle.addEventListener('click', function () {
        toggleInlinePanel(subcategoryToggle, subcategoryPanel, form.querySelector('[data-inline-name="subcategory"]'));
    });
    form.querySelectorAll('[data-inline-save]').forEach(function (button) {
        button.addEventListener('click', function () {
            saveInlineCategory(button.dataset.inlineSave);
        });
    });

    categorySelect.addEventListener('change', updateSubcategories);
    subcategorySelect.addEventListener('change', updateConditionField);
    updateSubcategories();

    description.addEventListener('input', function () {
        descriptionCount.textContent = String(description.value.length);
    });
    descriptionCount.textContent = String(description.value.length);

    imageInput.addEventListener('change', function () {
        addImages(imageInput.files);
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.add('is-dragging');
        });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.remove('is-dragging');
        });
    });

    dropzone.addEventListener('drop', function (event) {
        addImages(event.dataTransfer.files);
    });

    window.addEventListener('beforeunload', function () {
        selectedImages.forEach(function (entry) {
            URL.revokeObjectURL(entry.url);
        });
    });
});