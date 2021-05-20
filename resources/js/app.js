/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('admin-lte');


window.Vue = require('vue');

import Vue from 'vue'

import VueRouter from 'vue-router'

Vue.use(VueRouter)

import routes from './routes'
 
import Toaster from 'v-toaster'
 
// You need a specific loader for CSS files like https://github.com/webpack/css-loader
import 'v-toaster/dist/v-toaster.css'
 
// optional set default imeout, the default is 10000 (10 seconds).
Vue.use(Toaster, {timeout: 5000})

Vue.component('pagination', require('laravel-vue-pagination'));

/*import { VuejsDatatableFactory } from 'vuejs-datatable';
Vue.use( VuejsDatatableFactory );*/


//Import Datatable
import DataTable from 'laravel-vue-datatable';

import { Form, HasError, AlertError } from 'vform'
Vue.use(DataTable);


//Import Datetime
import { Datetime } from 'vue-datetime';
import 'vue-datetime/dist/vue-datetime.css'
Vue.component('datetime', Datetime);

//Import Date picker
import Datepicker from 'vuejs-datepicker';

Vue.component('datepicker', Datepicker);

//Global Form 
window.Form = Form;

// Use moment
Vue.use(require('vue-moment'));

//Use Ckeditor
import CKEditor from 'ckeditor4-vue';
Vue.use( CKEditor );

//Import date range picker
import DateRangePicker from 'vue2-daterange-picker'
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

Vue.component('DateRangePicker', DateRangePicker);

//Import chart
import VueApexCharts from 'vue-apexcharts'
Vue.component('apexchart', VueApexCharts)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

Vue.component('users-list', require('./components/admin/UsersListComponent.vue').default);
Vue.component('teams-list', require('./components/admin/teams/TeamsListComponent.vue').default);
Vue.component('clients-list', require('./components/admin/clients/ClientsListComponent.vue').default);
Vue.component('tasks-list', require('./components/admin/tasks/TasksListComponent.vue').default);
Vue.component('users-role', require('./components/admin/settings/UsersRoleComponent.vue').default);
Vue.component('users-permission', require('./components/admin/settings/UsersPermissionComponent.vue').default);
Vue.component('dropdown-notification', require('./components/admin/DropdownNotification.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.filter('capitalize', function (value) {
    if (!value) return ''
    value = value.toString()
    if(value.includes("_")){
       value = value.replace("_"," ")
    }
    return value.charAt(0).toUpperCase() + value.slice(1)
  })

//date filter
import moment from 'moment'
Vue.filter('formatDate', function(value) {
  if (value) {
    return moment(String(value)).format('MM/DD/YYYY')
  }
})

export const bus = new Vue();

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes)
});

//Import Sweetalert2
import Swal from 'sweetalert2'
window.Swal = Swal
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  onOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})
window.Toast = Toast
    
// Add a 401 response interceptor
window.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    console.log(error)
    if (401 === error.response.status) {
       //$("div.dataprocessing").remove();
       Swal.fire({
            title: "Session Expired",
            text: "Your session has expired. Would you like to be redirected to the login page?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }).then((result) => {
            if (result.value) {
                window.location = '/login';
            }
        });
        return error;
    } else {
        return Promise.reject(error);
    }
});


