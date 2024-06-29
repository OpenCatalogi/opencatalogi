<template>
	<NcContent app-name="opencatalog">
		<MainMenu selected="search" />
		<NcAppContent>
			<template #list>
				<SearchList />
			</template>
			<template #default />
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import SearchList from './list.vue'

export default {
	name: 'SearchIndex',
	components: {
		NcContent,
		NcAppContent,
		MainMenu,
		SearchList,
	},
}
</script>
