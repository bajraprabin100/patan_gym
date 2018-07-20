let mix = require('laravel-mix');

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

// mix.js('resources/assets/js/app.js', 'public/js')
//    .sass('resources/assets/sass/app.scss', 'public/css');
mix.scripts([
    'public/bower_components/jquery-ui/jquery-ui.min.js',
    'public/bower_components/bootstrap/dist/js/bootstrap.min.js',
    'public/bower_components/datatables.net/js/jquery.dataTables.min.js',
    'public/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
    'public/bower_components/fastclick/lib/fastclick.js',
    'public/dist/js/adminlte.min.js',
    'public/dist/js/demo.js',
    'public/select2/select2.min.js'
], 'public/js/all.js')
mix.styles([
    'public/bower_components/bootstrap/dist/css/bootstrap.min.css',
    'public/bower_components/font-awesome/css/font-awesome.min.css',
    'public/bower_components/Ionicons/css/ionicons.min.css',
    'public/dist/css/AdminLTE.min.css',
    'public/dist/css/skins/_all-skins.min.css',
    'public/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
    'public/select2/select2.min.css'
], 'public/css/all.css');