import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        svelte(),
    ],
    resolve: {
        alias: {
            $assets: '/resources/assets',
            $lib: '/resources/js/lib',
        },
    },
    server: {
        watch: {
            ignored: ['**/vendor/**', '**/storage/**'],
        },
    },
});
