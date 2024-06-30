<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<CatalogiList/>
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.item || store.selected != 'catalogi' "
				class="detailContainer"
				name="Geen Catalogi"
				description="Nog geen catalogi geselecteerd">
				<template #icon>
					<DatabaseOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('catalogiAdd')">
						Catalogi toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<CatalogiDetails v-if="store.item && store.selected === 'catalogi'" :catalog-id="catalogId" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import CatalogiList from './list.vue'
import CatalogiDetails from './details.vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline'

export default {
	name: 'CatalogiIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
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
