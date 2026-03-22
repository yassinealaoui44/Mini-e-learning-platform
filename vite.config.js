import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import path from 'path'; // Add this to handle paths

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // This ensures that <img src="/img/logo.png"> works correctly in Vue
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            // This allows you to use '@' as a shortcut for 'resources/js'
            // Example: import Navbar from '@/components/Navbar.vue'
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});