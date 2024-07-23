<script setup>
import { store } from '../store/store.js'
</script>

<template>
	<!-- Placeholder div for all of the vieuws-->
	<NcAppContent>
		<template #default>
			<Catalogi v-if="store.selected === 'catalogi'" />
			<Dashboard v-if="store.selected === 'dashboard'" />
			<Directory v-if="store.selected === 'directory'" />
			<Publications v-if="store.selected === 'publication'" />
			<Matadata v-if="store.selected === 'metaData'" />
			<Search v-if="store.selected === 'search'" :search="store.search" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent } from '@nextcloud/vue'
import Catalogi from './catalogi/CatalogiIndex.vue'
import Dashboard from './dashboard/DashboardIndex.vue'
import Directory from './directory/DirectoryIndex.vue'
import Matadata from './metaData/MetaDataIndex.vue'
import Publications from './publications/PublicationIndex.vue'
import Search from './search/SearchIndex.vue'

export default {
	name: 'Views',
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
