import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            colors: {
                "layout-sun": {
                },
                "layout-night": {
                },
                "primary-sun": {
                },
                "primary-night": {
                },
            },
            fontFamily: {
                sans: ["Open Sans", ...defaultTheme.fontFamily.sans],
                logo: ["Open Sans", ...defaultTheme.fontFamily.sans],
                title: ["Ubuntu", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};