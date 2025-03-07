import Vue from 'vue'
import AdminSettings from './views/AdminSettings.vue'

Vue.mixin({ methods: { t, n } })

new Vue(
	{
		render: h => h(AdminSettings),
	},
).$mount('#admin-settings')
