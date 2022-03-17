const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.postCss("resources/css/app.css", "public/css")
    .postCss("resources/css/orchid-custom.css", "public/css")
    .copy(
        "./node_modules/feather-icons/dist/feather-sprite.svg",
        "public/images"
    );

mix.js("resources/js/ckeditor.js", "public/js").js(
    "resources/js/highlight.js",
    "public/js"
);

if (!mix.inProduction()) {
    mix.sourceMaps();
}
