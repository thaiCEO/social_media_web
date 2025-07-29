import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';


export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    resolve: {
        alias: {
            ziggy: path.resolve('vendor/tightenco/ziggy/dist'), // ✅ fallback alias
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },

    build: {
        rollupOptions: {
        output: {
            manualChunks: {
            // ✅ Split vendor libraries into their own chunks
            vue: ['@inertiajs/vue3'],
            element: ['element-plus'],
            },
        },
        },
        chunkSizeWarningLimit: 1000, // optional: increase warning limit
    },

    

});