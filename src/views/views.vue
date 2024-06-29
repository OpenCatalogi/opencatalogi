<template>
	<!-- Placeholder div for all of the vieuws-->
	<div>		
		<Catalogi v-if="store.stroselected === 'catalogi'" />
		<Dashboard v-if="store.selected === ''" />
		<Directory v-if="store.selected === 'directory'" />
		<Matadata v-if="store.selected === 'metaData'" />
		<Search v-if="store.selected === 'search'" />
	</div>
</template>

<script>
import { store } from './store.js'
import Catalogi from './views/catalogi/index.vue'
import Dashboard from './views/dashboard/index.vue'
import Directory from './views/directory/index.vue'
import Matadata from './views/metData/index.vue'
import Publications from './views/publications/index.vue'
import Search from './views/search/index.vue'

export default {
	name: 'Modals',
	components: {
		Catalogi,
		Dashboard,
		Directory,
		Matadata,
		Publications,
		Search,
		store
	},
	methods: {
		setModal(modal) {
			store.modal = modal
		},			
		setSelected(selected) {
			store.selected = selected
		},
		setItem(item) {
			store.item = item
		}
	},
}
</script>


