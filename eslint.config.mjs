import js from '@eslint/js';
import globals from 'globals';

export default [
    js.configs.recommended,
    {
        files: ['src/web/assets/cp/src/**/*.js'],
        languageOptions: {
            sourceType: 'module',
            globals: {
                ...globals.browser,
                Craft: 'readonly',
                Garnish: 'readonly',
                jQuery: 'readonly',
            },
        },
    },
    {
        files: ['*.mjs'],
        languageOptions: {
            globals: globals.node,
        },
    },
];
