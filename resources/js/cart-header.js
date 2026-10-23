import { customerStorageKey, readCustomerList } from './customer-state.js';

const owner = document.body.dataset.cartOwner || 'guest';
const cartKey = customerStorageKey(owner, 'cart');

function updateCartCount() {
    const count = readCustomerList(cartKey).reduce((total, item) => {
        return total + Number(item.quantity || 1);
    }, 0);

    document.querySelectorAll('[data-cart-count]').forEach((badge) => {
        badge.textContent = String(count);
        badge.closest('a')?.setAttribute('aria-label', `Carrinho com ${count} ${count === 1 ? 'item' : 'itens'}`);
    });
}

updateCartCount();
window.addEventListener('ldm:cart-updated', updateCartCount);
window.addEventListener('storage', (event) => {
    if (event.key === cartKey) updateCartCount();
});