import {
    customerStorageKey,
    readCustomerList,
    writeCustomerList,
} from './customer-state.js';

document.addEventListener('DOMContentLoaded', function () {
    const clientPage = document.querySelector('.client-page');

    if (clientPage) {
        const userId = clientPage.dataset.clientId;
        const searchesKey = customerStorageKey(userId, 'searches');
        const cartKey = customerStorageKey(userId, 'cart');
        const recentKey = customerStorageKey(userId, 'recent');
        const favoritesKey = customerStorageKey(userId, 'favorites');
        const params = new URLSearchParams(window.location.search);
        const searchTerm = (params.get('search') || '').trim();

        if (searchTerm) {
            const products = Array.from(document.querySelectorAll('.search-product-card')).map((card) => {
                const link = card.querySelector('.search-product-link');
                const image = card.querySelector('.search-product-image img');

                return {
                    id: card.dataset.productId || link?.href.split('/').pop(),
                    name: card.querySelector('.search-product-name')?.textContent.trim() || '',
                    image: image?.currentSrc || image?.src || '',
                    url: link?.href || '',
                };
            }).filter((product) => product.id && product.name);

            const searches = readCustomerList(searchesKey);
            const normalizedTerm = searchTerm.toLocaleLowerCase();
            const updatedSearches = [
                { term: searchTerm, products, searchedAt: Date.now() },
                ...searches.filter((search) => search.term.toLocaleLowerCase() !== normalizedTerm),
            ];

            writeCustomerList(searchesKey, updatedSearches.slice(0, 8));
        }

        const searches = readCustomerList(searchesKey);
        const cart = readCustomerList(cartKey);
        const recent = readCustomerList(recentKey);
        let favorites = readCustomerList(favoritesKey);
        const searchProducts = [...new Map(
            searches.flatMap((search) => search.products || []).map((product) => [String(product.id), product]),
        ).values()];
        const cartQuantity = cart.reduce((total, item) => total + Number(item.quantity || 1), 0);

        const setCount = (name, value) => {
            const element = document.querySelector(`[data-shortcut-value="${name}"]`);
            if (element) element.textContent = value;
        };

        const setNote = (name, value) => {
            const element = document.querySelector(`[data-shortcut-note="${name}"]`);
            if (element) element.textContent = value;
        };

        const renderPreviews = (name, items) => {
            const container = document.querySelector(`[data-shortcut-preview="${name}"]`);
            if (!container) return;

            if (!container.dataset.fallbackText) {
                container.dataset.fallbackText = container.textContent;
            }

            const previewItems = items.filter((item) => item.image).slice(0, 4);
            container.textContent = '';

            if (!previewItems.length) {
                container.classList.remove('has-images');
                container.textContent = container.dataset.fallbackText;
                return;
            }

            container.classList.add('has-images');

            previewItems.forEach((item) => {
                const image = document.createElement('img');
                image.src = item.image;
                image.alt = item.name;
                image.loading = 'lazy';
                container.appendChild(image);
            });
        };

        const favoritesSection = document.querySelector('[data-favorites-section]');
        const favoritesList = document.querySelector('[data-favorites-list]');

        const renderFavorites = () => {
            favorites = readCustomerList(favoritesKey);
            setCount('favorites', `${favorites.length} ${favorites.length === 1 ? 'produto' : 'produtos'}`);
            setNote('favorites', favorites[0]?.name || 'Nenhum favorito salvo');
            renderPreviews('favorites', favorites);

            if (!favoritesSection || !favoritesList) return;

            favoritesSection.hidden = favorites.length === 0;
            favoritesList.replaceChildren();

            favorites.forEach((favorite) => {
                const card = document.createElement('article');
                card.className = 'client-favorite-card';

                const link = document.createElement('a');
                link.className = 'client-favorite-link';
                link.href = favorite.url || '#';

                const image = document.createElement('img');
                image.src = favorite.image || '';
                image.alt = favorite.name;
                image.loading = 'lazy';
                link.appendChild(image);

                const name = document.createElement('span');
                name.className = 'client-favorite-name';
                name.textContent = favorite.name;
                link.appendChild(name);

                if (Number.isFinite(Number(favorite.price))) {
                    const price = document.createElement('span');
                    price.className = 'client-favorite-price';
                    price.textContent = new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    }).format(Number(favorite.price));
                    link.appendChild(price);
                }

                const removeButton = document.createElement('button');
                removeButton.className = 'client-favorite-remove';
                removeButton.type = 'button';
                removeButton.textContent = 'Remover dos favoritos';
                removeButton.setAttribute('aria-label', `Remover ${favorite.name} dos favoritos`);
                removeButton.addEventListener('click', () => {
                    writeCustomerList(favoritesKey, readCustomerList(favoritesKey)
                        .filter((saved) => String(saved.id) !== String(favorite.id)));
                    renderFavorites();
                });

                card.append(link, removeButton);
                favoritesList.appendChild(card);
            });
        };

        setCount('searches', `${searches.length} ${searches.length === 1 ? 'busca' : 'buscas'}`);
        setCount('cart', `${cartQuantity} ${cartQuantity === 1 ? 'item' : 'itens'}`);
        setCount('recent', `${recent.length} ${recent.length === 1 ? 'produto' : 'produtos'}`);
        setNote('searches', searches[0] ? `Última: ${searches[0].term}` : 'Nenhuma pesquisa recente');
        setNote('cart', cart[0]?.name || 'Seu carrinho está vazio');
        setNote('recent', recent[0]?.name || 'Nenhum produto visto');
        renderPreviews('searches', searchProducts);
        renderPreviews('cart', cart);
        renderPreviews('recent', recent);
        renderFavorites();

        const searchShortcut = document.querySelector('[data-shortcut="searches"]');
        if (searchShortcut && searches[0]) {
            const target = new URL(searchShortcut.href, window.location.origin);
            target.search = '';
            target.searchParams.set('search', searches[0].term);
            searchShortcut.href = target.toString();
        }

        const recentShortcut = document.querySelector('[data-shortcut="recent"]');
        if (recentShortcut && recent[0]?.url) recentShortcut.href = recent[0].url;
    }

    const shortcuts = document.querySelector('.client-shortcuts');
    const previousButton = document.querySelector('.client-shortcuts-prev');
    const nextButton = document.querySelector('.client-shortcuts-next');

    if (!shortcuts || !previousButton || !nextButton) {
        return;
    }

    const scrollDistance = function () {
        return shortcuts.clientWidth;
    };

    previousButton.addEventListener('click', function () {
        shortcuts.scrollBy({ left: -scrollDistance(), behavior: 'smooth' });
    });

    nextButton.addEventListener('click', function () {
        shortcuts.scrollBy({ left: scrollDistance(), behavior: 'smooth' });
    });

    const updateButtons = function () {
        const maxScroll = shortcuts.scrollWidth - shortcuts.clientWidth - 1;

        previousButton.hidden = shortcuts.scrollLeft <= 1;
        nextButton.hidden = shortcuts.scrollLeft >= maxScroll;
    };

    shortcuts.addEventListener('scroll', updateButtons, { passive: true });
    window.addEventListener('resize', updateButtons);
    updateButtons();
});
