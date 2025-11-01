import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: true, // 👈 Allow external access (from Laravel in Sail)
        hmr: {
            host: 'localhost', // 👈 Use localhost or your WSL/Ubuntu IP
        },
    },
});
