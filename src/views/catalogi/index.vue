<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<CatalogiList/>
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
import { NcAppContent, NcEmptyContent } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import CatalogiList from './list.vue'
import CatalogiDetails from './details.vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline'

export default {
	name: 'CatalogiIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		MainMenu,
		CatalogiList,
		CatalogiDetails,
		DatabaseOutline
	},
	methods: {
		updateCatalogId(variable) {
			this.catalogId = variable
		},
	},
}
</script>
