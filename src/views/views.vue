<script setup>
import { store } from '../store.js'
</script>

<template>
	<!-- Placeholder div for all of the vieuws-->
	<NcAppContent>
		<template #default>
			<Catalogi v-if="store.selected === 'catalogi'" />
			<Dashboard v-if="store.selected === 'dashboard'" />
			<Directory v-if="store.selected === 'directory'" />
			<Publications v-if="store.selected === 'catalogus'" />
			<Matadata v-if="store.selected === 'metaData'" />
			<Search v-if="store.selected === 'search'" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent } from '@nextcloud/vue'
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
		NcAppContent,
	},
}
</script>
