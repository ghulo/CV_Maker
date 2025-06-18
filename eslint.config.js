import js from "@eslint/js";
import globals from "globals";
import pluginVue from "eslint-plugin-vue";
import eslintPluginPrettierRecommended from "eslint-plugin-prettier/recommended"; // Keep this import for now
import parserVue from "vue-eslint-parser";

export default [
  // Ignore list for files ESLint should not process
  {
    ignores: [
      "vendor/**",
      "public/**",
      "node_modules/**",
      "bootstrap/**",
      "storage/**",
      "tests/**",
      "*.config.js", // Ignores eslint.config.js, postcss.config.js, tailwind.config.js, prettier.config.js, vite.config.js
      "package.json",
      "package-lock.json",
      "composer.json",
      "composer.lock",
      "artisan",
      "phpunit.xml",
      "README.md",
    ],
  },
  // 1. Core ESLint Recommended Rules (for general JS files)
  js.configs.recommended,

  // 2. Vue 3 Recommended Rules (for Vue files)
  ...pluginVue.configs["flat/recommended"],

  // 3. Global language options and custom rules (applied to JS and Vue files)
  {
    files: ["**/*.{js,mjs,cjs,vue}"],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
      },
      ecmaVersion: "latest",
      sourceType: "module",
      parser: parserVue, // Primary parser for .vue files
      parserOptions: {
        parser: "@babel/eslint-parser", // Parser for <script> content inside .vue files and for .js files
        requireConfigFile: false,
      },
    },
    rules: {
      // Custom rules/overrides
      "no-unused-vars": "off",
      "vue/multi-word-component-names": "off",
      "vue/no-unused-components": "warn",
      "vue/html-indent": "off",
      "vue/max-attributes-per-line": "off",
      "vue/first-attribute-linebreak": "off",
      "vue/html-self-closing": "off",
      "vue/attributes-order": "off",
      "vue/singleline-html-element-content-newline": "off",
      "vue/html-closing-bracket-newline": "off",
      "vue/require-prop-types": "off",
      "vue/no-v-html": "off",
    },
  },

  // 4. Prettier integration (MUST be last to ensure it overrides all stylistic rules)
  // This already includes `prettier/prettier` rule and `eslint-config-prettier`
  // eslintPluginPrettierRecommended, // <--- REMOVE THIS LINE
];