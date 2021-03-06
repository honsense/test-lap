import Vue from 'vue';
import VueRouter from 'vue-router';
import index from 'core-js';
import App from './App.vue';
import Home from './components/Home.vue';
import tabletest from './components/tabletest.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import admin from './components/admin.vue';
import PasswordReset from './components/PasswordReset.vue';

import axios from 'axios';
import VueAxios from 'vue-axios';
import 'babel-polyfill'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'

Vue.use(Vuetify)
Vue.use(VueRouter);
Vue.use(VueAxios, axios);
axios.defaults.baseURL = '/api/';
Vue.config.productionTip = false;


// export default new Router({
const router = new VueRouter({
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: {
        auth: false
      }
    },
    {
        path: '/Register',
        name: 'register',
        component: Register,
        meta: {
            auth: 'admin'
        }
    },
    {
        path: '/admin',
        name: 'admin',
        component: admin,
        meta: {
            auth: 'admin'
        }
    },
    {
        path: '/passwordchange',
        name: 'passwordchange',
        component: PasswordReset,
        meta: {
            auth: true
        }
    },
    {
      path: '/dashboard',
      name: 'tabletest',
      component: tabletest,
      meta: {
        auth: true
      }
    }
  ]
})

Vue.router = router;

Vue.use(require('@websanova/vue-auth'), {
  auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
  http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
  router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
});

App.router = Vue.router;

new Vue(App).$mount('#app');