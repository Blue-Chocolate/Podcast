import js from '@eslint/js';
import globals from 'globals';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import reactRefresh from 'eslint-plugin-react-refresh';
import prettier from 'eslint-plugin-prettier';
import a11y from 'eslint-plugin-jsx-a11y';
import tseslint from 'typescript-eslint';

export default [
  {
    ignores: ['dist', './api/*', '**/node_modules/**']
  },
  js.configs.recommended,
  ...tseslint.configs.recommended,
  {
    files: ['**/*.{ts,tsx}'],
    plugins: {
      react,
      'react-hooks': reactHooks,
      'react-refresh': reactRefresh,
      prettier,
      'jsx-a11y': a11y
    },
    languageOptions: {
      ecmaVersion: 2020,
      globals: globals.browser,
      parserOptions: {
        ecmaFeatures: {
          jsx: true
        }
      }
    },
    rules: {
      // React rules
      'react/react-in-jsx-scope': 'off',
      'react/prop-types': 'off',
      'react/display-name': 'error',
      'react/function-component-definition': 'warn',
      'react/jsx-props-no-multi-spaces': 'warn',
      'react/jsx-pascal-case': ['error', { allowNamespace: true }],
      'react/boolean-prop-naming': ['error', { rule: '^(is|has|can|use|as|show)[A-Z]([A-Za-z0-9]?)+' }],
      'react/jsx-key': ['error', { checkFragmentShorthand: true }],

      // React Hooks rules
      'react-hooks/rules-of-hooks': 'error',
      'react-hooks/exhaustive-deps': 'off',

      // TypeScript rules
      'no-unused-vars': 'off',
      '@typescript-eslint/no-explicit-any': 'off',
      '@typescript-eslint/no-unused-vars': [
        'error',
        {
          argsIgnorePattern: '^_',
          varsIgnorePattern: '^_',
          caughtErrorsIgnorePattern: '^_',
          destructuredArrayIgnorePattern: '^_'
        }
      ],

      // General rules
      'no-console': 'error',

      // Prettier integration
      'prettier/prettier': [
        'error',
        {
          singleQuote: true
        }
      ],

      // Accessibility rules
      'jsx-a11y/alt-text': 'error',
      'jsx-a11y/anchor-is-valid': 'error'
    },
    settings: {
      react: {
        version: 'detect'
      }
    }
  }
];
