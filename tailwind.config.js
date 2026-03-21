/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/js/**/*.{js,jsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', 'Segoe UI', 'sans-serif'],
            },
            colors: {
                sidebar: {
                    DEFAULT: '#1a1d24',
                    hover: '#232730',
                },
            },
            boxShadow: {
                card: '0 4px 24px rgba(15, 23, 42, 0.08)',
            },
        },
    },
    plugins: [],
};
