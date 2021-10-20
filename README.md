# Laravel 5.8

> Document：
>
> -   https://learnku.com/docs/laravel/5.8/releases/3876

# Admin LTE 2

> Document：
>
> -   https://adminlte.io/themes/AdminLTE/documentation/
>
> Demo：
>
> -   https://adminlte.io/themes/AdminLTE/

# Laratrust

> Document：
>
> -   https://laratrust.santigarcor.me/docs/5.2/

# L5-repository

> Document：
>
> -   https://github.com/andersao/l5-repository
> -   laravel-validator
> -   https://github.com/andersao/laravel-validator
> -   https://www.twblogs.net/a/5bb030392b7177781a0fd2a9
>
> Reference：
>
> -   https://www.twblogs.net/a/5bbd67732b71776bd30c48be
> -   https://blog.csdn.net/qq_27295403/article/details/82965117
> -   https://www.jianshu.com/p/d640a61d8631
> -   https://adon988.logdown.com/posts/7811868-l5-repository
> -   https://adon988.logdown.com/posts/7812003-l5-repository-presenter-get-started

# Element

> Document：
>
> -   https://element.eleme.io/#/zh-CN

# VeeValidate

> Document：
>
> -   https://baianat.github.io/vee-validate/guide/
> -   vee-element for Element.
> -   https://github.com/davestewart/vee-element

# SweetAlert 2

> Document：
>
> -   https://sweetalert2.github.io/#examples

# Lodash

> Document：
>
> -   https://lodash.com/docs/4.17.11

# Laravel-JS-Localization

> Document：
>
> -   將 laravel 語系檔，轉換給前端 javascript 使用。
> -   已設定隨 npm run ... 自動轉出至 resources/js/languages.js 並 copy 至 public/js/languages.js
> -   前端引用後，設置好 Lang.setLocale("語系") 後，在 javascript 直接使用 Lang.get('menu.home.read.title_des')，即可取得對應的語系語言。
> -   https://github.com/rmariuzzo/Laravel-JS-Localization

# simple-qrcode

> Document：
>
> -   https://github.com/SimpleSoftwareIO/simple-qrcode
> -   https://www.simplesoftware.io/simple-qrcode/

# Notification Service Note

> Document：
>
> 1. Install redis, predis, laravel-echo-server, laravel-echo, socket.io-client
>
>     > `composer require predis/predis`
>
>     > `npm install -g laravel-echo-server`\
>     > `laravel-echo-server init`\
>     > `laravel-echo-server start / stop`
>
>     > `npm install --save laravel-echo`\
>     > `npm install --save socket.io-client`
>
> 2. Set `BROADCAST_DRIVER=redis` and `QUEUE_CONNECTION=redis` in .env file.
> 3. Start redis and start queue listen (`php artisan queue:listen` or `php artisan queue:work`).
>     > PS：If you don't want to use queue in redis, just set `QUEUE_CONNECTION=sync` in .env file, and don't start queue listen.
> 4. Start laravel-echo-server (`laravel-echo-server start`).
>
> ---
>
> 1. You can use Laravel Horizon to manage your queues, and the queues will be managed by Horizon. You don't need to execute `php artisan queue:work` or `php artisan queue:listen` artisan command to listen to the queue process.
> 2. You can access Horizon via `/horizon`, but before that you must define the permissions you can access. The permissions are granted in `HorizonServiceProvider.php` or you can define `Horizon::auth()` to manage it.
>
> ```php
> Horizon::auth(function ($request) {
>    // return true / false;
> });
> ```
>
> 3. `php artisan horizon` : Run Horizon.
> 4. `php artisan horizon:pause` : Pause Horizon.
> 5. `php artisan horizon:continue` : Continue Horizon.
> 6. `php artisan horizon:terminate` : Any jobs that Horizon is currently processing will be completed and then Horizon will exit.

# PM2 Process Manager

> Document：
> - Install：`npm i pm2 -g`.
> - http://pm2.keymetrics.io/
> - `pm2 start XXX.json` : create json file to run.
> - `pm2 delete id|name` : delete process.
> - `pm2 delete all` : delete all process.
> - `pm2 monit` : Terminal Based Dashboard.
