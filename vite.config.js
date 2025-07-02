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
        chunkSizeWarningLimit: 500,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Core vendor libraries
                    'vendor-vue': ['vue', 'vue-router'],
                    'vendor-ui': ['@fortawesome/fontawesome-svg-core', '@fortawesome/vue-fontawesome'],
                    'vendor-i18n': ['vue-i18n'],
                    'vendor-utils': ['axios'],
                    
                    // Split large components
                    'cv-templates': [
                        './resources/js/components/cv_templates/ClassicTemplate.vue',
                        './resources/js/components/cv_templates/ModernTemplate.vue',
                        './resources/js/components/cv_templates/ProfessionalTemplate.vue',
                        './resources/js/components/cv_templates/CreativeTemplate.vue'
                    ]
                },
            },
        },
        target: 'esnext',
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
    resolve: {
        alias: {
            '~css': '/resources/css',
            '@': '/resources/js',
        },
    },
    optimizeDeps: {
        include: [
            'vue',
            'vue-router',
            'vue-i18n',
            'axios'
        ],
        exclude: [
            '@fortawesome/free-solid-svg-icons',
            'vue3-apexcharts',
            'apexcharts'
        ]
    },
});

