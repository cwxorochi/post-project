import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import router from './router'
import { VueMasonryPlugin } from "vue-masonry";
import axios from 'axios'

import './scss/app.scss'

Vue.config.productionTip = false
Vue.use(VueMasonryPlugin);

window.axios = global.axios = axios

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'local',
    wsHost: '127.0.0.1',
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

window.hub = new Vue()

new Vue({
  vuetify,
  router,
  render: h => h(App)
}).$mount('#app')
