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

// 加入webpack偵測資料夾
// 因多國語(resources/lang)系會轉換給前端(languages.js)使用，所以加入偵測語系檔是否更改
const ExtraWatchWebpackPlugin = require('extra-watch-webpack-plugin');
mix.webpackConfig({
    plugins: [
        new ExtraWatchWebpackPlugin({
            files: [],
            dirs: ['resources/lang']
        })
    ]
});

// 抓取資料夾下的所有檔案
let fs = require('fs');
let getFiles = function(dir) {
    // get all 'files' in this directory
    // filter directories
    return fs.readdirSync(dir).filter(file => {
        return fs.statSync(`${dir}/${file}`).isFile();
    });
};
//自動1對1 compile admin資料夾底下的所有檔案，不合併
getFiles('resources/js/admin').forEach(function(filepath) {
    mix.js('resources/js/admin/' + filepath, 'js/admin/' + filepath);
});
//自動1對1 compile front資料夾底下的所有檔案，不合併
getFiles('resources/js/front').forEach(function(filepath) {
    mix.js('resources/js/front/' + filepath, 'js/front/' + filepath);
});

//產生給前端使用的javascript language.js 檔案
const WebpackShellPlugin = require('webpack-shell-plugin');
mix.webpackConfig({
    plugins: [
        new WebpackShellPlugin({
            onBuildStart: [
                // 'php artisan lang:js --quiet',
                'php artisan lang:js resources/js/languages.js --quiet'
            ],
            onBuildEnd: []
        })
    ]
});
//產生resources/js/languages.js之後再複製到前端public/js/languages.js供前端使用
//因需要用到mix('/js/languages.js')，所以使用mix來copy；不使用的話，可以直接使用php artisan lang:js指令產生。
mix.copy('resources/js/languages.js', 'public/js/languages.js');

mix.sass('resources/sass/admin/admin.scss', 'public/css/admin');
mix.sass('resources/sass/front/front.scss', 'public/css/front');
mix.sass('resources/sass/app.scss', 'public/css');
// mix.js('resources/js/admin/*', 'public/css/admin');
// mix.js('resources/js/front/*', 'public/css/front');
mix.js('resources/js/app.js', 'public/js').extract([
    'vue',
    'jquery',
    'lodash',
    'axios',
    'bootstrap-sass'
]);

if (!mix.inProduction()) {
    mix.sourceMaps().webpackConfig({ devtool: 'inline-source-map' });
} else {
    mix.version();
}

/**
 * browserSync
 */
// mix.browserSync('https://j.epay');
mix.browserSync({
    proxy: 'https://b.epay',
    host: 'b.epay',
    open: 'external',
    https: true,
    https: {
        key: "/Applications/MAMP/htdocs/epay/ssl_keys/b.epay.key",
        cert: "/Applications/MAMP/htdocs/epay/ssl_keys/b.epay.crt"
    }
});
