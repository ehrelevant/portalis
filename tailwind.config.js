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
            white: '#FFFFFF',
            black: '#101010',
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

            gray: {
                50: '#E8E8E8',
                100: '#DEDEDE',
                200: '#C7C7C7',
                300: '#B0B0B0',
                400: '#999999',
                500: '#828282',
                600: '#6E6E6E',
                700: '#575757',
                800: '#404040',
                900: '#292929',
            },

            floating: {
                blue: {
                    light: '#b2c1d1',
                    DEFAULT: '#2663a6',
                },
                forest: {
                    light: '#adc8b5',
                    DEFAULT: '#227a3d',
                },
                purple: {
                    light: '#cbbce3',
                    DEFAULT: '#8054d0',
                },
                brown: {
                    light: '#cbc2b0',
                    DEFAULT: '#866b26',
                },
                red: {
                    light: '#dcafb2',
                    DEFAULT: '#bf2a30',
                },
                gray: {
                    light: '#DEDEDE',
                    DEFAULT: '#6E6E6E',
                }
            },
        },
        extend: {},
    },
    plugins: [],
};
