import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'selector', 
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
         extend: {
             fontFamily: {
                battambang: ['Battambang', 'system-ui'],
                },
                fontWeight: {
                light: '300',
                Regular:'400',
                },
        },
    },

    plugins: [
        forms, typography,
        require('@tailwindcss/typography')
    ],
};
