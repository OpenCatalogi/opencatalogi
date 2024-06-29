<template>
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
			<CatalogiDetails v-if="store.item" :catalog-id="catalogId" />
		</template>
	</NcAppContent>
</template>

<script>
import { store } from '../../store.js'
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
	},
	methods: {
		updateCatalogId(variable) {
			this.catalogId = variable
		},
	},
}
</script>
