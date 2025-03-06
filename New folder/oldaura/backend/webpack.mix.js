const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/assets/js')
    .js('resources/js/theme-init.js', 'public/assets/js')
    .postCss('resources/css/app.css', 'public/assets/css')
    .version();

// Copiar el archivo theme-switcher.js a public/assets/js
mix.copy('public/assets/js/theme-switcher.js', 'public/assets/js');
