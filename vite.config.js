import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/welcome.css',
                'resources/js/welcome.js',
                'resources/css/auth.css',
                'resources/css/layout.css',
                'resources/css/roles.css',
                'resources/js/employee-dashboard.js',
                'resources/css/compact-forms.css',
                'resources/css/appointments.css',
                'resources/css/index-pages.css',
                'resources/js/appointments.js',
                'resources/js/bootstrap.js',
                'resources/js/calendar-init.js',
                'resources/js/calendar.js',
                'resources/js/roles.js',
                'resources/css/settings.css',
            ],
            refresh: true,
            buildDirectory: 'build',
        }),
    ],
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        emptyOutDir: true,
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources'),
        },
    },
});
