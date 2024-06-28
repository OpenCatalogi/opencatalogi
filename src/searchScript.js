import Vue from "vue";
import searchIndex from "./views/search/index.vue";
Vue.mixin({ methods: { t, n } });

const View = Vue.extend(searchIndex);
new View().$mount("#search");
