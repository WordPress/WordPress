let mix = require('laravel-mix');
let path = require('path');

mix.setPublicPath(path.resolve('./'));

mix.js('resources/js/app.js', 'js');

mix.postCss("resources/css/app.css", "css");

mix.postCss("resources/css/editor-style.css", "./");

mix.options({
    postCss: [
        require('postcss-nested-ancestors'),
        require('postcss-nested'),
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ]
});

// mix.browserSync({
//     proxy: 'http://your-website.test',
//     host: 'your-website.test',
//     open: 'external',
//     port: 8000
// });

mix.version();
