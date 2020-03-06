const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const dev = process.env.NODE_ENV === 'development';
// Path to my laravel app's public folder
const path = dev ? '../../public/vendor/schematics/' : 'dist/';

if (dev && process.argv.includes('--sync')) {
    mix.browserSync({
        proxy: 'localhost:8000/schematics',
        notify: false
    });
}

mix.setPublicPath(path)
    .js('resources/js/app.js', path)
    .sass('resources/scss/app.scss', path)
    .options({
        processCssUrls: false,
        postCss: [tailwindcss('./tailwind.config.js')],
    });
