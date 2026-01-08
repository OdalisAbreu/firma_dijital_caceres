import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Arial', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#C41230',
                    dark: '#A00E26',
                    light: '#E01A3F',
                },
                secondary: {
                    DEFAULT: '#425968',
                    dark: '#2F3F4A',
                    light: '#5A6F7F',
                },
                accent: {
                    DEFAULT: '#949CA1',
                    dark: '#6B7378',
                    light: '#B8C0C5',
                },
            },
        },
    },

    plugins: [forms],
};
