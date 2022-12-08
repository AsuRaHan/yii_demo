const mix = require('laravel-mix');

mix.js('vueapp/app.js', 'web/app.js')
    .setPublicPath('web')
    .vue();
