<template>
	<NcContent app-name="opencatalog">
		<MainMenu selected="catalogi" />
		<NcAppContent>
			<template #list>
				<CatalogiList @catalogId="updateCatalogId" />
			</template>
			<template #default>
				<NcEmptyContent v-if="!catalogId"
					class="detailContainer"
					name="Geen publicatie"
					description="Nog geen publicaite geselecteerd">
					<template #icon>
						<DatabaseOutline />
					</template>
					<template #action />
				</NcEmptyContent>
				<CatalogiDetails v-if="catalogId" :catalog-id="catalogId" />
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import CatalogiList from './list.vue'
import CatalogiDetails from './details.vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline'

export default {
	name: 'CatalogiIndex',
	components: {
		NcContent,
		NcAppContent,
		NcEmptyContent,
		MainMenu,
		CatalogiList,
		CatalogiDetails,
		DatabaseOutline,
	},
	data() {
		return {
			catalogId: undefined,
		}
	},
	methods: {
		updateCatalogId(variable) {
			this.catalogId = variable
		},
	},
}
</script>
