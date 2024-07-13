<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<CatalogiList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.catalogiItem || store.selected != 'catalogi' "
				class="detailContainer"
				name="Geen Catalogi"
				description="Nog geen catalogi geselecteerd">
				<template #icon>
					<DatabaseOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('catalogusAdd')">
						Catalogi toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<CatalogiDetails v-if="store.catalogiItem && store.selected === 'catalogi'" :catalogi-item="store.catalogiItem" />
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
}
</script>
