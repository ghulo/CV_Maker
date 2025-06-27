import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// Set host for development
const host = 'localhost';

export default defineConfig({
    base: '/',
    plugins: [
        laravel({
            input: ['resources/css/master.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: host,
        port: 5173,
        hmr: {
            host: host,
        },
        cors: true,
    },
    publicDir: 'public',
    build: {
        outDir: 'public/build',
        chunkSizeWarningLimit: 700,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString()
                    }
                },
            },
        },
    },
    resolve: {
        alias: {
            '~css': '/resources/css',
            '@': '/resources/js',
        },
    },
});

