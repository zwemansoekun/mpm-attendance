const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .js('resources/js/sampledesign.min.js', 'public/js')
   .js('resources/js/mdb.min.js', 'public/js')
   .js('resources/js/plugins/perfect-scrollbar.jquery.min.js', 'public/js/plugins')
   .js('resources/js/core/popper.min.js', 'public/js/core')
   .js('resources/js/core/jquery.min.js', 'public/js/core')
   .js('resources/js/core/bootstrap.min.js', 'public/js/core'); 
  
  
