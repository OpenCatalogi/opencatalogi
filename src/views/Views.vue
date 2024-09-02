<script setup>
import { navigationStore } from '../store/store.js'
</script>

<template>
	<!-- Placeholder div for all of the views-->
	<NcAppContent>
		<template #default>
			<Catalogi v-if="navigationStore.selected === 'catalogi'" />
			<Organisations v-if="navigationStore.selected === 'organisations'" />
			<Themes v-if="navigationStore.selected === 'themes'" />
			<Dashboard v-if="navigationStore.selected === 'dashboard'" />
			<Directory v-if="navigationStore.selected === 'directory'" />
			<Publications v-if="navigationStore.selected === 'publication'" />
			<Matadata v-if="navigationStore.selected === 'metaData'" />
			<Search v-if="navigationStore.selected === 'search'" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent } from '@nextcloud/vue'
import Catalogi from './catalogi/CatalogiIndex.vue'
import Organisations from './organisations/OrganisationIndex.vue'
import Themes from './themes/ThemeIndex.vue'
import Dashboard from './dashboard/DashboardIndex.vue'
import Directory from './directory/DirectoryIndex.vue'
import Matadata from './metaData/MetaDataIndex.vue'
import Publications from './publications/PublicationIndex.vue'
import Search from './search/SearchIndex.vue'

export default {
	name: 'Views',
	components: {
		Catalogi,
		Organisations,
		Themes,
		Dashboard,
		Directory,
		Matadata,
		Publications,
		Search,
		NcAppContent,
	},
}
</script>
