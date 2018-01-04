import Vue from 'vue'
import Vuex from 'vuex'
import Axios from 'axios'
import VueProgressiveImage from 'progressive-image/dist/vue'
import Store from './store'
import router from './router'
import App from './App.vue'
Vue.use(Vuex)
Vue.use(VueProgressiveImage, {
  removePreview: true
})

Vue.config.debug = true
Vue.config.productionTip = false
Vue.config.devTools = true
Axios.defaults.baseURL = 'http://localhost/rapportportal/'
Object.defineProperty(Vue.prototype, '$http', { value: Axios })
/* eslint-disable no-new */
new Vue({
  el: '#app',
  Store,
  router,
  template: '<App/>',
  components: { App }
})
