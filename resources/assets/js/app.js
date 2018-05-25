import Vue from 'vue';
import VueRouter from 'vue-router';
import index from 'core-js';
import App from './App.vue';
import Home from './components/Home.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import tableTemplate from './components/tableTemplate';
// import router from './router';
// import VueResource from 'vue-resource';
import axios from 'axios';
import VueAxios from 'vue-axios';

import VueMaterial from 'vue-material';
import 'vue-material/dist/vue-material.css';
import 'vue-material/dist/theme/default.css';

Vue.use(VueRouter);
Vue.use(VueAxios, axios);
axios.defaults.baseURL = 'http://test.test/api/';

Vue.use(VueMaterial);
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
            auth: false
        }
    },
    {
      path: '/dash',
      name: 'dash',
      component: tableTemplate,
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

// import Vue from 'vue';
// import VueRouter from 'vue-router';
// import axios from 'axios';
// import VueAxios from 'vue-axios';
// import App from './App.vue';
// import Dashboard from './components/Dashboard.vue';
// import Home from './components/Home.vue';
// import Register from './components/Register.vue';
// import Login from './components/Login.vue';

// Vue.use(VueRouter);
// Vue.use(VueAxios, axios);

// axios.defaults.baseURL = 'http://test.test/api';

// const router = new VueRouter({
//     routes: [
//         {
//             path: '/',
//             name: 'home',
//             component: Home
//         },
//         {
//             path: '/register',
//             name: 'register',
//             component: Register,
//             meta: {
//                 auth: false
//             }
//         },
//         {
//             path: '/login',
//             name: 'login',
//             component: Login,
//             meta: {
//                 auth: false
//             }
//         },
//         {
//             path: '/dashboard',
//             name: 'dashboard',
//             component: Dashboard,
//             meta: {
//                 auth: true
//             }
//         }
//     ]
// });

// Vue.router = router

// Vue.use(require('@websanova/vue-auth'), {
//   auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
//   http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
//   router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
// })

// App.router = Vue.router

// new Vue(App).$mount('#app');

// export default{
//     router
// }