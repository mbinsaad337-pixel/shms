import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#1e3a8a', // Deep Blue
                    50: '#eff6ff',
                    100: '#dbeafe',
                    900: '#1e3a8a',
                },
                secondary: {
                    DEFAULT: '#f97316', // Orange
                    50: '#fff7ed',
                    100: '#ffedd5',
                    500: '#f97316',
                }
            },
            fontFamily: {
                sans: ['Almarai', 'Cairo', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
