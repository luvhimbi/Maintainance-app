import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';import { defineConfig } from 'vite';
import laravelPlugin from 'laravel-vite-plugin';
import reactRefresh from '@vitejs/plugin-react';

export default defineConfig({ 
    plugins: [
        reactRefresh(), // If you’re using React; omit if not
        laravelPlugin({ 
            input: ['resources/js/app.js'], // <- We normally import CSS in app.js
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

