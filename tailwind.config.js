import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
            brand: {
                50: '#FDF1F7',
                100: '#FAD4E6',
                200: '#F5A9C9',
                300: '#EE7CAE',
                400: '#E45E9C',
                500: '#D4458A',
                600: '#C73E80',
                700: '#B73574',
                800: '#9A265E',
                900: '#7D1E4D',
            },
        },
        },
    },

    plugins: [forms],
};
