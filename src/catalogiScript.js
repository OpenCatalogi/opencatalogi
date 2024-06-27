import Vue from "vue";
import catalogiaIndex from "./views/catalogi/index.vue";
Vue.mixin({ methods: { t, n } });

const View = Vue.extend(catalogiIndex);
new View().$mount("#catalogi");
