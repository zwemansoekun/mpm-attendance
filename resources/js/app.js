/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.$=window.JQuery= require('jquery')//window.JQuery
window.Vue = require('vue');
window.moment = require('moment');
window.$_=require('lodash');
jQuery.noConflict(); 

import VueSweetalert2 from 'vue-sweetalert2';
import App from './App.vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import axios from 'axios';
import {routes} from './routes';
import { Verify } from 'crypto';
import VueSimpleAlert from "vue-simple-alert";



Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(VueSimpleAlert);
Vue.use(VueSweetalert2);

Vue.mixin({
    data: function() {
      return {
        GlobalVar:'',
        SsbMax:'',
        SsbPaid:'',
      }
    },
    created() {
        let that=this;
        this.axios({
            url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/dsettings",
            method: 'get'
        })
        .then(function (response) {
            let global='';
            console.log('global',response.data);
            global=response.data;
            for(let i=0;i<global.length;i++){
              that.SsbMax=global[i]['ssb_max'];
              that.SsbPaid=global[i]['ssb_paid'];
            }
        })
        .catch(function (error) {
        });
    }
  })


const router = new VueRouter({
    mode: 'history',
    routes: routes
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// window.onload = function () {

// }
const app = new Vue({
    el: '#app',
    router: router,
    render: h => h(App),
});
