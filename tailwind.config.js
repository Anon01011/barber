import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                obsidian: {
                    DEFAULT: '#0b0f17',
                    950: '#05070a',
                    900: '#070a10',
                    800: '#111724',
                    700: '#1a2233',
                    border: '#1f293d',
                },
                gold: {
                    DEFAULT: '#c5a880',
                    hover: '#b38e5d',
                    dark: '#916f3d',
                    light: '#fbf7f2',
                    border: '#e8dccb',
                },
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
                heading: ['Outfit', 'sans-serif'],
                serif: ['Playfair Display', 'serif'],
                body: ['Plus Jakarta Sans', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
