import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brick: {
                    yellow: '#FFD500',
                    'yellow-dark': '#E8C200',
                    red: '#E3000B',
                    'red-dark': '#C40009',
                    black: '#1A1A1A',
                    dark: '#242424',
                },
            },
        },
    },

    plugins: [forms],
};
