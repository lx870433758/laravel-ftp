
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router';
import ElementUI from 'element-ui';
import Vuex from 'vuex'
import 'element-ui/lib/theme-chalk/index.css';
import routers from './router/routers';

Vue.use(VueRouter);
Vue.use(ElementUI);
Vue.use(Vuex);

const router = new VueRouter({
    routes:routers
});
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    router,
    Vuex,
    el: '#app'
});
