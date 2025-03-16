import { fontFamily } from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
const config = {
    darkMode: ['class'],
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.{html,svelte,js,ts,jsx,tsx}',
    ],
    safelist: ['dark'],
    theme: {
        container: {
            center: true,
            padding: '2rem',
            screens: {
                '2xl': '1400px',
            },
        },
        extend: {
            fontFamily: {
                sans: [
                    'FiraSans',
                    'ui-sans-serif',
                    'system-ui',
                    ...fontFamily.sans,
                ],
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
                    },
                },

                red: {
                    DEFAULT: 'hsl(var(--destructive) / <alpha-value>)',
                    foreground:
                        'hsl(var(--destructive-foreground) / <alpha-value>)',
                },
                green: {
                    DEFAULT: 'hsl(var(--green) / <alpha-value>)',
                    foreground: 'hsl(var(--green-foreground) / <alpha-value>)',
                },
                yellow: {
                    DEFAULT: 'hsl(var(--yellow) / <alpha-value>)',
                    foreground: 'hsl(var(--yellow-foreground) / <alpha-value>)',
                },
                blue: {
                    DEFAULT: 'hsl(var(--blue) / <alpha-value>)',
                    foreground: 'hsl(var(--blue-foreground) / <alpha-value>)',
                },

                // Shadcn-svelte defaults
                border: 'hsl(var(--border) / <alpha-value>)',
                input: 'hsl(var(--input) / <alpha-value>)',
                ring: 'hsl(var(--ring) / <alpha-value>)',
                background: 'hsl(var(--background) / <alpha-value>)',
                foreground: 'hsl(var(--foreground) / <alpha-value>)',
                primary: {
                    DEFAULT: 'hsl(var(--primary) / <alpha-value>)',
                    foreground:
                        'hsl(var(--primary-foreground) / <alpha-value>)',
                },
                secondary: {
                    DEFAULT: 'hsl(var(--secondary) / <alpha-value>)',
                    foreground:
                        'hsl(var(--secondary-foreground) / <alpha-value>)',
                },
                destructive: {
                    DEFAULT: 'hsl(var(--destructive) / <alpha-value>)',
                    foreground:
                        'hsl(var(--destructive-foreground) / <alpha-value>)',
                },
                muted: {
                    DEFAULT: 'hsl(var(--muted) / <alpha-value>)',
                    foreground: 'hsl(var(--muted-foreground) / <alpha-value>)',
                },
                accent: {
                    DEFAULT: 'hsl(var(--accent) / <alpha-value>)',
                    foreground: 'hsl(var(--accent-foreground) / <alpha-value>)',
                },
                popover: {
                    DEFAULT: 'hsl(var(--popover) / <alpha-value>)',
                    foreground:
                        'hsl(var(--popover-foreground) / <alpha-value>)',
                },
                card: {
                    DEFAULT: 'hsl(var(--card) / <alpha-value>)',
                    foreground: 'hsl(var(--card-foreground) / <alpha-value>)',
                },
            },
            borderRadius: {
                lg: 'var(--radius)',
                md: 'calc(var(--radius) - 2px)',
                sm: 'calc(var(--radius) - 4px)',
            },
        },
    },
};

export default config;
