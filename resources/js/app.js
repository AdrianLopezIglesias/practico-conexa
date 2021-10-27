require('./bootstrap');

window.Vue = require('vue').default;

Vue.component('space-travel', require('./components/SpaceTravel.vue').default);

const app = new Vue({
    el: '#app',
});
