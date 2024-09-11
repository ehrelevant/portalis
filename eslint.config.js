import globals from 'globals';
import js from '@eslint/js';
import prettier from 'eslint-config-prettier';
import svelte from 'eslint-plugin-svelte';

export default [
    { ignores: ['vendor/'] },
    { languageOptions: { globals: globals.node } },
    js.configs.recommended,
    ...svelte.configs['flat/recommended'],
    prettier,
    ...svelte.configs['flat/prettier'],
    {
        files: ['**/*.svelte'],
        languageOptions: {
            parserOptions: {
                parser: js.parser,
            },
        },
    },
];
