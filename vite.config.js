import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js', 
                'resources/js/chat.jsx', 
                'resources/js/poll.jsx', 
                'resources/js/quizz.jsx'
            ],
            refresh: true,
        }),
        react(),
    ],
    build: {
        manifest: 'manifest.json', // Move manifest to root of build dir
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});