import {
    customerStorageKey,
    readCustomerList,
    writeCustomerList,
} from './customer-state.js';

const page = document.querySelector('[data-cart-page]');

if (page) {
    const cartKey = customerStorageKey(page.dataset.clientId, 'cart');
    const itemsContainer = page.querySelector('[data-cart-items]');
    const emptyState = page.querySelector('[data-cart-empty]');
    const footer = page.querySelector('[data-cart-footer]');
    const checkoutButton = page.querySelector('[data-cart-checkout]');
    const feedback = page.querySelector('[data-cart-feedback]');
    const money = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

    function renderCart() {
        const items = readCustomerList(cartKey);
        const itemCount = items.reduce((total, item) => total + Number(item.quantity || 1), 0);
        const subtotal = items.reduce((total, item) => total + Number(item.price || 0) * Number(item.quantity || 1), 0);

        itemsContainer.replaceChildren();
        items.forEach((item) => {
            const row = document.createElement('article');
            row.className = 'cart-product';
            row.dataset.productId = String(item.id);

            const image = document.createElement('img');
            image.className = 'cart-product-image';
            image.src = item.image || '';
            image.alt = item.name || 'Produto';
            image.loading = 'lazy';

            const details = document.createElement('div');
            details.className = 'cart-product-details';

            const name = document.createElement('a');
            name.className = 'cart-product-name';
            name.href = item.url || '#';
            name.textContent = item.name || 'Produto';

            const price = document.createElement('span');
            price.className = 'cart-product-price';
            price.textContent = money.format(Number(item.price || 0));
            details.append(name, price);

            const controls = document.createElement('div');
            controls.className = 'cart-product-controls';

            const quantity = document.createElement('div');
            quantity.className = 'cart-quantity';
            quantity.setAttribute('aria-label', 'Quantidade');

            const decrease = document.createElement('button');
            decrease.type = 'button';
            decrease.dataset.cartAction = 'decrease';
            decrease.setAttribute('aria-label', `Diminuir quantidade de ${item.name}`);
            decrease.textContent = '\u2212';

            const quantityValue = document.createElement('span');
            quantityValue.textContent = String(item.quantity || 1);

            const increase = document.createElement('button');
            increase.type = 'button';
            increase.dataset.cartAction = 'increase';
            increase.setAttribute('aria-label', `Aumentar quantidade de ${item.name}`);
            increase.textContent = '+';
            increase.disabled = Number(item.quantity || 1) >= Number(item.stock ?? 0);
            quantity.append(decrease, quantityValue, increase);

            const lineTotal = document.createElement('strong');
            lineTotal.className = 'cart-product-total';
            lineTotal.textContent = money.format(Number(item.price || 0) * Number(item.quantity || 1));

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'cart-remove';
            remove.dataset.cartAction = 'remove';
            remove.setAttribute('aria-label', `Remover ${item.name} do carrinho`);
            remove.textContent = 'Remover';

            controls.append(quantity, lineTotal, remove);
            row.append(image, details, controls);
            itemsContainer.appendChild(row);
        });

        page.querySelector('[data-cart-item-count]').textContent = `${itemCount} ${itemCount === 1 ? 'item' : 'itens'}`;
        page.querySelector('[data-cart-summary-count]').textContent = String(itemCount);
        page.querySelector('[data-cart-subtotal]').textContent = money.format(subtotal);
        page.querySelector('[data-cart-total]').textContent = money.format(subtotal);
        emptyState.hidden = items.length > 0;
        itemsContainer.hidden = items.length === 0;
        footer.hidden = items.length === 0;
        if (checkoutButton) checkoutButton.disabled = items.length === 0 || checkoutButton.dataset.submitting === 'true';
    }

    itemsContainer.addEventListener('click', (event) => {
        const button = event.target.closest('[data-cart-action]');
        const row = button?.closest('[data-product-id]');
        if (!button || !row) return;

        const items = readCustomerList(cartKey);
        const item = items.find((entry) => String(entry.id) === row.dataset.productId);
        if (!item) return;

        if (button.dataset.cartAction === 'remove') {
            writeCustomerList(cartKey, items.filter((entry) => String(entry.id) !== row.dataset.productId));
            return;
        }

        const quantity = Number(item.quantity || 1);
        if (button.dataset.cartAction === 'increase') {
            item.quantity = Math.min(Number(item.stock ?? 0), quantity + 1);
        } else if (button.dataset.cartAction === 'decrease') {
            item.quantity = Math.max(1, quantity - 1);
        }

        writeCustomerList(cartKey, items);
    });

    page.querySelector('[data-cart-clear]').addEventListener('click', () => writeCustomerList(cartKey, []));
    checkoutButton?.addEventListener('click', async () => {
        const items = readCustomerList(cartKey);
        if (!items.length) return;

        checkoutButton.dataset.submitting = 'true';
        checkoutButton.disabled = true;
        feedback.hidden = true;
        feedback.textContent = '';

        try {
            const response = await fetch(checkoutButton.dataset.checkoutUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': checkoutButton.dataset.csrfToken,
                },
                body: JSON.stringify({
                    items: items.map((item) => ({
                        productId: Number(item.id),
                        quantity: Number(item.quantity || 1),
                    })),
                }),
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.errors?.items?.[0] || result.message || 'Não foi possível registrar o pedido.');
            }

            writeCustomerList(cartKey, []);
            window.location.assign(result.redirectUrl);
        } catch (error) {
            feedback.textContent = error.message || 'Não foi possível registrar o pedido.';
            feedback.hidden = false;
        } finally {
            checkoutButton.dataset.submitting = 'false';
            renderCart();
        }
    });
    window.addEventListener('ldm:cart-updated', renderCart);
    window.addEventListener('storage', (event) => {
        if (event.key === cartKey) renderCart();
    });
    renderCart();
}