require ('./bootstrap.js');
import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)
import { Form, HasError, AlertError } from 'vform';
window.Form = Form;

import Swal from 'sweetalert2';
import JwPagination from 'jw-vue-pagination';
Vue.component('jw-pagination', JwPagination);


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
window.Swal = Swal;
window.Toast = Toast;

import Home from './components/Home'
import User from './components/User'
import Client from './components/Client'
import Device from './components/Device'
import Cow from './components/Cow'
import ClientDevices from './components/ClientDevice'
import Gate from './components/Gate'
import Reports from './components/Reports'
import Day from './components/Day'
import Day7 from './components/Day7'
import Day30 from './components/Day30'

const router = new VueRouter({
  mode: 'history',
  routes: [
    { path: '/', name: 'home',  component: Home },
    { path: '/users', name: 'user',  component: User },
    { path: '/clients', name: 'client',  component: Client },
    { path: '/devices', name: 'device',  component: Device },
    { path: '/clients/:id/cows', name: 'cow',  component: Cow },
    { path: '/clients/:id/devices', name: 'clients-devices',  component: ClientDevices },
    { path: '/gates', name: 'gate',  component: Gate },
    { path: '/reports', name: 'reports',  component: Reports },
    { path: '/day', name: 'day',  component: Day },
    { path: '/day7', name: 'day7',  component: Day7 },
    { path: '/day30', name: 'day30',  component: Day30 },
  ]
});

const app = new Vue({
  el: '#app',
  router,
});
