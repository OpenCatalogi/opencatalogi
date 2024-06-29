<template>
	SelectedL {{ store.selected }}
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
import { store } from '../store.js'
import Catalogi from './catalogi/index.vue'
import Dashboard from './dashboard/index.vue'
import Directory from './directory/index.vue'
import Matadata from './metaData/index.vue'
import Publications from './publications/index.vue'
import Search from './search/index.vue'

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
	data() {
		return {
			store: {
				selected: 'dashboard',
				modal: false,
				item: false
			}
		}
	}
}
</script>


