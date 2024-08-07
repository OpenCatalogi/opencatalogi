<script setup>
import { catalogiStore, navigationStore, searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<CatalogiList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!catalogiStore.catalogiItem || navigationStore.selected != 'catalogi' "
				class="detailContainer"
				name="Geen Catalogi"
				description="Nog geen catalogi geselecteerd">
				<template #icon>
					<DatabaseOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('addCatalog')">
						Catalogi toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<CatalogiDetails v-if="catalogiStore.catalogiItem && navigationStore.selected === 'catalogi'" :catalogi-item="catalogiStore.catalogiItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import CatalogiList from './CatalogiList.vue'
import CatalogiDetails from './CatalogiDetails.vue'
// eslint-disable-next-line n/no-missing-import
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline'

export default {
	name: 'CatalogiIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		CatalogiList,
		CatalogiDetails,
		DatabaseOutline,
	},
	data() {
		return {

		}
	},
}
</script>
