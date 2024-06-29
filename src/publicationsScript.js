import Vue from 'vue'
import publicationsIndex from './views/publications/index.vue'
Vue.mixin({ methods: { t, n } })

const View = Vue.extend(publicationsIndex)
new View().$mount('#publications')
