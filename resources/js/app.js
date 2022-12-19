/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue').default;
import Vue from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component('v-select', vSelect);
Vue.component('SubtaskBoard', require('./components/subtask/Board.vue').default);
Vue.component('Subtask', require('./components/subtask/Task.vue').default);

Vue.component('dashboard', require('./components/admin/Dashboard.vue').default);
Vue.component('Board', require('./components/admin/Board.vue').default);
Vue.component('Task', require('./components/admin/Task.vue').default);

Vue.component('heads', require('./components/heads/Dashboard.vue').default);
Vue.component('heads-board', require('./components/heads/Board').default);
Vue.component('heads-task', require('./components/heads/Task.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});