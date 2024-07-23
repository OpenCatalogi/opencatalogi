<script setup>
import { useUIStore, useSearchStore } from '../store/store.js'
</script>

<template>
	<!-- Placeholder div for all of the views-->
	<NcAppContent>
		<template #default>
			<Catalogi v-if="UIStore.selected === 'catalogi'" />
			<Dashboard v-if="UIStore.selected === 'dashboard'" />
			<Directory v-if="UIStore.selected === 'directory'" />
			<Publications v-if="UIStore.selected === 'publication'" />
			<Matadata v-if="UIStore.selected === 'metaData'" />
			<Search v-if="UIStore.selected === 'search'" :search="searchStore.search" />
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
	data() {
		return {
			UIStore: useUIStore(),
			searchStore: useSearchStore(),
		}
	},
}
</script>
