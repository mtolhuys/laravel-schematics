const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const path = process.env.NODE_ENV === 'development' ?
    // Path to my laravel's public folder
    '../../public/vendor/schematics/'
    : 'dist/';

mix.js('resources/js/app.js', path)
    .sass('resources/scss/app.scss', path)
    .setPublicPath(path)
    .options({
        processCssUrls: false,
        postCss: [tailwindcss('./tailwind.config.js')]
    });
