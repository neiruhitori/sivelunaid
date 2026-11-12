import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/welcome/style.css',
                'resources/css/welcome/roblox.css',
                'resources/js/welcome/site-loader.js',
                'resources/js/welcome/music-player.js',
                'resources/js/welcome/order-guide.js',
                'resources/js/welcome/script.js',
                'resources/js/welcome/product-order.js',
                'resources/js/welcome/order-history.js',
            ],
            refresh: true,
        }),
    ],
});
