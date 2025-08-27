import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/kanban-board.css',
                'resources/css/fullCalendar.css',
                'resources/js/app.js',
                'resources/js/fullCalendar.js',
                'resources/js/kanban-board.js'
            ],
            refresh: true,
        }),
    ],
});