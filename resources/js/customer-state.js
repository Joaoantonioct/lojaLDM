export function customerStorageKey(userId, section) {
    return `ldm:${userId || 'guest'}:${section}`;
}

export function readCustomerList(key) {
    try {
        const value = JSON.parse(localStorage.getItem(key) || '[]');
        return Array.isArray(value) ? value : [];
    } catch {
        return [];
    }
}

export function writeCustomerList(key, items) {
    localStorage.setItem(key, JSON.stringify(items));

    if (key.endsWith(':cart')) {
        window.dispatchEvent(new CustomEvent('ldm:cart-updated'));
    }
}

export function toggleCustomerFavorite(key, item) {
    const items = readCustomerList(key);
    const index = items.findIndex((saved) => String(saved.id) === String(item.id));

    if (index >= 0) {
        items.splice(index, 1);
        writeCustomerList(key, items);
        return { isFavorite: false, items };
    }

    items.unshift(item);
    writeCustomerList(key, items);
    return { isFavorite: true, items };
}

export function saveRecentCustomerItem(key, item, limit = 8) {
    const items = readCustomerList(key);
    const updated = [item, ...items.filter((saved) => String(saved.id) !== String(item.id))];
    writeCustomerList(key, updated.slice(0, limit));
}

export function addCustomerCartItem(key, item) {
    const items = readCustomerList(key);
    const existing = items.find((saved) => String(saved.id) === String(item.id));
    const stock = Number.isFinite(Number(item.stock)) ? Number(item.stock) : 99;

    if (stock < 1) {
        return false;
    }

    if (existing) {
        existing.stock = stock;
        const quantity = Number(existing.quantity || 1);

        if (quantity >= stock) {
            return false;
        }

        existing.quantity = quantity + 1;
    } else {
        items.unshift({ ...item, stock, quantity: 1 });
    }

    writeCustomerList(key, items.slice(0, 20));
    return true;
}