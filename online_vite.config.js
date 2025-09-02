import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

// Hilfsfunktion: lädt Certs je nach Domain
function getHttpsConfig() {
    const hostname = process.env.VITE_DOMAIN || 'www.marblefx.net';

    if (hostname === 'www.marblefx.net') {
        return {
            key: fs.readFileSync('/etc/letsencrypt/live/www.marblefx.net/privkey.pem'),
            cert: fs.readFileSync('/etc/letsencrypt/live/www.marblefx.net/fullchain.pem'),
        };
    }

    if (hostname === 'ab.marblefx.net') {
        return {
            key: fs.readFileSync('/etc/letsencrypt/live/ab.marblefx.net/privkey.pem'),
            cert: fs.readFileSync('/etc/letsencrypt/live/ab.marblefx.net/fullchain.pem'),
        };
    }

    return false; // Fallback = kein HTTPS
}

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
        'process.env': {},
        'process.env.NODE_ENV': '"development"',
    },
    server: {
        host: '0.0.0.0',
        cors: true,
        https: true,
        hmr: {
            host: process.env.VITE_DOMAIN || 'www.marblefx.net',
            protocol: 'wss',
        },
        allowedHosts: ['www.marblefx.net','ab.marblefx.net'],
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
});
