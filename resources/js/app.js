require('./bootstrap');

// Laravel 8.x
import Vue from 'vue';
// Laravel +8.x
// window.Vue = require('vue);

import { VueMaskDirective } from 'v-mask'
Vue.directive('mask', VueMaskDirective);

import axios from 'axios';

Vue.prototype.axios = window.axios

Vue.component('ex', require('./components/ExampleComponent.vue').default);
const app = new Vue({
    el: '#app'
});
