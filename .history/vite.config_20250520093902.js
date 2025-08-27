import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/kanban-board.css',
                'resources/css/fullCalendar.css',
                'resources/css/project-management.css',
                'resources/js/activityLog.js',
                'resources/js/sortingTask.js',
                'resources/js/app.js',
                'resources/js/fullCalendar.js',
                'resources/js/kanban-board.js'
            ],
            refresh: true,
        }),
    ],
});