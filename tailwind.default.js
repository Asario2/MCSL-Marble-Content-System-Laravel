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

colors: {
  layout-sun: {
      0: "#ffffff",
      50: "#fff8f3",
      100: "#ffe7d9",
      200: "#ffcfb3",
      300: "#ffb380",
      400: "#ff944d",
      500: "#ff751a",
      600: "#e66000",
      700: "#b34c00",
      800: "#803800",
      900: "#4d2400",
      950: "#331a00",
      1000: "#000000",
    },
    layout-night: {
      0: "#000000",
      50: "#1a0a00",
      100: "#330f00",
      200: "#4d1600",
      300: "#661c00",
      400: "#803300",
      500: "#ff4c00",
      600: "#ff6600",
      700: "#ff8533",
      800: "#ffa366",
      900: "#ffbf99",
      950: "#ffd9cc",
      1000: "#ffffff",
      1050: "#ffa200",
    
  },
  primary-sun: {
      0: "#ffffff",
      50: "#fff8f3",
      100: "#ffe7d9",
      200: "#ffcfb3",
      300: "#ffb380",
      400: "#ff944d",
      500: "#ff751a",
      600: "#e66000",
      700: "#b34c00",
      800: "#803800",
      900: "#4d2400",
      950: "#331a00",
      1000: "#000000",
    },
    primary-night: {
      0: "#000000",
      50: "#1a0a00",
      100: "#330f00",
      200: "#4d1600",
      300: "#661c00",
      400: "#803300",
      500: "#ff4c00",
      600: "#ff6600",
      700: "#ff8533",
      800: "#ffa366",
      900: "#ffbf99",
      950: "#ffd9cc",
      1000: "#ffffff",
      1050: "#ffa200",
    
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