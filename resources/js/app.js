/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//載入 AdminLTE 樣版
require('admin-lte');
//載入 AdminLTE Plugin
require('admin-lte/plugins/iCheck/icheck');

/* 載入 SweetAlert2 到全域 */
window.swal = require('sweetalert2');
/* 預先定義 SweetAlert2 初始設定 */
window.swal = swal.mixin({
    focusConfirm: false,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: Lang.get('contents.general.confirm'),
    cancelButtonText: Lang.get('contents.general.cancel')
});

import ElementUI from 'element-ui';

import locale_zh_TW from 'element-ui/lib/locale/lang/zh-TW';
import locale_zh_CN from 'element-ui/lib/locale/lang/zh-CN';
import locale from 'element-ui/lib/locale';

switch (_locale) {
    case 'zh-TW':
        locale.use(locale_zh_TW);
        break;
    case 'zh-CN':
        locale.use(locale_zh_CN);
        break;
    default:
        locale.use(locale_zh_TW);
        break;
}

Vue.use(ElementUI);
/**
 * 讓Vue使用 其他套件 排除 this 對象問題
 */
// Vue.prototype.$http = axios;
// Vue.prototype.$swal = swal;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component(
//     'example-component',
//     require('./components/ExampleComponent.vue').default
// )

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app'
// })
