import {
    addCustomerCartItem,
    customerStorageKey,
    saveRecentCustomerItem,
    readCustomerList,
    toggleCustomerFavorite,
} from './customer-state.js';

document.addEventListener('DOMContentLoaded', function () {
    var productPage = document.querySelector('.product-page[data-product-id]');

    if (productPage) {
        var userId = productPage.dataset.clientId;
        var product = {
            id: productPage.dataset.productId,
            name: productPage.dataset.productName,
            image: productPage.dataset.productImage,
            price: Number(productPage.dataset.productPrice),
            stock: Number(productPage.dataset.productStock),
            condition: productPage.dataset.productCondition || null,
            url: window.location.href,
        };

        saveRecentCustomerItem(customerStorageKey(userId, 'recent'), product);

        var favoriteKey = customerStorageKey(userId, 'favorites');
        var favoriteButton = productPage.querySelector('[data-favorite-toggle]');
        var favoriteFeedback = productPage.querySelector('.favorite-feedback');

        if (favoriteButton) {
            var syncFavoriteButton = function (isFavorite) {
                favoriteButton.setAttribute('aria-pressed', String(isFavorite));
                favoriteButton.setAttribute('aria-label', isFavorite ? 'Remover dos favoritos' : 'Adicionar aos favoritos');
                favoriteButton.title = isFavorite ? 'Remover dos favoritos' : 'Adicionar aos favoritos';
                favoriteButton.querySelector('[aria-hidden="true"]').textContent = isFavorite ? '\u2665' : '\u2661';
            };

            var isFavorite = readCustomerList(favoriteKey).some(function (saved) {
                return String(saved.id) === String(product.id);
            });
            syncFavoriteButton(isFavorite);

            favoriteButton.addEventListener('click', function () {
                var result = toggleCustomerFavorite(favoriteKey, product);
                syncFavoriteButton(result.isFavorite);

                if (favoriteFeedback) {
                    favoriteFeedback.textContent = result.isFavorite ? 'Salvo nos favoritos.' : 'Removido dos favoritos.';
                }
            });
        }

        var addToCartButton = productPage.querySelector('[data-cart-add]');
        var cartDrawer = document.querySelector('#cart-added-drawer');

        if (addToCartButton) {
            var originalButtonText = addToCartButton.textContent;
            addToCartButton.setAttribute('aria-live', 'polite');

            addToCartButton.addEventListener('click', function () {
                var added = addCustomerCartItem(customerStorageKey(userId, 'cart'), product);
                addToCartButton.textContent = added ? 'Adicionado ao carrinho' : 'Limite de estoque atingido';

                if (added && cartDrawer) {
                    var cartItem = readCustomerList(customerStorageKey(userId, 'cart')).find(function (item) {
                        return String(item.id) === String(product.id);
                    });

                    cartDrawer.querySelector('[data-drawer-quantity]').textContent = String(cartItem.quantity);
                    cartDrawer.querySelector('[data-drawer-total]').textContent = new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    }).format(product.price * cartItem.quantity);
                    cartDrawer.hidden = false;
                    window.requestAnimationFrame(function () {
                        cartDrawer.classList.add('is-open');
                    });
                    document.body.classList.add('cart-drawer-open');
                    cartDrawer.querySelector('.cart-drawer-close').focus();
                }

                window.setTimeout(function () {
                    addToCartButton.textContent = originalButtonText;
                }, 1800);
            });
        }

        var buyNowButton = productPage.querySelector('[data-cart-buy-now]');
        if (buyNowButton) {
            buyNowButton.addEventListener('click', function () {
                var added = addCustomerCartItem(customerStorageKey(userId, 'cart'), product);
                if (added) {
                    window.location.assign('/carrinho');
                    return;
                }

                buyNowButton.textContent = product.stock < 1 ? 'Sem estoque' : 'Limite de estoque atingido';
                window.setTimeout(function () {
                    buyNowButton.textContent = 'Comprar agora';
                }, 1800);
            });
        }

        if (cartDrawer) {
            var drawerCloseTimer;
            var drawerTrigger;

            var closeCartDrawer = function () {
                if (cartDrawer.hidden) return;

                cartDrawer.classList.remove('is-open');
                document.body.classList.remove('cart-drawer-open');
                window.clearTimeout(drawerCloseTimer);
                drawerCloseTimer = window.setTimeout(function () {
                    cartDrawer.hidden = true;
                    if (drawerTrigger) drawerTrigger.focus();
                }, 240);
            };

            cartDrawer.querySelectorAll('[data-cart-drawer-close]').forEach(function (button) {
                button.addEventListener('click', closeCartDrawer);
            });

            if (addToCartButton) {
                addToCartButton.addEventListener('click', function () {
                    if (!cartDrawer.hidden) drawerTrigger = addToCartButton;
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !cartDrawer.hidden) {
                    closeCartDrawer();
                }
            });
        }
    }

    var mainImage = document.querySelector('#product-main-image');
    var shareTrigger = document.querySelector('.product-share-trigger');
    var shareModal = document.querySelector('#share-product-modal');
    var shareCloseButtons = document.querySelectorAll('[data-share-close]');
    var copyLinkButton = document.querySelector('[data-share-copy]');
    var copyFeedback = document.querySelector('.share-copy-feedback');
    var gallery = document.querySelector('.product-hero-image');
    var thumbnails = Array.from(document.querySelectorAll('.product-thumbnail'));
    var activeImageIndex = Math.max(0, thumbnails.findIndex(function (thumbnail) {
        return thumbnail.classList.contains('active');
    }));

    var showProductImage = function (index) {
        if (!mainImage || !thumbnails.length) {
            return;
        }

        activeImageIndex = (index + thumbnails.length) % thumbnails.length;
        mainImage.src = thumbnails[activeImageIndex].dataset.image;
        thumbnails.forEach(function (thumbnail, thumbnailIndex) {
            var isActive = thumbnailIndex === activeImageIndex;
            thumbnail.classList.toggle('active', isActive);
            thumbnail.setAttribute('aria-current', String(isActive));
        });
    };

    thumbnails.forEach(function (thumbnail, index) {
        thumbnail.addEventListener('click', function () {
            showProductImage(index);
        });
    });

    var previousImageButton = document.querySelector('.product-gallery-previous');
    var nextImageButton = document.querySelector('.product-gallery-next');

    if (previousImageButton) {
        previousImageButton.addEventListener('click', function () {
            showProductImage(activeImageIndex - 1);
        });
    }

    if (nextImageButton) {
        nextImageButton.addEventListener('click', function () {
            showProductImage(activeImageIndex + 1);
        });
    }

    if (gallery && thumbnails.length > 1 && window.matchMedia('(max-width: 700px)').matches) {
        var swipeStart = null;

        gallery.addEventListener('pointerdown', function (event) {
            if (event.pointerType === 'mouse' || !event.isPrimary) {
                return;
            }

            swipeStart = { x: event.clientX, y: event.clientY };
        });

        gallery.addEventListener('pointerup', function (event) {
            if (!swipeStart) {
                return;
            }

            var deltaX = event.clientX - swipeStart.x;
            var deltaY = event.clientY - swipeStart.y;
            swipeStart = null;

            if (Math.abs(deltaX) < 45 || Math.abs(deltaX) <= Math.abs(deltaY)) {
                return;
            }

            showProductImage(activeImageIndex + (deltaX < 0 ? 1 : -1));
        });

        gallery.addEventListener('pointercancel', function () {
            swipeStart = null;
        });
    }

    if (shareTrigger && shareModal) {
        shareTrigger.addEventListener('click', function () {
            shareModal.hidden = false;
            document.body.classList.add('share-modal-open');
        });

        shareCloseButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                shareModal.hidden = true;
                document.body.classList.remove('share-modal-open');
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !shareModal.hidden) {
                shareModal.hidden = true;
                document.body.classList.remove('share-modal-open');
            }
        });
    }

    if (copyLinkButton && copyFeedback) {
        copyLinkButton.addEventListener('click', function () {
            navigator.clipboard.writeText(window.location.href).then(function () {
                copyFeedback.textContent = 'Link copiado.';
            });
        });
    }
});