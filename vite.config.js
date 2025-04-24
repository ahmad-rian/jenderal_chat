import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [`resources/views/**/*`],
        }),
    ],
    css: {
        postcss: {
            plugins: [
                require('tailwindcss/nesting'),
                require('tailwindcss')({
                    content: [
                        "./resources/**/*.blade.php",
                        "./resources/**/*.js",
                        "./resources/**/*.vue",
                    ],
                    plugins: [require("daisyui")],
                    daisyui: {
                        themes: ["light", "dark"],
                    }
                }),
                require('autoprefixer'),
            ],
        },
    },
    server: {
        cors: true,
    },
});