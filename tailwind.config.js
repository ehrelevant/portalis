/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.{svelte,js,ts,jsx,tsx}',
    ],
    theme: {
        fontFamily: {
            sans: ['FiraSans', 'ui-sans-serif', 'system-ui'],
        },
        colors: {
            light: {
                background: '#F1F1F1',
                primary: '#C7EAD2',
                secondary: '#227A3D',
                'primary-text': '#1E1E1E',
                'secondary-text': '#F1F1F1',
            },

            dark: {
                background: '#1E1E1E',
                primary: '#227A3D',
                secondary: '#C7EAD2',
                'primary-text': '#F1F1F1',
                'secondary-text': '#1E1E1E',
            },

            floating: {
                blue: {
                    dark: '#b2c1d1',
                    DEFAULT: '#2663a6',
                },
                forest: {
                    dark: '#adc8b5',
                    DEFAULT: '#227a3d',
                },
                purple: {
                    dark: '#cbbce3',
                    DEFAULT: '#8054d0',
                },
                brown: {
                    dark: '#cbc2b0',
                    DEFAULT: '#866b26',
                },
                red: {
                    dark: '#dcafb2',
                    DEFAULT: '#bf2a30',
                },
            },
        },
        extend: {},
    },
    plugins: [],
};
