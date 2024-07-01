import Vue from 'vue'
import metaDataIndex from './views/metaData/index.vue'
Vue.mixin({ methods: { t, n } })

const View = Vue.extend(metaDataIndex)
new View().$mount('#metaData')
