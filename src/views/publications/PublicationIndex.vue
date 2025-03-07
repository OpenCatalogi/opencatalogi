<script setup>
import { navigationStore, searchStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!publicationStore.publicationItem?.id || navigationStore.selected != 'publication'"
				class="detailContainer"
				name="Geen publicatie"
				description="Nog geen publicatie geselecteerd">
				<template #icon>
					<ListBoxOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('publicationAdd')">
						Publicatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PublicationDetails v-if="publicationStore.publicationItem?.id && navigationStore.selected === 'publication'" :publication-item="publicationStore.publicationItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PublicationList from './PublicationList.vue'
import PublicationDetails from './PublicationDetail.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'

export default {
	name: 'PublicationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		ListBoxOutline,
		PublicationList,
		PublicationDetails,
		NcButton,
	},
	data() {
		return {

		}
	},
}
</script>
