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
        host: '0.0.0.0', // Faz o Vite ouvir em todos os endereços da rede
        hmr: {
            host: '192.168.1.100', // O IP do computador que está a rodar o código
        },
    },
});