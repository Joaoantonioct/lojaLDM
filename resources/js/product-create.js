document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-product-create-form]');
    if (!form) return;

    function normalizeDisplayName(value) {
        return String(value || '')
            .normalize('NFC')
            .trim()
            .replace(/\s+/gu, ' ')
            .toLocaleLowerCase('pt-BR')
            .replace(/(^|[\s'’\-])(\p{L})/gu, function (_, separator, letter) {
                return separator + letter.toLocaleUpperCase('pt-BR');
            });
    }

    const categorySelect = form.querySelector('#category_id');
    const sizeTypeSelect = form.querySelector('#size_type');
    const subcategorySelect = form.querySelector('#subcategory_id');
    const subcategoryRequiredMark = form.querySelector('[data-subcategory-required]');
    const subcategoryHint = form.querySelector('[data-subcategory-hint]');
    const conditionField = form.querySelector('[data-condition-field]');
    const conditionSelect = form.querySelector('#condition');
    const sizeField = form.querySelector('[data-size-field]');
    const sizeOptions = form.querySelector('[data-size-options]');
    const colorOptions = form.querySelector('[data-color-options]');
    const addColorButton = form.querySelector('[data-add-color]');
    const totalQuantity = form.querySelector('[data-total-qty]');
    const totalQuantityHint = form.querySelector('[data-total-qty-hint]');
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
    let restoredVariants = [];
    let colorRows = [];
    try {
        restoredVariants = JSON.parse(sizeOptions.dataset.selectedVariants || '[]');
    } catch (error) {
        restoredVariants = [];
    }

    const sizeCatalog = {
        footwear: Array.from({ length: 14 }, (_, index) => String(index + 33)),
        trousers: Array.from({ length: 14 }, (_, index) => String(index * 2 + 34)),
        clothing: ['PP', 'P', 'M', 'G', 'GG', 'XGG'],
    };

    [form.querySelector('#name'), brandName, ...form.querySelectorAll('[data-inline-name]')]
        .filter(Boolean)
        .forEach(function (input) {
            input.addEventListener('blur', function () {
                input.value = normalizeDisplayName(input.value);
            });
        });

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
        subcategorySelect.required = hasOptions;
        subcategoryRequiredMark.hidden = !hasOptions;
        subcategoryToggle.disabled = !categoryId;
        const showSubcategoryCreator = Boolean(categoryId) && !hasOptions;
        subcategoryPanel.hidden = !showSubcategoryCreator;
        subcategoryToggle.setAttribute('aria-expanded', String(showSubcategoryCreator));

        if (!hasOptions || subcategorySelect.selectedOptions[0]?.dataset.parent !== categoryId) {
            subcategorySelect.value = '';
        }

        subcategoryHint.textContent = !categoryId
            ? 'Escolha uma categoria para ver as opções disponíveis.'
            : hasOptions
                ? 'Selecione uma subcategoria, se aplicável.'
                : 'Esta categoria ainda não tem subcategorias. Cadastre uma pelo campo abaixo.';

        updateConditionField();
    }

    function updateConditionField() {
        const sizeType = sizeTypeSelect.value;
        const hasSizes = ['clothing', 'trousers', 'footwear'].includes(sizeType);

        conditionField.hidden = !hasSizes;
        conditionSelect.disabled = !hasSizes;
        conditionSelect.required = hasSizes;

        if (!hasSizes) {
            conditionSelect.value = '';
        } else if (!conditionSelect.value) {
            conditionSelect.value = 'novo';
        }

        updateSizeOptions(hasSizes ? sizeType : null);
    }

    function updateSizeOptions(sizeType) {
        const sizes = sizeCatalog[sizeType] || [];
        const existing = new Map(Array.from(sizeOptions.querySelectorAll('[data-size-check]:checked'))
            .map((checkbox) => [checkbox.value, Number(checkbox.closest('[data-size-row]').querySelector('[data-size-qty]').value || 0)]));

        if (sizeOptions.childElementCount === 0 && restoredVariants.length) {
            restoredVariants.forEach((variant) => existing.set(String(variant.size), Number(variant.qty || 0)));
        }

        sizeField.hidden = sizes.length === 0 || colorRows.length > 0;
        totalQuantity.readOnly = sizes.length > 0 || colorRows.length > 0;
        totalQuantity.required = sizes.length === 0 && colorRows.length === 0;
        totalQuantityHint.hidden = sizes.length === 0 && colorRows.length === 0;
        sizeOptions.replaceChildren();

        sizes.forEach((size, index) => {
            const row = document.createElement('label');
            row.className = 'product-create-size-option';
            row.dataset.sizeRow = '';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = size;
            checkbox.dataset.sizeCheck = '';
            checkbox.name = `variants[${index}][size]`;
            checkbox.checked = existing.has(size);

            const label = document.createElement('span');
            label.textContent = size;

            const quantity = document.createElement('input');
            quantity.type = 'number';
            quantity.min = '0';
            quantity.step = '1';
            quantity.inputMode = 'numeric';
            quantity.placeholder = 'Qtd.';
            quantity.value = checkbox.checked ? String(existing.get(size)) : '0';
            quantity.dataset.sizeQty = '';
            quantity.name = `variants[${index}][qty]`;
            quantity.disabled = !checkbox.checked;
            quantity.required = checkbox.checked;
            quantity.setAttribute('aria-label', `Estoque do tamanho ${size}`);

            row.append(checkbox, label, quantity);
            sizeOptions.append(row);
        });

        updateTotalQuantity();
        sizeTypeSelect.setCustomValidity(sizes.length && !existing.size && colorRows.length === 0 ? 'Selecione pelo menos um tamanho.' : '');
    }

    function updateTotalQuantity() {
        if (colorRows.length) {
            totalQuantity.value = colorRows.reduce(function (total, color) {
                return total + Object.values(color.quantities).reduce((sum, quantity) => sum + Number(quantity || 0), 0);
            }, 0);
            totalQuantity.readOnly = true;
            totalQuantity.required = false;
            totalQuantityHint.hidden = false;
            sizeField.hidden = true;
            validateColorRows();
            return;
        }

        const checks = Array.from(sizeOptions.querySelectorAll('[data-size-check]'));
        const selected = checks.filter((checkbox) => checkbox.checked);
        selected.forEach((checkbox) => {
            const quantity = checkbox.closest('[data-size-row]').querySelector('[data-size-qty]');
            quantity.disabled = !checkbox.checked;
            quantity.required = checkbox.checked;
        });
        totalQuantity.value = selected.reduce((total, checkbox) => {
            return total + Number(checkbox.closest('[data-size-row]').querySelector('[data-size-qty]').value || 0);
        }, 0);
        sizeTypeSelect.setCustomValidity(checks.length && !selected.length ? 'Selecione pelo menos um tamanho.' : '');
    }

    function validateColorRows() {
        const sizes = sizeCatalog[sizeTypeSelect.value] || [];
        const colorNames = colorRows.map((color) => normalizeDisplayName(color.name)).filter(Boolean);
        const duplicateNames = new Set(colorNames.filter((name, index) => colorNames.indexOf(name) !== index));
        colorRows.forEach(function (color, index) {
            const nameInput = colorOptions.querySelector(`[data-color-name="${index}"]`);
            if (!nameInput) return;
            const missingSize = sizes.length && !Object.keys(color.quantities).length;
            nameInput.setCustomValidity(
                !color.name.trim() ? 'Informe o nome desta cor.'
                    : !color.imageFile ? 'Adicione uma imagem para esta cor.'
                        : duplicateNames.has(normalizeDisplayName(color.name)) ? 'Cada cor deve ter um nome diferente.'
                            : missingSize ? 'Selecione pelo menos um tamanho para esta cor.' : '',
            );
        });
        sizeTypeSelect.setCustomValidity('');
    }

    function renderColorRows() {
        colorOptions.replaceChildren();
        const sizes = sizeCatalog[sizeTypeSelect.value] || [];
        colorRows.forEach(function (color, colorIndex) {
            const card = document.createElement('section');
            card.className = 'product-create-color-card';
            const heading = document.createElement('div');
            heading.className = 'product-create-color-card-heading';
            const nameLabel = document.createElement('label');
            nameLabel.textContent = `Cor ${colorIndex + 1}`;
            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.maxLength = 50;
            nameInput.required = true;
            nameInput.value = color.name;
            nameInput.name = `colors[${colorIndex}][name]`;
            nameInput.dataset.colorName = String(colorIndex);
            nameLabel.append(nameInput);
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'product-create-add-brand';
            removeButton.dataset.removeColor = String(colorIndex);
            removeButton.textContent = 'Remover cor';
            heading.append(nameLabel, removeButton);

            const imageLabel = document.createElement('label');
            imageLabel.className = 'product-create-color-image';
            imageLabel.textContent = 'Imagem desta cor *';
            const imageInput = document.createElement('input');
            imageInput.type = 'file';
            imageInput.accept = 'image/jpeg,image/png,image/webp';
            imageInput.required = !color.imageFile;
            imageInput.dataset.colorImage = String(colorIndex);
            imageInput.name = `colors[${colorIndex}][image]`;
            imageLabel.append(imageInput);
            if (color.imageUrl) {
                const preview = document.createElement('img');
                preview.src = color.imageUrl;
                preview.alt = `Prévia da cor ${color.name || colorIndex + 1}`;
                preview.className = 'product-create-color-preview';
                imageLabel.append(preview);
            }

            const stockContainer = document.createElement('div');
            stockContainer.className = 'product-create-color-stock';
            if (sizes.length) {
                const stockHeading = document.createElement('strong');
                stockHeading.textContent = 'Tamanhos e estoque desta cor';
                const grid = document.createElement('div');
                grid.className = 'product-create-size-grid';
                sizes.forEach(function (size, sizeIndex) {
                    const row = document.createElement('label');
                    row.className = 'product-create-size-option';
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.checked = Object.prototype.hasOwnProperty.call(color.quantities, size);
                    checkbox.dataset.colorSize = `${colorIndex}:${size}`;
                    const label = document.createElement('span');
                    label.textContent = size;
                    const quantity = document.createElement('input');
                    quantity.type = 'number';
                    quantity.min = '0';
                    quantity.step = '1';
                    quantity.placeholder = 'Qtd.';
                    quantity.value = checkbox.checked ? String(color.quantities[size]) : '0';
                    quantity.disabled = !checkbox.checked;
                    quantity.required = checkbox.checked;
                    quantity.dataset.colorQuantity = `${colorIndex}:${size}`;
                    row.append(checkbox, label, quantity);
                    grid.append(row);

                    const variantIndex = colorIndex * Math.max(sizes.length, 1) + sizeIndex;
                    const colorIndexInput = document.createElement('input');
                    colorIndexInput.type = 'hidden';
                    colorIndexInput.name = `variants[${variantIndex}][color_index]`;
                    colorIndexInput.value = String(colorIndex);
                    colorIndexInput.disabled = !checkbox.checked;
                    const sizeInput = document.createElement('input');
                    sizeInput.type = 'hidden';
                    sizeInput.name = `variants[${variantIndex}][size]`;
                    sizeInput.value = size;
                    sizeInput.disabled = !checkbox.checked;
                    const quantityInput = document.createElement('input');
                    quantityInput.type = 'hidden';
                    quantityInput.name = `variants[${variantIndex}][qty]`;
                    quantityInput.value = quantity.value;
                    quantityInput.disabled = !checkbox.checked;
                    row.append(colorIndexInput, sizeInput, quantityInput);
                });
                stockContainer.append(stockHeading, grid);
            } else {
                const quantityLabel = document.createElement('label');
                quantityLabel.textContent = 'Estoque desta cor *';
                const quantityInput = document.createElement('input');
                quantityInput.type = 'number';
                quantityInput.min = '0';
                quantityInput.step = '1';
                quantityInput.required = true;
                quantityInput.value = String(color.quantities[''] || 0);
                quantityInput.dataset.colorQuantity = `${colorIndex}:`;
                quantityLabel.append(quantityInput);
                const colorIndexInput = document.createElement('input');
                colorIndexInput.type = 'hidden';
                colorIndexInput.name = `variants[${colorIndex}][color_index]`;
                colorIndexInput.value = String(colorIndex);
                const sizeInput = document.createElement('input');
                sizeInput.type = 'hidden';
                sizeInput.name = `variants[${colorIndex}][size]`;
                sizeInput.value = '';
                const variantQuantity = document.createElement('input');
                variantQuantity.type = 'hidden';
                variantQuantity.name = `variants[${colorIndex}][qty]`;
                variantQuantity.value = quantityInput.value;
                quantityLabel.append(colorIndexInput, sizeInput, variantQuantity);
                stockContainer.append(quantityLabel);
            }
            card.append(heading, imageLabel, stockContainer);
            colorOptions.append(card);

            if (color.imageFile) {
                const transfer = new DataTransfer();
                transfer.items.add(color.imageFile);
                imageInput.files = transfer.files;
            }
        });
        sizeField.hidden = (sizeCatalog[sizeTypeSelect.value] || []).length === 0 || colorRows.length > 0;
        validateColorRows();
        updateTotalQuantity();
    }

    function updateColorInput(index, key, value) {
        const color = colorRows[index];
        if (!color) return;
        if (key === 'name') color.name = value;
        if (key === 'image') {
            if (color.imageUrl) URL.revokeObjectURL(color.imageUrl);
            color.imageFile = value;
            color.imageUrl = value ? URL.createObjectURL(value) : '';
            renderColorRows();
            return;
        }
        if (key === 'quantity') {
            const size = value.key.slice(value.key.indexOf(':') + 1);
            color.quantities[size] = value.quantity;
            const row = colorOptions.querySelector(`[data-color-size="${index}:${size}"]`);
            const variantInputs = row?.closest('label')?.querySelectorAll('input[type="hidden"]') || [];
            if (variantInputs[2]) variantInputs[2].value = String(value.quantity);
            const variantQty = colorOptions.querySelector(`input[name="variants[${index}][qty]"]`);
            if (variantQty && size === '') variantQty.value = String(value.quantity);
            updateTotalQuantity();
            return;
        }
        validateColorRows();
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
        const name = normalizeDisplayName(input.value);
        input.value = name;
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
                    option.dataset.sizeType = result.category.sizeType || '';
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
                    option.dataset.sizeType = result.category.sizeType || '';
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
        const name = normalizeDisplayName(brandName.value);
        brandName.value = name;

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
    sizeTypeSelect.addEventListener('change', function () {
        restoredVariants = [];
        sizeOptions.replaceChildren();
        updateConditionField();
        renderColorRows();
    });
    sizeOptions.addEventListener('change', function (event) {
        if (event.target.matches('[data-size-check]')) updateTotalQuantity();
    });
    sizeOptions.addEventListener('input', function (event) {
        if (event.target.matches('[data-size-qty]')) updateTotalQuantity();
    });
    addColorButton.addEventListener('click', function () {
        if (colorRows.length >= 10) {
            window.alert('Cadastre até 10 cores por produto.');
            return;
        }
        colorRows.push({ name: '', imageFile: null, imageUrl: '', quantities: {} });
        renderColorRows();
        colorOptions.querySelector(`[data-color-name="${colorRows.length - 1}"]`)?.focus();
    });
    colorOptions.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-color]');
        if (!button) return;
        const color = colorRows[Number(button.dataset.removeColor)];
        if (color?.imageUrl) URL.revokeObjectURL(color.imageUrl);
        colorRows.splice(Number(button.dataset.removeColor), 1);
        renderColorRows();
        updateSizeOptions(sizeTypeSelect.value);
    });
    colorOptions.addEventListener('change', function (event) {
        const target = event.target;
        if (target.matches('[data-color-image]')) {
            updateColorInput(Number(target.dataset.colorImage), 'image', target.files[0] || null);
            return;
        }
        if (target.matches('[data-color-size]')) {
            const [index, ...sizeParts] = target.dataset.colorSize.split(':');
            const size = sizeParts.join(':');
            const color = colorRows[Number(index)];
            if (target.checked) {
                const quantityInput = target.closest('label').querySelector('[data-color-quantity]');
                color.quantities[size] = quantityInput?.value || '0';
            } else {
                delete color.quantities[size];
            }
            renderColorRows();
        }
    });
    colorOptions.addEventListener('input', function (event) {
        const target = event.target;
        if (target.matches('[data-color-name]')) {
            updateColorInput(Number(target.dataset.colorName), 'name', target.value);
            return;
        }
        if (target.matches('[data-color-quantity]')) {
            updateColorInput(Number(target.dataset.colorQuantity.split(':')[0]), 'quantity', {
                key: target.dataset.colorQuantity,
                quantity: target.value,
            });
        }
    });
    updateSubcategories();

    description.addEventListener('input', function () {
        descriptionCount.textContent = String(description.value.length);
    });
    descriptionCount.textContent = String(description.value.length);

    imageInput.addEventListener('change', function () {
        addImages(imageInput.files);
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!window.confirm('Tem certeza de que está tudo certo com os dados do produto? Confirme para concluir o cadastro.')) {
            return;
        }
        HTMLFormElement.prototype.submit.call(form);
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