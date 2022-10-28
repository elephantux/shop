import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import mkcert from 'vite-plugin-mkcert';

export default defineConfig({
    plugins: [
        mkcert(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/sass/main.sass',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        https: true
    },
});
