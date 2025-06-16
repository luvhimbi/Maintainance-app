import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({ 
    plugins: [
        laravel({ 
            input: ['resources/js/app.js'], // <- main entry
            refresh: true,
        })
    ],
    server: {
        strictPort: true,
        port: 5173,
    },
    build: {
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
            },
        },
    },
});

