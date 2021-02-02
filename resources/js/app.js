require('./bootstrap');

// Laravel 8.x
import Vue from 'vue';
// Laravel +8.x
// window.Vue = require('vue);

import { VueMaskDirective } from 'v-mask'
Vue.directive('mask', VueMaskDirective);

Vue.prototype.axios = window.axios

Vue.component('ex', require('./components/MainPage.vue').default);
Vue.component('service', require('./components/Service.vue').default);
Vue.component('company', require('./components/Company').default);

const app = new Vue({
    el: '#app'
});

document.ondragstart = noselect;
document.onselectstart = noselect;
document.oncontextmenu = noselect;

function noselect() {
    return false;
}
