module.exports = {
    content: [
        "./resources/**/*.{blade.php,css,js}",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    darkMode: "class",
    theme: {
        extend: {},
    },
    plugins: [require("@tailwindcss/typography")],
};
