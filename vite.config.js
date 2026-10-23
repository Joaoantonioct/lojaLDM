import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/style.css',
                'resources/css/app.css',
                'resources/css/welcome.css',
                'resources/css/cart-itens.css',
                'resources/css/cart.css',
                'resources/css/header.css',
                'resources/css/footer.css',
                'resources/css/contact.css',
                'resources/css/client.css',
                'resources/css/login.css',
                'resources/css/add.css',
                'resources/css/catalog-carousel.css',
                'resources/js/app.js',
                'resources/js/catalog-carousel.js',
                'resources/js/cart-itens.js',
                'resources/js/cart.js',
                'resources/js/cart-header.js',
                'resources/js/client.js',
                'resources/css/detalssProducts.css',
                'resources/css/cart-drawer.css',
                'resources/js/detallsProducts.js',
                'resources/js/product-create.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
