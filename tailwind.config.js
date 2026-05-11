// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                display: ['"DM Serif Display"', ...defaultTheme.fontFamily.serif],
                sans:    ['"DM Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50:  '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                ink: {
                    50:  '#f8f7f4',
                    100: '#f0ede6',
                    200: '#e0d9ce',
                    300: '#c5bba8',
                    400: '#a8987f',
                    500: '#8c7a62',
                    600: '#6b5c48',
                    700: '#4a3f31',
                    800: '#2e2820',
                    900: '#1a1612',
                },
            },
            screens: {
                xs: '420px',
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(26,22,18,0.08), 0 1px 2px -1px rgba(26,22,18,0.04)',
                'card-hover': '0 8px 24px -4px rgba(26,22,18,0.12), 0 4px 8px -2px rgba(26,22,18,0.06)',
                'nav': '0 1px 0 0 rgba(26,22,18,0.06)',
            },
            animation: {
                'fade-in-up':  'fadeInUp 0.4s ease both',
                'fade-in':     'fadeIn 0.3s ease both',
                'slide-in':    'slideIn 0.35s cubic-bezier(0.16,1,0.3,1) both',
            },
            keyframes: {
                fadeInUp: {
                    '0%':   { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideIn: {
                    '0%':   { opacity: '0', transform: 'translateX(-16px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
            },
        },
    },
    plugins: [],
}