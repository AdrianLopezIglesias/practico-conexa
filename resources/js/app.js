require('./bootstrap');

window.Vue = require('vue').default;
import VueRouter from 'vue-router';

import routes from './routes/routes';

import App from "./App.vue";
const router = new VueRouter({ mode: 'history', routes });
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

const app = new Vue({
	el: '#app',
	components: { App },
	router,
  template: "<App/>"
});
