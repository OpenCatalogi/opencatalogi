import Vue from "vue";
import directoryIndex from "./views/directory/index.vue";
Vue.mixin({ methods: { t, n } });

const View = Vue.extend(directoryIndex);
new View().$mount("#directory");
