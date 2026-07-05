import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Tailwind removed: SIPPM Madina uses Blade + Bootstrap 5 + Alpine.js (all via
// CDN, see resources/views layouts). Vite here only compiles the custom
// "Bright Skeuomorphism" palette CSS (resources/css/app.css, which owns
// typography — Inter via Google Fonts CDN) and the small app JS bundle
// (Laravel Echo + Pusher-js for Reverb, see resources/js/app.js).
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
