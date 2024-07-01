import Vue from 'vue'
import catalogiIndex from './views/catalogi/index.vue'
Vue.mixin({ methods: { t, n } })

const View = Vue.extend(catalogiIndex)
new View().$mount('#catalogi')
