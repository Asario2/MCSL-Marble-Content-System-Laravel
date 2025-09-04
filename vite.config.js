import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    define: {
        'process.env': {}, // Fix für php-parser & Node-spezifische Module im Browser
    },
    server: {
        cors: true,
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        sourcemap: true, // ?? aktiviert Source Maps für Debugging
    },
});
