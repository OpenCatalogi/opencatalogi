import Vue from 'vue'
import metaDataIndex from './views/metaData/MetaDataIndex.vue'
Vue.mixin({ methods: { t, n } })

const View = Vue.extend(metaDataIndex)
new View().$mount('#metaData')
