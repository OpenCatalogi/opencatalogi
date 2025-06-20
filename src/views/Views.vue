<script setup>
import { navigationStore } from '../store/store.js'
</script>

<template>
	<!-- Placeholder div for all of the views-->
	<NcAppContent>
		<template #default>
			<Catalogi v-if="navigationStore.selected === 'catalogi'" />
			<Organizations v-if="navigationStore.selected === 'organizations'" />
			<Themes v-if="navigationStore.selected === 'themes'" />
			<Pages v-if="navigationStore.selected === 'pages'" />
			<Dashboard v-if="navigationStore.selected === 'dashboard'" />
			<Directory v-if="navigationStore.selected === 'directory'" />
			<Publications v-if="navigationStore.selected === 'publication'" />
			<Search v-if="navigationStore.selected === 'search'" />
			<Menus v-if="navigationStore.selected === 'menus'" />
			<Glossary v-if="navigationStore.selected === 'glossary'" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent } from '@nextcloud/vue'
import Catalogi from './catalogi/CatalogiIndex.vue'
import Organizations from './organizations/OrganizationIndex.vue'
import Themes from './themes/ThemeIndex.vue'
import Pages from './pages/PageIndex.vue'
import Dashboard from './dashboard/DashboardIndex.vue'
import Directory from './directory/DirectoryIndex.vue'
import Publications from './publications/PublicationIndex.vue'
import Search from './search/SearchIndex.vue'
import Menus from './menus/MenuIndex.vue'
import Glossary from './glossary/GlossaryIndex.vue'

export default {
	name: 'Views',
	components: {
		Catalogi,
		Organizations,
		Themes,
		Pages,
		Dashboard,
		Directory,
		Publications,
		Search,
		Menus,
		Glossary,
		NcAppContent,
	},
}
</script>
