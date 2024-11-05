/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.{svelte,js,ts,jsx,tsx}',
    ],
    theme: {
        fontFamily: {
            'sans': ['FiraSans', 'ui-sans-serif', 'system-ui'],
        },
        colors: {
            'mint' : '#C7EAD2',
            'lighttint' : '#F1F1F1',
            'green' : '#227A3D',
            'darktint' : '#1E1E1E',
            'blue' : {
                light: '#b2c1d1',
                DEFAULT: '#2663a6',
            },
            'forest' : {
                light: '#adc8b5',
                DEFAULT: '#227a3d',
            },
            'purple' : {
                light: '#cbbce3',
                DEFAULT: '#8054d0',
            },
            'brown' : {
                light: '#cbc2b0',
                DEFAULT: '#866b26',
            },
            'red' : {
                light: '#dcafb2',
                DEFAULT: '#bf2a30',
            },
        },
        extend: {},
    },
    plugins: [],
};
