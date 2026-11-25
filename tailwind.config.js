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
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#EAF6FE',
                    100: '#D1ECFD',
                    200: '#A3D9FB',
                    300: '#6CC3F8',
                    400: '#34ACF5',
                    500: '#0194F3',
                    600: '#0770CD',
                    700: '#025BAA',
                    800: '#024590',
                    900: '#023B78',
                },
                accent: {
                    DEFAULT: '#F96D01',
                    dark: '#C45300',
                    soft: '#FFF1E5',
                },
                ink: '#03121A',
                muted: '#687176',
                line: '#E6EAED',
                surface: '#F7F9FA',
            },
            borderRadius: {
                card: '10px',
                control: '6px',
            },
            boxShadow: {
                card: '0 4px 16px rgba(3, 18, 26, 0.08)',
                float: '0 10px 30px rgba(3, 18, 26, 0.12)',
            },
        },
    },

    plugins: [forms],
};
